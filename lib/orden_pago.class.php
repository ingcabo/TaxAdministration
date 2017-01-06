<?
class orden_pago{

	// Propiedades

	var $nrodoc;
	var $descripcion;
	var $observaciones; 
	var $id_tipo_documento;
	var $id_proveedor;
	var $proveedor;
	var $dir_proveedor;
	var $nroref;
	var $id_unidad_ejecutora;
	var $id_usuario;
	var $fecha;
	var $fecha_pago;
	var $rif;
	var $status;
	var $tipo_contribuyente;
	var $nrorefcomp;
	var $montodoc;
	var $montoret;
	var $montopagado;
	var $motivo;
	var $cuenta_anticipo;
	var $montoanticipo;
	var $cerrado;
	var $id_banco;
	var $id_nro_cuenta;
		
	#PROPIEDADES QUE SE MANEJAN PARA LAS FACTURAS#
	var $nrofac;
	var $fechafac;
	var $montofac;
	var $base_imponible;
	var $monto_excento;
	var $monto_iva;
	var $iva_retenido;
	var $iva;
	var $nrocontrol;
	var $descuento;
	
	#PROPIEDADES QUE SE MANEJAN PARA LAS RETENCIONES#
	var $codigoretencion;
	var $montoretencion;
	var $montobase;
	var $porcentaje;
	var $anio;
	var $porc_ret;

	var $total;
	

	/*****************************
			Objeto Relacion Partidas
	*****************************/
	var $relacion; // almacena un array de objetos de relaciones de partidas
	
	#PROPIEDADES QUE UTILIZAN LOS OBJETOS CON LA RELACION DE PARTIDAS#
	var $idParCat;
	var $id_categoria_programatica;
	var $id_partida_presupuestaria;
	var $categoria_programatica;
	var $partida_presupuestaria;
	var $monto;

	function get($conn, $id, $escEnEje){
		
		$q = "SELECT op.*, op.nroref AS nrorefe, op.nrodoc AS nrodoco, ue.descripcion AS unidad_ejecutora, sp.nroref AS nrodoccomp, p.nombre AS proveedor, p.direccion AS dir_proveedor, p.rif AS rif_proveedor, ";
		$q.= "p.provee_contrat AS tipo_contribuyente,  ri.tipo_contribuyente, ri.ingreso_periodo_fiscal, ue.id AS id_unidad_ejecutora, p.id AS id_proveedor ";
		$q.= "FROM finanzas.orden_pago op ";
		$q.= "LEFT JOIN finanzas.solicitud_pago sp ON (sp.nrodoc = op.nroref) ";
		$q.= "LEFT JOIN puser.movimientos_presupuestarios mp ON (mp.nrodoc = sp.nroref) ";
		$q.= "INNER JOIN puser.proveedores p ON (op.id_proveedor = p.id) ";
		$q.= "INNER JOIN puser.unidades_ejecutoras ue ON (op.id_unidad_ejecutora = ue.id) ";
		$q.= "LEFT JOIN puser.retencion_iva ri ON (ri.id_proveedor = p.id) ";
		$q.= "WHERE op.nrodoc='$id' AND (ue.id_escenario = '$escEnEje')";
		//die($q);
		$r = $conn->Execute($q);
		
		if(!$r->EOF){
			$this->nrodoc = $r->fields['nrodoco'];
			$this->descripcion = $r->fields['descripcion'];
			$aux = explode('-',$r->fields['nrodoccomp']);
			$td = new tipos_documentos;
			$td->get($conn,$aux[0]); 
			$this->tipdoc = $td;
			$this->nroref = $r->fields['nrorefe'];
			$this->id_condicion_pago = $r->fields['id_condicion_pago'];
			$this->id_tipo_solicitud = $r->fields['fuente_financiamiento']; // Se cambio la fuente dde financiamiento por id_tipo_silicitud
			$this->id_tipo_solicitud_si = $r->fields['id_tipo_solicitud_si'];
			$this->id_unidad_ejecutora = $r->fields['id_unidad_ejecutora'];
			$this->unidad_ejecutora = $r->fields['unidad_ejecutora'];
			$this->id_usuario = $r->fields['id_usuario'];
			$this->fecha = $r->fields['fecha'];
			$this->tipo_contribuyente = $r->fields['tipo_contribuyente'];
			$this->ingreso_periodo_fiscal = $r->fields['ingreso_periodo_fiscal'];
			$this->status = $r->fields['status'];
			$this->id_proveedor = $r->fields['id_proveedor'];
			$this->proveedor = $r->fields['proveedor'];
			$this->dir_proveedor = $r->fields['dir_proveedor'];
			$this->rif_proveedor = $r->fields['rif_proveedor'];
			$this->monto_si = $r->fields['monto_si'];
			$this->nrorefcomp = $r->fields['nrodoccomp'];
			$this->montodoc = $r->fields['montodoc'];
			$this->montoret = $r->fields['montoret'];
			$this->montopagado = $r->fields['montopagado'];
			$this->motivo = $r->fields['motivo'];
			$this->getfacturas($conn, $r->fields['nrodoco']);
			$this->getretenciones($conn, $r->fields['nrodoco']);
			$this->getretencionesNom($conn, $r->fields['nrodoco']);
			$this->cuenta_anticipo = $r->fields['cuenta_contable_anticipo'];
			$this->montoanticipo = $r->fields['monto_anticipo'];
			$this->cerrado = $r->fields['cerrado'];
			$this->id_banco = $r->fields['id_banco'];
			$this->id_nro_cuenta = $r->fields['id_cuenta'];
			return true;
		
		}else
			
			return false;
	}

