<?
class recepcion_orden_compra{

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
	var $num_fact;
	var $fecha_recep;
	var $fecha_requi;
	var $comentario;
	var $total_parcial;
	var $status;
	var $num_control;

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
	
	/********************************
			Objeto de Relacion de Requisicion Global
	*********************************/
	
	var $relacionReqGlob; // Almacena array de objetos con la data de la requisicion Global
	
	//var $id_requisicion;
	var $cantidad_solic;
	var $cantidad_despachada;

	function get($conn, $id){
		
		$q = "SELECT orden_compra.*, unidades_ejecutoras.descripcion AS unidad_ejecutora, proveedores.id AS id_proveedor, ";
		$q.= "proveedores.nombre AS nombre_proveedor,proveedores.direccion AS direccion_proveedor,proveedores.id_municipio AS id_municipio_proveedor,";
		$q.= "proveedores.telefono AS telefono_proveedor, proveedores.rif AS nrif, ";
		$q.="puser.recepcion_orden_compra.num_fact, puser.recepcion_orden_compra.fecha AS fecha_rec, puser.recepcion_orden_compra.num_control, ";
		$q.="puser.gbl_requisicion.fecha_r, puser.recepcion_orden_compra.comentario, puser.recepcion_orden_compra.total_parcial ";
		$q.= "FROM puser.orden_compra ";
		$q.= "INNER JOIN puser.unidades_ejecutoras ON (orden_compra.id_unidad_ejecutora = unidades_ejecutoras.id) ";
		$q.= "INNER JOIN puser.proveedores ON (orden_compra.rif = proveedores.id) ";
		$q.="LEFT Join puser.recepcion_orden_compra ON puser.orden_compra.id = puser.recepcion_orden_compra.id_ordcompra ";
		$q.="Inner Join puser.gbl_requisicion ON puser.orden_compra.nrorequi = puser.gbl_requisicion.id ";
		$q.= "WHERE orden_compra.id='$id' AND unidades_ejecutoras.id_escenario = '1111'";	
		//die($q);
		$r = $conn->Execute($q);
		
		if(!$r->EOF){
			
			$this->id = $r->fields['id'];                  
			$this->fecha = $r->fields['fecha'];	
			$this->ano = $r->fields['ano'];	
			$this->f_entrega = $r->fields['f_entrega'];
			$this->l_entrega = $r->fields['l_entrega'];
			$this->c_pago = $r->fields['c_pago'];
			$this->f_solicitud = $r->fields['f_solicitud'];
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
			$this->getRelacionProductos($conn, $id);
			$this->id_requisicion = $r->fields['nrorequi'];
			$this->num_fact = $r->fields['num_fact'];
			$this->fecha_recep = $r->fields['fecha_rec'];
			$this->fecha_requi = $r->fields['fecha_r'];
			$this->comentario = $r->fields['comentario'];
			$this->total_parcial = $r->fields['total_parcial'];
			$this->status = $r->fields['status'];
			$this->num_control = $r->fields['num_control'];
			$this->getRelacionReqGlob($conn, $r->fields['nrorequi']);
																					
			return true;
		}else
			return false;
	}

