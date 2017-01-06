<?
class costo_vehiculo{

	// Propiedades

	var $cos_esp;
	var $cod_veh;
	var $desc_veh;
	var $monto;
	var $fecha_desde;
	var $fecha_hasta;
	
	function get($conn, $cod_esp){
		$q = "SELECT * FROM vehiculo.esp_costo_veh ";
		$q.= "INNER JOIN vehiculo.tipo_veh_segun_gaceta "; 
		$q.= "ON (vehiculo.esp_costo_veh.cod_veh = vehiculo.tipo_veh_segun_gaceta.cod_veh) ";
		$q.= "WHERE cod_esp='$cod_esp'";
		//die($q);
		
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['cod_esp'];
			$this->cod_veh = $r->fields['cod_veh'];
			$this->desc_veh = $r->fields['descripcion'];
			$this->monto = $r->fields['monto'];
			$this->fecha_desde = $r->fields['fecha_desde'];
			$this->fecha_hasta = $r->fields['fecha_hasta'];
																		
			return true;
		}else
			return false;
	}

	function get_all($conn, $from=0, $max=0,$orden="cod_esp"){
		$q = "SELECT * FROM vehiculo.esp_costo_veh ";
		$q.= "ORDER BY $orden "; //die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new costo_vehiculo;
			$ue->get($conn, $r->fields['cod_esp']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $cod_veh, $monto, $fecha_desde, $fecha_hasta){
		$q = "INSERT INTO vehiculo.esp_costo_veh ";
		$q.= "(cod_veh, monto, fecha_desde, fecha_hasta) ";
		$q.= "VALUES ";
		$q.= "($cod_veh, '$monto', '$fecha_desde', '$fecha_hasta') ";

		//die($q);
		
		if($conn->Execute($q)){
		
			return true;
		}
		else{
			return false;
		}	
		
	}

	function set($conn, $cod_esp, $cod_veh, $monto, $fecha_desde, $fecha_hasta){
		$q = "UPDATE vehiculo.esp_costo_veh SET cod_veh=$cod_veh, monto='$monto',fecha_desde='$fecha_desde', fecha_hasta='$fecha_hasta'";
		$q.= "WHERE cod_esp=$cod_esp";	
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $cod_esp){
		$q = "DELETE FROM vehiculo.esp_costo_veh WHERE cod_esp='$cod_esp'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
}
?>
