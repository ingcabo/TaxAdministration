<?
class lugar_evento{

	// Propiedades

	var $id_lugar_evento;
	var $descripcion;
	var $status;
		
	function get($conn, $cod_lugar_evento){
		$q = "SELECT * FROM publicidad.lugar_evento ";
		$q.= "WHERE cod_lugar_evento='$cod_lugar_evento'";
		//die($q);
		
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id_lugar_evento = $r->fields['cod_lugar_evento'];
			$this->descripcion = $r->fields['descripcion'];
			$this->status = $r->fields['status'];
			return true;
		}else
			return false;
	}

	function get_all($conn, $from=0, $max=0,$orden="cod_lugar_evento"){
		$q = "SELECT * FROM publicidad.lugar_evento ";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new lugar_evento;
			$ue->get($conn, $r->fields['cod_lugar_evento']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $descripcion, $status){
		$q = "INSERT INTO publicidad.lugar_evento ";
		$q.= "(descripcion, status) ";
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

	function set($conn, $cod_lugar_evento, $descripcion, $status){
		$q = "UPDATE publicidad.lugar_evento SET descripcion='$descripcion', status='$status'";
		$q.= "WHERE cod_lugar_evento='$cod_lugar_evento'";	
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $cod_espectaculo){
		$q = "DELETE FROM publicidad.lugar_evento WHERE cod_lugar_evento='$cod_lugar_evento'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
}
?>
