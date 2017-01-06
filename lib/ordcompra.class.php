<? 
class ordcompra{

	// Propiedades

	var $id;             //orden_compra
	var $fecha;
	var $ano;
	var $f_entrega;
	var $l_entrega;
	var $c_pago;
	var $f_solicitud;
	var $nrodoc;
	var $nrosol;
	var $id_unidad_ejecutora;
	var $id_tipo_documento;
	var $observaciones;
	var $rif;
	var $nrif;
	var $id_proveedor;
	var $nombre_proveedor;
	var $direccion_proveedor;
	var $telefono_proveedor;
	var $id_municipio_proveedor;
	var $unidad_medida_proveedor;
	var $id_requisicion;
	var $forma_pago;

	/*****************************
			Objeto Relacion Partidas
	*****************************/
	var $relacion; // almacena un array de objetos de relaciones de partidas
	
	// Propiedades utilizadas por el objeto con relaciones de partidas
	var $descripcion;
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
	var $disponible;
	var $precio_base;
	var $precio_iva;
	var $precio_total;
	var $idParCat;
	
	var $subtotal;
	var $total_iva;
	var $total_general;

	function get($conn, $id, $escEnEjec=''){
		
		$q = "SELECT orden_compra.*, unidades_ejecutoras.descripcion AS unidad_ejecutora, proveedores.id AS id_proveedor, ";
		$q.= "proveedores.nombre AS nombre_proveedor,proveedores.direccion AS direccion_proveedor,estado.descripcion AS id_municipio_proveedor,";
		$q.= "proveedores.telefono AS telefono_proveedor, proveedores.rif AS nrif, ";
		$q.= "vehiculo.forma_pago.descripcion AS forma_pago ";
		$q.= "FROM orden_compra ";
		$q.= "INNER JOIN unidades_ejecutoras ON (orden_compra.id_unidad_ejecutora = unidades_ejecutoras.id) ";
		$q.= "INNER JOIN proveedores ON (orden_compra.rif = proveedores.id) ";
		$q.= "INNER JOIN estado ON (proveedores.id_estado = estado.id) ";
		$q.= "LEFT JOIN vehiculo.forma_pago ON (forma_pago.id = orden_compra.c_pago) ";
		$q.= "WHERE orden_compra.id='$id'";	
		//echo ($q);
		$r = $conn->Execute($q);
		
		if(!$r->EOF){
			
			$this->id = $r->fields['id'];                  
			$this->fecha = $r->fields['fecha'];	
			$this->ano = $r->fields['ano'];	
			$this->f_entrega = $r->fields['f_entrega'];
			$this->l_entrega = $r->fields['l_entrega'];
			$this->c_pago = $r->fields['c_pago'];
			$this->f_solicitud = $r->fields['f_solicitud'];
			/*if(!$auxNrodoc)
				$this->nrodoc = $this->showNrodoc($r->fields['nrodoc']);
			else*/
			$this->nrodoc = $r->fields['nrodoc'];
			$this->nrosol = $r->fields['nrosol'];
			$this->observaciones = $r->fields['observaciones'];
			$this->id_tipo_documento = $r->fields['id_tipo_documento'];
			$this->id_unidad_ejecutora = $r->fields['id_unidad_ejecutora'];
			$this->unidad_ejecutora = $r->fields['unidad_ejecutora'];
			$this->condicion_operacion = $r->fields['condicion_operacion'];
			$this->rif = $r->fields['rif'];
			$this->nrif = $r->fields['nrif'];
			$this->id_proveedor = $r->fields['id_proveedor'];
			$this->nombre_proveedor = $r->fields['nombre_proveedor'];
			$this->direccion_proveedor = $r->fields['direccion_proveedor'];
			$this->telefono_proveedor = $r->fields['telefono_proveedor'];
			$this->id_municipio_proveedor = $r->fields['id_municipio_proveedor'];
			$this->monto = $r->fields['monto'];
			$this->monto_iva = $r->fields['monto_iva'];
			$this->total_orden = $r->fields['total_orden'];
			$this->status = $r->fields['status'];
			$this->getRelacionProductos($conn, $id, $escEnEjec);
			$this->id_requisicion = $r->fields['nrorequi'];
			$this->forma_pago = $r->fields['forma_pago'];
																					
			return true;
		}else
			return false;
	}

