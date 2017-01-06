<?
class parroquias{

	// Propiedades

	var $id;
	var $descripcion;
	var $id_municipio;
	var $id_estado;
	
	var $total;

	function get($conn, $id){
		/*$q = "SELECT * FROM parroquias ";
		$q.= "WHERE id='$id'";*/
		$q="SELECT ";
		$q.="puser.parroquias.id, ";
		$q.="puser.parroquias.descripcion, ";
		$q.="puser.parroquias.id_municipio, ";
		$q.="puser.municipios.id_estado ";
		$q.="FROM puser.parroquias ";
		$q.="Inner Join puser.municipios ON puser.parroquias.id_municipio = puser.municipios.id ";
		$q.="WHERE puser.parroquias.id = $id";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->descripcion = $r->fields['descripcion'];
			$this->id_municipio = $r->fields['id_municipio'];
			$this->id_estado = $r->fields['id_estado'];
			return true;
		}else
			return false;
	}

	function get_all($conn, $from=0, $max=0,$orden="id"){
		$q = "SELECT * FROM parroquias ";
		$q.= "ORDER BY $orden ";
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new parroquias;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $descripcion, $id_municipio){
		$sql="SELECT * FROM puser.parroquias WHERE descripcion ILIKE '$descripcion' AND id_municipio = '$id_municipio'";
		$r = $conn->Execute($sql);
		$num = $r->RecordCount();
		if($num<1){
			$q = "INSERT INTO parroquias ";
			$q.= "(descripcion, id_municipio) ";
			$q.= "VALUES ";
			$q.= "('$descripcion', $id_municipio ) ";
			if($conn->Execute($q))
				return REG_ADD_OK;
			else
				return ERROR;
		} else { 
			return ENTIDAD_DUPLICADA;
			}
	}

	function set($conn, $id, $descripcion, $id_municipio){
		$sql="SELECT * FROM puser.parroquias WHERE descripcion ILIKE '$descripcion' AND id_municipio = '$id_municipio'";
		//die($sql);
		$r = $conn->Execute($sql);
		$num = $r->RecordCount();
		if($num<1){
			$q = "UPDATE parroquias SET descripcion='$descripcion', id_municipio = '$id_municipio' ";
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
		$q = "DELETE FROM parroquias WHERE id='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	function buscar($conn,$descripcion,$estado, $max=10, $from=1, $orden="id"){
		try{
			$q = "SELECT p.*, e.descripcion AS estado, m.descripcion AS municipio FROM puser.parroquias p ";
			$q.= "INNER JOIN puser.municipios m ON (p.id_municipio = m.id) ";
			$q.= "INNER JOIN puser.estado e ON (m.id_estado = e.id) ";
			$q.= "WHERE 1=1 ";
			$q.= !empty($descripcion) ? "AND p.descripcion ILIKE '%$descripcion%' " : "";
			//$q.= !empty($municipio) ? "AND m.id = $municipio " : "";
			$q.= !empty($estado) ? "AND m.id = $estado " : "";
			$q.= "ORDER BY $orden ";
			//die($q);
			$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
			$collection=array();
			while(!$r->EOF){
				$ue = new parroquias;
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
	
	function total_registro_busqueda($conn,$descripcion,$estado, $orden="id"){
		
		$q = "SELECT p.*, e.descripcion AS estado, m.descripcion AS municipio FROM puser.parroquias p ";
		$q.= "INNER JOIN puser.municipios m ON (p.id_municipio = m.id) ";
		$q.= "INNER JOIN puser.estado e ON (m.id_estado = e.id) ";
		$q.= "WHERE 1=1 ";
		$q.= !empty($descripcion) ? "AND p.descripcion ILIKE '%$descripcion%' " : "";
		$q.= !empty($estado) ? "AND m.id = $estado " : "";
		//$q.= !empty($estado) ? "AND e.id = $estado " : "";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		$total = $r->RecordCount();

		return $total;
	}
}
?>
