<?
class costo_publicidad{

	// Propiedades

	var $id_publicidad;
	var $descripcion;
	var $monto;
	var $status;
	var $tipo_publicidad;
	
	function get($conn, $cod_publicidad){
		$q = "SELECT * FROM publicidad.tipo_publicidad ";
		$q.= "WHERE cod_publicidad='$cod_publicidad'";
		//die($q);
		
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id_publicidad = $r->fields['cod_publicidad'];
			$this->descripcion = $r->fields['descripcion'];
			$this->monto = $r->fields['monto'];
			$this->status = $r->fields['status'];				
			return true;
		}else
			return false;
	}

	function get_all($conn, $from=0, $max=0,$orden="cod_publicidad"){
		$q = "SELECT * FROM publicidad.tipo_publicidad ";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new costo_publicidad;
			$ue->get($conn, $r->fields['cod_publicidad']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $descripcion, $monto, $status){
		$q = "INSERT INTO publicidad.tipo_publicidad ";
		$q.= "(descripcion, monto, status, tip_publicidad) ";
		$q.= "VALUES ";
		$q.= "('$descripcion', '$monto', '$status') ";

		//die($q);
		
		if($conn->Execute($q)){
		
			return true;
		}
		else{
			return false;
		}	
		
	}

	function set($conn, $cod_publicidad, $descripcion, $monto, $status){
		$q = "UPDATE publicidad.tipo_publicidad SET descripcion='$descripcion', monto='$monto', status='$status'";
		$q.= "WHERE cod_publicidad=$cod_publicidad";	
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $cod_publicidad){
		$q = "DELETE FROM publicidad.tipo_publicidad WHERE cod_publicidad='$cod_publicidad'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
}
?>
