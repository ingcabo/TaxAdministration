<?
class relacion_ue_cp{

	// Propiedades

	var $id;
	var $id_escenario;
	var $escenario;
	var $id_unidad_ejecutora;
	var $unidad_ejecutora;
	var $id_categoria_programatica;
	var $categoria_programatica;
	var $descripcion;

	var $total;

	function get($conn, $id, $escEnEje=''){
		try{
			$q = "SELECT relacion_ue_cp.*, escenarios.descripcion AS escenario, ";
			$q.= "categorias_programaticas.descripcion AS categoria_programatica, ";
			$q.= "unidades_ejecutoras.descripcion AS unidad_ejecutora ";
			$q.= "FROM relacion_ue_cp ";
			$q.= "INNER JOIN unidades_ejecutoras ON (relacion_ue_cp.id_unidad_ejecutora = unidades_ejecutoras.id) ";
			$q.= "INNER JOIN escenarios ON (relacion_ue_cp.id_escenario = escenarios.id) ";
			$q.= "INNER JOIN categorias_programaticas ON (relacion_ue_cp.id_categoria_programatica = categorias_programaticas.id) ";
			$q.= "WHERE relacion_ue_cp.id=$id ";//AND unidades_ejecutoras.id_escenario=$escEnEje ";
			//$q.= "AND categorias_programaticas.id_escenario=$escEnEje";
			$r = $conn->Execute($q);
			$this->id =$id;// $r->fields['id'];
			$this->id_escenario = $r->fields['id_escenario'];
			$this->escenario = $r->fields['escenario'];
			$this->id_unidad_ejecutora = $r->fields['id_unidad_ejecutora'];
			$this->unidad_ejecutora = $r->fields['unidad_ejecutora'];
			$this->id_categoria_programatica = $r->fields['id_categoria_programatica'];
			$this->categoria_programatica = $r->fields['categoria_programatica'];
			$this->descripcion = $r->fields['descripcion'];
			return true;
		}
		catch( ADODB_Exception $e ){
			if($e->getCode()==-1)
				return ERROR_CATCH_VFK;
			elseif($e->getCode()==-5)
				return ERROR_CATCH_RELVUK;
			else
				return ERROR_CATCH_GENERICO;
		}
	}

	function get_all($conn, $escenario, $from=0, $max=0,$orden="id_escenario, id_categoria_programatica, id_unidad_ejecutora"){
		try{
			$q = "SELECT * FROM relacion_ue_cp ";
			$q.= "ORDER BY $orden ";
			$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
			$collection=array();
			while(!$r->EOF){
				$ue = new relacion_ue_cp;
				$ue->get($conn, $r->fields['id'], $escenario);
				$coleccion[] = $ue;
				$r->movenext();
			}
			$this->total = $r->RecordCount();
			return $coleccion;
		}
		catch( ADODB_Exception $e ){
			if($e->getCode()==-1)
				return ERROR_CATCH_VFK;
			elseif($e->getCode()==-5)
				return ERROR_CATCH_RELVUK;
			else
				return ERROR_CATCH_GENERICO;
		}
	}

	function add($conn, $id_escenario, $id_categoria_programatica, $id_unidad_ejecutora, $descripcion){
		try {
			$q = "INSERT INTO relacion_ue_cp ";
			$q.= "(id_escenario, id_categoria_programatica, id_unidad_ejecutora, descripcion) ";
			$q.= "VALUES ('$id_escenario', '$id_categoria_programatica', '$id_unidad_ejecutora', '$descripcion') ";
			//die($q);
			$conn->Execute($q);
			return REG_ADD_OK;
		}
		catch( ADODB_Exception $e ){
			if($e->getCode()==-1)
				return ERROR_CATCH_VFK;
			elseif($e->getCode()==-5)
				return ERROR_CATCH_RELVUK;
			else
				return ERROR_CATCH_GENERICO;
		}
	}

	function set($conn, $id, $id_escenario, $id_categoria_programatica, $id_unidad_ejecutora, $descripcion){
		try {
			$q = "UPDATE relacion_ue_cp SET id_escenario='$id_escenario', descripcion = '$descripcion', ";
			$q.= "id_categoria_programatica = '$id_categoria_programatica', id_unidad_ejecutora = '$id_unidad_ejecutora' ";
			$q.= "WHERE id='$id' ";
			//die($q);
			$conn->Execute($q);
			return REG_SET_OK;
		}
		catch( ADODB_Exception $e ){
			if($e->getCode()==-1)
				return ERROR_CATCH_VFK;
			elseif($e->getCode()==-5)
				return ERROR_CATCH_RELVUK;
			else
				return ERROR_CATCH_GENERICO;
		}
	}
	function del($conn, $id){
		try {
			$q = "DELETE FROM relacion_ue_cp WHERE id='$id'";
			$conn->Execute($q);
			return REG_DEL_OK;
		}
		catch( ADODB_Exception $e ){
			if($e->getCode()==-1)
				return ERROR_CATCH_VFK;
			elseif($e->getCode()==-5)
				return ERROR_CATCH_RELVUK;
			else
				return ERROR_CATCH_GENERICO;
		}
	}
	
