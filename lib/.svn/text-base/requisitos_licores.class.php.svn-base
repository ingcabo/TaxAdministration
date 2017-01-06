<?
// Porpiedades de la Clase Requisitos_licores
class requisitos_licores{
		var $id;
		var $requisito;
		var $total;
		
	function get($conn, $id){
		$q = "SELECT * FROM licores.requisito ";
		$q.= "WHERE tipo='$id'"; 
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['tipo'];
			$this->requisito = $r->fields['requisitos'];
			return true;
		}else
			return false;
	}
function get_all($conn){
		$q = "SELECT * FROM licores.requisito "; 
		//$q.= "ORDER BY $id ";
		$r = $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new requisitos_licores;
			$ue->get($conn, $r->fields['tipo']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $id, $requisito){
		$q = "INSERT INTO licores.requisito ";
		$q.= "(tipo, requisitos) ";
		$q.= "VALUES ";
		$q.= "('$id', '$requisito' )"; 
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function set($conn, $id, $requisito){
		$q = "UPDATE licores.requisito SET requisitos='$requisito'";
		$q.= "WHERE tipo='$id' ";	
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $id){
		$q = "DELETE FROM licores.requisito ";
		$q.= "WHERE tipo = '$id' ";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
}

?>