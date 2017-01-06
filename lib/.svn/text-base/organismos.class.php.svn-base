<?
class organismos{

	// Propiedades

	var $id;
	var $id_escenario; 
	var $descripcion;
	var $escenario;

	var $total;

	function get($conn, $id){
		$q = "SELECT * FROM organismos WHERE id='$id'";
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $id;
			$this->id_escenario = $r->fields['id_escenario'];
			$oEscenario = new escenarios;
			$oEscenario->get($conn, $r->fields['id_escenario']);
			$this->escenario = $oEscenario;
			$this->descripcion = $r->fields['descripcion'];
			return true;
		}else
			return false;
	}

	function get_all($conn, $from=0, $max=0,$orden="id"){
		$q = "SELECT * FROM organismos ";
		$q.= "ORDER BY $orden ";
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new organismos;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $id, $id_escenario, $descripcion){
		$q = "INSERT INTO organismos ";
		$q.= "(id, id_escenario, descripcion) ";
		$q.= "VALUES ('$id', '$id_escenario', '$descripcion') ";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function set($conn, $id_nuevo, $id, $id_escenario, $descripcion){
		$q = "UPDATE organismos SET id = '$id_nuevo', id_escenario='$id_escenario', ";
		$q.= "descripcion = '$descripcion' ";
		$q.= "WHERE id='$id'";
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $id){
		$q = "DELETE FROM organismos WHERE id='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	function buscar($conn, $id, $id_escenario, $descripcion, $orden="id_escenario, id"){
		if(empty($id) && empty($id_escenario) && empty($descripcion))
			return false;
		$q = "SELECT * FROM organismos ";
		$q.= "WHERE  1=1 ";
		$q.= !empty($id) ? "AND id = '$id'  ":"";
		$q.= !empty($id_base) ? "AND id_escenario = '$id_escenario'  ":"";
		$q.= !empty($descripcion) ? "AND descripcion ILIKE '%$descripcion%'  ":"";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new organismos;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		return $coleccion;
	}
}
?>