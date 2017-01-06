<?
class solvencia{

	// Propiedades

	var $id;
	var $fecha_desde;
	var $fecha_hasta;
	var $monto;
	var $monto_habilitado;

	function get($conn, $id){
		$q = "SELECT * FROM vehiculo.solvencia ";
		$q.= "WHERE id='$id'";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->fecha_desde = $r->fields['fecha_desde'];
			$this->fecha_hasta = $r->fields['fecha_hasta'];
			$this->monto = $r->fields['monto_normal'];
			$this->monto_habilitado = $r->fields['monto_habilitado'];
			
															
			return true;
		}else
			return false;
	}

	function get_all($conn, $from=0, $max=0,$orden="id"){
		$q = "SELECT * FROM vehiculo.solvencia ";
		$q.= "ORDER BY $orden ";
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new solvencia;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $fecha_desde, $fecha_hasta, $monto, $monto_habilitado){
		$q = "INSERT INTO vehiculo.solvencia ";
		$q.= "(fecha_desde, fecha_hasta, monto_normal, monto_habilitado) ";
		$q.= "VALUES ";
		$q.= "('$fecha_desde', '$fecha_hasta', '$monto', $monto_habilitado) ";

		//die($q);
		
		if($conn->Execute($q)){
		
			return true;
		}
		else{
			return false;
		}	
		
	}

	function set($conn, $id, $fecha_desde, $fecha_hasta, $monto, $monto_habilitado){
		$q = "UPDATE vehiculo.solvencia SET fecha_desde='$fecha_desde', fecha_hasta='$fecha_hasta', monto_normal='$monto', monto_habilitado=$monto_habilitado ";
		$q.= "WHERE id=$id";	
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $id){
		$q = "DELETE FROM vehiculo.solvencia WHERE id='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
}
?>
