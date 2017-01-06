<?
include('adodb/adodb-exceptions.inc.php'); 
require ('lib/config.php'); 
if(isset($_POST['JsonEnv'])){
	$JsonRec = new Services_JSON();
	$JsonEnv = new Services_JSON();
	$JsonRec=$JsonRec->decode(str_replace("\\","",$_POST['JsonEnv']));
	try {
		switch($JsonRec->Accion){
			case 0:	{ //ARMO LA FORMULA DE CADA CONCEPTO POR CADA TRABAJADOR PARA PASARSELA A LA FUNCION EVAL DE JAVASCRIPT
				$Index=0;
				$q = "SELECT A.int_cod AS int_cod, A.tra_fec_ing AS fechai, A.tra_fec_egr AS fechae FROM rrhh.trabajador as A INNER JOIN rrhh.cont_tra as B ON A.int_cod=B.tra_cod  WHERE B.cont_cod=$JsonRec->Contrato AND (A.tra_estatus!='4' AND A.tra_estatus!='5') ORDER BY A.int_cod";
				$RTrabajadores = $conn->Execute($q);
				if($RTrabajadores->EOF){
					$TFC="-1T";
				}
				while (!$RTrabajadores->EOF){
					$q = "SELECT int_cod,conc_form,conc_tipo,conc_desc,conc_nom FROM rrhh.concepto WHERE conc_estatus='0' AND conc_tipo='2' ORDER BY int_cod";
					$RConceptos = $conn->Execute($q);
					if($RConceptos->EOF){
						$TFC="-1C";
						break;
					}
					while (!$RConceptos->EOF){
						$Trabajador=$RTrabajadores->fields['int_cod'];
						$Concepto=$RConceptos->fields['int_cod'];
						$q = "SELECT int_cod FROM rrhh.tra_conc WHERE tra_cod=$Trabajador AND conc_cod=$Concepto";
						$RConceptoSuspendido = $conn->Execute($q);
						if($RConceptoSuspendido->EOF){ //VERIFICO QUE EL CONCEPTO NO ESTE SUSPENDIDO PARA EL TRABAJADOR
							$Periodo=!empty($JsonRec->Periodo) ? $JsonRec->Periodo : date("m/Y");
							$Periodo=!empty($JsonRec->Periodo) ? $JsonRec->Periodo : date("m/Y");
							$FechaIni="01/".$Periodo;
							$Mes=split("/",$Periodo);
							$Dia=DiaFin($Mes[0]);
							$FechaFin= $Dia."/".$Periodo; 
							$CFormula=Formula($conn,$RConceptos->fields['conc_form'],$RTrabajadores->fields['int_cod'],-1,$FechaIni,$FechaFin,$RTrabajadores->fields['fechai'],$RTrabajadores->fields['fechae']);
							$CDesc=Formula($conn,$RConceptos->fields['conc_desc'],$RTrabajadores->fields['int_cod'],-1,$FechaIni,$FechaFin,$RTrabajadores->fields['fechai'],$RTrabajadores->fields['fechae']);
							$TFC[$Index]['T']=$Trabajador;
							$TFC[$Index]['C']=$Concepto;
							$TFC[$Index]['F']=str_replace("+","?MAS?",$RConceptos->fields['conc_form']);
							$TFC[$Index]['V']=!empty($CFormula) ? $CFormula : 0;
							$TFC[$Index]['FD']=str_replace("+","?MAS?",$RConceptos->fields['conc_desc']);
							$TFC[$Index]['D']=!empty($CDesc) ? $CDesc : "'".$RConceptos->fields['conc_nom']."'";
							$Index++;
						}
						$RConceptos->movenext();
					}
					$RTrabajadores->movenext();
				}
				echo $JsonEnv->encode($TFC);
				break;
			}
			case 1:	{ //GUARDO LOS RESULTADOS DEL CALCULO DE LOS ACUMULADOS
				$JsonRecI = new Services_JSON();
				$JsonRecI=$JsonRecI->decode(str_replace("\\","",$_POST['JsonEnvI']));
				$Contrato = $JsonRec->Contrato;				
				$Periodo=!empty($JsonRec->Periodo) ? $JsonRec->Periodo : date("m/Y");
				$Fecha=date("Y-m-d");
				$q = "SELECT int_cod FROM rrhh.acumulado WHERE periodo='$Periodo' AND cont_cod= '$Contrato'";
				$RAcumulados = $conn->Execute($q);
				if(!$RAcumulados->EOF){
					$q = "DELETE from rrhh.acumulado WHERE int_cod >= ".$RAcumulados->fields['int_cod']." AND cont_cod = '$Contrato'";
					$Exe= $conn->Execute($q);				
				}
				$q = "INSERT INTO rrhh.acumulado (periodo,fecha, cont_cod) VALUES ('$Periodo','$Fecha','$Contrato')";
				$RAux = $conn->Execute($q);
				$q = "SELECT int_cod FROM rrhh.acumulado WHERE periodo='$Periodo' AND cont_cod= '$Contrato'";
				$RAcumulados = $conn->Execute($q);
				$AcumuladoCI=$RAcumulados->fields['int_cod'];
				foreach($JsonRecI AS $TCV ){
					$Formula=str_replace("?MAS?","+",$TCV->F);
					$FormulaD=str_replace("?MAS?","+",str_replace("'","\'",$TCV->FD));
					$q = "INSERT INTO rrhh.acum_tra_conc (acum_cod,tra_cod,conc_cod,conc_form,conc_val,conc_desc,conc_fdesc) VALUES ($AcumuladoCI,$TCV->T,$TCV->C,'$Formula',$TCV->V,'$TCV->D','$FormulaD')";
					$RAux = $conn->Execute($q);
				} 
				//HISTORIAL VARIABLES
				$q = "SELECT A.tra_cod, A.var_cod, A.var_tra_val FROM rrhh.var_tra as A INNER JOIN rrhh.trabajador as B ON A.tra_cod=B.int_cod WHERE B.tra_estatus='0' OR B.tra_estatus='3'";
				$rVT= $conn->Execute($q);
				while (!$rVT->EOF){
					$CTrabajador=$rVT->fields['tra_cod'];
					$CVariable=$rVT->fields['var_cod'];
					$VVariable=$rVT->fields['var_tra_val'];				
					$q = "INSERT INTO rrhh.acum_var_tra (acum_cod,tra_cod,var_cod,var_tra_val) VALUES ($AcumuladoCI,$CTrabajador,$CVariable,$VVariable)";
					$rExec= $conn->Execute($q);
					$rVT->movenext();
				}
				//HISTORIAL CONSTANTES
				$q = "SELECT int_cod,cons_val FROM rrhh.constante ORDER BY int_cod";
				$rC= $conn->Execute($q);
				while (!$rC->EOF){
					$CConstante=$rC->fields['int_cod'];
					$VConstante=$rC->fields['cons_val'];				
					$q = "INSERT INTO rrhh.acum_cons (acum_cod,cons_cod,cons_val) VALUES ($AcumuladoCI,$CConstante,$VConstante)";
					$rExec= $conn->Execute($q);
					$rC->movenext();
				}
				//HISTORIAL DE SUELDOS
				$q = "SELECT int_cod, tra_sueldo FROM rrhh.trabajador WHERE tra_estatus!= 4 AND tra_estatus!= 5 ORDER BY int_cod";
				$rTS= $conn->Execute($q);
				while (!$rTS->EOF){
					$CTrabajador=$rTS->fields['int_cod'];
					$STrabajador=$rTS->fields['tra_sueldo'];
					$q = "INSERT INTO rrhh.acum_tra_sueldo (acum_cod,tra_cod,tra_sueldo) VALUES ($AcumuladoCI,$CTrabajador,$STrabajador)";
					$rExec= $conn->Execute($q);
					$rTS->movenext();
				} 
				echo "OPERACION REALIZADA CON EXITO";
				break;
			}
		}
	}catch( ADODB_Exception $e ){
		//echo ERROR_CATCH_GENERICO;
		echo $e;
	}
} 
?>
