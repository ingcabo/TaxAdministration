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
				$q = "SELECT * FROM rrhh.historial_nom WHERE cont_cod=$JsonRec->Contrato ORDER BY int_cod desc";
				$r = $conn->Execute($q);
				$q = "SELECT * FROM rrhh.contrato WHERE int_cod=$JsonRec->Contrato";
				$rC = $conn->Execute($q);
				$Resultado[0]=$r->EOF ? muestrafecha($rC->fields['cont_fec_ini']) : FechaIni($rC->fields['cont_tipo'],"/",muestrafecha($r->fields['nom_fec_fin']),1,0);
				$Resultado[1]=FechaFin($rC->fields['cont_tipo'],"/",$Resultado[0],1,0,$conn,$JsonRec->Contrato);
				$Resultado[2]=$rC->fields['cont_tipo']==3 ? true : false;
				echo $JsonEnv->encode($Resultado);
				break;
			}
			case 1:	{
				$q = "SELECT * FROM rrhh.nomina WHERE cont_cod=$JsonRec->Contrato";
				$r = $conn->Execute($q);
				if (!$r->EOF){
					$Fecha=split ("-" ,$r->fields['nom_fec_ini']);
					$FechaIni= $Fecha[2]."/".$Fecha[1]."/".$Fecha[0];
					$Fecha=split ("-" ,$r->fields['nom_fec_fin']);
					$FechaFin= $Fecha[2]."/".$Fecha[1]."/".$Fecha[0];
				}
				$Resultado[0]=$FechaIni;
				$Resultado[1]=$FechaFin;
				echo $JsonEnv->encode($Resultado);
				break;
			}
			case 2:	{
				$q = "DELETE FROM rrhh.parametrossistema WHERE 1=1";
				$r = $conn->Execute($q);
				$q = "INSERT INTO rrhh.parametrossistema (multi_empresa,emp_pred,enlace_presupuesto) VALUES ('$JsonRec->MultiEmpresa',$JsonRec->Empresa,'$JsonRec->Enlace')";
				$r = $conn->Execute($q);
				break;
			}
			case 3:	{
				$q = "SELECT int_cod FROM rrhh.variable WHERE var_tipo=1";
				$r = $conn->Execute($q);
				while (!$r->EOF){
					$Variable=$r->fields['int_cod'];
					$conn->Execute("UPDATE rrhh.var_tra SET var_tra_val=0 WHERE var_cod=$Variable");
					$r->movenext();
				}
				echo "Operacion Realizada con Exito";
				break;
			}
			case 4:	{
				$Resultado[0]=false;
				switch($JsonRec->TipoVar){
					case "Cons":{
						$q = "SELECT cons_val FROM rrhh.constante WHERE int_cod=$JsonRec->Codigo";
						$r = $conn->Execute($q);
						$Resultado[0]= !$r->EOF ? (int)$r->fields['cons_val'] : false;
						break;
					}
					case "Vari":{
						$q = "SELECT var_tra_val FROM rrhh.var_tra WHERE var_cod=$JsonRec->Codigo";
						$r = $conn->Execute($q);
						$Resultado[0]= !$r->EOF ? (int)$r->fields['var_tra_val'] : false;
						break;
					}
				}
				echo $JsonEnv->encode($Resultado);
				break;
			}
			case 5:	{ //PAGOS DE ACUMULADOS
				switch($JsonRec->Accion){
					case 0:{
						$q="SELECT int_cod FROM rrhh.concepto WHERE conc_nom='$JsonRec->Concepto'";
						$r= $conn->Execute($q);
						if(!$r->EOF){
							echo $r->fields['int_cod'];
						}
						break;
					}
					case 1:{
						$q = "SELECT int_cod, tra_fec_ing AS fechai,tra_fec_egr AS fechae FROM rrhh.trabajador WHERE int_cod='$JsonRec->Trabajador'";
						$RTrabajadores = $conn->Execute($q);
						$q = "SELECT conc_form,conc_tipo FROM rrhh.concepto WHERE int_cod='$JsonRec->Concepto'";
						$RConceptos = $conn->Execute($q);
						if(!$RTrabajadores->EOF && !$RConceptos->EOF){
						 	// ESTO ES PARA QUE ME TOME LA FECHA DE INICIO Y FIN DE LA NOMINA LAS QU HAYAN SIDO PROGRAMADAS EN EL MODULO DE VACACIONES
							$q = "SELECT * FROM rrhh.preparar_vac WHERE tra_cod=".$RTrabajadores->fields['int_cod'];
							$rFVac = $conn->Execute($q);
							if(!$rFVac->EOF){
								$FechaIni=muestrafecha($rFVac->fields['fecha_ini']);
								$FechaFin=muestrafecha($rFVac->fields['fecha_fin']);
							}else{
								$FechaIni=date("d/m/Y");
								$FechaFin=date("d/m/Y");
							}
						 	// FIN
							$CFormula=Formula($conn,$RConceptos->fields['conc_form'],$RTrabajadores->fields['int_cod'],-1,'','',$RTrabajadores->fields['fechai'],$RTrabajadores->fields['fechae']);
							$TFC['F']=!empty($CFormula) ? $CFormula : 0;
							$TFC['T']=$RConceptos->fields['conc_tipo'];
							echo $JsonEnv->encode($TFC);
						}
						break;
					}
					case 2:{
						$Fecha=date("Y-m-d");
						$q = "INSERT INTO rrhh.pago_acumulado (tra_cod,descripcion,fecha,observaciones,tipo) VALUES ($JsonRec->Trabajador,'$JsonRec->Descripcion','$Fecha','$JsonRec->Obser',$JsonRec->Tipo)";
						$RAux = $conn->Execute($q);
						$q = "SELECT MAX(int_cod) as int_cod FROM rrhh.pago_acumulado";
						$Pago = $conn->Execute($q);
						$PagoCI=$Pago->fields['int_cod'];
						foreach( $JsonRec->Conceptos AS $CV ){
							$q = "INSERT INTO rrhh.pago_acumulado_conc (pago_acum_cod,conc_cod,conc_tipo,conc_val) VALUES ($PagoCI,$CV[0],$CV[2],$CV[1])";
							$RAux = $conn->Execute($q);
						}
						if($JsonRec->Tipo==2){
//							$q = "UPDATE rrhh.trabajador SET tra_estatus='3' WHERE int_cod=$JsonRec->Trabajador";
//							$RAux = $conn->Execute($q);
						}
						echo true;
						break;
					}
					case 3:{
						$q="SELECT SUM(B.cuota_monto) AS saldo FROM rrhh.prestamo AS A INNER JOIN rrhh.prestamo_cuotas AS B ON A.int_cod=B.pres_cod WHERE A.tra_cod=$JsonRec->Trabajador AND A.pres_estatus=1 AND B.cuota_estatus!='Cancelado' GROUP BY A.int_cod";
						$rP= $conn->Execute($q);
						$q="SELECT A.int_cod,A.conc_nom,A.conc_tipo FROM rrhh.concepto AS A INNER JOIN rrhh.mod_conc AS B ON A.int_cod=B.conc_cod WHERE B.modulo=0";
						$rC= $conn->Execute($q);
						if(!$rP->EOF && !$rC->EOF){
							$PC[0]=$rC->fields['int_cod'];
							$PC[1]=$rC->fields['conc_nom'];
							$PC[2]= $rC->fields['conc_tipo']=='1' ? -1*$rP->fields['saldo'] : $rP->fields['saldo'];
							echo $JsonEnv->encode($PC);
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
					case 0:{
						$q = "SELECT tra_fec_ing,tra_sueldo FROM rrhh.trabajador WHERE int_cod=$JsonRec->Trabajador";
						$r = $conn->Execute($q);
						$Resultado[0]= !$r->EOF ? date("d/m/Y",strtotime($r->fields['tra_fec_ing'])) : "";
						$Resultado[1]= !$r->EOF ? muestrafloat($r->fields['tra_sueldo']) : "";
						echo $JsonEnv->encode($Resultado);
						break;
					}
					case 1:{
						$q = "SELECT cont_tipo FROM rrhh.contrato WHERE int_cod=$JsonRec->Contrato";
						$r = $conn->Execute($q);
						echo !$r->EOF ? $r->fields['cont_tipo'] : false;
						break;
					}
					case 3:{
						$q = "SELECT (SUM(conc_val) - (CASE WHEN (SELECT sum(B.conc_val) AS conc_val FROM rrhh.pago_acumulado AS A INNER JOIN rrhh.pago_acumulado_conc AS B ON A.int_cod=B.pago_acum_cod WHERE A.tra_cod=$JsonRec->Trabajador) IS NULL THEN 0 ELSE (SELECT sum(B.conc_val) AS conc_val FROM rrhh.pago_acumulado AS A INNER JOIN rrhh.pago_acumulado_conc AS B ON A.int_cod=B.pago_acum_cod WHERE A.tra_cod=$JsonRec->Trabajador) END)) AS total FROM rrhh.acum_tra_conc WHERE tra_cod=$JsonRec->Trabajador";
						$r = $conn->Execute($q);
						echo !empty($r->fields['total']) ? $r->fields['total'] : 0;
						break;
					}
					case 4:{
						$q = "SELECT int_cod FROM rrhh.prestamo WHERE tra_cod=$JsonRec->Trabajador AND pres_estatus=1";
						$r = $conn->Execute($q);
						echo !$r->EOF ? true : false;
						break;
					}
					case 5:{
						$q = "DELETE FROM rrhh.mod_conc WHERE modulo=0";
						$r = $conn->Execute($q);
						$q = "INSERT INTO rrhh.mod_conc (modulo,conc_cod) VALUES (0,$JsonRec->Concepto)";
						$r = $conn->Execute($q);
						echo true;
						break;
					}
				}
				break;
			}
			case 7:	{
				switch($JsonRec->Accion){
					case "HABPRIV.txt":{
						$q = "SELECT emp_rif FROM rrhh.empresa WHERE int_cod=".$_SESSION['EmpresaL'];
						$r = $conn->Execute($q);
						$rifAux= split("-",$r->fields['emp_rif'],20);
						$rif= $rifAux[0].$rifAux[1];
						for($i=0;$i<(9-strlen($rifAux[1]));$i++){
							$rif.=" ";
						}
						$rif.=$rifAux[2];
						for($i=0;$i<(2-strlen($rifAux[2]));$i++){
							$rif.=" ";
						}
						$q = "SELECT A.int_cod,A.tra_ced,A.tra_nom,A.tra_ape,A.tra_fec_nac,A.tra_nac FROM rrhh.trabajador AS A INNER JOIN rrhh.cont_tra AS B ON A.int_cod=B.tra_cod WHERE B.cont_cod=$JsonRec->Contrato";
						$r = $conn->Execute($q);
						$fp = fopen("HABPRIV.txt","w");
						while(!$r->EOF){
							$CedulaLetra= $r->fields['tra_nac']==0 ? 'V' : 'E';
							$CedulaNumero=str_replace(".","",$r->fields['tra_ced']);
							$registro=$rif.$CedulaLetra.$CedulaNumero;
							for($i=0;$i<(10-strlen($CedulaNumero));$i++){
								$registro.=" ";
							}
							$NombreAux=split(" ",$r->fields['tra_nom'],20);
							$Nombre= is_array($NombreAux) ? $NombreAux[0] : $NombreAux;
							$ApellidoAux=split(" ",$r->fields['tra_ape'],20);
							$Apellido= is_array($ApellidoAux) ? $ApellidoAux[0] : $ApellidoAux;
							$NombreApellido=$Nombre." ".$Apellido;
							$registro.=$NombreApellido;
							for($i=0;$i<(30-strlen($NombreApellido));$i++){
								$registro.=" ";
							}
							$FechaNac=split("-",$r->fields['tra_fec_nac'],20);
							$registro.=$FechaNac[0].$FechaNac[1].$FechaNac[2];
							$registro.=$r->fields['tra_sex']==0 ? "M" : "F";
							$registro.="0";
							$Trabajador=$r->fields['int_cod'];
							$Mes= strlen($JsonRec->Mes)==1 ? ("0".$JsonRec->Mes) : $JsonRec->Mes;
							$q = "SELECT SUM(B.conc_val) AS valor FROM rrhh.historial_nom AS A INNER JOIN rrhh.hist_nom_tra_conc AS B ON A.int_cod=B.hnom_cod WHERE A.cont_cod=$JsonRec->Contrato AND B.tra_cod=$Trabajador AND B.conc_cod=$JsonRec->Concepto AND to_char(nom_fec_ini,'mm')='$Mes' AND to_char(nom_fec_ini,'yy')='$JsonRec->Ano' AND to_char(nom_fec_fin,'mm')='$Mes' AND to_char(nom_fec_fin,'yy')='$JsonRec->Ano'";
							$rC = $conn->Execute($q);
							if(!$rC->EOF){
								$Monto= !empty($rC->fields['valor']) ? $rC->fields['valor'] : 0;
							}else{
								$Monto=0;
							}
							$Monto=str_replace(".","",str_replace(",","",muestrafloat($Monto)));
							$registro.=$Monto;
							for($i=0;$i<(15-strlen($Monto));$i++){
								$registro.=" ";
							}
							$registro.=$Mes.$JsonRec->Ano;
							fputs($fp,$registro); 
							fwrite($fp,"\r\n"); 
							$r->movenext();
						}
						fclose($fp);
						echo true;
						break; 
					}
					case "NOMINA.txt":{
						$q = "SELECT B.id,A.nro_cuenta,B.descripcion FROM finanzas.cuentas_bancarias AS A INNER JOIN public.banco AS B ON A.id_banco=B.id WHERE A.id=$JsonRec->Cuenta";
						$rC = $conn->Execute($q);
						//die($q);
						
						
						$q = "SELECT sum(CASE WHEN (C.conc_tipo=1) THEN (A.conc_val::numeric(20,2)*-1) ELSE (A.conc_val::numeric(20,2)) END) AS valor,B.tra_ced,B.tra_num_cta FROM rrhh.hist_nom_tra_conc AS A INNER JOIN rrhh.trabajador AS B ON A.tra_cod=B.int_cod INNER JOIN rrhh.concepto AS C ON A.conc_cod=C.int_cod  WHERE A.hnom_cod=$JsonRec->Nomina AND B.ban_cod=$Banco AND B.tra_tip_pag=2 GROUP BY A.tra_cod,B.tra_ced,B.tra_num_cta ORDER BY A.tra_cod";
						//die($q);
						$r = $conn->Execute($q);
						$fp = fopen("NOMINA.txt","w");
						while(!$r->EOF){
							$CedulaNumero=trim(str_replace(".","",$r->fields['tra_ced']));
							$registro=$CedulaNumero;
							for($i=0;$i<(15-strlen($CedulaNumero));$i++){
								$registro.=" ";
							}
							$cuenta=trim(str_replace(".","",str_replace("-","",$r->fields['tra_num_cta'])));
							for($i=0;$i<(12-strlen($cuenta));$i++){
								$registro.="0";
							}
							$registro.=$cuenta;
							$monto=str_replace(".","",$r->fields['valor']);
							for($i=0;$i<(15-strlen($monto));$i++){
								$registro.="0";
							}
							$registro.=$monto."Nomina......";
							fputs($fp,$registro); 
							fwrite($fp,"\r\n"); 
							$r->movenext();
						} 
						fclose($fp);
						echo true;
						break; 
					}
					case "NOMINACENTRAL.txt":{
						$q = "SELECT B.id,A.nro_cuenta,B.descripcion FROM finanzas.cuentas_bancarias AS A INNER JOIN public.banco AS B ON A.id_banco=B.id WHERE A.id=$JsonRec->Cuenta";
						$rC = $conn->Execute($q);
						//die($q);
						$Banco=$rC->fields['id'];
						$q = "SELECT sum(CASE WHEN (C.conc_tipo=1) THEN (A.conc_val::numeric(20,2)*-1) ELSE (A.conc_val::numeric(20,2)) END) AS valor,B.tra_ced,B.tra_num_cta FROM rrhh.hist_nom_tra_conc AS A INNER JOIN rrhh.trabajador AS B ON A.tra_cod=B.int_cod INNER JOIN rrhh.concepto AS C ON A.conc_cod=C.int_cod  WHERE A.hnom_cod=$JsonRec->Nomina AND B.ban_cod=$Banco AND B.tra_tip_pag=2 GROUP BY A.tra_cod,B.tra_ced,B.tra_num_cta ORDER BY A.tra_cod";
						//die($q);
						$r = $conn->Execute($q);
						$fp = fopen("NOMINACENTRAL.txt","w");
						$correlativo=1;
						$total=0;
						while(!$r->EOF){
							$Constante = 'AC202';
							$registro = $Constante;
							$cuenta=trim(str_replace(".","",str_replace("-","",$r->fields['tra_num_cta'])));
							for($i=0;$i<(10-strlen($cuenta));$i++){
								$registro.="0";
							}
							$registro.=$cuenta;
							for($i=0;$i<(8-strlen($correlativo));$i++){
								$registro.="0";
							}
							$registro.=$correlativo;
							$monto=str_replace(".","",$r->fields['valor']);
							$total+=$monto;
							for($i=0;$i<(13-strlen($monto));$i++){
								$registro.="0";
							}
							$registro.=$monto."0506";
							fputs($fp,$registro); 
							fwrite($fp,"\r\n");
							$correlativo++;
							$r->movenext();
						}
						$Constante = 'AC402';
						$registro = $Constante;
						$cuenta=trim(str_replace(".","",str_replace("-","",$rC->fields['nro_cuenta'])));
						for($i=0;$i<(10-strlen($cuenta));$i++){
							$registro.="0";
						}
						$registro.=$cuenta;
						for($i=0;$i<(8-strlen($correlativo));$i++){
							$registro.="0";
						}
						$registro.=$correlativo;
						for($i=0;$i<(13-strlen($total));$i++){
							$registro.="0";
						}
						$registro.=$total."0506";
						fputs($fp,$registro); 
						fwrite($fp,"\r\n"); 					
						fclose($fp);
						echo true;
						break; 
					}
					case "NOMINAVENEZUELA.DAT":{
						$q = "SELECT e.emp_nom FROM rrhh.empresa as e WHERE e.int_cod = ".$_SESSION['EmpresaL'];
						$rE = $conn->Execute($q);
						
						$q = "SELECT B.id,A.nro_cuenta,B.descripcion FROM finanzas.cuentas_bancarias AS A INNER JOIN public.banco AS B ON A.id_banco=B.id WHERE A.id=$JsonRec->Cuenta";
						$rC = $conn->Execute($q);
						
						$Banco=$rC->fields['id'];
						
						$q = "SELECT sum(CASE WHEN (A.conc_tipo=1) THEN (A.conc_val::numeric(20,2)*-1) ELSE (A.conc_val::numeric(20,2)) END) AS valor FROM rrhh.hist_nom_tra_conc AS A INNER JOIN rrhh.trabajador AS B ON A.tra_cod=B.int_cod  WHERE A.hnom_cod=$JsonRec->Nomina AND B.ban_cod=$Banco AND B.tra_tip_pag=2 ";
						$rI =  $conn->Execute($q);						
												
						
						$q = "SELECT sum(CASE WHEN (A.conc_tipo=1) THEN (A.conc_val::numeric(20,2)*-1) ELSE (A.conc_val::numeric(20,2)) END) AS valor, B.tra_ced, B.tra_nom, B.tra_ape, B.tra_num_cta, B.tra_tipo_cta FROM rrhh.hist_nom_tra_conc AS A INNER JOIN rrhh.trabajador AS B ON A.tra_cod=B.int_cod  WHERE A.hnom_cod=$JsonRec->Nomina AND B.ban_cod=$Banco AND B.tra_tip_pag=2 GROUP BY A.tra_cod,B.tra_ced,B.tra_num_cta,B.tra_nom, B.tra_ape, B.tra_tipo_cta ORDER BY A.tra_cod";
						//die($r);
						$r = $conn->Execute($q);
						
						$fp = fopen("NOMINAVENEZUELA.DAT","w");
						
						$Empresa = $rE->fields['emp_nom'];
						$registro = $Empresa;
						for($i=0;$i<(41-strlen($Empresa));$i++){
								$registro.=" ";
						}
						//$Constante = '0';
						//$registro.=$Constante;
						$registro.= trim(str_replace(".","",str_replace("-","",$rC->fields['nro_cuenta']))); 
						$Codigo = '0'.$JsonRec->Cuenta;
						$registro.=$Codigo;
						
						$Fecha= date('d').'/'.date('m').'/'.date('y');
						$registro.=$Fecha;
						$Monto =  $rI->fields['valor'];  //'000785000000003293';
						$registro.=$Monto;						 
						fputs($fp,$registro); 
						fwrite($fp,"\r\n");
						
						while(!$r->EOF){
							$Tipo = $r->fields['tra_tipo_cta'];
							$registro = $Tipo;
							$cuenta=trim(str_replace(".","",str_replace("-","",$r->fields['tra_num_cta'])));
							$registro.=$cuenta;
							$sueldo=str_replace(".","",$r->fields['valor']);
							for($i=0;$i<(10-strlen($sueldo));$i++){
								$registro.="0";
							}						
							$registro.=$sueldo;
							$registro.= $Tipo;
							$Constante = '770';
							$registro.=$Constante;
							$apellido = trim($r->fields['tra_ape']);
							$nombre = trim($r->fields['tra_nom']);
							$trabajador =  $apellido.' '.$nombre;
							$registro.= $trabajador;
							for($i=0;$i<(41-strlen(utf8_decode($trabajador)));$i++){
								$registro.=" ";
							}
							$Cedula = $r->fields['tra_ced'];
							for($i=0;$i<(10-strlen(utf8_decode($Cedula)));$i++){
								$registro.="0";
							}
							$registro.= $Cedula.'003293';							
							fputs($fp,$registro); 
							fwrite($fp,"\r\n");
							$r->movenext();
						}					
						fclose($fp);
						echo true;
						break; 
					}
					case "HISTRAB.txt":{
						$q = "SELECT A.tra_ced,A.tra_nom,A.tra_ape, A.tra_fec_ing, B.dep_nom, B.car_nom, B.fun_nom, A.tra_num_cta, B.tra_sueldo, A.tra_tipo FROM rrhh.trabajador AS A INNER JOIN rrhh.hist_nom_tra_sueldo AS B ON A.int_cod=B.tra_cod WHERE B.hnom_cod = $JsonRec->Nomina ";
						$TS = $conn->Execute($q);
						//die($q);
						$fp = fopen("HISTRAB.txt","w");
						while(!$TS->EOF){
							$CedulaNumero=trim(str_replace(".","",$TS->fields['tra_ced']));
							$registro=$CedulaNumero;
							for($i=0;$i<(15-strlen($CedulaNumero));$i++){
								$registro.=" ";
							}
							$nombre=trim(str_replace(".","",str_replace("-","",$TS->fields['tra_nom'])));
							for($i=0;$i<(20-strlen($nombre));$i++){
								$registro.=" ";
							}
							$registro.=$nombre;
							$apellido=str_replace(".","",$TS->fields['tra_ape']);
							for($i=0;$i<(20-strlen($apellido));$i++){
								$registro.=" ";
							}
							$registro.=$apellido;
							$fecha=str_replace(".","",muestrafecha($TS->fields['tra_fec_ing']));
							for($i=0;$i<(25-strlen($fecha));$i++){
								$registro.=" ";
							}
							$registro.=$fecha;
							$departamento=str_replace(".","",$TS->fields['dep_nom']);
							for($i=0;$i<(85-strlen($departamento));$i++){
								$registro.=" ";
							}
							$registro.=$departamento;
							if($TS->fields['tra_tipo']==1){
								$funcion=str_replace(".","",$TS->fields['fun_nom']);
								for($i=0;$i<(90-strlen($funcion));$i++){
									$registro.=" ";
								}
								$registro.=$funcion;
							}
							else{
								$cargo=str_replace(".","",$TS->fields['car_nom']);
								for($i=0;$i<(90-strlen($cargo));$i++){
									$registro.=" ";
								}
								$registro.=$cargo;
							}
							$cuenta=str_replace(".","",$TS->fields['tra_num_cta']);
							for($i=0;$i<(15-strlen($cuenta));$i++){
								$registro.=" ";
							}
							$registro.=$cuenta;
							$sueldo=str_replace(".","",$TS->fields['tra_sueldo']);
							for($i=0;$i<(15-strlen($sueldo));$i++){
								$registro.=" ";
							}
							$registro.=$sueldo;
							fputs($fp,$registro); 
							fwrite($fp,"\r\n"); 
							$TS->movenext();
						} 
						fclose($fp);
						echo true;
						break; 
					}
					case "HISFON.txt":{
						$q = "SELECT cont_nom,nom_fec_ini,nom_fec_fin FROM rrhh.historial_nom WHERE int_cod= $JsonRec->Nomina";
						//die($q);
						$rN = $conn->Execute($q);
						$q = "SELECT conc_nom FROM rrhh.concepto WHERE int_cod= $JsonRec->Concepto";
						//die($q);
						$rC = $conn->Execute($q);
						$fp = fopen("HISFON.txt","w");
						$q = "SELECT DISTINCT A.tra_cod, A.tra_nom, B.tra_nac, B.tra_ced, B.tra_sex, B.tra_fec_nac, B.tra_sueldo  FROM rrhh.hist_nom_tra_conc AS A INNER JOIN rrhh.trabajador AS B ON A.tra_cod=B.int_cod WHERE A.hnom_cod= $JsonRec->Nomina AND B.tra_vac=0 ORDER BY A.tra_nom";
						//die($q);
						$rT = $conn->Execute($q);
						$TotalNomina=0;
						$TotalNominaAporte=0;
						$TotalNominaJubilacion=0;
						$Contador=0;
						while(!$rT->EOF){
						$q = "SELECT conc_val AS valor, conc_aporte AS aporte FROM rrhh.hist_nom_tra_conc WHERE hnom_cod= $JsonRec->Nomina AND conc_val<>0 AND tra_cod=".$rT->fields['tra_cod']." AND conc_cod= $JsonRec->Concepto ORDER BY conc_tipo, conc_cod";
						$rC = $conn->Execute($q);
						//die($q);
						$TotalTrabajador=0;
						$TotalTrabajadorAporte=0;
						$TotalTrabajadorJubilacion=0;
							while(!$rC->EOF) {
								if($JsonRec->Concepto == 34){
									$Cedula=trim(str_replace(".","",$rT->fields['tra_ced']));
									$registro=$Cedula;
									for($i=0;$i<(15-strlen($Cedula));$i++){
										$registro.=" ";
									}
									$Emision=trim(str_replace("/","",date('d/m/Y')));
									$registro.=$Emision;
									for($i=0;$i<(35-strlen($Emision));$i++){
										$registro.=" ";
									}

									$Trabajador=trim(str_replace(".","",$rT->fields['tra_nom']));
									$registro.=$Trabajador;
									for($i=0;$i<(35-strlen($Trabajador));$i++){
										$registro.=" ";
									}
									
									$Codigo=trim(8062001);
									for($i=0;$i<(25-strlen($Codigo));$i++){
										$registro.=" ";
									}
									$registro.=$Codigo;
									$Quincena=muestrafloat(trim($rT->fields['tra_sueldo']/2));
									for($i=0;$i<(25-strlen($Quincena));$i++){
										$registro.=" ";
									}
									$registro.=$Quincena;	
								}
							    else{						
									$Trabajador=trim(str_replace(".","",$rT->fields['tra_nom']));
									$registro=$Trabajador;
									for($i=0;$i<(35-strlen($Trabajador));$i++){
										$registro.=" ";
									}
									$nacionalidad=trim(str_replace(".","",$rT->fields['tra_nac']==0 ? 'V' : 'E'));
									for($i=0;$i<(12-strlen($nacionalidad));$i++){
										$registro.=" ";
									}
									$registro.=$nacionalidad;
									$Cedula=str_replace(".","",$rT->fields['tra_ced']);
									for($i=0;$i<(15-strlen($Cedula));$i++){
										$registro.=" ";
									}
									$registro.=$Cedula;
									$sexo=str_replace(".","",$rT->fields['tra_sex']==0 ? 'M':'F');
									for($i=0;$i<(15-strlen($sexo));$i++){
										$registro.=" ";
									}
									$registro.=$sexo;
									$fecha=str_replace("-","",$rT->fields['tra_fec_nac']);
									for($i=0;$i<(15-strlen($fecha));$i++){
										$registro.=" ";
									}
									$registro.=$fecha;
								}
								$valor=str_replace(".","",muestrafloat($rC->fields['valor']));
								for($i=0;$i<(25-strlen($valor));$i++){
									$registro.=" ";
								}
								$registro.=$valor;
								$aporte=str_replace(".","",muestrafloat($rC->fields['aporte']));
								for($i=0;$i<(25-strlen($valor));$i++){
									$registro.=" ";
								}
								$registro.=$aporte;
								$total=str_replace(".","",muestrafloat($rC->fields['valor']+$rC->fields['aporte']));
								for($i=0;$i<(25-strlen($total));$i++){
									$registro.=" ";
								}
								$registro.=$total;
								fputs($fp,$registro); 
								fwrite($fp,"\r\n");
								$TotalTrabajador+=$rC->fields['valor'];
								$TotalTrabajadorAporte+=$rC->fields['aporte'];
								$TotalTrabajadorJubilacion+=$rC->fields['valor'] + $rC->fields['aporte'];
								$rC->movenext();
								$Contador++;
							}
						$TotalNomina+=$TotalTrabajador;
						$TotalNominaAporte+=$TotalTrabajadorAporte;
						$TotalNominaJubilacion+=$TotalTrabajadorJubilacion;
						$rT->movenext();
						}
						fwrite($fp,"\r\n");
						$registro = "";
						for($i=0;$i<(102);$i++){
							$registro.=" ";
						} 
						$Nomina=str_replace(".","",muestrafloat($TotalNomina));
						for($i=0;$i<(25-strlen($Nomina));$i++){
							$registro.=" ";
						}
						$registro.=$Nomina;
						$NominaAporte=str_replace(".","",muestrafloat($TotalNominaAporte));
						for($i=0;$i<(25-strlen($NominaAporte));$i++){
							$registro.=" ";
						}
						$registro.=$NominaAporte;
						$NominaJubilacion=str_replace(".","",muestrafloat($TotalNominaJubilacion));
						for($i=0;$i<(26-strlen($NominaJubilacion));$i++){
							$registro.=" ";
						}
						$registro.=$NominaJubilacion;
						fputs($fp,$registro); 
						fwrite($fp,"\r\n");
						fclose($fp);
						echo true;
						break; 
					}					
					case 'TRABIER.txt':{
						$q = "SELECT T.tra_tipo from rrhh.trabajador  AS T  INNER JOIN  rrhh.cont_tra  AS C ON (T.int_cod= C.tra_cod)  where C.cont_cod = $JsonRec->Contrato ";
						//die($q);
						$Trabajador = $conn->Execute($q);
						if($Trabajador->fields['tra_tipo']==0){
						$q = "SELECT B.tra_nac ,B.tra_ced, B.tra_nom, B.tra_ape, B.tra_fec_nac, B.tra_sex, B.tra_fec_ing, B.tra_fec_egr,
						B.tra_num_cta, B.tra_tip_pag, E.dep_nom, D.car_nom, A.conc_desc, A.conc_val
						from rrhh.trabajador as B
						INNER JOIN rrhh.cargo AS D ON (D.int_cod=B.car_cod)
						INNER JOIN rrhh.departamento AS E ON (E.int_cod=B.dep_cod)
						INNER JOIN rrhh.nom_tra_conc AS A ON (A.tra_cod =B.tra_cod)
						INNER JOIN rrhh.nomina AS C ON (A.nom_cod=C.int_cod)
						WHERE C.cont_cod = $JsonRec->Contrato  AND A.conc_cod = $JsonRec->Concepto AND (B.tra_estatus = 0 
						OR B.tra_estatus = 3) AND B.tra_ced != 'null'";}
						else{
						$q = "SELECT B.tra_nac ,B.tra_ced, B.tra_nom, B.tra_ape, B.tra_fec_nac, B.tra_sex, B.tra_fec_ing, B.tra_fec_egr,
						B.tra_num_cta, B.tra_tip_pag, E.dep_nom, D.fun_nom, A.conc_desc, A.conc_val
						from rrhh.trabajador as B
						INNER JOIN rrhh.funciones AS D ON (D.int_cod=B.fun_cod)
						INNER JOIN rrhh.departamento AS E ON (E.int_cod=B.dep_cod)
						INNER JOIN rrhh.nom_tra_conc AS A ON (A.tra_cod =B.tra_cod)
						INNER JOIN rrhh.nomina AS C ON (A.nom_cod=C.int_cod)
						WHERE C.cont_cod = $JsonRec->Contrato  AND A.conc_cod = $JsonRec->Concepto AND (B.tra_estatus = 0 
						OR B.tra_estatus = 3) AND B.tra_ced != 'null'";
						}
						//die($q);
						$nA = $conn->Execute($q);
						$fp = fopen("TRABIER.txt","w");
						while(!$nA->EOF){
							$CedulaLetra= $nA->fields['tra_nac']==0 ? 'V' : 'E';
							$CedulaNumero=str_replace(".","",$nA->fields['tra_ced']);
							$registro=$CedulaLetra.$CedulaNumero;
							for($i=0;$i<(10-strlen($CedulaNumero));$i++){
								$registro.=" ";
							}
							$TrabajadorNombre=str_replace(".","",$nA->fields['tra_nom']);
							$registro.=$TrabajadorNombre;
							for($i=0;$i<(30-strlen($TrabajadorNombre));$i++){
								$registro.=" ";
							}
							$TrabajadorApellido=str_replace(".","",$nA->fields['tra_ape']);
							$registro.=$TrabajadorApellido;
							for($i=0;$i<(30-strlen($TrabajadorApellido));$i++){
								$registro.=" ";
							}
							$TrabajadorNacimiento=muestrafecha($nA->fields['tra_fec_nac']);
							$registro.=$TrabajadorNacimiento;
							for($i=0;$i<(15-strlen($TrabajadorNacimiento));$i++){
								$registro.=" ";
							}
							$TrabajadorSexo=$nA->fields['tra_sex']==0 ? 'M' : 'F';
							$registro.=$TrabajadorSexo;
							for($i=0;$i<(10-strlen($TrabajadorSexo));$i++){
								$registro.=" ";
							}
							$TrabajadorIngreso=muestrafecha($nA->fields['tra_fec_ing']);
							$registro.=$TrabajadorIngreso;
							for($i=0;$i<(15-strlen($TrabajadorIngreso));$i++){
								$registro.=" ";
							}
							$TrabajadorEgreso=muestrafecha($nA->fields['tra_fec_egr']);
							$registro.=$TrabajadorEgreso;
							for($i=0;$i<(15-strlen($TrabajadorEgreso));$i++){
								$registro.=" ";
							}
							$TrabajadorNumCta=str_replace(".","",$nA->fields['tra_num_cta']);
							$registro.=$TrabajadorNumCta;
							for($i=0;$i<(15-strlen($TrabajadorNumCta));$i++){
								$registro.=" ";
							}
							$TrabajadorTipPag=$nA->fields['tra_tip_pag']==0 ? 'Efectivo' : $nA->fields['tra_tip_pag']==1 ? 'Cheque' : 'Deposito';
							$registro.=$TrabajadorTipPag;
							for($i=0;$i<(10-strlen($TrabajadorTipPag));$i++){
								$registro.=" ";
							}
							$TrabajadorDepartamento=str_replace(".","",$nA->fields['dep_nom']);
							$registro.=$TrabajadorDepartamento;
							for($i=0;$i<(100-strlen($TrabajadorDepartamento));$i++){
								$registro.=" ";
							}
							$TrabajadorCargo= $Trabajador->fields['tra_tipo'] == 0 ? str_replace(".","",$nA->fields['car_nom']) : str_replace(".","",$nA->fields['fun_nom']);
							$registro.=$TrabajadorCargo;
							for($i=0;$i<(100-strlen($TrabajadorCargo));$i++){
								$registro.=" ";
							}
							$TrabajadorConcepto=str_replace(".","",$nA->fields['conc_desc']);
							$registro.=$TrabajadorConcepto;
							for($i=0;$i<(40-strlen($TrabajadorConcepto));$i++){
								$registro.=" ";
							}
							$TrabajadorConceptoValor=muestrafloat($nA->fields['conc_val']);
							$registro.=$TrabajadorConceptoValor;
							fputs($fp,$registro); 
							fwrite($fp,"\r\n");
							$nA->movenext();
							}
						fclose($fp);
						echo true;
						break;
					}
					case 'FIDEICOMISO.txt':{
						$q = "SELECT A.tra_nac, replace(A.tra_ced, '.','')::numeric(10,0) as tra_ced ,B.conc_val::numeric(20,2) FROM rrhh.trabajador as A  
						INNER JOIN rrhh.acum_tra_conc AS B ON A.int_cod=B.tra_cod 
						INNER JOIN rrhh.acumulado AS C ON B.acum_cod=C.int_cod 
						WHERE A.tra_vac = 0 AND C.int_cod = $JsonRec->Acumulado AND B.conc_cod = $JsonRec->ConceptoA   
						ORDER BY tra_ced";
						//die($q);
						$FideAcumulado = $conn->Execute($q);
						$fp = fopen("FIDEICOMISO.txt","w");
						while(!$FideAcumulado->EOF){
							$constante = "00004671";
							$registro = $constante;
							$CedulaLetra= $FideAcumulado->fields['tra_nac']==0 ? 'V' : 'E';
							$registro.= $CedulaLetra;
							$CedulaNumero= $FideAcumulado->fields['tra_ced'];
							for($i=0;$i<(9-strlen($CedulaNumero));$i++){
								$registro.="0";
							}
							$registro.= $CedulaNumero;
							$constante = "000APO002AO";
							$registro.= $constante; 
							$MontoFide=str_replace(".","",$FideAcumulado->fields['conc_val']);
							for($i=0;$i<(18-strlen($MontoFide));$i++){
								$registro.="0";
							}
							$registro.=$MontoFide;
							for($i=0;$i<(77);$i++){
								$registro.="0";
							}
							fputs($fp,$registro); 
							fwrite($fp,"\r\n");
							$FideAcumulado->movenext();
						}
						fclose($fp);
						echo true;
						break;
					}
					case 'NOMINA.xlsx':{
						error_reporting(E_ALL);
						
						//set_include_path(get_include_path() . PATH_SEPARATOR . $appRoot .'/lib/');	
						
						set_include_path($appRoot .'/lib/ExcelClasses/');

						/** PHPExcel */
						require_once 'PHPExcel.php';
						include 'PHPExcel/IOFactory.php';
						
						// Create new PHPExcel object
						$objPHPExcel = new PHPExcel();

						// Set properties
						$objPHPExcel->getProperties()->setCreator("Creatividad Technologica");
						$objPHPExcel->getProperties()->setLastModifiedBy("Creatividad Technologica");
						$objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
						$objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
						$objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");
						$objPHPExcel->getProperties()->setKeywords("office 2007 openxml php");
						$objPHPExcel->getProperties()->setCategory("Test result file");
						
						$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
						$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
						$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
						$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(50);

						// Add some data
						$objPHPExcel->setActiveSheetIndex(0);
						$objPHPExcel->getActiveSheet()->setCellValue('A1', 'mon_pag');
						$objPHPExcel->getActiveSheet()->getStyle('B1')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
						$objPHPExcel->getActiveSheet()->setCellValue('B1', 'nro_cta');
						$objPHPExcel->getActiveSheet()->setCellValue('C1', 'cedula');
						$objPHPExcel->getActiveSheet()->setCellValue('D1', 'nombre');						
						
						$q = "SELECT A.cont_nom, A.nom_fec_ini, A.nom_fec_fin FROM rrhh.historial_nom AS A  WHERE A.int_cod=$JsonRec->Nomina ";
						$Nomina = $conn->Execute($q);
						
						$Contrato = substr($Nomina->fields['cont_nom'], 0,3);
						$FechaIni = $Nomina->fields['nom_fec_ini'];
						$FechaFin = $Nomina->fields['nom_fec_fin'];
						
						$q = "SELECT sum(CASE WHEN (A.conc_tipo=1) THEN (A.conc_val::numeric(20,2)*-1) ELSE (A.conc_val::numeric(20,2)) END) AS valor, B.tra_nac, replace(B.tra_ced, '.','')::numeric(10,0) as tra_ced , B.tra_nom, B.tra_ape, B.tra_num_cta FROM rrhh.hist_nom_tra_conc AS A INNER JOIN rrhh.trabajador AS B ON A.tra_cod=B.int_cod  WHERE A.hnom_cod=$JsonRec->Nomina AND B.tra_tip_pag=2 GROUP BY A.tra_cod,B.tra_ced, B.tra_nac, B.tra_num_cta,B.tra_nom, B.tra_ape ORDER BY tra_ced";
						$r = $conn->Execute($q);
						
						$i = 2;
						while(!$r->EOF){
							$objPHPExcel->getActiveSheet()->getStyle('B'. $i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
							$objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $r->fields['valor']);
							$objPHPExcel->getActiveSheet()->getCell('B' . $i)->setValueExplicit( $r->fields['tra_num_cta'], PHPExcel_Cell_DataType::TYPE_STRING);							
							$CedulaLetra = $r->fields['tra_nac']==0 ? 'V' : 'E';
							$Cedula = $CedulaLetra.$r->fields['tra_ced'];
							$objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $Cedula);
							$objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $r->fields['tra_ape'].', '.$r->fields['tra_nom']);
							$i++;
							$r->movenext();
						}

						// Rename sheet
						$objPHPExcel->getActiveSheet()->setTitle("Nom->".$Contrato."->".$FechaIni."_".$FechaFin);

						// Set active sheet index to the first sheet, so Excel opens this as the first sheet
						$objPHPExcel->setActiveSheetIndex(0);
								
						// Save Excel 2007 file
						$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
						$objWriter->save('NOMINA.xlsx');

						echo true;
						break;
					}
				}
				break;
			}
			case 8:	{
				switch($JsonRec->Accion){
					case 0:{
						$q = "UPDATE rrhh.trabajador SET tra_sueldo=$JsonRec->Sueldo WHERE car_cod=$JsonRec->Cargo";
						//die($q);
						$r = $conn->Execute($q);
						echo true;
						break;
					}
					case 1:{
						$q = "UPDATE rrhh.trabajador SET tra_sueldo=$JsonRec->Hp WHERE fun_cod=$JsonRec->Funcion";
						//die($q);
						$r = $conn->Execute($q);
						echo true;
						break;
					}					
				}
				break;
			}
			case 9:	{
				switch($JsonRec->Accion){
					case 0:{
						$q = "SELECT car_sueldo FROM rrhh.cargo WHERE int_cod=$JsonRec->Cargo";
						$r = $conn->Execute($q);
						echo empty($r->fields['car_sueldo']) ? '0,00' : muestrafloat($r->fields['car_sueldo']);
						break;
						}
					case 1:{
						$q = "SELECT fun_hp FROM rrhh.funciones WHERE int_cod=$JsonRec->Cargo";
						$r = $conn->Execute($q);
						echo empty($r->fields['fun_hp']) ? '0,00' : muestrafloat($r->fields['fun_hp']);
						break;
						}
				}
				break;
			}
			case 10:	{ //FERIADOS
				switch($JsonRec->Accion){
					case 0:{
						$q="SELECT * FROM rrhh.feriados WHERE fecha='$JsonRec->Fecha'";
						$r= $conn->Execute($q);
						if($r->EOF){
							$q = "INSERT INTO rrhh.feriados (fecha) VALUES ('$JsonRec->Fecha')";
							$RAux = $conn->Execute($q);
							$Estatus=1;
						}else{
							$q = "DELETE FROM rrhh.feriados WHERE fecha='$JsonRec->Fecha'";
							$RAux = $conn->Execute($q);
							$Estatus=2;
						}
						echo $Estatus;
						break;
					}
					case 1:{
						$q = "DELETE FROM rrhh.feriados WHERE to_char(fecha,'YYYY')='$JsonRec->Ano'";
						$RAux = $conn->Execute($q);
						echo true;
						break;
					}
					case 2:{
						for($Mes=1;$Mes<=12;$Mes++){
							for($Dia=1;$Dia<=DiaFin($Mes);$Dia++){
								if(date("l",mktime(0,0,0,$Mes,$Dia,$JsonRec->Ano))=="Saturday" || date("l",mktime(0,0,0,$Mes,$Dia,$JsonRec->Ano))=="Sunday"){
									$Fecha=$JsonRec->Ano."-".$Mes."-".$Dia;
									$q="SELECT * FROM rrhh.feriados WHERE fecha='$Fecha'";
									$r= $conn->Execute($q);
									if($r->EOF){
										$q = "INSERT INTO rrhh.feriados (fecha) VALUES ('$Fecha')";
										$RAux = $conn->Execute($q);
									}
								}
							}
						}
						echo true;
						break;
					}
				}
				break;
			}
			case 11:	{ //PAGAR VACACIONES
				switch($JsonRec->Accion){
					case 0:{
						echo FechaFinVacaciones($conn,$JsonRec->FechaIni,$JsonRec->Dias);
						break;
					}
					case 1:{
						$Dias= intval((strtotime(guardafecha($JsonRec->FechaFin))-strtotime(guardafecha($JsonRec->FechaIni)))/86400);
						$DiasTotal=$Dias;
						$Fecha = $JsonRec->FechaIni;
						for($i=0;$i<$Dias;$i++){
							$q = "SELECT * FROM rrhh.feriados WHERE fecha='$Fecha'";
							$rF = $conn->Execute($q);
							if(!$rF->EOF){
								$DiasTotal--;
							}
//							echo "Fecha: ".$Fecha." Dias: ".$DiasTotal."Feriado=".$rF->EOF."\n";
							$Fecha=FechaIni('0','/',$Fecha,1,0); 
							
						} 
						echo  $DiasTotal;
						break;
					}
					case 2:{
						$q = "SELECT * FROM rrhh.mod_conc WHERE modulo=1";
						$rC = $conn->Execute($q);
						if(!$rC->EOF){
							$Contrato= $rC->fields['conc_cod'];
							$q = "DELETE FROM rrhh.cont_tra WHERE cont_cod=$Contrato";
							$rExec = $conn->Execute($q);
							foreach($JsonRec->Trabajadores AS $Trabajador){
								if($Trabajador[5]){
									$q = "INSERT INTO rrhh.cont_tra (cont_cod,tra_cod) VALUES ($Contrato,$Trabajador[0])";
									$rExec = $conn->Execute($q);
									$FechaIni=guardafecha($Trabajador[1]);
									$FechaFin=guardafecha($Trabajador[2]);
									$q = "DELETE FROM rrhh.preparar_vac WHERE tra_cod=$Trabajador[0]";
									$rExec = $conn->Execute($q);
									$q = "INSERT INTO rrhh.preparar_vac (tra_cod,fecha_ini,fecha_fin,dias,dias_pendientes) VALUES ($Trabajador[0],'$FechaIni','$FechaFin',$Trabajador[3],$Trabajador[4])";
									$rExec = $conn->Execute($q);
								}else{
									$q = "SELECT * FROM rrhh.vacaciones WHERE tra_cod=$Trabajador[0]";
									$rV = $conn->Execute($q);
									if($rV->EOF){
										$q = "INSERT INTO rrhh.vacaciones(tra_cod,vac_dias_pendientes) VALUES ($Trabajador[0],$Trabajador[4])";
										$rExec = $conn->Execute($q);
									}else{
										$q = "UPDATE rrhh.vacaciones SET vac_dias_pendientes=$Trabajador[4] WHERE tra_cod=$Trabajador[0]";
										$rExec = $conn->Execute($q);
									}
								}
							}
							echo true;
						}else{
							echo false;
						}
						break;
					}
					case 3:{
						$q="SELECT * FROM rrhh.mod_conc WHERE modulo=1";
						$r= $conn->Execute($q);
						if($r->EOF){
							$q = "INSERT INTO rrhh.mod_conc (modulo,conc_cod) VALUES (1,$JsonRec->Contrato)";
							$RAux = $conn->Execute($q);
						}else{
							$q = "UPDATE rrhh.mod_conc SET conc_cod=$JsonRec->Contrato WHERE modulo=1";
							$RAux = $conn->Execute($q);
						}
						$q="SELECT * FROM rrhh.mod_conc WHERE modulo=2";
						$r= $conn->Execute($q);
						if($r->EOF){
							$q = "INSERT INTO rrhh.mod_conc (modulo,conc_cod) VALUES (2,$JsonRec->Constante1)";
							$RAux = $conn->Execute($q);
						}else{
							$q = "UPDATE rrhh.mod_conc SET conc_cod=$JsonRec->Constante1 WHERE modulo=2";
							$RAux = $conn->Execute($q);
						}
						$q="SELECT * FROM rrhh.mod_conc WHERE modulo=3";
						$r= $conn->Execute($q);
						if($r->EOF){
							$q = "INSERT INTO rrhh.mod_conc (modulo,conc_cod) VALUES (3,$JsonRec->Constante2)";
							$RAux = $conn->Execute($q);
						}else{
							$q = "UPDATE rrhh.mod_conc SET conc_cod=$JsonRec->Constante2 WHERE modulo=3";
							$RAux = $conn->Execute($q);
						}
						echo true;
						break;
					}
				}
				break;
			}
			case 12:	{
				switch($JsonRec->Accion){
					case 0:{
						$objeto = $JsonRec->tabla;
						$oObjeto = new $objeto();
						$oObjeto = $oObjeto->getListadoKeys();
						echo $JsonEnv->encode($oObjeto);
						break;
					}
					case 1:{
						$q = "SELECT * FROM rrhh.listado WHERE int_cod=$JsonRec->reporte";
						$rListado= $conn->Execute($q);
						$objeto = $rListado->fields['lis_tabla']=='Rac' ? 'trabajador' : $rListado->fields['lis_tabla'] ;
						$oObjeto = new $objeto();
						$oObjeto = $oObjeto->getListadoKeys();
						for($i=0;$i<count($oObjeto);$i++){

							$q = "SELECT * FROM rrhh.lis_campos WHERE lis_cod=$JsonRec->reporte AND campo='".$oObjeto[$i]['C']."'";
							$r= $conn->Execute($q);
							$oObjeto[$i]['Com1'] = $r->EOF ? '0' : '1'; 
							$oObjeto[$i]['Com2'] = $r->EOF ? $oObjeto[$i]['D'] : $r->fields['cam_nom']; 
							$oObjeto[$i]['Com3'] = $r->EOF ? '40' : $r->fields['cam_lon']; 
							$oObjeto[$i]['Com4'] = $r->EOF ? 'C' : $r->fields['cam_ali']; 

							$q = "SELECT * FROM rrhh.lis_condiciones WHERE lis_cod=$JsonRec->reporte AND campo='".$oObjeto[$i]['C2']."'";
							$r= $conn->Execute($q);
							$oObjeto[$i]['Con1'] = $r->EOF ? '0' : '1'; 
							$oObjeto[$i]['Con2'] = $r->EOF ? '=' : $r->fields['cam_ope']; 
							$oObjeto[$i]['Con3'] = $r->EOF ? '' : $r->fields['cam_valor']; 

							$q = "SELECT * FROM rrhh.lis_orden WHERE lis_cod=$JsonRec->reporte AND campo='".$oObjeto[$i]['C2']."'";
							$r= $conn->Execute($q);
							$oObjeto[$i]['O'] = $r->EOF ? '0' : '1'; 

						}
						$resul[0]=$rListado->fields['lis_tabla'];
						$resul[1]=$rListado->fields['lis_titulo'];
						$resul[2]=$oObjeto;
						$resul[3]=$rListado->fields['lis_logo'];
						$resul[4]=$rListado->fields['lis_nro_pag'];
						$resul[5]=$rListado->fields['lis_fecha'];
						$resul[6]=$rListado->fields['lis_ori'];
						$resul[7]=$rListado->fields['lis_tipo_hoja'];
						$resul[8]=$rListado->fields['lis_tam_let'];
						echo $JsonEnv->encode($resul);
						break; 
					}
					case 2:{
						$JsonRec->logo = $JsonRec->logo ?  1 : 0;
						$JsonRec->nroP = $JsonRec->nroP ?  1 : 0;
						$JsonRec->fecha = $JsonRec->fecha ?  1 : 0;
						$q = "INSERT INTO rrhh.listado (lis_titulo,lis_logo,lis_nro_pag,lis_fec,lis_tabla,lis_ori,lis_tipo_hoja,lis_tam_let) VALUES ('$JsonRec->titulo',$JsonRec->logo,$JsonRec->nroP,$JsonRec->fecha,'$JsonRec->tabla','$JsonRec->orientacion_hoja','$JsonRec->tipo_hoja',$JsonRec->tamano_letra)";
						$conn->Execute($q);
						$q = "SELECT MAX(int_cod) AS int_cod FROM rrhh.listado";
						$r= $conn->Execute($q);
						$lis_cod=$r->fields['int_cod'];
						for($i=0;$i<count($JsonRec->campos);$i++){
							$q = "INSERT INTO rrhh.lis_campos (lis_cod,campo,cam_nom,cam_lon,cam_ali) VALUES ($lis_cod,'".$JsonRec->campos[$i][0]."','".$JsonRec->campos[$i][1]."',".$JsonRec->campos[$i][2].",'".$JsonRec->campos[$i][3]."')";
							$conn->Execute($q);
						}
						for($i=0;$i<count($JsonRec->condiciones);$i++){
							$q = "INSERT INTO rrhh.lis_condiciones (lis_cod,campo,cam_ope,cam_valor) VALUES ($lis_cod,'".$JsonRec->condiciones[$i][0]."','".$JsonRec->condiciones[$i][1]."','".$JsonRec->condiciones[$i][2]."')";
							$conn->Execute($q);
						}
						for($i=0;$i<count($JsonRec->orden);$i++){
							$q = "INSERT INTO rrhh.lis_orden (lis_cod,campo) VALUES ($lis_cod,'".$JsonRec->orden[$i]."')";
							$conn->Execute($q);
						}
						echo true;
						break;
					}
					case 3:{
						$q = "DELETE FROM rrhh.listado WHERE int_cod=$JsonRec->reporte";
						$conn->Execute($q);
						echo true;
						break;
					}
				}
				break;
			}
			case 13:{//fecha de reverso de nomina
				$q = "SELECT * FROM rrhh.historial_nom WHERE cont_cod=$JsonRec->Contrato  ORDER BY int_cod DESC LIMIT 1";
				$r = $conn->Execute($q);
				if (!$r->EOF){
					$Fecha=split ("-" ,$r->fields['nom_fec_ini']);
					$FechaIni= $Fecha[2]."/".$Fecha[1]."/".$Fecha[0];
					$Fecha=split ("-" ,$r->fields['nom_fec_fin']);
					$FechaFin= $Fecha[2]."/".$Fecha[1]."/".$Fecha[0];
				}
				$Resultado[0]=$FechaIni;
				$Resultado[1]=$FechaFin;
				echo $JsonEnv->encode($Resultado);
				break;
			}
		}
	}catch( ADODB_Exception $e ){
//		echo ERROR_CATCH_GENERICO;
		echo false;
//		echo $e;
	}
} 
?>

