<?
class tipo_producto{

	// Propiedades

	var $id;
	var $descripcion;
	var $observacion;
	var $partida;
	var $id_familia_producto;
	var $codigo;
	var $id_partidas_presupuestarias;
	var $id_grupo_proveedor;
	var $codigo_familia;
	var $codFamiliaProd;
	var $msj;
	var $total;

	function get($conn, $id){
		$q = "SELECT * FROM tipo_producto ";
		$q.= "WHERE id='$id'";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->descripcion = $r->fields['descripcion'];
			$this->observacion = $r->fields['observacion'];
			$this->partida = $r->fields['partida'];
			$this->id_familia_producto = $r->fields['id_familia_producto'];
			$this->codigo = $r->fields['codigo'];	
			$this->id_partidas_presupuestarias = $r->fields['id_partidas_presupuestarias'];
			$this->id_grupo_proveedor = $r->fields['id_grupo_proveedor'];
			$this->codFamiliaProd = $this->getFamilia($conn, $r->fields['id_familia_producto']);											
			return true;
		}else
			return false;
	}

	function get_all($conn, $from=0, $max=0,$orden="id"){
		$q = "SELECT * FROM tipo_producto ";
		$q.= "ORDER BY $orden ";
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new tipo_producto;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $tipo_producto_clasif,$descripcion, $observacion,$today, $partidas_presupuestarias, $grupo_proveedor, $codigo){
		$q = "INSERT INTO puser.tipo_producto ";
		$q.= "(descripcion, observacion, id_familia_producto, fecha, id_partidas_presupuestarias, id_grupo_proveedor, codigo) ";
		$q.= " VALUES ";
		$q.= "('$descripcion', '$observacion', '$tipo_producto_clasif', '$today', '$partidas_presupuestarias', '$grupo_proveedor', $codigo ) ";
		//die($q);
		/*echo $q;*/
		if($conn->Execute($q)){
			$this->msj = REG_ADD_OK;
			return true;
		} else {
			$this->msj = ERROR_ADD;
			return false;
			}
	}

	function set($conn, $id, $tipo_producto_clasif,$descripcion, $observacion,$today, $partidas_presupuestarias, $grupo_proveedor,$codigo){
		$q = "UPDATE tipo_producto SET descripcion = '$descripcion', observacion='$observacion', id_familia_producto=$tipo_producto_clasif, fecha='$today', id_partidas_presupuestarias='$partidas_presupuestarias', id_grupo_proveedor='$grupo_proveedor', codigo = $codigo";
		$q.= "WHERE id=$id";	
		//die($q);
		if($conn->Execute($q)){
			$this->msj = REG_SET_OK;
			return true;
		} else {
			$this->msj = ERROR_SET;
			return false;
			}
	}

	function del($conn, $id){
		$q = "DELETE FROM tipo_producto WHERE id='$id'";
		if($conn->Execute($q)){
			$this->msj = REG_DEL_OK;
			return true;
		} else {
			$this->msj = ERROR_DEL;
			return false;
			}
	}
	
	function buscar($conn,$id_pp, $grupo_prov, $observacion, $codigo, $idFamilia, $max=0, $from=0, $orden="id"){
		//die("aqui ".$grupo_prov);
		try{
			if(empty($id_pp) and empty($grupo_prov) and empty($observacion) and empty($codigo) and empty($idFamilia))
				return false;
			$q = "SELECT * FROM puser.tipo_producto ";
			$q.= "WHERE 1=1 ";
			$q.= !empty($id_pp) ? "AND id_partidas_presupuestarias = '$id_pp'  ":"";
			$q.= !empty($grupo_prov) ? "AND id_grupo_proveedor = '$grupo_prov'  ":"";
			$q.= !empty($observacion) ? "AND descripcion ILIKE '%$observacion%'  ":"";
			$q.= !empty($codigo) ? "AND codigo ILIKE '%$codigo%' " :"";
			$q.= !empty($idFamilia) ? "AND id_familia_producto = $idFamilia " :"";
			$q.= "ORDER BY $orden ";
			//die($q);
			$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
			$collection=array();
			while(!$r->EOF){
				$ue = new tipo_producto;
				$ue->get($conn, $r->fields['id']);
				$coleccion[] = $ue;
				$r->movenext();
			}
			return $coleccion;
		}
		catch( ADODB_Exception $e ){
			if($e->getCode()==-1)
				return ERROR_CATCH_VFK;
			elseif($e->getCode()==-5)
				return ERROR_CATCH_VUK;
			else
				return ERROR_CATCH_GENERICO;
		}
	}
	
	function total_registro_busqueda($conn, $id_pp, $grupo_prov, $observacion, $codigo, $idFamilia, $orden="id"){
		if(empty($id_pp) and empty($grupo_prov) and empty($observacion) and empty($codigo) and empty($idFamilia))
				return false;
		$q = "SELECT * FROM puser.tipo_producto ";
		$q.= "WHERE 1=1 ";
		$q.= !empty($id_pp) ? "AND id_partidas_presupuestarias = '$id_pp'  ":"";
		$q.= !empty($grupo_prov) ? "AND id_grupo_proveedor = '$grupo_prov'  ":"";
		$q.= !empty($descripcion) ? "AND descripcion ILIKE '%$observacion%'  ":"";
		$q.= !empty($codigo) ? "AND codigo ILIKE '%$codigo%' " :"";
		$q.= !empty($idFamilia) ? "AND id_familia_producto = $idFamilia " :"";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		$total = $r->RecordCount();

		return $total;
	}
	
	function getFamilia($conn, $id){
		$q = "SELECT codigo FROM puser.tipo_familia WHERE id = $id ";
		//die($q);
		$r = $conn->Execute($q);
		return $r->fields['codigo'];
	}
	
	function getLastFamilia($conn, $id){
		$q = "SELECT LPAD((COALESCE(MAX(codigo),0) + 1)::char, 4, '0') AS codigo FROM  puser.tipo_producto WHERE id_familia_producto = $id";
		//die($q);
		$r = $conn->Execute($q) or die($q);
		//var_dump($r);
		return $r->fields['codigo'];
	}
	
}
?>