	function get_all($conn, $from=0, $max=0,$orden="id"){
		
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
	
	
	function del($conn, $id){
		$q = "DELETE FROM orden_compra WHERE id='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	function getRelacionReqGlob($conn,$id){
		$q = "SELECT * FROM puser.relacion_gbl_requisicion ";
		$q.= "WHERE id_gbl_requisicion = '$id'";
		//die($q);
		$r = $conn->Execute($q);
		while(!$r->EOF){
			$reqglob = new ordcompra;
			$reqglob->id_requisicion = $r->fields['id_gbl_requisicion'];
			$reqglob->id_producto = $r->fields['id_producto'];
			$reqglob->cantidad_solic = $r->fields['cantidad'];
			$reqglob->cantidad_despachada = $r->fields['cantidad_despachada'];
			$coleccion[] = $reqglob; 
			$r->movenext();
		}
		
		$this->relacionReqGlob = new Services_JSON();
		$this->relacionReqGlob = is_array($coleccion) ? $this->relacionReqGlob->encode($coleccion) : false;
		//die($this->relacionReqGlob);
	}
	
	function getRelacionPartidas($conn, $id, $escEnEjec){
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
	}
	
	function getRelacionProductos($conn, $id){
		$q = "SELECT puser.relacion_ordcompra.id, ";
		$q.="puser.relacion_ordcompra.id_ord_compra, ";
		$q.="puser.relacion_ordcompra.id_producto, ";
		$q.="puser.relacion_ordcompra.cantidad, ";
		$q.="puser.relacion_ordcompra.precio_base, ";
		$q.="puser.relacion_ordcompra.precio_iva, ";
		$q.="puser.relacion_ordcompra.monto, ";
		$q.="puser.productos.descripcion AS producto, ";
		$q.="puser.productos.unidad_medida AS unidad_medida, ";
		$q.="puser.relacion_requisiciones.cantidad_despachada, ";
		$q.="puser.relacion_requisiciones.cantidad_despachada_anterior, ";
		$q.="relacion_ue_cp.id_unidad_ejecutora, ";
		$q.="requisiciones.id AS id_requisicion ";
		$q.="FROM puser.relacion_ordcompra ";
		$q.="Inner Join puser.productos ON (puser.relacion_ordcompra.id_producto = puser.productos.id) ";
		$q.="Inner Join puser.orden_compra ON puser.relacion_ordcompra.id_ord_compra = puser.orden_compra.id ";
		$q.="Inner Join puser.relacion_gbl_requisicion ON puser.orden_compra.nrorequi = puser.relacion_gbl_requisicion.id_gbl_requisicion "; 
		$q.="AND puser.relacion_ordcompra.id_producto = puser.relacion_gbl_requisicion.id_producto ";
		$q.="INNER JOIN puser.relacion_ue_cp ON (relacion_ordcompra.id_categoria_programatica = relacion_ue_cp.id_categoria_programatica) "; 
		$q.="INNER JOIN puser.requisiciones ON (orden_compra.nrorequi = requisiciones.nroreqgbl AND relacion_ue_cp.id_unidad_ejecutora = requisiciones.id_unidad_ejecutora) ";
		$q.="INNER Join puser.relacion_requisiciones ON requisiciones.id = relacion_requisiciones.id_requisicion AND relacion_ordcompra.id_producto = relacion_requisiciones.id_producto "; 
		$q.="WHERE puser.relacion_ordcompra.id_ord_compra = '$id' AND relacion_ue_cp.id_escenario = '1111'";
		//die($q);
		$r = $conn->Execute($q);
		$i=0;
		$subtotal = 0;
		$total_iva = 0;
		$total_general = 0;
		while(!$r->EOF){
			//$ue = new movimientos_presupuestarios;
			$coleccion[$i][0] = $r->fields['id_producto'];
			$coleccion[$i][1]	= $r->fields['producto'];
			$coleccion[$i][2] = $r->fields['unidad_medida'];
			$coleccion[$i][3]	= $r->fields['cantidad'];
			$coleccion[$i][4] = $r->fields['precio_base'];
			$coleccion[$i][5] = $r->fields['precio_iva'];
			$coleccion[$i][6] = $r->fields['monto'];
			$coleccion[$i][7] = $r->fields['cantidad_despachada'];
			$coleccion[$i][8] = $r->fields['id_unidad_ejecutora'];
			$coleccion[$i][9] = $r->fields['id_requisicion'];
			$coleccion[$i][10] = $r->fields['cantidad_despachada_anterior'];
			$subtotal += $r->fields['precio_total'];
			$total_iva += $r->fields['precio_iva'];
			
			$i++;
			$r->movenext();		
		}
		$total_general = $subtotal + $total_iva;
		$this->subtotal = $subtotal;
		$this->total_iva = $total_iva;
		$this->total_general = $total_general;
		$this->relacionProductos = new Services_JSON();
		$this->relacionProductos = is_array($coleccion) ? $this->relacionProductos->encode($coleccion) : false;
	}
	
	function add($conn, $id_ordcompra, $id_requisicion, $numfactura, $tot_par, $comentario, $fecha, $id_usuario, $recepcionGbl,$recepcionDet, $nrocontrol){
		//die(print_r($tot_par));
		$q="INSERT INTO puser.recepcion_orden_compra ( ";
		$q.="id_ordcompra, num_fact, fecha, comentario, total_parcial, id_usuario, num_control ) ";
		$q.="VALUES ( ";
		$q.="'$id_ordcompra', '$numfactura', '$fecha', '$comentario', '$tot_par[0]', '$id_usuario', '$nrocontrol')";
		/*echo $q."<br>";
		var_dump($recepcionGbl);
		die(var_dump($recepcionDet));*/
		if($conn->Execute($q)){
			$a = $this->setDespacho_ordencompra($conn, $recepcionGbl);
			//var_dump($a);
			if($a){
				if($this->setDespacho_requisicion($conn,$recepcionDet)){
					$this->setStatusOC($conn, $id_ordcompra);
					return true;
				} else {
					return false;
				}
			}else{
				return false;
			}
		}else
			return false;
		
	}
	
	
	function setDespacho_ordencompra($conn, $recepcion){
		$JsonRec = new Services_JSON();
		$JsonRec=$JsonRec->decode(str_replace("\\","",$recepcion));
		if(is_array($JsonRec->recepcion)){
			foreach ($JsonRec->recepcion as $oRecepcion){
				$q="UPDATE puser.relacion_gbl_requisicion ";
				$q.="SET cantidad_despachada = $oRecepcion[3] ";
				$q.="WHERE id_gbl_requisicion = '$oRecepcion[0]' AND id_producto = '$oRecepcion[1]'";
				//die($q);
				//echo $q."<br>";
				$r= $conn->Execute($q);
				if(!$r= $conn->Execute($q)){
					$this->msj = ERROR_SET;
					return false;
				}
			}
			$this->msj = REG_SET_OK;
			return true;
		} 	
	}
	
	function setDespacho_requisicion($conn,$recepcionDet){
		$JsonRec = new Services_JSON();
		$JsonRec=$JsonRec->decode(str_replace("\\","",$recepcionDet));
		//die($JsonRec);
		if(is_array($JsonRec->recepcionDet)){
			foreach ($JsonRec->recepcionDet as $oRecepcion){
				$sql = "SELECT cantidad_despachada FROM puser.relacion_requisiciones ";
				$sql.= "WHERE id_requisicion = '$oRecepcion[0]' AND id_producto = '$oRecepcion[2]' ";
				$row = $conn->Execute($sql);
				$despacho_anterior = $row->fields['cantidad_despachada'];
				$q="UPDATE puser.relacion_requisiciones ";
				$q.="SET cantidad_despachada = $oRecepcion[5], ";
				$q.="cantidad_despachada_anterior = $despacho_anterior ";
				$q.="WHERE id_requisicion = '$oRecepcion[0]' AND id_producto = '$oRecepcion[2]'";
				//die($q);
				//echo $q."<br>";
				$r= $conn->Execute($q);
				if(!$r= $conn->Execute($q)){
					$this->msj = ERROR_SET;
					return false;
				}
			}
			$this->msj = REG_SET_OK;
			return true;
		}
	} 
	
	function setStatusOC($conn, $id_ordcompra){
		$q="UPDATE puser.orden_compra ";
		$q.="SET status = 4 ";
		$q.="WHERE id = '$id_ordcompra'";
		$r= $conn->Execute($q);
	}
	
	function buscar($conn, 
						$id_proveedor, 
						$id_ue, 
						$fecha_desde, 
						$fecha_hasta, 
						$nrodoc,
						$nrorequi,
						 
						$orden="id",$from, $max){
		if(empty($id_proveedor) 
			&& empty($id_ue)
			&& empty($fecha_desde)
			&& empty($fecha_hasta)
			&& empty($nrodoc)
			&& empty($nrorequi)
			)
			return false;
		$q = "SELECT * FROM puser.orden_compra ";
		$q.= "WHERE  1=1 ";
		$q.= !empty($nrodoc) ? "AND nrodoc='$nrodoc' ": "";
		$q.= !empty($fecha_desde) ? "AND fecha >='".guardafecha($fecha_desde)."' ": "";
		$q.= !empty($fecha_hasta) ? "AND fecha <='".guardafecha($fecha_hasta)."' ": "";
		$q.= !empty($id_proveedor) ? "AND rif = '$id_proveedor'  ":"";
		$q.= !empty($id_ue) ? "AND id_unidad_ejecutora = '$id_ue'  ":"";
		$q.= !empty($nrorequi) ? "AND nrorequi = '$nrorequi' ":"";
		$q.= "AND (status = '2' OR status = '4') ";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from):$conn->Execute($q);
		if(!r || $r->EOF)
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
	
	function totalRegsBusqueda($conn,$id_proveedor,$id_ue,$fecha_desde,$fecha_hasta,$nrodoc,$nrorequi)
	{
		if(empty($id_proveedor) && empty($id_ue) && empty($fecha_desde) && empty($fecha_hasta) && empty($nrodoc) && empty($nrorequi))
			return 0;
			
		$q = "SELECT * FROM puser.orden_compra ";
		$q.= "WHERE  1=1 ";
		$q.= !empty($nrodoc) ? "AND nrodoc='$nrodoc' ": "";
		$q.= !empty($fecha_desde) ? "AND fecha >='".guardafecha($fecha_desde)."' ": "";
		$q.= !empty($fecha_hasta) ? "AND fecha <='".guardafecha($fecha_hasta)."' ": "";
		$q.= !empty($id_proveedor) ? "AND rif = '$id_proveedor'  ":"";
		$q.= !empty($id_ue) ? "AND id_unidad_ejecutora = '$id_ue'  ":"";
		$q.= !empty($nrorequi) ? "AND nrorequi = '$nrorequi' ":"";
		$q.= "AND (status = '2' OR status = '4') ";
		//die($q);
		$r = $conn->Execute($q);
		
		return $r->RecordCount();
	}
	
	function set($conn, $id, $nrofact, $tot_par, $comentario, $id_requisicion, $recepcion, $recepcionDet, $nrocontrol){
		$q="UPDATE puser.recepcion_orden_compra ";
		$q.="SET num_fact = '$nrofact', ";
		$q.="comentario = '$comentario', ";
		$q.="total_parcial = $tot_par[0], ";
		$q.="num_control = '$nrocontrol' ";
		$q.="WHERE id_ordcompra = '$id'";
		//die($q);
		
		$r = $conn->Execute($q);
		if($r){
			$a = $this->setDespacho_ordencompra($conn, $recepcion);
			//var_dump($a);
			if($a)
				if($this->setDespacho_requisicion($conn,$recepcionDet))
					$this->msj = REG_SET_OK;
		} else {
			$this->msj = ERROR_SET;
		}
		
	}
	
}
?>