	function del_escenario($conn, $id){
		try{
			$q = "DELETE FROM relacion_ue_cp WHERE id_escenario='$id'";
			$conn->Execute($q);
		}
		catch( ADODB_Exception $e ){
			if($e->getCode()==-1)
				return ERROR_CATCH_VFK;
			elseif($e->getCode()==-5)
				return ERROR_CATCH_RELVUK;
			else
				return ERROR_CATCH_GENERICO;
		}
	}
	
	function get_all_by_esc($conn, $id_escenario, $from=0, $max=0,$orden="id"){
		try{
			$q = "SELECT * FROM relacion_ue_cp WHERE id_escenario = '$id_escenario' ";
			$q.= "ORDER BY $orden ";
			$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
			$collection=array();
			while(!$r->EOF){
				$ue = new relacion_ue_cp;
				$ue->get($conn, $r->fields['id'], $id_escenario);
				$coleccion[] = $ue;
				$r->movenext();
			}
			$this->total = $r->RecordCount();
			return $coleccion;
		}
		catch( ADODB_Exception $e ){
			if($e->getCode()==-1)
				return ERROR_CATCH_VFK;
			elseif($e->getCode()==-5)
				return ERROR_CATCH_RELVUK;
			else
				return ERROR_CATCH_GENERICO;
		}
	}

	function buscar($conn, 
	                $id_escenario, 
						 $id_unidad_ejecutora,
             $cod_categoria_programatica, 
						 $id_categoria_programatica, 
						 $descripcion, 
						 $escEnEje,
						 $max=10, 
						 $from=1,
						 $orden="id_escenario, id_unidad_ejecutora, id_categoria_programatica"){
		try{
			if(empty($id_escenario) && empty($descripcion) && empty($id_unidad_ejecutora) && empty($cod_categoria_programatica) && empty($id_categoria_programatica))
				return false;
			$q = "SELECT * FROM relacion_ue_cp ";
			$q.= "WHERE  1=1 ";
			$q.= !empty($id_escenario) ? "AND id_escenario = '$id_escenario'  ":"";
			$q.= !empty($id_unidad_ejecutora) ? "AND id_unidad_ejecutora = '$id_unidad_ejecutora'  ":"";
			$q.= !empty($cod_categoria_programatica) ? "AND id_categoria_programatica = '$cod_categoria_programatica'  ":"";
			$q.= !empty($id_categoria_programatica) ? "AND id_categoria_programatica = '$id_categoria_programatica'  ":"";
			$q.= !empty($descripcion) ? "AND descripcion ILIKE '%$descripcion%'  ":"";
			$q.= "ORDER BY $orden ";
			//die($q);
			$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
			$collection=array();
			while(!$r->EOF){
				$ue = new relacion_ue_cp;
				$ue->get($conn, $r->fields['id'], $id_escenario);
				$coleccion[] = $ue;
				$r->movenext();
			}
			return $coleccion;
		}
		catch( ADODB_Exception $e ){
			if($e->getCode()==-1)
				return ERROR_CATCH_VFK;
			elseif($e->getCode()==-5)
				return ERROR_CATCH_RELVUK;
			else
				return ERROR_CATCH_GENERICO;
		}
	}
	
	function total_registro_busqueda($conn, $id_escenario, $id_unidad_ejecutora, $cod_categoria, $id_categoria_programatica, $descripcion/*, $orden="id_escenario, id"*/){
		if(empty($id_escenario) && empty($id_unidad_ejecutora) && empty($id_partida_presupuestaria) && empty($cod_categoria) && empty($id_categoria_programatica))
			return false;
		$q = "SELECT * FROM relacion_ue_cp ";
		$q.= "WHERE  1=1 ";
			$q.= !empty($id_escenario) ? "AND id_escenario = '$id_escenario'  ":"";
			$q.= !empty($id_unidad_ejecutora) ? "AND id_unidad_ejecutora = '$id_unidad_ejecutora'  ":"";
			$q.= !empty($cod_categoria) ? "AND id_categoria_programatica = '$cod_categoria'  ":"";
			$q.= !empty($id_categoria_programatica) ? "AND id_categoria_programatica = '$id_categoria_programatica'  ":"";
			$q.= !empty($descripcion) ? "AND descripcion ILIKE '%$descripcion%'  ":"";
		//die($q);
		$r = $conn->Execute($q);
		$total = $r->RecordCount();

		return $total;
	}
	
	function get_First_by_UE($conn,$ue,$escEnEje){
		$q = "SELECT ruc.id_categoria_programatica AS id_categoria, cp.descripcion AS descripcion FROM puser.relacion_ue_cp ruc ";
		$q.= "INNER JOIN puser.categorias_programaticas cp ON(ruc.id_categoria_programatica = cp.id AND ruc.id_escenario = cp.id_escenario) ";
		$q.= "WHERE ruc.id_unidad_ejecutora = '$ue' AND ruc.id_escenario = '$escEnEje' ";
		$q.= "LIMIT 1";
		//die($q);
		$r = $conn->Execute($q);
		if($r){
			$ruc = new relacion_ue_cp;
			$ruc->id_categoria = $r->fields['id_categoria'];
			$ruc->descripcion = $r->fields['descripcion'];
			$coleccion[] = $ruc;
		}
		return $coleccion;
		
	}
}
?>
