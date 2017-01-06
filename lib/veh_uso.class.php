<?
class veh_uso{

	// Propiedades

	var $id;
	var $descripcion;
	var $status;
	var $total;
	function get($conn, $id){
		$q = "SELECT * FROM vehiculo.uso ";
		$q.= "WHERE cod_uso='$id'";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['cod_uso'];
			$this->descripcion = $r->fields['descripcion'];
			$this->status = $r->fields['status'];
			
															
			return true;
		}else
			return false;
	}

	function get_all($conn, $orden="cod_uso"){
		$q = "SELECT * FROM vehiculo.uso ";
		$q.= "ORDER BY $orden "; 
		$r = $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new veh_uso;
			$ue->get($conn, $r->fields['cod_uso']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $descripcion, $status){
		$q = "INSERT INTO vehiculo.uso ";
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
		$q = "UPDATE vehiculo.uso SET descripcion='$descripcion', status=$status ";
		$q.= "WHERE cod_uso=$id";	
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $id){
		$q = "DELETE FROM vehiculo.uso WHERE cod_uso='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
}
?>
