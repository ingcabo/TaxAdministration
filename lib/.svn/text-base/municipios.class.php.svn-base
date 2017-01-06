<?
class municipios{

	// Propiedades

	var $id;
	var $descripcion;
	var $id_estado;
	var $total;
	var $alcaldia;

	function get($conn, $id){
		$q = "SELECT * FROM puser.municipios ";
		$q.= "WHERE id='$id'";
		
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->descripcion = $r->fields['descripcion'];
			$this->id_estado = $r->fields['id_estado'];
			$this->alcaldia = $r->fields['alcaldia'];
			return true;
		}else
			return false;
	}

	function get_all($conn, $orden="id"){
		$q = "SELECT * FROM puser.municipios ";
		$q.= "ORDER BY $orden ";
		$r = $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new municipios;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $descripcion, $id_estado, $alcaldia){
		$sql="SELECT * FROM puser.municipios WHERE descripcion ILIKE '$descripcion' AND id_estado = '$id_estado'";
		$r = $conn->Execute($sql);
		$num = $r->RecordCount();
		if($num<1){
			$q = "INSERT INTO puser.municipios ";
			$q.= "(descripcion, id_estado, alcaldia) ";
			$q.= "VALUES ";
			$q.= "('$descripcion', '$id_estado', '$alcaldia') "; //die($q);
			if($conn->Execute($q))
				return REG_ADD_OK;
			else
				return ERROR;
		} else { 
			return ENTIDAD_DUPLICADA;
			}
	}

	function set($conn, $id, $descripcion, $id_estado, $alcaldia){
		/*$sql="SELECT * FROM puser.municipios WHERE descripcion ILIKE '$descripcion' AND id_estado = '$id_estado'";
		$r = $conn->Execute($sql);
		$num = $r->RecordCount();
		if($num<1){*/
			$q = "UPDATE puser.municipios SET descripcion='$descripcion', id_estado='$id_estado', alcaldia = '$alcaldia' ";
			$q.= "WHERE id='$id' ";
			if($conn->Execute($q))
				return REG_SET_OK;
			else
				return ERROR;
		/*} else { 
			return ENTIDAD_DUPLICADA;
			}*/
	}

	function del($conn, $id){
		$q = "DELETE FROM puser.municipios WHERE id='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	function buscar($conn, $descripcion, $estado, $max=10, $from=1, $orden="id"){
		try{
			$q = "SELECT * FROM puser.municipios ";
			$q.= "WHERE 1=1 ";
			$q.= !empty($descripcion) ? "AND descripcion ILIKE '%$descripcion%' " : "";
			$q.= !empty($estado) ? "AND id_estado = '$estado' " : "";
			$q.= "ORDER BY $orden ";
			//die($q);
			$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
			$collection=array();
			while(!$r->EOF){
				$ue = new municipios;
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
	
	function total_registro_busqueda($conn, $descripcion,$estado, $orden="id"){
		
		$q = "SELECT * FROM puser.municipios ";
		$q.= "WHERE 1=1 ";
		$q.= !empty($descripcion) ? "AND descripcion ILIKE '%$descripcion%' " : "";
		$q.= !empty($estado) ? "AND id_estado = '$estado' " : "";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		$total = $r->RecordCount();

		return $total;
	}
}
?>