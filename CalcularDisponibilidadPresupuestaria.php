<?
include('adodb/adodb-exceptions.inc.php'); 
require ("comun/ini.php");
if(isset($_POST['JsonEnv'])){
	$JsonRec = new Services_JSON();
	$JsonEnv = new Services_JSON();
	$JsonRec=$JsonRec->decode(str_replace("\\","",$_POST['JsonEnv']));
	try {
		$emp_cod=$_SESSION['EmpresaL'];
		$q = "SELECT id FROM puser.unidades_ejecutoras WHERE id_escenario = '$escEnEje'";
		//$q = "SELECT A.int_cod,A.unidad_ejecutora_cod FROM rrhh.departamento as A INNER JOIN rrhh.division as B ON A.div_cod=B.int_cod WHERE B.emp_cod=$emp_cod AND A.dep_estatus=0 ORDER BY A.dep_ord";
		//die($q);
		$rUE = $conn->Execute($q);
		$indice=0;
		while (!$rUE->EOF){
			$UE=$rUE->fields['id'];
				$q = "SELECT id_categoria_programatica FROM puser.relacion_ue_cp WHERE id_unidad_ejecutora='$UE' AND id_escenario='$escEnEje' ";
				//die($q);
				$rCat = $conn->Execute($q);
				while (!$rCat->EOF){
					$Categoria=$rCat->fields['id_categoria_programatica'];
					$q = "SELECT distinct par_cod FROM rrhh.conc_part WHERE cat_cod='$Categoria'" ;
					//die($q);
					//echo($q).'<--------------->';
					$rP = $conn->Execute($q);
					while (!$rP->EOF){
						$Partida=$rP->fields['par_cod'];
						$q = "SELECT conc_cod FROM rrhh.conc_part WHERE par_cod='$Partida' AND cat_cod='$Categoria'" ;
						//die($q);
						$rC = $conn->Execute($q);
						$Monto=0;
						$q = "SELECT int_cod FROM rrhh.departamento WHERE unidad_ejecutora_cod='$UE' AND dep_estatus=0" ;
						//die($q);
						$rD = $conn->Execute($q);
						$Departamento=$rD->fields['int_cod'];
						while (!$rC->EOF){
							$Concepto=$rC->fields['conc_cod'];
							$q = "SELECT conc_retencion FROM rrhh.concepto WHERE int_cod=$Concepto";
							$rCAux = $conn->Execute($q);
							if(!$rD->EOF){
								$q = "SELECT sum(A.conc_val::numeric(20,2)) AS valor,sum(A.conc_aporte::numeric(20,2)) AS aporte ";
								$q.= "FROM rrhh.nom_tra_conc AS A INNER JOIN rrhh.trabajador AS B ON A.tra_cod=B.int_cod ";
								$q.= "INNER JOIN rrhh.nomina AS C ON A.nom_cod=C.int_cod "; 
								$q.= "WHERE B.dep_cod=$Departamento AND C.cont_cod=$JsonRec->Contrato AND A.conc_cod=$Concepto";
							}
							else{
								$q = "SELECT sum(A.conc_val::numeric(20,2)) AS valor,sum(A.conc_aporte::numeric(20,2)) AS aporte ";
								$q.= "FROM rrhh.nom_tra_conc AS A ";
								$q.= "INNER JOIN rrhh.nomina AS B ON A.nom_cod=B.int_cod "; 
								$q.= "WHERE B.cont_cod=$JsonRec->Contrato AND A.conc_cod=$Concepto";							
							}
							//die ($q);
							$rM = $conn->Execute($q);
							if(!$rM->EOF){
	                               $Monto+= $rCAux->fields['conc_retencion']==1 ? $rM->fields['aporte'] : $rM->fields['valor']==0.00 ? $rM->fields['aporte'] : $rM->fields['valor'];
							}
							$rC->movenext();
						}
						//BUSCAR DISPONIBLIDAD
						$q = "SELECT id,disponible FROM puser.relacion_pp_cp WHERE id_categoria_programatica='$Categoria' AND id_partida_presupuestaria='$Partida' AND id_escenario='$escEnEje'" ;
						//die($q);
						$rDisp = $conn->Execute($q);
						if(!$rDisp->EOF){
							$MontoD=$rDisp->fields['disponible'];
					/*		if($MontoD<$Monto){
								die(-1);
							}else{ */
							if($Monto>0){
								$Vector[$indice]['idPartCad'] = $rDisp->fields['id'];
								$Vector[$indice]['Categoria'] = $Categoria;
								$Vector[$indice]['Partida'] = $Partida;
								$Vector[$indice]['Monto'] = $Monto;
								$indice++;
								}
						//	}
						}
						$rP->movenext();
					}
					$rCat->movenext();
				}
			$rUE->movenext();
		}
		if(is_array($Vector)){
			echo $JsonEnv->encode($Vector);
		}else{
			echo -1;
		}
	}catch( ADODB_Exception $e ){
		echo -1;
	}
} 
?>