	function get_all($conn, $escEnEje,$orden="id"){
		
		$q = "SELECT * FROM finanzas.orden_pago ";
		$q.= "ORDER BY $orden ";
		$r = $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new orden_pago;
			$ue->get($conn, $r->fields['id'], $escEnEje);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, 
						$nroref,
						$fecha,
						$status,
						$id_condicion_pago,
						$fuente_financiamiento,
						$id_tipo_solicitud_si= '',
						$monto_si = '',
						$aPartidas,
						$aFacturas,
						$aRetenciones,
						$id_proveedor,
						$id_unidad_ejecutora,
						$descripcion,
						$nrodoccomp,
						$id_banco,
						$id_cuenta,
						$id_cta_anticipo = '',
						$monto_anticipo = ''
						
						){
		
		
		//die("aqui ".$id_tipo_solicitud_si);
		$nrodoc = $this->getNroDoc($conn, '004');
		
				
		$q = "INSERT INTO finanzas.orden_pago ";
		$q.= "( ";
		$q.= "nrodoc, ";
		$q.= "nroref, ";
		$q.= "fecha, ";
		$q.= "status, ";
		$q.= "id_condicion_pago, ";
		$q.= "fuente_financiamiento, ";
		$q.= "id_tipo_solicitud_si, ";
		$q.= "monto_si, ";
		$q.= "id_proveedor, ";
		$q.= "id_unidad_ejecutora, ";
		$q.= "descripcion, ";
		$q.= "cuenta_contable_anticipo, ";
		$q.= "monto_anticipo, ";
		$q.= "id_banco, ";
		$q.= "id_cuenta ";
		$q.= ") ";
		$q.= "VALUES ";
		$q.= "( ";
		$q.= " '$nrodoc', ";
		$q.= " '$nroref', ";
		$q.= " '$fecha', ";
		$q.= " '$status', ";
		$q.= " '$id_condicion_pago', ";
		$q.= " '$fuente_financiamiento', ";
		$q.= " '$id_tipo_solicitud_si', ";
		$q.= " '$monto_si', ";
		$q.= " '$id_proveedor', ";
		$q.= " '$id_unidad_ejecutora', ";
		$q.= " '$descripcion', ";
		$q.= " $id_cta_anticipo, ";
		$q.= " $monto_anticipo, ";
		$q.= " $id_banco, ";
		$q.= " $id_cuenta ";
		$q.= ") ";
		
		//die($q);	 
				if($conn->execute($q)){
				//if(1==1){
						if($id_tipo_solicitud_si == 0)
							$this->addRelacionPartidas($conn, $nrodoc,$aPartidas); 
													
						$this->add_facturas($conn, $nrodoc,$aFacturas);
					
						$this->addRetenciones($conn, $nrodoc, $aRetenciones,date('Y'));
									
						$aux = explode('-',$nrodoccomp);
						$tipdoccomp = $aux[0];
						if($tipdoccomp == '010');
						  $this->set_retenciones_nomina($conn,$nrodoccomp,$nrodoc);
						
						$this->msj = REG_ADD_OK;
						return true;
						
					}else{
						
						$this->msj = ERROR_ADD;
						return false;
					}
		}

	function set($conn, 
						$nrodoc,
						$nroref,
						$fecha,
						$status,
						$id_condicion_pago,
						$fuente_financiamiento,
						$id_tipo_solicitud_si,
						$monto_si,
						$aPartidas,
						$aFacturas,
						$aRetenciones,
						$id_proveedor,
						$id_unidad_ejecutora,
						$descripcion,
						$nrodoccomp,
						$id_banco,
						$id_cuenta,
						$id_cta_anticipo = '',
						$monto_anticipo = ''
						){
		
		$q = "UPDATE finanzas.orden_pago SET  ";
		//$q.= "status = '$status', ";
		$q.= "id_condicion_pago = '$id_condicion_pago', ";
		$q.= "fuente_financiamiento = '$fuente_financiamiento', ";
		$q.= "nroref = '$nroref', ";
		$q.= "id_tipo_solicitud_si = '$id_tipo_solicitud_si', ";
		$q.= "fecha = '$fecha', ";
		$q.= "monto_si = '$monto_si', ";
		$q.= "id_proveedor = '$id_proveedor', ";
		$q.= "id_unidad_ejecutora = '$id_unidad_ejecutora', ";
		$q.= "descripcion = '$descripcion', ";
		$q.= "cuenta_contable_anticipo = $id_cta_anticipo, ";
		$q.= "monto_anticipo = $monto_anticipo, ";
		$q.= "id_banco = $id_banco, ";
		$q.= "id_cuenta = $id_cuenta ";
		$q.= "WHERE nrodoc='$nrodoc' ";
		//die($q);	
		
		if($conn->Execute($q)){
			if($this->delRelacionPartidas($conn, $nrodoc) && $this->delfacturas($conn, $nrodoc) && $this->delretenciones($conn, $nrodoc)){
				
				if($id_tipo_solicitud_si==0)
					$this->addRelacionPartidas($conn, $nrodoc,$aPartidas);
													
				$this->add_facturas($conn, $nrodoc,$aFacturas);
					
				$this->addRetenciones($conn, $nrodoc, $aRetenciones,date('Y'));
				
				$aux = explode('-',$nrodoccomp);
				$tipdoccomp = $aux[0];
				if($tipdoccomp == '010');
					  $this->set_retenciones_nomina($conn,$nrodoccomp,$nrodoc);
				
				$this->msj = REG_SET_OK;
				return true;
			
			}else{
				
				$this->msj = ERROR_SET;
				return false;
				
			}
			
		}else{
			
			$this->msj = ERROR_SET;
			return false;
			
		}
	}

	function del($conn, $id){
		$q = "DELETE FROM finanzas.solicitud_pago WHERE id='$id'";
		if($conn->Execute($q)){
			$this->msj = REG_DEL_OK;
			return true;
		}else{
			$this->msj = ERROR_DEL;
			return false;
		}
	}
	
