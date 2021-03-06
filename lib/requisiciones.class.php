<?
class requisiciones{

	// Propiedades

	var $id;
	var $id_unidad_ejecutora;
	var $id_usuario;
	var $motivo;
	var $unidad_ejecutora;
	var $ano;
	var $fecha;
	var $fecha_aprobacion;
	var $status;
	var $total;
	var $nom_status;
	var $nroreqgbl;
	var $msj;
	
	/*****************************
       Objeto Relacion Partidas
	*****************************/
	var $relacion; // almacena un array de objetos de relaciones de partidas
	
	// Propiedades utilizadas por el objeto con relaciones de partidas
	var $idParCat;
	var $id_categoria_programatica;
	var $id_partida_presupuestaria;
	var $categoria_programatica;
	var $partida_presupuestaria;
	var $cantidad;
	var $nombre_p;
	var $id_producto;
	var $costo;
	var $RelacionPARCAT;
	

	function get($conn, $id, $escEnEje=''){
		$q = "SELECT puser.requisiciones.*, puser.unidades_ejecutoras.descripcion AS unidad_ejecutora ";
		$q.= "FROM puser.requisiciones ";
		$q.= "INNER JOIN puser.unidades_ejecutoras ON (requisiciones.id_unidad_ejecutora = unidades_ejecutoras.id) ";
		$q.= "WHERE requisiciones.id='$id'  ";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->id_unidad_ejecutora = $r->fields['id_unidad_ejecutora'];
			$this->id_usuario = $r->fields['id_usuario'];
			$this->motivo = $r->fields['motivo'];
			$this->unidad_ejecutora = $r->fields['unidad_ejecutora'];
			$this->fecha = $r->fields['fecha_r'];
			$this->fecha_aprobacion = $r->fields['fecha_aprobacion'];
			$this->getRelacionPartidas($conn, $id, $escEnEje);
			$this->status= $r->fields['status'];
			switch ($r->fields['status']){
			case '01':
				$this->nom_status = 'Registrada';
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
			$this->ano = $r->fields['ano'];
			$this->nroreqgbl = $r->fields['nroreqgbl'];
			return true;
		}else
			return false;
	}

	function get_all($conn, $escEnEje,$orden="id"){
		$q = "SELECT * FROM puser.requisiciones ";
		$q.= "ORDER BY $orden ";
		$r = $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new requisiciones;
			$ue->get($conn, $r->fields['id'], $escEnEje);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, 
						$id_unidad_ejecutora,
						$ano,
						$fecha_r,
						$motivo,
						$status,
						$id_usuario,
						$requisicion){
		$q = "INSERT INTO puser.requisiciones ";
		$q.= "( ";
		$q.= "id, ";
		$q.= "id_unidad_ejecutora, ";
		$q.= "ano, ";
		$q.= "fecha_r, ";
		$q.= "motivo, ";
		$q.= "status, ";
		$q.= "id_usuario ";
		$q.= ") ";
		$q.= "VALUES ";
		$q.= "( ";
		$q.= "(SELECT id_unidad_ejecutora || '-' || trim(to_char(substring(id from 6)::int+1,'0000')) AS prueba FROM puser.requisiciones WHERE id_unidad_ejecutora='$id_unidad_ejecutora' UNION(SELECT '$id_unidad_ejecutora' || '-0001') ORDER BY prueba desc LIMIT 1),";
		$q.= " '$id_unidad_ejecutora', ";
		$q.= " '$ano', ";
		$q.= " '$fecha_r', ";
		$q.= " '".trim($motivo)."', ";
		$q.= " '$status', ";
		$q.= " '$id_usuario' ";
		$q.= ") ";
		//die($q);
		$r = $conn->execute($q);
		//$r = true;
		if($r){
				//$nrodoc = getLastId($conn, 'id', 'requisicion'); 
				$sql= "SELECT id_unidad_ejecutora || '-' || trim(to_char(substring(id from 6)::int,'0000')) AS prueba FROM puser.requisiciones WHERE id_unidad_ejecutora='$id_unidad_ejecutora' ORDER BY prueba desc LIMIT 1";
				//die($sql);
				$r= $conn->Execute($sql);
				$nrodoc= $r->fields['prueba'];
				if(
				$this->addRelacionPartidas($conn, 
												$nrodoc,
												$requisicion)
																	){
					$this->msj = REG_ADD_OK;
					return true;
				}
		}else{
			$this->msj = ERROR_ADD;
			return false;
		}
	}

