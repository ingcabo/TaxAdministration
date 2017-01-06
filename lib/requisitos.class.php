<?
class requisitos{

	// Propiedades

	var $id;
	var $id_solvencia;
	var $nombre;
	var $descripcion;
	var $solvencia;
	var $vencido;
	var $msj;

	var $total;

	function get($conn, $id){
		$q = "SELECT * FROM puser.requisitos ";
		$q.= "WHERE id='$id' ";
		//echo $q."<br>";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->id_solvencia = $r->fields['id_solvencia'];
			$this->nombre = $r->fields['nombre'];
			$this->descripcion = $r->fields['descripcion'];
			$this->solvencia = $r->fields['solvencia'];
			$this->fecha= muestrafecha($r->fields['fecha']);
			$this->vencido= $r->fields['vencido'];			
			
			return true;
		}else
			return false;
	}

	function get_all($conn, $from=1, $max=20,$orden="id"){
		$q = "SELECT * FROM puser.requisitos ";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new requisitos;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		//echo $this->total;
		return $coleccion;
	}

	function add($conn, $nombre, $descripcion, $fecha, $vencido){
		$q = "INSERT INTO puser.requisitos ";
		$q.= "( nombre, descripcion, fecha, vencido) ";
		$q.= "VALUES ";
		$q.= "('$nombre', '$descripcion', '$fecha', $vencido ) ";
		//die($q);
		if($conn->Execute($q)){
			$this->msj = REG_ADD_OK;
			return true;
		}else{
			$this->msj = ERROR;
			return false;
		}
	}

	function set($conn, $id, $nombre, $descripcion, $vencido, $today){
		$q = "UPDATE requisitos SET nombre = '$nombre', descripcion='$descripcion', ";
		$q.= "vencido=$vencido, fecha='$today' ";
		$q.= "WHERE id = '$id' ";
		//die ($q);
		if($conn->Execute($q)){
			$this->msj = REG_SET_OK;
			return true;
		}else{
			$this->msj = ERROR;
			return false;
		}
	}

	function del($conn, $id){
		try{
		$q = "DELETE FROM requisitos WHERE id='$id'";
		$r = $conn->Execute($q);
		$this->msj = REG_DEL_OK;
		return true;
		}
		catch( ADODB_Exception $e ){
			if($e->getCode()==-1){
				
				$this->msj = ERROR_CATCH_VFK;
				return false;
			}elseif($e->getCode()==-5){
				$this->msj = ERROR_CATCH_VUK;
				return false;
			}else{
				$this->msj = ERROR_CATCH_GENERICO;
				return false;
			}
		}

	}
	
	function buscar($conn, $nombre="", $max=10, $from=1, $orden="id"){
		try{
			$q = "SELECT * FROM puser.requisitos ";
			if (!empty($nombre))
				$q.= "WHERE nombre ILIKE '%$nombre%' ";
			$q.= "ORDER BY $orden ";
			//die($q);
			$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
			$collection=array();
			while(!$r->EOF){
				$ue = new requisitos;
				$ue->get($conn, $r->fields['id']);
				$coleccion[] = $ue;
				$r->movenext();
			}
			return $coleccion;
		}
		catch( ADODB_Exception $e ){
			if($e->getCode()==-1)
				return ERROR_CATCH_VFK;
			elseif($e->getCode()==-5)
				return ERROR_CATCH_VUK;
			else
				return ERROR_CATCH_GENERICO;
		}
	}
	
	function total_registro_busqueda($conn, $nombre="", $orden="id"){
		
		$q = "SELECT * FROM puser.requisitos ";
			if (!empty($nombre))
				$q.= "WHERE nombre ILIKE '%$nombre%' ";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		$total = $r->RecordCount();

		return $total;
	}
}
?>
