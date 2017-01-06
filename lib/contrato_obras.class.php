<?
class contrato_obras{

	// Propiedades

	var $id;
	var $id_tipo_documento;
	var $id_unidad_ejecutora;
	var $id_proveedor;
	var $id_tipo_fianza;
	var $id_obra;
	var $id_usuario;
	var $descripcion;
	var $observaciones;
	var $rif;
	var $nombre_proveedor;
	var $dir_proveedor;
	var $unidad_ejecutora;
	var $fecha;
	var $fecha_aprobacion;
	var $nrodoc;
	var $status;
	var $montotot;
	var $total;
	var $tipoProv;
	
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
	var $monto;
	var $RelacionPARCAT;
	
	/****************************
		Para almacenar los valores de las fianzas
	*****************************/
	var $tip_fianza;
	var $porc_fianza;
	var	$num_contrato;
	var	$fecha_ini;
	var $fecha_fin;

	function get($conn, $id, $escEnEje=''){
		$q = "SELECT contrato_obras.*, contrato_obras.id_tipo_fianza::char(1) AS fianza, unidades_ejecutoras.descripcion AS unidad_ejecutora, ";
		$q.= "proveedores.id AS id_proveedor, proveedores.rif, proveedores.nombre AS nombre_proveedor, ";
		$q.= "proveedores.direccion AS dir_proveedor, tipos_fianzas.id AS id_tipo_fianza, proveedores.provee_contrat, contrato_obras.monto::numeric AS montotot ";
		$q.= "FROM contrato_obras ";
		$q.= "INNER JOIN unidades_ejecutoras ON (contrato_obras.id_unidad_ejecutora = unidades_ejecutoras.id) ";
		$q.= "INNER JOIN proveedores ON (contrato_obras.id_proveedor = proveedores.id) ";
		$q.= "LEFT JOIN tipos_fianzas ON (contrato_obras.id_tipo_fianza = tipos_fianzas.id) ";
		$q.= "WHERE contrato_obras.id='$id'  ";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->id_tipo_documento = $r->fields['id_tipo_documento'];
			$this->id_unidad_ejecutora = $r->fields['id_unidad_ejecutora'];
			$this->id_proveedor = $r->fields['id_proveedor'];
			$this->id_tipo_fianza = $r->fields['fianza'];
			$this->id_obra = $r->fields['id_obra'];
			$this->id_usuario = $r->fields['id_usuario'];
			$this->descripcion = $r->fields['descripcion'];
			$this->observaciones = $r->fields['observaciones'];
			$this->rif = $r->fields['rif'];
			$this->nombre_proveedor = $r->fields['nombre_proveedor'];
			$this->dir_proveedor = $r->fields['dir_proveedor'];
			$this->unidad_ejecutora = $r->fields['unidad_ejecutora'];
			$this->fecha = $r->fields['fecha'];
			$this->fecha_aprobacion = $r->fields['fecha_aprobacion'];
			/*if(!$auxNrodoc)
				$this->nrodoc = $this->showNrodoc($r->fields['nrodoc']);
			else*/
			$this->nrodoc = $r->fields['nrodoc'];
			$this->getRelacionPartidas($conn, $id, $escEnEje);
			$this->status= $r->fields['status'];
			$this->montotot= $r->fields['montotot'];
			$this->tipoProv = $r->fields['provee_contrat'];
			//$this->porc_iva= $r->fields['porc_iva'];
			return true;
		}else
			return false;
	}

	function get_all($conn, $escEnEje,$orden="id"){
		$q = "SELECT * FROM contrato_obras ";
		$q.= "ORDER BY $orden ";
		$r = $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new contrato_obras;
			$ue->get($conn, $r->fields['id'], $escEnEje);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, 
						$id_tipo_documento,
						$id_unidad_ejecutora,
						$id_proveedor,
						$id_tipo_fianza,
						$id_obra,
						$id_usuario,
						$descripcion,
						$observaciones,
						$fecha,
						$contrato_o,
						$monto,
						$nrodoc='',
						$auxNroDoc
						){
		//$nrodoc = $this->addNrodoc($nrodoc, $id_tipo_documento,$id_unidad_ejecutora);
		$q = "INSERT INTO puser.contrato_obras ";
		$q.= "( ";
		$q.= "id_tipo_documento, ";
		$q.= "id_unidad_ejecutora, ";
		$q.= "id_proveedor, ";
		$q.= "id_tipo_fianza, ";
		$q.= "id_obra, ";
		$q.= "id_usuario, ";
		$q.= "descripcion, ";
		$q.= "observaciones, ";
		$q.= "fecha, ";
		$q.= "status, ";
		$q.= "monto, ";
		$q.= "nrodoc ";
		$q.= ") ";
		$q.= "VALUES ";
		$q.= "( ";
		$q.= " '$id_tipo_documento', ";
		$q.= " '$id_unidad_ejecutora', ";
		$q.= " '$id_proveedor', ";
		$q.= " '$id_tipo_fianza', ";
		$q.= " '$id_obra', ";
		$q.= " '$id_usuario', ";
		$q.= " '$descripcion', ";
		$q.= " '$observaciones', ";
		$q.= " '$fecha', ";
		$q.= " 1,";
		$q.= " '$monto', ";
		$q.= " '$nrodoc' ";
		$q.= ") ";
		//die($q);

		#DECODIFICO EL JSON DE LAS PARTIDAS PRESUPUESTARIAS#
		$JsonRec = new Services_JSON();
		$JsonRec = $JsonRec->decode(str_replace("\\","",$contrato_o));
		$contador = sizeof($JsonRec->contrato);
		
		#CHEQUEO LA DISPONIBILIDAD DE LAS PARTIDAS, EN EL CASO DE QUE EL MONTO NO ESTA DISPONIBLE NO SE APRUEBA ESA ORDEN#
		for($i = 0; $i < $contador; $i++){
			$q2 = "SELECT relacion_pp_cp.disponible FROM relacion_pp_cp WHERE id = '".$JsonRec->contrato[$i][7]."' ";
			//die($q2);
			$r2 = $conn->Execute($q2);
			if($r2){
				if($r2->fields['disponible'] < $JsonRec->contrato[$i][2]){
					$this->msj = ERROR_CO_APR_NO_DISP;
					return false;
				}
			}
		}
		$r = $conn->execute($q);
		if($r){
				$nrodoc = getLastId($conn, 'id', 'contrato_obras'); 
				if(
				$this->addRelacionPartidas($conn, 
												$nrodoc,
												$contrato_o)
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
						$id_tipo_documento,
						$id_unidad_ejecutora,
						$id_proveedor,
						//$id_tipo_fianza,
						$id_obra,
						$id_usuario,
						$descripcion,
						$observaciones,
						$fecha,
						$aPartidas,
						$monto, 
						$nrodoc='',
						$auxNroDoc){
						//$porc_iva){
		//$nrodoc = $this->addNrodoc2($nrodoc, $id_tipo_documento);
		$q = "UPDATE puser.contrato_obras SET  ";
		$q.= "id_tipo_documento = '$id_tipo_documento', ";
		$q.= "id_unidad_ejecutora = '$id_unidad_ejecutora', ";
		$q.= "id_proveedor = '$id_proveedor', ";
		$q.= "id_tipo_fianza = '1', ";
		$q.= "id_obra = '$id_obra', ";
		$q.= "id_usuario = '$id_usuario', ";
		$q.= "descripcion = '$descripcion', ";
		$q.= "observaciones = '$observaciones', ";
		$q.= "monto = '$monto', ";
		$q.= "nrodoc = '$nrodoc', ";
		$q.= "fecha = '$fecha' ";
		//$q.= "porc_iva = $porc_iva ";
		$q.= "WHERE id='$id' ";	
		//die($q);
		if($conn->Execute($q)){
			if($this->delRelacionPartidas($conn, $id)){
				if($this->addRelacionPartidas($conn, 
													$id,
													$aPartidas)){
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

	function del($conn, $id){
		$q = "DELETE FROM contrato_obras WHERE id='$id'";
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
										 $c_obras){
		//die($c_obras);
		$JsonRec = new Services_JSON();
		$JsonRec=$JsonRec->decode(str_replace("\\","",$c_obras));
		if(is_array($JsonRec->contrato)){
			foreach ($JsonRec->contrato as $oCO_Aux){
				$CO_Aux_2= $oCO_Aux[2];
			
				$q = "INSERT INTO puser.relacion_contrato_obras ";
				$q.= "( id_parcat, id_categoria_programatica, id_partida_presupuestaria, id_contrato_obras, monto,porc_iva,monto_exc) ";
				$q.= "VALUES ";
				$q.= "('$oCO_Aux[7]', '$oCO_Aux[0]', '$oCO_Aux[1]', '$nrodoc', '$CO_Aux_2', '$oCO_Aux[4]',$oCO_Aux[3]) ";
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
		$q = "DELETE FROM relacion_contrato_obras WHERE id_contrato_obras='$nrodoc'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	function getRelacionPartidas($conn, $id, $escEnEjec){
		$q = "SELECT puser.relacion_contrato_obras.*, puser.partidas_presupuestarias.descripcion AS partida_presupuestaria,  ";
		$q.= "puser.categorias_programaticas.descripcion AS categoria_programatica ";
		$q.= "FROM puser.relacion_contrato_obras  ";
		$q.= "INNER JOIN puser.partidas_presupuestarias ON (relacion_contrato_obras.id_partida_presupuestaria = partidas_presupuestarias.id) ";
		$q.= "INNER JOIN puser.categorias_programaticas ON (relacion_contrato_obras.id_categoria_programatica = categorias_programaticas.id) ";
		$q.= "WHERE relacion_contrato_obras.id_contrato_obras='$id' ";
		$q.= "AND categorias_programaticas.id_escenario = '$escEnEjec' ";
		$q.= "AND partidas_presupuestarias.id_escenario = '$escEnEjec' ";
		//die($q);
		$r = $conn->Execute($q);
		while(!$r->EOF){
			$ue = new movimientos_presupuestarios;
			$ue->nrodoc = $r->fields['nrodoc'];
			$ue->idParCat	= $r->fields['id_parcat'];
			$ue->id_partida_presupuestaria	= $r->fields['id_partida_presupuestaria'];
			$ue->id_categoria_programatica = $r->fields['id_categoria_programatica'];
			$ue->partida_presupuestaria	= $r->fields['partida_presupuestaria'];
			$ue->categoria_programatica = $r->fields['categoria_programatica'];
			$ue->monto = $r->fields['monto'];
			$ue->porc_iva = $r->fields['porc_iva'];
			$ue->monto_exc = $r->fields['monto_exc'];
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
	function aprobar($conn, $id_obra,  $nrif,
										$id_usuario,
										$id_unidad_ejecutora,
										$ano,
										$descrip,
										$tipdoc,
										$fechadoc,
										$status,
										$rif,
										$aContrato,
										$nrodoc='',
										$auxNroDoc,
										$escEnEje,
										$tipoProv){
		#OBTENGO EL NRO DE DOCUMENTOS#
		if(empty($nrodoc))
			$nrodoc = movimientos_presupuestarios::getNroDoc($conn, $tipdoc);
		else
			$nrodoc = $this->addNrodoc2($nrodoc, $tipdoc);
		
		#DECODIFICO EL JSON DE LAS PARTIDAS PRESUPUESTARIAS#
		$JsonRec = new Services_JSON();
		$JsonRec = $JsonRec->decode(str_replace("\\","",$aContrato));
		$contador = sizeof($JsonRec->contrato);
		
		#CHEQUEO LA DISPONIBILIDAD DE LAS PARTIDAS, EN EL CASO DE QUE EL MONTO NO ESTA DISPONIBLE NO SE APRUEBA ESA ORDEN#
		for($i = 0; $i < $contador; $i++){
			$q = "SELECT relacion_pp_cp.disponible FROM relacion_pp_cp WHERE id = '".$JsonRec->contrato[$i][7]."' ";
			//die($q);
			$r = $conn->Execute($q);
			if($r){
				//echo "disponible ".$r->fields['disponible']."<br>";
				//die("partida ".$JsonRec->contrato[$i][2]."<br>");
				if($r->fields['disponible'] < $JsonRec->contrato[$i][2]){
					$this->msj = ERROR_CO_APR_NO_DISP;
					return false;
				}
			}
		} 
		// chequeo si fue agregada la fianza	
		if($tipoProv!='G'){
			$q = "SELECT 'X' AS si FROM contrato_obras_fianza WHERE id_contrato = '$id_obra' ";
			//die($q);
			$r = $conn->Execute($q);
			if($r->fields['si'] != 'X'){
				$this->msj = ERROR_ORDEN_APR_NO_FIANZA;
				return false;
			}
		}

		#REGISTRO EL CONTRATO DE OBRAS EN MOVIMIENTOS PRESUPUESTARIOS#	
		$q = "INSERT INTO movimientos_presupuestarios ";
		$q.= "(id_usuario, id_unidad_ejecutora, ano, descripcion, nrodoc, tipdoc,  ";//nroref, 
		$q.= "fechadoc, status, id_proveedor, status_movimiento) ";
		$q.= "VALUES ";
		$q.= "('$id_usuario', '$id_unidad_ejecutora', '$ano', '$descrip', '$nrodoc', '011',  ";//'$nroref', 
		$q.= " '$fechadoc', '1', '$rif', '1') ";
		$r = $conn->Execute($q) or die($q);
		
		#ESTE FOREACH SE HACE PARA PASAR LAS VARIABLES A LA FUNCION ADD_RELACION DE LA CLASE MOVIMIENTOS PRESUPUESTARIOS#
		$idCat = array();
		foreach($JsonRec->contrato as $contrato){
		
			$aIdParCat[] 				= $contrato[7];
			$aCategoriaProgramatica[] 	= $contrato[0];
			$aPartidaPresupuestaria[] 	= $contrato[1];
			$aMonto[] 					= muestrafloat($contrato[2]);
			
			//ESTO SE HACE PARA SACAR EL IVA POR CATEGORIA PROGRAMATICA
			$indice = array_search($contrato[0],$idCat);
			if ($indice!==false){
				$aIva[$indice] = $aIva[$indice] + guardafloat($contrato[5]);
			} else {
				$idCat[] = $contrato[0];
				$aIva[] = guardafloat($contrato[5]);
			}
		}
		
		#ESTO SE HACE PARA AÑADIR LA PARTIDA DEL IVA A LA IMPUTACION PRESUPUESTARIA
		if(count($aIva) > 0){
			for($i=0;$i<count($aIva);$i++){
				$q = "SELECT relacion_pp_cp.id AS idparcat FROM puser.relacion_pp_cp WHERE id_categoria_programatica = '$idCat[$i]' ";
				$q.= "AND id_partida_presupuestaria = '4031801000000' ";
				$q.= "AND id_escenario = '$escEnEje'";
				$row = $conn->Execute($q);
				while(!$row->EOF){
					$idpc = $row->fields['idparcat'];
					//die("aqui: ".$idpc);
					if($aIva[$i]>0){
						$aIdParCat[] 			= $idpc;        //$partidas[3];
						$aCategoriaProgramatica[] 	= $idCat[$i];
						$aPartidaPresupuestaria[] 	= '4031801000000';
						$aMonto[] 				= muestrafloat($aIva[$i]);
					}
					$row->movenext();
				}
				
			}
		}
		
		/*print_r($aIdParCat);
		echo "<br>";
		die(print_r($aMonto));*/
		#VERIFICO SI EL CONTRATO SE REGISTRO EN MOVIMIENTOS PRESUPUESTARIOS#
		if($r){
			
			#AGREGO LAS RELACIONES DEL MOVIMIENTO PRESUPUESTARIO#
			$a = movimientos_presupuestarios::add_relacion($conn,
					$aIdParCat,
					$aCategoriaProgramatica,
					$aPartidaPresupuestaria,
					$nrodoc,
					$aMonto);
			
			#ASIGNO EL NRO DE DOCUMENTO A LA ORDEN DE PAGO#	
			# ESTA OPCION SE ACTIVA CUANDO LOS NUMEROS DE DOCUMENTOS SE GENEREN AUTOMATICAMENTE	
			$c = $this->setNrodoc($conn, $nrodoc, $id_obra);
			
			#ACTUALIZO LA LA FECHA DE APROBACION Y EL ESTATUS DE LA ORDEN DE PAGO#
			$d = $this->setFechaAprobacion($conn, $id_obra);
			
			#ACTUALIZO LA TABLA RELACION PP_CP EL COMPROMISO Y EL DISPONIBLE#
			$e = relacion_pp_cp::set_desde_compromiso($conn, $aIdParCat, $aMonto);
			
			#VERIFICO SI TODOS LOS PROCESOS ANTERIORES SE EJECUTARON PARA MOSTRAR EL MENSAJE#
			if($a && $c && $d && $e){
			#if($a && $d && $e){
				$this->msj = CO_APROBADA;
				return true;
			}
		}else
			return false;
	}
	
	function anular($conn, $id, $id_usuario,
										$id_unidad_ejecutora,
										$ano,
										$descripcion,
										$tipdoc,
										$fechadoc,
										$status,
										$id_proveedor,
										$aPartidas,
										$nrodoc,
										$porc_iva,
										$escEnEje){
		
		try {
		
		$q3 ="UPDATE puser.contrato_obras SET status='3' WHERE id='$id'";

		$r3 = $conn->Execute($q3);
		
		//$nrodoc = $this->addNrodoc2($nrodoc, $tipdoc);
		
		#ESTE IF ES EN EL CASO DE QUE LA ORDEN DE COMPRA YA ESTA APROBADA#			
		//
		if ($r3 && $status=='2'){
			
			#DECODIFICO EL JSON Y LOS CONVIERTO EN UN ARRAY DE PHP #	
			$JsonRec = new Services_JSON();
			$JsonRec = $JsonRec->decode(str_replace("\\","",$aPartidas));
			//die(print_r($JsonRec));
			$contador = sizeof($JsonRec->contrato); 
			$nrodocanulado = $nrodoc."-ANULADO";
			$idCat = array();
			//REGISTRO LA SOLICITUD EN MOVIMIENTOS PRESUPUESTARIOS//
			$q2 = "INSERT INTO puser.movimientos_presupuestarios ";
			$q2.= "(id_usuario, id_unidad_ejecutora, ano, descripcion, nrodoc, tipdoc, nroref, ";
			$q2.= "fechadoc, status, id_proveedor, status_movimiento) ";
			$q2.= "VALUES ";
			$q2.= "('$id_usuario', '$id_unidad_ejecutora', '$ano', '$descripcion', '$nrodoc', '$tipdoc', '$nrodocanulado', ";
			$q2.= " '$fechadoc', '1', '$id_proveedor', '2') ";
			//die($q2);
			//ESTE FOREACH SE HACE PARA PASAR LAS VARIABLES A LA CLASE MOVIMIENTOS PRESUPUESTARIOS//
			foreach($JsonRec->contrato as $contrato){
				$guarda_monto = $contrato[2] * (-1);
				
				$aIdParCat[] = $contrato[7];
				$aCategoriaProgramatica[] = $contrato[0];
				$aPartidaPresupuestaria[] = $contrato[1];
				$aMonto[] = muestrafloat($guarda_monto); 
				
				$indice = array_search($contrato[0],$idCat);
				if ($indice!==false){
					$aIva[$indice] = $aIva[$indice] + guardafloat($contrato[5]);
				} else {
					$idCat[] = $contrato[0];
					$aIva[] = guardafloat($contrato[5]);
				}
			
			}
			
			if(count($aIva) > 0){
				for($i=0;$i<count($aIva);$i++){
					$q = "SELECT relacion_pp_cp.id AS idparcat FROM puser.relacion_pp_cp WHERE id_categoria_programatica = '$idCat[$i]' ";
					$q.= "AND id_partida_presupuestaria = '4031801000000' ";
					$q.= "AND id_escenario = '$escEnEje'";
					$row = $conn->Execute($q);
					$idpc = $row->fields['idparcat'];
					//die("aqui: ".$idpc);
					if($aIva[$i]>0){
						$aIdParCat[]				= $idpc;        //$partidas[7];
						$aCategoriaProgramatica[] 	= $idCat[$i];
						$aPartidaPresupuestaria[] 	= '4031801000000';
						$aMonto[] 				= $aIva[$i] * (-1);
					}
					
					
				}
			}
			
			/*print_r($aIdParCat);
			echo "<br>";
			die(print_r($aMonto));*/
			
			//die("Monto ".$aMonto[0]);	
			$r2 = $conn->Execute($q2);
			
			#VALIDO SI INSERTO EL REGISTRO EN LA TABLA DE MOVIMIENTO PRESUPUESTARIOS#
			if($r2){
				
				#AGREGO LAS RELACIONES DEL MOVIMIENTO PRESUPUESTARIO#
				if(movimientos_presupuestarios::add_relacion($conn,$aIdParCat,$aCategoriaProgramatica,$aPartidaPresupuestaria,$nrodoc,$aMonto)){
					
					#MODIFICO EL COMPROMISO Y EL DISPONIBLE DE LA PARTIDA PRESUPUESTARIA#
					if(relacion_pp_cp::set_desde_compromiso_anulado($conn, $aIdParCat, $aMonto)){
						$this->msj = CO_ANULADA;
						return true;
	
					}
				}
			}else{
		
				$this->msj = ERROR;
				return false;
			}
		
		}elseif ($r3){
		
			$this->msj = CO_ANULADA;
			return true;
		
		}else{
			
			$this->msj = ERROR;
			return false;
			
		}
		}
		
		catch( ADODB_Exception $e ){
			//die($conn->ErrorMsg());
				$this->msj = $conn->ErrorMsg();
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
		$q = "UPDATE puser.contrato_obras SET fecha_aprobacion = now(), status = 2 WHERE id='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	function buscar($conn, 
	$id_proveedor, 
	$id_ue, 
	$fecha_desde, 
	$fecha_hasta, 
	$nrodoc,
	$descripcion, 
	$orden="id",$from=1, $max=10){
		if(empty($id_proveedor) 
			&& empty($id_ue)
			&& empty($fecha_desde)
			&& empty($fecha_hasta)
			&& empty($nrodoc)
			&& empty($descripcion)
			)
			return false;
		$q = "SELECT * FROM contrato_obras ";
		$q.= "WHERE  1=1 ";
		$q.= !empty($nrodoc) ? "AND nrodoc='$nrodoc' ": "";
		$q.= !empty($fecha_desde) ? "AND fecha >='".guardafecha($fecha_desde)."' ": "";
		$q.= !empty($fecha_hasta) ? "AND fecha <='".guardafecha($fecha_hasta)."' ": "";
		$q.= !empty($id_proveedor) ? "AND id_proveedor = '$id_proveedor'  ":"";
		$q.= !empty($id_ue) ? "AND id_unidad_ejecutora = '$id_ue'  ":"";
		$q.= !empty($descripcion) ? "AND descripcion ILIKE '%$descripcion%' ":"";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from):$conn->Execute($q);
		if(!$r)
			return false;
		$collection=array();
		while(!$r->EOF){
			$ue = new contrato_obras;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		return $coleccion;
	}
	
	function totalRegistroBusqueda($conn,$id_proveedor,$id_ue,$fecha_desde,$fecha_hasta,$nrodoc,$descripcion,$orden="id"){
		if(empty($id_proveedor) && empty($id_ue) && empty($fecha_desde) && empty($fecha_hasta) && empty($nrodoc) && empty($descripcion))
			return 0;
		$q = "SELECT * FROM puser.contrato_obras ";
		$q.= "WHERE  1=1 ";
		$q.= !empty($nrodoc) ? "AND nrodoc='$nrodoc' ": "";
		$q.= !empty($fecha_desde) ? "AND fecha >='".guardafecha($fecha_desde)."' ": "";
		$q.= !empty($fecha_hasta) ? "AND fecha <='".guardafecha($fecha_hasta)."' ": "";
		$q.= !empty($id_proveedor) ? "AND id_proveedor = '$id_proveedor'  ":"";
		$q.= !empty($id_ue) ? "AND id_unidad_ejecutora = '$id_ue'  ":"";
		$q.= !empty($descripcion) ? "AND descripcion ILIKE '%$descripcion%' ":"";
		$q.= "ORDER BY $orden ";
		//die($q);
		if($r = $conn->Execute($q))
			return $r->RecordCount();
	}
	
	function add_fianza($conn, $id_contrato, $id_tip_fianza, $num_contr_fian, $porc_fianza, $fecha_ini, $fecha_fin){
		$q= "INSERT INTO puser.contrato_obras_fianza (id_contrato, id_tipo_fianza, num_contrato, porc_fianza, fecha_ini, fecha_fin) VALUES (";
		$q.= "'$id_contrato', ";
		$q.= "'$id_tip_fianza', ";
		$q.= "'$num_contr_fian', ";
		$q.= "$porc_fianza, ";
		$q.= "'$fecha_ini', ";
		$q.= "'$fecha_fin') ";
		//die($q);
		$r = $conn->execute($q);
		if ($r){
			$q= "UPDATE contrato_obras SET id_tipo_fianza = '$id_tip_fianza' WHERE id = $id_contrato";		
			$a = $conn->execute($q);
			$this->msj = REG_ADD_OK;
					return true;
		} else {
			$this->msj = ERROR_ADD;
			return false;
		}
	}
	
	function set_fianza($conn,  $id_contrato, $id_tipo_fianza, $num_contra_fian, $porc_fianza, $fecha_ini, $fecha_fin){
		$q= "UPDATE puser.contrato_obras_fianza SET id_tipo_fianza = '$id_tipo_fianza', ";
		$q.= "num_contrato = '$num_contra_fian', ";
		$q.= "porc_fianza = '$porc_fianza', ";
		$q.= "fecha_ini = '$fecha_ini', ";
		$q.= "fecha_fin = '$fecha_fin' ";
		$q.= "WHERE id_contrato = '$id_contrato'";
		//die($q);
		$r= $conn->execute($q);
		if ($r){
			$q= "UPDATE contrato_obras SET id_tipo_fianza = '$id_tip_fianza' WHERE id = $id_contrato";
			$a = $conn->execute($q);
		}
	}
	
	function get_fianza($conn, $id_contrato){
		$q= "SELECT * FROM puser.contrato_obras_fianza ";
		$q.= "WHERE id_contrato = '$id_contrato'";
		$r = $conn->execute($q);
		if ($r) {
			$this->tip_fianza = $r->fields['id_tipo_fianza'];
			$this->porc_fianza = $r->fields['porc_fianza'];
			$this->num_contrato = $r->fields['num_contrato'];
			$this->fecha_ini = $r->fields['fecha_ini'];
			$this->fecha_fin = $r->fields['fecha_fin'];
			return true;
			} else {
				return false;
			}
	}
	
	function showNrodoc($nrodoc){
		$aux = explode("-",$nrodoc);
		$tipdoc = $aux[0];
		$id_ue = $aux[1];
		$nrodoc = $aux[2];
		if ($nrodoc!= ''){
			return $id_ue."-".$nrodoc;
		} else {
			$nrodoc = '';
			return $nrodoc;
			}	
	}
	
	function addNrodoc($nrodoc, $tipdoc, $id_ue){
		$aux = $tipdoc."-".$id_ue."-".$nrodoc;
		return $aux;
	}
	
	function addNrodoc2($nrodoc, $tipdoc){
		$aux = $tipdoc."-".$nrodoc;
		return $aux;
	}
}
?>
