<?
class tipo_producto{

	// Propiedades

	var $id;
	var $descripcion;
	var $observacion;
	var $partida;
	var $id_tipo_producto_clasif;
	var $desc_completa;
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
			$this->id_tipo_producto_clasif = $r->fields['id_tipo_producto_clasif'];	
			$this->desc_completa = $r->fields['desc_completa'];
														
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

	function add($conn, $descripcion, $tipo_producto_clasif, $observacion, $partida, $today,$desc_completa){
		$q = "INSERT INTO tipo_producto ";
		$q.= "( descripcion, observacion, partida, id_tipo_producto_clasif, fecha, desc_completa ) ";
		$q.= " VALUES ";
		$q.= "( '$descripcion', '$observacion', '$partida', '$tipo_producto_clasif', '$today', '$desc_completa' ) ";
		/*echo $q;*/
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function set($conn, $id, $descripcion, $tipo_producto_clasif, $observacion, $partida, $today,$desc_completa){
		$q = "UPDATE tipo_producto SET descripcion = '$descripcion', observacion='$observacion', partida='$partida', id_tipo_producto_clasif=$tipo_producto_clasif, fecha='$today', desc_completa='$desc_completa' ";
		$q.= "WHERE id=$id";	
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $id){
		$q = "DELETE FROM tipo_producto WHERE id='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
}
?>
