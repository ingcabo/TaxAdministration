<?
class veh_tipotran{

	// Propiedades

	var $id;
	var $descripcion;
	var $id_par_pre;
	var $transaccion;
	var $anio;
	var $status;
	var $total;
	function get($conn, $id){
		$q = "SELECT * FROM vehiculo.tipo_transaccion ";
		$q.= "WHERE id='$id'";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->descripcion = $r->fields['descripcion'];
			$this->id_par_pre = $r->fields['id_par_pre'];
			$this->transaccion = $r->fields['tipo_trans'];
			$this->anio = $r->fields['anio'];
			$this->status = $r->fields['status'];
			
															
			return true;
		}else
			return false;
	}

	function get_all($conn, $orden="id"){
		$q = "SELECT * FROM vehiculo.tipo_transaccion ";
		//$q.= "ORDER BY $orden "; 
		$r = $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new veh_tipotran;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $descripcion, $id_par_pre, $transaccion, $anio, $status){
		$q = "INSERT INTO vehiculo.tipo_transaccion ";
		$q.= "(descripcion, id_par_pre, tipo_trans, anio, status) ";
		$q.= "VALUES ";
		$q.= "('$descripcion', '$id_par_pre', '$transaccion', '$anio', $status) ";

		//die($q);
		
		if($conn->Execute($q)){
		
			return true;
		}
		else{
			return false;
		}	
		
	}

	function set($conn, $id, $descripcion, $id_par_pre, $transaccion, $anio, $status){
		$q = "UPDATE vehiculo.tipo_transaccion SET descripcion='$descripcion', id_par_pre= '$id_par_pre', tipo_trans='$transaccion', anio= '$anio', status=$status ";
		$q.= "WHERE id=$id";	
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $id){
		$q = "DELETE FROM vehiculo.tipo_transaccion WHERE id='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
}
?>