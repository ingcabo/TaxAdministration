<?
class orden_servicio_trabajo{

	// Propiedades

	var $id;
	var $descripcion;
	var $id_tipo_documento;
	var $id_unidad_ejecutora;
	var $fecha;
	var $fecha_entrega;
	var $fecha_aprobacion;
	var $lugar_entrega;
	var $condicion_pago;
	var $condicion_operacion;
	var $rif;
	var $id_proveedor;
	var $proveedor;
	var $dir_proveedor;
	var $observaciones;
	var $nro_requisicion;
	var $nro_cotizacion;
	var $nro_factura;
	var $fecha_factura;
	var $cod_contraloria;
	var $id_ciudadano;
	var $ciudadano;
	var $dir_ciudadano;
	var $tlf_ciudadano;
	var $nrodoc;
	var $msj; // para enviar un mensaje de error al usuario
	var $total;
	var $status;
	
	/*****************************
			Objeto Relacion Partidas
	*****************************/
	var $relacion; // almacena un array de objetos de relaciones de partidas
	
	// Propiedades utilizadas por el objeto con relaciones de partidas
	var $idParCat;
	var $id_categoria_programatica;
	var $id_partida_presupuestaria;
	var $categoria_programatica;
	var $partida_presupuestaria;
	var $monto;

	/********************************
			Objeto Relacion Productos
	********************************/
	var $relacionProductos; // almacena un array de objetos de relaciones de partidas
	
	// Propiedades utilizadas por el objeto con relaciones de partidas
	var $id_producto;
	var $producto;
	var $unidad_medida;
	var $cantidad;
	var $precio_base;
	var $precio_iva;
	var $precio_total;

