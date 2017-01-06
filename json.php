<?
include("comun/ini.php");
$op = $_GET['op'];
//die($op);
switch($op){
	case "movpre":
		$nrodoc = $_GET['nrodoc'];
		$momento = $_REQUEST['momento'];
		$oMovPre = new movimientos_presupuestarios;
		if($oMovPre->get($conn, $nrodoc, $momento)){
			$json=new Services_JSON();
			echo $json->encode($oMovPre);
		}
		break;
	case "parcat":
		$fila = $_GET['fila'];
		$escenario = !empty($_GET['escenario']) ? $_GET['escenario'] : $escEnEje;
		if($oParpre = relacion_pp_cp::get_all_by_cp_pp_esc($conn, $_GET['cp'], $_GET['pp'], $escenario)){
			$json=new Services_JSON();
			echo $json->encode($oParpre);
		}
		break;
	
	case "SP": #SE UTILIZA PARA TRAER LA SUMATORIA DE LA REENCIONES Y FACTURAS DE LA SOLICITUD PAGO#
		$nrodoc = $_GET['nrodoc'];
		$oSP = new solicitud_pago;
		if($x = $oSP->GetMontoRetFac($conn, $nrodoc)){
			$json=new Services_JSON();
			echo $json->encode($x);
		}
		break;
	
	case "productos": #SE UTILIZA PARA TRAER LOS DATOS DE LOS PRODUCTOS#
		$id = $_GET['id'];
		$oProducto = new productos;
		if ($oProducto->get($conn, $id)){
			
			$json=new Services_JSON();
			echo $json->encode($oProducto);
		}
		break;
	case "categorias_programaticas":
		$cp = new categorias_programaticas;
		$cCategorias = $cp->get_all_by_ue($conn, $_GET['ue'], $escEnEje);
		if ($cCategorias){
			$json=new Services_JSON();
			echo $json->encode($cCategorias);
		}
		break;
	
	case "partidas_presupuestarias":
	$cPartidas = partidas_presupuestarias::get_all_by_cp($conn, $_GET['cp'], $escEnEje);
		if ($cPartidas){
			$json=new Services_JSON();
			echo $json->encode($cPartidas);
		}
	break;
	//ESTE CASO SE USA CUANDO SE VA A CREAR UNA SOLICITUD DE PAGO NUEVA//
	case "pp_solicitud":
		$nrodoc = $_REQUEST['id'];
		$status = $_REQUEST['status'];
		$cRelaciones = movimientos_presupuestarios::get_relaciones($conn, $nrodoc, $escEnEje, $status);
		if ($cRelaciones){
			$json=new Services_JSON();
			echo $json->encode($cRelaciones);
		}
	break;
	
	#ESTE CASO ES CUANDO SE BUSCA UNA SOLICITUD YA REGISTRADA#
	case "pp_solicitud2":
		$nrodoc = $_REQUEST['id'];
		$cRelaciones = solicitud_pago::getRelacionPartidas($conn, $nrodoc, $escEnEje);
		if ($cRelaciones){
			$json=new Services_JSON();
			echo $json->encode($cRelaciones);
		}
	break;
	
	case "ultimo_cheque":
		$id_cuenta = $_REQUEST['id_cuenta'];
		$cc = new control_chequera;
		$cUltimoCheque = $cc->ChequeraxCuenta($conn, $id_cuenta);
		if ($cUltimoCheque){
			$json=new Services_JSON();
			echo $json->encode($cUltimoCheque);
		}
	break;
	case "activar_chequera":
		$id_chequera = $_REQUEST['id_chequera'];
		$id_cuenta = $_REQUEST['id_cuenta'];
		echo control_chequera::activarChequera($conn,$id_chequera,$id_cuenta);
	break;
	
	
	#ESTE CASO SE USA CUANDO TRAE LAS SOLICITUDES CUANDO SE SELECCIONA UNA ORDEN DE PAGO EN CHEQUE#
	case "Solicitudes_cheque":
		$nrodoc = $_REQUEST['nrodoc'];
		$op = new orden_pago;
		$op->getRelacionOrdenPago($conn, $nrodoc);	
		echo $op->solicitudes;
	break;
	
	//ESTE CASO SE USA CUANDO SE VA A AÑADIR UNA SANCION A UN CONTRIBUYENTE//
	case "san_agregar":
		$id_san = $_REQUEST['id'];
		$vs = new veh_sanciones;
		$cSancion = $vs->get($conn, $id_san);
		if ($cSancion){
			$json=new Services_JSON();
			echo $json->encode($vs);
		}
	break;
	
	case "relacion_ordcompra":
		$oc = new ordcompra;
		$relacion = $oc->getRelacionPartidas($conn, $_REQUEST['id'], $escEnEje);
		if ($relacion){
			$json=new Services_JSON();
			echo $json->encode($relacion);
		}

	break;
	
	case "relacion_servicio_trabajo":
		$oc = new orden_servicio_trabajo;
		$relacion = $oc->getRelacionPartidas($conn, $_REQUEST['id'], $escEnEje);
		if ($relacion){
			$json=new Services_JSON();
			echo $json->encode($relacion);
		}

	break;
	
	//ESTE CASO SE USA PARA BUSCAR LOS REQUISITOS DE UN PROVEEDOR//
	case "req_prov_busca":
		$id_prov = $_REQUEST['id'];
		$vs = new proveedores;
		$cRequisito = $vs->busca_req($conn, $id_prov);
		if ($cRequisito){
			$json=new Services_JSON();
			echo $json->encode($cRequisito);
		}
	break;
	
	//ESTE CASO SE USA PARA BUSCAR LAS PARTIDAS PRESUPUESTARIAS SEGUN LA CATEGORIA PROGRAMATICA Y EL PRODUCTO SELECCIONADO
	case "prodpar":
		$id_cp = $_REQUEST['cp'];
		$id_prod = $_REQUEST['prod'];
		$cant = $_REQUEST['cant'];
		//die("aqui ".$id_prod);
		$pc = new requisiciones;
		$cRequisiciones = $pc->get_prod_pp_cp($conn,$id_cp,$id_prod,$escEnEje,$cant);
		if ($cRequisiciones){
			$json=new Services_JSON();
			echo $json->encode($cRequisiciones);
		}
	break;
	
	//ESTE CASO SE USA PARA ACTUALIZAR LOS MONTOS DESPACHADOS EN LA TABLA DE RELACION DE REQUISICIONES
	case "setdespacho":
		$id= $_REQUEST['id'];
		$cantd = $_REQUEST['cd'];
		$id_produ = $_REQUEST['id_prod'];
		$rr = new revision_requisicion;
		if(!$cRequisiciones = $rr->set_despacho($conn, $id, $cantd, $id_produ)){
			echo "No se actualizo el registro";
		}
	break;
	
	//ESTE CASO SE USA PARA CARGAR LOS RODUCTOS QUE CADA PROVEEDOR ESTA COTIZANDO PARA UNA REQUISICION
	case "prodcotizacion":
		$idp= $_REQUEST['idp'];
		$idr = $_REQUEST['idr'];
		$ac = new actualiza_cotizacion;
		$oCotizacion = $ac->getArticulosCotizacion($conn, $idr, $idp);
		if($oCotizacion){
			$json= new Services_JSON();
			echo $json->encode($oCotizacion);
		}
	break;
	
	
	case "relacion_cheque":
		$nrodoc = $_REQUEST['nrodoc'];
		$oCheque = cheque::getRelaciones($conn, $nrodoc);
		if ($oCheque){
			$json=new Services_JSON();
			echo $json->encode($oCheque);
		}
	break;
	

	case "detalle_comp":
		$id = $_REQUEST['id'];
		$com = new comprobante($conn);
		$oComprobante = $com->get_det($id);
		if ($oComprobante){
			$json=new Services_JSON();
			echo $json->encode($oComprobante);
		}
	break;
	
	case "busca_vencido":
		$idr = $_REQUEST['id'];
		$req = new requisitos;
		$req->get($conn, $idr);
		//die(print_r($req));
		if ($req){
			$json=new Services_JSON();
			echo $json->encode($req);
		}
	break;
	
	case "busca_nrodoc":
		$tabla = $_REQUEST['tabla'];
		$id_ue = $_REQUEST['id_ue'];
		$tipdoc = $_REQUEST['tipdoc'];
		$nrodoc = $_REQUEST['nrodoc'];
		//die($nrodoc);
		$ord = new ordcompra;
		echo $ord->buscaNroDoc($conn,$nrodoc,$tipdoc,$id_ue,$tabla);
	break;

	case "busca_causado":
		$id = $_REQUEST['id'];
		$mp = new movimientos_presupuestarios;
		$cau = $mp->get_causado($conn, $id);
		if($cau){
			$json=new Services_JSON();
			echo $json->encode($cau);
		}
	break;
	case "busca_porc":
		$id = $_GET['id'];
		$oRet = new retenciones_adiciones;
		if($oRet->get($conn, $id)){
			$json=new Services_JSON();
			echo $json->encode($oRet);
		}
		break;	
		
	case "buscar_plan_cta":
		$codcta = $_GET['codcta'];
		$oPlanCta = new plan_cuenta;
		$oPlanCta->get($conn, $codcta);
		$json=new Services_JSON();
		echo $json->encode($oPlanCta);
		break;

	case "actualizar":	//Realiza la actualizacion
		$anio = $_GET['anio'];
		$mes = $_GET['mes'];
		$fecha = date('Y-m-d', strtotime($_GET['fecha']));
		
		$sql = "SELECT contabilidad.actualizar($anio::int2, $mes::int2, $escEnEje::int8)";
		$rs = $conn->Execute($sql);

//		var_dump($rs);		
//		return;
		if ($rs === false)
			echo $conn->ErrorMsg();
		else
			echo "Se ha Actualizado con &eacute;xito";
		break;
		
	case "cerrar":
		$anio = $_GET['anio'];
		$mes = $_GET['mes'];
		$fecha = date('Y-m-d', strtotime($_GET['fecha']));
		
		$sql = "SELECT contabilidad.cerrar($anio::int2, $mes::int2, $escEnEje::int8, $usuario->id::int8)";
		$rs = $conn->Execute($sql);
		
		if ($rs === false)
			echo $conn->ErrorMsg();//ERROR;
		else
			echo "Se ha Cerrado el mes con &eacute;xito";
		break;

	case "reversar":
		$anio = $_GET['anio'];
		$mes = $_GET['mes'];
		$fecha = date('Y-m-d', strtotime($_GET['fecha']));
		
		$sql = "SELECT contabilidad.reversar($anio::int2, $mes::int2, $escEnEje::int8, $usuario->id::int8)";
		$rs = $conn->Execute($sql);
		
		if ($rs === false)
			echo $conn->ErrorMsg();//ERROR;
		else
			echo "Se ha Reversado el mes con &eacute;xito";
		break;
		
	//ESTE CASO SE USA CUANDO SE VA A CREAR UNA SOLICITUD DE PAGO NUEVA//
	case "pp_solicitud3":
		$nrodoc = $_REQUEST['id'];
		$status = $_REQUEST['status'];
		$cRelaciones = solicitud_pago::get_relaciones($conn, $nrodoc, $escEnEje, $status);
		if ($cRelaciones){
			$json=new Services_JSON();
			echo $json->encode($cRelaciones);
		}
	break;
	
	case "solpag":
		$nrodoc = $_GET['nrodoc'];
		$oSolPag = new solicitud_pago;
		if($oSolPag->get($conn, $nrodoc)){
			$json=new Services_JSON();
			echo $json->encode($oSolPag);
		}
		break;
		
	//ESTE CASO SE USA CUANDO SE VA A CREAR UNA ORDEN DE PAGO NUEVA//
	case "ordenpago":
		$nrodoc = $_REQUEST['id'];
		$cRelaciones = solicitud_pago::getRelacionPar_Sol($conn, $nrodoc, $escEnEje);
		if ($cRelaciones){
			$json=new Services_JSON();
			echo $json->encode($cRelaciones);
		}
	break;
	
	
	case "ordenpagototales":
		$nrodoc = $_REQUEST['id'];
		$cRelaciones = solicitud_pago::getCompromisosXDoc($conn, $nrodoc);
		if ($cRelaciones){
			$json=new Services_JSON();
			echo $json->encode($cRelaciones);
		}
	break;
	
	#ESTE CASO ES CUANDO SE BUSCA UNA ORDEN DE PAGO YA REGISTRADA#
	case "pp_orden":
		$nrodoc = $_REQUEST['id'];
		$cRelaciones = orden_pago::getRelacionPartidas($conn, $nrodoc, $escEnEje);
		if ($cRelaciones){
			$json=new Services_JSON();
			echo $json->encode($cRelaciones);
		}
	break;
	case "obra":
		$id = $_REQUEST['id'];
		$oObra = new obras();
		$oObra->get($conn, $id, $escEnEje);
		$json=new Services_JSON();
		echo $json->encode($oObra);
		break;
	case "tieneFianza":
		$id = $_REQUEST['id'];
		$oObra = new obras();
		$oObra->get($conn, $id, $escEnEje);
		$json=new Services_JSON();
		echo $json->encode($oObra);
		break;	
		
	case "saldoConc":
		$op = $_REQUEST['tipo'];
		$fecha = $_REQUEST['fecha'];
		$fecha = substr($fecha, 6, 4).'-'.substr($fecha, 3, 2).'-'.substr($fecha, 0, 2);
		
		$id_cta = $_REQUEST['id_cta_banc'];
		$obj = new conciliacionBancaria;
		$saldo = $obj->getSaldo($conn, $id_cta, $fecha, $op);
		echo $saldo;
		break;
		
	case "asientosConc":
		/*$fecha = $_REQUEST['fecha'];
		$fecha = substr($fecha, 6, 4).'-'.substr($fecha, 3, 2).'-'.substr($fecha, 0, 2);
		$id_cta = $_REQUEST['id_cta_banc'];
		$orden = $_REQUEST['orden'];
		$opcion = $_REQUEST['opcion'];*/
		$id_conc = $_REQUEST['id_conc'];

		$obj = new conciliacionBancaria2;
		$json = new Services_JSON();
		/*if ($opcion)
			echo $json->encode($obj->getAsientosConciliar($conn, $id_cta, $fecha, $orden));
		else*/
			echo $json->encode($obj->asientosConciliados($conn, $id_conc));
		break;
		
	case "movEstadoCuenta":
		$idEst = $_REQUEST['idEstadoCuenta'];
		$orden = $_REQUEST['orden'];
		$obj = new estadoCuenta;
		$json = new Services_JSON();
		echo $json->encode($obj->getMovimientos($conn, $idEst));
		break;
	
	case "traeFechasConc":
		$concBanc = new conciliacionBancaria;
		$ctaBanc = new cuentas_bancarias;
		$ctaBanc->get($conn, $_REQUEST['id_cta_banc']);
		$array = array();
		$array[0] = $concBanc->getFechaDesde($conn, $_REQUEST['id_cta_banc'], $escEnEje);
		$array[1] = $concBanc->getFechaHasta($conn, $_REQUEST['id_cta_banc'], $escEnEje);
		$array[2] = $ctaBanc;
		$array[3] = $concBanc->getSaldoInicialLibro($conn,$_REQUEST['id_cta_banc'],$array[0]);
		$json = new Services_JSON();
		echo $json->encode($array);
		break;
		
	case "traeFechasEstadoCta":
		$estCta = new estadoCuenta;
		$ctaBanc = new cuentas_bancarias;
		$ctaBanc->get($conn, $_REQUEST['id_cta_banc']);
		$array = array();
		$array[0] = $estCta->getFechaDesde($conn, $_REQUEST['id_cta_banc'], $escEnEje);
		$array[1] = $estCta->getFechaHasta($conn, $_REQUEST['id_cta_banc'], $escEnEje);
		$array[2] = $ctaBanc;
		$json = new Services_JSON();
		echo $json->encode($array);
		break;
		
	case "getProgRG":
		$ano = $_REQUEST['ano'];
		$cRequisiciones = requisicion_global::getRequisiciones($conn, $ano);
		if ($cRequisiciones){
			//die(var_dump($cRequisiciones));
			$json=new Services_JSON();
			echo $json->encode($cRequisiciones);
		}
	break;	
	
	case "muestracategoria":
		$ue = $_REQUEST['ue'];
		$cRelUECP = relacion_ue_cp::get_First_by_UE($conn, $ue,$escEnEje);
		if ($cRelUECP){
			$json=new Services_JSON();
			echo $json->encode($cRelUECP);
		}
	break;
	
	case "buscaAumentos":
		$nrodoc = $_REQUEST['nrodoc'];
		$aumento = movimientos_presupuestarios::get_monto_aumentos($conn,$nrodoc);
		if($aumento){
			$json = new Services_JSON();
			echo $json->encode($aumento);
		}
	break;
	
	case "retencion_nomina":
	$id = $_REQUEST['id'];
	$relRetNom = orden_pago::get_retenciones_causado($conn,$id);
	if($relRetNom){
		$json = new Services_JSON();
		echo $json->encode($relRetNom);
	}
	break;
	
	case "aportesNomina":
		$nrodoc = $_REQUEST['id'];
		$cRelaciones = solicitud_pago::get_suma_aportes_sp($conn, $nrodoc);
		if ($cRelaciones){
			$json=new Services_JSON();
			echo $json->encode($cRelaciones);
		}
	break;
	
	case "aportesNomina2":
		$nrodoc = $_REQUEST['id'];
		$cRelaciones = orden_pago::get_suma_aportes_op($conn, $nrodoc);
		if ($cRelaciones){
			$json=new Services_JSON();
			echo $json->encode($cRelaciones);
		}
	break;
	
	case "cargaPeriodos":
		$ano = date('Y');
		$mes = date('m');
		$dia = date('d');
		$oPeriodo = iva::get_periodos($conn,$dia,$mes,$ano);
		if($oPeriodo){
			$json=new Services_JSON();
			echo $json->encode($oPeriodo);
		}
	break;
	
	case "generaTXT":
		$rango = $_GET['rango'];
		$aux = explode(' ',$rango);
		$desde = $aux[0];
		$hasta = $aux[1];
		$sql = "SELECT  p.rif, to_char(op.fecha:: date,'yyyymm')::varchar AS periodo, f.fecha, f.nrofactura, f.nrocontrol, f.monto, f.base_imponible, f.iva_retenido, replace(f.nrocorret,'-','')::varchar AS nrocorret, f.monto_excento, f.iva::numeric ";
		$sql.= "FROM finanzas.facturas f ";
		$sql.= "INNER JOIN finanzas.orden_pago op ON (f.nrodoc = op.nrodoc) ";
		$sql.= "INNER JOIN puser.proveedores p ON (op.id_proveedor = p.id) ";
		$sql.= "WHERE (op.status = '2' AND op.fecha>='".$desde."' AND op.fecha<='".$hasta."') OR (op.status = '3' AND (op.fecha_anulacion>='".$desde."' AND op.fecha_anulacion<='".$hasta."') AND f.nrocorret<>null)";
		//$sql.= "WHERE (op.status = '2' AND op.fecha>='2007-06-01' AND op.fecha<='2007-06-15') OR (op.status = '3' AND (op.fecha_anulacion>='2007-06-01' AND op.fecha_anulacion<='2007-06-15') AND f.nrocorret<>null)";
		//die($sql);
		$r = $conn->Execute($sql);
		$fp = fopen("IVA.txt","w");
		$rif_contribuyente = 'G200004371'."\t";
		$cont = 1;
		while(!$r->EOF){
			$registro = $rif_contribuyente; //Rif Alcaldia
			$registro.= trim($r->fields['periodo']); //Periodo de Enteracion
			$registro.= "\t";
			$registro.= $r->fields['fecha']; //Fecha de la Factura
			$registro.= "\t";
			$registro.= "C"."\t";  //Tipo de Operacion
			$registro.= "01"."\t"; //Tipo de Documento
			$rifProveedor = str_replace('-','',$r->fields['rif']);
			//$registro.= $rifProveedor."\t";  //rif del Proveedor
			for($i=0;$i<(10-strlen($rifProveedor));$i++){
				$rifProveedor."0";
			}
			$registro.= $rifProveedor."\t";
			$numFactura = $r->fields['nrofactura'];
			/*for($i=0;$i<(20-strlen($r->fields['nrofactura']));$i++){
				$numFactura.= " ";
			}*/
			$registro.=$numFactura."\t";  
			$numControl= $r->fields['nrocontrol'];
			/*for($i=0;$i<(20-strlen($r->fields['nrocontrol']));$i++){
				$numControl.= " ";
			}*/
			$registro.= $numControl."\t";
			$montoDoc = $r->fields['monto']."\t";
			/*for($i=0;$i<(15-strlen($r->fields['monto']));$i++){
				$registro.= " ";
			}*/
			$registro.= $montoDoc;
			$baseImponible = $r->fields['base_imponible']."\t";
			/*for($i=0;$i<(15-strlen($r->fields['base_imponible']));$i++){
				$registro.= " ";
			}*/
			$registro.= $baseImponible;
			$montoIva = $r->fields['iva_retenido']."\t";
			/*for($i=0;$i<(15-strlen($r->fields['iva_retenido']));$i++){
				$registro.= " ";
			}*/
			$registro.= $montoIva;
			/*for($i=0;$i<18;$i++){
				$registro.= " ";
			}*/
			$registro.= "0\t";  //NUMERO DE DOCUMENTO AFECTADO
			$numComprobante = $r->fields['nrocorret']."\t";
			$registro.= $numComprobante;
			$montoExcento = $r->fields['monto_excento']."\t";
			/*for($i=0;$i<(15-strlen($r->fields['monto_excento']));$i++){
				$registro.= " ";
			} */
			$registro.= $montoExcento;
			$alicuota = $r->fields['iva'];
			if(substr_count($alicuota,'.')==1){
				$alicuota = str_pad($alicuota,4);
			} else {
				$alicuota.= ".00";
			}
			for($i=0;$i<(5-strlen($alicuota));$i++){
				$registro.= " ";
			}
			$registro.= $alicuota."\t";
			$registro.= "0";
			fputs($fp,$registro); 
			fwrite($fp,"\r\n");
			$cont++; 
			$r->movenext();
		}
		fclose($fp);
		echo true;
	break;
	
	case "cargaMeses":
		
		$oMeses = iva::get_meses($conn);
		if($oMeses){
			$json=new Services_JSON();
			echo $json->encode($oMeses);
		}
	break;
	
	case "clasificador_bienes":
		$sql="SELECT MAX(rcb.codigo) AS maximo, (SELECT codigo FROM puser.clasificador_bienes WHERE id = ".$_REQUEST['id_grupo'].") AS  codigo ";
		$sql.= "FROM puser.relacion_clasificacion_bienes rcb ";
		$sql.= "WHERE id_grupo = ".$_REQUEST['id_grupo'];
		$r = $conn->Execute($sql);
		$longitud = strlen($r->fields['codigo']);
		$newCodigo = $r->fields['codigo']."-".str_pad(substr($r->fields['maximo'], $longitud+1, 3) + 1, 3, 0, STR_PAD_LEFT);
		echo $newCodigo;
	break;
	
	case "disponibilidad_vacantes":   //Agregado por Deivis el 22/01/2008   Verifica  la disponibibilidad de cargos
		$car_cod = $_REQUEST['car_cod'];
		$sql="SELECT count(T.int_cod) as cant_act, C.car_cant ";
		$sql.="FROM rrhh.trabajador AS T ";
		$sql.="INNER JOIN  rrhh.cargo AS C ";
		$sql.="ON C.int_cod = T.car_cod ";
		$sql.="WHERE T.car_cod = $car_cod AND T.tra_estatus != 4 AND T.tra_estatus!= 5 ";
		$sql.="GROUP BY C.car_cant ";
		$r = $conn->Execute($sql);
		if($r->EOF){
			$sql="SELECT car_cant ";
			$sql.="FROM rrhh.cargo ";
			$sql.="WHERE int_cod = $car_cod";
			$r = $conn->Execute($sql);
			$disponibles = $r->fields[car_cant];
		} 
		else{
			$disponibles = $r->fields[car_cant] - $r->fields[cant_act];
			}
		echo $disponibles;
	break;
	
	case "partidasAconciliar":
		$idEdoCta = $_REQUEST['edoCta'];
		$idCta = $_REQUEST['ctaBan'];
		$idEdosCta = conciliacionBancaria2::mesesAnteriores($conn,$idEdoCta,$idCta);
		$idEdosCta = implode(',',$idEdosCta);
		$oConciliados = conciliacionBancaria2::getAsientosConciliar($conn,$idEdosCta);
		if($oConciliados){
			$json=new Services_JSON();
			echo $json->encode($oConciliados);
		}
	break;
	
	case "datosEdoCta":
		$idEdoCta = $_REQUEST['idEdoCta'];
		$q = "SELECT * FROM contabilidad.estado_cuenta WHERE id = $idEdoCta";
		$r = $conn->Execute($q);
		if($r){
			$lista[0] = muestraFecha($r->fields['fech_inicio']);
			$lista[1] = muestraFecha($r->fields['fech_fin']);
			$lista[2] = $r->fields['saldo_inic'];
			$lista[3] = $r->fields['saldo_fin'];
		}
		$json=new Services_JSON();
		echo $json->encode($lista);
	break;
	
	case "saldoMes":
		$idCta = $_REQUEST['idCta'];
		$fechaIni = guardaFecha($_REQUEST['fechaIni']);
		$fechaFin = guardaFecha($_REQUEST['fechaFin']);
		$auxMesAno = explode('-',$fechaIni);
		//$ctaBanco = $_REQUEST['idCtaContable'];
		$q = "SELECT id_plan_cuenta FROM finanzas.cuentas_bancarias WHERE id = $idCta";
		$r = $conn->Execute($q);
		$ctaBanco = $r->fields['id_plan_cuenta'];
		$oConciliacion = conciliacionBancaria2::getSaldoInicialLibro($conn,$ctaBanco,$fechaIni, $fechaFin, $auxMesAno[1], $auxMesAno[0]);
		if($oConciliacion){
			$json=new Services_JSON();
			echo $json->encode($oConciliacion);
		}
	
	break;
	
	case "buscaPartEdoCtanoLibro":
		$idEdoCta = $_REQUEST['idEdoCta'];
		$idCta = $_REQUEST['idCta'];
		$suma = $_REQUEST['suma'];
		$fecha = $_REQUEST['fecha'];
		$idEdosCta = conciliacionBancaria2::mesesAnteriores($conn,$idEdoCta,$idCta);
		$idEdosCta = implode(',',$idEdosCta);
		$oConciliacion = conciliacionBancaria2::partEdoCtanoLibro($conn,$idEdosCta,$idCta,$fecha);
		if($oConciliacion){
			$json=new Services_JSON();
			echo $json->encode($oConciliacion);
		}
	
	break;
	
	case "buscaPartLibronoEdoCta":
		$idEdoCta = $_REQUEST['idEdoCta'];
		$idCta = $_REQUEST['idCta'];
		$fecha = $_REQUEST['fecha'];
		$fechaIni = $_REQUEST['fechaIni'];
		$q = "SELECT id_plan_cuenta FROM finanzas.cuentas_bancarias WHERE id = $idCta";
		$r = $conn->Execute($q);
		$idCtaContable = $r->fields['id_plan_cuenta'];
		$idEdosCta = conciliacionBancaria2::mesesAnteriores($conn,$idEdoCta,$idCta);
		$idEdosCta = implode(',',$idEdosCta);
		$oConciliacion = conciliacionBancaria2::partLibronoEdoCta($conn, $idCtaContable, $idEdosCta, $fecha, $fechaIni);
		if($oConciliacion){
			$json=new Services_JSON();
			echo $json->encode($oConciliacion);
		}else
			echo "No se consiguieron elementos";
	break;
	
	case "buscaRequisicio":
		$requi = $_REQUEST['nroReq'];
		$requiGbl = $_REQUEST['nroReqGbl'];
		if((!$requi) && (!$requiGbl)){
			return false;
		} else {
			if(!empty($requiGbl)){
				$oReqGbl = new requisicion_global;
				if($oReqGbl->get($conn, $requiGbl)){
					$json = new services_JSON();
					echo $json->encode($oReqGbl);
			    }
			} else {
				$oRequi = new requisiciones;
				if($oRequi->get($conn, $requi)){
					$json = new services_JSON();
					echo $json->encode($oRequi);
				}
			}
		}
	break;
	
	case "validaMontoAnticipo":
		$nrodoc = $_REQUEST['id'];
		$oContObra = new contrato_obras;
		if($oContObra->get($conn, $nrodoc)){
			$json = new services_JSON();
			echo $json->encode($oContObra);
		}else
			echo "No se consiguieron elementos";
	break;	
	
	case "transferencia":
		$nrodoc = $_REQUEST['id'];
		$oTra = traFondosTerceros::getAsientos($conn, $nrodoc,$ctaFondos);
		if($oTra){
			$json = new services_JSON();
			echo $json->encode($oTra);
		}else
			echo "No se consiguieron elementos";
	break;
	
	case "revisa_lote":
		$ocheque =cheque::revisaLote($conn, $_REQUEST['banco'], $_REQUEST['cuenta'],$_REQUEST['desde'], $_REQUEST['hasta']);
		$json=new Services_JSON();
		echo $json->encode($ocheque);
	break;

	case "tipoCategoria":
		$idCat = $_GET['idCat'];
		$idEscenario = $_GET['escenario'];
		$q = "SELECT status FROM puser.categorias_programaticas WHERE id = '$idCat' AND id_escenario = '$idEscenario'";
		$r = $conn->Execute($q);
		$status = $r->fields['status'];
		echo $status;
	break;
	
	case "traeResponsableUnidad":

		$esc = $_REQUEST['escenario'];
		$uni = $_REQUEST['unidad'];
		$ounidad = new unidades_ejecutoras;
		$ounidad->get($conn,$uni,$esc);
		$responsable = $ounidad->responsable;
		echo $responsable;
	break;	
	
	case "tipo_producto":
		$id = $_REQUEST['id'];
		$tipoProd = new tipo_producto;
		$codigo = $tipoProd->getFamilia($conn,$id);
		$lista[0] = $codigo;
		$codigo2 = $tipoProd->getLastFamilia($conn,$id);
		$lista[1] = $codigo2;
		$json= new Services_JSON();
		echo $json->encode($lista);
		//echo $codigo;
	break;
	
	case "actualizaPrecio":
		$id_proveedor = $_REQUEST['id_proveedor'];
		$id_requi = $_REQUEST['id_requisicion'];
		//$busca = new ordcompra;
		$lista = ordcompra::actualizaPrecios($conn,$id_requi,$id_proveedor);	
		//die($lista);
		$json=new Services_JSON();
		
		echo $json->encode($lista);
	break;
	
	case "datosProveedor":
		$idProveedor = $_REQUEST['id_proveedor'];
		$oProvee = new proveedores;
		$oProvee->get($conn,$idProveedor);
		//$result = proveedores::get($conn,$idProveedor);
		//die(var_dump($oProvee));
		$json= new Services_JSON();
		echo $json->encode($oProvee);
	break;	
	
	//ESTE CASO SE USA CUANDO SE VA A CREAR UNA ORDEN DE PAGO NUEVA//
	case "facturassolicitud":
		$nrodoc = $_REQUEST['nrodoc'];
		$cRelaciones = solicitud_pago::getfacturas2($conn, $nrodoc);
		if ($cRelaciones){
			$json=new Services_JSON();
			echo $json->encode($cRelaciones);
		}
	break;	
	
	//ESTE CASO SE USA CUANDO SE VA A LLAMAR UNA SOLICITUD DE PAGO PROVEIENTE DE UNA CAJA CHICA CUYAS FACTURAS YA FUERON CARGADAS POR ESE MODULO
	case "facturassolicitudCC":
		$nrodoc = $_REQUEST['nrodoc'];
		$cRelaciones = solicitud_pago::getfacturasCajaChica($conn, $nrodoc);
		if ($cRelaciones){
			$json=new Services_JSON();
			echo $json->encode($cRelaciones);
		}
	break;	
}
?>
