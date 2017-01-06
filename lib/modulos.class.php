<?
class modulos{

	// Propiedades

	var $id;
	var $descripcion;
	var $orden;

	var $total;

	function get($conn, $id){
		$q = "SELECT * FROM modulos ";
		$q.= "WHERE id='$id'";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->descripcion = $r->fields['descripcion'];
			$this->orden = $r->fields['orden'];
			return true;
		}else
			return false;
	}

	function getAll($conn, $orden="descripcion"){
		$q = "SELECT * FROM modulos ";
		$q.= "ORDER BY $orden ";
		$r = $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new modulos;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $descripcion, $orden){
		$q = "INSERT INTO modulos ";
		$q.= "(descripcion, orden) ";
		$q.= "VALUES ";
		$q.= "('$descripcion', '$orden' ) ";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function set($conn, $id_nuevo, $id, $descripcion){
		$q = "UPDATE modulos SET descripcion='$descripcion', orden = '$orden' ";
		$q.= "WHERE id='$id' ";	
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $id){
		$q = "DELETE FROM modulos WHERE id='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
}
?>
