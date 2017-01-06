<?
class veh_sanciones{

	// Propiedades

	var $id;
	var $descripcion;
	var $status;
	var $total;
	var $monto;
	function get($conn, $id){
		$q = "SELECT * FROM vehiculo.sancion ";
		$q.= "WHERE cod_san='$id'";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['cod_san'];
			$this->descripcion = $r->fields['descripcion'];
			$this->status = $r->fields['status'];
			$this->monto = $r->fields['monto'];
															
			return true;
		}else
			return false;
	}

	function get_all($conn, $orden="cod_san"){
		$q = "SELECT * FROM vehiculo.sancion ";
		$q.= "ORDER BY $orden "; 
		$r = $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new veh_sanciones;
			$ue->get($conn, $r->fields['cod_san']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $descripcion, $status, $monto, $anio){
		$q = "INSERT INTO vehiculo.sancion ";
		$q.= "(descripcion, status, anio, monto) ";
		$q.= "VALUES ";
		$q.= "('$descripcion', $status, $anio, $monto) ";

		//die($q);
		
		if($conn->Execute($q)){
		
			return true;
		}
		else{
			return false;
		}	
		
	}

	function set($conn, $id, $descripcion, $status, $monto, $anio){
		$q = "UPDATE vehiculo.sancion SET descripcion='$descripcion', status=$status, anio='$anio', monto='$monto' ";
		$q.= "WHERE cod_san=$id";	
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $id){
		$q = "DELETE FROM vehiculo.sancion WHERE cod_san='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
}
?>
