<?
class solicitud{

	// Propiedades

	var $id;
	var $detalle;
	
	var $total;

	function get($conn, $id){
		$q = "SELECT * FROM licores.solicitud ";
		$q.= "WHERE codigo='$id'";
		$r = $conn->Execute($q) or die($q);
		if(!$r->EOF){
			$this->id = $r->fields['codigo'];
			$this->detalle = $r->fields['detalle'];
			return true;
		}else
			return false;
	}

	function get_all($conn, $orden="id"){
		$q = "SELECT * FROM licores.solicitud ";
		//$q.= "ORDER BY $orden ";
		$r = $conn->Execute($q) or die($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new solicitud;
			$ue->get($conn, $r->fields['codigo']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $id, $detalle){
		$q = "INSERT INTO licores.solicitud ";
		$q.= "(detalle) ";
		$q.= "VALUES ";
		$q.= "('$detalle' ) ";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function set($conn, $id, $detalle){
		$q = "UPDATE licores.solicitud SET detalle='$detalle' ";
		$q.= "WHERE codigo='$id' ";	
		//die($q);
		if($conn->Execute($q) or die($q))
			return true;
		else
			return false;
	}

	function del($conn, $id){
		$q = "DELETE FROM licores.solicitud ";
		$q.= "WHERE codigo = '$id' ";
		if($conn->Execute($q) or die($q))
			return true;
		else
			return false;
	}
}
?>