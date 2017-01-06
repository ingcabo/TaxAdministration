<?
class ciudadanos{

	// Propiedades

	var $id;
	var $nombre;
	var $direccion;
	var $tlf;

	var $total;

	function get($conn, $id){
		$q = "SELECT * FROM ciudadanos ";
		$q.= "WHERE id='$id'";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->nombre = $r->fields['nombre'];
			$this->direccion = $r->fields['direccion'];
			$this->tlf = $r->fields['tlf'];
			return true;
		}else
			return false;
	}

	function get_all($conn, $from=0, $max=0,$orden="id"){
		$q = "SELECT * FROM ciudadanos ";
		$q.= "ORDER BY $orden ";
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new ciudadanos;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $id, $nombre, $direccion, $tlf){
		$q = "INSERT INTO ciudadanos ";
		$q.= "(id, nombre, direccion, tlf) ";
		$q.= "VALUES ";
		$q.= "('$id', '$nombre', '$direccion', '$tlf' ) ";
		//die($q);
		$r = $conn->Execute($q) or die($q);
		if($r)
			return true;
		else
			return false;
	}

	function set($conn, $id_nuevo, $id, $nombre, $direccion, $tlf){
		$q = "UPDATE ciudadanos SET id = '$id_nuevo', nombre='$nombre', direccion = '$direccion', tlf = '$tlf' ";
		$q.= "WHERE id='$id' ";	
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $id){
		$q = "DELETE FROM ciudadanos WHERE id='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
}
?>
