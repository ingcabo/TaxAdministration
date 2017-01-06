<?
class analisis_cotizacion{

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
			$this->fecha_aprobacion = $r->fields['fecha_aprobacion'];
			//$this->getRelacionProductosProveedor($conn, $id, $escEnEje);
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
		if(empty($fecha_desde)
			&& empty($fecha_hasta)
			&& empty($nrorequi)
			)
			return false;
		$q = "SELECT * FROM puser.gbl_requisicion ";
		$q.= "WHERE  1=1 ";
		$q.= !empty($fecha_desde) ? "AND puser.gbl_requisicion.fecha_r >='".guardafecha($fecha_desde)."' ": "";
		$q.= !empty($fecha_hasta) ? "AND puser.gbl_requisicion.fecha_r <='".guardafecha($fecha_hasta)."' ": "";
		//$q.= !empty($id_ue) ? "AND puser.requisiciones.id_unidad_ejecutora = '$id_ue'  ":"";
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
		$q.= "WHERE relacion_requisiciones.id_requisicion='$id' ";
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
	
	/*function set_despacho($conn, $id, $cantd, $id_producto ){
		$q= "UPDATE puser.relacion_gbl_requisicion SET cantidad_despachada = $cantd WHERE id_requisicion= $id AND id_producto = $id_producto";
		if($r= $conn->Execute($q)){
			$this->msj = REG_SET_OK;
		} else {
			$this->msj = ERROR_SET;
		}
	}*/
	
	function get_all($conn, $escEnEje,$orden="id"){
		$q = "SELECT * FROM gbl_requisicion ";
		$q.= "ORDER BY $orden ";
		$r = $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new requisicion_global;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}	
	
