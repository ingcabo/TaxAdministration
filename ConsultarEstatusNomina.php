<?
include('adodb/adodb-exceptions.inc.php'); 
require ("comun/ini.php");
if(isset($_POST['JsonEnv'])){
	$JsonRec = new Services_JSON();
	$JsonEnv = new Services_JSON();
	$JsonRec=$JsonRec->decode(str_replace("\\","",$_POST['JsonEnv']));
	try {//Comentados por no haber enalce presupuestario
		$emp_cod=$_SESSION['EmpresaL'];
		//$q = "SELECT int_cod, nrodoc FROM rrhh.historial_nom WHERE nom_fec_ini = '$JsonRec->FechaIni' AND nom_fec_fin='$JsonRec->FechaFin' AND cont_cod='$JsonRec->Contrato'";
		$q = "SELECT int_cod FROM rrhh.historial_nom WHERE nom_fec_ini = '$JsonRec->FechaIni' AND nom_fec_fin='$JsonRec->FechaFin' AND cont_cod='$JsonRec->Contrato'";
		//die($q);
		$rN = $conn->Execute($q);
		if(!$rN->EOF){
			$nomina = $rN->fields['int_cod'];
			/*$nrodocumento = $rN->fields['nrodoc']; 
			$q = "SELECT status FROM finanzas.solicitud_pago WHERE nroref = '$nrodocumento'";
			//die($q);
			$rSP = $conn->Execute($q);
			if($rSP->EOF || $rSP->fields['status']== 3)*/
				echo $JsonEnv->encode($nomina);
			/*else
				echo -1;*/
		}
		else{
			echo -1;
		}
	}catch( ADODB_Exception $e ){
		echo -1;
	}
} 
?>
