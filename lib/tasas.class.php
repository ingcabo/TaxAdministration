<?
class tasas{

	// Propiedades

	var $id;
	var $detalle;
	var $ordenanza;
	var $unit;

	var $total;

	function get($conn, $id){
		$q = "SELECT * FROM licores.tasas ";
		$q.= "WHERE id='$id'";
		$r = $conn->Execute($q) or die($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->detalle = $r->fields['detalle'];
			$this->ordenanza = $r->fields['ordenanza'];
			$this->unit = $r->fields['uni_t'];
			return true;
		}else
			return false;
	}

	function get_all($conn, $orden="id"){
		$q = "SELECT * FROM licores.tasas ";
		//$q.= "ORDER BY $orden ";
		$r = $conn->Execute($q) or die($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new tasas;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $id, $detalle, $ordenanza, $unit){
		$q = "INSERT INTO licores.tasas ";
		$q.= "(detalle, ordenanza, uni_t) ";
		$q.= "VALUES ";
		$q.= "('$detalle', '$ordenanza', '$unit' ) ";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function set($conn, $id, $detalle, $ordenanza, $unit){
		$q = "UPDATE licores.tasas SET detalle='$detalle', ordenanza='$ordenanza', uni_t='$unit' ";
		$q.= "WHERE id='$id' ";	
		//die($q);
		if($conn->Execute($q) or die($q))
			return true;
		else
			return false;
	}

	function del($conn, $id){
		$q = "DELETE FROM licores.tasas ";
		$q.= "WHERE id = '$id' ";
		if($conn->Execute($q) or die($q))
			return true;
		else
			return false;
	}
}
?>