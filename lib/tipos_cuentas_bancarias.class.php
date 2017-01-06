<?

class tipos_cuentas_bancarias{

# PROPIEDADES #

	var $id;
	var $descripcion;
	var $total;

# METODOS #

	function get($conn, $id){
		$q = "SELECT * FROM puser.tipos_cuentas_bancarias ";
		$q.= "WHERE id='$id'";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->descripcion = $r->fields['descripcion'];
																					
			return true;
		}else
			return false;
	}
	
	function get_all($conn, $from=0, $max=0,$orden="id"){
		$q = "SELECT * FROM puser.tipos_cuentas_bancarias ";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new tipos_cuentas_bancarias;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}
	
	function add($conn, $descripcion){
		$q = "INSERT INTO puser.tipos_cuentas_bancarias ";
		$q.= "(descripcion) ";
		$q.= "VALUES ";
		$q.= "('$descripcion') ";

		//die($q);
		
		if($conn->Execute($q)){
		
			return true;
		}
		else{
			return false;
		}	
		
	}
	
	function set($conn, $id, $descripcion){
		$q = "UPDATE puser.tipos_cuentas_bancarias SET descripcion='$descripcion' ";
		$q.= "WHERE id=$id";	
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	function del($conn, $id){
		$q = "DELETE FROM puser.tipos_cuentas_bancarias WHERE id='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
}

?>