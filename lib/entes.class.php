<?
class entes{

	// Propiedades

	var $id;
	var $nombre;
	var $siglas;
	var $direccion; 
	var $datoCreacion;
	var $actividad;
	var $msj;

	var $total;

	function get($conn, $id){
		$q = "SELECT * FROM puser.entes WHERE id='$id' ";//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->nombre = $r->fields['nomb_ente'];
			$this->siglas = $r->fields['sigl_ente'];
			$this->direccion = $r->fields['direc_ente'];
			$this->datoCreacion = $r->fields['dato_crea_ente'];
			$this->actividad = $r->fields['acti_ente'];
			return true;
		}else
			return false;
	}

	function get_all($conn, $from=0, $max=0,$orden="id"){
		$q = "SELECT * FROM entes ";
		$q.= "ORDER BY $orden ";
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		
		while(!$r->EOF){
			$ue = new entes;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}
	
	function add($conn,$nombre, $siglas, $direccion, $datos, $actividad){
		$q = "INSERT INTO puser.entes (nomb_ente, sigl_ente, direc_ente, dato_crea_ente, acti_ente) ";
		$q.= "VALUES ('$nombre', '$siglas', '$direccion', '$datos', '$actividad' )"; 
		//die($q);
		$r = $conn->Execute($q);
		if($r){
			$this->msj =  REG_ADD_OK;
			return true;
		} else {
			$this->msj =  ERROR;
			return ERROR;
		}
	}

	function set($conn, $id, $nombre, $siglas, $direccion, $datos, $actividad){
		$q = "UPDATE entes SET nomb_ente ='$nombre', ";
		$q.= "sigl_ente = '$siglas', direc_ente = '$direccion', ";
		$q.= "dato_crea_ente = '$datos', acti_ente = '$actividad' ";
		$q.= "WHERE id='$id' ";
		//die($q);
		if($conn->Execute($q)){
			$this->msj = REG_SET_OK; 
			return true;
		}else{
			$this->msj = ERROR;
			return false;
		}
	}

	function del($conn, $id){
		$q = "DELETE FROM puser.entes WHERE id='$id'";
		//die($q);
		if($conn->Execute($q)){
			$this->msj = REG_DEL_OK;
			return true;
		} else {
			$this->msj = ERROR;
			return false;
		}
	}

	function buscar($conn, $nombre, $siglas, $orden="id",$from,$max){
		if(empty($id) && empty($nombre) && empty($siglas))
			return false;
		$q = "SELECT * FROM puser.entes ";
		$q.= "WHERE  1=1 ";
		$q.= !empty($id) ? "AND id = '$id'  ":"";
		$q.= !empty($nombre) ? "AND nomb_ente ILIKE '%$nombre%'  ":"";
		$q.= !empty($siglas) ? "AND sigl_ente ILIKE '%$siglas%'  ":"";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new entes;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		return $coleccion;
	}
	
	function totalRegistroBusqueda($conn,$nombre,$siglas,$orden="id"){
		if(empty($nombre) && empty($siglas))
			return 0;
		$q = "SELECT * FROM puser.entes ";
		$q.= "WHERE  1=1 ";
		$q.= !empty($nombre) ? "AND nomb_ente ILIKE '%$nombre%'  ":"";
		$q.= !empty($siglas) ? "AND sigl_ente ILIKE '%$siglas%' ":"";
		$q.= "ORDER BY $orden ";
		//die($q);
		if($r = $conn->Execute($q))
			return $r->RecordCount();
	}
	
	
}
?>
