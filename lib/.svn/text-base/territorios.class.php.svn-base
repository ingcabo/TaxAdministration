<?
class territorios{

	// Propiedades

	var $id;
	var $descripcion;
	var $id_parroquia;
	var $id_municipio;
	var $id_estado;
	
	var $total;

	function get($conn, $id){
		/*$q = "SELECT * FROM parroquias ";
		$q.= "WHERE id='$id'";*/
		$q="SELECT ";
		$q.="A.id, ";
		$q.="A.descripcion, ";
		$q.="A.id_parroquia, ";
		$q.="B.id_municipio, ";
		$q.="C.id_estado ";
		$q.="FROM puser.territorios AS A ";
		$q.="INNER JOIN puser.parroquias AS B ON A.id_parroquia = B.id ";
		$q.="INNER JOIN puser.municipios AS C ON B.id_municipio = C.id ";
		$q.="WHERE A.id = $id";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->descripcion = $r->fields['descripcion'];
			$this->id_parroquia = $r->fields['id_parroquia'];
			$this->id_municipio = $r->fields['id_municipio'];
			$this->id_estado = $r->fields['id_estado'];
			return true;
		}else
			return false;
	}

	function get_all($conn, $from=0, $max=0,$orden="id"){
		$q = "SELECT * FROM territorios ";
		$q.= "ORDER BY $orden ";
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new territorios;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $descripcion, $id_parroquia){
		$sql="SELECT * FROM puser.territorios WHERE descripcion ILIKE '$descripcion' AND id_parroquia = '$id_parroquia'";
		$r = $conn->Execute($sql);
		$num = $r->RecordCount();
		if($num<1){
			$q = "INSERT INTO territorios ";
			$q.= "(descripcion, id_parroquia) ";
			$q.= "VALUES ";
			$q.= "('$descripcion', $id_parroquia ) ";
			if($conn->Execute($q))
				return REG_ADD_OK;
			else
				return ERROR;
		} else { 
			return ENTIDAD_DUPLICADA;
			}
	}

	function set($conn, $id, $descripcion, $id_parroquia){
		$sql="SELECT * FROM puser.territorios WHERE descripcion ILIKE '$descripcion' AND id_parroquia = '$id_parroquia'";
		//die($sql);
		$r = $conn->Execute($sql);
		$num = $r->RecordCount();
		if($num<1){
			$q = "UPDATE territorios SET descripcion='$descripcion', id_parroquia = '$id_parroquia' ";
			$q.= "WHERE id='$id' ";
			if($conn->Execute($q))
				return REG_SET_OK;
			else
				return ERROR;
		} else { 
			return ENTIDAD_DUPLICADA;
			}
	}

	function del($conn, $id){
		$q = "DELETE FROM territorios WHERE id='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	function buscar($conn, $max=10, $from=1, $orden="id"){
		try{
			$q = "SELECT * FROM puser.territorios ";
			$q.= "ORDER BY $orden ";
			$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
			$collection=array();
			while(!$r->EOF){
				$ue = new territorios;
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
	
	function total_registro_busqueda($conn, $orden="id"){
		
		$q = "SELECT * FROM puser.territorios ";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		$total = $r->RecordCount();

		return $total;
	}
}
?>
