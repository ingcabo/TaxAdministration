<?
class espectaculo{

	// Propiedades
	var $fec_ini;
	var $fec_fin;
	var $id_contribuyente;
	var $lugar_evento;
	var $id_espectaculo;
	var $patente;
	var $fecha_registro;
	var $id_solicitud;
	var $id;
	var $espectaculo_id;
	var $entrada;
	var $entradas;
	var $tipo_de_pago;
	var	 $banco;
	var	 $nro_documento; 
	var	 $monto;

	
function get($conn, $id){
		$q = "SELECT publicidad.espectaculo.*, vehiculo.contribuyente.id AS id, 
		vehiculo.contribuyente.primer_nombre, vehiculo.contribuyente.segundo_nombre, 
		vehiculo.contribuyente.primer_apellido, vehiculo.contribuyente.segundo_apellido, 
		vehiculo.contribuyente.direccion, vehiculo.contribuyente.razon_social, 
		vehiculo.contribuyente.telefono, vehiculo.contribuyente.ciudad_domicilio, 
		vehiculo.contribuyente.domicilio_fiscal FROM publicidad.espectaculo 
		INNER JOIN vehiculo.contribuyente ON (publicidad.espectaculo.id_contribuyente=vehiculo.contribuyente.id) 
		WHERE publicidad.espectaculo.id_espectaculo = '$id'"; //die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id_espectaculo'];  
			$this->id_contribuyente = $r->fields['id_contribuyente'];
			$this->patente = $r->fields['patente'];
			$this->fecha_registro = $r->fields['fecha_registro']; 
			$this->tipo_espectaculo = $r->fields['tipo_espectaculo'];
			$this->fec_ini = $r->fields['fecha_inicio'];
			$this->fec_fin = $r->fields['fecha_fin'];
			$this->lugar_evento = $r->fields['cod_lugar_evento'];
			$this->id_solicitud = $r->fields['id_solicitud'];     
			$this->nombre_contribuyente = $r->fields['primer_nombre']." ".$r->fields['segundo_nombre']." ".$r->fields['primer_apellido']." ".$r->fields['segundo_apellido'];
			$this->razon_contribuyente = $r->fields['razon_social'];
			$this->ciudad_contribuyente = $r->fields['ciudad_domicilio'];
			$this->telefono_contribuyente = $r->fields['telefono'];
			$this->domiciliotrib_contribuyente = $r->fields['domicilio_fiscal'];
			$this->cod_espectaculo = $r->fields['cod_espectaculo'];	
			
			$w = "SELECT * FROM publicidad.calculo_espectaculo WHERE id_espectaculo=$id"; //die($w);
					$r = $conn->Execute($w);
					$i=0;
						while(!$r->EOF){
							$FamiliarAux[$i][0] = $r->fields['cant_entradas'];
							$FamiliarAux[$i][1] = $r->fields['costo_entrada'];
							$FamiliarAux[$i][2] = $r->fields['ut_espectaculo'];
						$i++;
					$r->movenext();
				}
			$this->entrada = new Services_JSON();
			$this->entrada = is_array($FamiliarAux) ? $this->entrada->encode($FamiliarAux) : false;	
		
			return true;
		}else
			return false;
	}

function get_all($conn, $from=0, $max=0,$orden="id_espectaculo"){
		$q = "SELECT * FROM publicidad.espectaculo ";
		//$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new espectaculo;
			$ue->get($conn, $r->fields['id_espectaculo']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

function add($conn, $fec_ini,$fec_fin,$id_contribuyente,$lugar_evento,$id_espectaculo,$patente,$fecha_registro,$entradas){
		$q = "INSERT INTO publicidad.espectaculo (fecha_inicio, 
												  fecha_fin, 
												  id_contribuyente,
												  Cod_lugar_evento,
												  tipo_espectaculo,
												  patente,
												  fecha_registro)";
		$q.= "VALUES ";
		$q.= " 		   ('$fec_ini',
			            '$fec_fin',
		                '$id_contribuyente',
						'$lugar_evento',
						'$id_espectaculo',
						'$patente',
						'$fecha_registro')"; //die($q);
						$conn->Execute($q);
						$sql_id_espectaculo = $conn->Execute("SELECT max(id_espectaculo) as id FROM publicidad.espectaculo"); 
						$espectaculo_id = $sql_id_espectaculo->fields['id'];
						
						$this->GuardarCarga($conn,$patente,$espectaculo_id,$entradas);
	if($q)
			return true;
		else
	 		return false;
		
	}
		
function set($conn, $id, $fec_ini,$fec_fin,$id_contribuyente,$lugar_evento,$id_espectaculo,$patente,$fecha_registro,$id_solicitud){
$q = "UPDATE publicidad.espectaculo SET fecha_inicio='$fecha_ini', fec_fin='$fec_fin', 
id_contribuyente='$id_contribuyente',lugar_evento='$lugar_evento',cod_espectaculo='$cod_espectaculo', 
patente='$patente',fecha_registro='$fecha_registro',id_solicitud='$id_solicitud' WHERE id_espectaculo=$id";
	if($conn->Execute($q))
	return true;
		else
	return false;
	}
		
function GuardarCarga($conn,$patente,$espectaculo_id,$entradas){
			$JsonRec = new Services_JSON();
			$JsonRec=$JsonRec->decode(str_replace("\\","",$entradas));
			if(is_array($JsonRec->entradas)){
				foreach($JsonRec->entradas as $familiarAux){
					$q = "INSERT INTO publicidad.calculo_espectaculo ";
					$q.= "(cant_entradas,costo_entrada,ut_espectaculo,id_espectaculo,patente) ";
					$q.= "VALUES ";
					$q.= "('$familiarAux[0]','$familiarAux[1]','$familiarAux[2]',$espectaculo_id,$patente) ";//die($q);
					$conn->Execute($q);
					$i++;
				}
			}
		}		
		
	function ActualizarGrid($conn,$espectaculo_id,$entradas,$id){
		$q = "DELETE FROM publicidad.calculo_espectaculo WHERE id_espectaculo='$id'";//die($q);
		$conn->Execute($q);
		$this->GuardarCarga($conn,$patente,$espectaculo_id,$entradas);
	}	
	
function set_espectaculo($conn, $id, $tipo_de_pago, $banco, $nro_documento, $monto){
		$montos=guardafloat($monto);
		$q = "UPDATE publicidad.espectaculo SET pago='$tipo_de_pago', banco='$banco', nro_documento='$nro_documento', ";
		$q.= "monto='$montos' WHERE id_espectaculo=$id"; // die($q);
		if($conn->Execute($q))
		return true;
			else
		return false;
	}
}
?>