<?
/*if(empty(status)){
status=0;
}*/
class veh_color{

	// Propiedades
	var $id;
	var $color_nom;
	var $status;
	var $total;
	function get($conn, $id){
		$q = "SELECT * FROM vehiculo.colores ";
		$q.= "WHERE cod_col='$id'"; 
		$r = $conn->Execute($q) ;
		if(!$r->EOF){
			$this->id = $r->fields['cod_col'];
			$this->color_nom = $r->fields['descripcion'];
			$this->status = $r->fields['status'];
			return true;
		}else
			return false;
	}
	function get_all($conn, $orden="cod_col"){
		$q = "SELECT * FROM vehiculo.colores ";
		$q.= "ORDER BY $orden "; 
		$r = $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new veh_color;
			$ue->get($conn, $r->fields['cod_col']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $color_nom, $status){
		$q = "INSERT INTO vehiculo.colores ";
		$q.= "(descripcion, status) ";
		$q.= "VALUES ";
		$q.= "('$color_nom', '$status' ) ";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function set($conn, $id, $color_nom, $status){	
		$q = "UPDATE vehiculo.colores SET descripcion='$color_nom', status='$status' ";
		$q.= "WHERE cod_col='$id' "; 
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $id){
		$q = "DELETE FROM vehiculo.colores ";
		$q.= "WHERE cod_col = '$id' ";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
}
?>