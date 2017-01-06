<?
class tasa_inscripcion_publicidad{

	// Propiedades

	var $id;
	var $fecha_desde;
	var $fecha_hasta;
	var $monto;
	var $status;

	function get($conn, $id){
		$q = "SELECT * FROM publicidad.tasa_inscripcion ";
		$q.= "WHERE id='$id'";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->fecha_desde = $r->fields['fecha_desde'];
			$this->fecha_hasta = $r->fields['fecha_hasta'];
			$this->monto = $r->fields['monto'];
			$this->status = $r->fields['status'];
			
															
			return true;
		}else
			return false;
	}

	function get_all($conn, $from=0, $max=0,$orden="id"){
		$q = "SELECT * FROM publicidad.tasa_inscripcion ";
		$q.= "ORDER BY $orden ";
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new tasa_inscripcion_publicidad;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $fecha_desde, $fecha_hasta, $monto, $status){
		$q = "INSERT INTO publicidad.tasa_inscripcion ";
		$q.= "(fecha_desde, fecha_hasta, monto, status) ";
		$q.= "VALUES ";
		$q.= "('$fecha_desde', '$fecha_hasta', '$monto', '$status') ";

		//die($q);
		
		if($conn->Execute($q)){
		
			return true;
		}
		else{
			return false;
		}	
		
	}

	function set($conn, $id, $fecha_desde, $fecha_hasta, $monto, $status){
		$q = "UPDATE publicidad.tasa_inscripcion SET fecha_desde='$fecha_desde', fecha_hasta='$fecha_hasta', monto='$monto', status='$status' ";
		$q.= "WHERE id=$id";	
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $id){
		$q = "DELETE FROM publicidad.tasa_inscripcion WHERE id='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
}
?>