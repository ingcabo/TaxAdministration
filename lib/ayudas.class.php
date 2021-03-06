<?
class ayudas{

	// Propiedades

	var $id;
	var $id_tipo_documento;
	var $id_unidad_ejecutora;
	var $id_proveedor;
	var $id_servicio;
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
	var $nombre_benef;
	var $cedula_benef;

	var $total;
	var $porc_iva;
	
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
	var $monto;
	var $RelacionPARCAT;

	function get($conn, $id, $escEnEje=''){
		$q = "SELECT ayudas.*, unidades_ejecutoras.descripcion AS unidad_ejecutora, ";
		$q.= "proveedores.id AS id_proveedor, proveedores.rif, proveedores.nombre AS nombre_proveedor, ";
		$q.= "proveedores.direccion AS dir_proveedor ";
		$q.= "FROM puser.ayudas ";
		$q.= "INNER JOIN puser.unidades_ejecutoras ON (ayudas.id_unidad_ejecutora = unidades_ejecutoras.id) ";
		$q.= "INNER JOIN puser.proveedores ON (ayudas.id_proveedor = proveedores.id) ";
		$q.= "WHERE ayudas.id='$id'  ";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->id_tipo_documento = $r->fields['id_tipo_documento'];
			$this->id_unidad_ejecutora = $r->fields['id_unidad_ejecutora'];
			$this->id_proveedor = $r->fields['id_proveedor'];
			$this->id_servicio = $r->fields['id_servicio'];
			$this->id_usuario = $r->fields['id_usuario'];
			$this->descripcion = $r->fields['descripcion'];
			$this->observaciones = $r->fields['observaciones'];
			$this->rif = $r->fields['rif'];
			$this->nombre_proveedor = $r->fields['nombre_proveedor'];
			$this->dir_proveedor = $r->fields['dir_proveedor'];
			$this->unidad_ejecutora = $r->fields['unidad_ejecutora'];
			$this->fecha = $r->fields['fecha'];
			$this->fecha_aprobacion = $r->fields['fecha_aprobacion'];
			$this->nrodoc = $r->fields['nrodoc'];
			$this->getRelacionPartidas($conn,$id,$escEnEje);
			$this->status = $r->fields['status'];
			$this->nombre_benef = $r->fields['nombre_benef'];
			$this->cedula_benef = $r->fields['cedula_benef'];
			return true;
		}else
			return false;
	}

	function get_all($conn, $escEnEje,$orden="id"){
		$q = "SELECT * FROM puser.ayudas ";
		$q.= "ORDER BY $orden ";
		$r = $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new contrato_servicio;
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
						$id_usuario,
						$descripcion,
						$observaciones,
						$fecha,
						$aPartidas,
						$nrodoc='',
						$auxNrodoc,
						$nombre_benef,
						$cedula_benef
						){
		//die("aqui ".$nrodoc);
		//$nrodoc = $this->addNrodoc($nrodoc, $id_tipo_documento, $id_unidad_ejecutora);
		$q = "INSERT INTO puser.ayudas ";
		$q.= "( ";
		$q.= "id_tipo_documento, ";
		$q.= "id_unidad_ejecutora, ";
		$q.= "id_proveedor, ";
		$q.= "id_usuario, ";
		$q.= "descripcion, ";
		$q.= "observaciones, ";
		$q.= "fecha, ";
		$q.= "status, ";
		$q.= "nrodoc, ";
		$q.= "nombre_benef, ";
		$q.= "cedula_benef ";
		$q.= ") ";
		$q.= "VALUES ";
		$q.= "( ";
		$q.= " '$id_tipo_documento', ";
		$q.= " '$id_unidad_ejecutora', ";
		$q.= " '$id_proveedor', ";
		$q.= " '$id_usuario', ";
		$q.= " '$descripcion', ";
		$q.= " '$observaciones', ";
		$q.= " '$fecha', ";
		$q.= "'1', ";
		$q.= " '$nrodoc', ";
		$q.= " '$nombre_benef', ";
		$q.= " '$cedula_benef' ";
		$q.= ") ";
		//die($q);
		$r = $conn->execute($q);
		if($r){
				$nrodoc = getLastId($conn, 'id', 'ayudas'); 
				if(
				$this->addRelacionPartidas($conn, 
												$nrodoc,
												$aPartidas)
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
						$id_usuario,
						$descripcion,
						$observaciones,
						$fecha,
						$aPartidas,
						$nrodoc='',
						$auxNrodoc,
						$nombre_benef,
						$cedula_benef
						){
		//$nrodoc = $this->addNrodoc2($nrodoc, $id_tipo_documento);
		$q = "UPDATE puser.ayudas SET  ";
		$q.= "id_tipo_documento = '$id_tipo_documento', ";
		$q.= "id_unidad_ejecutora = '$id_unidad_ejecutora', ";
		$q.= "id_proveedor = '$id_proveedor', ";
		$q.= "id_usuario = '$id_usuario', ";
		$q.= "descripcion = '$descripcion', ";
		$q.= "observaciones = '$observaciones', ";
		$q.= "fecha = '$fecha', ";
		$q.= "nrodoc = '$nrodoc', ";
		$q.= "nombre_benef = '$nombre_benef', ";
		$q.= "cedula_benef = '$cedula_benef' ";
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
		$q = "DELETE FROM puser.ayudas WHERE id='$id'";
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
									 $c_servicios){
		$JsonRec = new Services_JSON();
		$JsonRec=$JsonRec->decode(str_replace("\\","",$c_servicios));
		//die($JsonRec);
		if(is_array($JsonRec->servicio)){
			foreach ($JsonRec->servicio as $oCS_Aux){
				$CS_Aux_2= $oCS_Aux[2];
			$q = "INSERT INTO relacion_ayudas ";
			$q.= "( id_parcat, id_categoria_programatica, id_partida_presupuestaria, monto, id_ayuda,porc_iva,monto_exc) ";
			$q.= "VALUES ";
			$q.= "('$oCS_Aux[7]', '$oCS_Aux[0]', '$oCS_Aux[1]', '".guardaFloat($CS_Aux_2)."', '$nrodoc', '$oCS_Aux[4]', '".guardaFloat($oCS_Aux[3])."') ";
			//die($q);
			//echo($q."<br>");
			$r = $conn->Execute($q);
		} 
		if($r)
			return true;
		else
			return false;
	}
}

	function delRelacionPartidas($conn, $nrodoc){
		$q = "DELETE FROM puser.relacion_ayudas WHERE id_ayuda='$nrodoc'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	function getRelacionPartidas($conn, $id, $escEnEjec){
		$q = "SELECT relacion_ayudas.*, partidas_presupuestarias.descripcion AS partida_presupuestaria,  ";
		$q.= "categorias_programaticas.descripcion AS categoria_programatica ";
		$q.= "FROM puser.relacion_ayudas  ";
		$q.= "INNER JOIN partidas_presupuestarias ON (relacion_ayudas.id_partida_presupuestaria = partidas_presupuestarias.id) ";
		$q.= "INNER JOIN categorias_programaticas ON (relacion_ayudas.id_categoria_programatica = categorias_programaticas.id) ";
		$q.= "WHERE relacion_ayudas.id_ayuda='$id' ";
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
			$ue = new contrato_servicio;
			$ue->id_categoria_programatica = $r->fields['id_categoria_programatica'];
			$ue->categoria_programatica = $r->fields['descripcion'];
			$coleccion[] = $ue;
			$r->movenext();
		}
		return $coleccion;
	}
	
	function aprobar($conn, $id_ayuda,
										$id_usuario,
										$id_unidad_ejecutora,
										$id_proveedor,
										$ano,
										$descripcion,
										$tipdoc,
										$fechadoc,
										$status,
										$aPartidas,
										$nrodoc='',
										$auxNrodoc,
										$escEnEje
										){
									
		#OBTENGO EL NRO DE DOCUMENTOS#
		
			$nrodoc = movimientos_presupuestarios::getNroDoc($conn, $tipdoc);
		
		
		#DECODIFICO EL JSON DE LAS PARTIDAS PRESUPUESTARIAS#
		$JsonRec = new Services_JSON();
		$JsonRec = $JsonRec->decode(str_replace("\\","",$aPartidas));
		$contador = sizeof($JsonRec->servicio);
		// chequeo la disponibilidad actual en la partida, si en alguna no hay disponibilidad no se aprueba la orden
		for($i = 0; $i < $contador; $i++){
			$q = "SELECT relacion_pp_cp.disponible FROM relacion_pp_cp WHERE id = '".$JsonRec->servicio[$i][7]."'";
			//die($q);
			$r = $conn->Execute($q);
			if($r){
				if($r->fields['disponible'] < guardaFloat($JsonRec->servicio[$i][2])){
					$this->msj = ERROR_AY_APR_NO_DISP;
					return false;
				}
			}
		} 
		
		#REGISTRO EL CONTRATO DE OBRAS EN MOVIMIENTOS PRESUPUESTARIOS#	
		$q = "INSERT INTO movimientos_presupuestarios ";
		$q.= "(id_usuario, id_unidad_ejecutora, ano, descripcion, nrodoc, tipdoc,  ";//nroref, 
		$q.= "fechadoc, status, id_proveedor, status_movimiento) ";
		$q.= "VALUES ";
		$q.= "('$id_usuario', '$id_unidad_ejecutora', '$ano', '$descripcion', '$nrodoc', '$tipdoc',  ";//'$nroref', 
		$q.= " '$fechadoc', '1', '$id_proveedor', '1') ";
		//die($q);
		$r = $conn->Execute($q) or die($q);
		
		$idCat = array();
		#ESTE FOREACH SE HACE PARA PASAR LAS VARIABLES A LA FUNCION ADD_RELACION DE LA CLASE MOVIMIENTOS PRESUPUESTARIOS#
		foreach($JsonRec->servicio as $servicio){
			$aIdParCat[] 				= $servicio[7];
			$aCategoriaProgramatica[] 	= $servicio[0];
			$aPartidaPresupuestaria[] 	= $servicio[1];
			$aMonto[] 					= $servicio[2];
			
			//ESTO SE HACE PARA SACAR EL IVA POR CATEGORIA PROGRAMATICA
			$indice = array_search($servicio[0],$idCat);
			if ($indice!==false){
				$aIva[$indice] = $aIva[$indice] + $servicio[5];
			} else {
				$idCat[] = $servicio[0];
				$aIva[] = $servicio[5];
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
					$aIdParCat[] 				= $idpc;        //$partidas[3];
					$aCategoriaProgramatica[] 	= $idCat[$i];
					$aPartidaPresupuestaria[] 	= '4031801000000';
					$aMonto[] = $aIva[$i];
					$row->movenext();
				}
				
			}
		}
		
		/*print_r($aIdParCat);
		echo "<br>";
		die(print_r($aMonto));*/
		#VERIFICO SI EL CONTRATO DE OBRAS SE REGISTRO EN MOVIMIENTOS PRESUPUESTARIOS#
		if($r){
			
			#AGREGO LAS RELACIONES DEL MOVIMIENTO PRESUPUESTARIO#
			$a = movimientos_presupuestarios::add_relacion($conn,
					$aIdParCat,
					$aCategoriaProgramatica,
					$aPartidaPresupuestaria,
					$nrodoc,
					$aMonto);
			
			#ASIGNO EL NRO DE DOCUMENTO A LA ORDEN DE PAGO#		
			$c = $this->setNrodoc($conn, $nrodoc, $id_ayuda);
			
			#ACTUALIZO LA LA FECHA DE APROBACION Y EL ESTATUS DE LA ORDEN DE PAGO#
			$d = $this->setFechaAprobacion($conn, $id_ayuda);
			
			#ACTUALIZO LA TABLA RELACION PP_CP EL COMPROMISO Y EL DISPONIBLE#
			$e = relacion_pp_cp::set_desde_compromiso($conn, $aIdParCat, $aMonto);
			
			#VERIFICO SI TODOS LOS PROCESOS ANTERIORES SE EJECUTARON PARA MOSTRAR EL MENSAJE#
			//este caso se comenta porque no se esta asignando el nrodoc automaticamente 
			if($a && $c && $d && $e){
			#if($a && $d && $e){	
				$this->msj = AY_APROBADA;
				return true;
				
			}
		
		}else{
			$this->msj = ERROR;
			return false;
		}
	}

	function setNrodoc($conn, $nrodoc, $id){
		$q = "UPDATE puser.ayudas SET nrodoc = '$nrodoc' WHERE id='$id'";
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	function setFechaAprobacion($conn, $id){
		$q = "UPDATE puser.ayudas SET fecha_aprobacion = now(), status = '2' WHERE id='$id'";
		//die($q);
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
	$orden="id",$from=0, $max=0){
		if(empty($id_proveedor) 
			&& empty($id_ue)
			&& empty($fecha_desde)
			&& empty($fecha_hasta)
			&& empty($nrodoc)
			&& empty($descripcion)
			)
			return false;
		$q = "SELECT * FROM puser.ayudas ";
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
			$ue = new ayudas;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		return $coleccion;
	}
	
	#PARA ANULAR EL DOCUMENTO DE AYUDA
	
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
										$escEnEje){
		try {
		
		$q3 ="UPDATE puser.ayudas SET status=3 WHERE id=$id";

		$r3 = $conn->Execute($q3) or die($q3);
		//$r3=1;
		#MIENTRAS SE CARGUE EL NUMERO DE DOCUMENTO DE MANERA MANUAL
		
		/*if(!$auxNrodoc)
			$nrodoc = $this->addNrodoc2($nrodoc, $tipdoc);*/
		
		#ESTE IF ES EN EL CASO DE QUE LA ORDEN DE COMPRA YA ESTA APROBADA#			
		//
		if ($r3 && $status=='2'){
			
			#DECODIFICO EL JSON Y LOS CONVIERTO EN UN ARRAY DE PHP #	
			$JsonRec = new Services_JSON();
			$JsonRec = $JsonRec->decode(str_replace("\\","",$aPartidas));
			//die(print_r($JsonRec));
			$contador = sizeof($JsonRec->servicio); 
			$nrodocanulado = $nrodoc."-ANULADO";
			//REGISTRO LA SOLICITUD EN MOVIMIENTOS PRESUPUESTARIOS//
			$q2 = "INSERT INTO puser.movimientos_presupuestarios ";
			$q2.= "(id_usuario, id_unidad_ejecutora, ano, descripcion, nrodoc, tipdoc, nroref, ";
			$q2.= "fechadoc, status, id_proveedor, status_movimiento) ";
			$q2.= "VALUES ";
			$q2.= "('$id_usuario', '$id_unidad_ejecutora', '$ano', '$descripcion', '$nrodoc', '$tipdoc', '$nrodocanulado', ";
			$q2.= " '$fechadoc', '1', '$id_proveedor', '2') ";
			//die($q2);
			$idCat = array();
			//ESTE FOREACH SE HACE PARA PASAR LAS VARIABLES A LA CLASE MOVIMIENTOS PRESUPUESTARIOS//
			foreach($JsonRec->servicio as $servicio){
				$guarda_monto = guardaFloat($servicio[2]) * (-1);
				$aIdParCat[] = $servicio[7];
				$aCategoriaProgramatica[] = $servicio[0];
				$aPartidaPresupuestaria[] = $servicio[1];
				$aMonto[] = $guarda_monto;
				
				//ESTO SE HACE PARA SACAR EL IVA POR CATEGORIA PROGRAMATICA
				$indice = array_search($servicio[0],$idCat);
				if ($indice!==false){
					$aIva[$indice] = $aIva[$indice] + $servicio[5];
				} else {
					$idCat[] = $servicio[0];
					$aIva[] = guardaFloat($servicio[5]);
				}
			}
			
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
						$aIdParCat[] 				= $idpc;        //$partidas[3];
						$aCategoriaProgramatica[] 	= $idCat[$i];
						$aPartidaPresupuestaria[] 	= '4031801000000';
						$aMonto[] = $monto;
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
						$this->msj = AY_ANULADA;
						return true;
	
					}
				}
			}else{
				$this->msj = ERROR;
				return false;
			}
		
		}elseif ($r3){
			$this->msj = AY_ANULADA;
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
	
	function totalRegistroBusqueda($conn,$id_proveedor,$id_ue,$fecha_desde,$fecha_hasta,$nrodoc,$descripcion,$orden="id"){
		if(empty($id_proveedor) && empty($id_ue) && empty($fecha_desde) && empty($fecha_hasta) && empty($nrodoc) && empty($descripcion))
			return 0;
		$q = "SELECT * FROM puser.ayudas ";
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

}
?>