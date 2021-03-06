<?
class solicitud_pago{

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
	/*var $id_ciudadano;
	var $ciudadano;
	var $dir_ciudadano;*/
	var $pago;
	var $fuente;
	var $motivo;

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

	function get($conn, $id, $escEnEje='1111'){
		
		$q = "SELECT sp.*, sp.descripcion AS motivo, sp.nroref AS nrorefe, sp.nrodoc AS nrodoco, ue.descripcion AS unidad_ejecutora, mp.*, ";
		$q.= "p.nombre AS proveedor, p.direccion AS dir_proveedor, p.rif AS rif_proveedor, sp.status AS status_sp, p.provee_contrat AS tipo_contribuyente, ";
		$q.= "c.nombre as ciudadano, c.direccion AS dir_ciudadano, ";
		$q.= "ri.tipo_contribuyente, ri.ingreso_periodo_fiscal ";
		$q.= "FROM finanzas.solicitud_pago sp ";
		$q.= "INNER JOIN puser.movimientos_presupuestarios mp ON (mp.nrodoc = sp.nroref) ";
		$q.= "LEFT JOIN puser.proveedores p ON (mp.id_proveedor = p.id) ";
		$q.= "LEFT JOIN puser.ciudadanos c ON (mp.id_ciudadano = c.id) ";
		$q.= "INNER JOIN puser.unidades_ejecutoras ue ON (mp.id_unidad_ejecutora = ue.id) ";
		$q.= "LEFT JOIN puser.retencion_iva ri ON (ri.id_proveedor = p.id) ";
		$q.= "WHERE sp.nrodoc='$id' AND (ue.id_escenario = '$escEnEje')   ";
		//die($q);
		$r = $conn->Execute($q);
		
		if(!$r->EOF){
			
	
			$this->nrodoc = $r->fields['nrodoco'];
			$this->descripcion = $r->fields['descripcion'];
			$td = new tipos_documentos;
			$td->get($conn,$r->fields['tipdoc']); 
			$this->tipdoc = $td;
			$this->nroref = $r->fields['nrorefe'];
			$this->id_condicion_pago = $r->fields['id_condicion_pago'];
			$this->id_unidad_ejecutora = $r->fields['id_unidad_ejecutora'];
			$this->unidad_ejecutora = $r->fields['unidad_ejecutora'];
			$this->id_usuario = $r->fields['id_usuario'];
			$this->fecha = $r->fields['fecha'];
			$this->tipo_contribuyente = $r->fields['tipo_contribuyente'];
			$this->ingreso_periodo_fiscal = $r->fields['ingreso_periodo_fiscal'];
			$this->status = $r->fields['status_sp'];
			$this->id_proveedor = $r->fields['id_proveedor'];
			$this->proveedor = $r->fields['proveedor'];
			$this->dir_proveedor = $r->fields['dir_proveedor'];
			$this->rif_proveedor = $r->fields['rif_proveedor'];
			$this->monto_si = $r->fields['monto_si'];
			$this->fuente = $r->fields['fuente_financiamiento'];
			$this->pago = $r->fields['pago'];
			$this->motivo = $r->fields['motivo'];
			//$this->getfacturas($conn, $r->fields['nrodoco']);
			//$this->getretenciones($conn, $r->fields['nrodoco']);
			return true;
		
		}else
			
			return false;
	}

