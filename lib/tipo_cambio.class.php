<?
//ESTA CLASE MANEJA TODO LO RELACIONADO CON LOS TIPOS DE CAMBIOS//
class tipo_cambio{

//PROPIEDADES//
	var $id;
	var $descripcion;
	var $status;
	var $total;
	
//METODOS//

	function get($conn, $id){
		$q = "SELECT * FROM vehiculo.tipo_cambio";
		$q .= " WHERE cod_cambio=$id";
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['cod_cambio'];
			$this->descripcion = $r->fields['descripcion'];
			$this->status = $r->fields['status'];
			return true;
		}
		else{
			return false;
		}
	}
	
	function get_all($conn, $from=0, $max=0,$orden="cod_cambio"){
		$q = "SELECT * FROM vehiculo.tipo_cambio ";
		$q.= "ORDER BY $orden ";
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		while(!$r->EOF){
			$ue = new tipo_cambio;
			$ue->get($conn, $r->fields['cod_cambio']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}
	
	function add($conn, $descripcion, $status){
		$q = "INSERT INTO vehiculo.tipo_cambio ";
		$q.= "(descripcion,status) ";
		$q.= "VALUES ";
		$q.= "('$descripcion', '$status') ";

		//die($q);
		
		if($conn->Execute($q)){
		
			return true;
		}
		else{
			return false;
		}	
		
	}
	
	function set($conn, $id, $descripcion, $status){
		$q = "UPDATE vehiculo.tipo_cambio SET descripcion = '$descripcion', status='$status'";
		$q.= "WHERE cod_cambio=$id";	
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
}

?>
