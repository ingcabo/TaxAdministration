<?
class gacetas{

	// Propiedades

	var $id;
	var $descripcion;

	var $total;

	function get($conn, $id){
		$q = "SELECT * FROM gacetas WHERE id='$id'";
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->descripcion = $r->fields['descripcion'];
			$this->detalle = $r->fields['detalle'];
			$this->factor = $r->fields['factor'];
			$this->formulacion = $r->fields['formulacion'];
			return true;
		}else
			return false;
	}

	function get_all($conn, $from=0, $max=0,$orden="id"){
		$q = "SELECT * FROM gacetas";
		$q.= "ORDER BY $orden ";
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new gacetas;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $id, $id_base, $descripcion, $ano, $detalle, $factor, $formulacion){
		$id_base = (empty($id_base))? "0" : $id_base;
		$formulacion = ($formulacion == 'on')? "true" : "false";
		$q = "INSERT INTO gacetas ";
		$q.= "(id, id_base, descripcion, ano, detalle, factor, formulacion) ";
		$q.= "VALUES ('$id', '$id_base', '$descripcion', '$ano', '$detalle', '$factor', '$formulacion') ";
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function set($conn, $id_nuevo, $id, $id_base, $descripcion, $ano, $detalle, $factor, $formulacion){
		$formulacion = ($formulacion == 'on')? "true" : "false";
		$q = "UPDATE gacetas SET id = '$id_nuevo', id_base = '$id_base', ";
		$q.= "descripcion = '$descripcion', ano = '$ano', ";
		$q.= "detalle = '$detalle', factor = '$factor', formulacion = '$formulacion' ";
		$q.= "WHERE id='$id' ";
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $id){
		$q = "DELETE FROM gacetas WHERE id='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	function get_descripcion($conn, $id){
		$q = "SELECT descripcion FROM gacetas WHERE id='$id'";
		$r = $conn->Execute($q);
		if(!$r->EOF){
			return $r->fields['descripcion'];
		}else
			return false;
	}
}
?>