	function get_all($conn, $escEnEje,$orden="id"){
		
		$q = "SELECT * FROM finanzas.solicitud_pago ";
		$q.= "ORDER BY $orden ";
		$r = $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new solicitud_pago;
			$ue->get($conn, $r->fields['id'], $escEnEje);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, 
						$tipdoc,
						$nroref,
						$fecha,
						$status,
						$aPartidas,
						$nrodoc='',
						$descripcion,
						$id_unidad_ejecutora,
						$id_proveedor,
						$pago){
		//die(print_r($aRetenciones));				
		//if(empty($nrodoc))
			$nrodoc = $this->getNroDoc($conn, '014');
		//else
			//$nrodoc = $this->addNrodoc($nrodoc, '014');
						
				
		$q = "INSERT INTO finanzas.solicitud_pago ";
		$q.= "( ";
		$q.= "nrodoc, ";
		$q.= "nroref, ";
		$q.= "fecha, ";
		$q.= "status, ";
		$q.= "descripcion, ";
		$q.= "id_unidad_ejecutora, ";
		$q.= "id_proveedor, ";
		$q.= "pago ";
		$q.= ") ";
		$q.= "VALUES ";
		$q.= "( ";
		$q.= " '$nrodoc', ";
		$q.= " '$nroref', ";
		$q.= " '$fecha', ";
		$q.= " '$status', ";
		$q.= " '$descripcion', ";
		$q.= " '$id_unidad_ejecutora', ";
		$q.= " '$id_proveedor', ";
		$q.= " ".guardaFloat($pago)." ";
		$q.= ") ";
		
		//die($q);	 
		$r = $conn->execute($q);
				if($r !== false){
				
						$this->addRelacionPartidas($conn, $nrodoc,$aPartidas,$nroref); 
									
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
						$aPartidas,
						$descripcion,
						$id_unidad_ejecutora,
						$id_proveedor,
						$pago
						){
		#Esto hay que regresarlo al valor cuando se trabaje sin imputacion, por ahora queda asi
		$q = "UPDATE finanzas.solicitud_pago SET  ";
		//$q.= "status = '$status', ";
		$q.= "nroref = '$nroref', ";
		$q.= "fecha = '$fecha', ";
		$q.= "descripcion = '$descripcion', ";
		$q.= "id_unidad_ejecutora = '$id_unidad_ejecutora', ";
		$q.= "id_proveedor = '$id_proveedor', ";
		$q.= "pago = ".guardaFloat($pago)." ";
		$q.= "WHERE nrodoc='$nrodoc' ";
		//die($q);	
		
		if($conn->Execute($q)){
			if($this->delRelacionPartidas($conn, $nrodoc)){
				
				$this->addRelacionPartidas($conn, $nrodoc,$aPartidas,$nroref);									
				
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
	
	function anular($conn, $nrodoc){
	
	try {
		$q3 ="UPDATE finanzas.solicitud_pago SET status=3 WHERE nrodoc='$nrodoc'";
		//die($q3);
		$r3 = $conn->Execute($q3) or die($q3);
		//die("status: ".$status);
		if($r3){
			$this->msj = SOLICITUD_ANULADA;
			return true;
		}else{
		
			$this->msj = SOLICITUD_NO_ANULADA;
			return false;
		}
		} catch( ADODB_Exception $e ){
			//die($conn->ErrorMsg());
				$this->msj = $conn->ErrorMsg();
				return false;

		}
	
		
	}
	
	function addRelacionPartidas($conn,$nrodoc,	$aPartidas, $nroref){
	
		$JsonRec = new Services_JSON();
		$JsonRec = $JsonRec->decode(str_replace("\\","",$aPartidas));
		
		if(is_array($JsonRec->partidaspresupuestarias)){
			foreach($JsonRec->partidaspresupuestarias as $partidas){
				$q = "INSERT INTO puser.relacion_solicitud_pago ";
				$q.= "( id_parcat, id_categoria_programatica, id_partida_presupuestaria, id_solicitud_pago, monto, nroref) ";
				$q.= "VALUES ";
				$q.= "('$partidas[3]', '$partidas[0]', '$partidas[1]', '$nrodoc', '".$partidas[2]."', '$nroref') ";
				//die($q);
				$r = $conn->Execute($q) or die($q);
			} 
		}

		if($r)
			return true;
		else
			return false;
	}

	function delRelacionPartidas($conn, $nrodoc){
		$q = "DELETE FROM puser.relacion_solicitud_pago WHERE id_solicitud_pago='$nrodoc'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	function getRelacionPartidas($conn, $id, $escEnEjec){
		$q = "SELECT relacion_solicitud_pago.*, partidas_presupuestarias.descripcion AS partida_presupuestaria,  ";
		$q.= "categorias_programaticas.descripcion AS categoria_programatica, puser.partidas_presupuestarias.ano ";
		$q.= "FROM puser.relacion_solicitud_pago  ";
		$q.= "INNER JOIN partidas_presupuestarias ON (relacion_solicitud_pago.id_partida_presupuestaria = partidas_presupuestarias.id) ";
		$q.= "INNER JOIN categorias_programaticas ON (relacion_solicitud_pago.id_categoria_programatica = categorias_programaticas.id) ";
		$q.= "Inner Join finanzas.solicitud_pago ON puser.relacion_solicitud_pago.id_solicitud_pago = finanzas.solicitud_pago.nrodoc ";
		$q.= "WHERE relacion_solicitud_pago.id_solicitud_pago='$id' ";
		$q.= "AND categorias_programaticas.id_escenario = '$escEnEjec' ";
		$q.= "AND partidas_presupuestarias.id_escenario = '$escEnEjec' ";
		//die($q);
		$r = $conn->Execute($q);
		while(!$r->EOF){
			$ue = new solicitud_pago;
			$ue->nrodoc = $r->fields['nrodoc'];
			$ue->nroref = $r->fields['nroref'];
			$ue->idParCat	= $r->fields['id_parcat'];
			$ue->id_partida_presupuestaria	= $r->fields['id_partida_presupuestaria'];
			$ue->id_categoria_programatica = $r->fields['id_categoria_programatica'];
			$ue->partida_presupuestaria	= $r->fields['partida_presupuestaria'];
			$ue->categoria_programatica = $r->fields['categoria_programatica'];
			$ue->monto = $r->fields['monto'];
			$cNrodocReferencia = movimientos_presupuestarios::getdocstatus2($conn, $r->fields['nroref'] ,1);
			//die(print_r($cNrodocReferencia));
			$montoReferencia = 0;
			
			if(is_array($cNrodocReferencia)){
	
				foreach($cNrodocReferencia as $nrodocRef){
					$montoReferencia += movimientos_presupuestarios::get_monto($conn,	
		 														$r->fields['nroref'], 
																$r->fields['id_categoria_programatica'],
																$r->fields['id_partida_presupuestaria']);
					
					$monto_transito = 0; //PARA DEJAR SOLO LA ULTIMA ENTRADA AL MONTO TOTAL DEL TRANSITO
					$monto_transito += solicitud_pago::get_monto($conn,	
		 														$r->fields['nroref'], 
																$r->fields['id_categoria_programatica'],
																$r->fields['id_partida_presupuestaria'],
																2);
					//die($montoReferencia);
				}
			}
			//$transito = solicitud_pago::getTransito($conn,$r->fields['nroref'],);
			//0echo $montoReferencia."<br>";
			//echo "monto ".$r->fields['monto']."<br>";
			$ue->comprometido += $montoReferencia;
			$ue->transito += $monto_transito;
			//$ue->transito += $r->fields['monto'];
			$ue->montoporcausar = $montoReferencia - $monto_transito;
						
			$coleccion[] = $ue;
			$r->movenext();
					
						
		}
		//die();
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
	
	function aprobar($conn, $nrodoc, 
										$descripcion,
										$nroref,
										$fechadoc,
										$aPartidas,
										$pago
										){
		
		$q = "UPDATE finanzas.solicitud_pago SET status = '2', fecha_aprobacion = '$fechadoc', descripcion = '$descripcion', pago = ".guardaFloat($pago)." WHERE nrodoc = '$nrodoc'";
		$r = $conn->Execute($q);
		if($r){
			if($this->delRelacionPartidas($conn, $nrodoc)){
				
				$this->addRelacionPartidas($conn, $nrodoc,$aPartidas,$nroref);		
					$this->msj = SOLICITUD_PAGO_APROBADA;
					return true;
			} else {
				$this->msj = ERROR_APROBADA;
				return false;
			}
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
	
	function setFechaAprobacion($conn, $id){
		$q = "UPDATE finanzas.solicitud_pago SET fecha_aprobacion = now(), status='2' WHERE nrodoc='$id'";
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	function buscar($conn, $id_proveedor, $id_ue, $fecha_desde, $fecha_hasta, $nrodoc, $descripcion, $id_escenario, $orden="nrodoc", $from=0, $max=0)
	{
		if(empty($id_proveedor) && empty($id_ue) && empty($fecha_desde) && empty($fecha_hasta) && empty($nrodoc) && empty($descripcion))
			return false;

		$q = "SELECT sp.nrodoc AS nrodoco FROM finanzas.solicitud_pago sp ";
		$q.= "INNER JOIN puser.movimientos_presupuestarios mp ON (mp.nrodoc = sp.nroref) ";
		$q.= "LEFT JOIN puser.proveedores p ON (mp.id_proveedor = p.id) ";
		$q.= "LEFT JOIN puser.ciudadanos ciu ON (mp.id_ciudadano = ciu.id) ";
		$q.= "INNER JOIN puser.unidades_ejecutoras ue ON (mp.id_unidad_ejecutora = ue.id) ";
		$q.= "LEFT JOIN puser.retencion_iva ri ON (ri.id_proveedor = p.id) ";
		$q.= "WHERE mp.status=1 AND ue.id_escenario='$id_escenario'";
		$q.= !empty($nrodoc) ? "AND sp.nrodoc='$nrodoc' ": "";
		$q.= !empty($fecha_desde) ? "AND sp.fecha >='".guardafecha($fecha_desde)."' ": "";
		$q.= !empty($fecha_hasta) ? "AND sp.fecha <='".guardafecha($fecha_hasta)."' ": "";
		$q.= !empty($id_proveedor) ? "AND mp.id_proveedor = '$id_proveedor'  ":"";
		$q.= !empty($id_ue) ? "AND mp.id_unidad_ejecutora = '$id_ue'  ":"";
		$q.= !empty($descripcion) ? "AND mp.descripcion ILIKE '%$descripcion%' ":"";
		$q.= "ORDER BY sp.$orden ";
			//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from):$conn->Execute($q);
		if(!$r || $r->EOF)
			return false;
			
		$collection=array();
		while(!$r->EOF){
			$ue = new solicitud_pago;
			$ue->get($conn, $r->fields['nrodoco']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		return $coleccion;
	}
	
	function totalRegsBusqueda($conn, $id_proveedor, $id_ue, $fecha_desde, $fecha_hasta, $nrodoc, $descripcion, $id_escenario, $orden="nrodoc")
	{
		if(empty($id_proveedor) && empty($id_ue) && empty($fecha_desde) && empty($fecha_hasta) && empty($nrodoc) && empty($descripcion))
				return 0;

		$q = "SELECT sp.*, sp.nroref AS nrorefe, sp.nrodoc AS nrodoco, ue.descripcion AS unidad_ejecutora, mp.*, ";
		$q.= "p.nombre AS proveedor, p.direccion AS dir_proveedor, p.id AS id_proveedor, ";
		$q.= "ri.tipo_contribuyente, ri.ingreso_periodo_fiscal ";
		$q.= "FROM finanzas.solicitud_pago sp ";
		$q.= "INNER JOIN puser.movimientos_presupuestarios mp ON (mp.nrodoc = sp.nroref) ";
		$q.= "LEFT JOIN puser.proveedores p ON (mp.id_proveedor = p.id) ";
		$q.= "LEFT JOIN puser.ciudadanos ciu ON (mp.id_ciudadano = ciu.id) ";
		$q.= "INNER JOIN puser.unidades_ejecutoras ue ON (mp.id_unidad_ejecutora = ue.id) ";
		$q.= "LEFT JOIN puser.retencion_iva ri ON (ri.id_proveedor = p.id) ";
		$q.= "WHERE  1=1  AND mp.status=1 AND ue.id_escenario='$id_escenario'";
		$q.= !empty($nrodoc) ? "AND sp.nrodoc='$nrodoc' ": "";
		$q.= !empty($fecha_desde) ? "AND sp.fecha >='".guardafecha($fecha_desde)."' ": "";
		$q.= !empty($fecha_hasta) ? "AND sp.fecha <='".guardafecha($fecha_hasta)."' ": "";
		$q.= !empty($id_proveedor) ? "AND mp.id_proveedor = '$id_proveedor'  ":"";
		$q.= !empty($id_ue) ? "AND mp.id_unidad_ejecutora = '$id_ue'  ":"";
		$q.= !empty($descripcion) ? "AND mp.descripcion ILIKE '%$descripcion%' ":"";
		$q.= "ORDER BY sp.$orden ";
			//die($q);
		$r = $conn->Execute($q);
		
		return $r->RecordCount();
	}
	
	function getNroDoc($conn, $tipdoc){
		$q = "SELECT max(nrodoc) AS nrodoc FROM finanzas.solicitud_pago ";
		$r = $conn->execute($q);
		//die($r->fields['nrodoc']);
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
						monto_excento, 
						monto_iva, 
						iva_retenido,
						iva) ";
				$q .= "VALUES ";
				$q .= "('$nrodoc', 
						'$facturas[0]',
						'$facturas[1]', 
						'$fecha2', 
						$facturas[4], 
						$facturas[5], 
						$facturas[6], 
						$facturas[7], 
						$facturas[8],
						$facturas[3])";
						
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
					
				$q = "INSERT INTO finanzas.relacion_retenciones_solicitud 
					(nrodoc, codret, mntret, mntbas, porcen, anio, porc_ret) 
				 		VALUES 
				 	('$nrodoc', '$retenciones[0]', '".$retenciones[4]."', ".guardaFloat($retenciones[2]).", $retenciones[1], '$anio', $retenciones[3])";
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
		
			$q = "SELECT * FROM finanzas.relacion_retenciones_solicitud WHERE nrodoc = '$nrodoc'";
			
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
				$coleccion[] = $sp;
				$r->movenext();
				
			}
			
			$this->relacionRetenciones = new Services_JSON();
			$this->relacionRetenciones = is_array($coleccion) ? $this->relacionRetenciones->encode($coleccion) : false;
		}
			
	}
	
	function delretenciones($conn, $nrodoc){
		$q = "DELETE FROM finanzas.relacion_retenciones_solicitud WHERE nrodoc='$nrodoc'";
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
	
	function get_relaciones($conn, $id, $escEnEjec, $status=''){
		$q = "SELECT relacion_movimientos.*, partidas_presupuestarias.descripcion AS partida_presupuestaria,  ";
		$q.= "categorias_programaticas.descripcion AS categoria_programatica ";
		$q.= "FROM puser.relacion_movimientos  ";
		$q.= "INNER JOIN puser.partidas_presupuestarias ON (relacion_movimientos.id_partida_presupuestaria = partidas_presupuestarias.id) ";
		$q.= "INNER JOIN puser.categorias_programaticas ON (relacion_movimientos.id_categoria_programatica = categorias_programaticas.id) ";
		$q.= "WHERE relacion_movimientos.nrodoc='$id' ";
		$q.= "AND categorias_programaticas.id_escenario = '$escEnEjec' ";
		$q.= "AND partidas_presupuestarias.id_escenario = '$escEnEjec' ";
		//die($q);
		//die($q);
		$r = $conn->Execute($q);
		$aIdP = array();
		while(!$r->EOF){
			$ue = new movimientos_presupuestarios;
			$ue->nrodoc = $r->fields['nrodoc'];
			$ue->nroref = $r->fields['nroref'];
			$ue->idParCat	= $r->fields['id_parcat'];
			$ue->id_partida_presupuestaria	= $r->fields['id_partida_presupuestaria'];
			$ue->id_categoria_programatica = $r->fields['id_categoria_programatica'];
			$ue->partida_presupuestaria	= $r->fields['partida_presupuestaria'];
			$ue->categoria_programatica = $r->fields['categoria_programatica'];
			$ue->monto = $r->fields['monto'];
			//$ue->causado_ant = $ue->get_causado($conn,$id,$r->fields['id_parcat']);
			$ue->get_suma_monto($conn, $id);
			
			$sql = "SELECT * FROM puser.relacion_solicitud_pago ";
			$sql.= "INNER JOIN finanzas.solicitud_pago ON relacion_solicitud_pago.id_solicitud_pago = solicitud_pago.nrodoc ";
			$sql.= "WHERE relacion_solicitud_pago.nroref = '$id' AND solicitud_pago.status <> '3'";
			
			//die($sql);
			$row = $conn->Execute($sql);
			
			#OBTENGO EL TOTAL EN TRANSITO POR RELACION_PP_CP
			$sp = new solicitud_pago;
			
			$montoReferencia = 0;
			while(!$row->EOF){
	
					if($row->fields['id_partida_presupuestaria'] == $r->fields['id_partida_presupuestaria']){ 
						$indice = array_search($row->fields['id_partida_presupuestaria'],$aIdP);
						if ($indice===false){
							$aIdP[] = $row->fields['id_partida_presupuestaria'];
							$montoReferencia += $sp->get_monto($conn,	
		 														$id, 
																$row->fields['id_categoria_programatica'],
																$row->fields['id_partida_presupuestaria']);
						}
					}
					$row->movenext();
			}
			
			//die("aqui ".$montoReferencia);
			$ue->compromiso = $ue->monto_total_documento($conn, $id);
			$sp = new solicitud_pago;
			$ue->transito 	= $sp->monto_total_documento($conn, $id);
			$ue->comprometido += $r->fields['monto'];
			//$ue->transito += $montoReferencia;
			
			$ue->montoporcausar = $r->fields['monto'] - $montoReferencia;
						
			$coleccion[] = $ue;
			$r->movenext();
		}
		
		return $coleccion;
	}
	
	function monto_total_documento($conn, $nrodoc){
	
		$q = "SELECT sum(monto) as total_doc FROM puser.relacion_solicitud_pago ";
		$q.= "INNER JOIN finanzas.solicitud_pago ON relacion_solicitud_pago.id_solicitud_pago = solicitud_pago.nrodoc ";
		$q.= "WHERE relacion_solicitud_pago.nroref='$nrodoc' AND solicitud_pago.status <> '3'";
		//die($q);
		$r = $conn->execute($q);
		return $r->fields['total_doc'];
	
	}
	
	function get_monto($conn, $nrodoc, $id_categoria, $id_partida, $status=''){
		$q = "SELECT sum(monto) AS monto FROM puser.relacion_solicitud_pago ";
		$q.= "INNER JOIN finanzas.solicitud_pago ON relacion_solicitud_pago.id_solicitud_pago = solicitud_pago.nrodoc ";
		$q.= "WHERE relacion_solicitud_pago.nroref='$nrodoc' ";
		$q.= "AND id_categoria_programatica = '$id_categoria' AND id_partida_presupuestaria = '$id_partida' ";
		$q.= !empty($status) ? "AND solicitud_pago.status = '$status'" : "AND solicitud_pago.status <> '3' ";
		
		//die($q);
		//echo $q."<br>";
		$r = $conn->Execute($q);
		if($r)
			return $r->fields['monto'];
		else
			return false;
	}
	
	function getRelacionPar_Sol($conn, $id, $escEnEjec){
		$q = "SELECT relacion_solicitud_pago.*, partidas_presupuestarias.descripcion AS partida_presupuestaria,  ";
		$q.= "categorias_programaticas.descripcion AS categoria_programatica, puser.partidas_presupuestarias.ano ";
		$q.= "FROM puser.relacion_solicitud_pago  ";
		$q.= "INNER JOIN partidas_presupuestarias ON (relacion_solicitud_pago.id_partida_presupuestaria = partidas_presupuestarias.id) ";
		$q.= "INNER JOIN categorias_programaticas ON (relacion_solicitud_pago.id_categoria_programatica = categorias_programaticas.id) ";
		$q.= "Inner Join finanzas.solicitud_pago ON puser.relacion_solicitud_pago.id_solicitud_pago = finanzas.solicitud_pago.nrodoc ";
		$q.= "WHERE relacion_solicitud_pago.id_solicitud_pago='$id' ";
		$q.= "AND categorias_programaticas.id_escenario = '$escEnEjec' ";
		$q.= "AND partidas_presupuestarias.id_escenario = '$escEnEjec' ";
		//die($q);
		$r = $conn->Execute($q);
		while(!$r->EOF){
			$ue = new solicitud_pago;
			$ue->nrodoc = $r->fields['nrodoc'];
			$ue->nroref = $r->fields['nroref'];
			$ue->idParCat	= $r->fields['id_parcat'];
			$ue->id_partida_presupuestaria	= $r->fields['id_partida_presupuestaria'];
			$ue->id_categoria_programatica = $r->fields['id_categoria_programatica'];
			$ue->partida_presupuestaria	= $r->fields['partida_presupuestaria'];
			$ue->categoria_programatica = $r->fields['categoria_programatica'];
			$ue->monto = $r->fields['monto'];
			$r->movenext();
			$coleccion[] = $ue;
		}
		return $coleccion;
	}
	
	function getCompromisosXDoc($conn, $nroref){
		$op = new orden_pago;
			$op->totTransito = solicitud_pago::getTransito($conn,$nroref);
			$op->totCompromiso = solicitud_pago::getCompromiso($conn,$nroref);
			$op->totCausado = solicitud_pago::getCausado($conn,$nroref);
			//$op->totAportesNomina = solicitud_pago::get_suma_aportes_sp($conn, $nroref);	
		$coleccion[] = $op;
		return $coleccion;
		
	}
	
	function getTransito($conn, $nrodoc){
		$q = "SELECT SUM(monto) AS monto_transito FROM puser.relacion_solicitud_pago ";
		$q.= "INNER JOIN finanzas.solicitud_pago ON relacion_solicitud_pago.id_solicitud_pago = solicitud_pago.nrodoc ";
		$q.= "WHERE relacion_solicitud_pago.nroref='$nrodoc' AND solicitud_pago.status <> '3'";
		//die($q);
		$r= $conn->Execute($q);
		if($r){
			$monto_transito = $r->fields['monto_transito'];
			return $monto_transito;
		}
	}
	
	function getCompromiso($conn, $nrodoc){
		$q = "SELECT SUM(monto) AS monto_compromiso FROM puser.relacion_movimientos ";
		$q.= "WHERE relacion_movimientos.nrodoc='$nrodoc'";
		if($r = $conn->Execute($q)){
			$monto_compromiso = $r->fields['monto_compromiso'];
			return $monto_compromiso;
		}
	}
	
	function getCausado($conn, $nroref){
		$total_causado = 0;
		$q = "SELECT nrodoc AS nrodoc_solicitud FROM finanzas.solicitud_pago ";
		$q.= "WHERE nroref = '$nroref'";
		//echo $q."<br>";
		$r = $conn->Execute($q);
		while(!$r->EOF){
			$sql = "SELECT nrodoc AS nrodoc_orden FROM finanzas.orden_pago ";
			$sql.= "WHERE nroref = '".$r->fields['nrodoc_solicitud']."' AND orden_pago.status = '2' ";
			//echo $sql."<br>";
			$row = $conn->Execute($sql);
			while(!$row->EOF){
				$q2 = "SELECT SUM(monto) AS monto_causado FROM puser.relacion_movimientos ";
				$q2.= "INNER JOIN puser.movimientos_presupuestarios ON (relacion_movimientos.nrodoc = movimientos_presupuestarios.nrodoc) ";
				$q2.= "WHERE movimientos_presupuestarios.nrodoc = '".$row->fields['nrodoc_orden']."'";
				//echo $q2."<br>";
				$r2 = $conn->Execute($q2);
				if($r2){
					$total_causado+= $r2->fields['monto_causado'];
				}
				$row->movenext();
			}
			$r->movenext();
		}
		return $total_causado;
	}
	
	function get_suma_aportes_sp($conn, $nrodocref){
		$q = "SELECT   SUM(a.monto) as monto, a.id_partida_presupuestaria AS partida
			  FROM puser.relacion_movimientos as a 
			  INNER JOIN rrhh.conc_part as cp ON ( a.id_partida_presupuestaria = cp.par_cod and a.id_categoria_programatica = cp.cat_cod )
			  INNER JOIN rrhh.concepto as c ON (c.int_cod = cp.conc_cod)
			  WHERE a.nrodoc = '$nrodocref' AND COALESCE(LENGTH(c.conc_aporte), 0) > 0
			  GROUP BY a.id_partida_presupuestaria, c.conc_cod";
		
		/*$q = "SELECT   SUM(a.monto) as monto FROM puser.relacion_movimientos as a 
			  INNER JOIN rrhh.conc_part as cp ON ( a.id_partida_presupuestaria = cp.par_cod and a.id_categoria_programatica = cp.cat_cod )
			  INNER JOIN rrhh.concepto as c ON (c.int_cod = cp.conc_cod)
			  WHERE a.nrodoc = '$nrodocref' AND COALESCE(LENGTH(c.conc_aporte), 0) > 0 ";*/
		//die($q);
		$r = $conn->Execute($q);
		$aporte = array();
		$montoAportes = 0; 
		if($r){
			while(!$r->EOF){
				$indice = array_search($r->fields['partida'], $aporte);
				if($indice !== false){
					echo '';
				}else{
					$aporte[] = $r->fields['partida']; 
					$montoAportes = $montoAportes + $r->fields['monto']; 
				}
				print_r($aporte);
				echo "<br>";
				$r->movenext;
			}
			die();
			return $montoAportes;			
		}else
			$montoAportes = 0;
		return $montoAportes;  
	}

}
?>
