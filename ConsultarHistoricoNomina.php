<?
include('adodb/adodb-exceptions.inc.php'); 
require ("comun/ini.php");
if(isset($_POST['JsonEnv'])){
	$JsonRec = new Services_JSON();
	$JsonEnv = new Services_JSON();
	$JsonRec=$JsonRec->decode(str_replace("\\","",$_POST['JsonEnv']));
	try {
		$emp_cod=$_SESSION['EmpresaL'];
		$Fecha = split("/",$JsonRec->Periodo);
		$FechaIni = $Fecha[1].'-'.$Fecha[0].'-01';
		$FechaFin = $Fecha[1].'-'.$Fecha[0].'-'.DiaFin($Fecha[0]);
		
		$q = "SELECT int_cod FROM rrhh.historial_nom WHERE nom_fec_ini BETWEEN '$FechaIni' AND '$FechaFin' AND cont_cod='$JsonRec->Contrato'";
		//die($q);
		$rN = $conn->Execute($q);
		if(!$rN->EOF){
			$q = "SELECT cont_tipo FROM rrhh.contrato WHERE int_cod = '$JsonRec->Contrato'";
			//die($q);
			$rC = $conn->Execute($q);
			if(($rC->fields['cont_tipo']== 0 && $rN->RecordCount() >= 4)||($rC->fields['cont_tipo']== 1 && $rN->RecordCount() == 2) ||($rC->fields['cont_tipo']== 2 && $rN->RecordCount() == 1))
				echo $JsonEnv->encode($rN->RecordCount());
			else
				echo -1;
		}
		else{
			echo -1;
		}
	}catch( ADODB_Exception $e ){
		echo -1;
	}
} 
?>
