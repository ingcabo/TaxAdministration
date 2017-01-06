<?
class familiaProductos{

	// Propiedades

	var $id;
	var $descripcion;
	var $codigo;
	var $id_tipo_producto_clasif;
	var $msj;
	var $total;

	function get($conn, $id){
		$q = "SELECT * FROM puser.tipo_familia ";
		$q.= "WHERE id='$id'";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->descripcion = $r->fields['descripcion'];
			$this->id_tipo_producto_clasif = $r->fields['id_familia'];
			$this->codigo = $r->fields['codigo'];		
			return true;
		}else
			return false;
	}

	function get_all($conn, $from=0, $max=0,$orden="id"){
		$q = "SELECT * FROM tipo_familia ";
		$q.= "ORDER BY $orden ";
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new familiaProductos;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $tipo_producto_clasif,$descripcion, $codigo){
		try{
			$q = "INSERT INTO puser.tipo_familia ";
			$q.= "(descripcion, id_familia,codigo) ";
			$q.= " VALUES ";
			$q.= "('$descripcion', '$tipo_producto_clasif', '$codigo') ";
			//die($q);
			/*echo $q;*/
			$conn->Execute($q);
			$this->msj = REG_ADD_OK;
			return $this->msj;
			
		} catch(ADODB_Exception $e){
			echo $e->getCode();
			if($e->getCode()==-5){
				
				$this->msj = ERROR_CATCH_VUK;	
				return $this->msj;
			}else{
				$this->msj = ERROR_CATCH_GENERICO;	
				return $this->msj;
			}
		}
	}

	function set($conn, $id, $tipo_producto_clasif,$descripcion, $codigo){
		try{
			$q = "UPDATE tipo_familia SET descripcion = '$descripcion', codigo='$observacion', id_familia=$tipo_producto_clasif";
			$q.= "WHERE id=$id";	
			//die($q);
			$conn->Execute($q);
			return REG_SET_OK;
		}catch(ADODB_Exception $e){
			if($e->getCode()==-5){
				return  ERROR_CATCH_VUK;	
			}else{
				return ERROR_CATCH_GENERICO;
			}	
		}
	}

	function del($conn, $id){
		$q = "DELETE FROM tipo_familia WHERE id='$id'";
		if($conn->Execute($q)){
			$this->msj = REG_DEL_OK;
			return true;
		} else {
			$this->msj = ERROR_DEL;
			return false;
			}
	}
	
	function buscar($conn,$id_tipo_prod_clasif, $descripcion, $max=0, $from=0, $orden="id"){
		//die("aqui ".$grupo_prov);
		try{
			if(empty($id_tipo_prod_clasif) and empty($descripcion))
				return false;
			$q = "SELECT * FROM puser.tipo_familia ";
			$q.= "WHERE 1=1 ";
			$q.= !empty($id_tipo_prod_clasif) ? "AND id_familia = '$id_tipo_prod_clasif'  ":"";
			$q.= !empty($descripcion) ? "AND descripcion ILIKE '%$descripcion%'  ":"";
			$q.= "ORDER BY $orden ";
			//die($q);
			$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
			$collection=array();
			while(!$r->EOF){
				$ue = new familiaProductos;
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
	
	function total_registro_busqueda($conn,$id_tipo_prod_clasif, $descripcion, $orden="id"){
		if(empty($id_pp) and empty($descripcion))
				return false;
		$q = "SELECT * FROM puser.tipo_familia ";
		$q.= "WHERE 1=1 ";
		$q.= !empty($id_tipo_prod_clasif) ? "AND id_familia = '$id_tipo_prod_clasif'  ":"";
		$q.= !empty($descripcion) ? "AND descripcion ILIKE '%$descripcion%'  ":"";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		$total = $r->RecordCount();

		return $total;
	}
	
}
?>
