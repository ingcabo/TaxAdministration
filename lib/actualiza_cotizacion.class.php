<?
class actualiza_cotizacion{

	// Propiedades

	var $id;
	var $id_unidad_ejecutora;
	var $id_usuario;
	var $motivo;
	var $unidad_ejecutora;
	var $ano;
	var $fecha;
	var $fecha_aprobacion;
	var $status;
	var $total;
	var $nom_status;
	
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
	var $cantidad;
	var $nombre_p;
	var $id_producto;
	var $costo;
	var $articulos_requisicion;
	

	function get($conn, $id, $escEnEje=''){
		$q="SELECT puser.gbl_requisicion.id, puser.gbl_requisicion.fecha_r, ";
		$q.="puser.gbl_requisicion.motivo, puser.gbl_requisicion.status, puser.gbl_requisicion.ano ";
		$q.="FROM puser.gbl_requisicion ";
		//$q.="Inner Join puser.unidades_ejecutoras ON puser.requisiciones.id_unidad_ejecutora = puser.unidades_ejecutoras.id ";
		$q.="WHERE puser.gbl_requisicion.id =  '$id'";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			//$this->id_unidad_ejecutora = $r->fields['id_unidad_ejecutora'];
			$this->id_usuario = $r->fields['id_usuario'];
			$this->motivo = $r->fields['motivo'];
			//$this->unidad_ejecutora = $r->fields['unidad_ejecutora'];
			$this->fecha = $r->fields['fecha_r'];
			//$this->fecha_aprobacion = $r->fields['fecha_aprobacion'];
			//$this->getRelacionProductosProveedor($conn, $id, $escEnEje);
			$this->status= $r->fields['status'];
			switch ($r->fields['status']){
			case '01':
				$this->nom_status = 'Pendiente';
				break;
			case '02':
				$this->nom_status = 'Aprobada';
				break;
			case '03':
				$this->nom_status = 'Anulada';
				break;
			case '04':
				$this->nom_status = 'Recibida por Compras';
				break;
			case '05':
				$this->nom_status = 'Requisicion General';
				break;
			case '06':
				$this->nom_status = 'Solicitud de Cotizacion';
				break;
			case '07':
				$this->nom_status = 'Cotizada';
				break;
			case '08':
				$this->nom_status = 'Orden de Compra';
				break;
			
		}
		$this->ano = $r->fields['ano'];
			return true;
		}else
			return false;
	}

