<?
class veh_tvehgaceta{

	// Propiedades

	var $id;
	var $descripcion;
	var $status;
	var $total;
	function get($conn, $id){
		$q = "SELECT * FROM vehiculo.tipo_veh_segun_gaceta ";
		$q.= "WHERE cod_veh='$id'";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['cod_veh'];
			$this->descripcion = $r->fields['descripcion'];
			$this->status = $r->fields['status'];
			
															
			return true;
		}else
			return false;
	}

	function get_all($conn, $orden="cod_veh"){
		$q = "SELECT * FROM vehiculo.tipo_veh_segun_gaceta ";
		$q.= "ORDER BY $orden "; 
		$r = $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new veh_tvehgaceta;
			$ue->get($conn, $r->fields['cod_veh']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $descripcion, $status){
		$q = "INSERT INTO vehiculo.tipo_veh_segun_gaceta ";
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
		$q = "UPDATE vehiculo.tipo_veh_segun_gaceta SET descripcion='$descripcion', status=$status ";
		$q.= "WHERE cod_veh=$id";	
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $id){
		$q = "DELETE FROM vehiculo.tipo_veh_segun_gaceta WHERE cod_veh='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
}
?>