	function set($conn, 
						$id, 
						$id_unidad_ejecutora,
						$ano,
						$id_usuario,
						$motivo,
						$fecha,
						$aRequisiciones
						){
		$q = "UPDATE puser.requisiciones SET  ";
		$q.= "id_unidad_ejecutora = '$id_unidad_ejecutora', ";
		$q.= "ano = $ano, ";
		$q.= "id_usuario = '$id_usuario', ";
		$q.= "motivo = '".trim($motivo)."', ";			
		$q.= "fecha_r = '$fecha' ";
		$q.= "WHERE id='$id' ";	
		//die($q);
		if($conn->Execute($q)){
			if($this->delRelacionPartidas($conn, $id)){
				if($this->addRelacionPartidas($conn, 
													$id,
													$aRequisiciones)){
					$this->msj = REG_SET_OK;
					return true;
				}else{
					$this->msj = ERROR_SET;
					return false;
				}
			}else{
				$this->msj = ERROR_SET;
				return false;
			}
		}else{
			$this->msj = ERROR_SET;
			return false;
		}
	}

	function del1($conn, $id){
		$q = "DELETE FROM puser.requisiciones WHERE id='$id'";
		if($conn->Execute($q)){
			$this->msj = REG_DEL_OK;
			return true;
		}else{
			$this->msj = ERROR_DEL;
			return false;
		}
	}
	
