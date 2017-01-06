<?

class tipo_movimiento{

# PROPIEDADES #

	var $id;
	var $descripcion;
	var $accion;
	var $total;

# METODOS #

	function get($conn, $id){
		$q = "SELECT * FROM puser.tipo_movimiento ";
		$q.= "WHERE id='$id'";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->descripcion = $r->fields['descripcion'];
			$this->accion = $r->fields['accion'];
																		
			return true;
		}else
			return false;
	}
	
	function get_all($conn, $from=0, $max=0,$orden="id"){
		$q = "SELECT * FROM puser.tipo_movimiento ";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new tipo_movimiento;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}
	
	function add($conn, $descripcion, $accion){
		$q = "INSERT INTO puser.tipo_movimiento ";
		$q.= "(descripcion, accion) ";
		$q.= "VALUES ";
		$q.= "('$descripcion', '$accion') ";

		//die($q);
		
		if($conn->Execute($q)){
		
			return true;
		}
		else{
			return false;
		}	
		
	}
	
	function set($conn, $id, $descripcion, $accion){
		$q = "UPDATE puser.tipo_movimiento SET descripcion='$descripcion', accion='$accion' ";
		$q.= "WHERE id=$id";	
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	function del($conn, $id){
		$q = "DELETE FROM puser.tipo_movimiento WHERE id='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
}

?>