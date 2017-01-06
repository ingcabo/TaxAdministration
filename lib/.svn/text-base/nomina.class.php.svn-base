<?
class nomina{

	// Propiedades

	var $id;
	var $descripcion;
	var $observaciones;
	var $id_tipo_documento;
	var $id_proveedor;
	var $proveedor; // nombre del proveedor
	var $dir_proveedor;
	var $nro_ref;
	var $id_unidad_ejecutora;
	var $id_usuario;
	var $fecha;
	var $fecha_pago;
	var $rif;
	var $nrodoc;

	var $total;
	
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

	function get($conn, $id, $escEnEje=''){
		$q = "SELECT nomina.*, unidades_ejecutoras.descripcion AS unidad_ejecutora, ";
		$q.= "proveedores.id AS id_proveedor, proveedores.nombre AS proveedor, proveedores.direccion AS dir_proveedor ";
		$q.= "FROM nomina ";
		$q.= "INNER JOIN unidades_ejecutoras ON (nomina.id_unidad_ejecutora = unidades_ejecutoras.id) ";
		$q.= "INNER JOIN proveedores ON (nomina.id_proveedor = proveedores.id) ";
		$q.= "WHERE nomina.id='$id'  ";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->descripcion = $r->fields['descripcion'];
			$this->observaciones = $r->fields['observaciones'];
			$this->id_tipo_documento = $r->fields['id_tipo_documento'];
			$this->nro_ref = $r->fields['nro_ref'];
			$this->id_unidad_ejecutora = $r->fields['id_unidad_ejecutora'];
			$this->unidad_ejecutora = $r->fields['unidad_ejecutora'];
			$this->id_usuario = $r->fields['id_usuario'];
			$this->fecha = $r->fields['fecha'];
			$this->fecha_entrega = $r->fields['fecha_pago'];
			$this->rif = $r->fields['rif'];
			$this->id_proveedor = $r->fields['id_proveedor'];
			$this->proveedor = $r->fields['proveedor'];
			$this->dir_proveedor = $r->fields['dir_proveedor'];
			$this->nrodoc = $r->fields['nrodoc'];
			return true;
		}else
			return false;
	}

	function get_all($conn, $escEnEje,$orden="id"){
		$q = "SELECT * FROM nomina ";
		$q.= "ORDER BY $orden ";
		$r = $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new nomina;
			$ue->get($conn, $r->fields['id'], $escEnEje);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, 
						$descripcion,
						$observaciones,
						$id_tipo_documento,
						$nro_ref,
						$id_unidad_ejecutora,
						$id_usuario,
						$fecha,
						$fecha_pago,
						$id_proveedor,
						$aIdParCat,
						$aCategoriaProgramatica,
						$aPartidaPresupuestaria,
						$aMontoPartida){
		$q = "INSERT INTO nomina ";
		$q.= "( ";
		$q.= "descripcion, ";
		$q.= "observaciones, ";
		$q.= "id_tipo_documento, ";
		$q.= "nro_ref, ";
		$q.= "id_unidad_ejecutora, ";
		$q.= "id_usuario, ";
		$q.= "fecha, ";
		$q.= "fecha_pago, ";
		$q.= "id_proveedor ";
		$q.= ") ";
		$q.= "VALUES ";
		$q.= "( ";
		$q.= " '$descripcion', ";
		$q.= " '$observaciones', ";
		$q.= " '$id_tipo_documento', ";
		$q.= " '$nro_ref', ";
		$q.= " '$id_unidad_ejecutora', ";
		$q.= " '$id_usuario', ";
		$q.= " '$fecha', ";
		$q.= " '$fecha_pago', ";
		$q.= " '$id_proveedor' ";
		$q.= ") ";
		//die($q);
		$r = $conn->execute($q);
		if($r){
				$nrodoc = getLastId($conn, 'id', 'nomina'); 
				if(
				$this->addRelacionPartidas($conn, 
																		$aIdParCat,
																		$aCategoriaProgramatica,
																		$aPartidaPresupuestaria,
																		$nrodoc,
																		$aMontoPartida)
																	){
					$this->msj = REG_ADD_OK;
					return true;
				}
		}else{
			$this->msj = ERROR_ADD;
			return false;
		}
	}

	function set($conn, 
						$nrodoc, 
						$descripcion,
						$observaciones,
						$id_tipo_documento,
						$nro_ref,
						$id_unidad_ejecutora,
						$fecha,
						$fecha_pago,
						$id_proveedor,
						$aIdParCat,
						$aCategoriaProgramatica,
						$aPartidaPresupuestaria,
						$aMontoPartida){
		$q = "UPDATE nomina SET  ";
		$q.= "descripcion = '$descripcion', ";
		$q.= "observaciones = '$observaciones', ";
		$q.= "id_tipo_documento = '$id_tipo_documento', ";
		$q.= "nro_ref = '$nro_ref', ";
		$q.= "id_unidad_ejecutora = '$id_unidad_ejecutora', ";
		$q.= "fecha = '$fecha', ";
		$q.= "fecha_pago = '$fecha_pago', ";
		$q.= "id_proveedor = '$id_proveedor' ";
		$q.= "WHERE id='$nrodoc' ";	
		//die($q);
		if($conn->Execute($q)){
			if($this->delRelacionPartidas($conn, $nrodoc)){
				if($this->addRelacionPartidas($conn, 
																		$aIdParCat,
																		$aCategoriaProgramatica,
																		$aPartidaPresupuestaria,
																		$nrodoc,
																		$aMontoPartida)){
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
		$q = "DELETE FROM nomina WHERE id='$id'";
		if($conn->Execute($q)){
			$this->msj = REG_DEL_OK;
			return true;
		}else{
			$this->msj = ERROR_DEL;
			return false;
		}
	}
	
	function addRelacionPartidas($conn, 
															 $aIdParCat,
															 $aCategoriaProgramatica,
															 $aPartidaPresupuestaria,
															 $nrodoc,
															 $aMonto){
		for($i = 0; $i<count($aCategoriaProgramatica); $i++){
			$q = "INSERT INTO relacion_nomina ";
			$q.= "( id_parcat, id_categoria_programatica, id_partida_presupuestaria, id_nomina, monto) ";
			$q.= "VALUES ";
			$q.= "('$aIdParCat[$i]', '$aCategoriaProgramatica[$i]', '$aPartidaPresupuestaria[$i]', '$nrodoc', ".guardafloat($aMonto[$i]).") ";
			//echo($q."<br>");
			$r = $conn->Execute($q);
		} 
		if($r)
			return true;
		else
			return false;
	}

	function delRelacionPartidas($conn, $nrodoc){
		$q = "DELETE FROM relacion_nomina WHERE id_nomina='$nrodoc'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	function getRelacionPartidas($conn, $id, $escEnEjec){
		$q = "SELECT relacion_nomina.*, partidas_presupuestarias.descripcion AS partida_presupuestaria,  ";
		$q.= "categorias_programaticas.descripcion AS categoria_programatica ";
		$q.= "FROM relacion_nomina  ";
		$q.= "INNER JOIN partidas_presupuestarias ON (relacion_nomina.id_partida_presupuestaria = partidas_presupuestarias.id) ";
		$q.= "INNER JOIN categorias_programaticas ON (relacion_nomina.id_categoria_programatica = categorias_programaticas.id) ";
		$q.= "WHERE relacion_nomina.id_nomina='$id' ";
		$q.= "AND categorias_programaticas.id_escenario = '$escEnEjec' ";
		$q.= "AND partidas_presupuestarias.id_escenario = '$escEnEjec' ";
		//die($q);
		$r = $conn->Execute($q);
		while(!$r->EOF){
			$ue = new movimientos_presupuestarios;
			$ue->nrodoc = $r->fields['nrodoc'];
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
	
	function aprobar($conn, $id_nomina,
										$id_usuario,
										$id_unidad_ejecutora,
										$ano,
										$descripcion,
										$tipdoc,
										$nroref,
										$fechadoc,
										$status,
										$id_proveedor,
										$aIdParCat,
										$aCategoriaProgramatica,
										$aPartidaPresupuestaria,
										$aMonto){
		// chequeo la disponibilidad actual en la partida, si en alguna no hay disponibilidad no se aprueba la orden
		for($i = 0; $i < count($aIdParCat); $i++){
			$q = "SELECT relacion_pp_cp.disponible FROM relacion_pp_cp WHERE id = '$aIdParCat[$i]' ";
			$r = $conn->Execute($q);
			if($r){
				if($r->fields['disponible'] < guardafloat($aMonto[$i])){
					$this->msj = ERROR_NOMINA_APR_NO_DISP;
					return false;
				}
			}
		}
		$oProveedor = new proveedores;
		$oProveedor->get($conn, $id_proveedor);
		
		$nrodoc = movimientos_presupuestarios::getNroDoc($conn, $tipdoc);
		$q = "INSERT INTO movimientos_presupuestarios ";
		$q.= "(id_usuario, id_unidad_ejecutora, ano, descripcion, nrodoc, tipdoc, nroref, ";
		$q.= "fechadoc, status, rif) ";
		$q.= "VALUES ";
		$q.= "('$id_usuario', '$id_unidad_ejecutora', '$ano', '$descripcion', '$nrodoc', '$tipdoc', '$nroref', ";
		$q.= " '$fechadoc', '$status', '$oProveedor->rif') ";
		//die($q);
		$r = $conn->Execute($q);
		if($r){
				if(movimientos_presupuestarios::add_relacion($conn, 
																										$aCategoriaProgramatica,
																										$aPartidaPresupuestaria,
																										$nrodoc,
																										$aMonto) &&
					$this->setNrodoc($conn, $nrodoc, $id_nomina) &&
					$this->setFechaAprobacion($conn, $id_nomina) )
				{
					if(relacion_pp_cp::set_desde_orden_servicio_trabajo($conn, $aIdParCat, $aMonto)){
						$this->msj = NOMINA_APROBADA;
						return true;
					}
				}
		}else{
			$this->msj = ERROR_APROBADA;
			return false;
		}
	}
	
	function setNrodoc($conn, $nrodoc, $id){
		$q = "UPDATE nomina SET nrodoc = '$nrodoc' WHERE id='$id'";
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	function setFechaAprobacion($conn, $id){
		$q = "UPDATE nomina SET fecha_aprobacion = now() WHERE id='$id'";
		if($conn->Execute($q))
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
	$descripcion, 
	$orden="id"){
		if(empty($id_proveedor) 
			&& empty($id_ue)
			&& empty($fecha_desde)
			&& empty($fecha_hasta)
			&& empty($nrodoc)
			&& empty($descripcion)
			)
			return false;
		$q = "SELECT * FROM nomina ";
		$q.= "WHERE  1=1 ";
		$q.= !empty($nrodoc) ? "AND nrodoc='$nrodoc' ": "";
		$q.= !empty($fecha_desde) ? "AND fecha >='".guardafecha($fecha_desde)."' ": "";
		$q.= !empty($fecha_hasta) ? "AND fecha <='".guardafecha($fecha_hasta)."' ": "";
		$q.= !empty($id_proveedor) ? "AND id_proveedor = '$id_proveedor'  ":"";
		$q.= !empty($id_ue) ? "AND id_unidad_ejecutora = '$id_ue'  ":"";
		$q.= !empty($descripcion) ? "AND descripcion ILIKE '%$descripcion%' ":"";
		$q.= "ORDER BY $orden ";
		//die($q);
		if(!$r = $conn->Execute($q))
			return false;
		$collection=array();
		while(!$r->EOF){
			$ue = new nomina;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		return $coleccion;
	}
}
?>