	function addRelacionPartidas($conn, 												
										 $nrodoc,
										 $c_requis){
		//die($c_obras);
		$JsonRec = new Services_JSON();
		$JsonRec=$JsonRec->decode(str_replace("\\","",$c_requis));
		if(is_array($JsonRec->requisicion)){
			foreach ($JsonRec->requisicion as $oRE_Aux){
			
				$q = "INSERT INTO puser.relacion_requisiciones ";
				$q.= "( id_requisicion, id_categoria, id_partida, id_producto, cantidad) ";
				$q.= "VALUES ";
				$q.= "('$nrodoc', '$oRE_Aux[1]', '$oRE_Aux[2]', '$oRE_Aux[0]', $oRE_Aux[3]) ";
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

	function delRelacionPartidas($conn, $nrodoc){
		$q = "DELETE FROM puser.relacion_requisiciones WHERE id_requisicion='$nrodoc'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	function getRelacionPartidas($conn, $id, $escEnEjec){
		$q = "SELECT puser.relacion_requisiciones.*, puser.partidas_presupuestarias.descripcion AS partida_presupuestaria,  ";
		$q.= "puser.categorias_programaticas.descripcion AS categoria_programatica, ";
		$q.= "puser.productos.descripcion AS nombre_p, puser.productos.ultimo_costo AS costo ";
		$q.= "FROM puser.relacion_requisiciones  ";
		$q.= "INNER JOIN puser.partidas_presupuestarias ON (relacion_requisiciones.id_partida = partidas_presupuestarias.id) ";
		$q.= "INNER JOIN puser.categorias_programaticas ON (relacion_requisiciones.id_categoria = categorias_programaticas.id) ";
		$q.= "INNER JOIN puser.productos ON (relacion_requisiciones.id_producto = productos.id) ";
		$q.= "WHERE relacion_requisiciones.id_requisicion='$id' ";
		$q.= "AND categorias_programaticas.id_escenario = '$escEnEjec' ";
		$q.= "AND partidas_presupuestarias.id_escenario = '$escEnEjec' ";
		$q.= "ORDER BY puser.relacion_requisiciones.id ";
		//die($q);
		$r = $conn->Execute($q);
		while(!$r->EOF){
			$ue = new movimientos_presupuestarios;
			$ue->nrodoc = $r->fields['id_requisicion'];
			$ue->id_partida	= $r->fields['id_partida'];
			$ue->id_categoria = $r->fields['id_categoria'];
			$ue->partida_presupuestaria	= $r->fields['partida_presupuestaria'];
			$ue->categoria_programatica = $r->fields['categoria_programatica'];
			$ue->cantidad = $r->fields['cantidad'];
			$ue->costo = $r->fields['costo'];
			$ue->id_producto = $r->fields['id_producto'];
			$ue->nombre_p = $r->fields['nombre_p'];
			$coleccion[] = $ue;
			$r->movenext();
			
		}
		$this->relacionPARCAT = new Services_JSON();
		$this->relacionPARCAT = is_array($coleccion) ? $this->relacionPARCAT->encode($coleccion) : false;
		return $coleccion;
	}

	function getCategorias($conn, $escEnEjec){
		$q = "SELECT  ";
		$q.= "categorias_programaticas.* ";
		$q.= "FROM categorias_programaticas ";
		$q.= "WHERE categorias_programaticas.id_escenario = '$escEnEjec' ";
		$q.= "ORDER BY categorias_programaticas.descripcion ";
		//echo($q);
		$r = $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new contrato_obras;
			$ue->id_categoria_programatica = $r->fields['id_categoria_programatica'];
			$ue->categoria_programatica = $r->fields['descripcion'];
			$coleccion[] = $ue;
			$r->movenext();
		}
		return $coleccion;
	}
	
	#FUNCION PARA APROBAR LOS CONTRATOS DE SERVICIO#
	function aprobar($conn, $id,
										$id_unidad_ejecutora,
										$ano,
										$motivo,
										$id_usuario,
										$fechadoc,
										$fecha_aprovacion,
										$aRequisicion
										){

		
		
		#DECODIFICO EL JSON DE LAS REQUISICIONES#
		$JsonRec = new Services_JSON();
		$JsonRec = $JsonRec->decode(str_replace("\\","",$aRequisicion));
		$contador = sizeof($JsonRec->requisicion);
		
		#CHEQUEO LA DISPONIBILIDAD DE LAS PARTIDAS, EN EL CASO DE QUE EL MONTO NO ESTA DISPONIBLE NO SE APRUEBA LA REQUISICION#
	/*	for($i = 0; $i < $contador; $i++){
			$q = "SELECT relacion_pp_cp.disponible FROM relacion_pp_cp WHERE id_categoria_programatica = '".$JsonRec->requisicion[$i][1]."' AND id_partida_presupuestaria = '".$JsonRec->requisicion[$i][2]."'";
			//die($q);
			$r = $conn->Execute($q);
			if($r){
				if($r->fields['disponible'] < guardafloat($JsonRec->requisicion[$i][7])){
					$this->msj = ERROR_SOLICITUD_PAGO_APR_NO_DISP;
					return false;
				}
			}
		}*/
		
		#ESTE FOREACH SE HACE PARA PASAR LAS VARIABLES A LA FUNCION ADD_RELACION DE LA CLASE MOVIMIENTOS PRESUPUESTARIOS#
		foreach($JsonRec->requisicion as $requisicion){
		
			$aCategoriaProgramatica[] 	= $requisicion[1];
			$aPartidaPresupuestaria[] 	= $requisicion[2];
			$aMonto[] 					= $requisicion[7];
		}
		
	for($i=0;$i<$contador;$i++) {
		
		#REGISTRO LA REQUISICION EN EL PRE-COMPROMISO#	
		$q = "INSERT INTO puser.precompromiso_requisiciones ";
		$q.= "(id_usuario, id_unidad_ejecutora, ano, id_requisicion, ";
		$q.= "fechadoc, id_partida_presupuestaria, id_categoria_programatica, monto) ";
		$q.= "VALUES ";
		$q.= "('$id_usuario', '$id_unidad_ejecutora', '$ano', '$id', '$fechadoc',  "; 
		$q.= " '$aPartidaPresupuestaria[$i]', '$aCategoriaProgramatica[$i]', ".guardaFloat($aMonto[$i]).") ";
		$r = $conn->Execute($q) or die($q);
		
		$q= "UPDATE puser.relacion_pp_cp SET disponible = disponible - ".$aMonto[$i]." WHERE id_categoria_programatica = '$aCategoriaProgramatica[$i]' AND id_partida_presupuestaria = '$aPartidaPresupuestaria[$i]' AND id_escenario = $ano";
		$sql = $conn->Execute($q);
		
	}
		#VERIFICO SI EL CONTRATO SE REGISTRO EN MOVIMIENTOS PRESUPUESTARIOS#
		if($r){
			
			
			#ACTUALIZO LA LA FECHA DE APROBACION Y EL ESTATUS DE LA REQUISICION#
			$d = $this->setFechaAprobacion($conn, $id);
			
			#VERIFICO SI TODOS LOS PROCESOS ANTERIORES SE EJECUTARON PARA MOSTRAR EL MENSAJE#
			if($d){
				
				return true;
				
			}
		
		}else
			return false;
	}
	
	function anular($conn, $id, $id_usuario,
										$id_unidad_ejecutora,
										$ano,
										$motivo,
										$fechadoc,
										$status,
										$aRequisicion
										){
		
		$q3 ="UPDATE puser.requisiciones SET status='03' WHERE id='$id'";
		//die($q3);
		$r3 = $conn->Execute($q3) or die($q3);
		
		#ESTE IF ES EN EL CASO DE QUE LA ORDEN DE COMPRA YA ESTA APROBADA#			
		//
		if ($r3 && $status=='02'){
			
			#DECODIFICO EL JSON DE LAS REQUISICIONES#
			$JsonRec = new Services_JSON();
			$JsonRec = $JsonRec->decode(str_replace("\\","",$aRequisicion));
			$contador = sizeof($JsonRec->requisicion);
			
			#ESTE FOREACH SE HACE PARA PASAR LAS VARIABLES A LA FUNCION ADD_RELACION DE LA CLASE MOVIMIENTOS PRESUPUESTARIOS#
			foreach($JsonRec->requisicion as $requisicion){
		
				$aCategoriaProgramatica[] 	= $requisicion[1];
				$aPartidaPresupuestaria[] 	= $requisicion[2];
				$aMonto[] 					= $requisicion[7] * (-1);
			}
			
			for($i=0;$i<$contador;$i++) {
			
				$q= "UPDATE puser.relacion_pp_cp SET disponible = disponible - ".$aMonto[$i]." WHERE id_categoria_programatica = '$aCategoriaProgramatica[$i]' AND id_partida_presupuestaria = '$aPartidaPresupuestaria[$i]' AND id_escenario = $ano";
				$sql = $conn->Execute($q);
			}
			
			if($sql){
						$this->msj = RQ_ANULADA;
						return true;
	
			} else {
				$this->msj = ERROR;
				return false;
				}
		
		}elseif ($r3){
			$this->msj = RQ_ANULADA;		
			return true;
		}else{
			$this->msj = ERROR;			
			return false;
		}	
		
		
	}
	
	function setNrodoc($conn, $nrodoc, $id){
		$q = "UPDATE puser.contrato_obras SET nrodoc = '$nrodoc' WHERE id='$id'";
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	function setFechaAprobacion($conn, $id){
		$q = "UPDATE puser.requisiciones SET fecha_aprobacion = now(), status = '02' WHERE id='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	function buscar($conn, 
	$id_ue, 
	$fecha_desde, 
	$fecha_hasta, 
	$orden="id",
	$escEnEje='',
	$max=10, 
	$from=1,
	$status,
	$nrequi){
		if(empty($id_ue) && empty($fecha_desde) && empty($fecha_hasta) && empty($status) && empty($nrequi)
			)
			return false;
		$q = "SELECT * FROM puser.requisiciones ";
		$q.= "WHERE  1=1 ";
		$q.= !empty($fecha_desde) ? "AND fecha_r >='".guardafecha($fecha_desde)."' ": "";
		$q.= !empty($fecha_hasta) ? "AND fecha_r <='".guardafecha($fecha_hasta)."' ": "";
		$q.= !empty($id_ue) ? "AND id_unidad_ejecutora = '$id_ue'  ":"";
		$q.= !empty($status) ? "AND status = '$status'  ":"";
		$q.= !empty($nrequi) ? "AND id = '$nrequi'  ":"";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new requisiciones;
			$ue->get($conn, $r->fields['id'], $escEnEje);
			$coleccion[] = $ue;
			$r->movenext();
		}
		return $coleccion;
	}
	
	function get_prod_pp_cp($conn, $id_cp, $id_producto, $escEnEje, $cantidad){
	$q="SELECT puser.categorias_programaticas.descripcion AS categoria, ";
	$q.="puser.partidas_presupuestarias.descripcion AS partida, ";
	$q.="puser.tipo_producto.descripcion AS tipo, ";
	$q.="puser.productos.descripcion AS producto, ";
	$q.="puser.relacion_pp_cp.id AS idparcat, ";
	$q.="coalesce(puser.relacion_pp_cp.disponible,0) AS disponible, ";
	$q.="puser.productos.ultimo_costo*".$cantidad." AS total, ";
	//$q.="(puser.productos.ultimo_costo*".$cantidad."<=coalesce(puser.partidas_presupuestarias.disponible,0)) AS PUEDO_COMPRAR, ";
	$q.="puser.partidas_presupuestarias.id AS id_partida, ";
	$q.="puser.productos.ultimo_costo AS precio ";
	$q.="FROM puser.relacion_pp_cp ";
	$q.="Inner Join puser.partidas_presupuestarias ON puser.relacion_pp_cp.id_partida_presupuestaria = puser.partidas_presupuestarias.id "; 
	$q.="AND puser.relacion_pp_cp.id_escenario = puser.partidas_presupuestarias.id_escenario ";
	$q.="Inner Join puser.categorias_programaticas ON puser.relacion_pp_cp.id_categoria_programatica = puser.categorias_programaticas.id "; 
	$q.="AND puser.relacion_pp_cp.id_escenario = puser.categorias_programaticas.id_escenario ";
	$q.="Inner Join puser.tipo_producto ON puser.partidas_presupuestarias.id = puser.tipo_producto.id_partidas_presupuestarias ";
	$q.="Inner Join puser.productos ON puser.tipo_producto.id = puser.productos.id_tipo_producto ";
	$q.="WHERE puser.relacion_pp_cp.id_categoria_programatica = '$id_cp' AND puser.relacion_pp_cp.id_escenario = $escEnEje AND puser.productos.id = '$id_producto'";
	//die($q);
	$r= $conn->Execute($q);
		if($r){
			$pp = new requisiciones;
				$pp->id_partida = $r->fields['id_partida']; 
				$pp->idparcat = $r->fields['idparcat'];
				$pp->disponible = $r->fields['disponible'];
				$pp->total = $r->fields['total'];
				$pp->puedo = $r->fields['puedo_comprar'];
				$pp->precio = $r->fields['precio'];
			
		}
		return($pp);	
	}
	
	function buscaProductoporCategoria($conn,$id_categoria_programatica, $descripcion=""){
		$q="SELECT DISTINCT puser.relacion_pp_cp.id_categoria_programatica, ";
		$q.="puser.relacion_pp_cp.id_partida_presupuestaria, puser.productos.id, ";
		$q.="puser.productos.descripcion ";
		$q.="FROM puser.relacion_pp_cp ";
		$q.="Inner Join puser.tipo_producto ON puser.relacion_pp_cp.id_partida_presupuestaria = puser.tipo_producto.id_partidas_presupuestarias ";
		$q.="Inner Join puser.productos ON puser.tipo_producto.id = puser.productos.id_tipo_producto ";
		$q.="WHERE puser.relacion_pp_cp.id_categoria_programatica = '$id_categoria_programatica' ";
		if(!empty($descripcion)) $q.="AND puser.productos.descripcion ILIKE '$descripcion%'";
		//die($q);
		$r=$conn->Execute($q);
		while(!$r->EOF){
			$pro = new requisiciones;
				$pro->id = $r->fields['id'];
				$pro->descripcion = $r->fields['descripcion'];
				$pro->id_categoria_programatica = $r->fields['id_categoria_programatica'];
				$pro->id_partida_presupuestaria = $r->fileds['id_partida_presupuestaria'];
			$coleccion[]= $pro;
			$r->movenext();
		}
		return($coleccion);		
			
	}
	
	function total_registro_busqueda($conn,$id_ue, $fecha_desde, $fecha_hasta,  $orden="id", $status, $nrequi){
		if(empty($id_ue)
			&& empty($fecha_desde)
			&& empty($fecha_hasta)
			&& empty($status)
			&& empty($nrequi))
			return false;
		$q = "SELECT * FROM puser.requisiciones ";
		$q.= "WHERE  1=1 ";
		$q.= !empty($fecha_desde) ? "AND fecha_r >='".guardafecha($fecha_desde)."' ": "";
		$q.= !empty($fecha_hasta) ? "AND fecha_r <='".guardafecha($fecha_hasta)."' ": "";
		$q.= !empty($id_ue) ? "AND id_unidad_ejecutora = '$id_ue'  ":"";
		$q.= !empty($status) ? "AND status = '$status'  ":"";
		$q.= !empty($nrequi) ? "AND id = '$nrequi'  ":"";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		//$collection=array();
		$total = $r->RecordCount();

		return $total;
	}
	
	function reversoRequisicion($conn, $actEstado, $nextEstado, $idRequi, $idReqGbl){
		try{
			if($nextEstado == '01'){
				if($actEstado == '07'){
					$this->backToActualizacion($conn, $idReqGbl);
					$this->backToGblRequisicion($conn, $idReqGbl);
					$this->deleteGblRequisicion($conn, $idReqGbl);
					$sql = "SELECT id FROM puser.requisiciones WHERE nroreqgbl = '$idReqGbl'";
					$r = $conn->Execute($sql);
					while(!$r->EOF){
						$this->backToRequisicion($conn, $r->fields['id']);
						$r->movenext();
					}	
					return "Requisicion Reversada con Exito";
				}elseif($actEstado == '06'){
					$this->backToGblRequisicion($conn, $idReqGbl);
					$this->deleteGblRequisicion($conn, $idReqGbl);
					$sql = "SELECT id FROM puser.requisiciones WHERE nroreqgbl = '$idReqGbl'";
					$r = $conn->Execute($sql);
					while(!$r->EOF){
						$this->backToRequisicion($conn, $r->fields['id']);
						$r->movenext();
					}
					return "Requisicion Reversada con Exito";	
				}	
				elseif($actEstado == '04' || $actEstado == '02'){
					$this->backToRequisicion($conn, $idRequi);
					return "Requisicion Reversada con Exito";
				}
			}elseif($nextEstado == '05'){
				if($actEstado == '07'){
					$this->backToActualizacion($conn, $idReqGbl);
					$this->backToGblRequisicion($conn, $idReqGbl);
					return "Requisicion Reversada con Exito";
				}	
				elseif($actEstado == '06'){
					$this->backToGblRequisicion($conn, $idReqGbl);
					return "Requisicion Reversada con Exito";
				}
			}elseif($nextEstado == '06'){
				$this->backToActualizacion($conn, $idReqGbl);
				return "Requisicion Reversada con Exito";
			}
		}catch(ADODB_Exeption $e){
			if($e->getCode()==-1)
				return ERROR_CATCH_VFK;
			elseif($e->getCode()==-5)
				return ERROR_CATCH_VUK;
			else
				return 'ERROR AL REVERSAR REQUISICION';
		}
				
	}
	
	function backToRequisicion($conn, $idRequi){
		$sql = "DELETE FROM puser.precompromiso_requisiciones WHERE id_requisicion = '$idRequi'";
		//echo $sql.'<br>';
		$r = $conn->Execute($sql);
		$q = "UPDATE puser.requisiciones SET nroreqgbl = null, status = '01' WHERE id = '$idRequi'";
		//echo $q.'<br>';
		$row = $conn->Execute($q);
	}
	
	function backToGblRequisicion($conn, $idGblRequi){
		$sql = "DELETE FROM puser.proveedores_requisicion WHERE id_requisicion = '$idGblRequi'";
		//echo $sql.'<br>';
		$r = $conn->Execute($sql);
		$q1 = "UPDATE puser.gbl_requisicion SET status = '05' WHERE id = '$idGblRequi'";
		//echo $q1.'<br>';
		$r1 = $conn->Execute($q1);
		$q2 = "UPDATE puser.requisiciones SET status = '05' WHERE nroreqgbl = '$idGblRequi'";
		//echo $q2.'<br>';
		$r2 = $conn->Execute($q2);
	}
	
	function backToActualizacion($conn, $idGblRequi){
		$sql = "UPDATE puser.gbl_requisicion SET status = '06' WHERE id = '$idGblRequi' ";
		//echo $sql.'<br>';
		$row = $conn->Execute($sql);
		$q = "UPDATE puser.requisiciones SET status = '06' WHERE nroreqgbl = '$idGblRequi'";
		//echo $q.'<br>';
		$r = $conn->Execute($q);
	} 
	
	function deleteGblRequisicion($conn, $idGblRequi){
		$sql = "DELETE FROM puser.gbl_requisicion WHERE id = '$idGblRequi'";
		//echo $sql.'<br>';
		$r = $conn->Execute($sql);
	}
	
}
?>
