<?
/*if(empty(status)){
status=0;
}*/
class veh_modelo{

	// Propiedades
	var $id;
	var $mod_nom;
	var $status;
	var $total;
	var $marca;
	function get($conn, $id){
		$q = "SELECT * FROM vehiculo.modelo ";
		$q.= "WHERE cod_mod='$id'"; 
		$r = $conn->Execute($q) ;
		if(!$r->EOF){
			$this->id = $r->fields['cod_mod'];
			$this->mod_nom = $r->fields['descripcion'];
			$this->status = $r->fields['status'] ;
			$vh= new veh_marca;
			$vh->get($conn,$r->fields['cod_mar']);
			$this->marca = $vh;
			return true;
		}else
			return false;
	}
	function get_all($conn, $orden="cod_mod"){
		$q = "SELECT * FROM vehiculo.modelo ";
		$q.= "ORDER BY $orden "; 
		$r = $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new veh_modelo;
			$ue->get($conn, $r->fields['cod_mod']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $mod_nom, $status, $cod_mar){
		$q = "INSERT INTO vehiculo.modelo ";
		$q.= "(descripcion, status, cod_mar) ";
		$q.= "VALUES ";
		$q.= "('$mod_nom', '$status', '$cod_mar' ) "; //die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function set($conn, $id, $mod_nom, $status, $cod_mar){	
		$q = "UPDATE vehiculo.modelo SET descripcion='$mod_nom', status='$status', cod_mar='$cod_mar' ";
		$q.= "WHERE cod_mod='$id' "; 
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $id){
		$q = "DELETE FROM vehiculo.modelo ";
		$q.= "WHERE cod_mod = '$id' ";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	function get_marca($conn, $idm){
		$q= "SELECT * FROM vehiculo.modelo WHERE cod_mar= '$idm' "; //die($q);
		$r = $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$vm = new veh_modelo;
			$vm->get($conn, $r->fields['cod_mod']);
			$coleccion[] = $vm;
			$r->movenext();
		}
		return $coleccion;
	}
		
}
?>