<?
class requisitos_publicidad{
	// Propiedades
	var $id;
	var $requisito;
	var $estatus;
	var $id_solicitud;
	var $total;

	function get($conn, $id){
		$q = "SELECT * FROM publicidad.requisitos_pub ";
		$q.= "WHERE id='$id' ";
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->id_solicitud=$r->fields['cod_solicitud'];
			$this->requisito = $r->fields['requisito'];
			$this->estatus = $r->fields['estatus'];			
			return true;
		}else
			return false;
	}

	function get_all($conn, $from=0, $max=0,$orden="cod_solicitud"){
		$q = "SELECT * FROM publicidad.requisitos_pub ";
		//$q.= "ORDER BY $orden ";
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new requisitos_publicidad;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $id_solicitud, $requisito, $estatus){
		$q = "INSERT INTO publicidad.requisitos_pub ";
		$q.= "(cod_solicitud,requisito, estatus) ";
		$q.= "VALUES ";
		$q.= "('$id_solicitud','$requisito', '$estatus' ) ";
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function set($conn, $id, $id_solicitud, $requisito, $estatus){
		$q = "UPDATE publicidad.requisitos_pub SET cod_solicitud='$id_solicitud', requisito='$requisito', "; 
		$q.= "estatus = $estatus ";
		$q.= "WHERE id = '$id' ";
		//die ($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $id){
		$q = "DELETE FROM publicidad.requisitos_pub WHERE id='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
}
?>
