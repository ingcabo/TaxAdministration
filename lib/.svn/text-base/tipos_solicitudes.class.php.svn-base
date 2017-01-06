<?
class tipos_solicitudes{

	// Propiedades

	var $id;
	var $descripcion;

	var $total;

	function get($conn, $id){
		$q = "SELECT * FROM finanzas.tipos_solicitudes ";
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
		$q = "SELECT * FROM finanzas.tipos_solicitudes ";
		$q.= "ORDER BY $orden ";
		$r = $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new tipos_solicitudes;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $id, $descripcion){
		$q = "INSERT INTO finanzas.tipos_solicitudes ";
		$q.= "(id, descripcion) ";
		$q.= "VALUES ";
		$q.= "('$id', '$descripcion' ) ";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function set($conn, $id_nuevo, $id, $descripcion){
		$q = "UPDATE finanzas.tipos_solicitudes SET id = '$id_nuevo', descripcion='$descripcion' ";
		$q.= "WHERE id='$id' ";	
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $id){
		$q = "DELETE FROM finanzas.tipos_solicitudes WHERE id='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
}
?>
