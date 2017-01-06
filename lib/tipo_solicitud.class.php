<?
class tipo_solicitud{
	// Propiedades
	var $id;
	var $descripcion;
	var $estatus;
	var $total;

	function get($conn, $id){
		$q = "SELECT * FROM publicidad.tipo_solicitud ";
		$q.= "WHERE id='$id' ";
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->descripcion = $r->fields['descripcion'];
			$this->estatus = $r->fields['estatus'];			
			return true;
		}else
			return false;
	}

	function get_all($conn, $from=0, $max=0,$orden="id"){
		$q = "SELECT * FROM publicidad.tipo_solicitud ";
		$q.= "ORDER BY $orden ";
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new tipo_solicitud;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $descripcion, $estatus){
		$q = "INSERT INTO publicidad.tipo_solicitud ";
		$q.= "(descripcion, estatus) ";
		$q.= "VALUES ";
		$q.= "('$descripcion', $estatus) ";//die($q);
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function set($conn, $id, $descripcion, $estatus){
		$q = "UPDATE publicidad.tipo_solicitud SET descripcion='$descripcion', ";
		$q.= "estatus = '$estatus' ";
		$q.= "WHERE id = '$id' ";
		//die ($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $id){
		$q = "DELETE FROM publicidad.tipo_solicitud WHERE id='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
}
?>