	function get_all($conn, $from=0, $max=0, $orden="id"){
		
		$q = "SELECT * FROM orden_compra ";
		$q.= "ORDER BY $orden ";
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		
		while(!$r->EOF){
			
			$ue = new ordcompra;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}
	
	function add($conn, $fecha, $id_unidad_ejecutora, $ano, $fechaent, $lugar, $condpag, $fechasol, $nrosol, $id_proveedor, $observ, $aProducto, $nrodoc='', $id_tipo_documento,$auxNrodoc){
		//$nrodoc = $this->addNrodoc($nrodoc, $id_tipo_documento, $id_unidad_ejecutora);
		$q = "INSERT INTO orden_compra ";
		$q.= "(fecha, id_unidad_ejecutora, ano, f_entrega,  "; 
	    $q.= "l_entrega, c_pago, f_solicitud, nrorequi, observaciones, rif, status, nrodoc) ";
		$q.= "VALUES ";
		$q.= "('$fecha', '$id_unidad_ejecutora', '$ano', '$fechaent', ";
		$q.= "'$lugar', '$condpag', '$fechasol', '$nrosol', '$observ', '$id_proveedor', 1, '$nrodoc')";
		//die($q);
		$r = $conn->execute($q);
		if($r){
			$nrodoc = getLastId($conn, 'id', 'orden_compra'); 
			if($this->addRelacionPartidas($conn,$nrodoc, $aProducto)){
					$this->msj = REG_ADD_OK;
					return true;
			}
		
		}else{
			$this->msj = ERROR_ADD;
			return false;
		}
	}
	
	function set($conn,$id,$today,$anopres,$fechaent,$lugar,$condpag,$fechasol,$nrosol,
					$observ,$aUnidadEjecutora,$aProductos,$nrodoc='', $id_tipo_documento,$auxNrodoc,$idProveedor){
		//$nrodoc = $this->addNrodoc2($nrodoc, $id_tipo_documento);
		$q = "UPDATE orden_compra SET fecha='$today', ano='$anopres', ";
		$q.= "f_entrega='$fechaent', l_entrega='$lugar', c_pago='$condpag', f_solicitud='$fechasol', nrorequi='$nrosol',";  
		$q.= "observaciones='$observ', id_unidad_ejecutora = '$aUnidadEjecutora', ";
		$q.= "nrodoc = '$nrodoc', rif = '$idProveedor' ";
		$q.= "WHERE id=".$id;	
		//die($q);
		if($conn->Execute($q)){
			if($this->delRelacionPartidas($conn, $id)){

				if ($this->addRelacionPartidas($conn,$id, $aProductos)){
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
		$q = "DELETE FROM orden_compra WHERE id='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	function addRelacionPartidas($conn,$nrodoc, $aPartidas){
	
	$JsonRec = new Services_JSON();
	$JsonRec = $JsonRec->decode(str_replace("\\","",$aPartidas));
		if(is_array($JsonRec->productos)){
			foreach($JsonRec->productos as $partidas){
			$sql= "SELECT id as idparcat FROM puser.relacion_pp_cp ";
			$sql.="WHERE id_categoria_programatica = '$partidas[1]' ";
			$sql.="AND id_partida_presupuestaria = '$partidas[2]' ";
			$sql.="AND id_escenario = '1111'";
			//die($sql);
			$aux = $conn->Execute($sql);
			if($aux){
				$idparcat = $aux->fields[idparcat];
				$q = "INSERT INTO relacion_ordcompra ";
				$q.= "( id_categoria_programatica, id_partida_presupuestaria, id_ord_compra, monto, idParCat, id_producto, cantidad, precio_base, precio_iva, iva_porc) ";
				$q.= "VALUES ";
				$q.= "('$partidas[1]', '$partidas[2]', '$nrodoc', ".guardaFloat($partidas[7]).", '$idparcat', ";
				$q.= "'$partidas[0]', ".guardaFloat($partidas[3]).", ".guardaFloat($partidas[4]).", ".guardaFloat($partidas[6]).", ".guardaFloat($partidas[5]).")";
				//echo $q."<br>";
				$r = $conn->Execute($q) or die($q);
			}
		} 
		if($r)
			return true;
		else
			return false;
		}
	}
	
	function addRelacionProductos($conn, $nrodoc,$aProducto){
		
		$JsonRec = new Services_JSON();
		$JsonRec = $JsonRec->decode(str_replace("\\","",$aProducto));
		if(is_array($JsonRec->productos)){
			foreach($JsonRec->productos as $productos){
				$q = "INSERT INTO  relacion_ordcompra_prod ";
				$q.= "( id_ord_compra, id_producto, cantidad, precio_base, precio_iva, precio_total) ";
				$q.= "VALUES ";
				$q.= "('$nrodoc', '$productos[0]', $productos[3], $productos[4], $productos[6], $productos[7]) ";
			//	die($q);
				$r = $conn->Execute($q) or die($q);
			}
		} 
		if($r)
			return true;
		else
			return false;
	}
	
	function delRelacionPartidas($conn, $nrodoc){
		$q = "DELETE FROM relacion_ordcompra WHERE id_ord_compra='$nrodoc'";
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}	
	
	/*function delRelacionProductos($conn, $nrodoc){
		$q = "DELETE FROM relacion_ordcompra_prod WHERE id_ord_compra='$nrodoc'";
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}*/
	
	/*function getRelacionPartidas($conn, $id, $escEnEjec){
		$q = "SELECT relacion_ordcompra.*, partidas_presupuestarias.descripcion AS partida_presupuestaria,  ";
		$q.= "categorias_programaticas.descripcion AS categoria_programatica, puser.relacion_pp_cp.disponible, puser.relacion_pp_cp.id ";
		$q.= "FROM relacion_ordcompra  ";
		$q.= "INNER JOIN partidas_presupuestarias ON (relacion_ordcompra.id_partida_presupuestaria = partidas_presupuestarias.id) ";
		$q.= "INNER JOIN categorias_programaticas ON (relacion_ordcompra.id_categoria_programatica = categorias_programaticas.id) ";
		$q.= "INNER JOIN puser.relacion_pp_cp ON puser.partidas_presupuestarias.id = puser.relacion_pp_cp.id_partida_presupuestaria AND puser.categorias_programaticas.id = puser.relacion_pp_cp.id_categoria_programatica ";
		$q.= "WHERE relacion_ordcompra.id_ord_compra='$id' ";
		$q.= "AND categorias_programaticas.id_escenario = '$escEnEjec' ";
		$q.= "AND partidas_presupuestarias.id_escenario = '$escEnEjec' ";
		$q.= "AND relacion_pp_cp.id_escenario = '$escEnEjec' ";
		//die($q);
		$r = $conn->Execute($q);
		while(!$r->EOF){
			$ue = new ordcompra;
			$ue->nrodoc = $r->fields['nrodoc'];
			$ue->id_partida_presupuestaria	= $r->fields['id_partida_presupuestaria'];
			$ue->id_categoria_programatica = $r->fields['id_categoria_programatica'];
			$ue->partida_presupuestaria	= $r->fields['partida_presupuestaria'];
			$ue->categoria_programatica = $r->fields['categoria_programatica'];
			$ue->monto = $r->fields['monto'];
			$ue->disponible = $r->fields['disponible'];
			$ue->idParCat = $r->fields['idparcat'];
			$coleccion[] = $ue;
			$r->movenext();
		}
		
			
		return $coleccion;
	}*/
	
	function getRelacionProductos($conn, $id, $escEnEjec){
		/*$q = "SELECT relacion_ordcompra_prod.*, productos.descripcion AS producto, productos.unidad_medida AS unidad_medida
				FROM relacion_ordcompra_prod
				INNER JOIN productos ON (relacion_ordcompra_prod.id_producto = productos.id) ";
		$q.= "WHERE relacion_ordcompra_prod.id_ord_compra='$id' ";*/
		
		$q = "SELECT relacion_ordcompra.*, partidas_presupuestarias.descripcion AS partida_presupuestaria,  ";
		$q.= "categorias_programaticas.descripcion AS categoria_programatica, puser.relacion_pp_cp.disponible, puser.relacion_pp_cp.id, ";
		$q.= "productos.descripcion AS desc_prod, productos.unidad_medida AS unid_medida ";
		$q.= "FROM relacion_ordcompra  ";
		$q.= "INNER JOIN partidas_presupuestarias ON (relacion_ordcompra.id_partida_presupuestaria = partidas_presupuestarias.id) ";
		$q.= "INNER JOIN categorias_programaticas ON (relacion_ordcompra.id_categoria_programatica = categorias_programaticas.id) ";
		$q.= "INNER JOIN puser.relacion_pp_cp ON puser.partidas_presupuestarias.id = puser.relacion_pp_cp.id_partida_presupuestaria AND puser.categorias_programaticas.id = puser.relacion_pp_cp.id_categoria_programatica ";
		$q.= "INNER JOIN puser.productos ON relacion_ordcompra.id_producto = puser.productos.id ";
		$q.= "WHERE relacion_ordcompra.id_ord_compra='$id' ";
		$q.= "AND categorias_programaticas.id_escenario = '$escEnEjec' ";
		$q.= "AND partidas_presupuestarias.id_escenario = '$escEnEjec' ";
		$q.= "AND relacion_pp_cp.id_escenario = '$escEnEjec' ";
		//die($q);
		$r = $conn->Execute($q);
		$i=0;
		$subtotal = 0;
		$total_iva = 0;
		$total_general = 0;
		while(!$r->EOF){
			//$ue = new movimientos_presupuestarios;
			$coleccion[$i]['id_producto'] = $r->fields['id_producto'];
			$coleccion[$i]['id_categoria_programatica'] = $r->fields['id_categoria_programatica'];
			$coleccion[$i]['id_partida_presupuestaria'] = $r->fields['id_partida_presupuestaria'];
			$coleccion[$i]['cantidad'] = $r->fields['cantidad'];
			$coleccion[$i]['precio_base'] = $r->fields['precio_base'];
			$coleccion[$i]['precio_iva'] = $r->fields['precio_iva'];
			$coleccion[$i]['iva_porc'] = $r->fields['iva_porc'];
			$coleccion[$i]['monto'] = $r->fields['monto'];
			$coleccion[$i]['desc_producto'] =  $r->fields['desc_prod'];
			$coleccion[$i]['unidad_medida'] = $r->fields['unid_medida'];
			$coleccion[$i]['partida_presupuestaria'] = $r->fields['partida_presupuestaria'];
			$subtotal += $r->fields['monto'];
			$total_iva += $r->fields['precio_iva'];
			
			$i++;
			$r->movenext();		
		}
		$total_general = $subtotal;
		//$this->subtotal = $subtotal;
		$this->total_iva = $total_iva;
		$this->total_general = $total_general;
		$this->relacionProductos = new Services_JSON();
		$this->relacionProductoReporte = $coleccion;
		$this->relacionProductos = is_array($coleccion) ? $this->relacionProductos->encode($coleccion) : false;
	}
	
	#FUNCION QUE PERMITE APROBAR LA ORDEN DE PAGO Y REGISTRALO EN MOVIMIENTOS PRESUPUESTARIOS#
	function aprobar($conn, $id_orden,  $nrif,
										$id_usuario,
										$id_unidad_ejecutora,
										$ano,
										$observ,
										$nrodoc,
										$tipdoc,
										$tipref,
										$fechadoc,
										$fechaent,
										$status,
										$rif,
										$aCategoriaProgramatica,
										//$aPartidas,
										$aProductos,
										$nrodoc='',
										$escEnEje='',
										$auxNrodoc){

		#OBTENGO EL NRO DE DOCUMENTOS#
		// Mientras se cargue el numero de documento de manera manual esto debe estar asi
		if(empty($nrodoc))
			$nrodoc = movimientos_presupuestarios::getNroDoc($conn, $tipdoc);
		else
			$nrodoc = $this->addNrodoc2($nrodoc, $tipdoc);
		
		#DECODIFICO EL JSON DE LAS PARTIDAS PRESUPUESTARIAS#
		$JsonRec = new Services_JSON();
		$JsonRec = $JsonRec->decode(str_replace("\\","",$aProductos));
		$contador = sizeof($JsonRec->productos);
		
		 #DECODIFICO EL JSON DE LOS PRODUCTOS#
		/*$JsonPro = new Services_JSON();
		$JsonPro = $JsonPro->decode(str_replace("\\","",$aProductos));*/

		//die($nrodoc);
		#CHEQUEO LA DISPONIBILIDAD DE LAS PARTIDAS, EN EL CASO DE QUE EL MONTO NO ESTA DISPONIBLE NO SE APRUEBA ESA ORDEN#
		/*for($i = 0; $i < $contador; $i++){
			$q = "SELECT disponible::numeric FROM puser.relacion_pp_cp WHERE id_categoria_programatica = '".$JsonRec->productos[$i][1]."' ";
			$q.= "AND id_partida_presupuestaria = '".$JsonRec->productos[$i][2]."' ";
			$q.= "AND id_escenario = '$escEnEje'";
			//die($q);
			$r = $conn->Execute($q);
			//die(var_dump($r));
			if($r){
				//die('disp: '.$r->fields['disponible'].' silic: '.$JsonRec->productos[$i][7]); 
				if($r->fields['disponible'] < $JsonRec->productos[$i][7]){
					//die('entro2');
					$this->msj = ERROR_SOLICITUD_PAGO_APR_NO_DISP;
					return false;
				}
			}
		} */
		
		#REGISTRO LA ORDEN DE COMPRAS EN MOVIMIENTOS PRESUPUESTARIOS#	
		$q = "INSERT INTO movimientos_presupuestarios ";
		$q.= "(id_usuario, id_unidad_ejecutora, ano, descripcion, nrodoc, tipdoc, tipref, ";//nroref, 
		$q.= "fechadoc, fecharef, status, id_proveedor) ";
		$q.= "VALUES ";
		$q.= "('$id_usuario', '$id_unidad_ejecutora', '$ano', '$observ', '$nrodoc', '001', '$tipref', ";//'$nroref', 
		$q.= " '$fechadoc', '$fechaent', '1', '$rif') ";
		//die($q);
		$r = $conn->Execute($q) or die($q);
		
		#ESTE FOREACH SE HACE PARA PASAR LAS VARIABLES A LA FUNCION ADD_RELACION DE LA CLASE MOVIMIENTOS PRESUPUESTARIOS#
		$aIdParCat = array();
		$aIva = array();
		$aIPC = array();
		
		
		$iva = 0;
		foreach($JsonRec->productos as $partidas){
		
			$q = "SELECT relacion_pp_cp.id AS idparcat FROM puser.relacion_pp_cp WHERE id_categoria_programatica = '".$partidas[1]."' ";
			$q.= "AND id_partida_presupuestaria = '".$partidas[2]."' ";
			$q.= "AND id_escenario = '$escEnEje'";
			//die($q);
			//echo $q."<br>";
			#EL PRECIO TOTAL DEL ARTICULO ESTA ES $partidas[7] SE VA A UTILIZAR AHORA $partidas[4] * $partidas[3]
			#PARA MANEJAR SOLO LA BASE IMPONIBLE Y AÑADIR LA SUMATORIA DE $partidas[5] PARA LLEVAR EL TOTAL A LA PARTIDA DEL IVA
			
			$row = $conn->Execute($q);
			$idparcat = $row->fields['idparcat']; 
			//echo "ParCat: N# ".$j." - ".$idparcat."<br>";
			$base = $partidas[4] * $partidas[3];
			$iva = $base * ($partidas[5]/100);
			
			$indice = array_search($idparcat,$aIdParCat);
			if ($indice!==false){
				$AuxaMonto = guardaFloat($aMonto[$indice]) + $base;
				$aMonto[$indice] = muestraFloat($AuxaMonto);
				//REVISAR PORQUE DUPLICA EL MONTO DEL IVA EN UNA ITERACION
				//$aIva[$indice] = $aIva[$indice] + $iva;
				//echo $aMonto[$indice]."<br>";
				//echo $aIva[$indice]."<br>";
				
			} else {
				$aIdParCat[] 				= $idparcat;        //$partidas[3];
				$aCategoriaProgramatica[] 	= $partidas[1];
				$aPartidaPresupuestaria[] 	= $partidas[2];
				$aMonto[] 					= muestraFloat($base);
				}
			
			$indiceIva = array_search($partidas[1],$aIPC);
			if ($indiceIva!==false){
				$aIva[$indiceIva] = $aIva[$indiceIva] + $iva;
				//echo $aIva[$indiceIva]."<br>";
				
			} else {
				$aIva[]						= $iva;
				$aIPC[]						= $partidas[1];
				}	
			
			
			
			$aProducto[] 		= $partidas[0];
			$aCantidadProd[] 	= $partidas[3];
			$aPrecioBaseProd[] 	= $partidas[4];
			$aPrecioIVAProd[] 	= $partidas[5];
			$aPrecioTotalProd[] = $partidas[7];
		}
		
		/*echo "<br>ParCat";
		print_r($aIdParCat);
		echo "<br>aMonto";
		print_r($aMonto);
		echo "<br>aIva";
		print_r($aIva);
		echo "<br>aIPC";
		print_r($aIPC);
		die();*/
		
		//echo (print_r($aIdParCat));
		$j = 0;
	
		#ESTO SE HACE PARA AÑADIR LA PARTIDA DEL IVA A LA IMPUTACION PRESUPUESTARIA
		foreach($aIPC as $IvaParCat){
			if($aIva[$j]>0){
				$q = "SELECT relacion_pp_cp.id AS idparcat FROM puser.relacion_pp_cp WHERE id_categoria_programatica = '$IvaParCat' ";
				$q.= "AND id_partida_presupuestaria = '4031801000000' ";
				$q.= "AND id_escenario = '$escEnEje'";
				$row = $conn->Execute($q);
				$idpc = $row->fields['idparcat'];
				$aIdParCat[] 				= $idpc;        //$partidas[3];
				$aCategoriaProgramatica[] 	= $IvaParCat;
				$aPartidaPresupuestaria[] 	= '4031801000000';
				$aMonto[] 					= muestraFloat($aIva[$j]);
			}
			$j++;
		}
		/*echo "<br>".$q."<br>";
		print_r($aIdParCat);
		echo "<br>";
		die(print_r($aMonto));*/
		
		#ESTE FOREACH SE HACE PARA PASAR LAS VARIABLES A LA FUNCION ADD_RELACION_PRODUCTOS DE LA CLASE MOVIMIENTOS PRESUPUESTARIOS#
		/*foreach($JsonPro->productos as $productos){
		
			$aProducto[] 		= $productos[0];
			$aCantidadProd[] 	= $productos[1];
			$aPrecioBaseProd[] 	= $productos[2];
			$aPrecioIVAProd[] 	= $productos[3];
			$aPrecioTotalProd[] = $productos[4];
		}*/

	
		#VERIFICO SI LA ORDEN DE PAGO SE REGISTRO EN MOVIMIENTOS PRESUPUESTARIOS#
		if($r){
			
			#AGREGO LAS RELACIONES DEL MOVIMIENTO PRESUPUESTARIO#
			$a = movimientos_presupuestarios::add_relacion($conn,
					$aIdParCat,
					$aCategoriaProgramatica,
					$aPartidaPresupuestaria,
					$nrodoc,
					$aMonto);
			#AGREGO LOS PRODUCTOS DEL MOVIMIENTO PRESUPUESTARIO#			
			$b = movimientos_presupuestarios::addRelacionProductos($conn, 
					$nrodoc,
					$aProducto,
					$aCantidadProd,
					$aPrecioBaseProd,
					$aPrecioIVAProd,
					$aPrecioTotalProd);
			
			#ASIGNO EL NRO DE DOCUMENTO A LA ORDEN DE PAGO#		
			//Esta funcion se debe activar cuando los nrodoc se generen solos 
			$c = $this->setNrodoc($conn, $nrodoc, $id_orden);
			
			#ACTUALIZO LA LA FECHA DE APROBACION Y EL ESTATUS DE LA ORDEN DE PAGO#
			$d = $this->setFechaAprobacion($conn, $id_orden);
			
			#ACTUALIZO LA TABLA RELACION_PP_CP EL COMPROMISO Y EL DISPONIBLE#
			$e = relacion_pp_cp::set_desde_compromiso($conn, $aIdParCat, $aMonto);
			
			#VERIFICO SI TODOS LOS PROCESOS ANTERIORES SE EJECUTARON PARA MOSTRAR EL MENSAJE#
			#este es el que debe estar cuando los nrodoc se generen automaticos IODG 01/12/06
			if($a && $b && $c && $d && $e){ 
			//if($a && $b && $d && $e){
				$this->msj = OC_APROBADA;
				return true;
				
			}
		
		}else{
			$this->msj = ERROR;
			return false;
		}
	}
	
	#FUNCION QUE PERMITE ANULAR LA ORDEN DE COMPRA SI ESTA O NO APROBADA#
	function anular($conn, $id, $id_usuario,
										$id_unidad_ejecutora,
										$ano,
										$descripcion,
										$tipdoc,
										$fechadoc,
										$status,
										$id_proveedor,
										$aPartidas,
										$nrodoc,
										$escEnEje=''){
		//$nrodoc = $this->addNrodoc2($nrodoc, $tipdoc);
		//die($nrodoc);
		
		try{
		
		$q3 ="UPDATE puser.orden_compra SET status=3 WHERE id=$id";
		
		$r3 = $conn->Execute($q3) or die($q3);
		//$r3 = true;
		#ESTE IF ES EN EL CASO DE QUE LA ORDEN DE COMPRA YA ESTA APROBADA#			
		if ($r3 && $status=='2'){
			
			#DECODIFICO EL JSON Y LOS CONVIERTO EN UN ARRAY DE PHP #		
			$JsonRec = new Services_JSON();
			$JsonRec = $JsonRec->decode(str_replace("\\","",$aPartidas));
			$contador = sizeof($JsonRec->productos); 
			$nrodocanulado = $nrodoc."-ANULADO";
			//REGISTRO LA SOLICITUD EN MOVIMIENTOS PRESUPUESTARIOS//
			$q2 = "INSERT INTO puser.movimientos_presupuestarios ";
			$q2.= "(id_usuario, id_unidad_ejecutora, ano, descripcion, nrodoc, tipdoc, nroref, ";
			$q2.= "fechadoc, status, id_proveedor, status_movimiento) ";
			$q2.= "VALUES ";
			$q2.= "('$id_usuario', '$id_unidad_ejecutora', '$ano', '$descripcion', '$nrodoc', '$tipdoc', '$nrodocanulado', ";
			$q2.= " '$fechadoc', '1', '$id_proveedor', '2') ";
			$aIdParCat = array();
			//ESTE FOREACH SE HACE PARA PASAR LAS VARIABLES A LA CLASE MOVIMIENTOS PRESUPUESTARIOS//
			$aIva = array();
			$aIPC = array();
			
			foreach($JsonRec->productos as $partidas){
				
				$q = "SELECT relacion_pp_cp.id AS idparcat FROM puser.relacion_pp_cp WHERE id_categoria_programatica = '".$partidas[1]."' ";
				$q.= "AND id_partida_presupuestaria = '".$partidas[2]."' ";
				$q.= "AND id_escenario = '$escEnEje'";
				//die($q);
				$row = $conn->Execute($q);
				
				$idparcat = $row->fields['idparcat']; 
				
				$base = $partidas[4] * $partidas[3];
				$iva = $base * ($partidas[5]/100);
			
				$indice = array_search($idparcat,$aIdParCat);
				if ($indice!==false){
					$AuxaMonto = guardaFloat($aMonto[$indice]) + ($base * (-1)) ;
					$aMonto[$indice] = muestraFloat($AuxaMonto);
					//$aMonto[$indice] = $AuxaMonto;
					//$aIva[$indice] = $aIva[$indice] + $iva;
					
				} else {
					$aIdParCat[] 				= $idparcat;        //$partidas[3];
					$aCategoriaProgramatica[] 	= $partidas[1];
					$aPartidaPresupuestaria[] 	= $partidas[2];
					$aMonto[] 					= muestraFloat($base * (-1));
					//$aIva[]						= $iva;
					//$aIPC[]						= $partidas[1];
					}
					
				$indiceIva = array_search($partidas[1],$aIPC);
				if ($indiceIva!==false){
					$AuxaIva = guardaFloat($aIva[$indiceIva]) + $iva;
					$aIva[$indiceIva] = muestraFloat($AuxaIva);
					//$aIva[$indiceIva] = $aIva[$indiceIva] + $iva;
	
					
				} else {
					$aIva[]						= muestraFloat($iva);
					$aIPC[]						= $partidas[1];
				}	
					
				
			}
			
			/*print_r($aIdParCat);
			echo"<br>";
			print_r($aCategoriaProgramatica);
			echo"<br>";
			print_r($aPartidaPresupuestaria);
			echo"<br>";
			print_r($aMonto);
			echo"<br>";
			print_r($aIva);
			echo"<br>";
			die(print_r($aIPC));*/
			
			#ESTO SE HACE PARA AÑADIR LA PARTIDA DEL IVA A LA IMPUTACION PRESUPUESTARIA
			$j = 0;
			foreach($aIPC as $IvaParCat){
				if($aIva[$j] > 0){
					$q = "SELECT relacion_pp_cp.id AS idparcat FROM puser.relacion_pp_cp WHERE id_categoria_programatica = '$IvaParCat' ";
					$q.= "AND id_partida_presupuestaria = '4031801000000' ";
					$q.= "AND id_escenario = '$escEnEje'";
					//echo $q."<br>";
					$row = $conn->Execute($q);
					$idpc = $row->fields['idparcat'];
					$aIdParCat[] 				= $idpc;        //$partidas[3];
					$aCategoriaProgramatica[] 	= $IvaParCat;
					$aPartidaPresupuestaria[] 	= '4031801000000';
					$AuxMontoIva 				= guardaFloat($aIva[$j]) * (-1);
					$aMonto[] 					= muestraFloat($AuxMontoIva);
				}
				$j++;
			}
			
			/*print_r($aIdParCat);
			echo"<br>";
			die(print_r($aMonto));*/
				
			$r2 = $conn->Execute($q2);
			//$r2 = true;
			#VALIDO SI INSERTO EL REGISTRO EN LA TABLA DE MOVIMIENTO PRESUPUESTARIOS#
			if($r2){
				
				#AGREGO LAS RELACIONES DEL MOVIMIENTO PRESUPUESTARIO#
				if(movimientos_presupuestarios::add_relacion($conn,$aIdParCat,$aCategoriaProgramatica,$aPartidaPresupuestaria,$nrodocanulado,$aMonto)){
					
					#MODIFICO EL COMPROMISO Y EL DISPONIBLE DE LA PARTIDA PRESUPUESTARIA#
					if(relacion_pp_cp::set_desde_compromiso_anulado($conn, $aIdParCat, $aMonto)){
						$this->msj = OC_ANULADA;
						return true;
	
					}
				}
			}else{
				$this->msj = ERROR;
				return false;
			}
		
		}elseif ($r3){
			$this->msj = OC_ANULADA;
			return true;
		
		}else{
			$this->msj = ERROR;
			return false;
			
		}
		} catch( ADODB_Exception $e ){
			//die($conn->ErrorMsg());
				$this->msj = $conn->ErrorMsg();
				return false;

		}
		
		
	}
	
	
	function setNrodoc($conn, $nrodoc, $id){
		$q = "UPDATE orden_compra SET nrodoc = '$nrodoc' WHERE id='$id'";
		$r = $conn->Execute($q) or die($q); 
		if($r)
			return true;
		else
			return false;
	}
	
	function setFechaAprobacion($conn, $id){
		$q = "UPDATE orden_compra SET fecha_aprobacion = now(), status=2 WHERE id='$id'";
		$r = $conn->Execute($q) or die($q); 
		if($r)
			return true;
		else
			return false;
	}
	
	function buscar($conn, 
						$id_proveedor, 
						$id_ue, 
						$fecha_desde, 
						$fecha_hasta, 
						$nrodoc,
						$observaciones, 
						$orden="id",$from=0, $max=0){
		if(empty($id_proveedor) 
			&& empty($id_ue)
			&& empty($fecha_desde)
			&& empty($fecha_hasta)
			&& empty($nrodoc)
			&& empty($observaciones)
			)
			return false;
		$q = "SELECT * FROM orden_compra ";
		$q.= "WHERE  1=1 ";
		$q.= !empty($nrodoc) ? "AND nrodoc='$nrodoc' ": "";
		$q.= !empty($fecha_desde) ? "AND fecha >='".guardafecha($fecha_desde)."' ": "";
		$q.= !empty($fecha_hasta) ? "AND fecha <='".guardafecha($fecha_hasta)."' ": "";
		$q.= !empty($id_proveedor) ? "AND rif = '$id_proveedor'  ":"";
		$q.= !empty($id_ue) ? "AND id_unidad_ejecutora = '$id_ue'  ":"";
		$q.= !empty($observaciones) ? "AND observaciones ILIKE '%$observaciones%' ":"";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from):$conn->Execute($q);
		if(!$r)
			return false;
		$collection=array();
		while(!$r->EOF){
			$ue = new ordcompra;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		return $coleccion;
	}
	
	function totalRegistroBusqueda($conn,$id_proveedor,$id_ue,$fecha_desde,$fecha_hasta,$nrodoc,$descripcion,$orden="id"){
		if(empty($id_proveedor) && empty($id_ue) && empty($fecha_desde) && empty($fecha_hasta) && empty($nrodoc) && empty($descripcion))
			return 0;
		$q = "SELECT * FROM puser.orden_compra ";
		$q.= "WHERE  1=1 ";
		$q.= !empty($nrodoc) ? "AND nrodoc='$nrodoc' ": "";
		$q.= !empty($fecha_desde) ? "AND fecha >='".guardafecha($fecha_desde)."' ": "";
		$q.= !empty($fecha_hasta) ? "AND fecha <='".guardafecha($fecha_hasta)."' ": "";
		$q.= !empty($id_proveedor) ? "AND rif = '$id_proveedor'  ":"";
		$q.= !empty($id_ue) ? "AND id_unidad_ejecutora = '$id_ue'  ":"";
		$q.= !empty($descripcion) ? "AND observaciones ILIKE '%$descripcion%' ":"";
		$q.= "ORDER BY $orden ";
		//die($q);
		if($r = $conn->Execute($q))
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
	
	function buscaNroDoc($conn, $nrodoc, $tipdoc, $id_ue, $tabla){
		$aux2=  explode('-',$nrodoc);
		//die($aux2[1]);
		if($aux2[1]=='')
			$aux = $tipdoc."-".$id_ue."-".$nrodoc;
		else
			$aux = $tipdoc."-".$nrodoc;
		$q= "SELECT id from $tabla WHERE nrodoc = '$aux'";
		//die($q);
		$r= $conn->Execute($q);
		return $r->fields['id'];
	}
	
	function actualizaPrecios($conn, $id_requisicion, $id_proveedor){
		$q = "SELECT d.costo,a.id_producto,a.id_categoria AS categoria,a.id_partida AS partida ,a.cantidad, d.iva AS iva, d.costo as monto_base, (d.costo * a.cantidad) * (d.iva /100) as precio_iva, (d.costo * a.cantidad)+ (d.costo * a.cantidad * d.iva /100) as monto, ";
		$q.= " ( SELECT puser.relacion_pp_cp.id FROM puser.relacion_pp_cp WHERE puser.relacion_pp_cp.id_categoria_programatica = a.id_categoria AND puser.relacion_pp_cp.id_partida_presupuestaria = a.id_partida AND puser.relacion_pp_cp.id_escenario = '1111' ) AS idparcat "; 
		$q.= "FROM ((puser.relacion_requisiciones AS a "; 
		$q.= "INNER JOIN puser.requisiciones AS b ON a.id_requisicion= b.id) "; 
		$q.= "INNER JOIN puser.gbl_requisicion as c on b.nroreqgbl = c.id) "; 
		$q.= "INNER JOIN puser.proveedores_requisicion AS d ON d.id_requisicion = c.id AND a.id_producto=d.id_producto "; 
		$q.= "WHERE d.id_requisicion='$id_requisicion' AND d.id_proveedor=$id_proveedor ";
		//die($q);
		$r = $conn->Execute($q);
		while(!$r->EOF){
			$prod = new ordcompra;
			$prod->costo = $r->fields['costo'];
			$prod->id_producto = $r->fields['id_producto'];
			$prod->categoria = $r->fields['categoria'];
			$prod->partida = $r->fields['partida'];
			$prod->cantidad = $r->fields['cantidad'];
			$prod->iva = $r->fields['iva'];
			$coleccion[] = $prod;
			$r->movenext();
		}
		return $coleccion;
		
	}
}
?>
