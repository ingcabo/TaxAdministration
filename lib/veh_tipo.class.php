<?
class veh_tipo{

	// Propiedades

	var $id;
	var $descripcion;
	var $status;
	var $total;
	function get($conn, $id){
		$q = "SELECT * FROM vehiculo.tipo ";
		$q.= "WHERE cod_tip='$id'";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['cod_tip'];
			$this->descripcion = $r->fields['descripcion'];
			$this->status = $r->fields['status'];
			
															
			return true;
		}else
			return false;
	}

	function get_all($conn, $orden="cod_tip"){
		$q = "SELECT * FROM vehiculo.tipo ";
		$q.= "ORDER BY $orden "; 
		$r = $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new veh_tipo;
			$ue->get($conn, $r->fields['cod_tip']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $descripcion, $status){
		$q = "INSERT INTO vehiculo.tipo ";
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
		$q = "UPDATE vehiculo.tipo SET descripcion='$descripcion', status=$status ";
		$q.= "WHERE cod_tip=$id";	
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $id){
		$q = "DELETE FROM vehiculo.tipo WHERE cod_tip='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
}
?>
