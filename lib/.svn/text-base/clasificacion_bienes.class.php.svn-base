<?
class clasificacion_bienes{

	// Propiedades

	var $id;
	var $descripcion;
	var $subgrupo;
	var $grupo;
	var $id_grupo;
	var $total;

	function get($conn, $id){
		/*$q = "SELECT * FROM parroquias ";
		$q.= "WHERE id='$id'";*/
		$q="SELECT ";
		$q.="rcb.id, ";
		$q.="rcb.descripcion, ";
		$q.="rcb.codigo AS subgrupo, ";
		$q.="rcb.id_grupo AS id_grupo, ";
		$q.="cb.codigo AS grupo ";
		$q.="FROM puser.relacion_clasificacion_bienes rcb ";
		$q.="Inner Join puser.clasificador_bienes cb ON cb.id = rcb.id_grupo ";
		$q.="WHERE rcb.id = $id";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->descripcion = $r->fields['descripcion'];
			$this->subgrupo = $r->fields['subgrupo'];
			$this->grupo = $r->fields['grupo'];
			$this->id_grupo = $r->fields['id_grupo'];
			return true;
		}else
			return false;
	}

	function get_all($conn, $from=0, $max=0,$orden="id"){
		$q = "SELECT * FROM puser.relacion_clasificacion_bienes ";
		$q.= "ORDER BY $orden ";
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new clasificacion_bienes;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $cb;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $id_grupo, $codigo, $descripcion){
		/*$sql="SELECT MAX(rcb.codigo) AS maximo, cb.codigo FROM puser.relacion_clasificacion_bienes rcb ";
		$sql.= "INNER JOIN puser.clasificador_bienes cb ON rcb.id_grupo = cb.id  ";
		$sql.= "WHERE id_grupo ='$descripcion' '";
		$sql.= "GROUP BY 2";
		$r = $conn->Execute($sql);
		$longitud = strlen($r->fields['codigo']);
		$newCodigo = $r->fields['codigo']."-".str_pad(substr($r->fields['maximo'], $longitud+1, 3) + 1, 3, 0, STR_PAD_LEFT);
		die($codigo);*/
			$q = "INSERT INTO puser.relacion_clasificacion_bienes ";
			$q.= "(id_grupo, descripcion, codigo) ";
			$q.= "VALUES ";
			$q.= "($id_grupo, '$descripcion', '$codigo') ";
			//die($q);
			$r = $conn->Execute($q);
			if($r)
				return REG_ADD_OK;
			else
				return ERROR;
	}

	function set($conn, $id, $id_grupo, $codigo, $descripcion){
	
		$q = "UPDATE puser.relacion_clasificacion_bienes SET descripcion='$descripcion' ";
		$q.= "WHERE id='$id' ";
		if($conn->Execute($q))
			return REG_SET_OK;
		else
			return ERROR;
	}

	function del($conn, $id){
		$q = "DELETE FROM puser.relacion_clasificacion_bienes WHERE id='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	function buscar($conn,$descripcion,$grupo, $max=10, $from=1, $orden="codigo"){
		try{
			$q = "SELECT rcb.*, cb.descripcion AS descrip FROM puser.relacion_clasificacion_bienes rcb ";
			$q.= "INNER JOIN puser.clasificador_bienes cb ON (rcb.id_grupo = cb.id) ";
			$q.= "WHERE 1=1 ";
			$q.= !empty($descripcion) ? "AND rcb.descripcion ILIKE '%$descripcion%' " : "";
			$q.= !empty($grupo) ? "AND rcb.id_grupo = $grupo " : "";
			$q.= "ORDER BY $orden ";
			//die($q);
			$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
			$collection=array();
			while(!$r->EOF){
				$ue = new clasificacion_bienes;
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
	
	function total_registro_busqueda($conn,$descripcion,$grupo, $orden="id"){
		
		$q = "SELECT rcb.*, cb.descripcion AS descrip FROM puser.relacion_clasificacion_bienes rcb ";
		$q.= "INNER JOIN puser.clasificador_bienes cb ON (rcb.id_grupo = cb.id) ";
		$q.= "WHERE 1=1 ";
		$q.= !empty($descripcion) ? "AND rcb.descripcion ILIKE '%$descripcion%' " : "";
		$q.= !empty($grupo) ? "AND cb.id = $grupo " : "";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		$total = $r->RecordCount();

		return $total;
	}
}
?>
