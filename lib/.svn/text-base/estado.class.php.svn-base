<?
class estado{

	// Propiedades

	var $id;
	var $descripcion;

	var $total;

	function get($conn, $id){
		$q = "SELECT * FROM puser.estado ";
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
		$q = "SELECT * FROM puser.estado ";
		$q.= "ORDER BY $orden ";
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new estado;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $descripcion){
		$sql="SELECT * FROM puser.estado WHERE descripcion ILIKE '$descripcion'";
		$r = $conn->Execute($sql);
		$num = $r->RecordCount();
		if($num<1){
		
			$q = "INSERT INTO puser.estado ";
			$q.= "(descripcion) ";
			$q.= "VALUES ";
			$q.= "('$descripcion' ) ";
			if($conn->Execute($q))
				return REG_ADD_OK;
			else
				return ERROR;
		} else {
			return ENTIDAD_DUPLICADA;
		}
	}	

	function set($conn, $id, $descripcion){
		$sql="SELECT * FROM puser.estado WHERE descripcion ILIKE '$descripcion'";
		$r = $conn->Execute($sql);
		$num = $r->RecordCount();
		if($num<1){
			$q = "UPDATE puser.estado SET descripcion='$descripcion' ";
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
		$q = "DELETE FROM puser.estado WHERE id='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	function buscar($conn, $max=10, $from=1, $orden="id", $desc="")
	{
		try{
			$q = "SELECT id FROM puser.estado ";
			if ($desc!='')
				$q.= "WHERE descripcion ILIKE '%$desc%' ";
			$q.= "ORDER BY $orden ";
			//die($q);
			$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
			$collection=array();
			while(!$r->EOF)
			{
				$ue = new estado;
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
	
	function total_registro_busqueda($conn, $orden="id", $desc="")
	{
		$q = "SELECT id FROM puser.estado ";
		if ($desc!="")
			$q.= "WHERE descripcion ILIKE '%$desc%' ";
		$q.= "ORDER BY $orden ";
		$r = $conn->Execute($q);
		$total = $r->RecordCount();

		return $total;
	}
}
?>