	function anular($conn, $nrodoc, $id_usuario,
										$id_unidad_ejecutora,
										$ano,
										$descripcion,
										$tipdoc,
										$nroref,
										$fechadoc,
										$status,
										$id_proveedor,
										$aPartidas,
										$montoDoc, 
										$montoRet,
										$motivo,
										$escEnEje){
	
		try{
		$q3 ="UPDATE finanzas.orden_pago SET status='3', motivo='$motivo' WHERE nrodoc='$nrodoc'";
		//die($q3);
		$r3 = $conn->Execute($q3) or die($q3);
		//$r3 = true;
		//die("status: ".$status);
		if(!empty($nroref)){
			if ($r3 && $status=='2'){
			
				$JsonRec = new Services_JSON();
				$JsonRec = $JsonRec->decode(str_replace("\\","",$aPartidas));
				$contador = sizeof($JsonRec->partidaspresupuestarias); 
				
				$oProveedor = new proveedores;
				$oProveedor->get($conn, $id_proveedor);
				$nrodocanulado = $nrodoc."-ANULADO";
				//REGISTRO LA SOLICITUD EN MOVIMIENTOS PRESUPUESTARIOS//
				$q2 = "INSERT INTO puser.movimientos_presupuestarios ";
				$q2.= "(id_usuario, id_unidad_ejecutora, ano, descripcion, nrodoc, tipdoc, nroref, ";
				$q2.= "fechadoc, status, id_proveedor, status_movimiento) ";
				$q2.= "VALUES ";
				$q2.= "('$id_usuario', '$id_unidad_ejecutora', '$ano', '$descripcion', '$nrodoc', '$tipdoc', '$nrodocanulado', ";
				$q2.= " '$fechadoc', '$status', '$id_proveedor', '2') ";
				//die($q2);
				//ESTE FOREACH SE HACE PARA PASAR LAS VARIABLES A LA CLASE MOVIMIENTOS PRESUPUESTARIOS//
				foreach($JsonRec->partidaspresupuestarias as $partidas){
					$monto = guardaFloat($partidas[2]) * (-1);
					//$monto = str_replace(".",",",$monto);
					//die($monto);
					$aIdParCat[] = $partidas[3];
					$aCategoriaProgramatica[] = $partidas[0];
					$aPartidaPresupuestaria[] = $partidas[1];
					$aMonto[] = muestraFloat($monto);
				}
				/*print_r($aIdParCat);
				die(print_r($aMonto));*/
					
				$r2 = $conn->Execute($q2) or die($q);
				
				if($r2){
					
					if(movimientos_presupuestarios::add_relacion($conn,$aIdParCat,$aCategoriaProgramatica,$aPartidaPresupuestaria,$nrodoc,$aMonto) ){
		
						if(relacion_pp_cp::set_desde_solicitud_pagos_anulada($conn, $aIdParCat, $aMonto , $status)){
							$q = "SELECT public.asiento_orden_pago('$nrodoc'::varchar, 0::int2, $escEnEje::int8) ";
							$r = $conn->Execute($q);
							$this->msj = ORDEN_ANULADA;
							return true;
		
						}
					}
				}else{
			
					$this->msj = ORDEN_NO_ANULADA;
					return false;
				}
			
			}else{
			
				$this->msj = ORDEN_ANULADA;
				return false;
			}
		}else if($r3 and empty($nroref)){
				$q = "SELECT public.asiento_orden_pago('$nrodoc'::varchar, 1::int2, $escEnEje::int8) ";
				$r = $conn->Execute($q);
				$this->msj = ORDEN_ANULADA;
				return true;
				} else {
					$this->msj = ORDEN_ANULADA;
					return false;
				}
		}
		
		catch( ADODB_Exception $e ){
			//die($conn->ErrorMsg());
				$this->msj = $conn->ErrorMsg();
				return false;

		}
	}
	
	function addRelacionPartidas($conn,$nrodoc,	$aPartidas){
	
		$JsonRec = new Services_JSON();
		$JsonRec = $JsonRec->decode(str_replace("\\","",$aPartidas));
		if(is_array($JsonRec->partidaspresupuestarias)){
			foreach($JsonRec->partidaspresupuestarias as $partidas){
			
				$q = "INSERT INTO finanzas.relacion_orden_pago ";
				$q.= "( id_parcat, id_categoria_programatica, id_partida_presupuestaria, id_orden_pago, monto) ";
				$q.= "VALUES ";
				$q.= "('$partidas[3]', '$partidas[0]', '$partidas[1]', '$nrodoc', '".guardaFloat($partidas[2])."') ";
				$r = $conn->Execute($q) or die($q);
			} 
		}
		if($r)
			return true;
		else
			return false;
	}

