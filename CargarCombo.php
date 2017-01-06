<? set_time_limit(0);
include('adodb/adodb-exceptions.inc.php'); 
require ("comun/ini.php");
if(isset($_POST['JsonEnv'])){
	$JsonRec = new Services_JSON();
	$JsonEnv = new Services_JSON();
	$JsonRec=$JsonRec->decode(str_replace("\\","",$_POST['JsonEnv']));
	try {
		switch($JsonRec->Forma){
			case 0:	{
				$q = "SELECT A.int_cod AS int_cod,A.tra_cod AS tra_cod,A.tra_nom AS tra_nom, A.tra_ape AS tra_ape, A.tra_ced AS tra_ced FROM rrhh.trabajador AS A INNER JOIN rrhh.cont_tra AS B ON A.int_cod=B.tra_cod WHERE B.cont_cod=$JsonRec->Contrato AND A.tra_estatus<>4 AND A.tra_nom NOT ILIKE '%vacante%' AND A.tra_nom NOT ILIKE '%permiso no remunerado%' ORDER BY A.tra_nom";
				//die($q);
				$r = $conn->Execute($q);
				$i=0;
				while(!$r->EOF){
					$Trabajador[$i]['CI'] = (int)$r->fields['int_cod'];
					$Trabajador[$i]['CU'] = $r->fields['tra_cod'];
					$Trabajador[$i]['N'] = $r->fields['tra_nom'];
					$Trabajador[$i]['A'] = $r->fields['tra_ape'];
					$Trabajador[$i]['IU'] = $r->fields['tra_ced'];
					$i++;
					$r->movenext();
				}
				if(is_array($Trabajador)){
					echo $JsonEnv->encode($Trabajador);
				}else{
					echo false;
				}
				break;
			}
			case 1:	{
				$q = "SELECT A.int_cod,A.nom_fec_ini,A.nom_fec_fin,B.cont_nom FROM rrhh.historial_nom AS A INNER JOIN rrhh.contrato AS B ON A.cont_cod=B.int_cod ORDER BY A.int_cod DESC";
				$r = $conn->Execute($q);
				$i=0;
				while(!$r->EOF){
					$Fecha=split ("-" ,$r->fields['nom_fec_ini']);
					$FechaIni= $Fecha[2]."/".$Fecha[1]."/".$Fecha[0];
					$Fecha=split ("-" ,$r->fields['nom_fec_fin']);
					$FechaFin= $Fecha[2]."/".$Fecha[1]."/".$Fecha[0];
					$Nomina[$i]['CI'] = (int)$r->fields['int_cod'];
					$Nomina[$i]['D'] = $r->fields['cont_nom']."--> Periodo: ".$FechaIni." AL ".$FechaFin;
					$i++;
					$r->movenext();
				}
				if(is_array($Nomina)){
					echo $JsonEnv->encode($Nomina);
				}else{
					echo false;
				}
				break;
			}
			case 2:	{
				$q = "SELECT DISTINCT B.int_cod,B.tra_nom,B.tra_ape FROM rrhh.hist_nom_tra_conc AS A INNER JOIN rrhh.trabajador AS B ON A.tra_cod=B.int_cod WHERE A.hnom_cod=$JsonRec->Nomina ORDER BY B.int_cod";
				$r = $conn->Execute($q);
				$i=0;
				while(!$r->EOF){
					$Trabajador[$i]['CI'] = (int)$r->fields['int_cod'];
					$Trabajador[$i]['N'] = $r->fields['tra_nom'];
					$Trabajador[$i]['A'] = $r->fields['tra_ape'];
					$i++;
					$r->movenext();
				}
				if(is_array($Trabajador)){
					echo $JsonEnv->encode($Trabajador);
				}else{
					echo false;
				}
				break;
			}
			case 3:	{
				$q = "SELECT int_cod,dep_nom FROM rrhh.departamento WHERE div_cod=$JsonRec->Division ORDER BY int_cod";
				$r = $conn->Execute($q);
				$i=0;
				while(!$r->EOF){
					$Division[$i]['CI'] = (int)$r->fields['int_cod'];
					$Division[$i]['N'] = $r->fields['dep_nom'];
					$i++;
					$r->movenext();
				}
				if(is_array($Division)){
					echo $JsonEnv->encode($Division);
				}else{
					echo false;
				}
				break;
			}
			case 4:	{
				$q = "SELECT A.int_cod,A.tra_nom,A.tra_ape FROM rrhh.trabajador AS A LEFT JOIN rrhh.cont_tra AS B ON A.int_cod=B.tra_cod WHERE B.cont_cod=$JsonRec->Contrato AND (A.dep_cod=$JsonRec->Departamento OR $JsonRec->Departamento=-1) AND A.tra_vac != 1 AND tra_estatus <> 4 ORDER BY A.int_cod";
				$r = $conn->Execute($q);
				$i=0;
				//echo $q;
				while(!$r->EOF){
					$Trabajador[$i]['CI'] = (int)$r->fields['int_cod'];
					$Trabajador[$i]['N'] = $r->fields['tra_nom'];
					$Trabajador[$i]['A'] = $r->fields['tra_ape'];
					$i++;
					$r->movenext();
				}
				if(is_array($Trabajador)){
					echo $JsonEnv->encode($Trabajador);
				}else{
					echo false;
				}
				break;
			}
			case 5:	{
				$q = "SELECT DISTINCT B.int_cod,B.conc_nom FROM rrhh.hist_nom_tra_conc AS A INNER JOIN rrhh.concepto AS B ON A.conc_cod=B.int_cod WHERE A.hnom_cod=$JsonRec->Nomina ORDER BY B.int_cod";
				$r = $conn->Execute($q);
				$i=0;
				//echo $q;
				while(!$r->EOF){
					$Concepto[$i]['CI'] = (int)$r->fields['int_cod'];
					$Concepto[$i]['N'] = $r->fields['conc_nom'];
					$i++;
					$r->movenext();
				}
				if(is_array($Concepto)){
					echo $JsonEnv->encode($Concepto);
				}else{
					echo false;
				}
				break;
			}
			case 6:	{
				$q = "SELECT A.int_cod, A.periodo, B.cont_nom FROM rrhh.acumulado AS A INNER JOIN rrhh.contrato AS B ON A.cont_cod=B.int_cod ORDER BY A.int_cod DESC";
				$r = $conn->Execute($q);
				$i=0;
				while(!$r->EOF){
					$Nomina[$i]['CI'] = (int)$r->fields['int_cod'];
					$Nomina[$i]['D'] = $r->fields['cont_nom']."--> Periodo: ".$r->fields['periodo'];
					$i++;
					$r->movenext();
				}
				if(is_array($Nomina)){
					echo $JsonEnv->encode($Nomina);
				}else{
					echo false;
				}
				break;
			}
		}
	}catch( ADODB_Exception $e ){
		//	echo ERROR_CATCH_GENERICO;
		echo false;
	}
} 
?>