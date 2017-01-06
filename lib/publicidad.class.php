<?
class publicidad{

	// Propiedades
	var $id;
		var $fecha;
		var $patente;
		var $id_contribuyente;
		var $id_solicitud;
		var $cod_ins;
		var $fec_desde;
		var $fec_hasta;
		var $excento;
		var $id_exo;
		var $id_publicidad;
		var $publicidad;
		var $publicida;
		var $tipo_de_pago; 
		var $banco;
		var $nro_documento;
		var $monto;
	
	
	function get($conn, $id)
	{//die($id);
		$q = "SELECT 
					publicidad.publicidad.*, 
					vehiculo.contribuyente.id AS contribuyente, 
					vehiculo.contribuyente.primer_nombre, 
					vehiculo.contribuyente.segundo_nombre, 
					vehiculo.contribuyente.primer_apellido, 
					vehiculo.contribuyente.segundo_apellido, 
					vehiculo.contribuyente.direccion, 
					vehiculo.contribuyente.razon_social, 
					vehiculo.contribuyente.telefono, 
					vehiculo.contribuyente.ciudad_domicilio, 
					vehiculo.contribuyente.domicilio_fiscal, 
					publicidad.calculo_publicidad.* 
				FROM 
					publicidad.publicidad 
				INNER JOIN 
					vehiculo.contribuyente 
				ON 
					(publicidad.publicidad.id_contribuyente=vehiculo.contribuyente.id) 
				INNER JOIN 
					publicidad.calculo_publicidad 
				ON 
					(publicidad.publicidad.patente=publicidad.calculo_publicidad.patente) 
				AND 
					(publicidad.publicidad.id = publicidad.calculo_publicidad.id_publicidad) 
				WHERE 
					publicidad.publicidad.id = '$id'";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];  
			$this->fecha = $r->fields['fecha_actual']; 
			$this->patente = $r->fields['patente']; 
			$this->id_contribuyente = $r->fields['id_contribuyente'];
			$this->cod_ins = $r->fields['cod_inspector'];
			$this->nombre_contribuyente = $r->fields['primer_nombre']." ".$r->fields['segundo_nombre']." ".$r->fields['primer_apellido']." ".$r->fields['segundo_apellido'];
			$this->razon_contribuyente = $r->fields['razon_social'];
			$this->ciudad_contribuyente = $r->fields['ciudad_domicilio'];
			$this->telefono_contribuyente = $r->fields['telefono'];
			$this->domiciliotrib_contribuyente = $r->fields['domicilio_fiscal'];
			$this->id_solicitud = $r->fields['id_solicitud'];
			$this->fec_desde = $r->fields['fec_desde'];
			$this->fec_hasta = $r->fields['fec_hasta'];
			
		$w = "SELECT * FROM publicidad.calculo_publicidad WHERE id_publicidad=$id";
					$r = $conn->Execute($w);
					$i=0;
						while(!$r->EOF){
							$FamiliarAux[$i][0] = $r->fields['id_tipopub'];
							$FamiliarAux[$i][1] = $r->fields['cantidad'];
							$FamiliarAux[$i][2] = $r->fields['unidad'];
							$FamiliarAux[$i][3] = $r->fields['aforo'];
						$i++;
					$r->movenext();
				}
			$this->publicida = new Services_JSON();
			$this->publicida = is_array($FamiliarAux) ? $this->publicida->encode($FamiliarAux) : false;	
			return true;
		}else
			return false;
	}

function get_all($conn, $from=0, $max=0,$orden="id"){
		$q = "SELECT * FROM publicidad.publicidad ";
		//$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new publicidad;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

		function add($conn,$id_contribuyente,$patente,$id_solicitud,$cod_ins,$fecha,$fec_desde, $fec_hasta,$publicidad){
		$q = "INSERT INTO publicidad.publicidad (id_contribuyente, 
												 patente, 
												 id_solicitud, 
												 cod_inspector, 
												 fecha, 
												 fec_desde, 
												 fec_hasta) VALUES
		 										 ('$id_contribuyente',
												 '$patente',
												 '$id_solicitud',
												 '$cod_ins',
												 '$fecha',
												 '$fec_desde',
												 '$fec_hasta')";		 //die($q);
			$conn->Execute($q);	
			$sql_id_publicidad = $conn->Execute("SELECT max(id) as ids FROM publicidad.publicidad");
			$publicidad_id = $sql_id_publicidad->fields['ids'];//die($publicidad_id);
			
			$this->GuardarCarga($conn, $patente, $publicidad_id, $publicidad);			
			if($q)
					return true;
				else
					return false;
				
	}
		
function set($conn,$id, $id_contribuyente,$patente,$id_solicitud,$cod_ins,$fecha,$fec_desde, $fec_hasta,$publicidad){
$q = "UPDATE publicidad.espectaculo SET fec_desde='$fec_desde', fec_hasta='$fec_hasta', cod_inspector='$cod_ins', id_contribuyente='$id_contribuyente', patente='$patente',fecha='$fecha',id_solicitud='$id_solicitud' WHERE id=$id";
		$this->ActualizarGrid($conn,$espectaculo_id,$publicidad,$id);
	if($conn->Execute($q))
	return true;
		else
	return false;
	}
	
	
function set_publicidad($conn, $id, $tipo_de_pago, $banco, $nro_documento, $monto){
	$montos=guardafloat($monto);
$q = "UPDATE publicidad.publicidad SET pago='$tipo_de_pago', banco='$banco', 
nro_documento='$nro_documento', monto='$montos' WHERE id=$id";//die($q);
	if($conn->Execute($q))
	return true;
		else
	return false;
	}
		
		
function GuardarCarga($conn, $patente, $publicidad_id, $publicidad){//die($publicidad);
			$JsonRec = new Services_JSON();
			$JsonRec=$JsonRec->decode(str_replace("\\","", $publicidad));
			if(is_array($JsonRec->publicidad)){
				foreach($JsonRec->publicidad as $familiarAux){
					$q = "INSERT INTO publicidad.calculo_publicidad ";
					$q.= "(id_tipopub, cantidad, unidad, aforo, patente, id_publicidad) ";
					$q.= "VALUES ";
					$q.= "('$familiarAux[0]','$familiarAux[1]', 
					'$familiarAux[2]','$familiarAux[3]', '$patente','$publicidad_id') ";//die($q); 
					$conn->Execute($q);
					$i++;
				}
			}
		}		

		function GuardarCargaGrid($conn, $patente, $publicidad_id, $publicidad,$id){//die($publicidad);
			$JsonRec = new Services_JSON();
			$JsonRec=$JsonRec->decode(str_replace("\\","", $publicidad));
			if(is_array($JsonRec->publicidad)){
				foreach($JsonRec->publicidad as $familiarAux){
					$q = "INSERT INTO publicidad.calculo_publicidad ";
					$q.= "(id_tipopub, cantidad, unidad, aforo, patente, id_publicidad) ";
					$q.= "VALUES ";
					$q.= "('$familiarAux[0]','$familiarAux[1]', 
					'$familiarAux[2]','$familiarAux[3]', '$patente','$id') ";//die($q); 
					$conn->Execute($q);
					$i++;
				}
			}
		}
		
		function ActualizarGrid($conn,$espectaculo_id,$publicidad,$id){
			$q = "DELETE FROM publicidad.calculo_espectaculo WHERE id_espectaculo='$id'";//die($q);
			$conn->Execute($q);
			$this->GuardarCargaGrid($conn,$id,$patente,$espectaculo_id,$publicidad);
		}	
		
}

?>