<?
include('adodb/adodb-exceptions.inc.php'); 
set_time_limit(0);
require ("comun/ini.php");
if(isset($_POST['JsonEnv'])){
	$JsonRec = new Services_JSON();
	$JsonEnv = new Services_JSON();
	$JsonRec=$JsonRec->decode(str_replace("\\","",$_POST['JsonEnv']));
	try {
		switch($JsonRec->Accion){
			case 0:	{ //ARMO LA FORMULA DE CADA CONCEPTO POR CADA TRABAJADOR PARA PASARSELA A LA FUNCION EVAL DE JAVASCRIPT
				$Index=0;
				if($JsonRec->Trabajador1==-1 || $JsonRec->Trabajador2==-1){
					$q = "SELECT A.int_cod AS int_cod, A.tra_fec_ing AS fechai, A.tra_fec_egr AS fechae,A.tra_vac FROM rrhh.trabajador as A INNER JOIN rrhh.cont_tra as B ON A.int_cod=B.tra_cod  WHERE B.cont_cod=$JsonRec->Contrato AND (A.tra_estatus='0' OR A.tra_estatus='3') ORDER BY A.int_cod";
				}else{
					$q = "SELECT A.int_cod AS int_cod, A.tra_fec_ing AS fechai, A.tra_fec_egr AS fechae,A.tra_vac FROM rrhh.trabajador as A INNER JOIN rrhh.cont_tra as B ON A.int_cod=B.tra_cod  WHERE B.cont_cod=$JsonRec->Contrato AND (A.tra_estatus='0' OR A.tra_estatus='3') AND A.int_cod>=$JsonRec->Trabajador1 AND A.int_cod<=$JsonRec->Trabajador2 ORDER BY A.int_cod";
				}
				//die($q);
				$RTrabajadores = $conn->Execute($q);
				if($RTrabajadores->EOF){
					$TFC="-1T";
				}
				//---
				//CALCULO DEL MODULO DE PRESTAMO
				$q = "SELECT int_cod FROM rrhh.prestamo WHERE cont_cod=$JsonRec->Contrato";
				$rPres = $conn->Execute($q);
				while (!$rPres->EOF){
					$PrestamoAux=$rPres->fields['int_cod'];
					$q = "UPDATE rrhh.prestamo_cuotas SET cuota_estatus='Por Cobrar' WHERE cuota_estatus='Cobrando' AND pres_cod=$PrestamoAux";
					$rAux = $conn->Execute($q);
					$rPres->movenext();
				}
				//---
				//PARA CONTRATO VACACIONES
				$q = "SELECT int_cod FROM rrhh.mod_conc WHERE modulo=1 AND conc_cod=$JsonRec->Contrato";
				$rVac = $conn->Execute($q);
				$ContratoVacaciones = !$rVac->EOF ? true : false;
				//<-
				
				while (!$RTrabajadores->EOF){
					$q = "SELECT C.int_cod,C.conc_nom,C.conc_form,C.conc_desc,C.conc_aporte,C.conc_tipo FROM (rrhh.cont_gconc as A INNER JOIN rrhh.gconc_conc as B ON A.gconc_cod=B.gconc_cod) INNER JOIN rrhh.concepto as C ON B.conc_cod=C.int_cod WHERE A.cont_cod=$JsonRec->Contrato AND C.conc_estatus='0' ORDER BY C.int_cod";
					$RConceptos = $conn->Execute($q);
					if($RConceptos->EOF){
						$TFC="-1C";
						break;
					}
					while (!$RConceptos->EOF){
						$Trabajador=$RTrabajadores->fields['int_cod'];
						$Vacante=$RTrabajadores->fields['tra_vac'];
						$Concepto=$RConceptos->fields['int_cod'];
						$q = "SELECT int_cod FROM rrhh.tra_conc WHERE tra_cod=$Trabajador AND conc_cod=$Concepto";
						$RConceptoSuspendido = $conn->Execute($q);
						if($RConceptoSuspendido->EOF){ //VERIFICO QUE EL CONCEPTO NO ESTE SUSPENDIDO PARA EL TRABAJADOR
							//PARA CONTRATO VACACIONES
							if($ContratoVacaciones){
								$q = "SELECT * FROM rrhh.preparar_vac WHERE tra_cod=$Trabajador";
								$rFVac = $conn->Execute($q);
								if(!$rFVac->EOF){
									$JsonRec->FechaIni=muestrafecha($rFVac->fields['fecha_ini']);
									$JsonRec->FechaFin=muestrafecha($rFVac->fields['fecha_fin']);
								}
							}
							//<-
							$CFormula=Formula($conn,$RConceptos->fields['conc_form'],$Trabajador,$JsonRec->Contrato,$JsonRec->FechaIni,$JsonRec->FechaFin,$RTrabajadores->fields['fechai'],$RTrabajadores->fields['fechae']);
							$CDesc=Formula($conn,$RConceptos->fields['conc_desc'],$Trabajador,$JsonRec->Contrato,$JsonRec->FechaIni,$JsonRec->FechaFin,$RTrabajadores->fields['fechai'],$RTrabajadores->fields['fechae']);
							$CAporte=Formula($conn,$RConceptos->fields['conc_aporte'],$Trabajador,$JsonRec->Contrato,$JsonRec->FechaIni,$JsonRec->FechaFin,$RTrabajadores->fields['fechai'],$RTrabajadores->fields['fechae']);
							$TFC[$Index]['T']=$Trabajador;
							$TFC[$Index]['C']=$Concepto;
							$TFC[$Index]['F']= $Vacante==1 ? 0 : (!empty($CFormula) ? $CFormula : 0);
							$TFC[$Index]['D']=!empty($CDesc) ? $CDesc : "'".$RConceptos->fields['conc_nom']."'";
							$TFC[$Index]['A']= $Vacante==1 ? 0 : (!empty($CAporte) ? $CAporte : 0);
							$Index++;
						}
						$RConceptos->movenext();
					}
					$RTrabajadores->movenext();
				}
				echo $JsonEnv->encode($TFC);
				break;
			}
			case 1:	{ //GUARDO LOS RESULTADOS DEL CALCULO DE LA NOMINA
				$JsonRecI = new Services_JSON();
				$JsonRecI=$JsonRecI->decode(str_replace("\\","",$_POST['JsonEnvI']));
				$q = "SELECT * FROM rrhh.nomina WHERE cont_cod=$JsonRec->Contrato";
				$RNomina = $conn->Execute($q);
				if($RNomina->EOF){
					$q = "INSERT INTO rrhh.nomina (cont_cod,nom_fec_ini,nom_fec_fin) VALUES ($JsonRec->Contrato,'$JsonRec->FechaIni','$JsonRec->FechaFin')";
					$RAux = $conn->Execute($q);
					$q = "SELECT int_cod FROM rrhh.nomina WHERE cont_cod=$JsonRec->Contrato";
					$RNomina = $conn->Execute($q);
					$NominaCI=$RNomina->fields['int_cod'];
				}else{
					$q = "UPDATE rrhh.nomina SET nom_fec_ini='$JsonRec->FechaIni', nom_fec_fin='$JsonRec->FechaFin' WHERE cont_cod=$JsonRec->Contrato";
					$RAux = $conn->Execute($q);
					$NominaCI=$RNomina->fields['int_cod'];
				}
				if($JsonRec->Trabajador1==-1 || $JsonRec->Trabajador2==-1){
					$q = "DELETE FROM rrhh.nom_tra_conc WHERE nom_cod=$NominaCI";
				}else{
					$q = "DELETE FROM rrhh.nom_tra_conc WHERE nom_cod=$NominaCI AND tra_cod>=$JsonRec->Trabajador1 AND tra_cod<=$JsonRec->Trabajador2";
				}
				$RAux = $conn->Execute($q);
				foreach( $JsonRecI AS $TCV ){
					$q = "INSERT INTO rrhh.nom_tra_conc (nom_cod,tra_cod,conc_cod,conc_val,conc_aporte,conc_desc) VALUES ($NominaCI,$TCV->T,$TCV->C,$TCV->F,$TCV->A,'$TCV->D')";
					$RAux = $conn->Execute($q);
				} 
				
				echo "OPERACION REALIZADA CON EXITO"; 
				break; 
			}
			case 2:	{ //ELIMINO LA NOMINA CALCULADA
				$q = "DELETE FROM rrhh.nomina WHERE cont_cod=$JsonRec->Contrato";
				$conn->Execute($q);
				echo "OPERACION REALIZADA CON EXITO";
				break;
			}
			case 3:	{ //CIERRE DE NOMINA 
				//HISTORIAL NOMINA
				$q = "SELECT A.int_cod, A.cont_cod, B.cont_nom, A.nom_fec_ini, A.nom_fec_fin FROM rrhh.nomina AS A INNER JOIN rrhh.contrato AS B ON A.cont_cod=B.int_cod WHERE A.cont_cod=$JsonRec->Contrato";
				$rNC= $conn->Execute($q);
				$CNomina=$rNC->fields['int_cod'];
				$CContrato=$rNC->fields['cont_cod'];
				$NContrato=$rNC->fields['cont_nom'];
				$Fini=$rNC->fields['nom_fec_ini'];
				$Ffin=$rNC->fields['nom_fec_fin'];
				$Fecha=date("d/m/y");
				
				$q = "INSERT INTO rrhh.historial_nom (cont_cod,cont_nom,nom_fec_ini,nom_fec_fin,fecha,hnom_ban) VALUES ($CContrato,'$NContrato','$Fini','$Ffin','$Fecha','1')";
				$rExec= $conn->Execute($q);
				
				$q = "SELECT int_cod FROM rrhh.historial_nom WHERE hnom_ban='1'";
				$rHN= $conn->Execute($q);
				$CHNomina=$rHN->fields['int_cod'];

				//CALCULO DEL MODULO DE PRESTAMO
				$q = "SELECT int_cod FROM rrhh.prestamo WHERE cont_cod=$JsonRec->Contrato";
				$rPres = $conn->Execute($q);
				while (!$rPres->EOF){
					$PrestamoAux=$rPres->fields['int_cod'];
					$q = "UPDATE rrhh.prestamo_cuotas SET cuota_estatus='Cancelado' WHERE cuota_estatus='Cobrando' AND pres_cod=$PrestamoAux";
					$rExec = $conn->Execute($q);
					$rPres->movenext();
				}
				//CALCULO DEL MODULO DE PRESTAMO

				//HISTORIAL CONCEPTOS
				$q = "SELECT A.tra_cod, B.tra_nom, B.tra_ape,A.conc_cod, C.conc_nom, C.conc_tipo, A.conc_val,A.conc_desc,A.conc_aporte,C.conc_form,C.conc_desc AS conc_fdesc,C.conc_aporte AS conc_faporte FROM (rrhh.nom_tra_conc AS A INNER JOIN rrhh.trabajador AS B ON A.tra_cod=B.int_cod) INNER JOIN rrhh.concepto AS C ON A.conc_cod=C.int_cod WHERE A.nom_cod=$CNomina";
				$rNTC= $conn->Execute($q);
				while (!$rNTC->EOF){
					$CTrabajador=$rNTC->fields['tra_cod'];
					$NTrabajador=$rNTC->fields['tra_nom']." ".$rNTC->fields['tra_ape'];				
					$CConcepto=$rNTC->fields['conc_cod'];
					$NConcepto=$rNTC->fields['conc_nom'];				
					$TConcepto=$rNTC->fields['conc_tipo'];				
					$VConcepto=$rNTC->fields['conc_val'];
					$FConcepto=$rNTC->fields['conc_form'];
					$DConcepto=$rNTC->fields['conc_desc'];
					$DFConcepto=str_replace("'","\'",$rNTC->fields['conc_fdesc']);
					$AConcepto=$rNTC->fields['conc_aporte'];
					$AFConcepto=str_replace("'","\'",$rNTC->fields['conc_faporte']);
					$q = "INSERT INTO rrhh.hist_nom_tra_conc (hnom_cod,tra_cod,tra_nom,conc_cod,conc_nom,conc_tipo,conc_val,conc_form,conc_desc,conc_fdesc,conc_aporte,conc_faporte) VALUES ($CHNomina,$CTrabajador,'$NTrabajador',$CConcepto,'$NConcepto','$TConcepto',$VConcepto,'$FConcepto','$DConcepto','$DFConcepto',$AConcepto,'$AFConcepto')";
					$rExec= $conn->Execute($q);
					$rNTC->movenext();
				}

				//HISTORIAL VARIABLES
				$q = "SELECT A.tra_cod, C.tra_nom, C.tra_ape, A.var_cod, D.var_nom, A.var_tra_val FROM ((rrhh.var_tra as A INNER JOIN rrhh.cont_tra as B ON A.tra_cod=B.tra_cod) INNER JOIN rrhh.trabajador as C ON A.tra_cod=C.int_cod) INNER JOIN rrhh.variable as D ON A.var_cod=D.int_cod  WHERE B.cont_cod=$JsonRec->Contrato";
				$rVT= $conn->Execute($q);

				while (!$rVT->EOF){
					$CTrabajador=$rVT->fields['tra_cod'];
					$NTrabajador=$rVT->fields['tra_nom']." ".$rVT->fields['tra_ape'];				
					$CVariable=$rVT->fields['var_cod'];
					$NVariable=$rVT->fields['var_nom'];				
					$VVariable=$rVT->fields['var_tra_val'];				
					$q = "INSERT INTO rrhh.hist_nom_var_tra (hnom_cod,tra_cod,tra_nom,var_cod,var_nom,var_tra_val) VALUES ($CHNomina,$CTrabajador,'$NTrabajador',$CVariable,'$NVariable',$VVariable)";
					$rExec= $conn->Execute($q);
					$rVT->movenext();
				}

				//HISTORIAL CONSTANTES
				$q = "SELECT int_cod,cons_nom,cons_val FROM rrhh.constante ORDER BY int_cod";
				$rC= $conn->Execute($q);
				
				while (!$rC->EOF){
					$CConstante=$rC->fields['int_cod'];
					$NConstante=$rC->fields['cons_nom'];
					$VConstante=$rC->fields['cons_val'];				
					$q = "INSERT INTO rrhh.hist_nom_cons (hnom_cod,cons_cod,cons_nom,cons_val) VALUES ($CHNomina,$CConstante,'$NConstante',$VConstante)";
					$rExec= $conn->Execute($q);
					$rC->movenext();
				}
				//HISTORIAL DE SUELDOS, CARGOS O FUNCIONES, DEPARTAMENTOS Y BANCOS
				$q = "SELECT B.int_cod, B.tra_sueldo, B.tra_ced, B.tra_vac, B.tra_tipo,C.int_cod AS cargo,C.car_cod,C.car_nom,C.car_ord,F.int_cod AS funcion,F.fun_cod,F.fun_nom,F.fun_ord,D.int_cod AS departamento,D.dep_cod,D.dep_nom,D.dep_estatus,D.dep_ord,E.id,E.codigo,E.descripcion,E.nombre_corto 
					FROM ((((rrhh.cont_tra AS A INNER JOIN rrhh.trabajador AS B ON A.tra_cod=B.int_cod) 
					LEFT JOIN rrhh.cargo AS C ON B.car_cod=C.int_cod) 
					LEFT JOIN rrhh.funciones AS F ON B.fun_cod=F.int_cod) 
					LEFT JOIN rrhh.departamento AS D ON B.dep_cod=D.int_cod) 
					LEFT JOIN public.banco AS E ON B.ban_cod=E.id   WHERE A.cont_cod=$JsonRec->Contrato AND (B.tra_estatus = 0 OR B.tra_estatus = 3) ORDER BY B.int_cod ";
				$rTS= $conn->Execute($q);
				//die($q);
				while (!$rTS->EOF){
					$CTrabajador=$rTS->fields['int_cod'];
					$STrabajador=$rTS->fields['tra_sueldo'];
					$CeTrabajador=$rTS->fields['tra_ced'];
					$VTrabajador=$rTS->fields['tra_vac'];
					$TTrabajador=empty($rTS->fields['tra_tipo']) ? 0 : $rTS->fields['tra_tipo'];
					$CCTrabajador=empty($rTS->fields['cargo']) ?  'null' : $rTS->fields['cargo'];
					$CC2Trabajador=empty($rTS->fields['car_cod']) ?  'null' : $rTS->fields['car_cod'];
					$CNTrabajador=empty($rTS->fields['car_nom']) ?  'null' : $rTS->fields['car_nom'];
					$COTrabajador=empty($rTS->fields['car_ord']) ?  'null' : $rTS->fields['car_ord'];
					$FFTrabajador=empty($rTS->fields['funcion']) ?  'null' : $rTS->fields['funcion'];
					$FF2Trabajador=empty($rTS->fields['fun_cod']) ?  'null' : $rTS->fields['fun_cod'];
					$FNTrabajador=empty($rTS->fields['fun_nom']) ?  'null' : $rTS->fields['fun_nom'];
					$FOTrabajador=empty($rTS->fields['fun_ord']) ?  'null' : $rTS->fields['fun_ord'];
					$DCTrabajador=$rTS->fields['departamento'];
					$DC2Trabajador=$rTS->fields['dep_cod'];
					$DNTrabajador=$rTS->fields['dep_nom'];
					$DETrabajador=$rTS->fields['dep_estatus'];
					$DOTrabajador=$rTS->fields['dep_ord'];
					$BCTrabajador=empty($rTS->fields['id']) ?  'null' : $rTS->fields['id'];
					$BC2Trabajador=$rTS->fields['codigo'];
					$BNTrabajador=$rTS->fields['descripcion'];
					$BN2Trabajador=$rTS->fields['nombre_corto'];
					$q = "INSERT INTO rrhh.hist_nom_tra_sueldo (hnom_cod,tra_cod,tra_sueldo,tra_ced,tra_vac,tra_tipo,car_cod,car_cod2,car_nom,car_ord,fun_cod,fun_cod2,fun_nom,fun_ord,dep_cod,dep_cod2,dep_nom,dep_estatus,dep_ord,ban_cod,ban_cod2,ban_nom,ban_nom2)";
					$q.= " VALUES ($CHNomina,$CTrabajador,'$STrabajador','$CeTrabajador',$VTrabajador,'$TTrabajador',$CCTrabajador,'$CC2Trabajador','$CNTrabajador',$COTrabajador,$FFTrabajador,'$FF2Trabajador','$FNTrabajador',$FOTrabajador,$DCTrabajador,'$DC2Trabajador','$DNTrabajador',$DETrabajador,$DOTrabajador,$BCTrabajador,'$BCTrabajador','$BNTrabajador','$BN2Trabajador')";
					//die($q);
					$rExec= $conn->Execute($q);
					$rTS->movenext();
				}
				
				//PARA CONTRATO VACACIONES
				$q = "SELECT int_cod FROM rrhh.mod_conc WHERE modulo=1 AND conc_cod=$JsonRec->Contrato";
				$rVac = $conn->Execute($q);
				if(!$rVac->EOF){
					$q = "SELECT * FROM rrhh.preparar_vac";
					$rPVac = $conn->Execute($q);
					while(!$rPVac->EOF){
						$Trabajador=$rPVac->fields['tra_cod'];
						$FechaIni=$rPVac->fields['fecha_ini'];
						$FechaFin=$rPVac->fields['fecha_fin'];
						$Dias=$rPVac->fields['dias'];
						$DiasP=$rPVac->fields['dias_pendientes'];
						$q = "SELECT * FROM rrhh.vacaciones WHERE tra_cod=$Trabajador";
						$rV = $conn->Execute($q);
						if($rV->EOF){
							$q = "INSERT INTO rrhh.vacaciones(tra_cod,vac_fec_ini,vac_fec_fin,vac_dias,vac_dias_pendientes) VALUES ($Trabajador,'$FechaIni','$FechaFin',$Dias,$DiasP)";
							$rExec = $conn->Execute($q);
						}else{
							$q = "UPDATE rrhh.vacaciones SET vac_fec_ini='$FechaIni',vac_fec_fin='$FechaFin',vac_dias=$Dias,vac_dias_pendientes=$DiasP WHERE tra_cod=$Trabajador";
							$rExec = $conn->Execute($q);
						}
						$rPVac->movenext();
					}
					$q = "DELETE FROM rrhh.preparar_vac";
					$rExec = $conn->Execute($q);
					$q = "DELETE FROM rrhh.cont_tra WHERE cont_cod=$JsonRec->Contrato";
					$rExec = $conn->Execute($q);
				}/*
				//<- 
				//PRESUPUESTO
				$nrodoc = movimientos_presupuestarios::getNroDoc($conn, '010');

				// GUARDO RETENCIONES PARA PRESUPUESTO
				$q = "SELECT B.conc_cod,sum(A.conc_val::numeric(20,2)) AS valor FROM rrhh.nom_tra_conc AS A INNER JOIN rrhh.concepto AS B ON A.conc_cod=B.int_cod INNER JOIN rrhh.nomina AS C ON A.nom_cod=C.int_cod  WHERE C.cont_cod=$JsonRec->Contrato AND B.conc_retencion=1 AND B.conc_tipo=1 GROUP BY B.conc_cod";
				$rR= $conn->Execute($q);
				while (!$rR->EOF){
					$concepto=$rR->fields['conc_cod'];
					$monto=$rR->fields['valor'];
					$q = "INSERT INTO rrhh.presupuesto_retenciones (nro_doc_compromiso,conc_cod,monto)";
					$q.= " VALUES ('$nrodoc',$concepto,$monto)";
					$rExec= $conn->Execute($q);
					$rR->movenext();
				}
				$fecha=date("Y-m-d");
				$Fecha=split ("-" ,$Fini);
				$FechaIni= $Fecha[2]."/".$Fecha[1]."/".$Fecha[0];
				$Fecha=split ("-" ,$Ffin);
				$FechaFin= $Fecha[2]."/".$Fecha[1]."/".$Fecha[0];
				$Nomina = $NContrato." Periodo: ".$FechaIni." AL ".$FechaFin;
				
				
				#REGISTRO LA NOMINA EN MOVIMIENTOS PRESUPUESTARIOS#	
				$q = "INSERT INTO movimientos_presupuestarios ";
				$q.= "(id_usuario, id_unidad_ejecutora, ano, descripcion, nrodoc, tipdoc, tipref, ";//nroref, 
				$q.= "fechadoc, fecharef, status, id_proveedor) ";
				$q.= "VALUES ";
				$q.= "($usuario->id, '28', '$anoCurso', '$Nomina', '$nrodoc', '010', '', ";//'$nroref', 
				$q.= " '$fecha', '$fecha', '1', 90) ";
				//die($q);
				$r = $conn->Execute($q) or die($q);

				for($i=0;$i<count($JsonRec->Presupuesto);$i++){
					//echo "idParCad: ".$JsonRec->Presupuesto[$i]->idPartCad." Categoria: ".$JsonRec->Presupuesto[$i]->Categoria."Partida: ".$JsonRec->Presupuesto[$i]->Partida." Monto: ".$JsonRec->Presupuesto[$i]->Monto."\n";
					movimientos_presupuestarios::add_relacion_nomina($conn,
						$JsonRec->Presupuesto[$i]->idPartCad,
						$JsonRec->Presupuesto[$i]->Categoria,
						$JsonRec->Presupuesto[$i]->Partida,	
						$nrodoc, 
						$JsonRec->Presupuesto[$i]->Monto);  
					relacion_pp_cp::set_desde_compromiso_nomina($conn, $JsonRec->Presupuesto[$i]->idPartCad, $JsonRec->Presupuesto[$i]->Monto);
				}
				
				$q = "UPDATE rrhh.historial_nom SET nrodoc='$nrodoc' WHERE hnom_ban='1'";
				$rExec= $conn->Execute($q);*/
				
				$q = "UPDATE rrhh.historial_nom SET hnom_ban='0' WHERE hnom_ban='1'";
				$rExec= $conn->Execute($q);	
				
				$q = "DELETE FROM rrhh.nomina WHERE int_cod=$CNomina";
				$rExec= $conn->Execute($q);
				
				echo "OPERACION REALIZADA CON EXITO"; 
				break;
			}
			case 4:{ //REVERSO DE NOMINA
				
				//REVERSO PRESUPUESTO
				/*$q = "SELECT nrodoc FROM rrhh.historial_nom WHERE int_cod = '$JsonRec->Nomina'";
				$r= $conn->Execute($q);
				$nrodoc = $r->fields['nrodoc'];
				$q = "UPDATE puser.movimientos_presupuestarios SET status_movimiento = '2' WHERE nrodoc='$nrodoc'";
				$rExec= $conn->Execute($q);	
				
				$q = "SELECT  id_parcat, monto FROM puser.relacion_movimientos WHERE nrodoc= '$nrodoc'"; 
				$rCP= $conn->Execute($q);
				while(!$rCP->EOF)
				{
					$q = "UPDATE puser.relacion_pp_cp SET  ";
					$q.= " compromisos = compromisos - ".$rCP->fields['monto'];
					$q.= ", disponible = disponible + ".$rCP->fields['monto'];
					$q.= "WHERE id=".$rCP->fields['id_parcat'];
					$r = $conn->Execute($q);
					$rCP->movenext();
				}
				//REVERSO PRESUPUESTO
				
				//REVERSO CALCULO DEL MODULO DE PRESTAMO
				$q = "SELECT B.int_cod FROM rrhh.prestamo_cuotas AS B  WHERE  B.cuota_nom_fec_ini='$JsonRec->FechaIni' AND B.cuota_nom_fec_fin='$JsonRec->FechaFin' AND B.cuota_estatus='Cancelado'";
				$r = $conn->Execute($q);
				while(!$r->EOF){
					$Cuota=$r->fields['int_cod'];
					$q = "UPDATE rrhh.prestamo_cuotas SET cuota_estatus='Por Cobrar' WHERE int_cod=$Cuota";
					$rAux = $conn->Execute($q);
					$r->movenext();
				}
				//REVERSO CALCULO DEL MODULO DE PRESTAMO
				
				//REVERSO DE ACUMULADOS
				$Fecha = split("/",$FechaIni);
				$Fecha = $Fecha[0]."/".$Fecha[1];
				$q = "DELETE FROM rrhh.acumulado WHERE cont_cod='$JsonRec->Nomina'AND periodo = '$Fecha'";
				$rExec = $conn->Execute($q);	
				//REVERSO DE ACUMULADOS				
				*/
				//REVERSO HISTORIAL NOMINA
				$q = "DELETE FROM rrhh.historial_nom WHERE int_cod='$JsonRec->Nomina'";
				$rExec = $conn->Execute($q);
				//REVERSO HISTORIAL NOMINA
				
				echo "OPERACION REALIZADA CON EXITO"; 
				break;
			}
			case 5:	{ //ARMO LA FORMULA DE CADA CONCEPTO POR CADA TRABAJADOR PARA PASARSELA A LA FUNCION EVAL DE JAVASCRIPT
			    //NOMINA PARALELA PARA PRESUPUESTO
				$Index=0;
				//SE BUSCAN LOS CODIGOS DE LOS TRABAJADORES ASOCIADOS A ESA UNIDAD EJECUTORA
				if($JsonRec->Cargo == 0 ){
					$q = "SELECT A.int_cod AS int_cod, A.tra_fec_ing AS fechai, A.tra_fec_egr AS fechae,A.tra_vac, C.cont_cod  FROM rrhh.trabajador as A INNER JOIN rrhh.departamento as B ON A.dep_cod=B.int_cod  INNER JOIN rrhh.cont_tra as C ON A.int_cod = C.tra_cod WHERE B.unidad_ejecutora_cod='$JsonRec->Unidad' AND (A.tra_estatus='0' OR A.tra_estatus='3') ORDER BY A.int_cod";
				}else{
					$q = "SELECT A.int_cod AS int_cod, A.tra_fec_ing AS fechai, A.tra_fec_egr AS fechae,A.tra_vac, C.cont_cod  FROM rrhh.trabajador as A INNER JOIN rrhh.departamento as B ON A.dep_cod=B.int_cod  INNER JOIN rrhh.cont_tra as C ON A.int_cod = C.tra_cod WHERE B.unidad_ejecutora_cod='$JsonRec->Unidad' AND (A.tra_estatus='0' OR A.tra_estatus='3') AND A.car_cod = $JsonRec->Cargo ORDER BY A.int_cod";
				}
				//die($q);
				$RTrabajadores = $conn->Execute($q);				
				while (!$RTrabajadores->EOF){
					$Contrato = $RTrabajadores->fields['cont_cod'];
					$q = "SELECT C.int_cod,C.conc_nom,C.conc_form,C.conc_desc,C.conc_aporte,C.conc_tipo FROM (rrhh.cont_gconc as A INNER JOIN rrhh.gconc_conc as B ON A.gconc_cod=B.gconc_cod) INNER JOIN rrhh.concepto as C ON B.conc_cod=C.int_cod WHERE A.cont_cod = $Contrato AND C.conc_estatus='0' ORDER BY C.int_cod";
					//die($q);
					$RConceptos = $conn->Execute($q);
					if($RConceptos->EOF){
						$TFC="-1C";
						break;
					}
					$FechaIni = '01/01/2008';
					$FechaFin = '31/01/2008';
					while (!$RConceptos->EOF){
						$Trabajador=$RTrabajadores->fields['int_cod'];
						$Vacante=$RTrabajadores->fields['tra_vac'];
						$Concepto=$RConceptos->fields['int_cod'];
						if($RConceptos->fields['conc_tipo'] == 0)
							$CFormula=Formula($conn,$RConceptos->fields['conc_form'],$Trabajador,$Contrato,$FechaIni,$FechaFin,$RTrabajadores->fields['fechai'],$RTrabajadores->fields['fechae']);
							else if($RConceptos->fields['conc_tipo'] == 1)	
								$CFormula=Formula($conn,$RConceptos->fields['conc_aporte'],$Trabajador,$JsonRec->Contrato,$FechaIni,$FechaFin,$RTrabajadores->fields['fechai'],$RTrabajadores->fields['fechae']);
						$TFC[$Index]['T']=$Trabajador;
						$TFC[$Index]['C']=$Concepto;
						$TFC[$Index]['F']= !empty($CFormula) ? $CFormula : 0;
						$Index++;
						$RConceptos->movenext();
					}
					$RTrabajadores->movenext();
				}
				echo $JsonEnv->encode($TFC);
				break;
			}
			case 6:{
				$JsonRecI = new Services_JSON();
				$JsonRecI=$JsonRecI->decode(str_replace("\\","",$_POST['JsonEnvI']));
				$q = "SELECT id_categoria_programatica FROM puser.relacion_ue_cp WHERE id_unidad_ejecutora = '$JsonRec->Unidad' AND id_escenario = $JsonRec->Escenario ";
				//die($q);
				$RCategorias = $conn->Execute($q);
				while(!$RCategorias->EOF){
						$Categoria = $RCategorias->fields['id_categoria_programatica'];
						$q ="UPDATE rrhh.conc_part SET estimacion = 0 WHERE cat_cod = '$Categoria' AND escenario =  $JsonRec->Escenario ";
						$rExec= $conn->Execute($q);
						$RCategorias->movenext();
				}
				foreach( $JsonRecI AS $TCV ){
					$temp = 0;
				    $q = "SELECT DISTINCT (A.int_cod), A.estimacion FROM rrhh.conc_part AS A INNER JOIN puser.relacion_ue_cp AS B ON (A.cat_cod = B.id_categoria_programatica AND A.escenario = B.id_escenario) WHERE A.conc_cod = '$TCV->C' AND B.id_unidad_ejecutora = '$JsonRec->Unidad' AND B.id_escenario = $JsonRec->Escenario ";
					//die($q);
					$RAux = $conn->Execute($q);
					if(!$RAux->EOF){
					$temp = $RAux->fields['estimacion'] + ($TCV->F * 24);
					$identificador = $RAux->fields['int_cod'];
					$q = "UPDATE rrhh.conc_part SET estimacion = $temp WHERE int_cod = $identificador ";
					$RAux = $conn->Execute($q);
					}
				}
				echo "OPERACION REALIZADA CON EXITO"; 
				break;
			}
		}
	}catch( ADODB_Exception $e ){
		$CHNomina=!empty($CHNomina) ? $CHNomina : -1;
		$q = "DELETE FROM rrhh.historial_nom WHERE int_cod='$CHNomina' ";
		$rExec= $conn->Execute($q);
		//echo ERROR_CATCH_GENERICO;
		echo $e;
	}
} 
?>