	function delRelacionPartidas($conn, $nrodoc){
		$q = "DELETE FROM finanzas.relacion_orden_pago WHERE id_orden_pago='$nrodoc'";
		//echo $q;
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	function getRelacionPartidas($conn, $id, $escEnEjec){
		$q = "SELECT relacion_orden_pago.*, partidas_presupuestarias.descripcion AS partida_presupuestaria,  ";
		$q.= "categorias_programaticas.descripcion AS categoria_programatica, finanzas.orden_pago.nroref, puser.partidas_presupuestarias.ano ";
		$q.= "FROM finanzas.relacion_orden_pago  ";
		$q.= "INNER JOIN puser.partidas_presupuestarias ON (relacion_orden_pago.id_partida_presupuestaria = partidas_presupuestarias.id) ";
		$q.= "INNER JOIN puser.categorias_programaticas ON (relacion_orden_pago.id_categoria_programatica = categorias_programaticas.id) ";
		$q.= "Inner Join finanzas.orden_pago ON finanzas.relacion_orden_pago.id_orden_pago = finanzas.orden_pago.nrodoc ";
		$q.= "WHERE relacion_orden_pago.id_orden_pago='$id' ";
		$q.= "AND categorias_programaticas.id_escenario = '$escEnEjec' ";
		$q.= "AND partidas_presupuestarias.id_escenario = '$escEnEjec' ";
			//die($q);
		$r = $conn->Execute($q);
		while(!$r->EOF){
			$ue = new orden_pago;
			$ue->nrodoc = $r->fields['nrodoc'];
			$ue->nroref = $r->fields['nroref'];
			$ue->idParCat	= $r->fields['id_parcat'];
			$ue->id_partida_presupuestaria	= $r->fields['id_partida_presupuestaria'];
			$ue->id_categoria_programatica = $r->fields['id_categoria_programatica'];
			$ue->partida_presupuestaria	= $r->fields['partida_presupuestaria'];
			$ue->categoria_programatica = $r->fields['categoria_programatica'];
			$ue->monto = $r->fields['monto'];
								
			$coleccion[] = $ue;
			$r->movenext();
					
						
		}
		return $coleccion;
	}

	function getCategorias($conn, $escEnEjec){
		$q = "SELECT  ";
		$q.= "categorias_programaticas.descripcion AS categoria_programatica,  ";
		$q.= "partidas_presupuestarias.descripcion AS partida_presupuestaria, ";
		$q.= "categorias_programaticas.id AS id_categoria_programatica,  ";
		$q.= "partidas_presupuestarias.id AS id_partida_presupuestaria ";
		$q.= "FROM relacion_pp_cp ";
		$q.= "INNER JOIN categorias_programaticas ON (categorias_programaticas.id = relacion_pp_cp.id_categoria_programatica) ";
		$q.= "INNER JOIN partidas_presupuestarias ON (partidas_presupuestarias.id = relacion_pp_cp.id_partida_presupuestaria) ";
		$q.= "WHERE substr(relacion_pp_cp.id_partida_presupuestaria, 1, 3) = '401' ";
		$q.= "AND categorias_programaticas.id_escenario = '$escEnEjec' ";
		$q.= "AND partidas_presupuestarias.id_escenario = '$escEnEjec' ";
		$q.= "ORDER BY categorias_programaticas.descripcion ";

		//echo($q);
		$r = $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new categorias_programaticas;
			$ue->id = $r->fields['id_categoria_programatica'];
			$ue->descripcion = $r->fields['categoria_programatica'];
			$coleccion[] = $ue;
			$r->movenext();
		}
		return $coleccion;
	}
	
	function aprobar($conn, $nrodoc, $id_usuario,
										$id_unidad_ejecutora,
										$ano,
										$descripcion,
										$nroref,
										$fechadoc,
										$status,
										$id_proveedor,
										$aPartidas,
										$montoDoc,
										$montoRet,
										$escEnEje){
		
		try {
		$fecha = explode("-",$fechadoc);
		
		$ano = $fecha[0];
		$mes = $fecha[1];
		if(!empty($nroref)){
			
			// Se valida la relacion de partidas programaticas con cuentas contables
			$q = "SELECT finanzas.validar_relacion_cc_pp('$nrodoc'::varchar) ";
			//die($q);
			$r = $conn->Execute($q);
			
			$JsonRec = new Services_JSON();
			$JsonRec = $JsonRec->decode(str_replace("\\","",$aPartidas));
			$contador = sizeof($JsonRec->partidaspresupuestarias); 
			
			$tipdoc= '004';
			// chequeo la disponibilidad actual en la partida, si en alguna no hay disponibilidad no se aprueba la orden
			for($i = 0; $i < $contador; $i++){
				$q = "SELECT relacion_pp_cp.disponible FROM relacion_pp_cp WHERE id = '".$JsonRec->partidaspresupuestarias[$i][3]."' ";
				
				$r = $conn->Execute($q);
				if($r){
					if($r->fields['disponible'] < guardafloat($aMonto[$i])){
						$this->msj = ERROR_ORDEN_PAGO_APR_NO_DISP;
						return false;
					}
				}
			}

			$oProveedor = new proveedores;
			$oProveedor->get($conn, $id_proveedor);
			//$nroref = $tipodoc."-".$nroref;
			//REGISTRO LA SOLICITUD EN MOVIMIENTOS PRESUPUESTARIOS//
			$q = "INSERT INTO puser.movimientos_presupuestarios ";
			$q.= "(id_usuario, id_unidad_ejecutora, ano, descripcion, nrodoc, tipdoc, nroref, ";
			$q.= "fechadoc, status, id_proveedor) ";
			$q.= "VALUES ";
			$q.= "('$id_usuario', '$id_unidad_ejecutora', '$ano', '$descripcion', '$nrodoc', '$tipdoc', '$nroref', ";
			$q.= " '$fechadoc', '$status', '$id_proveedor') ";
			//ESTE FOREACH SE HACE PARA PASAR LAS VARIABLES A LA CLASE MOVIMIENTOS PRESUPUESTARIOS//
			foreach($JsonRec->partidaspresupuestarias as $partidas){
			
				$aIdParCat[] = $partidas[3];
				$aCategoriaProgramatica[] = $partidas[0];
				$aPartidaPresupuestaria[] = $partidas[1];
				$aMonto[] = $partidas[2];
			}
					
			$r = $conn->Execute($q);
			//$r = true;
			if($r){
					if(movimientos_presupuestarios::add_relacion($conn,$aIdParCat,$aCategoriaProgramatica,$aPartidaPresupuestaria,$nrodoc,$aMonto) &&
					$this->setFechaAprobacion($conn, $nrodoc, $montoDoc, $montoRet)){
						
						$q = "SELECT public.asiento_orden_pago('$nrodoc'::varchar, 0::int2, $escEnEje::int8) ";
						$r = $conn->Execute($q);
						if(relacion_pp_cp::set_desde_solicitud_pagos($conn, $aIdParCat, $aMonto)){
							$this->getCorrelativoRetenciones($conn, $nrodoc, $ano, $mes);
							$this->msj = ORDEN_APROBADA;
							return true;
		
						}
					}
			}else{
		
				$this->msj = ORDEN_NO_APROBADA;
				return false;
			
			}
		} else {
			if($this->setFechaAprobacion($conn, $nrodoc, $montoDoc, $montoRet)){
				$this->getCorrelativoRetenciones($conn, $nrodoc, $ano, $mes);
				$q = "SELECT public.asiento_orden_pago('$nrodoc'::varchar, 1::int2, $escEnEje::int8) ";
				$r = $conn->Execute($q);
				$this->msj = ORDEN_APROBADA;
				
				return true;
			} else {
				$this->msj = ORDEN_NO_APROBADA;
				return false;
			}
		}
		}
		
		catch( ADODB_Exception $e ){
			//die($conn->ErrorMsg());
				$this->msj = $conn->ErrorMsg();
				return false;

		}	
	}
	
