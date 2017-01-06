<?

class motivo_anulacion_orden_pago{

# PROPIEDADES #

	var $id;
	var $descripcion;
	var $total;

# METODOS #

	function get($conn, $id){
		$q = "SELECT * FROM finanzas.motivo_anulacion_orden_pago ";
		$q.= "WHERE id='$id'";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->descripcion = $r->fields['descripcion'];
																					
			return true;
		}else
			return false;
	}
	
	function get_all($conn, $from=0, $max=0,$orden="id"){
		$q = "SELECT * FROM finanzas.motivo_anulacion_orden_pago ";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new motivo_anulacion_orden_pago;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}
	
	function add($conn, $descripcion){
		$q = "INSERT INTO finanzas.motivo_anulacion_orden_pago ";
		$q.= "(descripcion) ";
		$q.= "VALUES ";
		$q.= "('$descripcion') ";

		//die($q);
		
		if($conn->Execute($q)){
		
			return true;
		}
		else{
			return false;
		}	
		
	}
	
	function set($conn, $id, $descripcion){
		$q = "UPDATE finanzas.motivo_anulacion_orden_pago SET descripcion='$descripcion' ";
		$q.= "WHERE id=$id";	
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	function del($conn, $id){
		$q = "DELETE FROM finanzas.motivo_anulacion_orden_pago WHERE id='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
}

?>