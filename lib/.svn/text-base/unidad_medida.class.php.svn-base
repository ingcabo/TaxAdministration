<?
class unidad_medida{

	// Propiedades

	var $id;
	var $descripcion;
	var $abreviacion;

	function get($conn, $id){
		$q = "SELECT * FROM puser.unidades_medida ";
		$q.= "WHERE id='$id'";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->descripcion = $r->fields['descripcion'];
			$this->abreviacion = $r->fields['abreviacion'];
														
			return true;
		}else
			return false;
	}

	function get_all($conn, $from=0, $max=0,$orden="id"){
		$q = "SELECT * FROM puser.unidades_medida ";
		$q.= "ORDER BY $orden ";
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new tipo_producto;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $descripcion, $abreviacion){
		$q = "INSERT INTO puser.unidades_medida ";
		$q.= "(descripcion, abreviacion) ";
		$q.= " VALUES ";
		$q.= "('$descripcion', '$abreviacion') ";
		//die($q);
		/*echo $q;*/
		if($conn->Execute($q)){
			$this->msj = REG_ADD_OK;
			return true;
		} else {
			$this->msj = ERROR_ADD;
			return false;
			}
	}

	function set($conn, $id, $descripcion, $abreviacion){
		$q = "UPDATE puser.unidades_medida SET descripcion = '$descripcion', abreviacion='$abreviacion' ";
		$q.= "WHERE id=$id";	
		//die($q);
		if($conn->Execute($q)){
			$this->msj = REG_SET_OK;
			return true;
		} else {
			$this->msj = ERROR_SET;
			return false;
			}
	}

	function del($conn, $id){
		$q = "DELETE FROM puser.unidades_medida WHERE id='$id'";
		if($conn->Execute($q)){
			$this->msj = REG_DEL_OK;
			return true;
		} else {
			$this->msj = ERROR_DEL;
			return false;
			}
	}
	
	function buscar($conn,$descripcion,$abreviacion, $max=0, $from=0, $orden="id"){
		//die("aqui ".$grupo_prov);
		try{
			if(empty($descripcion) and empty($abreviacion))
				return false;
			$q = "SELECT * FROM puser.unidades_medida ";
			$q.= "WHERE 1=1 ";
			$q.= !empty($descripcion) ? "AND descripcion ILIKE '%$descripcion%'  ":"";
			$q.= !empty($abreviacion) ? "AND abreviacion ILIKE '%$abreviacion%'  ":"";
			$q.= "ORDER BY $orden ";
			//die($q);
			$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
			$collection=array();
			while(!$r->EOF){
				$ue = new unidad_medida;
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
	
	function total_registro_busqueda($conn,$descripcion,$abreviacion, $orden="id"){
		if(empty($descripcion) and empty($abreviacion))
				return false; 
		$q = "SELECT * FROM puser.unidades_medida ";
		$q.= "WHERE 1=1 ";
		$q.= !empty($descripcion) ? "AND descripcion ILIKE '%$descripcion%'  ":"";
		$q.= !empty($abreviacion) ? "AND abreviacion ILIKE '%$abreviacion%'  ":"";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		$total = $r->RecordCount();

		return $total;
	}
	
}
?>