	function setNrodoc($conn, $nrodoc, $id){
		$q = "UPDATE solicitud_pago SET nrodoc = '$nrodoc' WHERE id='$id'";
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	function setFechaAprobacion($conn, $id, $montoDoc, $montoRet){
		$q = "UPDATE finanzas.orden_pago SET fecha_aprobacion = now(), status='2', montodoc = '".guardaFloat($montoDoc)."', montoret = '".guardaFloat($montoRet)."' WHERE nrodoc='$id'";
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	function buscar($conn, $id_proveedor, $id_ue, $fecha_desde, $fecha_hasta, $nrodoc,$descripcion, $status, $orden="nrodoc", $from=0, $max=0)
	{
		if(empty($id_proveedor) && empty($id_ue) && empty($fecha_desde) && empty($fecha_hasta)&& empty($nrodoc) && empty($descripcion) && empty($status))
			return false;
		
		$q = 	"SELECT DISTINCT finanzas.orden_pago.nrodoc, finanzas.orden_pago.status, proveedores.nombre FROM finanzas.orden_pago ";
		$q.=  "LEFT Join finanzas.relacion_orden_pago ON finanzas.orden_pago.nrodoc = finanzas.relacion_orden_pago.id_orden_pago ";
		$q.=  "INNER JOIN puser.proveedores ON (finanzas.orden_pago.id_proveedor = puser.proveedores.id) ";
		$q.= 	"WHERE  1=1 ";
		$q.= 	!empty($nrodoc) ? "AND orden_pago.nrodoc='$nrodoc' ": "";
		$q.= 	!empty($fecha_desde) ? "AND orden_pago.fecha >='".guardafecha($fecha_desde)."' ": "";
		$q.= 	!empty($fecha_hasta) ? "AND orden_pago.fecha <='".guardafecha($fecha_hasta)."' ": "";
		$q.= 	!empty($id_proveedor) ? "AND orden_pago.id_proveedor = '$id_proveedor'  ":"";
		$q.= 	!empty($status) ? "AND orden_pago.status = '$status'  ":"";
		$q.= 	!empty($descripcion) ? "AND orden_pago.descripcion ILIKE '%$descripcion%'  ":"";
		$q.= 	!empty($id_ue) ? "AND orden_pago.id_unidad_ejecutora = '$id_ue'  ":"";
		$q.= 	"ORDER BY orden_pago.$orden ";
		//die($q);
		
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from):$conn->Execute($q);
		if(!$r || $r->EOF)
			return false;
			
		$collection=array();
		while(!$r->EOF){
			$ue = new orden_pago;
			$ue->nrodoc = $r->fields['nrodoc'];
			$ue->status = $r->fields['status'];
			$ue->proveedor = $r->fields['nombre'];
			$coleccion[] = $ue;
			$r->movenext();
		}
		return $coleccion;
	}
	
	function totalRegsBusqueda($conn, $id_proveedor, $id_ue, $fecha_desde, $fecha_hasta, $nrodoc,$descripcion,$status)
	{
		if(empty($id_proveedor) && empty($id_ue) && empty($fecha_desde) && empty($fecha_hasta)&& empty($nrodoc) && empty($descripcion) && empty($status))
			return 0;
		
		$q = 	"SELECT DISTINCT finanzas.orden_pago.nrodoc, finanzas.orden_pago.status FROM finanzas.orden_pago ";
		$q.=  "LEFT Join finanzas.relacion_orden_pago ON finanzas.orden_pago.nrodoc = finanzas.relacion_orden_pago.id_orden_pago ";
		$q.= 	"WHERE  1=1 ";
		$q.= 	!empty($nrodoc) ? "AND orden_pago.nrodoc='$nrodoc' ": "";
		$q.= 	!empty($fecha_desde) ? "AND orden_pago.fecha >='".guardafecha($fecha_desde)."' ": "";
		$q.= 	!empty($fecha_hasta) ? "AND orden_pago.fecha <='".guardafecha($fecha_hasta)."' ": "";
		$q.= 	!empty($id_proveedor) ? "AND orden_pago.id_proveedor = '$id_proveedor'  ":"";
		$q.= 	!empty($status) ? "AND orden_pago.status = '$status'  ":"";
		$q.= 	!empty($descripcion) ? "AND orden_pago.descripcion ILIKE '%$descripcion%'  ":"";
		//die($q);

		$r = $conn->Execute($q);
		
		return $r->RecordCount();
	}
	
	function getNroDoc($conn, $tipdoc){
		$q = "SELECT max(nrodoc) AS nrodoc FROM finanzas.orden_pago ";
		$r = $conn->execute($q);
		//die($r->fields['nrodoc']);
		//CAMBIAR AL CERRAR PRESUPUESTO
		return $tipdoc."-".str_pad(substr($r->fields['nrodoc'], 4, 4) + 1, 4, 0, STR_PAD_LEFT)."-".date('Y');
		//return $tipdoc."-".str_pad(substr($r->fields['nrodoc'], 4, 4) + 1, 4, 0, STR_PAD_LEFT)."-2007";
	}
	
	function add_facturas($conn,$nrodoc,$aFacturas){
		
		$JsonRec = new Services_JSON();
		$JsonRec = $JsonRec->decode(str_replace("\\","",$aFacturas));
		if(is_array($JsonRec->facturas)){
			foreach($JsonRec->facturas as $facturas){
		
				$fecha2 = guardafecha($facturas[2]);
				$q = "INSERT INTO finanzas.facturas (
						nrodoc, 
						nrofactura,
						nrocontrol, 
						fecha, 
						monto, 
						base_imponible,
						descuento, 
						monto_excento, 
						monto_iva, 
						iva_retenido,
						iva,
						id_retencion) ";
				$q .= "VALUES ";
				$q .= "('$nrodoc', 
						'$facturas[0]',
						'$facturas[1]', 
						'$fecha2', 
						$facturas[3], 
						$facturas[8], 
						$facturas[4], 
						$facturas[5], 
						$facturas[9], 
						$facturas[10],
						$facturas[7],
						$facturas[6]
						)";
				//die($q);		
				$r = $conn->Execute($q) or die($q);
			}
		}
		
