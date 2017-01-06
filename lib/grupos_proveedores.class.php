<?
class grupos_proveedores{

	// Propiedades

	var $id;
	var $id_organismo;
	var $nombre;
	var $descripcion;
	var $fecha;

	// total de registros de la tabla grupos_proveedores
	var $total;
	
	// coleccion de objetos de requisitos de un grupo de proveedores
	var $relacionREQPRO;
	var $requisitos;

	function get($conn, $id){
		$q = "SELECT * FROM grupos_proveedores ";
		$q.= "WHERE id='$id'";
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->id_organismo = $r->fields['id_organismo'];
			$this->nombre = $r->fields['nombre'];
			$this->descripcion = $r->fields['descripcion'];
			$this->fecha = $r->fields['fecha'];
			$this->get_requisitos($conn, $r->fields['id']);
			return true;
		}else
			return false;
	}

	function get_all($conn, $from=0, $max=0,$orden="id"){
		$q = "SELECT * FROM grupos_proveedores ";
		$q.= "ORDER BY $orden ";
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new grupos_proveedores;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $nombre, $descripcion, $fecha, $aRequisitos){
		$q = "INSERT INTO puser.grupos_proveedores ";
		$q.= "(nombre, descripcion, fecha) ";
		$q.= "VALUES ";
		$q.= "('$nombre', '$descripcion', '$fecha') ";
		//die($q);
		$r= $conn->Execute($q);
		if ($r){
			$nrogprov = getLastId($conn, 'id', 'grupos_proveedores'); 
			//die("last ".$nrogprov);
			if(
				$this->addRelacionReqGProv($conn, 
												$nrogprov,
												$aRequisitos)
																	){
					return true;
				}
		else
			return false;
		}
	}

	function set($conn, $id, $nombre, $descripcion, $fecha, $aRequisitos){
		$q = "UPDATE grupos_proveedores SET ";
		$q.= "nombre = '$nombre', descripcion='$descripcion', fecha = '$fecha' ";
		$q.= "WHERE id='$id' ";
		//die($q);
		$r= $conn->Execute($q);
		if ($r){
			$nrodoc = getLastId($conn, 'id', 'contrato_obras');
			$this->DelRelacionReqGProv($conn, $id);
				$this->addRelacionReqGProv($conn, 
												$id,
												$aRequisitos); 
			return true;
		} else {
			return false;
			}
	}

	function del($conn, $id)
	{
		try
		{
			$q = "DELETE FROM grupos_proveedores WHERE id='$id'";
			$r = $conn->Execute($q);
			if($r !== false)
				return true;
			else
				return false;
		}
		catch( ADODB_Exception $e )
		{
			if($e->getCode()==-1)
				return ERROR_CATCH_VFK;
			elseif($e->getCode()==-5)
				return ERROR_CATCH_VUK;
			else
				return ERROR_CATCH_GENERICO;
		}
	}
	
	function get_requisitos($conn, $id){
		$q = "SELECT puser.relacion_req_gp.*, puser.requisitos.nombre, puser.requisitos.fecha ";
		$q.= "FROM puser.relacion_req_gp ";
		$q.= "INNER JOIN puser.requisitos ON (relacion_req_gp.id_requisito = requisitos.id) ";
		$q.= "INNER JOIN puser.grupos_proveedores ON (relacion_req_gp.id_grupo_proveedor = grupos_proveedores.id) ";
		$q.= "WHERE relacion_req_gp.id_grupo_proveedor='$id' ";
		//die($q);
		$r = $conn->Execute($q);
		while(!$r->EOF){
			$ue = new grupos_proveedores;
			$ue->id = $r->fields['id_requisito'];
			$ue->nombre	= $r->fields['nombre'];
			$ue->fecha = muestrafecha($r->fields['fecha']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->relacionREQPRO = new Services_JSON();
		$this->relacionREQPRO = is_array($coleccion) ? $this->relacionREQPRO->encode($coleccion) : false;
		return $coleccion;
	}
	
	function addRelacionReqGProv($conn, 												
										 $nrogprov,
										 $c_requisitos){
		//die($c_requisitos);
		$JsonRec = new Services_JSON();
		$JsonRec=$JsonRec->decode(str_replace("\\","",$c_requisitos));
		if(is_array($JsonRec->requisito)){
			foreach ($JsonRec->requisito as $oRGP_Aux){
				$q = "INSERT INTO puser.relacion_req_gp ";
				$q.= "( id_grupo_proveedor, id_requisito) ";
				$q.= "VALUES ";
				$q.= "('$nrogprov','$oRGP_Aux[0]') ";
				//echo($q."<br>");
				//die($q);
				$r = $conn->Execute($q);
			} 
		if($r){
			return true;
		} else {
			return false;
		
				}
		}
	}
	
	function delRelacionReqGProv($conn, $nrogprov){
		$q = "DELETE FROM puser.relacion_req_gp WHERE id_grupo_proveedor ='$nrogprov'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	function buscar($conn, $nombre="", $max=10, $from=1, $orden="id"){
		try{
			$q = "SELECT id FROM puser.grupos_proveedores ";
			if (!empty($nombre))
				$q.= "WHERE nombre ILIKE '%$nombre%' ";
			$q.= "ORDER BY $orden ";
			//die($q);
			$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
			$collection=array();
			while(!$r->EOF){
				$ue = new grupos_proveedores;
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
		
		$q = "SELECT id FROM puser.grupos_proveedores ";
			if (!empty($nombre))
				$q.= "WHERE nombre ILIKE '%$nombre%' ";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = $conn->Execute($q);
		$total = $r->RecordCount();

		return $total;
	}
}
?>