function buscar($conn, 
	$fecha_desde, 
	$fecha_hasta,
	$nrorequi,
	$orden="id",
	$escEnEje='',
	$estado){
		if(//empty($id_ue)
			empty($fecha_desde)
			&& empty($fecha_hasta)
			&& empty($nrorequi)
			)
			return false;
		$q = "SELECT * FROM puser.gbl_requisicion ";
		$q.= "WHERE  1=1 ";
		$q.= !empty($fecha_desde) ? "AND puser.gbl_requisicion.fecha_r >='".guardafecha($fecha_desde)."' ": "";
		$q.= !empty($fecha_hasta) ? "AND puser.gbl_requisicion.fecha_r <='".guardafecha($fecha_hasta)."' ": "";
		$q.= !empty($nrorequi) ? "AND puser.gbl_requisicion.id = '$nrorequi' ":"";
		$q.= "AND puser.gbl_requisicion.status = '$estado' ";
		$q.= "ORDER BY $orden ";
		//die($q);
		if(!$r = $conn->Execute($q))
			return false;
		$collection=array();
		while(!$r->EOF){
			$ue = new requisicion_global;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		return $coleccion;
	}
	
	function getRelacionPartidas($conn, $id, $escEnEjec){
		$q = "SELECT puser.relacion_gbl_requisicion.*, ";
		$q.= "puser.productos.descripcion AS nombre_p, puser.productos.ultimo_costo AS costo ";
		$q.= "FROM puser.relacion_gbl_requisicion  ";
		//$q.= "INNER JOIN puser.partidas_presupuestarias ON (relacion_requisiciones.id_partida = partidas_presupuestarias.id) ";
		//$q.= "INNER JOIN puser.categorias_programaticas ON (relacion_requisiciones.id_categoria = categorias_programaticas.id) ";
		$q.= "INNER JOIN puser.productos ON (relacion_gbl_requisicion.id_producto = productos.id) ";
		$q.= "WHERE relacion_gbl_requisicion.id_gbl_requisicion='$id' ";
		//$q.= "AND categorias_programaticas.id_escenario = '$escEnEjec' ";
		//$q.= "AND partidas_presupuestarias.id_escenario = '$escEnEjec' ";
		//die($q);
		$r = $conn->Execute($q);
		while(!$r->EOF){
			$ue = new movimientos_presupuestarios;
			$ue->nrodoc = $r->fields['id_requisicion'];
			//$ue->id_partida	= $r->fields['id_partida'];
			//$ue->id_categoria = $r->fields['id_categoria'];
			//$ue->partida_presupuestaria	= $r->fields['partida_presupuestaria'];
			//$ue->categoria_programatica = $r->fields['categoria_programatica'];
			$ue->cantidad = $r->fields['cantidad'];
			$ue->costo = $r->fields['costo'];
			$ue->id_producto = $r->fields['id_producto'];
			$ue->nombre_p = $r->fields['nombre_p'];
			$ue->cantidad_despachada = $r->fields['cantidad_despachada'];
			$coleccion[] = $ue;
			$r->movenext();
			
		}
		$this->relacionPARCAT = new Services_JSON();
		$this->relacionPARCAT = is_array($coleccion) ? $this->relacionPARCAT->encode($coleccion) : false;
		return $coleccion;
	}
	
	function set_despacho($conn, $id, $cantd, $id_producto ){
		$q= "UPDATE puser.relacion_gbl_requisicion SET cantidad_despachada = $cantd WHERE id_gbl_requisicion= $id AND id_producto = $id_producto";
		if($r= $conn->Execute($q)){
			$this->msj = REG_SET_OK;
		} else {
			$this->msj = ERROR_SET;
		}
	}
	
	function get_all($conn, $escEnEje,$orden="id"){
		$q = "SELECT * FROM gbl_requisicion ";
		$q.= "ORDER BY $orden ";
		$r = $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new requisiciones;
			$ue->get($conn, $r->fields['id'], $escEnEje);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}	
	
	function set_status($conn, $status, $id){
		revision_requisicion::set_status_requisicion($conn,$status,$id);
		
		$q= "UPDATE puser.gbl_requisiciones SET status = '$status' WHERE id = '$id'";
		//die($q);
		if($r = $conn->Execute($q)){
			$this->msj = REG_SET_OK;
		} else {
			$this->msj = ERROR_SET;
			}
	}
	
	//Esta FUNCION DEVUELVE LOS PROVEEDORES QUE PUEDEN COTIZAR PARA ESTOS PRODUCTO
	function busca_proveedores_cotizacion($conn, $id_productos){
		$q="SELECT DISTINCT ON ";
		$q.="(puser.proveedores.id) puser.proveedores.id, ";
		$q.="puser.proveedores.nombre, puser.productos.id AS idp ";
		$q.="FROM puser.productos ";
		$q.="Inner Join puser.tipo_producto ON puser.productos.id_tipo_producto = puser.tipo_producto.id ";
		$q.="Inner Join puser.grupos_proveedores ON puser.tipo_producto.id_grupo_proveedor = puser.grupos_proveedores.id ";
		$q.="Inner Join puser.relacion_provee_grupo_provee ON puser.grupos_proveedores.id = puser.relacion_provee_grupo_provee.id_grupo_provee ";
		$q.="Inner Join puser.proveedores ON puser.relacion_provee_grupo_provee.id_provee = puser.proveedores.id ";
		$q.="WHERE puser.productos.id IN ($id_productos) AND puser.proveedores.status = 'A'";
		//die($q);
		$r= $conn->Execute($q);
		while(!$r->EOF){
			$pc = new revision_requisicion;
			$pc->id = $r->fields['id'];
			$pc->nombre_proveedor = $r->fields['nombre'];
			$coleccion[] = $pc;
			$r->movenext();
		}
		return $coleccion;
	}
	
	//GUARDA LOS PROVEEDORES A LOS QUE SE LES ENVIA LA SOLICITUD DE COTIZACION
	function guardaProveedoresParaCotizacion($conn, $id_requisicion, $id_proveedores){
	  //die(print_r($id_proveedores));
	  $q="DELETE FROM puser.proveedores_requisicion WHERE id_requisicion = '$id_requisicion'";
	  $aux= $conn->Execute($q);	
	  if(is_array($id_proveedores)){
		foreach ($id_proveedores as $idp){
			$q="INSERT INTO puser.proveedores_requisicion (id_requisicion, id_proveedor) ";
			$q.="VALUES ('$id_requisicion', $idp)";
			//die($q);
			$r= $conn->Execute($q);
		}
	  }
	}			 
		
	function getArticulosCotizacion($conn, $id_requisicion, $id_proveedor){
		$q= "SELECT puser.productos.descripcion, ";
		$q.="puser.proveedores_requisicion.costo, ";
		$q.="puser.proveedores_requisicion.iva, ";
		$q.="puser.proveedores_requisicion.id_producto, ";
		$q.="puser.relacion_gbl_requisicion.cantidad ";
		$q.="FROM puser.proveedores_requisicion ";
		$q.="Inner Join puser.productos ON puser.proveedores_requisicion.id_producto = puser.productos.id ";
		$q.="Inner Join puser.relacion_gbl_requisicion ON puser.proveedores_requisicion.id_requisicion = puser.relacion_gbl_requisicion.id_gbl_requisicion ";
		$q.="AND puser.proveedores_requisicion.id_producto = puser.relacion_gbl_requisicion.id_producto ";
		$q.="WHERE puser.proveedores_requisicion.id_requisicion =  '$id_requisicion' AND ";
		$q.="puser.proveedores_requisicion.id_proveedor =  '$id_proveedor' ";
		$q.="ORDER BY proveedores_requisicion.id ";
		//die($q);
		$r = $conn->Execute($q);
		while(!$r->EOF){
			$ac = new actualiza_cotizacion;
				$ac->descripcion = $r->fields['descripcion'];
				$ac->costo = $r->fields['costo'];
				$ac->iva = $r->fields['iva'];
				$ac->id = $r->fields['id_producto'];
				$ac->cantidad = $r->fields['cantidad'];
				$coleccion[] = $ac;
			$r->movenext();
		}
		return $coleccion;
	}
	
	
	function setCostoArticuloProveedor($conn, $id_requisicion, $oCotizacion, $id_proveedor){
		$JsonRec = new Services_JSON();
		$JsonRec=$JsonRec->decode(str_replace("\\","",$oCotizacion));
		if(is_array($JsonRec->cotizacion)){
			foreach ($JsonRec->cotizacion as $oCO_Aux){
			$costo = guardaFloat($oCO_Aux[1]);
				$q="UPDATE puser.proveedores_requisicion SET ";
				$q.="costo = $costo, ";
				$q.="iva = $oCO_Aux[2] ";
				$q.="WHERE id_requisicion = '$id_requisicion' AND id_producto = '$oCO_Aux[0]' ";
				$q.="AND id_proveedor = '$id_proveedor'";
				//die($q);
				//echo $q."<br>";
				$r= $conn->Execute($q);
				if(!$r= $conn->Execute($q)){
					$this->msj = ERROR_SET;
				}
			}
			$this->msj = REG_SET_OK;
		}
	}
					
}

?>
