<?
class multa{

	// Propiedades

	var $id;
	var $detalle;
	var $ordenanza;
	var $multa;

	var $total;

	function get($conn, $id){
		$q = "SELECT * FROM licores.multas ";
		$q.= "WHERE id='$id'";
		$r = $conn->Execute($q) or die($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->detalle = $r->fields['detalle'];
			$this->ordenanza = $r->fields['ordenanza'];
			$this->multa = $r->fields['multa'];
			return true;
		}else
			return false;
	}

	function get_all($conn, $orden="id"){
		$q = "SELECT * FROM licores.multas ";
		//$q.= "ORDER BY $orden ";
		$r = $conn->Execute($q) or die($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new multa;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $id, $detalle, $ordenanza, $multa){
		$q = "INSERT INTO licores.multas ";
		$q.= "(detalle, ordenanza, multa) ";
		$q.= "VALUES ";
		$q.= "('$detalle', '$ordenanza', '$multa' ) ";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function set($conn, $id, $detalle, $ordenanza, $multa){
		$q = "UPDATE licores.multas SET detalle='$detalle', ordenanza='$ordenanza', multa='$multa' ";
		$q.= "WHERE id='$id' ";	
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $id){
		$q = "DELETE FROM licores.multas ";
		$q.= "WHERE id = '$id' ";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
}
?>
