<?
class retencion_iva{

	// Propiedades

	var $id;
	var $id_proveedor;
	var $tipo_contribuyente;
	var $fecha;
	var $ingreso_periodo_fiscal;
	var $cant_unid_tributaria;

	var $total;

	function get($conn, $id){
		$q = "SELECT * FROM retencion_iva ";
		$q.= "WHERE id='$id'";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->id_proveedor = $r->fields['id_proveedor'];
			$this->tipo_contribuyente = $r->fields['tipo_contribuyente'];
			$this->fecha = $r->fields['fecha'];
			$this->ingreso_periodo_fiscal = $r->fields['ingreso_periodo_fiscal'];
			$this->cant_unid_tributaria = $r->fields['cant_unid_tributaria'];
			return true;
		}else
			return false;
	}

	function getAll($conn, $orden="id"){
		$q = "SELECT * FROM retencion_iva ";
		$q.= "ORDER BY $orden ";
		$r = $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new retencion_iva;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $id, $descripcion){
		$q = "INSERT INTO retencion_iva ";
		$q.= "(id, descripcion) ";
		$q.= "VALUES ";
		$q.= "('$id', '$descripcion' ) ";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function set($conn, $id_nuevo, $id, $descripcion){
		$q = "UPDATE retencion_iva SET id = '$id_nuevo', descripcion='$descripcion' ";
		$q.= "WHERE id='$id' ";	
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $id){
		$q = "DELETE FROM retencion_iva WHERE id='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
}
?>
