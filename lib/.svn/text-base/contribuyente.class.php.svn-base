<?
class contribuyente{

	// Propiedades

	var $id;
	var $tipo_persona;
	var $nacionalidad;
	var $tipo_identificacion;
	var $identificacion;
	var $id_estado;
	var $id_municipio;
	var $id_parroquia;

	var $contribuyente;
	var $razon_social;
	var $fec_nacimiento;
	var $pasaporte;
	var $primer_nombre;
	var $segundo_nombre;
	var $primer_apellido;
	var $segundo_apellido;
	var $direccion;
	var $domicilio_fiscal;
	var $fec_registro;
	var $fec_desincorporacion;
	var $direccion_eventual;
	var $ciudad_domicilio;
/*	
	var $ciudad_nacimiento;
	var $pais_nacimiento;
	
	var $edo_domicilio;
*/	
	var $rif_letra;
	var $rif_numero;
	var $rif_control;
	var $rif;
	var $telefono;
	var $fax;	
	var $celular;
	var $email;		

	function get($conn, $id){
		$q = "SELECT * FROM vehiculo.contribuyente ";
		$q.= "WHERE id='$id'";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];   
			$this->ciudad_domicilio = $r->fields['ciudad_domicilio'];
			$this->tipo_persona = $r->fields['tipo_persona'];
			$this->nacionalidad = $r->fields['nacionalidad'];
			$this->tipo_identificacion = $r->fields['tipo_identificacion'];
			$this->identificacion = $r->fields['identificacion'];
			$this->id_estado = $r->fields['id_estado'];
			$this->id_municipio = $r->fields['id_municipio'];
			$this->id_parroquia = $r->fields['id_parroquia'];
			$this->contribuyente = $r->fields['contribuyente'];
			$this->razon_social = $r->fields['razon_social'];
			$this->fec_nacimiento = $r->fields['fec_nacimiento'];
			$this->pasaporte = $r->fields['pasaporte'];
			$this->primer_nombre = $r->fields['primer_nombre'];
			$this->segundo_nombre = $r->fields['segundo_nombre'];
			$this->primer_apellido = $r->fields['primer_apellido'];
			$this->segundo_apellido = $r->fields['segundo_apellido'];
			$this->direccion = $r->fields['direccion'];
			$this->domicilio_fiscal = $r->fields['domicilio_fiscal'];
			$this->fec_registro = $r->fields['fec_registro'];
			$this->fec_desincorporacion = $r->fields['fec_desincorporacion'];
			$this->direccion_eventual = $r->fields['direccion_eventual'];
			$this->telefono = $r->fields['telefono'];
			$this->fax = $r->fields['fax'];			
			$this->celular = $r->fields['celular'];
			$this->email = $r->fields['email'];
			$this->rif_letra = $r->fields['rif_letra'];
			$this->rif_numero = $r->fields['rif_numero'];
			$this->rif_control = $r->fields['rif_control'];
			$this->rif = $r->fields['rif'];
			$this->nombre_completo = $r->fields['primer_nombre'].' '.$r->fields['primer_apellido'];												
			return true;
		}else
			return false;
	}

	function get_all($conn, $from=0, $max=0,$orden="id"){
		$q = "SELECT * FROM vehiculo.contribuyente ";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new contribuyente;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}
	//insert
	function add($conn, $tipo_persona, $nacionalidad, $tipo_ident, $identificacion, $contribuyente, $razon_social, $fec_nacimiento, $pasaporte,
				$primer_nombre, $segundo_nombre, $primer_apellido, $segundo_apellido, $direccion, $domicilio_fiscal, $fec_registro, $fec_desincorporacion,
				$direccion_eventual, $telefono, $celular, $email, $rif_letra, $rif_numero, $rif_cntrl, $rif, $fax, $estado, $municipio, $parroquia){
		$q = "INSERT INTO vehiculo.contribuyente ";
		$q.= "(tipo_persona, nacionalidad, tipo_identificacion, identificacion, contribuyente, razon_social, fec_nacimiento, pasaporte, ";
		$q.= " primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, direccion, domicilio_fiscal, fec_registro, fec_desincorporacion, ";
		$q.= " direccion_eventual, telefono, celular, email, rif_letra, rif_numero, rif_control, rif, fax, id_estado, id_municipio, id_parroquia )";
		$q.= "VALUES ";
		$q.= "('$tipo_persona', '$nacionalidad', '$tipo_ident', '$identificacion', '$contribuyente', '$razon_social', '$fec_nacimiento', '$pasaporte', ";
		$q.= " '$primer_nombre', '$segundo_nombre', '$primer_apellido', '$segundo_apellido', '$direccion', '$domicilio_fiscal', '$fec_registro', ";
		$q.= " '$fec_desincorporacion', '$direccion_eventual', '$telefono', '$celular', '$email', '$rif_letra', '$rif_numero', '$rif_cntrl', '$rif', '$fax',
				 $estado, $municipio, $parroquia)";
		
//		die($q);
		
		if($conn->Execute($q)){
		
			return true;
		}
		else{
			return false;
		}	
		
	}
	//update
	function set($conn, $id, $tipo_persona, $nacionalidad, $tipo_ident, $identificacion, $contribuyente, $razon_social, $fec_nacimiento, $pasaporte,
				$primer_nombre, $segundo_nombre, $primer_apellido, $segundo_apellido, $direccion, $domicilio_fiscal, $fec_registro, $fec_desincorporacion,
				$direccion_eventual, $telefono, $celular, $email, $rif_letra, $rif_numero, $rif_cntrl, $rif, $fax, $estado, $municipio, $parroquia){
		$q = "UPDATE vehiculo.contribuyente SET tipo_persona = '$tipo_persona', nacionalidad='$nacionalidad', tipo_identificacion='$tipo_ident', ";
		$q.= " contribuyente='$contribuyente', razon_social='$razon_social', fec_nacimiento='$fec_nacimiento', pasaporte='$pasaporte',  ";
		$q.= " primer_nombre='$primer_nombre', segundo_nombre='$segundo_nombre', primer_apellido='$primer_apellido', segundo_apellido='$segundo_apellido', ";
		$q.= " direccion='$direccion', domicilio_fiscal='$domicilio_fiscal', fec_registro='$fec_registro', fec_desincorporacion='$fec_desincorporacion', ";
		$q.= " direccion_eventual='$direccion_eventual', telefono='$telefono', celular='$celular', email='$email', rif_letra='$rif_letra', ";
		$q.= " rif_numero='$rif_numero', rif_control='$rif_cntrl', rif='$rif', fax='$fax', id_estado=$estado, id_municipio=$municipio, id_parroquia=$parroquia ";
		$q.= "WHERE id=$id";	
//		die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $id){
		$q = "DELETE FROM vehiculo.contribuyente WHERE id='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
}
?>
