<?

class clasificacion_cuenta{

# PROPIEDADES #

	var $id;
	var $descripcion;
	var $observacion;
	var $total;

# METODOS #

	function get($conn, $id){
		$q = "SELECT * FROM puser.clasificacion_cuenta ";
		$q.= "WHERE id='$id'";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->descripcion = $r->fields['descripcion'];
			$this->accion = $r->fields['accion'];
			$this->observacion = $r->fields['observacion'];			
																		
			return true;
		}else
			return false;
	}
	
	function get_all($conn, $from=0, $max=0,$orden="id"){
		$q = "SELECT * FROM puser.clasificacion_cuenta ";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new clasificacion_cuenta;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}
	
	function add($conn, $descripcion, $observacion){
		$q = "INSERT INTO puser.clasificacion_cuenta ";
		$q.= "(descripcion, observacion) ";
		$q.= "VALUES ";
		$q.= "('$descripcion', '$observacion') ";

		//die($q);
		
		if($conn->Execute($q)){
		
			return true;
		}
		else{
			return false;
		}	
		
	}
	
	function set($conn, $id, $descripcion, $observacion){
		$q = "UPDATE puser.clasificacion_cuenta SET descripcion='$descripcion', observacion='$observacion' ";
		$q.= "WHERE id=$id";	
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	function del($conn, $id){
		$q = "DELETE FROM puser.clasificacion_cuenta WHERE id='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
}

?>
