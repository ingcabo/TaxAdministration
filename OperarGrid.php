<?
include('adodb/adodb-exceptions.inc.php'); 
require ("comun/ini.php");
if(isset($_POST['JsonEnv'])){
	$JsonRec = new Services_JSON();
	$JsonEnv = new Services_JSON();
	$JsonRec=$JsonRec->decode(str_replace("\\","",$_POST['JsonEnv']));
	try {
		switch($JsonRec->Forma){
			case 0:	{
				switch($JsonRec->Accion){
					case 0:	{
						$q = "SELECT int_cod,var_cod,var_nom FROM rrhh.variable  WHERE var_estatus = 0 ORDER BY var_nom";
						$r = $conn->Execute($q);
						$i=0;
						while(!$r->EOF){
							$Variable[$i]['CI'] = (int)$r->fields['int_cod'];
							$Variable[$i]['CU'] = $r->fields['var_cod'];
							$Variable[$i]['N'] = $r->fields['var_nom'];
							$res = $conn->Execute("SELECT COALESCE(var_tra_val,'0') AS var_val FROM rrhh.var_tra WHERE tra_cod=$JsonRec->Trabajador AND var_cod=".$r->fields['int_cod']);
							$Variable[$i]['V'] = (!$res->EOF) ?  $res->fields['var_val'] : 0;
							$i++;
							$r->movenext();
						}
						if(is_array($Variable)){
							echo $JsonEnv->encode($Variable);
						}else{
							echo false;
						}
						break;
					}
					case 1:	{
						$q = "DELETE FROM rrhh.var_tra WHERE tra_cod=$JsonRec->Trabajador";
						$r = $conn->Execute($q);
						foreach($JsonRec->Variables as $Variable){
							$VariableAux = (!empty($Variable[1])) ? $Variable[1] : 0;
							$q = "INSERT INTO rrhh.var_tra (tra_cod,var_cod,var_tra_val) VALUES ($JsonRec->Trabajador,$Variable[0],$VariableAux)";
							$r = $conn->Execute($q);
						}
						echo "Operacion Realizada con Exito";
						break;
					}
					case 2:	{
						$q = "DELETE FROM rrhh.var_tra WHERE tra_cod=$JsonRec->Trabajador";
						$r = $conn->Execute($q);
						echo "Operacion Realizada con Exito";
						break;
					}
				}
				break;
			}
			case 1:	{
				switch($JsonRec->Accion){
					case 0:	{
						$q = "SELECT A.int_cod AS int_cod, A.tra_cod AS tra_cod, A.tra_nom AS tra_nom, A.tra_ape AS tra_ape, A.tra_ced AS tra_ced FROM rrhh.trabajador AS A INNER JOIN rrhh.cont_tra AS B ON A.int_cod=B.tra_cod WHERE B.cont_cod=$JsonRec->Contrato AND A.tra_estatus<>4 ORDER BY A.tra_nom,A.tra_ape";
						//die($q);
						$r = $conn->Execute($q);
						$i=0;
						while(!$r->EOF){
							$Trabajador[$i]['CI'] = (int)$r->fields['int_cod'];
							$Trabajador[$i]['CU'] = $r->fields['tra_cod'];
							$Trabajador[$i]['N'] = $r->fields['tra_nom'];
							$Trabajador[$i]['A'] = $r->fields['tra_ape'];
							$Trabajador[$i]['IU'] = $r->fields['tra_ced'];
							$res = $conn->Execute("SELECT COALESCE(var_tra_val,'0') AS var_val FROM rrhh.var_tra WHERE var_cod=$JsonRec->Variable AND tra_cod=".$r->fields['int_cod']);
							$Trabajador[$i]['V'] = (!$res->EOF) ? $res->fields['var_val'] : 0 ;
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
						foreach($JsonRec->Trabajadores as $Trabajador){
							$TrabajadorAux = (!empty($Trabajador[1])) ? $Trabajador[1] : 0;
							$q = "DELETE FROM rrhh.var_tra WHERE var_cod=$JsonRec->Variable AND tra_cod=$Trabajador[0]";
							$r = $conn->Execute($q);
							$q = "INSERT INTO rrhh.var_tra (var_cod,tra_cod,var_tra_val) VALUES ($JsonRec->Variable,$Trabajador[0],$TrabajadorAux)";
							$r = $conn->Execute($q);
						}
						echo "Operacion Realizada con Exito";
						break;
					}
					case 2:	{
						$q = "DELETE FROM rrhh.var_tra WHERE var_cod=$JsonRec->Variable";
						$r = $conn->Execute($q);
						echo "Operacion Realizada con Exito";
						break;
					}
				}
				break;
			}
			case 2:	{
				switch($JsonRec->Accion){
					case 0:	{
						$q = "SELECT C.conc_cod,B.conc_desc,C.conc_tipo,B.conc_val::numeric(20,2) FROM (rrhh.nomina as A INNER JOIN rrhh.nom_tra_conc as B ON A.int_cod=B.nom_cod) INNER JOIN rrhh.concepto as C ON B.conc_cod=C.int_cod WHERE A.cont_cod=$JsonRec->Contrato AND B.tra_cod=$JsonRec->Trabajador ORDER BY C.int_cod ";
						$r = $conn->Execute($q);
						$i=0;
						$Asignaciones=0;
						$Deducciones=0;
						while(!$r->EOF){
							if($r->fields['conc_val']!=0){
								$Concepto[$i]['CU'] = $r->fields['conc_cod'];
								$Concepto[$i]['N'] = $r->fields['conc_desc'];
								$Concepto[$i]['V'] = muestrafloat($r->fields['conc_val']);
								if($r->fields['conc_tipo']=='0'){
									$Asignaciones+=calculafloat($r->fields['conc_val'],2);
								}
								if($r->fields['conc_tipo']=='1'){
									$Deducciones+=calculafloat($r->fields['conc_val'],2);
									$Concepto[$i]['V']="-".$Concepto[$i]['V'];
								}
								$i++;
							}
							$r->movenext();
						}
						$Concepto[$i]['CU'] = muestrafloat($Asignaciones);
						$Concepto[$i]['N'] = muestrafloat($Deducciones);
						$Concepto[$i]['V'] = muestrafloat($Asignaciones-$Deducciones);
						if(is_array($Concepto)){
							echo $JsonEnv->encode($Concepto);
						}else{
							echo false;
						}
						break;
					}
					case 1:	{
						$q = "DELETE FROM rrhh.nom_tra_conc WHERE tra_cod=$JsonRec->Trabajador AND nom_cod=(SELECT int_cod FROM rrhh.nomina WHERE cont_cod=$JsonRec->Contrato)";
						$r = $conn->Execute($q);
						echo "Operacion Realizada con Exito";
						break;
					}
				}
				break;
			}
			case 3:	{
				switch($JsonRec->Accion){
					case 0:	{
						$q = "SELECT C.tra_cod,C.tra_nom,C.tra_ape,B.conc_val FROM (rrhh.nomina as A INNER JOIN rrhh.nom_tra_conc as B ON A.int_cod=B.nom_cod) INNER JOIN rrhh.trabajador as C ON B.tra_cod=C.int_cod WHERE A.cont_cod=$JsonRec->Contrato AND B.conc_cod=$JsonRec->Concepto ORDER BY C.int_cod";
						$r = $conn->Execute($q);
						$i=0;
						$TotalConcepto=0;
						while(!$r->EOF){
							if($r->fields['conc_val']!=0){
								$Trabajador[$i]['CU'] = $r->fields['tra_cod'];
								$Trabajador[$i]['N'] = $r->fields['tra_nom'];
								$Trabajador[$i]['A'] = $r->fields['tra_ape'];
								$Trabajador[$i]['V'] = muestrafloat($r->fields['conc_val']);
								$TotalConcepto+=calculafloat($r->fields['conc_val'],2);
								$i++;
							}
							$r->movenext();
						}
						$Trabajador[$i]['CU'] = muestrafloat($TotalConcepto);
						if(is_array($Trabajador)){
							echo $JsonEnv->encode($Trabajador);
						}else{
							echo false;
						}
						break;
					}
					case 1:	{
						$q = "DELETE FROM rrhh.nom_tra_conc WHERE conc_cod=$JsonRec->Concepto AND nom_cod=(SELECT int_cod FROM rrhh.nomina WHERE cont_cod=$JsonRec->Contrato)";
						$r = $conn->Execute($q);
						echo "Operacion Realizada con Exito";
						break;
					}
				}
				break;
			}
			case 4:	{
				switch($JsonRec->Accion){
					case 0:	{
						$TotalConceptoAnoA=0;
						$TotalConceptoAnoP=0;
						for($i=1;$i<=12;$i++){
							$Periodo= $i<10 ? "0".$i."/".$JsonRec->Ano : $i."/".$JsonRec->Ano;
							//ACUMULADO
							$q = "SELECT sum(B.conc_val::numeric(20,2)) AS conc_val FROM rrhh.acumulado AS A INNER JOIN rrhh.acum_tra_conc AS B ON A.int_cod=B.acum_cod WHERE periodo='$Periodo' AND (tra_cod=$JsonRec->Trabajador OR $JsonRec->Trabajador=-1)  AND (conc_cod=$JsonRec->Concepto OR $JsonRec->Concepto=-1)";
							$rA = $conn->Execute($q);
							$Valor[$i]['A']= muestrafloat(!$rA->EOF ? $rA->fields['conc_val'] : 0);
							$TotalConceptoAnoA+= !$rA->EOF ? calculafloat($rA->fields['conc_val'],2) : 0;
							//PAGOS
							$q = "SELECT sum(B.conc_val::numeric(20,2)) AS conc_val FROM rrhh.pago_acumulado AS A INNER JOIN rrhh.pago_acumulado_conc AS B ON A.int_cod=B.pago_acum_cod WHERE (A.tra_cod=$JsonRec->Trabajador OR $JsonRec->Trabajador=-1) AND (B.conc_cod=$JsonRec->Concepto OR $JsonRec->Concepto=-1) AND to_char(A.fecha,'mm/yyyy')='$Periodo'";
							$rP = $conn->Execute($q);
							$Valor[$i]['P']= muestrafloat(!$rP->EOF ? $rP->fields['conc_val'] : 0);
							$TotalConceptoAnoP+= !$rP->EOF ? calculafloat($rP->fields['conc_val'] ,2) : 0;
						}
						$Valor[13]['A']=muestrafloat($TotalConceptoAnoA-$TotalConceptoAnoP);
						$q = "SELECT (SUM(conc_val::numeric(20,2)) - (CASE WHEN (SELECT sum(B.conc_val) AS conc_val FROM rrhh.pago_acumulado AS A INNER JOIN rrhh.pago_acumulado_conc AS B ON A.int_cod=B.pago_acum_cod WHERE (A.tra_cod=$JsonRec->Trabajador OR $JsonRec->Trabajador=-1) AND (B.conc_cod=$JsonRec->Concepto OR $JsonRec->Concepto=-1)) IS NULL THEN 0 ELSE (SELECT sum(B.conc_val) AS conc_val FROM rrhh.pago_acumulado AS A INNER JOIN rrhh.pago_acumulado_conc AS B ON A.int_cod=B.pago_acum_cod WHERE (A.tra_cod=$JsonRec->Trabajador OR $JsonRec->Trabajador=-1) AND (B.conc_cod=$JsonRec->Concepto OR $JsonRec->Concepto=-1)) END)) AS total FROM rrhh.acum_tra_conc WHERE (tra_cod=$JsonRec->Trabajador OR $JsonRec->Trabajador=-1) AND (conc_cod=$JsonRec->Concepto OR $JsonRec->Concepto=-1)";
						$r = $conn->Execute($q);
						$Valor[14]['A']= !$r->EOF ? muestrafloat($r->fields['total']) : '0,00';
						if(is_array($Valor)){
							echo $JsonEnv->encode($Valor);
						}else{
							echo false;
						}
						break;
					}
				}
				break;
			}
			case 5:	{
				switch($JsonRec->Accion){
					case 0:	{
						//$q = "SELECT A.int_cod,A.conc_nom, sum(B.conc_val) as conc_val FROM rrhh.concepto AS A INNER JOIN rrhh.acum_tra_conc as B ON A.int_cod=B.conc_cod  WHERE A.conc_tipo='2' AND B.tra_cod=$JsonRec->Trabajador GROUP BY A.int_cod,A.conc_nom ORDER BY A.int_cod";
						//$q = "SELECT A.int_cod,A.conc_nom, sum(B.conc_val-C.conc_val) as conc_val  FROM rrhh.concepto AS A INNER JOIN rrhh.acum_tra_conc as B ON A.int_cod=B.conc_cod INNER JOIN rrhh.pago_acumulado_conc as C ON A.int_cod=C.conc_cod INNER JOIN rrhh.pago_acumulado as D ON C.pago_acum_cod=D.int_cod WHERE A.conc_tipo='2' AND B.tra_cod=$JsonRec->Trabajador AND D.tra_cod=$JsonRec->Trabajador GROUP BY A.int_cod,A.conc_nom ORDER BY A.int_cod";
						$q = "SELECT A.int_cod,A.conc_nom, (sum(B.conc_val::numeric(20,2))-";
						$q .= " (CASE WHEN";
						$q .= " ((SELECT sum(D.conc_val) FROM rrhh.pago_acumulado AS C INNER JOIN rrhh.pago_acumulado_conc AS D ON C.int_cod=D.pago_acum_cod WHERE C.tra_cod=$JsonRec->Trabajador AND A.int_cod=D.conc_cod) IS NULL)"; 
						$q .= " THEN 0 ELSE";
						$q .= " (SELECT sum(D.conc_val::numeric(20,2)) FROM rrhh.pago_acumulado AS C INNER JOIN rrhh.pago_acumulado_conc AS D ON C.int_cod=D.pago_acum_cod WHERE C.tra_cod=$JsonRec->Trabajador AND A.int_cod=D.conc_cod )"; 
						$q .= " END))"; 
						$q .= " AS conc_val";
						$q .= " FROM rrhh.concepto AS A INNER JOIN rrhh.acum_tra_conc as B ON A.int_cod=B.conc_cod";
						$q .= " WHERE A.conc_tipo='2' AND B.tra_cod=$JsonRec->Trabajador";
						$q .= " GROUP BY A.int_cod,A.conc_nom";
						$q .= " ORDER BY A.int_cod";
						$r = $conn->Execute($q);
						$i=0;
						$TotalConcepto=0;
						while(!$r->EOF){
							$Concepto[$i]['CI'] = $r->fields['int_cod'];
							$Concepto[$i]['N'] = $r->fields['conc_nom'];
							$Concepto[$i]['V'] = muestrafloat($r->fields['conc_val']);
							$TotalConcepto+=calculafloat($r->fields['conc_val'],2);
							$i++;
							$r->movenext();
						}
						$Concepto[$i]['CI'] = muestrafloat($TotalConcepto);
						if(is_array($Concepto)){
							echo $JsonEnv->encode($Concepto);
						}else{
							echo false;
						}
						break;
					}
				}
				break;
			}
			case 6:	{
				switch($JsonRec->Accion){
					case 0:	{
						$q = "SELECT cont_tipo FROM rrhh.contrato WHERE int_cod=$JsonRec->Contrato";
						$r = $conn->Execute($q);
						$TipoContrato=$r->fields['cont_tipo'];
						$q = "SELECT tra_sueldo FROM rrhh.trabajador WHERE int_cod=$JsonRec->Trabajador";
						$r = $conn->Execute($q);
						$Sueldo=empty($r->fields['tra_sueldo']) ? 1 : $r->fields['tra_sueldo'];
						$FechaIni=$JsonRec->Fecha;
						$FechaFin=FechaFin($TipoContrato,"/",$FechaIni,1,0);
						for($i=0;$i<$JsonRec->Cuotas;$i++){
							$CuotasAux[$i]['Nro']=$i+1;
							$CuotasAux[$i]['FechaIni']=$FechaIni;
							$CuotasAux[$i]['FechaFin']=$FechaFin;
							$CuotasAux[$i]['Porc']=($JsonRec->Monto/($JsonRec->Cuotas*$Sueldo))*100;
							$CuotasAux[$i]['Monto']=$JsonRec->Monto/$JsonRec->Cuotas;
							$CuotasAux[$i]['Estatus']="Activo";
							$FechaIni=FechaIni($TipoContrato,"/",$FechaFin,1,0);
							$FechaFin=FechaFin($TipoContrato,"/",$FechaIni,1,0);
						}
						if(is_array($CuotasAux)){
							echo $JsonEnv->encode($CuotasAux);
						}else{
							echo false;
						}
						break; 
					}
					case 1:	{
						$q = "SELECT * FROM rrhh.prestamo_cuotas WHERE pres_cod=$JsonRec->Prestamo ORDER BY cuota_nro";
						$r = $conn->Execute($q);
						$i=0;
						while(!$r->EOF){
							$CuotasAux[$i]['Nro']=$r->fields['cuota_nro'];
							$CuotasAux[$i]['FechaIni']=date("d/m/Y",strtotime($r->fields['cuota_nom_fec_ini']));
							$CuotasAux[$i]['FechaFin']=date("d/m/Y",strtotime($r->fields['cuota_nom_fec_fin']));
							$CuotasAux[$i]['Porc']=$r->fields['cuota_porc'];
							$CuotasAux[$i]['Monto']=$r->fields['cuota_monto'];
							$CuotasAux[$i]['Estatus']=$r->fields['cuota_estatus'];
							$i++;
							$r->movenext();
						}
						if(is_array($CuotasAux)){
							echo $JsonEnv->encode($CuotasAux);
						}else{
							echo false;
						}
						break; 
					}
					case 2:	{
						$j=0;
						foreach($JsonRec->CuotasDet as $CuotasDetAux){
							$CuotasAux[$j]['Nro']= $CuotasDetAux[0];
							if($JsonRec->Cuota>$CuotasDetAux[0]){
								$CuotasAux[$j]['FechaIni']= $CuotasDetAux[1];
								$CuotasAux[$j]['FechaFin']= $CuotasDetAux[2];
							}else{
								if($JsonRec->Cuota==$CuotasDetAux[0]){
									$FechaIni=$JsonRec->Fecha;
									$FechaFin=FechaFin($JsonRec->TipoContrato,"/",$JsonRec->Fecha,1,0);
								}
								$CuotasAux[$j]['FechaIni']= $FechaIni;
								$CuotasAux[$j]['FechaFin']= $FechaFin;
								$FechaIni=FechaIni($JsonRec->TipoContrato,"/",$FechaFin,1,0);
								$FechaFin=FechaFin($JsonRec->TipoContrato,"/",$FechaIni,1,0);
							}
							$CuotasAux[$j]['Porc']= $CuotasDetAux[3];
							$CuotasAux[$j]['Monto']= $CuotasDetAux[4];
							$CuotasAux[$j]['Estatus']= $CuotasDetAux[5];
							$j++;
						}
						if(is_array($CuotasAux)){
							echo $JsonEnv->encode($CuotasAux);
						}else{
							echo false;
						}
						break; 
					}
				}
				break;
			}
			case 7:	{  //PREPARAR VACACIONES
				switch($JsonRec->Accion){
					case 0:	{
						$q = "SELECT B.cons_val FROM rrhh.mod_conc AS A INNER JOIN rrhh.constante AS B ON A.conc_cod=B.int_cod WHERE A.modulo=2 "; // Dias de Disfrute
						$rD = $conn->Execute($q);
						$DiasDisfrute= !$rD->EOF ? $rD->fields['cons_val'] : 0;

						$q = "SELECT B.cons_val FROM rrhh.mod_conc AS A INNER JOIN rrhh.constante AS B ON A.conc_cod=B.int_cod WHERE A.modulo=3 "; // Dias de Disfrute
						$rA = $conn->Execute($q);
						$DiasAdicionales= !$rA->EOF ? $rA->fields['cons_val'] : 0;
						
						$q = "SELECT A.int_cod,A.tra_nom,A.tra_ape,A.tra_fec_ing FROM (rrhh.trabajador AS A INNER JOIN rrhh.departamento AS B ON A.dep_cod=B.int_cod) INNER JOIN rrhh.division AS C ON B.div_cod=C.int_cod WHERE A.tra_estatus!=4 AND A.tra_estatus!=5 AND C.emp_cod=".$_SESSION['EmpresaL']." ORDER BY A.int_cod";
						$rT = $conn->Execute($q);
						$j=0;
						while(!$rT->EOF){
							$AnosAntiguedad=(strtotime(guardafecha($JsonRec->FechaIni)) - strtotime($rT->fields['tra_fec_ing']))/31536000;
							if($AnosAntiguedad>=1){ //AQUI
								$DiasF=$DiasDisfrute+($DiasAdicionales* (((int)$AnosAntiguedad)-1));
								$Resul[$j]['CI']= $rT->fields['int_cod'];
								$Resul[$j]['N']= cadena($rT->fields['tra_nom'])." ".cadena($rT->fields['tra_ape']);
								$Resul[$j]['FI']= muestrafecha($rT->fields['tra_fec_ing']);
								$Resul[$j]['FIV']= $JsonRec->FechaIni;
								$Resul[$j]['FFV']= FechaFinVacaciones($conn,$JsonRec->FechaIni,$DiasF);
								$Resul[$j]['DI']= $DiasF;
								$j++;
							}
							$rT->movenext();
						}
						if(is_array($Resul)){
							echo $JsonEnv->encode($Resul);
						}else{
							echo false;
						}  
						break; 
					}
					case 1:	{
						$q = "SELECT A.int_cod,A.tra_nom,A.tra_ape,A.tra_fec_ing,D.fecha_ini,D.fecha_fin,D.dias,D.dias_pendientes FROM ((rrhh.trabajador AS A INNER JOIN rrhh.departamento AS B ON A.dep_cod=B.int_cod) INNER JOIN rrhh.division AS C ON B.div_cod=C.int_cod) INNER JOIN rrhh.preparar_vac AS D ON A.int_cod=D.tra_cod WHERE A.tra_estatus!=4 AND A.tra_estatus!=5 AND C.emp_cod=".$_SESSION['EmpresaL']." ORDER BY A.int_cod";
						$rV = $conn->Execute($q);
						$j=0;
						while(!$rV->EOF){
							$Resul[$j]['CI']= $rV->fields['int_cod'];
							$Resul[$j]['N']= cadena($rV->fields['tra_nom'])." ".cadena($rV->fields['tra_ape']);
							$Resul[$j]['FI']= muestrafecha($rV->fields['tra_fec_ing']);
							$Resul[$j]['FIV']= muestrafecha($rV->fields['fecha_ini']);
							$Resul[$j]['FFV']= muestrafecha($rV->fields['fecha_fin']);
							$Resul[$j]['D']= $rV->fields['dias'];
							$Resul[$j]['DP']= $rV->fields['dias_pendientes'];
							$j++;
							$rV->movenext();
						}
						if(is_array($Resul)){
							echo $JsonEnv->encode($Resul);
						}else{
							echo false;
						}
					break;	  
					}
					}
				break;					 
				}
		case 8:	{  //Sueldos por unidad ejecutora
				switch($JsonRec->Accion){
					case 0: {
						//$q = "SELECT  T.int_cod, T.tra_cod, T.tra_nom, T.tra_ape, T.tra_ced, T.tra_ape, T.tra_sueldo, T.tra_sueldo_pre FROM (rrhh.trabajador AS T INNER JOIN rrhh.departamento AS D ON  D.int_cod = T.dep_cod)";
				  		//$q.= " WHERE D.unidad_ejecutora_cod = '$JsonRec->Unidad'";
						$q = "SELECT c.car_nom, c.car_sueldo, t.car_cod,COUNT(t.car_cod) AS cantidad, COALESCE(t.tra_sueldo_pre,0) AS suel_estimado FROM rrhh.trabajador AS t ";
						$q.= "INNER JOIN rrhh.departamento AS d ON (t.dep_cod = d.int_cod) ";
						$q.= "INNER JOIN rrhh.cargo AS c ON (t.car_cod = c.int_cod) ";
						$q.= "WHERE t.dep_cod = $JsonRec->Unidad AND t.tra_estatus_pre = 0 AND (t.tra_estatus = 0 OR t.tra_estatus = 1 OR (t.tra_estatus = 5 AND t.tra_ced is not Null)) ";
						if($JsonRec->Cargo != 0){
							$q.= " AND t.car_cod = $JsonRec->Cargo ";
							$q.= " GROUP BY 1,2,3,5 ";
						}else{
						    $q.= " GROUP BY 1,2,3,5 "; 
							$q.= " ORDER BY 2 DESC";
						}							
						//die($q);
						$r = $conn->Execute($q);
						$i=0;
						while(!$r->EOF){
							$Trabajador[$i]['cod'] = (int)$r->fields['car_cod'];
							$Trabajador[$i]['nomb'] = $r->fields['car_nom'];
							$Trabajador[$i]['suel'] = $r->fields['car_sueldo'];
							$Trabajador[$i]['cant'] = $r->fields['cantidad'];

							//die("aqui". $r->fields['suel_estimado']);
							$Trabajador[$i]['suelEst'] = !empty($r->fields['suel_estimado']) ? $r->fields['suel_estimado'] : $r->fields['car_sueldo'];
							//die("aqui".$Trabajador[$i]['suelEst']);
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
					case 1: {
						foreach($JsonRec->Cargo as $Cargos){
							//AGREGO O ELIMINO LOS CARGOS SOLICITADOS
							//die ('aqui '.$Cargos[2]);
							$aux = cantidadCargos($conn, $Cargos[0], $Cargos[2], $JsonRec->Unidad);
							//die(var_dump($aux));
							//$TrabajadorAux = (!empty($Trabajador[1])) ? $Trabajador[1] : 0;
							$q = "UPDATE rrhh.trabajador SET tra_sueldo_pre = $Cargos[1] WHERE car_cod= $Cargos[0]";
							//die($q);
							$r = $conn->Execute($q);
						}
						echo "Operacion Realizada con Exito";
						break;
					break;
					}
				break;
				}
			break;
			}
		case 9:{ //Orden de cargos por departamentos
				switch($JsonRec->Accion){
					case 0: {
						$q = "SELECT  int_cod, dep_cod, dep_nom FROM rrhh.departamento  
						WHERE dep_estatus = 0 ORDER BY int_cod, dep_cod";
						//var_dump($q);
						$r = $conn->Execute($q);
						$i=0;
						while(!$r->EOF){
							$Departamento[$i]['DI'] = (int)$r->fields['int_cod'];
							$Departamento[$i]['DC'] = $r->fields['dep_cod'];
							$Departamento[$i]['DN'] = $r->fields['dep_nom'];
							$q = "SELECT COALESCE(orden,'0') AS valor FROM rrhh.dep_carg WHERE car_cod = $JsonRec->Cargo AND dep_cod=".$r->fields['int_cod'];
							$res = $conn->Execute($q);
							$Departamento[$i]['V'] = (!$res->EOF) ?  $res->fields['valor'] : 0;
							$i++;
							$r->movenext();
						}
						if(is_array($Departamento)){
							echo $JsonEnv->encode($Departamento);
						}else{
							echo false;
						}
						break;
					}
				}
				break;
			}
		}	
		
	}catch( ADODB_Exception $e ){
		echo ($JsonRec->Accion!=0) ? ERROR_CATCH_GENERICO : false;
		//echo $e;
	}
}
	
	function query($conn, $idUe, $tipo, $idCargo, $contador, $cant = ''){
		//return $contador;
		$sql = ($contador==0) ? "SELECT t.int_cod " : "SELECT COUNT(t.int_cod) AS cantidad ";
		$sql.= "FROM rrhh.trabajador AS t ";
		$sql.= "INNER JOIN rrhh.departamento AS d ON (t.dep_cod = d.int_cod) ";
		$sql.= "INNER JOIN rrhh.cargo AS c ON (t.car_cod = c.int_cod) ";
		$sql.= "WHERE t.dep_cod = '$idUe' AND t.tra_estatus_pre = $tipo AND (t.tra_estatus = 0 OR t.tra_estatus = 1 OR (t.tra_estatus = 5 AND t.tra_ced is not Null)) ";
		$sql.= "AND t.car_cod = $idCargo ";
		$sql.=  !empty($cant) ? "LIMIT $cant " : "";
		//die($sql);
		$row = $conn->Execute($sql);
		//return $sql;
		return $row;
	}
	
	function cantidadCargos($conn, $idCargo, $newCant, $idUe){
		$q = "SELECT t.car_cod,COUNT(t.car_cod) AS cantidad FROM rrhh.trabajador AS t ";
		$q.= "INNER JOIN rrhh.departamento AS d ON (t.dep_cod = d.int_cod) ";
		$q.= "INNER JOIN rrhh.cargo AS c ON (t.car_cod = c.int_cod) ";
		$q.= "WHERE t.dep_cod = '$idUe' AND t.tra_estatus_pre = 0 AND (t.tra_estatus = 0 OR t.tra_estatus = 1 OR (t.tra_estatus = 5 AND t.tra_ced is not Null)) ";
		$q.= " AND t.car_cod = $idCargo ";
		$q.= " GROUP BY 1";
		//return $newCant;
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$cantActual = $r->fields['cantidad'];	
			if($newCant < $cantActual){
				$cantEliminar = $cantActual - $newCant;
				$row = query($conn, $idUe, 0, $idCargo, 0, $cantEliminar);
				//return $row;
				while(!$row->EOF){
					$idTrab = $row->fields['int_cod'];
					$sql = "UPDATE rrhh.trabajador SET tra_estatus_pre = 1 WHERE int_cod = $idTrab ";
					//return $sql;
					$res = $conn->Execute($sql);
					$row->movenext();
				}
				return;
			}else if ($newCant > $cantActual){
				$cantAgregar = $newCant - $cantActual;
				//die(''.$cantAgregar);
				$row = query($conn, $idUe, 1, $idCargo, 1);
				//return $row;
				if(!$row->EOF){
					//die(''.$row->fields['cantidad'].'  '.$cantAgregar);
					if($row->fields['cantidad'] >= $cantAgregar){
						$res = query($conn, $idUe, 1, $idCargo, 0, $cantAgregar);
						//return $res;
						while(!$res->EOF){
							$idTrab = $res->fields['int_cod'];
							$sql = "UPDATE rrhh.trabajador SET tra_estatus_pre = 0 WHERE int_cod = $idTrab";
							$r = $conn->Execute($sql);
							$res->movenext();
						}




					}else{
						echo 'entro';
					}
				}
				return;
			}else
				return;
	}
	
	
		
		
}
?>
