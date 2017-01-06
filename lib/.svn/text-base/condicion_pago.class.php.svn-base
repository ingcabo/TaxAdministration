<?
class condicion_pago{

	// Propiedades

	var $id;
	var $descripcion;

	var $total;

	function get($conn, $id){
		$q = "SELECT * FROM condicion_pago ";
		$q.= "WHERE id='$id'";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->descripcion = $r->fields['descripcion'];
			return true;
		}else
			return false;
	}

	function get_all($conn, $orden="id"){
		$q = "SELECT * FROM condicion_pago ";
		$q.= "ORDER BY $orden ";
		$r = $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new condicion_pago;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $id, $descripcion){
		$q = "INSERT INTO condicion_pago ";
		$q.= "(id, descripcion) ";
		$q.= "VALUES ";
		$q.= "('$id', '$descripcion' ) ";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function set($conn, $id_nuevo, $id, $descripcion){
		$q = "UPDATE condicion_pago SET id = '$id_nuevo', descripcion='$descripcion' ";
		$q.= "WHERE id='$id' ";	
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $id){
		$q = "DELETE FROM condicion_pago WHERE id='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
}
?>
