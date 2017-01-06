<?
class clasificacion_espectaculo{

	// Propiedades

	var $id;
	var $descripcion;
	var $status;
	var $total;
	function get($conn, $id){
		$q = "SELECT * FROM publicidad.clasificacion_evento ";
		$q.= "WHERE cod_lugar_evento='$id'";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['cod_lugar_evento'];
			$this->descripcion = $r->fields['descripcion'];
			$this->status = $r->fields['status'];
			
															
			return true;
		}else
			return false;
	}

	function get_all($conn, $orden="cod_lugar_evento"){
		$q = "SELECT * FROM publicidad.clasificacion_evento ";
		$q.= "ORDER BY $orden "; 
		$r = $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new clasificacion_espectaculo;
			$ue->get($conn, $r->fields['cod_lugar_evento']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $descripcion, $status){
		$q = "INSERT INTO publicidad.clasificacion_evento ";
		$q.= "(descripcion, status) ";
		$q.= "VALUES ";
		$q.= "('$descripcion', $status) ";

		//die($q);
		
		if($conn->Execute($q)){
		
			return true;
		}
		else{
			return false;
		}	
		
	}

	function set($conn, $id, $descripcion, $status){
		$q = "UPDATE publicidad.clasificacion_evento SET descripcion='$descripcion', status=$status ";
		$q.= "WHERE cod_lugar_evento=$id";	
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $id){
		$q = "DELETE FROM publicidad.clasificacion_evento WHERE cod_lugar_evento='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
}
?>
