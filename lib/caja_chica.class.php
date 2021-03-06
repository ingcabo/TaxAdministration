<?
class caja_chica{

	// Propiedades

	var $id;
	var $id_tipo_documento;
	var $id_unidad_ejecutora;
	var $id_ciudadano;
	var $id_usuario;
	var $descripcion;
	var $observaciones;
	var $ciudadano;
	var $dir_ciudadano;
	var $tlf_ciudadano;
	var $rif_ciudadano;
	var $unidad_ejecutora;
	var $fecha;
	var $fecha_aprobacion;
	var $nrodoc;
	var $json;
	var $json_fact;
	var $total;
	var $status;
	
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

	function get($conn, $id, $escEnEje='1111'){
		$q = "SELECT caja_chica.*, unidades_ejecutoras.descripcion AS unidad_ejecutora, ";
		$q.= "proveedores.nombre AS ciudadano,  ";
		$q.= "proveedores.direccion AS dir_ciudadano, proveedores.telefono AS tlf_ciudadano, proveedores.rif AS rif_ciudadano ";
		$q.= "FROM caja_chica ";
		$q.= "INNER JOIN unidades_ejecutoras ON (caja_chica.id_unidad_ejecutora = unidades_ejecutoras.id) ";
		$q.= "LEFT JOIN proveedores ON (caja_chica.id_ciudadano = proveedores.id) ";
		$q.= "WHERE caja_chica.id='$id' AND  unidades_ejecutoras.id_escenario=$escEnEje ";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->id_tipo_documento = $r->fields['id_tipo_documento'];
			$this->id_unidad_ejecutora = $r->fields['id_unidad_ejecutora'];
			$this->id_ciudadano = $r->fields['id_ciudadano'];
			$this->id_usuario = $r->fields['id_usuario'];
			$this->descripcion = $r->fields['descripcion'];
			$this->observaciones = $r->fields['observaciones'];
			$this->ciudadano = $r->fields['ciudadano'];
			$this->dir_ciudadano = $r->fields['dir_ciudadano'];
			$this->tlf_ciudadano = $r->fields['tlf_ciudadano'];
			$this->rif_ciudadano = $r->fields['rif_ciudadano'];
			$this->unidad_ejecutora = $r->fields['unidad_ejecutora'];
			$this->fecha = $r->fields['fecha'];
			$this->fecha_aprobacion = $r->fields['fecha_aprobacion'];
			/*if(!$auxNrodoc)
				$this->nrodoc = $this->showNrodoc($r->fields['nrodoc']);
			else*/
				$this->nrodoc = $r->fields['nrodoc'];
			$this->json = $this->getRelacionPartidas($conn, $id, $escEnEje);
			$this->status = $r->fields['status'];
			$this->json_fact = $this->getFacturas_CajaChica($conn, $id);
			return true;
		}else
			return false;
	}

	function get_all($conn, $escEnEje,$orden="id"){
		$q = "SELECT * FROM caja_chica ";
		$q.= "ORDER BY $orden ";
		$r = $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new caja_chica;
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
						$id_ciudadano,
						$ciudadano,
						$dir_ciudadano,
						$tlf_ciudadano,
						$id_usuario,
						$descripcion,
						$observaciones,
						$fecha,
						$aPartidas,
						$aFacturas,
						$nrodoc='',
						$auxNroDoc){
						
		//$nrodoc = $this->addNrodoc($nrodoc, $id_tipo_documento, $id_unidad_ejecutora);
		$q = "INSERT INTO caja_chica ";
		$q.= "( ";
		$q.= "id_tipo_documento, ";
		$q.= "id_unidad_ejecutora, ";
		$q.= "id_ciudadano, ";
		$q.= "id_usuario, ";
		$q.= "descripcion, ";
		$q.= "observaciones, ";
		$q.= "fecha, ";
		$q.= "status, ";
		$q.= "nrodoc ";
		$q.= ") ";
		$q.= "VALUES ";
		$q.= "( ";
		$q.= " '$id_tipo_documento', ";
		$q.= " '$id_unidad_ejecutora', ";
		$q.= " '$id_ciudadano', ";
		$q.= " '$id_usuario', ";
		$q.= " '$descripcion', ";
		$q.= " '$observaciones', ";
		$q.= " '$fecha', ";
		$q.= " '1', ";
		$q.= " '$nrodoc' ";
		$q.= ") ";
		//die($q);
		$r = $conn->execute($q);
		if($r){
				$nrodoc = getLastId($conn, 'id', 'caja_chica'); 
				/*$ec = new ciudadanos;
				 $ec->get($conn, $id_ciudadano);
				if(empty($ec->id))
					ciudadanos::add($conn, $id_ciudadano, $ciudadano, $dir_ciudadano, $tlf_ciudadano);*/
				$this->addFacturas_CajaChica($conn,$nrodoc,$aFacturas);
				if(
				$this->addRelacionPartidas($conn, 
												$nrodoc,$aPartidas)){
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
						$id_ciudadano, 
						$ciudadano, 
						$dir_ciudadano, 
						$tlf_ciudadano, 
						$descripcion,
						$observaciones,
						$fecha,
						$aPartidas,
						$aFacturas,
						$nrodoc='',
						$auxNroDoc
						){
						
		//$nrodoc = $this->addNrodoc2($nrodoc, $id_tipo_documento);
		$q = "UPDATE caja_chica SET  ";
		$q.= "id_tipo_documento = '$id_tipo_documento', ";
		$q.= "id_unidad_ejecutora = '$id_unidad_ejecutora', ";
		$q.= "id_ciudadano = '$id_ciudadano', ";
		$q.= "descripcion = '$descripcion', ";
		$q.= "observaciones = '$observaciones', ";
		$q.= "nrodoc = '$nrodoc '";
		$q.= "WHERE id='$id' ";	
		//die($q);
		if($conn->Execute($q)){
			/*$oCiudadano = new ciudadanos;
			$oCiudadano->get($conn, $id_ciudadano);
			if(empty($oCiudadano->nombre))
				ciudadanos::add($conn, $id_ciudadano, $ciudadano, $dir_ciudadano, $tlf_ciudadano);*/
			if($this->delRelacionPartidas($conn, $id)){
				if ($this->addRelacionPartidas($conn, $id, $aPartidas)) {
					if ($this->delFacturas_CajaChica($conn, $id)) {
						if ($this->addFacturas_CajaChica($conn,$id,$aFacturas)) {
								$this->msj = REG_SET_OK;
								return true;
								} else {
									$this->msj = ERROR_SET;
									return false;
								}
							} else {
								$this->msj = ERROR_SET;
								return false;
							}
					} else {
						$this->msj = ERROR_SET;
						return false;
						}
			} else {
				$this->msj = ERROR_SET;
				return false;
			}
		}else{
			$this->msj = ERROR_SET;
			return false;
		}
	}

	function del($conn, $id){
		$q = "DELETE FROM caja_chica WHERE id='$id'";
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
										 $aCChica){
		//die($aCChica);								 
		$JsonRec = new Services_JSON();
		$JsonRec=$JsonRec->decode(str_replace("\\","",$aCChica));
		if(is_array($JsonRec->cajac)){
			foreach ($JsonRec->cajac as $aCC_Aux){
				$CC_Aux_2= $aCC_Aux[2];
			
				$q = "INSERT INTO puser.relacion_caja_chica ";
				$q.= "( id_parcat, id_categoria_programatica, id_partida_presupuestaria, id_caja_chica, monto, porc_iva, monto_exc) ";
				$q.= "VALUES ";
				$q.= "('$aCC_Aux[7]', '$aCC_Aux[0]', '$aCC_Aux[1]', '$nrodoc', '$CC_Aux_2', '$aCC_Aux[4]', '$aCC_Aux[3]') ";
				//echo($q."<br>");
				//die($q);
				$r = $conn->Execute($q);
			} 
		} 
		if($r)
			return true;
		else
			return false;
	}

	function delRelacionPartidas($conn, $nrodoc){
		$q = "DELETE FROM relacion_caja_chica WHERE id_caja_chica='$nrodoc'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
		
	function aprobar($conn, $id_caja_chica,
										$id_usuario,
										$id_unidad_ejecutora,
										$id_ciudadano,
										$ano,
										$descripcion,
										$tipdoc,
										$fechadoc,
										$status,
										$aPartidas,
										$aFacturas,
										$nrodoc='',
										$auxNroDoc,
										$escEnEje){
										
	#OBTENGO EL NRO DE DOCUMENTOS#
		if(empty($nrodoc))
			$nrodoc = movimientos_presupuestarios::getNroDoc($conn, $tipdoc);
		else
			$nrodoc = $this->addNrodoc2($nrodoc, $tipdoc);
		//die($nrodoc);
		#DECODIFICO EL JSON DE LAS PARTIDAS PRESUPUESTARIAS#
		$JsonRec = new Services_JSON();
		$JsonRec = $JsonRec->decode(str_replace("\\","",$aPartidas));
		$contador = sizeof($JsonRec->cajac);
		
		#CHEQUEO LA DISPONIBILIDAD DE LAS PARTIDAS, EN EL CASO DE QUE EL MONTO NO ESTA DISPONIBLE NO SE APRUEBA ESA ORDEN#
		for($i = 0; $i < $contador; $i++){
			$q = "SELECT relacion_pp_cp.disponible FROM relacion_pp_cp WHERE id = '".$JsonRec->cajac[$i][7]."' ";
			//die($q);
			$r = $conn->Execute($q);
			if($r){
				if($r->fields['disponible'] < $JsonRec->cajac[$i][2]){
					$this->msj = ERROR_SOLICITUD_PAGO_APR_NO_DISP;
					return false;
				}
			}
		} 
		
		#REGISTRO EL CONTRATO DE OBRAS EN MOVIMIENTOS PRESUPUESTARIOS#	
		$q = "INSERT INTO movimientos_presupuestarios ";
		$q.= "(id_usuario, id_unidad_ejecutora, ano, descripcion, nrodoc, tipdoc,  ";//nroref, 
		$q.= "fechadoc, status, id_proveedor, status_movimiento) ";
		$q.= "VALUES ";
		$q.= "('$id_usuario', '$id_unidad_ejecutora', '$ano', '$descripcion', '$nrodoc', '013',  ";//'$nroref', 
		$q.= " '$fechadoc', '1', '$id_ciudadano', '1') ";
		//die($q);
		$r = $conn->Execute($q) or die($q);
		
		#ESTE FOREACH SE HACE PARA PASAR LAS VARIABLES A LA FUNCION ADD_RELACION DE LA CLASE MOVIMIENTOS PRESUPUESTARIOS#
		$idCat = array();
		foreach($JsonRec->cajac as $cajac){
		
			$aIdParCat[] 				= $cajac[7];
			$aCategoriaProgramatica[] 	= $cajac[0];
			$aPartidaPresupuestaria[] 	= $cajac[1];
			$aMonto[] 					= muestrafloat($cajac[2]);
			
			//ESTO SE HACE PARA SACAR EL IVA POR CATEGORIA PROGRAMATICA
			$indice = array_search($cajac[0],$idCat);
			if ($indice!==false){
				$aIva[$indice] = $aIva[$indice] + $cajac[5];
			} else {
				$idCat[] = $cajac[0];
				$aIva[] = $cajac[5];
			}
		}
	
		#ESTO SE HACE PARA A�ADIR LA PARTIDA DEL IVA A LA IMPUTACION PRESUPUESTARIA
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
			$c = $this->setNrodoc($conn, $nrodoc, $id_caja_chica);
			
			#ACTUALIZO LA LA FECHA DE APROBACION Y EL ESTATUS DE LA ORDEN DE PAGO#
			$d = $this->setFechaAprobacion($conn, $id_caja_chica);
			
			#ACTUALIZO LA TABLA RELACION PP_CP EL COMPROMISO Y EL DISPONIBLE#
			$e = relacion_pp_cp::set_desde_compromiso($conn, $aIdParCat, $aMonto);
			
			#VERIFICO SI TODOS LOS PROCESOS ANTERIORES SE EJECUTARON PARA MOSTRAR EL MENSAJE#
			if($a && $c && $d && $e){
			#if($a && $d && $e){
				$this->msj = CC_APROBADA;
				return true;
				
			}
		
		}else{
			$this->msj = ERROR_CC_APR;
			return false;
		}
	}
		
	
	function setNrodoc($conn, $nrodoc, $id){
		$q = "UPDATE caja_chica SET nrodoc = '$nrodoc' WHERE id='$id'";
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	function setFechaAprobacion($conn, $id){
		$q = "UPDATE caja_chica SET fecha_aprobacion = now(), status = '2' WHERE id='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	function buscar($conn, $id_ciudadano, $id_ue, $fecha_desde, $fecha_hasta, $nrodoc, $descripcion, $orden="id", $from=0, $max=0)
	{
		if(empty($id_ciudadano) && empty($id_ue) && empty($fecha_desde) && empty($fecha_hasta) && empty($nrodoc) && empty($descripcion))
			return false;

		$q = "SELECT * FROM caja_chica ";
		$q.= "WHERE  1=1 ";
		$q.= !empty($nrodoc) ? "AND nrodoc='$nrodoc' ": "";
		$q.= !empty($fecha_desde) ? "AND fecha >='".guardafecha($fecha_desde)."' ": "";
		$q.= !empty($fecha_hasta) ? "AND fecha <='".guardafecha($fecha_hasta)."' ": "";
		$q.= !empty($id_ciudadano) ? "AND id_ciudadano = '$id_ciudadano'  ":"";
		$q.= !empty($id_ue) ? "AND id_unidad_ejecutora = '$id_ue'  ":"";
		$q.= !empty($descripcion) ? "AND descripcion ILIKE '%$descripcion%' ":"";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from):$conn->Execute($q);
		if(!$r || $r->EOF)
			return false;
			
		$collection=array();
		while(!$r->EOF){
			$ue = new caja_chica;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		return $coleccion;
	}
	
	function totalRegsBusqueda($conn, $id_ciudadano, $id_ue, $fecha_desde, $fecha_hasta, $nrodoc, $descripcion)
	{
		if(empty($id_ciudadano) && empty($id_ue) && empty($fecha_desde) && empty($fecha_hasta) && empty($nrodoc) && empty($descripcion))
			return 0;

		$q = "SELECT * FROM caja_chica ";
		$q.= "WHERE  1=1 ";
		$q.= !empty($nrodoc) ? "AND nrodoc='$nrodoc' ": "";
		$q.= !empty($fecha_desde) ? "AND fecha >='".guardafecha($fecha_desde)."' ": "";
		$q.= !empty($fecha_hasta) ? "AND fecha <='".guardafecha($fecha_hasta)."' ": "";
		$q.= !empty($id_ciudadano) ? "AND id_ciudadano = '$id_ciudadano'  ":"";
		$q.= !empty($id_ue) ? "AND id_unidad_ejecutora = '$id_ue'  ":"";
		$q.= !empty($descripcion) ? "AND descripcion ILIKE '%$descripcion%' ":"";
		//die($q);
//		echo $q;
		$r = $conn->Execute($q);
		
		return $r->RecordCount();
	}
	
	function getRelacionPartidas($conn, $id, $escEnEjec){
		$q = "SELECT puser.relacion_caja_chica.*, puser.partidas_presupuestarias.descripcion AS partida_presupuestaria,  ";
		$q.= "puser.categorias_programaticas.descripcion AS categoria_programatica ";
		$q.= "FROM puser.relacion_caja_chica  ";
		$q.= "INNER JOIN puser.partidas_presupuestarias ON (relacion_caja_chica.id_partida_presupuestaria = partidas_presupuestarias.id) ";
		$q.= "INNER JOIN puser.categorias_programaticas ON (relacion_caja_chica.id_categoria_programatica = categorias_programaticas.id) ";
		$q.= "WHERE relacion_caja_chica.id_caja_chica='$id' ";
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
				$ue->monto_exc = $r->fields['monto_exc'];
				$ue->porc_iva = $r->fields['porc_iva'];
				$coleccion[] = $ue;
			$r->movenext();
			
		}
		$json = new Services_JSON();
		return $json->encode($coleccion);
	}
	
	function addFacturas_CajaChica($conn, $id_caja, $aFacturas){
	$JsonRec = new Services_JSON();
	$JsonRec=$JsonRec->decode(str_replace("\\","",$aFacturas));
	if(is_array($JsonRec->facturas)){
		foreach ($JsonRec->facturas as $aFactAux){
			//$aFactAux_4= $aFactAux[4];
			$aFactAux_2= guardafecha($aFactAux[2]);
			$q = "INSERT INTO puser.relacion_factura_caja_chica ";
			$q.= "( id_caja_chica, nrofactura, nrocontrol, iva, monto, fecha, base_imponible, monto_iva,monto_excento,descuento) ";			
			$q.= "VALUES ";
			$q.= "('$id_caja', '$aFactAux[0]', '$aFactAux[1]', '$aFactAux[6]', '$aFactAux[3]', '$aFactAux_2', '$aFactAux[7]', '$aFactAux[8]','$aFactAux[5]','$aFactAux[4]' ) ";
				//echo($q."<br>");
				//die($q);
				$r = $conn->Execute($q);
			} 
		} 
		if($r)
			return true;
		else
			return false;
	}
	
	function getFacturas_CajaChica($conn, $id_caja){
	$q="SELECT * FROM puser.relacion_factura_caja_chica WHERE id_caja_chica = $id_caja ";
	//var_dump($q);
	$r= $conn->Execute($q);
	if($r){
		$a = $r->RecordCount();
		if ($a > 0) {
		while (!$r->EOF){
			$fc= new caja_chica;
				$fc->nfact = $r->fields['nrofactura'];
				$fc->ncontrol = $r->fields['nrocontrol'];
				$fc->fecha = muestrafecha($r->fields['fecha']);
				$fc->iva = $r->fields['iva'];
				$fc->monto_iva = $r->fields['monto_iva'];
				$fc->monto = $r->fields['monto'];
				$fc->monto_excento = $r->fields['monto_excento'];
				$fc->descuento = $r->fields['descuento'];
				$fc->base_imponible = $r->fields['base_imponible'];
				
			$coleccion[]=$fc;
			$r->movenext();
			}
		}
	}
	//die(print_r($coleccion));
	$json_fact= new Services_JSON();
	return $json_fact->encode($coleccion);	
	}
	
	function delFacturas_CajaChica($conn, $id_caja){
		$q = "DELETE FROM puser.relacion_factura_caja_chica WHERE id_caja_chica='$id_caja'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	function anular($conn, $id, $id_usuario,
										$id_unidad_ejecutora,
										$ano,
										$descripcion,
										$tipdoc,
										$fechadoc,
										$status,
										$id_ciudadano,
										$aPartidas,
										$nrodoc,
										$escEnEje){
		try {
		$q3 ="UPDATE caja_chica SET status=3 WHERE id=$id";
		//die($q3);
		$r3 = $conn->Execute($q3) or die($q3);
		
		//$nrodoc = $this->addNrodoc2($nrodoc, $tipdoc);
		
		#ESTE IF ES EN EL CASO DE QUE LA ORDEN DE COMPRA YA ESTA APROBADA#			
		//die("aqui ".$status);
		if ($r3 && $status=='2'){
			
			#DECODIFICO EL JSON Y LOS CONVIERTO EN UN ARRAY DE PHP #	
			$JsonRec = new Services_JSON();
			$JsonRec = $JsonRec->decode(str_replace("\\","",$aPartidas));
			//die(print_r($JsonRec));
			$contador = sizeof($JsonRec->cajac); 
			$nrodocanulado = $nrodoc."-ANULADO";
			//REGISTRO LA SOLICITUD EN MOVIMIENTOS PRESUPUESTARIOS//
			$q2 = "INSERT INTO puser.movimientos_presupuestarios ";
			$q2.= "(id_usuario, id_unidad_ejecutora, ano, descripcion, nrodoc, tipdoc, nroref, ";
			$q2.= "fechadoc, status, id_ciudadano, status_movimiento) ";
			$q2.= "VALUES ";
			$q2.= "('$id_usuario', '$id_unidad_ejecutora', '$ano', '$descripcion', '$nrodoc', '$tipdoc', '$nrodocanulado', ";
			$q2.= " '$fechadoc', '1', '$id_ciudadano', '2') ";
			//die($q2);
			//ESTE FOREACH SE HACE PARA PASAR LAS VARIABLES A LA CLASE MOVIMIENTOS PRESUPUESTARIOS//
			$idCat = array();
			foreach($JsonRec->cajac as $cajac){
				$monto_part = $cajac[2] * (-1);
				$aIdParCat[] = $cajac[7];
				$aCategoriaProgramatica[] = $cajac[0];
				$aPartidaPresupuestaria[] = $cajac[1];
				$aMonto[] = muestraFloat($monto_part);
				
				//ESTO SE HACE PARA SACAR EL IVA POR CATEGORIA PROGRAMATICA
				$indice = array_search($cajac[0],$idCat);
				if ($indice!==false){
					$aIva[$indice] = $aIva[$indice] + $cajac[5];
				} else {
					$idCat[] = $cajac[0];
					$aIva[] = $cajac[5];
				}
			}
			//die($montoIva);
			if(count($aIva) > 0){
				for($i=0;$i<count($aIva);$i++){
					$q = "SELECT relacion_pp_cp.id AS idparcat FROM puser.relacion_pp_cp WHERE id_categoria_programatica = '$idCat[$i]' ";
					$q.= "AND id_partida_presupuestaria = '4031801000000' ";
					$q.= "AND id_escenario = '$escEnEje'";
					$row = $conn->Execute($q);
					while(!$row->EOF){
						$idpc = $row->fields['idparcat'];
						//die("aqui: ".$idpc);
						$monto = $aIva[$i] * (-1);
						if($aIva[$i]>0){
							$aIdParCat[] 			= $idpc;        //$partidas[3];
							$aCategoriaProgramatica[] 	= $idCat[$i];
							$aPartidaPresupuestaria[] 	= '4031801000000';
							$aMonto[] 				= muestraFloat($monto);
						}
						$row->movenext();
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
						$this->msj = CC_ANULADA;
						return true;
	
					}
				}
			}else{
				$this->msj = ERROR;
				return false;
			}
		
		}elseif ($r3){
			$this->msj = CC_ANULADA;
			return true;
		
		}else{
			$this->msj = ERROR;
			return false;
			
		}
		} catch( ADODB_Exception $e ){
			//die($conn->ErrorMsg());
				$this->msj = $conn->ErrorMsg();
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
