<?
class tasa_inscripcion{

	// Propiedades

	var $id;
	var $anio;
	var $fecha_desde;
	var $fecha_hasta;
	var $monto;
	var $status;

	function get($conn, $id){
		$q = "SELECT * FROM vehiculo.tasa_inscripcion ";
		$q.= "WHERE id='$id'";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->anio = $r->fields['anio'];
			$this->fecha_desde = $r->fields['fecha_desde'];
			$this->fecha_hasta = $r->fields['fecha_hasta'];
			$this->monto = $r->fields['monto'];
			$this->status = $r->fields['status'];
			
															
			return true;
		}else
			return false;
	}

	function get_all($conn, $from=0, $max=0,$orden="id"){
		$q = "SELECT * FROM vehiculo.tasa_inscripcion ";
		$q.= "ORDER BY $orden ";
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new tasa_inscripcion;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $anio, $fecha_desde, $fecha_hasta, $monto, $status){
		$q = "INSERT INTO vehiculo.tasa_inscripcion ";
		$q.= "(anio, fecha_desde, fecha_hasta, monto, status) ";
		$q.= "VALUES ";
		$q.= "($anio, '$fecha_desde', '$fecha_hasta', '$monto', $status) ";

		//die($q);
		
		if($conn->Execute($q)){
		
			return true;
		}
		else{
			return false;
		}	
		
	}

	function set($conn, $id, $anio, $fecha_desde, $fecha_hasta, $monto, $status){
		$q = "UPDATE vehiculo.tasa_inscripcion SET anio=$anio, fecha_desde='$fecha_desde', fecha_hasta='$fecha_hasta', monto='$monto', status=$status ";
		$q.= "WHERE id=$id";	
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $id){
		$q = "DELETE FROM vehiculo.tasa_inscripcion WHERE id='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
}
?>
