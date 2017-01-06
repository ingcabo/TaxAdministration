<?
class veh_desincorporacion{

	// Propiedades

	var $id;
	var $descripcion;
	var $status;
	var $total;
	function get($conn, $id){
		$q = "SELECT * FROM vehiculo.mot_desincorporacion ";
		$q.= "WHERE cod_des='$id'";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['cod_des'];
			$this->descripcion = $r->fields['descripcion'];
			$this->status = $r->fields['status'];
			
															
			return true;
		}else
			return false;
	}

	function get_all($conn, $orden="cod_des"){
		$q = "SELECT * FROM vehiculo.mot_desincorporacion ";
		$q.= "ORDER BY $orden "; 
		$r = $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new veh_desincorporacion;
			$ue->get($conn, $r->fields['cod_des']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $descripcion, $status){
		$q = "INSERT INTO vehiculo.mot_desincorporacion ";
		$q.= "(descripcion, status) ";
		$q.= "VALUES ";
		$q.= "('$descripcion', $status) ";

		//die($q);
		
		if($conn->Execute($q)){
		
			return true;
		}
		else{
			return false;
		}	
		
	}

	function set($conn, $id, $descripcion, $status){
		$q = "UPDATE vehiculo.mot_desincorporacion SET descripcion='$descripcion', status=$status ";
		$q.= "WHERE cod_des=$id";	
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $id){
		$q = "DELETE FROM vehiculo.mot_desincorporacion WHERE cod_des='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
}
?>