		if($r)
			return true;
		else
			return false; 
				
	
	}
	
	function delfacturas($conn, $nrodoc){
		
		$q = "DELETE FROM finanzas.facturas WHERE nrodoc='$nrodoc'";
		//echo "<br>".$q;
		$r = $conn->Execute($q);
		
		if ($r){
			
			return true;
			
		}else{
			
			return false;
			
		}
	
	}
	
	function getfacturas($conn, $nrodoc){
		
		if ($nrodoc==""){
			
			return false;
		
		}
			
		else{
			$q = "SELECT * FROM finanzas.facturas WHERE nrodoc = '$nrodoc'";
			//die($q);
			$r = $conn->Execute($q);
			
			while (!$r->EOF){
				
				$sp = new solicitud_pago;
				$sp->nrofac = $r->fields['nrofactura'];
				$sp->fechafac = muestrafecha($r->fields['fecha']);
				$sp->montofac = $r->fields['monto'];
				$sp->base_imponible = $r->fields['base_imponible'];
				$sp->monto_excento = $r->fields['monto_excento'];
				$sp->monto_iva = $r->fields['monto_iva'];
				$sp->iva_retenido = $r->fields['iva_retenido'];
				$sp->iva = $r->fields['iva'];
				$sp->nrocontrol = $r->fields['nrocontrol'];
				$sp->id_retencion = $r->fields['id_retencion'];
				$sp->descuento = $r->fields['descuento'];
				$coleccion[] = $sp;
				$r->movenext();
				
			}
			
			$this->relacionFacturas = new Services_JSON();
			$this->relacionFacturas = is_array($coleccion) ? $this->relacionFacturas->encode($coleccion) : false;
		}
			
	}
	
	function addRetenciones($conn, $nrodoc, $aRetenciones, $anio){
		
		$JsonRec = new Services_JSON();
		$JsonRec = $JsonRec->decode(str_replace("\\","",$aRetenciones));
		
		if(is_array($JsonRec->retenciones)){
			
			foreach($JsonRec->retenciones as $retenciones){
					
				$q = "INSERT INTO finanzas.relacion_retenciones_orden 
					(nrodoc, codret, mntret, mntbas, porcen, anio, aplico_sust,nrofactura) 
				 		VALUES 
				 	('$nrodoc', '$retenciones[1]', $retenciones[6], ".guardaFloat($retenciones[3]).", $retenciones[2], '$anio', $retenciones[5],'$retenciones[0]')";
					//die($q);
			$r = $conn->Execute($q) or die($q);
			}
			
		}
		
		if($r)
			return true;
		else
			return false; 
	
	}
	
	function getretenciones($conn, $nrodoc){
		
		if ($nrodoc==""){
			
			return false;
		
		}else{
		
			$q = "SELECT rr.*, ra.descri AS descripcion FROM finanzas.relacion_retenciones_orden rr ";
			$q.= "INNER JOIN finanzas.retenciones_adiciones ra ON (rr.codret = ra.id) ";
			$q.= "WHERE nrodoc = '$nrodoc'";
			//die($q);
			$r = $conn->Execute($q) or die($q);
			//die(guardafloat($r->fields['porcen']));
			while (!$r->EOF){
				
				$sp = new solicitud_pago;
				$sp->nrodoc = $r->fields['nrodoc'];
				$sp->codigoretencion = $r->fields['codret'];
				$sp->montoretencion = $r->fields['mntret'];
				$sp->montobase = $r->fields['mntbas'];
				$sp->porcentaje = $r->fields['porcen'];
				$sp->anio = $r->fields['anio'];
				$sp->porc_ret = $r->fields['porc_ret'];
				$sp->aplico_sust = $r->fields['aplico_sust'];
				$sp->descripcion_ret = $r->fields['descripcion'];
				$sp->nrofactura = $r->fields['nrofactura'];
				$coleccion[] = $sp;
				$r->movenext();
				
			}
			
			$this->relacionRetenciones = new Services_JSON();
			$this->relacionRetenciones = is_array($coleccion) ? $this->relacionRetenciones->encode($coleccion) : false;
		}
			
	}
	
	function getretencionesNom($conn, $nrodoc){
		
		if ($nrodoc==""){
			
			return false;
		
		}else{
		
			$q = "SELECT pr.* FROM rrhh.presupuesto_retenciones pr ";
			$q.= "WHERE nro_doc_causado = '$nrodoc'";
			//die($q);
			$r = $conn->Execute($q) or die($q);
			//die(guardafloat($r->fields['porcen']));
			while (!$r->EOF){
				
				$sp = new solicitud_pago;
				$sp->id = $r->fields['conc_cod'];
				$sp->monto = $r->fields['monto'];
				$coleccion[] = $sp;
				$r->movenext();
				
			}
			
			$this->relacionRetencionesNom = new Services_JSON();
			$this->relacionRetencionesNom = is_array($coleccion) ? $this->relacionRetencionesNom->encode($coleccion) : false;
		}
			
	}
	
	function delretenciones($conn, $nrodoc){
		$q = "DELETE FROM finanzas.relacion_retenciones_orden WHERE nrodoc='$nrodoc'";
		//echo "<br>".$q;
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	#ESTA FUNCION TRAE LA SUMATORIA DE LA FACTURA  Y LAS RETENCIONES POR SOLICITUD DE PAGOSS#
	function GetMontoRetFac($conn, $nrodoc){
		
		$q = 	"SELECT finanzas.solicitud_pago.nrodoc, 
				Sum(relacion_retenciones_solicitud.mntret) AS monto_retenciones,
				(SELECT SUM(facturas.monto) AS monto_factura FROM finanzas.facturas WHERE nrodoc = '$nrodoc') AS monto_factura
				FROM finanzas.solicitud_pago
				Inner Join finanzas.relacion_retenciones_solicitud ON solicitud_pago.nrodoc = relacion_retenciones_solicitud.nrodoc
				Inner Join finanzas.facturas ON solicitud_pago.nrodoc = finanzas.facturas.nrodoc
				WHERE solicitud_pago.nrodoc = '$nrodoc'
				GROUP BY solicitud_pago.nrodoc";
			//die($q);	
		$r = $conn->execute($q);
		
		if (!$r->EOF){
			
			$ue = new solicitud_pago;
			$ue->nrodoc				=	$r->fields['nrodoc'];
			$ue->montoretencion 	= 	$r->fields['monto_retenciones'];
			$ue->montofac 			= 	$r->fields['monto_factura'];
			
					
			$r->movenext();
			
		}	
			return $ue;
				
			
		
	
	}
	
	function GetMontoRetFacOP($conn, $nrodoc){
		
		$q = 	"SELECT finanzas.solicitud_pago.nrodoc, 
				Sum(relacion_retenciones_solicitud.mntret) AS monto_retenciones,
				Sum(facturas.monto) AS monto_factura 
				FROM finanzas.solicitud_pago
				Inner Join finanzas.relacion_retenciones_solicitud ON solicitud_pago.nrodoc = relacion_retenciones_solicitud.nrodoc
				Inner Join finanzas.facturas ON solicitud_pago.nrodoc = finanzas.facturas.nrodoc
				WHERE solicitud_pago.nrodoc = '$nrodoc'
				GROUP BY solicitud_pago.nrodoc";
			//die($q);	
		$r = $conn->execute($q);
		
		while (!$r->EOF){
			
			$ue = new solicitud_pago;
			$ue->nrodoc				=	$r->fields['nrodoc'];
			$ue->montoretencion 	= 	$r->fields['monto_retenciones'];
			$ue->montofac 			= 	$r->fields['monto_factura'];
			$coleccion = $ue;
					
			$r->movenext();
			
		}	
			return $coleccion;
				
			
		
	
	}	
	
	function showNrodoc($nrodoc){
		$aux = explode("-",$nrodoc);
		$tipdoc = $aux[0];
		$nrodoc = $aux[1];
		return $nrodoc;	
	}
	
	function addNrodoc($nrodoc, $tipdoc){
		$aux = $tipdoc."-".$nrodoc."-".date('Y');
		return $aux;
	}
	
	function getSolicitudes($conn,$status, $id_unidad_ejecutora='', $id_proveedor='', $nrodoc=''){
		$q = "SELECT sp.*, orden_pago.nrodoc as op_nrodoc ";
		$q.= "FROM finanzas.solicitud_pago sp ";
		$q.= "LEFT OUTER JOIN finanzas.orden_pago ON (sp.nrodoc = orden_pago.nroref) ";
		$q.= "WHERE ";
		$q.= !empty($id_unidad_ejecutora) ? "sp.id_unidad_ejecutora = '$id_unidad_ejecutora' AND " : "";
		$q.= !empty($id_proveedor) ? "sp.id_proveedor = '$id_proveedor' AND " : "";
		$q.= !empty($nrodoc) ? "sp.nrodoc = '$nrodoc' AND " : "";
		$q.= "(sp.status = '2' AND NOT EXISTS( SELECT nroref FROM finanzas.orden_pago WHERE nroref=sp.nrodoc AND status=2)) OR (orden_pago.status = '3' AND NOT EXISTS( SELECT nroref FROM finanzas.orden_pago WHERE nroref=sp.nrodoc AND status=2)) AND sp.status = '2'";
		//echo $q;
		//die($q);
		$r = $conn->Execute($q); 
		while(!$r->EOF){
			$op = new orden_pago;
			$op->id_sp = $r->fields['nrodoc'];
			$op->observacion = $r->fields['descripcion'];
			$op->nroref = $r->fields['nroref'];
			$coleccion[] = $op;
			//die(print_r($coleccion));
			$r->movenext();
			 
		}
		return $coleccion;
	}
	function getOrdenesPagoBy($conn,$status=-1, $id_proveedor='-1'){
		$q = "SELECT nrodoc,montodoc,montoret,montopagado,descripcion,id_banco,id_cuenta ";
		$q.= "FROM finanzas.orden_pago ";
		$q.= "WHERE (status = $status OR $status=-1) AND (id_proveedor=$id_proveedor OR $id_proveedor=-1) ";
		//die($q);
		$r = $conn->Execute($q); 
		while(!$r->EOF){
			if($r->fields['montodoc']-$r->fields['montoret']-$r->fields['montopagado']!=0){
				$op = new orden_pago;
				$op->nrodoc = $r->fields['nrodoc'];
				$op->montodoc = $r->fields['montodoc']-$r->fields['montoret']-$r->fields['montopagado'];
				$op->montopagado = $r->fields['montopagado'];
				$op->descripcion = $r->fields['descripcion'];
				$op->id_banco = $r->fields['id_banco'];
				$op->id_nro_cuenta = $r->fields['id_cuenta'];
				$coleccion[] = $op;
			}
			$r->movenext();
			 
		}
		return $coleccion;
	}
	
	function getCorrelativoRetenciones($conn, $nrodoc,$ano,$mes){
		
	$q = "SELECT A.codret, B.es_iva AS tipo, "; 
	$q.= "CASE B.es_iva "; 
	$q.= "WHEN 1 THEN to_char((SELECT COALESCE(MAX(substring(nrocorret, 9, 8))::int4,0)+1 FROM finanzas.relacion_retenciones_orden WHERE length(nrocorret)=16),'00000000')::varchar ELSE ";
	$q.= "to_char((SELECT COALESCE(MAX(substring(nrocorret, 9, 4))::int4,0)+1 FROM finanzas.relacion_retenciones_orden WHERE length(nrocorret)=12),'0000')::varchar END AS correlativo ";
 	$q.= "FROM finanzas.relacion_retenciones_orden A ";
	$q.= "INNER JOIN finanzas.retenciones_adiciones B ON (A.codret = B.id) "; 
	$q.= "WHERE A.nrodoc = '$nrodoc' ";
	//die($q);
	$r = $conn->Execute($q);
	while(!$r->EOF){
		   $correlativo = $ano."-".$mes."-".trim($r->fields['correlativo']);	
		   $sql = "UPDATE finanzas.relacion_retenciones_orden SET nrocorret = '".$correlativo."' ";
		   $sql.= "WHERE codret = '".$r->fields['codret']."' AND nrodoc = '$nrodoc'";
		   $row = $conn->Execute($sql);
		   $r->movenext();
		}
		
	$q2 = "SELECT A.id_retencion, to_char((SELECT COALESCE(MAX(substring(nrocorret, 9, 8))::int4,0)+1 FROM finanzas.facturas),'00000000')::varchar  AS correlativo ";
	$q2.= "FROM finanzas.facturas A ";
	$q2.= "INNER JOIN finanzas.retenciones_adiciones B ON (A.id_retencion = B.id) ";
	$q2.= "WHERE A.nrodoc = '$nrodoc'";
	
	$r2 = $conn->Execute($q2);
	while(!$r2->EOF){
		$correlativo = $ano."-".$mes."-".trim($r2->fields['correlativo']);
		$sql2 = "UPDATE finanzas.facturas SET nrocorret = '".$correlativo."' ";
		$sql2.= "WHERE nrodoc = '".$nrodoc."'";
		$r3 = $conn->Execute($sql2);
		$r2->movenext();
		}
	}
	
	function get_retenciones_causado($conn,$nrorefcomp){
		$q = "SELECT pr.* FROM rrhh.presupuesto_retenciones pr ";
			$q.= "WHERE nro_doc_compromiso = '$nrorefcomp'";
			//die($q);
			$r = $conn->Execute($q) or die($q);
			//die(guardafloat($r->fields['porcen']));
			while (!$r->EOF){
				
				$sp = new solicitud_pago;
				$sp->id = $r->fields['conc_cod'];
				$sp->monto = $r->fields['monto'];
				$coleccion[] = $sp;
				$r->movenext();
				
			}
			
			return $coleccion;
			//$this->relacionRetencionesNomCau = new Services_JSON();
			//$this->relacionRetencionesNomCau = is_array($coleccion) ? $this->relacionRetenciones->encode($coleccion) : false;
			
		}
		
	function set_retenciones_nomina($conn, $nrodoccomp, $nrodoccausado){
		$q = "UPDATE rrhh.presupuesto_retenciones SET nro_doc_causado = '$nrodoccausado' WHERE nro_doc_compromiso = '$nrodoccomp'";
		$r = $conn->Execute($q);
		if($r)
			return true;
		else
			return false;
	}
	
	function getOrdenesPagoByAnteriores($conn,$status=-1, $id_proveedor='-1', $ano){
		$q = "SELECT nrodoc,montodoc,montoret,montopagado,descripcion ";
		$q.= "FROM historico.orden_pago ";
		$q.= "WHERE status = 2  AND id_proveedor=$id_proveedor AND ano = '$ano' AND (montodoc - (montoret + montopagado)) > 0 ";
		$q.= "ORDER BY nrodoc";
		//die($q);
		$r = $conn->Execute($q); 
		while(!$r->EOF){
			if($r->fields['montodoc']-$r->fields['montoret']-$r->fields['montopagado']!=0){
				$op = new orden_pago;
				$op->nrodoc = $r->fields['nrodoc'];
				$op->montodoc = $r->fields['montodoc']-$r->fields['montoret']-$r->fields['montopagado'];
				$op->montopagado = $r->fields['montopagado'];
				$op->descripcion = $r->fields['descripcion'];
				$coleccion[] = $op;
			}
			$r->movenext();
			 
		}
		return $coleccion;
	}
	
}
?>
