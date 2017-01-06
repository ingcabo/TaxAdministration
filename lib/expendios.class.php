<?
// Porpiedades de la Clase Expendios
class expendios{
		var $id;
		var $nombre;
		var $direccion;
		var $representante;
		var $total;
		
	function get($conn, $id){
		$q = "SELECT * FROM licores.expendios ";
		$q.= "WHERE codigo='$id'";
		$r = $conn->Execute($q) or die($q);
		if(!$r->EOF){
			$this->id = $r->fields['codigo'];
			$this->nombre = $r->fields['nombre'];
			$this->direccion = $r->fields['direccion'];
			$this->representante = $r->fields['representante'];
			return true;
		}else
			return false;
	}
function get_all($conn, $id="codigo"){
		$q = "SELECT * FROM licores.expendios ";
		$q.= "ORDER BY $id "; 
		$r = $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new expendios;
			$ue->get($conn, $r->fields['codigo']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $nombre, $direccion, $representante){
		$q = "INSERT INTO licores.expendios ";
		$q.= "(nombre, direccion, representante) ";
		$q.= "VALUES ";
		$q.= "('$nombre', '$direccion', '$representante' ) ";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function set($conn, $id, $nombre, $direccion, $representante){
		$q = "UPDATE licores.expendios SET nombre='$nombre', direccion='$direccion', representante='$representante' ";
		$q.= "WHERE codigo='$id' ";	
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $id){
		$q = "DELETE FROM licores.expendios ";
		$q.= "WHERE codigo = '$id' ";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
}
?>