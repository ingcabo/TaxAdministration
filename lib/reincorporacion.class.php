<?
class reincorporacion{

	// Propiedades

	var $id;
	var $serial_carroceria;
	var $marca;
	var $placa;
	var $id_contribuyente;
	var $anio_veh;
	var $serial_motor;
	var $cod_mar;
	var $cod_mod;
	var $cod_col;
	var $cod_uso;
	var $cod_tip;
	var $fec_compra;
	var $peso_kg;
	var $cant_eje;
	var $precio;
	var $nro_patente;
	var $observacion;
	var $exento;
	var $status;
	var $anio_pago;
	var $per_pago;
	var $cod_lin;
	var $concesionario;
	var $puestos;
	var $primera_vez;
	var $fecha_reincorporacion;
	var $cod_vehiculo;
	var $check_reincorporar;
	
	
	

	function get($conn, $id){
		$q = "SELECT * FROM vehiculo.vehiculo ";
		$q.= "WHERE id='$id'";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->serial_carroceria = $r->fields['serial_carroceria'];
			$this->placa = $r->fields['placa'];
			$this->id_contribuyente = $r->fields['id_contribuyente'];
			$this->anio_veh = $r->fields['anio_veh'];
			$this->serial_motor = $r->fields['serial_motor'];
			$this->cod_mar = $r->fields['cod_mar'];
			$this->cod_mod = $r->fields['cod_mod'];
			$this->cod_col = $r->fields['cod_col'];
			$this->cod_uso = $r->fields['cod_uso'];
			$this->cod_tip = $r->fields['cod_tip'];
			$this->fec_compra = $r->fields['fec_compra'];
			$this->peso_kg = $r->fields['peso_kg'];
			$this->cant_eje = $r->fields['cant_eje'];
			$this->precio = $r->fields['precio'];
			$this->nro_patente = $r->fields['nro_patente'];
			$this->observacion = $r->fields['observacion'];
			$this->exento = $r->fields['exento'];
			$this->status = $r->fields['status'];
			$this->anio_pago = $r->fields['anio_pago'];
			$this->per_pago = $r->fields['per_pago'];
			$this->cod_lin = $r->fields['cod_lin'];
			$this->concesionario = $r->fields['concesionario'];
			$this->puestos = $r->fields['puestos'];
			$this->primera_vez = $r->fields['primera_vez'];
			
			
													
			return true;
		}else
			return false;
	}

	function get_all($conn, $from=0, $max=0,$orden="id"){
		$q = "SELECT * FROM vehiculo.vehiculo ";
		$q.= "ORDER BY $orden ";
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new reincorporacion;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $id_contribuyente, $cod_vehiculo, $fecha_reincorporacion, $check_reincorporar){
		
	
		$q = "INSERT INTO vehiculo.veh_reincorporado ";
		$q.= "(cod_contribuyente, cod_vehiculo, fecha_reincorporacion) ";
		$q.= "VALUES ";
		$q.= "($id_contribuyente, '$cod_vehiculo', '$fecha_reincorporacion') ";
		
		$w = "UPDATE vehiculo.vehiculo SET desincorporado=$check_reincorporar ";
		$w.= "WHERE id=$cod_vehiculo";

		//die($q);
		
		$resul_q = $conn->Execute($q);
		$resul_w = $conn->Execute($w);
				
		if($resul_q and $resul_w){
		
			return true;
		}
		else{
			return false;
		}	
		
	}

	function set($conn, $id, $id_contribuyente, $serial_carroceria, $placa, $fec_crea, $anio_veh, $serial_motor, $cod_mar, $cod_mod, $cod_col, $cod_uso,
					$cod_tip, $fec_compra, $peso_kg, $cant_eje, $precio, $observacion, $exento, $anio_pago, $cod_lin, $concesionario, $primera_vez){
		$q = "UPDATE vehiculo.vehiculo SET id_contribuyente = $id_contribuyente, serial_carroceria='$serial_carroceria', placa='$placa',
				fec_mod='$fec_crea', anio_veh='$anio_veh', serial_motor='$serial_motor', cod_mar='$cod_mar', cod_mod='$cod_mod', cod_col='$cod_col',
				cod_uso='$cod_uso', cod_tip=$cod_tip, fec_compra='$fec_compra', peso_kg=$peso_kg, cant_eje=$cant_eje, precio='$precio',
				observacion='$observacion', exento='$exento', anio_pago=$anio_pago, cod_lin=$cod_lin, concesionario=$concesionario, primera_vez=$primera_vez ";
		$q.= "WHERE id=$id";	
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $id){
		$q = "DELETE FROM vehiculo.vehiculo WHERE id='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
}
?>
