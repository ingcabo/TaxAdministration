<?
class insppblicidad{

	// Propiedades

	var $id;
	var $nombre;
	var $apellido;
	var $cedula;
	var $status;
	var $total;
	function get($conn, $id){
		$q = "SELECT * FROM publicidad.inspector ";
		$q.= "WHERE cod_ins='$id'";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['cod_ins'];
			$this->nombre = $r->fields['nombre'];
			$this->apellido = $r->fields['apellido'];
			$this->cedula = $r->fields['cedula'];
			$this->status = $r->fields['status'];												
			return true;
		}else
			return false;
	}

	function get_all($conn, $orden="cod_ins"){
		$q = "SELECT * FROM publicidad.inspector ";
		$q.= "ORDER BY $orden "; 
		$r = $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new insppblicidad;
			$ue->get($conn, $r->fields['cod_ins']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $nombre, $apellido, $cedula, $status){
		$q = "INSERT INTO publicidad.inspector ";
		$q.= "(nombre, apellido, cedula, status) ";
		$q.= "VALUES ";
		$q.= "('$nombre', '$apellido', '$cedula', $status) ";// die($q);
		if($conn->Execute($q)){		
			return true;
		}
		else{
			return false;
		}	
		
	}

	function set($conn, $id, $nombre, $apellido, $cedula, $status){
		$q = "UPDATE publicidad.inspector SET nombre='$nombre', apellido='$apellido', cedula='$cedula',  status=$status ";
		$q.= "WHERE cod_ins=$id";	
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $id){
		$q = "DELETE FROM publicidad.inspector WHERE cod_ins='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
}
?>
