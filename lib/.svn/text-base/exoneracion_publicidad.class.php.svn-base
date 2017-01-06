<?
class exoneracion_publicidad{

	// Propiedades

	var $id;
	var $descripcion;
	var $estatus;
	var $tipo;
	var $total;
	function get($conn, $id){
		$q = "SELECT * FROM publicidad.exoneracion_publicidad ";
		$q.= "WHERE id_exo='$id'";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id_exo'];
			$this->descripcion = $r->fields['descripcion'];
			$this->estatus = $r->fields['estatus'];
			$this->tipo = $r->fields['tipo'];
			return true;
		}else
			return false;
	}

	function get_all($conn){
		$q = "SELECT * FROM publicidad.exoneracion_publicidad";
		$r = $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new exoneracion_publicidad;
			$ue->get($conn, $r->fields['id_exo']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $descripcion, $estatus, $tipo){
		$q = "INSERT INTO publicidad.exoneracion_publicidad";
		$q.= "(descripcion, estatus, tipo) ";
		$q.= "VALUES ";
		$q.= "('$descripcion', $estatus, $tipo) "; die($q);
		if($conn->Execute($q)){
			return true;
		}
		else{
			return false;
		}	
	}

	function set($conn, $id, $descripcion, $estatus, $tipo){
		$q = "UPDATE publicidad.exoneracion_publicidad SET descripcion='$descripcion', ";
		$q.= "estatus=$estatus, tipo='$tipo' ";
		$q.= "WHERE id_exo=$id";
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $id){
		$q = "DELETE FROM publicidad.exoneracion_publicidad WHERE id_exo='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
}
?>