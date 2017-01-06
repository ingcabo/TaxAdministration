<?
class tipos_solicitud_sin_imp{

	// Propiedades

	var $id;
	var $descripcion;
	var $cc;
	var $anio;

	var $total;

	function get($conn, $id){
		$q = "SELECT * FROM finanzas.tipos_solicitud_sin_imp ";
		$q.= "WHERE id='$id'";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->descripcion = $r->fields['descripcion'];
			$this->cc = $r->fields['cuenta_contable'];
			$this->anio = $r->fields['anio'];
			return true;
		}else
			return false;
	}

	function getAll($conn, $orden="id"){
		$q = "SELECT * FROM finanzas.tipos_solicitud_sin_imp ";
		$q.= "ORDER BY $orden ";
		$r = $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new tipos_solicitud_sin_imp;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $descripcion, $cc, $anio){
		$q = "INSERT INTO finanzas.tipos_solicitud_sin_imp ";
		$q.= "(descripcion, cuenta_contable, anio) ";
		$q.= "VALUES ";
		$q.= "('$descripcion', '$cc', '$anio' ) ";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function set($conn, $id, $descripcion, $cc, $anio){
		$q = "UPDATE finanzas.tipos_solicitud_sin_imp SET descripcion='$descripcion', ";
		$q.= "cuenta_contable='$cc', anio = '$anio' ";	
		$q.= "WHERE id='$id' ";	
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $id){
		$q = "DELETE FROM finanzas.tipos_solicitud_sin_imp WHERE id='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
}
?>
