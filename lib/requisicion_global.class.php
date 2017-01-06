<?
class requisicion_global{

	// Propiedades

	var $id;
	var $ano;
	var $fecha_r;
	var $motivo;
	var $status;
	var $nom_status;
	var $relacionGLOB;
	var $nroreqgbl;

	var $total;

	function get($conn, $id){
		$q = "SELECT * FROM puser.gbl_requisicion ";
		//$q.= "INNER JOIN puser.requisiciones ON (glb_requisicion.id = requisiciones.nroreqgbl) ";
		$q.= "WHERE id='$id'";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->ano = $r->fields['ano'];
			$this->fecha_r = $r->fields['fecha_r'];
			$this->motivo = $r->fields['motivo'];
			$this->status = $r->fields['status'];	
			$this->getRelacionAgrupados($conn, $id);
			switch ($r->fields['status']){
			case '01':
				$this->nom_status = 'Pendiente';
				break;
			case '02':
				$this->nom_status = 'Aprobada';
				break;
			case '03':
				$this->nom_status = 'Anulada';
				break;
			case '04':
				$this->nom_status = 'Recibida por Compras';
				break;
			case '05':
				$this->nom_status = 'Requisicion General';
				break;
			case '06':
				$this->nom_status = 'Solicitud de Cotizacion';
				break;
			case '07':
				$this->nom_status = 'Cotizada';
				break;
			case '08':
				$this->nom_status = 'Orden de Compra';
				break;
			
			}
			$this->nroreqgbl = $r->fields['id'];
			//die('entro');
														
			return true;
		}else
			return false;
	}

	function get_all($conn, $from=0, $max=0,$orden="id"){
		$q = "SELECT * FROM puser.gbl_requisicion ";
		$q.= "ORDER BY $orden ";
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new requisicion_global;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $ano, $fecha, $motivo, $id_usuario, $status, $gbl_requisicion){
		
		//die(var_dump($gbl_requisicion));
		$sql = "SELECT trim(to_char(substring(id from 1 for 4)::int+1,'0000')||'-'||'$ano')::varchar AS id_requisicion FROM puser.gbl_requisicion UNION(SELECT '0001' || '-$ano') ORDER BY id_requisicion desc LIMIT 1";
		//die($sql);
		$row = $conn->Execute($sql);
		if($row){
			$id = $row->fields['id_requisicion'];
		} else{
			$this->msj = "Error al generar codigo de requisicion global ";
			return false;
		}		
		$q = "INSERT INTO puser.gbl_requisicion ";
		$q.= "(id, ano, fecha_r, motivo, status, id_usuario) ";
		$q.= " VALUES ";
		$q.= "( '$id', '$ano', '$fecha', '".trim($motivo)."', '$status', '$id_usuario' ) ";
		//die($q);
		//echo $q."<br>";
		$r = $conn->Execute($q);
		//$r = true;
		if($r){
		
				if(
				$this->addRelacionRequisiciones($conn, 
												$id,
												$gbl_requisicion)){
					revision_requisicion::set_status_requisicion($conn,'05',$id);
					$this->msj = REG_ADD_OK;
					return true;
				}else{
					$this->msj = ERROR_ADD;
					return false;
				}	
		}else{
			$this->msj = ERROR_ADD;
			return false;
		}
	}

	function set($conn, $id, $fecha, $motivo, $status, $id_usuario, $gbl_requisicion){
		$q = "UPDATE puser.gbl_requisicion SET fecha_r = '$fecha', motivo='$motivo', status='$status', id_usuario=$id_usuario ";
		$q.= "WHERE id=$id";	
		//die($q);
		if($conn->Execute($q)){
			if(
				$this->addRelacionRequisiciones($conn, 
												$id,
												$gbl_requisicion)){
					revision_requisicion::set_status_requisicion($conn,'05',$id);
					$this->msj = REG_ADD_OK;
					return true;
				}else{
					$this->msj = ERROR_ADD;
					return false;
				}	
		}else{
			$this->msj = ERROR_ADD;
			return false;
		}
	}
	
	function addRelacionRequisiciones($conn, 												
										 $nrodoc,
										 $c_requis){
		//die($c_obras);
		$JsonRec = new Services_JSON();
		$JsonRec=$JsonRec->decode(str_replace("\\","",$c_requis));
		$aux = '';
		//die(var_dump())
		$max = count($JsonRec->requisicion);
		if(is_array($JsonRec->requisicion)){
			$i = 1;
			foreach ($JsonRec->requisicion as $oRE_Aux){
				if($i!=$max)
					$aux.=  "'".$oRE_Aux[0]."',";
				else
					$aux.= "'".$oRE_Aux[0]."'";
					
				$sql = "UPDATE puser.requisiciones SET nroreqgbl = '$nrodoc' WHERE id = '$oRE_Aux[0]'";
				$row = $conn->Execute($sql);
				$i++;
			}
		}else {
			return false;
		}	
				$sql = "SELECT id_producto, productos.descripcion, SUM(cantidad) AS articulos FROM puser.relacion_requisiciones ";
				$sql.= "INNER JOIN puser.productos ON (relacion_requisiciones.id_producto = productos.id) ";
				$sql.= "WHERE id_requisicion IN ($aux) ";
				$sql.= "GROUP BY id_producto, descripcion, relacion_requisiciones.id "; 
				$sql.= "ORDER BY relacion_requisiciones.id";
				//die($sql);
				$row = $conn->Execute($sql);
				
				while(!$row->EOF){
					$q = "INSERT INTO puser.relacion_gbl_requisicion ";
					$q.= "( id_gbl_requisicion, id_producto, cantidad) ";
					$q.= "VALUES ";
					$q.= "('$nrodoc', '".$row->fields['id_producto']."', '".trim($row->fields['articulos'])."') ";
					
					//die($q);
					$r = $conn->Execute($q);
					$row->movenext();
				}
				if($r){
						return true;
					} else {
						return false;
					}
					
	}
	
	function getRequisiciones($conn, $ano){
		$q = "SELECT DISTINCT requisiciones.id, unidades_ejecutoras.id AS unidad_ejecutora, motivo FROM puser.requisiciones INNER JOIN puser.unidades_ejecutoras ON (puser.requisiciones.id_unidad_ejecutora = puser.unidades_ejecutoras.id) WHERE status = '04' AND nroreqgbl is null";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF)
			$coleccion = array();
		while(!$r->EOF){
			$ue = new requisicion_global;
			$ue->id = $r->fields['id'];
			$ue->unidad_ejecutora = $r->fields['unidad_ejecutora'];
			$ue->motivo = $r->fields['motivo'];
			$coleccion[] = $ue;
			$r->movenext();	
		}
		return $coleccion;
	}
	
		function buscar($conn,$id_rg, $fecha_desde, $fecha_hasta, $motivo, $max=0, $from=0, $orden="id"){
		//die("aqui ".$grupo_prov);
		try{
			if(empty($id_rg) and empty($fecha_desde) and empty($fecha_hasta) and empty($motivo))
				return false;
			$q = "SELECT * FROM puser.gbl_requisicion ";
			$q.= "WHERE 1=1 ";
			$q.= !empty($id_rg) ? "AND id = '$id_rg'  ":"";
			$q.= !empty($fecha_desde) ? "AND fecha_r >= '$fecha_desde'  ":"";
			$q.= !empty($fecha_hasta) ? "AND fecha_r <= '$fecha_hasta'  ":"";
			$q.= !empty($motivo) ? "AND motivo ILIKE '%$motivo%'  ":"";
			$q.= "ORDER BY $orden ";
			//die($q);
			$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
			$collection=array();
			while(!$r->EOF){
				$ue = new requisicion_global;
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
	
	function total_registro_busqueda($conn,$id_rg, $fecha_desde, $fecha_hasta, $motivo, $orden="id"){
		if(empty($id_rg) and empty($fecha_desde) and empty($fecha_hasta) and empty($motivo))
				return false;
		$q = "SELECT * FROM puser.gbl_requisicion ";
			$q.= "WHERE 1=1 ";
			$q.= !empty($id_rg) ? "AND id = '$id_rg'  ":"";
			$q.= !empty($fecha_desde) ? "AND fecha_r >= '".guardafecha($fecha_desde)."'  ":"";
			$q.= !empty($fecha_hasta) ? "AND fecha_r <= '".guardafecha($fecha_hasta)."'  ":"";
			$q.= !empty($motivo) ? "AND motivo ILIKE '%$motivo%'  ":"";
			$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		$total = $r->RecordCount();

		return $total;
	}
	
	//OBTIENE LAS REQUISICIONES AGRUPADAS EN UNA REQUISICION GENERAL
	function getRelacionAgrupados($conn, $id_rg){
		$q = "SELECT id, id_unidad_ejecutora, motivo FROM puser.requisiciones WHERE nroreqgbl = '$id_rg'";
		//die($q);
		$r = $conn->Execute($q);
		while(!$r->EOF){
			$rg = new requisicion_global;
			$rg->id_req = $r->fields['id'];
			$rg->id_ue = $r->fields['id_unidad_ejecutora'];
			$rg->motivo = $r->fields['motivo'];
			$coleccion[] = $rg;
			$r->movenext();
		}
		$this->relacionGLOB = new Services_JSON();
		$this->relacionGLOB = is_array($coleccion) ? $this->relacionGLOB->encode($coleccion) : false;
		return $coleccion;
		
	}
	
	function del($conn,$id){
		$q = "DELETE FROM puser.gbl_requisicion WHERE id = '$id' ";
		$r = $conn->Execute($q);
		$sql = "UPDATE puser.requisiciones SET nroreqgbl = null, status = '04' WHERE nroreqgbl = '$id'";
		$row = $conn->Execute($sql);
		if($r){
			$this->msj = REG_DEL_OK;
			return true;
		} else {
			$this->msj = ERROR_DEL;
			return true;
		}

			
	}


}
?>