	function set_status($conn, $status, $id){
		
		revision_requisicion::set_status_requisicion($conn,$status,$id);
		$q= "UPDATE puser.gbl_requisicion SET status = '$status' WHERE id = '$id'";
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
		$q.="puser.proveedores_requisicion.id_producto, ";
		$q.="puser.relacion_requisiciones.cantidad ";
		$q.="FROM puser.proveedores_requisicion ";
		$q.="Inner Join puser.productos ON puser.proveedores_requisicion.id_producto = puser.productos.id ";
		$q.="Inner Join puser.relacion_requisiciones ON puser.proveedores_requisicion.id_requisicion = puser.relacion_requisiciones.id_requisicion AND puser.proveedores_requisicion.id_producto = puser.relacion_requisiciones.id_producto ";
		$q.="WHERE puser.proveedores_requisicion.id_requisicion =  '$id_requisicion' AND ";
		$q.="puser.proveedores_requisicion.id_proveedor =  '$id_proveedor' ";
		//die($q);
		$r = $conn->Execute($q);
		while(!$r->EOF){
			$ac = new actualiza_cotizacion;
				$ac->descripcion = $r->fields['descripcion'];
				$ac->costo = $r->fields['costo'];
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
		//die(print_r($oCotizacion));
		if(is_array($JsonRec->cotizacion)){
			foreach ($JsonRec->cotizacion as $oCO_Aux){
			$costo = guardaFloat($oCO_Aux[1]);
				$q="UPDATE puser.proveedores_requisicion SET ";
				$q.="costo = $costo ";
				$q.="WHERE id_requisicion = '$id_requisicion' AND id_producto = '$oCO_Aux[0]' ";
				$q.="AND id_proveedor = '$id_proveedor'";
				//die($q);
				$r= $conn->Execute($q);
				if(!$r= $conn->Execute($q)){
					$this->msj = ERROR_SET;
				}
			}
			$this->msj = REG_SET_OK;
		}
	}
	
	function getArticulosporProveedor($conn, $id_requisicion, $id_proveedor){
		$q="SELECT puser.productos.descripcion, ";
		$q.="puser.proveedores_requisicion.id_producto, ";
		$q.="puser.proveedores_requisicion.costo, ";
		$q.="puser.proveedores_requisicion.iva, ";
		$q.="puser.proveedores_requisicion.id AS id_pr, ";
		$q.="puser.relacion_gbl_requisicion.cantidad, ";
		$q.="puser.productos.unidad_medida ";
		$q.="FROM puser.proveedores_requisicion ";
		$q.="Inner Join puser.productos ON puser.proveedores_requisicion.id_producto = puser.productos.id ";
		$q.="Inner Join puser.relacion_gbl_requisicion ON puser.proveedores_requisicion.id_requisicion = puser.relacion_gbl_requisicion.id_gbl_requisicion ";
		$q.="AND puser.proveedores_requisicion.id_producto = puser.relacion_gbl_requisicion.id_producto ";
		$q.="WHERE puser.proveedores_requisicion.id_requisicion = '$id_requisicion' ";
		$q.="AND puser.proveedores_requisicion.id_proveedor = '$id_proveedor' ";
		$q.="ORDER BY descripcion";
		//die($q);
		$r= $conn->Execute($q);
		while(!$r->EOF){
			$ac = new analisis_cotizacion;
				$ac->id_producto = $r->fields['id_producto'];
				$ac->descripcion = $r->fields['descripcion'];
				$ac->costo = $r->fields['costo'];
				$ac->iva = $r->fields['iva'];
				$ac->id_pr = $r->fields['id_pr'];
				$ac->cantidad = $r->fields['cantidad'];
				$ac->unidad_medida = $r->fields['unidad_medida'];
			$coleccion[]= $ac;
			$r->movenext();
		}
		return $coleccion;
	}
	
	function getProveedoresporRequisicion($conn, $id_requisicion){
		$q="SELECT DISTINCT puser.proveedores_requisicion.id_proveedor, ";
		$q.="puser.proveedores.nombre ";
		$q.="FROM puser.proveedores_requisicion ";
		$q.="INNER JOIN puser.proveedores ON puser.proveedores_requisicion.id_proveedor = puser.proveedores.id ";
		$q.="WHERE puser.proveedores_requisicion.id_requisicion = '$id_requisicion'";
		//die($q);
		$r = $conn->Execute($q);
		while(!$r->EOF){
			$ac = new actualiza_cotizacion;
				$ac->id_proveedor = $r->fields['id_proveedor'];
				$ac->nombre = $r->fields['nombre'];
			$coleccion[] = $ac;
			$r->movenext();
		}
		return $coleccion;
	}
	
	function getCotizacionesGanadoras($conn, $id_requisicion){
		/*ESTE PROCESO OBTIENE EL GANADOR POR PRODUCTO MAS ECONOMICO
		$q="SELECT DISTINCT ON (puser.proveedores_requisicion.id_producto) puser.proveedores_requisicion.id_producto AS idprod, ";
		$q.="puser.proveedores_requisicion.costo, ";
		$q.="Min(puser.proveedores_requisicion.id_proveedor) AS proveedor, "; 
		$q.="(puser.proveedores_requisicion.costo*puser.relacion_requisiciones.cantidad) AS total, ";
		$q.="puser.relacion_requisiciones.cantidad, ";
		$q.="puser.proveedores_requisicion.id ";
		$q.="FROM puser.relacion_requisiciones ";
		$q.="Inner Join puser.proveedores_requisicion ON puser.relacion_requisiciones.id_producto = puser.proveedores_requisicion.id_producto ";
		$q.="AND puser.relacion_requisiciones.id_requisicion = puser.proveedores_requisicion.id_requisicion ";
		$q.="WHERE puser.relacion_requisiciones.id_requisicion =  '$id_requisicion' ";
		$q.="AND puser.proveedores_requisicion.costo > 0 ";
		$q.="GROUP BY ";
		$q.="puser.proveedores_requisicion.id_producto, puser.proveedores_requisicion.costo,";
		$q.="puser.relacion_requisiciones.cantidad,puser.proveedores_requisicion.id";*/
		//ESTE PROCESO OBTIENE EL LA REQUISICION CUYO COSTO TOTAL SEA MENOR
		$q = "SELECT DISTINCT id_proveedor,  SUM((relacion_gbl_requisicion.cantidad * costo) + ((relacion_gbl_requisicion.cantidad * costo) * (iva/100)))  AS total ";
		$q.= "FROM puser.proveedores_requisicion ";
		$q.= "Inner Join puser.relacion_gbl_requisicion ON puser.proveedores_requisicion.id_requisicion = puser.relacion_gbl_requisicion.id_gbl_requisicion AND puser.proveedores_requisicion.id_producto = puser.relacion_gbl_requisicion.id_producto ";
		$q.= "WHERE puser.proveedores_requisicion.id_requisicion =  '$id_requisicion' AND id_proveedor = id_proveedor ";
		$q.= "GROUP BY id_proveedor ";
		$q.= "ORDER BY total ";
		//die($q);
		//echo $q."<br>";
		if($row= $conn->Execute($q))
			$winner = $row->fields['id_proveedor'];
		else
			return false;
		$q = "SELECT pr.id_producto, ((pr.costo * rr.cantidad) + (pr.costo * rr.cantidad * (pr.iva/100))) as total FROM puser.proveedores_requisicion pr ";
		$q.= "Inner Join puser.relacion_gbl_requisicion rr ON pr.id_requisicion = rr.id_gbl_requisicion AND pr.id_producto = rr.id_producto ";
		$q.= "WHERE pr.id_proveedor = '$winner' AND pr.id_requisicion = '$id_requisicion'";
		//die($q);
		$r = $conn->Execute($q);
		$i=0;
		$sql="DELETE FROM puser.ganadores_co_re WHERE id_requisicion = '$id_requisicion'";
		$res= $conn->Execute($sql);
		while(!$r->EOF){
			$ac = new analisis_cotizacion;
			$ac->id_producto = $r->fields['id_producto'];
			$ac->costo = $r->fields['total'];
			$ac->id_proveedor = $winner;
			$coleccion[] = $ac;
			$sql="INSERT INTO puser.ganadores_co_re (id_requisicion, id_proveedor, id_producto) ";
			$sql.="VALUES ('$id_requisicion', '".$winner."', '".$r->fields['id_producto']."')";
			//die($sql);
				$row = $conn->Execute($sql);
				if(!$row){
					$this->msj = ERROR_SET;
				}
			$r->movenext();
			$i++;
		}
		/*if(!$r){
			$sql="UPDATE requisiciones SET status = '07' WHERE id = '$id_requisicion'";
			$this->creaOrdenCompra($conn, $id_requisicion);
			}*/
		return $winner;
		}
		
		function creaOrdenCompra($conn, $id_requisicion, $escEnEje){
			//ESTA FUNCION SE HACE PARA UBICAR LA UNIDAD EJECUTORA QUE GENERA MAS GASTO DE LA REQUISICION GLOBAL
			//YA QUE ESTA ES LA QUE DEBE FIGURAR EN LA ORDEN DE COMPRA SEGUN LO ACORDADO CON JEFE DE COMPRAS EL 14/03/07 .IODG.
			$sql = "SELECT SUM(total), categoria, unidad FROM ";
			$sql.= "(SELECT   (MIN(a.costo) * c.cantidad) AS total, c.id_categoria AS categoria, c.cantidad, b.id_unidad_ejecutora AS unidad FROM puser.proveedores_requisicion a ";
			$sql.= "INNER JOIN puser.requisiciones b ON (a.id_requisicion = b.nroreqgbl) ";
			$sql.= "INNER JOIN puser.relacion_requisiciones c ON (b.id = c.id_requisicion) ";
			$sql.= "WHERE a.id_requisicion = '$id_requisicion' AND a.id_producto = c.id_producto ";
			$sql.= "GROUP BY 2,3,4) AS totales ";
			$sql.= "GROUP BY categoria, unidad ";
			$sql.= "ORDER BY 1 DESC LIMIT 1";
			//echo $sql."<br>";
			$row = $conn->Execute($sql);
			if($row){
				$ue_mayor_gasto = $row->fields['unidad']; 
			} else {
				return false;
			}
			
			
			$q = "SELECT DISTINCT ON (puser.ganadores_co_re.id_proveedor) ";	
			$q.="puser.ganadores_co_re.id_proveedor, ";
			$q.="puser.ganadores_co_re.id_requisicion, ";
			$q.="puser.proveedores.rif, ";
			$q.="puser.gbl_requisicion.ano ";
			//$q.="puser.requisiciones.id_unidad_ejecutora ";
			$q.="FROM puser.ganadores_co_re ";
			$q.="Inner Join puser.proveedores ON puser.ganadores_co_re.id_proveedor = puser.proveedores.id ";
			$q.="Inner Join puser.gbl_requisicion ON puser.ganadores_co_re.id_requisicion = puser.gbl_requisicion.id ";
			$q.="WHERE puser.ganadores_co_re.id_requisicion = '$id_requisicion'";
			//die($q);
			$r = $conn->Execute($q);
			while(!$r->EOF){
				$sql = "INSERT INTO puser.orden_compra (fecha, ano, rif, id_unidad_ejecutora, status, nrorequi, observaciones) ";
				$sql.= "VALUES ('".date("Y-m-d")."', '".$r->fields['ano']."', '".$r->fields['id_proveedor']."', ";
				$sql.="'$ue_mayor_gasto', '1', '$id_requisicion', 'Requisicion # $id_requisicion')";
				//die($sql);
				//echo $sql."<br>";
				$row= $conn->Execute($sql);
					$nrodoc = getLastId($conn, 'id', 'orden_compra');
					$this->generaRelacionOrdenCompra($conn, $r->fields['id_proveedor'], $nrodoc, $escEnEje, $id_requisicion);
				
				$r->movenext();
			} 
			$this->msj = REG_ADD_OK;
			return true;
		}
	
	function generaRelacionOrdenCompra($conn, $id_proveedor, $nrodoc, $escEnEje, $id_requisicion){
		/*ESTE QUERY SE UTILIZA PARA BUSCAR EL GANADOR POR MENOR COSTO DE CADA PRODUCTO
		$q="SELECT SUM(total) AS total,proveedor,categoria,partida, idparcat, id_producto ";
		$q.="FROM ( SELECT DISTINCT ON (puser.proveedores_requisicion.id_producto) ";
		$q.="Min(puser.proveedores_requisicion.id_proveedor) AS proveedor, ";
		$q.="puser.relacion_requisiciones.id_categoria as categoria, ";
		$q.="puser.relacion_requisiciones.id_partida as partida, ";
		$q.="((puser.proveedores_requisicion.costo * puser.relacion_requisiciones.cantidad) ";
		$q.="+ (puser.proveedores_requisicion.costo * ";
		$q.="puser.proveedores_requisicion.iva/100) * ";
		$q.="puser.relacion_requisiciones.cantidad) AS total, ";
		$q.="puser.proveedores_requisicion.id_producto AS id_producto, ";
		$q.="( SELECT puser.relacion_pp_cp.id FROM puser.relacion_pp_cp WHERE ";
		$q.="puser.relacion_pp_cp.id_categoria_programatica = puser.relacion_requisiciones.id_categoria ";
		$q.="AND puser.relacion_pp_cp.id_partida_presupuestaria = puser.relacion_requisiciones.id_partida ";
		$q.="AND puser.relacion_pp_cp.id_escenario = $escEnEje ) as idparcat ";
		$q.="FROM puser.relacion_requisiciones ";
		$q.="Inner Join puser.proveedores_requisicion ON puser.relacion_requisiciones.id_producto = ";
		$q.="puser.proveedores_requisicion.id_producto ";
		$q.="AND puser.relacion_requisiciones.id_requisicion = puser.proveedores_requisicion.id_requisicion ";
		$q.="WHERE puser.relacion_requisiciones.id_requisicion = '$id_requisicion' ";
		$q.="AND puser.proveedores_requisicion.costo > 0 ";
		$q.="GROUP BY ";
		$q.="puser.proveedores_requisicion.id_producto, ";
		$q.="puser.proveedores_requisicion.costo, ";
		$q.="puser.relacion_requisiciones.id_categoria, ";
		$q.="puser.relacion_requisiciones.id_partida, ";
		$q.="puser.relacion_requisiciones.cantidad, ";
		$q.="puser.proveedores_requisicion.iva, ";
		$q.="idparcat ";
		$q.=") AS general ";
		$q.="GROUP BY 2,3,4,5,6";*/
		/*$q = "SELECT pr.id_requisicion, pr.id_proveedor AS proveedor, pr.costo, pr.iva, pr.id_producto, rr.id_categoria as categoria, rr.id_partida as partida, rr.cantidad, pr.costo AS monto_base,  ((pr.costo * rr.cantidad) * (pr.iva/100)) AS precio_iva, ";
		$q.= "((pr.costo * rr.cantidad) + ((pr.costo * rr.cantidad) * (pr.iva/100))) AS monto, ( SELECT puser.relacion_pp_cp.id FROM puser.relacion_pp_cp WHERE puser.relacion_pp_cp.id_categoria_programatica = rr.id_categoria AND puser.relacion_pp_cp.id_partida_presupuestaria = rr.id_partida AND puser.relacion_pp_cp.id_escenario = '$escEnEje' ) AS idparcat ";
		$q.= "FROM puser.proveedores_requisicion pr ";
		$q.= "INNER JOIN puser.relacion_requisiciones rr ON (pr.id_requisicion = rr.id_requisicion AND pr.id_producto = rr.id_producto) ";
		$q.= "WHERE pr.id_proveedor = $id_proveedor AND pr.id_requisicion = '$id_requisicion' ";*/
		$q = "SELECT d.costo,a.id_producto,a.id_categoria AS categoria,a.id_partida AS partida ,a.cantidad, d.iva AS iva, d.costo as monto_base, (d.costo * a.cantidad) * (d.iva /100) as precio_iva, (d.costo * a.cantidad)+ (d.costo * a.cantidad * d.iva /100) as monto, ";
		$q.= " ( SELECT puser.relacion_pp_cp.id FROM puser.relacion_pp_cp WHERE puser.relacion_pp_cp.id_categoria_programatica = a.id_categoria AND puser.relacion_pp_cp.id_partida_presupuestaria = a.id_partida AND puser.relacion_pp_cp.id_escenario = '$escEnEje' ) AS idparcat "; 
		$q.= "FROM ((puser.relacion_requisiciones AS a "; 
		$q.= "INNER JOIN puser.requisiciones AS b ON a.id_requisicion= b.id) "; 
		$q.= "INNER JOIN puser.gbl_requisicion as c on b.nroreqgbl = c.id) "; 
		$q.= "INNER JOIN puser.proveedores_requisicion AS d ON d.id_requisicion = c.id AND a.id_producto=d.id_producto "; 
		$q.= "WHERE d.id_requisicion='$id_requisicion' AND d.id_proveedor=$id_proveedor ";
		
		//die($q);
		$r = $conn->Execute($q);
		while(!$r->EOF){
			
				$sql="INSERT INTO puser.relacion_ordcompra (id_ord_compra, id_categoria_programatica, id_partida_presupuestaria, monto, idparcat, id_producto, cantidad, precio_base, precio_iva, iva_porc) ";
				$sql.="VALUES ('$nrodoc', '".$r->fields['categoria']."', '".$r->fields['partida']."', '".$r->fields['monto']."', '".$r->fields['idparcat']."', '".$r->fields['id_producto']."', '".$r->fields['cantidad']."', '".$r->fields['monto_base']."', '".$r->fields['precio_iva']."', '".$r->fields['iva']."')";
				//die($sql);
				//echo("relacion: ".$sql."\n");
				$row= $conn->Execute($sql);
				$sql2 = "UPDATE puser.productos SET ultimo_costo = '".$r->fields['costo']."' WHERE id = '".$r->fields['id_producto']."'";
				//echo $sql2."<br>";
				$row2 = $conn->Execute($sql2);
			
			
			$r->movenext();
		}
		//die();
		//die("\n aqui");
		//ESTA FUNCIO SE LLAMABA EN EL CASO DE QUE SE COMPRARA EL PRODUCTO DE MENOR COSTO
		//$this->generaRelacionOrdenCompraProductos($conn, $nrodoc, $id_requisicion, $id_proveedor);
		if($r){
				return true;
		}else {
				return false;
		}
	}	
	
	function generaRelacionOrdenCompraProductos($conn, $id_ordcompra, $id_requisicion, $id_proveedor){
		//die("aqui ".$id_ordcompra);
		$q="SELECT puser.proveedores_requisicion.id_producto, ";
		$q.="puser.proveedores_requisicion.costo, ";
		$q.="puser.proveedores_requisicion.iva, ";
		$q.="puser.relacion_requisiciones.cantidad ";
		$q.="FROM puser.proveedores_requisicion ";
		$q.="Inner Join puser.ganadores_co_re ON "; 
		$q.="puser.ganadores_co_re.id_requisicion = puser.proveedores_requisicion.id_requisicion ";
		$q.="AND puser.ganadores_co_re.id_proveedor = puser.proveedores_requisicion.id_proveedor ";
		$q.="AND puser.ganadores_co_re.id_producto = puser.proveedores_requisicion.id_producto ";
		$q.="Inner Join puser.relacion_requisiciones ON ";
		$q.="puser.proveedores_requisicion.id_requisicion = puser.relacion_requisiciones.id_requisicion ";
		$q.="AND puser.proveedores_requisicion.id_producto = puser.relacion_requisiciones.id_producto ";
		$q.="WHERE puser.ganadores_co_re.id_requisicion = '$id_requisicion' ";
		$q.="AND puser.ganadores_co_re.id_proveedor = '$id_proveedor'";
		//die($q);
		$r= $conn->Execute($q);
		while(!$r->EOF){
			$precio_iva= $r->fields['cantidad'] * ($r->fields['costo'] * $r->fields['iva']/100);
			//$precio_total= $r->fields['cantidad'] * $r->fields['costo'];
			$sql="UPDATE puser.relacion_ordcompra SET cantidad = ".$r->fields['cantidad'].", precio_base = ".$r->fields['costo'].", precio_iva = ".$precio_iva.", iva_porc = ".$r->fields['iva']." ";
			$sql.="WHERE id_ord_compra = '$id_ordcompra' AND id_producto = '".$r->fields['id_producto']."' ";
			//die($sql);
			$row= $conn->Execute($sql);
			$r->movenext();
		}
		if($r){
			return true;
			} else {
				return false;
			}	
			
	}
			
					
}

?>