	function get($conn, $id, $escEnEje=''){
		
		$q = "SELECT orden_servicio_trabajo.*, unidades_ejecutoras.descripcion AS unidad_ejecutora, proveedores.id AS id_proveedor, proveedores.nombre AS proveedor, proveedores.direccion AS dir_proveedor, ";
		$q.= "proveedores.rif AS rif_proveedor FROM puser.orden_servicio_trabajo ";
		$q.= "INNER JOIN puser.unidades_ejecutoras ON (orden_servicio_trabajo.id_unidad_ejecutora = unidades_ejecutoras.id) ";
		$q.= "LEFT JOIN puser.proveedores ON (orden_servicio_trabajo.id_proveedor = proveedores.id) ";
		$q.= "WHERE orden_servicio_trabajo.id='$id'  ";
		//$q.= "WHERE orden_servicio_trabajo.id='$id' AND unidades_ejecutoras.id_escenario = '$escEnEje' ";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->id_tipo_documento = $r->fields['id_tipo_documento'];
			$this->status = $r->fields['status'];
			$this->id_unidad_ejecutora = $r->fields['id_unidad_ejecutora'];
			$this->unidad_ejecutora = $r->fields['unidad_ejecutora'];
			$this->fecha = $r->fields['fecha'];
			$this->fecha_entrega = $r->fields['fecha_entrega'];
			$this->fecha_aprobacion = $r->fields['fecha_aprobacion'];
			$this->lugar_entrega = $r->fields['lugar_entrega'];
			$this->condicion_pago = $r->fields['condicion_pago'];
			$this->condicion_operacion = $r->fields['condicion_operacion'];
			$this->rif = $r->fields['rif_proveedor'];
			$this->id_proveedor = $r->fields['id_proveedor'];
			$this->dir_proveedor = $r->fields['dir_proveedor'];
			$this->proveedor = $r->fields['proveedor'];
			$this->observaciones = $r->fields['observaciones'];
			$this->nro_requisicion = $r->fields['nro_requisicion'];
			$this->nro_cotizacion = $r->fields['nro_cotizacion'];
			$this->nro_factura = $r->fields['nro_factura'];
			$this->fecha_factura = $r->fields['fecha_factura'];
			$this->cod_contraloria = $r->fields['cod_contraloria'];
			$this->id_ciudadano = $r->fields['id_ciudadano'];
			$this->ciudadano = $r->fields['ciudadano'];
			$this->dir_ciudadano = $r->fields['dir_ciudadano'];
			$this->tlf_ciudadano = $r->fields['tlf_ciudadano'];
			/*if(!$auxNrodoc)
				$this->nrodoc = $this->showNrodoc($r->fields['nrodoc']);
			else*/
			$this->nrodoc = $r->fields['nrodoc'];
			$this->getRelacionProductos($conn,$r->fields['id']);
			return true;
		}else
			return false;
	}

	function get_all($conn, $escEnEje, $orden="id"){
		$q = "SELECT * FROM orden_servicio_trabajo ";
		$q.= "ORDER BY $orden ";
		$r = $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new orden_servicio_trabajo;
			$ue->get($conn, $r->fields['id'], $escEnEje);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, 
						$id_tipo_documento, 
						$id_unidad_ejecutora, 
						$fecha, 
						$fecha_entrega, 
						$lugar_entrega, 
						$condicion_pago, 
						$condicion_operacion, 
						$id_proveedor, 
						$observaciones, 
						$nro_requisicion, 
						$nro_cotizacion, 
						$nro_factura, 
						$fecha_factura, 
						$cod_contraloria,
						$id_usuario,
						$aPartidas,
						$aProductos,
						$nrodoc='',
						$auxNroDoc){
		//$nrodoc = $this->addNrodoc($nrodoc, $id_tipo_documento, $id_unidad_ejecutora);
		$id_ciudadano = '';
		$q = "INSERT INTO orden_servicio_trabajo ";
		$q.= "( ";
		$q.= "id_tipo_documento, ";
		$q.= "id_unidad_ejecutora, ";
		$q.= "fecha, ";
		$q.= "fecha_entrega, ";
		$q.= "lugar_entrega, ";
		$q.= "condicion_pago, ";
		$q.= "condicion_operacion, ";
		$q.= "id_proveedor, ";
		$q.= "observaciones, ";
		$q.= "nro_requisicion, ";
		$q.= "nro_cotizacion, ";
		$q.= "nro_factura, ";
		$q.= "fecha_factura, ";
		$q.= "cod_contraloria, ";
		$q.= "id_usuario, ";
		$q.= "id_ciudadano, ";
		$q.= "status, ";
		$q.= "nrodoc ";
		$q.= ") ";
		$q.= "VALUES ";
		$q.= "( ";
		$q.= " '$id_tipo_documento', ";
		$q.= " '$id_unidad_ejecutora', ";
		$q.= " '$fecha', ";
		$q.= " '$fecha_entrega', ";
		$q.= " '$lugar_entrega', ";
		$q.= " '$condicion_pago', ";
		$q.= " '$condicion_operacion', ";
		$q.= " '$id_proveedor', ";
		$q.= " '$observaciones', ";
		$q.= " '$nro_requisicion', ";
		$q.= " '$nro_cotizacion', ";
		$q.= " '$nro_factura', ";
		$q.= " '$fecha_factura', ";
		$q.= " '$cod_contraloria', ";
		$q.= " '$id_usuario', ";
		$q.= " '$id_ciudadano', ";
		$q.= " 1, ";
		$q.= " '$nrodoc' ";
		$q.= ") ";
		//die($q);
		//die(print_r($aPartidas."-".$aProductos));
		$r = $conn->execute($q) or die($q);
		if($r){
			
			$nrodoc = getLastId($conn, 'id', 'orden_servicio_trabajo');
			
			// Se elimino del documento los campos para seleccionar ciudadanos ya que ellos se encuentran entre los proveedores
			/*if (!empty($id_ciudadano)){
				
				$oCiudadano = new ciudadanos;
				$oCiudadano->get($conn,$id_ciudadano);
				if(empty($oCiudadano->nombre))
					ciudadanos::add($conn, $id_ciudadano, $ciudadano, $dir_ciudadano, $tlf_ciudadano);
			  }*/
		}
		if($this->addRelacionPartidas($conn, $nrodoc,$aPartidas) &&	$this->addRelacionProductos($conn, $nrodoc,$aProductos)){
					$this->msj = REG_ADD_OK;
					return true;	
		}else{
			$this->msj = ERROR_ADD;
			return false;
		}
	}

	function set($conn, 
							$id, 
							$id_tipo_documento, 
							$id_unidad_ejecutora, 
							$fecha, 
							$fecha_entrega, 
							$lugar_entrega, 
							$condicion_pago, 
							$condicion_operacion, 
							$id_proveedor, 
							$observaciones, 
							$nro_requisicion, 
							$nro_cotizacion, 
							$nro_factura, 
							$fecha_factura, 
							$cod_contraloria, 
							$aPartidas,
							$aProductos,
							$nrodoc='',
							$auxNroDoc){
		//die('p: '.$id_proveedor);
		//$nrodoc = $this->addNrodoc2($nrodoc, $id_tipo_documento);
		$id_ciudadano = '';
		$q = "UPDATE orden_servicio_trabajo SET  ";
		$q.= "id_tipo_documento = '$id_tipo_documento', ";
		$q.= "id_unidad_ejecutora = '$id_unidad_ejecutora', ";
		$q.= "fecha = '$fecha', ";
		$q.= "fecha_entrega = '$fecha_entrega', ";
		$q.= "lugar_entrega = '$lugar_entrega', ";
		$q.= "condicion_pago = '$condicion_pago', ";
		$q.= "condicion_operacion = '$condicion_operacion', ";
		$q.= "id_proveedor = '$id_proveedor', ";
		$q.= "observaciones = '$observaciones', ";
		$q.= "nro_requisicion = '$nro_requisicion', ";
		$q.= "nro_cotizacion = '$nro_cotizacion', ";
		$q.= "nro_factura = '$nro_factura', ";
		$q.= "fecha_factura = '$fecha_factura', ";
		$q.= "cod_contraloria = '$cod_contraloria', ";
		$q.= "id_ciudadano = '$id_ciudadano', ";
		$q.= "nrodoc = '$nrodoc' "; // aqui se actualiza el Nº de documento revisar si se puede hacer
		$q.= "WHERE id='$id' ";	
		//die($q);
		if($conn->Execute($q)){
			/*if (!empty($id_ciudadano)){
				$oCiudadano = new ciudadanos;
				$oCiudadano->get($conn,$id_ciudadano);
				if(empty($oCiudadano->nombre))
					ciudadanos::add($conn, $id_ciudadano, $ciudadano, $dir_ciudadano, $tlf_ciudadano);
			}*/	
			if($this->delRelacionPartidas($conn, $id) && $this->delRelacionProductos($conn, $id)){
				if($this->addRelacionPartidas($conn, $id,$aPartidas) &&	$this->addRelacionProductos($conn, $id,$aProductos)){
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
		}else{
			$this->msj = ERROR_SET;	
			return false;
		}
	}

	function del($conn, $id){
		$q = "DELETE FROM orden_servicio_trabajo WHERE id='$id' ";
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	function addRelacionPartidas($conn, $nrodoc, $aPartidas){
	
		$JsonRec = new Services_JSON();
		$JsonRec = $JsonRec->decode(str_replace("\\","",$aPartidas));
	
		if(is_array($JsonRec->partidaspresupuestarias)){
			
			foreach($JsonRec->partidaspresupuestarias as $partidas){
				$q = "INSERT INTO relacion_ord_serv_trab ";
				$q.= "( id_parcat, id_categoria_programatica, id_partida_presupuestaria, id_ord_serv_trab, monto, porc_iva,monto_exc) ";
				$q.= "VALUES ";
				$q.= "('$partidas[7]', '$partidas[0]', '$partidas[1]','$nrodoc', ".$partidas[2].",$partidas[4],".$partidas[3].") ";
				//die($q);
				$r = $conn->Execute($q);
			}
		} 
		if($r)
			return true;
		else
			return false;
	}

	function addRelacionProductos($conn, $idOrdServTrab,$aProducto){
		$JsonRec = new Services_JSON();
		$JsonRec = $JsonRec->decode(str_replace("\\","",$aProducto));
		if(is_array($JsonRec->productos)){
			foreach($JsonRec->productos as $productos){
				$q = "INSERT INTO  relacion_ord_serv_trab_productos ";
				$q.= "( id_ord_serv_trab, descripcion, precio_base, precio_iva, precio_total) ";
				$q.= "VALUES ";
				$q.= "('$idOrdServTrab', '$productos[0]', $productos[1], $productos[3], $productos[4]) ";
				$r = $conn->Execute($q);
			}
		} 
		if($r)
			return true;
		else
			return false;
	}

	function delRelacionPartidas($conn, $nrodoc){
		$q = "DELETE FROM relacion_ord_serv_trab WHERE id_ord_serv_trab='$nrodoc' ";
		//echo($q."<br>");
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	function delRelacionProductos($conn, $nrodoc){
		$q = "DELETE FROM relacion_ord_serv_trab_productos WHERE id_ord_serv_trab='$nrodoc'";
		//echo($q."<br>");
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	function getRelacionPartidas($conn, $id, $escEnEjec){
		$q = "SELECT relacion_ord_serv_trab.*, partidas_presupuestarias.descripcion AS partida_presupuestaria,  ";
		$q.= "categorias_programaticas.descripcion AS categoria_programatica ";
		$q.= "FROM relacion_ord_serv_trab  ";
		$q.= "INNER JOIN partidas_presupuestarias ON (relacion_ord_serv_trab.id_partida_presupuestaria = partidas_presupuestarias.id) ";
		$q.= "INNER JOIN categorias_programaticas ON (relacion_ord_serv_trab.id_categoria_programatica = categorias_programaticas.id) ";
		$q.= "WHERE relacion_ord_serv_trab.id_ord_serv_trab='$id' ";
		$q.= "AND categorias_programaticas.id_escenario = '$escEnEjec' ";
		$q.= "AND partidas_presupuestarias.id_escenario = '$escEnEjec' ";
		//die($q);
		$r = $conn->Execute($q);
		while(!$r->EOF){
			$ue = new orden_servicio_trabajo;
			$ue->nrodoc = $r->fields['nrodoc'];
			$ue->idParCat	= $r->fields['id_parcat'];
			$ue->id_partida_presupuestaria	= $r->fields['id_partida_presupuestaria'];
			$ue->id_categoria_programatica = $r->fields['id_categoria_programatica'];
			$ue->partida_presupuestaria	= $r->fields['partida_presupuestaria'];
			$ue->categoria_programatica = $r->fields['categoria_programatica'];
			$ue->monto = $r->fields['monto'];
			$ue->porc_iva = $r->fields['porc_iva'];
			$ue->monto_exc = $r->fields['monto_exc'];
			$coleccion[] = $ue;
			$r->movenext();
		}
		return $coleccion;
	}

	function getRelacionProductos($conn, $id){
		$q = "SELECT relacion_ord_serv_trab_productos.*  ";
		$q.= "FROM relacion_ord_serv_trab_productos  ";
		$q.= "WHERE relacion_ord_serv_trab_productos.id_ord_serv_trab='$id' ";
		//die($q);
		$r = $conn->Execute($q);
		$i = 0;
		while(!$r->EOF){
			$porc_iva = ($r->fields['precio_iva'] * 100) / $r->fields['precio_base'];
			$coleccion1[$i][0]	= 	$r->fields['descripcion'];
			$coleccion1[$i][1]	= 	$r->fields['precio_base'];
			$coleccion1[$i][2]	= 	$r->fields['precio_iva'];
			$coleccion1[$i][3] 	= 	$r->fields['precio_total'];
			$coleccion1[$i][4] 	= 	$porc_iva;
			$i++;
			$subtotal += $r->fields['precio_base'];
			$total_iva += $r->fields['precio_iva'];
			$r->movenext();
		}
		$total_general = $subtotal + $total_iva;
		$this->subtotal = $subtotal;
		$this->total_iva = $total_iva;
		$this->total_general = $total_general;
		
		$this->relacionProductos = new Services_JSON();
		$this->relacionProductos = is_array($coleccion1) ? $this->relacionProductos->encode($coleccion1) : false;
		
	}

	
	#FUNCION QUE PERMITE APROBAR LA ORDEN Y REGISTRALO EN MOVIMIENTOS PRESUPUESTARIOS#
	function aprobar($conn, $id_orden,  
										$id_usuario,
										$id_unidad_ejecutora,
										$ano,
										$observ,
										$nrodoc,
										$tipdoc,
										$fechadoc,
										$fechaent,
										$status,
										$id_proveedor='',
										$id_ciudadano,
										$aCategoriaProgramatica,
										$aPartidas,
										$nrodoc='',
										$auxNroDoc,
										$aProducto,
										$escEnEje){

		$rif = ($id_proveedor=='')? $id_ciudadano : $id_proveedor;
		
		#OBTENGO EL NRO DE DOCUMENTOS#
		// Mientras se cargue el numero de documento de manera manual esto debe estar asi
		if(empty($nrodoc))
			$nrodoc = movimientos_presupuestarios::getNroDoc($conn, $tipdoc);
		else
			$nrodoc = $this->addNrodoc2($nrodoc, $tipdoc);
		
		#DECODIFICO EL JSON DE LOS PRODUCTOS#

		$JsonRec = new Services_JSON();
		$JsonRec = $JsonRec->decode(str_replace("\\","",$aProducto));
		if(is_array($JsonRec->productos)){
			foreach($JsonRec->productos as $productos){
				//die("aqui ".$productos[3]);
				
			} 
		}
		
		#DECODIFICO EL JSON DE LAS PARTIDAS PRESUPUESTARIAS#
		$JsonRec = new Services_JSON();
		$JsonRec = $JsonRec->decode(str_replace("\\","",$aPartidas));
		$contador = sizeof($JsonRec->partidaspresupuestarias);
		
		
		#CHEQUEO LA DISPONIBILIDAD DE LAS PARTIDAS, EN EL CASO DE QUE EL MONTO NO ESTA DISPONIBLE NO SE APRUEBA ESA ORDEN#
		for($i = 0; $i < $contador; $i++){
			$q = "SELECT relacion_pp_cp.disponible FROM relacion_pp_cp WHERE id = '".$JsonRec->partidaspresupuestarias[$i][7]."' ";
			
			$r = $conn->Execute($q);
			if($r){
				if($r->fields['disponible'] < $JsonRec->partidaspresupuestarias[$i][2]){
					die($r->fields['disponible'].' : '.guardafloat($JsonRec->partidaspresupuestarias[$i][2]));
					$this->msj = ERROR_SOLICITUD_PAGO_APR_NO_DISP;
					return false;
				}
			}
		} 
		
		#REGISTRO LA ORDEN DE COMPRAS EN MOVIMIENTOS PRESUPUESTARIOS#	
		$q = "INSERT INTO puser.movimientos_presupuestarios ";
		$q.= "(id_usuario, id_unidad_ejecutora, ano, descripcion, nrodoc, tipdoc, tipref, ";//nroref, 
		$q.= "fechadoc, fecharef, status, id_proveedor) ";
		$q.= "VALUES ";
		$q.= "('$id_usuario', '$id_unidad_ejecutora', '$ano', '$observ', '$nrodoc', '$tipdoc', '0', ";//'$nroref', 
		$q.= " '$fechadoc', '$fechaent', '1', '$rif') ";
		
		$r = $conn->Execute($q) or die($q);
		
		#ESTE FOREACH SE HACE PARA PASAR LAS VARIABLES A LA FUNCION ADD_RELACION DE LA CLASE MOVIMIENTOS PRESUPUESTARIOS#
		$iva = 0;
		$idCat = array();
		foreach($JsonRec->partidaspresupuestarias as $partidas){
			
			$aIdParCat[] 				= $partidas[7];
			$aCategoriaProgramatica[] 	= $partidas[0];
			$aPartidaPresupuestaria[] 	= $partidas[1];
			$aMonto[] 					= muestrafloat($partidas[2]);
			
			$indice = array_search($partidas[0],$idCat);
			if ($indice!==false){
				$aIva[$indice] = $aIva[$indice] + $partidas[5];
			} else {
				$idCat[] = $partidas[0];
				$aIva[] = $partidas[5];
			}
				
		}
		
		#ESTO SE HACE PARA AÑADIR LA PARTIDA DEL IVA A LA IMPUTACION PRESUPUESTARIA
		if(count($aIva) > 0){
			for($i=0;$i<count($aIva);$i++){
				$q = "SELECT relacion_pp_cp.id AS idparcat FROM puser.relacion_pp_cp WHERE id_categoria_programatica = '$idCat[$i]' ";
				$q.= "AND id_partida_presupuestaria = '4031801000000' ";
				$q.= "AND id_escenario = '$escEnEje'";
				$row = $conn->Execute($q);
				$idpc = $row->fields['idparcat'];
				//die("aqui: ".$idpc);
				if($aIva[$i]>0){
					$aIdParCat[] 			= $idpc;        //$partidas[3];
					$aCategoriaProgramatica[] 	= $idCat[$i];
					$aPartidaPresupuestaria[] 	= '4031801000000';
					$aMonto[] 				= muestrafloat($aIva[$i]);
				}
				
			}
		}
		
		/*print_r($aIdParCat);
		echo "<br>";
		die(print_r($aMonto));*/
		
		#VERIFICO SI LA ORDEN DE PAGO SE REGISTRO EN MOVIMIENTOS PRESUPUESTARIOS#
		if($r){
			
			#AGREGO LAS RELACIONES DEL MOVIMIENTO PRESUPUESTARIO#
			$a = movimientos_presupuestarios::add_relacion($conn,
					$aIdParCat,
					$aCategoriaProgramatica,
					$aPartidaPresupuestaria,
					$nrodoc,
					$aMonto);
			#ASIGNO EL NRO DE DOCUMENTO A LA ORDEN DE PAGO#		
			$b = $this->setNrodoc($conn, $nrodoc, $id_orden);
			
			#ACTUALIZO LA LA FECHA DE APROBACION Y EL ESTATUS DE LA ORDEN DE PAGO#
			$c = $this->setFechaAprobacion($conn, $id_orden);
			
			#ACTUALIZO LA TABLA RELACION_PP_CP EL COMPROMISO Y EL DISPONIBLE#
			$d = relacion_pp_cp::set_desde_compromiso($conn, $aIdParCat, $aMonto);
			
			#VERIFICO SI TODOS LOS PROCESOS ANTERIORES SE EJECUTARON PARA MOSTRAR EL MENSAJE#
			if($a && $b && $c && $d){
			//if($a && $c && $d){	
				$this->msj = ORDEN_APROBADA;
				return true;
				
			}
		
		}else {
			$this->msj = ERROR_ORDEN_APR;
			return false;
		}
	}
	
	#FUNCION QUE PERMITE ANULAR LA ORDEN DE SERVICIO SI ESTA O NO APROBADA#
	function anular($conn, $id, $id_usuario,
										$id_unidad_ejecutora,
										$ano,
										$descripcion,
										$tipdoc,
										$fechadoc,
										$status,
										$id_proveedor,
										$id_ciudadano,
										$aPartidas,
										$nrodoc,
										$aProducto,
										$escEnEje){
										
		//$nrodoc = $this->addNrodoc2($nrodoc, $tipdoc);
		
		try {
		$q3 ="UPDATE orden_servicio_trabajo SET status=3 WHERE id=$id";
	
		$r3 = $conn->Execute($q3) or die($q3);
		//$r3 = true;
		$rif = ($id_proveedor=='')? $id_ciudadano : $id_proveedor;
		//$r3=1;
		#ESTE IF ES EN EL CASO DE QUE LA ORDEN DE SERVICIO YA ESTA APROBADA#			
		if ($r3 && $status=='2'){
		
			#DECODIFICO EL JSON DE LOS PRODUCTOS#
		
			$JsonRec = new Services_JSON();
			$JsonRec = $JsonRec->decode(str_replace("\\","",$aProducto));
			if(is_array($JsonRec->productos)){
				foreach($JsonRec->productos as $productos){
					//die("aqui ".$productos[3]);
									} 
			}
			
			#DECODIFICO EL JSON Y LOS CONVIERTO EN UN ARRAY DE PHP #		
			$JsonRec = new Services_JSON();
			$JsonRec = $JsonRec->decode(str_replace("\\","",$aPartidas));
			$contador = sizeof($JsonRec->partidaspresupuestarias); 
			$nrodocanulado = $nrodoc."-ANULADO";
			
			//REGISTRO LA SOLICITUD EN MOVIMIENTOS PRESUPUESTARIOS//
			$q2 = "INSERT INTO puser.movimientos_presupuestarios ";
			$q2.= "(id_usuario, id_unidad_ejecutora, ano, descripcion, nrodoc, tipdoc, nroref, ";
			$q2.= "fechadoc, status, id_proveedor, status_movimiento) ";
			$q2.= "VALUES ";
			$q2.= "('$id_usuario', '$id_unidad_ejecutora', '$ano', '$descripcion', '$nrodoc', '$tipdoc', '$nrodocanulado', ";
			$q2.= " '$fechadoc', '1', '$rif', '2') ";
			
			//ESTE FOREACH SE HACE PARA PASAR LAS VARIABLES A LA CLASE MOVIMIENTOS PRESUPUESTARIOS//
			$idCat = array();
			//die()
			foreach($JsonRec->partidaspresupuestarias as $servicio){
				$guarda_monto = $servicio[2] * (-1);
				//echo $servicio[2]."<br>";
				$aIdParCat[] = $servicio[7];
				$aCategoriaProgramatica[] = $servicio[0];
				$aPartidaPresupuestaria[] = $servicio[1];
				$aMonto[] = muestrafloat($guarda_monto);
				
				//ESTO SE HACE PARA SACAR EL IVA POR CATEGORIA PROGRAMATICA
				$indice = array_search($servicio[0],$idCat);
				if ($indice!==false){
					$aIva[$indice] = $aIva[$indice] + $servicio[5];
				} else {
					$idCat[] = $servicio[0];
					$aIva[] = $servicio[5];
				}
			}
			
			if(count($aIva) > 0){
				for($i=0;$i<count($aIva);$i++){
					$q = "SELECT relacion_pp_cp.id AS idparcat FROM puser.relacion_pp_cp WHERE id_categoria_programatica = '$idCat[$i]' ";
					$q.= "AND id_partida_presupuestaria = '4031801000000' ";
					$q.= "AND id_escenario = '$escEnEje'";
					$row = $conn->Execute($q);
					while(!$row->EOF){
						$idpc = $row->fields['idparcat'];
						//die("aqui: ".$idpc);
						$monto = $aIva[$i] * (-1);
						$aIdParCat[] 				= $idpc;        //$partidas[3];
						$aCategoriaProgramatica[] 	= $idCat[$i];
						$aPartidaPresupuestaria[] 	= '4031801000000';
						$aMonto[] = muestraFloat($monto);
						$row->movenext();
					}
					
				}
			}
			/*print_r($aIdParCat);
			echo "<br>";
			die(print_r($aMonto));*/
				
			$r2 = $conn->Execute($q2);
			
			#VALIDO SI INSERTO EL REGISTRO EN LA TABLA DE MOVIMIENTO PRESUPUESTARIOS#
			if($r2){
				
				#AGREGO LAS RELACIONES DEL MOVIMIENTO PRESUPUESTARIO#
				if(movimientos_presupuestarios::add_relacion($conn,$aIdParCat,$aCategoriaProgramatica,$aPartidaPresupuestaria,$nrodoc,$aMonto)){
					
					#MODIFICO EL COMPROMISO Y EL DISPONIBLE DE LA PARTIDA PRESUPUESTARIA#
					if(relacion_pp_cp::set_desde_compromiso_anulado($conn, $aIdParCat, $aMonto)){
						if($tipdoc == '009')
							$this->msj = OT_ANULADA;
						else if ($tipdoc == '002')
							$this->msj = OS_ANULADA;
						return true;
	
					}
				}
			}else{
				$this->msj = ERROR;
				return false;
			}
		
		}elseif ($r3){
			if($tipdoc == '009')
				$this->msj = OT_ANULADA;
			else if ($tipdoc == '002')
				$this->msj = OS_ANULADA;			
			return true;
		
		}else{
			$this->msj = ERROR;
			return false;
			
		}
		} catch ( ADODB_Exception $e ){
			//die($conn->ErrorMsg());
				$this->msj = $conn->ErrorMsg();
				return false;

		}
		
	}
		

	function setNrodoc($conn, $nrodoc, $id){
		$q = "UPDATE orden_servicio_trabajo SET nrodoc = '$nrodoc' WHERE id='$id'";
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	function setFechaAprobacion($conn, $id){
		$q = "UPDATE orden_servicio_trabajo SET fecha_aprobacion = now(), status='2' WHERE id='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function buscar($conn, 
						$id_tipo_documento, 
						$id_proveedor, 
						$id_ue, 
						$fecha_desde, 
						$fecha_hasta, 
						$nrodoc,
						$observaciones,
						$max=10, $from=1,
						$orden="id"){
		if(empty($id_tipo_documento)
			&& empty($id_proveedor) 
			&& empty($id_ue)
			&& empty($fecha_desde)
			&& empty($fecha_hasta)
			&& empty($nrodoc)
			&& empty($observaciones)
			)
			return false;
		$q = "SELECT * FROM orden_servicio_trabajo ";
		$q.= "WHERE  1=1 ";
		$q.= !empty($nrodoc) ? "AND nrodoc='$nrodoc' ": "";
		$q.= !empty($fecha_desde) ? "AND fecha >='".guardafecha($fecha_desde)."' ": "";
		$q.= !empty($fecha_hasta) ? "AND fecha <='".guardafecha($fecha_hasta)."' ": "";
		$q.= !empty($id_tipo_documento) ? "AND id_tipo_documento = '$id_tipo_documento'  ":"";
		$q.= !empty($id_proveedor) ? "AND id_proveedor = '$id_proveedor'  ":"";
		$q.= !empty($id_ue) ? "AND id_unidad_ejecutora = '$id_ue'  ":"";
		$q.= !empty($fecha) ? "AND fecha = '".guardafecha($fecha)."' ":"";
		$q.= !empty($observaciones) ? "AND observaciones ILIKE '%$observaciones%' ":"";
		$q.= "ORDER BY $orden ";
		//die($q);
		if(!$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q))
			return false;
		$collection=array();
		while(!$r->EOF){
			$ue = new orden_servicio_trabajo;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		return $coleccion;
	}
	
	function total_buscar($conn, 
						$id_tipo_documento, 
						$id_proveedor, 
						$id_ue, 
						$fecha_desde, 
						$fecha_hasta, 
						$nrodoc,
						$observaciones, 
						$orden="id"){
		if(empty($id_tipo_documento)
			&& empty($id_proveedor) 
			&& empty($id_ue)
			&& empty($fecha_desde)
			&& empty($fecha_hasta)
			&& empty($nrodoc)
			&& empty($observaciones)
			)
			return false;
		$q = "SELECT * FROM orden_servicio_trabajo ";
		$q.= "WHERE  1=1 ";
		$q.= !empty($nrodoc) ? "AND nrodoc='$nrodoc' ": "";
		$q.= !empty($fecha_desde) ? "AND fecha >='".guardafecha($fecha_desde)."' ": "";
		$q.= !empty($fecha_hasta) ? "AND fecha <='".guardafecha($fecha_hasta)."' ": "";
		$q.= !empty($id_tipo_documento) ? "AND id_tipo_documento = '$id_tipo_documento'  ":"";
		$q.= !empty($id_proveedor) ? "AND id_proveedor = '$id_proveedor'  ":"";
		$q.= !empty($id_ue) ? "AND id_unidad_ejecutora = '$id_ue'  ":"";
		$q.= !empty($fecha) ? "AND fecha = '".guardafecha($fecha)."' ":"";
		$q.= !empty($observaciones) ? "AND observaciones ILIKE '%$observaciones%' ":"";
		$q.= "ORDER BY $orden ";
		//die($q);
		if(!$r = $conn->Execute($q))
			return false;
		else
			return $r->RecordCount();
	}
	function showNrodoc($nrodoc){
		$aux = explode("-",$nrodoc);
		$tipdoc = $aux[0];
		$id_ue = $aux[1];
		$nrodoc = $aux[2];
		if ($nrodoc!= ''){
			return $id_ue."-".$nrodoc;
		} else {
			$nrodoc = '';
			return $nrodoc;
			}	
	}
	
	function addNrodoc($nrodoc, $tipdoc, $id_ue){
		$aux = $tipdoc."-".$id_ue."-".$nrodoc;
		return $aux;
	}
	
	function addNrodoc2($nrodoc, $tipdoc){
		$aux = $tipdoc."-".$nrodoc;
		return $aux;
	}
}
?>
