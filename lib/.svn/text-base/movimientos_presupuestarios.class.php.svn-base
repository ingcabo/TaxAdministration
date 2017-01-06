<?
class movimientos_presupuestarios{

	// Propiedades

	var $id;
	var $id_usuario;
	var $id_unidad_ejecutora;
	var $usuario;
	var $unidad_ejecutora;
	var $ano;
	var $descripcion;
	var $nrodoc;
	var $tipdoc;
	var $tipo_documento;
	var $tipref;
	var $nroref;
	var $fecharef;
	var $imppre;
	var $fecha;
	var $status; // esta relacionado con momentos_presupuestarios.id
	var $momento;
	var $id_proveedor;
	var $proveedor;
	var $rif;
	var $fecha_aprobacion;
	
	var $compromiso;
	var $causado;
	var $pagado;
	var $aumentos;
	var $disminuciones;

	var $total;

	/*********************
			Objeto Relacion
	*********************/
	var $relacion; // almacena un array de objetos de relaciones de obras
	
	// Propiedades utilizadas por el objeto con relaciones de las obras
	var $id_categoria_programatica;
	var $id_partida_presupuestaria;
	var $categoria_programatica;
	var $partida_presupuestaria;
	var $monto;

	function get($conn, $id, $id_momento){
		$q = "SELECT mp.*, td.descripcion AS tipo_documento, p.rif, p.nombre AS proveedor, ci.nombre AS ciudadano, ";
		$q.= "momentos.descripcion AS momento, ue.descripcion AS unidad_ejecutora, ri.tipo_contribuyente, ";
		$q.= "ri.ingreso_periodo_fiscal ";
		$q.= "FROM movimientos_presupuestarios mp ";
		$q.= "INNER JOIN puser.tipos_documentos td ON (mp.tipdoc = td.id) ";
		$q.= "INNER JOIN puser.momentos_presupuestarios momentos ON (mp.status = momentos.id) ";
		$q.= "INNER JOIN puser.unidades_ejecutoras ue ON (mp.id_unidad_ejecutora = ue.id) ";
		$q.= "LEFT JOIN puser.proveedores p ON (mp.id_proveedor = p.id) ";
		$q.= "LEFT JOIN puser.ciudadanos ci ON (mp.id_ciudadano = ci.id) ";
		$q.= "LEFT JOIN puser.retencion_iva ri ON (ri.id_proveedor = p.id) ";
		$q.= "WHERE mp.nrodoc='$id' ";
		$q.= "AND mp.status=$id_momento";
		//die($q);
		//echo $q;
		if(!$r = $conn->Execute($q))
			return false;
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->id_usuario = $r->fields['id_usuario'];
			$this->id_unidad_ejecutora = $r->fields['id_unidad_ejecutora'];
			$this->usuario = $r->fields['usuario'];
			$this->unidad_ejecutora = $r->fields['unidad_ejecutora'];
			$this->ano = $r->fields['ano'];
			$this->descripcion = $r->fields['descripcion'];
			$this->nrodoc = $r->fields['nrodoc'];
			$this->tipdoc = $r->fields['tipdoc'];
			$this->tipo_documento = $r->fields['tipo_documento'];
			$this->tipo_contribuyente = $r->fields['tipo_contribuyente'];
			if(empty($r->fields['ingreso_periodo_fiscal']))
				$this->ingreso_periodo_fiscal = 0;
			else
				$this->ingreso_periodo_fiscal = $r->fields['ingreso_periodo_fiscal'];
			//die($r->fields['ingreso_periodo_fiscal']);
			$this->tipref = $r->fields['tipref'];
			$tdr = new tipos_documentos;
			$tdr->get($conn, $r->fields['tipref']);
			$this->tipo_documento_ref = $tdr;
			$this->nroref = $r->fields['nroref'];
			$this->documento = ($id_momento=='1' || $id_momento=='4' || $id_momento=='5')? $r->fields['nrodoc'] : $r->fields['nroref'] ;
			$this->fecharef = $this->get_fecha($conn, $r->fields['nroref']);
			$this->imppre = $r->fields['imppre'];
			$this->fecha = $r->fields['fechadoc'];
			$this->status = $r->fields['status'];
			$this->momento = $r->fields['momento'];
			if(!empty($r->fields['id_proveedor'])){
				$this->id_proveedor = $r->fields['id_proveedor'];
				$this->proveedor = $r->fields['proveedor'];
			} else {
				$this->id_proveedor = $r->fields['id_ciudadano'];
				$this->proveedor = $r->fields['ciudadano'];
			}
			//$this->proveedor = $r->fields['proveedor'];
			$this->rif = $r->fields['rif'];
			switch($r->fields['status']){
				case 1:
					$this->compromiso = $this->get_suma_monto($conn, $r->fields['nrodoc']);
				break;
				case 2:
					$this->causado = $this->get_suma_monto($conn, $r->fields['nrodoc']);
					// obtengo el nro de doc de quien hace referencia a el
					$nroDocComprometido = $this->get_nroref($conn, $r->fields['nrodoc']);
					// guardo en comprometido la suma del monto del doc que lo referencia
					$this->compromiso = $this->get_suma_monto($conn, $nroDocComprometido);
				break;
				case 3:
					$this->pagado = $this->get_suma_monto($conn, $r->fields['nrodoc']);
					// obtengo el nro de doc de quien hace referencia a el
					$nroDocCausado = $this->get_nroref($conn, $r->fields['nrodoc']);
					// guardo en causado la suma del monto del doc que lo referencia
					$this->causado = $this->get_suma_monto($conn, $nroDocCausado);
					// obtengo el nro de doc de quien hace referencia a el
					$nroDocComprometido = $this->get_nroref($conn, $nroDocCausado);
					// guardo en comprometido la suma del monto del doc que lo referencia
					$this->compromiso = $this->get_suma_monto($conn, $nroDocComprometido);
				break;
				case 4:
					$this->aumentos = $this->get_suma_monto($conn, $r->fields['nrodoc']);
					// si el documento es referenciado obtengo el nro de doc de quien hace referencia a el
					$nroDoc = $this->_get_nrodoc($conn, $r->fields['nrodoc']);
					// guardo en disminuciones la suma del monto del doc que lo referencia
					$this->disminuciones = $this->get_suma_monto($conn, $nroDoc);
				break;
				case 5:
					$this->disminuciones = $this->get_suma_monto($conn, $r->fields['nrodoc']);
					$this->aumentos = $this->get_suma_monto($conn, $r->fields['nroref']);
				break;
			}
			return true;
		}else
			return false;
	}

	function get_all($conn, $from=0, $max=0,$orden="fechadoc, id"){
		$q = "SELECT * FROM movimientos_presupuestarios ";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new movimientos_presupuestarios;
			$ue->get($conn, $r->fields['nrodoc']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, 
							$id_usuario,
							$id_unidad_ejecutora,
							$ano,
							$descripcion,
							$nrodoc,
							$tipdoc,
							$tipref,
							$nroref,
							$fechadoc,
							$fecharef,
							$status,
							$id_proveedor,
							$aPartidas){
		
		//DECODIFICO EL JSON DE LAS PARTIDAS PRESUPUESTARIAS					
		$JsonRec = new Services_JSON();
		$JsonRec = $JsonRec->decode(str_replace("\\","",$aPartidas));
		$contador = sizeof($JsonRec->partidaspresupuestarias); 
		
		if ($status=='2' || $status=='3'){
		
			$nrodoc2 = $nroref;
			$nroref2 = $nrodoc;
		
		}else{
		
			$nrodoc2 = $nrodoc;
			$nroref2 = $nroref;
		
		}
							
		$q = "INSERT INTO movimientos_presupuestarios ";
		$q.= "(id_usuario, id_unidad_ejecutora, ano, descripcion, nrodoc, tipdoc, tipref, nroref, ";
		$q.= "fechadoc, fecharef, status";
		$q.= !empty($id_proveedor) ? ", id_proveedor) " : ") ";
		$q.= "VALUES ";
		$q.= "('$id_usuario', '$id_unidad_ejecutora', '$ano', '$descripcion', '$nrodoc2', '$tipdoc', '$tipref', '$nroref2', ";
		$q.= " '".guardafecha($fechadoc)."', '$fecharef', '$status'";
		$q.= !empty($id_proveedor) ? ", '$id_proveedor') " : ") ";
		//echo($q."<br/>");
		//die($q);
		//ESTE FOREACH SE HACE PARA PASAR LAS VARIABLES A LA CLASE MOVIMIENTOS PRESUPUESTARIOS//
		foreach($JsonRec->partidaspresupuestarias as $partidas){
		
			$aIdParCat[] = $partidas[3];
			$aCategoriaProgramatica[] = $partidas[0];
			$aPartidaPresupuestaria[] = $partidas[1];
			$aMonto[] = $partidas[2];
		}
			
		$r = $conn->Execute($q);
		//$r = true;
		if($r){
				if($this->add_relacion($conn, 
										$aIdParCat,
										$aCategoriaProgramatica,
										$aPartidaPresupuestaria,
										$nrodoc,
										$aMonto)){
					return true;
				}
		}else
			return false;
	}

	function set($conn, 
							$id_unidad_ejecutora,
							$ano,
							$descripcion,
							$nrodoc,
							$tipdoc,
							$tipref,
							$nroref,
							$fecha,
							$status,
							$id_proveedor,
							$fecha_aprobacion,
							$idParCat,
							$aCategoriaProgramatica,
							$aPartidaPresupuestaria,
							$aMonto){
		$q = "UPDATE movimientos_presupuestarios SET id_unidad_ejecutora = '$id_unidad_ejecutora', ";
		$q.= "ano='$ano,' ";
		$q.= "descripcion='$descripcion', ";
		$q.= "tipdoc='$tipdoc', ";
		$q.= "tipref='$tipref', ";
		$q.= "nroref='$nroref', ";
		$q.= "fecha='$fecha', ";
		$q.= "status='$status', ";
		$q.= "id_proveedor='$id_proveedor', ";
		$q.= "fecha_aprobacion='$fecha_aprobacion' ";
		$q.= "WHERE nrodoc='$nrodoc' ";	
		//die($q);
		if($conn->Execute($q)){
			$this->del_relacion($conn, $nrodoc);
			if($this->add_relacion($conn, 
												$idParCat,
												$aCategoriaProgramatica,
												$aPartidaPresupuestaria,
												$nrodoc,
												$aMonto))
				return true;
		}else
			return false;
	}

	function del($conn, $id){
		$q = "DELETE FROM movimientos_presupuestarios WHERE nrodoc='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function get_fecha($conn, $nrodoc){
		$q = "SELECT fechadoc FROM puser.movimientos_presupuestarios WHERE nrodoc='$nrodoc'";
		$r = $conn->Execute($q);
		if($r)
			return $r->fields['fechadoc'];
		else
			return false;
	}

	function add_relacion($conn,$idParCat,$aCategoriaProgramatica,$aPartidaPresupuestaria,$nrodoc,$aMonto){
		
		for($i = 0; $i<count($aCategoriaProgramatica); $i++){
			$q = "INSERT INTO puser.relacion_movimientos ";
			$q.= "( id_parcat, id_categoria_programatica, id_partida_presupuestaria, nrodoc, monto) ";
			$q.= "VALUES ";
			$q.= "('$idParCat[$i]', '$aCategoriaProgramatica[$i]', '$aPartidaPresupuestaria[$i]', '$nrodoc', ".guardafloat($aMonto[$i]).") ";
		//	die($q);
			//echo $q."<br>";
			$r = $conn->Execute($q) or die($q);
		} 
		if($r)
			return true;
		else
			return false;
	}
	function add_relacion_nomina($conn,$idParCat,$aCategoriaProgramatica,$aPartidaPresupuestaria,$nrodoc,$aMonto){

		$q = "INSERT INTO relacion_movimientos ";
		$q.= "( id_parcat, id_categoria_programatica, id_partida_presupuestaria, nrodoc, monto) ";
		$q.= "VALUES ";
		$q.= "('$idParCat', '$aCategoriaProgramatica', '$aPartidaPresupuestaria', '$nrodoc', $aMonto) ";
		//die($q);
		$r = $conn->Execute($q) or die($q);
		if($r)
			return true;
		else
			return false;
	}

	function del_relacion($conn, $nrodoc){
		$q = "DELETE FROM relacion_movimientos WHERE nrodoc='$nrodoc'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	function del_relacion_productos($conn, $nrodoc){
		$q = "DELETE FROM relacion_movimientos_productos WHERE nrodoc='$nrodoc'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	function get_relaciones($conn, $id, $escEnEjec, $status=''){
		$q = "SELECT relacion_movimientos.*, partidas_presupuestarias.descripcion AS partida_presupuestaria,  ";
		$q.= "categorias_programaticas.descripcion AS categoria_programatica ";
		$q.= "FROM relacion_movimientos  ";
		$q.= "INNER JOIN partidas_presupuestarias ON (relacion_movimientos.id_partida_presupuestaria = partidas_presupuestarias.id) ";
		$q.= "INNER JOIN categorias_programaticas ON (relacion_movimientos.id_categoria_programatica = categorias_programaticas.id) ";
		$q.= "WHERE relacion_movimientos.nrodoc='$id' ";
		$q.= "AND categorias_programaticas.id_escenario = '$escEnEjec' ";
		$q.= "AND partidas_presupuestarias.id_escenario = '$escEnEjec' ";
		//die($q);
		$tipdoc = explode("-",$id);
		if ($tipdoc[0]=='014'){
		
			$q2 = "SELECT * FROM movimientos_presupuestarios WHERE nroref='$id'";
			//die($q2);
			$r2 = $conn->execute($q2);
			$numdoc = $r2->fields['nrodoc'];
			
		
		}
		//die($q);
		$r = $conn->Execute($q);
		while(!$r->EOF){
			$ue = new movimientos_presupuestarios;
			$ue->nrodoc = $r->fields['nrodoc'];
			$ue->nroref = $r->fields['nroref'];
			$ue->idParCat	= $r->fields['id_parcat'];
			$ue->id_partida_presupuestaria	= $r->fields['id_partida_presupuestaria'];
			$ue->id_categoria_programatica = $r->fields['id_categoria_programatica'];
			$ue->partida_presupuestaria	= $r->fields['partida_presupuestaria'];
			$ue->categoria_programatica = $r->fields['categoria_programatica'];
			$ue->monto = $r->fields['monto'];
			//$ue->causado_ant = $ue->get_causado($conn,$id,$r->fields['id_parcat']);
			$ue->get_suma_monto($conn, $id);
			
			$cNrodocReferencia = $ue->getdocstatus($conn, $id ,2);
			//print_r($cNrodocReferencia);
			//die("hola");
			$montoReferencia = 0;
			if(is_array($cNrodocReferencia)){
	
				foreach($cNrodocReferencia as $nrodocRef){
					
					$montoReferencia += movimientos_presupuestarios::get_monto($conn,	
		 														$nrodocRef, 
																$r->fields['id_categoria_programatica'],
																$r->fields['id_partida_presupuestaria']);
			
				}
			}
			//die("aqui ".$montoReferencia);
			$ue->compromiso = $ue->monto_total_documento($conn, $numdoc);
			$ue->causados 	= $ue->monto_total_documento($conn, $id);
			$ue->comprometido += $r->fields['monto'];
			$ue->causado += $montoReferencia;
			$ue->montoporcausar = $r->fields['monto'] - $montoReferencia;
						
			$coleccion[] = $ue;
			$r->movenext();
		}
		return $coleccion;
	}
	
	function monto_total_documento($conn, $nrodoc){
	
		$q = "SELECT sum(monto) as total_doc FROM relacion_movimientos WHERE nrodoc='$nrodoc'";
		//die($q);
		$r = $conn->execute($q);
		return $r->fields['total_doc'];
	
	}

	function get_all_by_ue_status_prov_tipref($conn, $ue, $status, $id_proveedor, $tipref){
		$q.= "SELECT * ";
		$q.= "FROM movimientos_presupuestarios ";
		$q.= "WHERE status = '$status' ";
		$q.= "AND id_proveedor = '$id_proveedor' ";
		$q.= "AND id_unidad_ejecutora = '$ue' ";
		$q.= "AND tipdoc = '$tipref' ";
		$q.= "AND status_movimiento='1'";
		//die($q);
		$r = $conn->Execute($q);
		while(!$r->EOF){
			$ue = new movimientos_presupuestarios;
			$ue->nrodoc = $r->fields['nrodoc'];
			$ue->nroref = $r->fields['nroref'];
			$ue->descripcion = $r->fields['descripcion'];
			$coleccion[] = $ue;
			$r->movenext();
		}
		return $coleccion;
	}

	function get_suma_monto($conn, $nrodoc){
		$q = "SELECT sum(monto) AS monto FROM relacion_movimientos WHERE nrodoc='$nrodoc'";
	
		$r = $conn->Execute($q);
		
		if($r)
			return $r->fields['monto'];
		else
			return false;
	}

	function get_nroref($conn, $nrodoc){
		$q = "SELECT nroref FROM movimientos_presupuestarios WHERE nrodoc='$nrodoc'";
		//echo $q."<br>";
		$r = $conn->Execute($q);
		if($r)
			return $r->fields['nroref'];
		else
			return false;
	}
	
	function getdocstatus($conn, $nrodoc, $status){
		$q = "SELECT nrodoc FROM puser.movimientos_presupuestarios WHERE status='$status' AND nroref='$nrodoc'";
		//die($q);
		$r = $conn->Execute($q);
		while(!$r->EOF){
			$coleccion[] = $r->fields['nrodoc'];
			$r->movenext();
		}
			return $coleccion;
	}
	
	function getdocstatus2($conn, $nrodoc, $status){
		$q = "SELECT nroref FROM puser.movimientos_presupuestarios WHERE status='$status' AND nrodoc='$nrodoc'";
		//die($q);
		$r = $conn->Execute($q);
		while(!$r->EOF){
			$coleccion[] = $r->fields['nroref'];
			$r->movenext();
		}
			return $coleccion;
	}
	
	function get_nrodoc($conn, $nroref){
		$q = "SELECT nroref FROM movimientos_presupuestarios WHERE nroref='$nroref'";
		//die($q);
		//echo $q."<br>";
		$r = $conn->Execute($q);
		while(!$r->EOF){
			$coleccion[] = $r->fields['nrodoc'];
			$r->movenext();
		}
			return $coleccion;
	}
	
	function _get_nrodoc($conn, $nroref){
		$q = "SELECT nrodoc FROM movimientos_presupuestarios WHERE nroref='$nroref'";
		//echo $q."<br>";
		$r = $conn->Execute($q);
		if($r)
			return $r->fields['nrodoc'];
		else
			return false;
	}
	
	function get_monto($conn, $nrodoc, $id_categoria, $id_partida){
		$q = "SELECT sum(monto) AS monto FROM relacion_movimientos WHERE nrodoc='$nrodoc' ";
		$q.= "AND id_categoria_programatica = '$id_categoria' AND id_partida_presupuestaria = '$id_partida' ";
		//die($q);
		//echo $q."<br>";
		$r = $conn->Execute($q);
		if($r)
			return $r->fields['monto'];
		else
			return false;
	}

	function get_all_aumentos($conn, $from=0, $max=0,$orden="fechadoc, id"){
		$q = "SELECT nrodoc FROM movimientos_presupuestarios WHERE status = '4' ";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new movimientos_presupuestarios;
			$ue->get($conn, $r->fields['nrodoc'],4);
			$coleccion[] = $ue;
			$r->movenext();
		}
		return $coleccion;
	}

	// devuelve true si un documento (aumento) es referenciado por otro (disminucion)
	function has_referencia($conn, $nrodoc){
		$q = "SELECT nrodoc FROM movimientos_presupuestarios WHERE nroref='$nrodoc' AND status=5 ";
		//echo $q."<br>";
		$r = $conn->Execute($q);
		if($r->fields['nrodoc'])
			return true;
		else
			return false;
	}
	
	function buscar($conn, 
							$fecha_desde, 
							$fecha_hasta, 
							$tipdoc, 
							$tipmov, 
							$nrodoc, 
							$descripcion,
							$max=10, 
							$from=1){
		if(empty($tipdoc) 
		&& empty($descripcion)
		&& empty($tipmov)
		&& empty($nrodoc)
		&& empty($fecha_desde)
		&& empty($fecha_hasta) )
			return false;
		$q = "SELECT nrodoc, status FROM movimientos_presupuestarios WHERE 1=1 ";
		$q.= !empty($descripcion) ? "AND descripcion ILIKE '%$descripcion%' ": "";
		$q.= !empty($nrodoc) ? "AND nrodoc='$nrodoc' ": "";
		$q.= !empty($fecha_desde) ? "AND fechadoc >='".guardafecha($fecha_desde)."' ": "";
		$q.= !empty($fecha_hasta) ? "AND fechadoc <='".guardafecha($fecha_hasta)."' ": "";
		$q.= !empty($tipdoc) ? "AND tipdoc='$tipdoc' ": "";
		$q.= !empty($tipmov) ? "AND status='$tipmov' ": "";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		if(!$r)
			return false;
		$collection=array();
		while(!$r->EOF){
			$ue = new movimientos_presupuestarios;
			$ue->get($conn, $r->fields['nrodoc'], $r->fields['status']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		return $coleccion;
	}

	function total_registro_busqueda($conn, 
							$fecha_desde, 
							$fecha_hasta, 
							$tipdoc, 
							$tipmov, 
							$nrodoc, 
							$descripcion){
		if(empty($tipdoc) 
		&& empty($descripcion)
		&& empty($tipmov)
		&& empty($nrodoc)
		&& empty($fecha_desde)
		&& empty($fecha_hasta) )
			return false;
		$q = "SELECT count(nrodoc) AS total FROM movimientos_presupuestarios WHERE 1=1 ";
		$q.= !empty($descripcion) ? "AND descripcion ILIKE '%$descripcion%' ": "";
		$q.= !empty($nrodoc) ? "AND nrodoc='$nrodoc' ": "";
		$q.= !empty($fecha_desde) ? "AND fechadoc >='".guardafecha($fecha_desde)."' ": "";
		$q.= !empty($fecha_hasta) ? "AND fechadoc <='".guardafecha($fecha_hasta)."' ": "";
		$q.= !empty($tipdoc) ? "AND tipdoc='$tipdoc' ": "";
		$q.= !empty($tipmov) ? "AND status='$tipmov' ": "";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r)
			return false;
		if(!$r->EOF){
			return $r->fields['total'];
		}
	}

	function addRelacionProductos($conn, 
																	 $nrodoc,
																	 $idProducto,
																	 $cantidad,
																	 $precioBase,
																	 $precioIva,
																	 $precioTotal){
		for($i = 0; $i<count($idProducto); $i++){
			$q = "INSERT INTO  relacion_movimientos_productos ";
			$q.= "( nrodoc, id_producto, cantidad, precio_base, precio_iva, precio_total) ";
			$q.= "VALUES ";
			$q.= "('$nrodoc', '$idProducto[$i]', '$cantidad[$i]', ".guardafloat($precioBase[$i]).", ".guardafloat($precioIva[$i]).", ".guardafloat($precioTotal[$i]).") ";
			//echo($q."<br>");
			$r = $conn->Execute($q) or die($q);
		} 
		if($r)
			return true;
		else
			return false;
	}
	
	function getNroDoc($conn, $tipdoc){
		$q = "SELECT max(nrodoc) AS nrodoc FROM movimientos_presupuestarios WHERE tipdoc = '$tipdoc' ";
		$r = $conn->execute($q);
		//die($r->fields['nrodoc']);
		return $tipdoc."-".str_pad(substr($r->fields['nrodoc'], 4, 4) + 1, 4, 0, STR_PAD_LEFT);
	}
	
	function getMovimientosStatus($conn, $status, $auxNrodoc="", $nrodoc_busqueda="", $id_ue="", $tipdoc=""){
		$st = $status - 1;
		$q = "SELECT nrodoc, descripcion, sum(monto) AS monto ";
		$q.= "FROM  ";
		$q.= "movimientos_presupuestarios mp ";
		$q.= "LEFT JOIN relacion_movimientos rm USING (nrodoc) ";
		$q.= "WHERE mp.status='$st' ";
		$q.= "AND NOT EXISTS (SELECT id FROM puser.movimientos_presupuestarios WHERE nrodoc=mp.nrodoc AND status_movimiento=2) ";
		if(!empty($nrodoc_busqueda)) $q.= "AND nrodoc = '$nrodoc_busqueda' ";
		if(!empty($id_ue)) $q.= "AND id_unidad_ejecutora = '$id_ue' ";
		if(!empty($tipdoc)) $q.= "AND tipdoc = '$tipdoc' ";
		$q.= "GROUP BY mp.nrodoc, mp.descripcion ";
		$q.= "ORDER BY mp.nrodoc, mp.descripcion ";
		//die($q);
		//echo "primera ".$q."<br>";
		$i=1;
		$r = $conn->Execute($q);
		while(!$r->EOF){
			$nrodoc = $r->fields['nrodoc'];
			$descripcion = $r->fields['descripcion'];
			$montocompromiso = $r->fields['monto'];
			/*$q = "SELECT sum(monto) AS monto ";
			$q.= "FROM  ";
			$q.= "movimientos_presupuestarios mp ";
			$q.= "INNER JOIN relacion_movimientos rm USING (nrodoc) ";
			$q.= "WHERE mp.status=$status ";
			$q.= "AND mp.nrodoc='$nrodoc' ";
			$q.= "GROUP BY mp.nrodoc ";*/
			$q = "SELECT COALESCE(SUM(rop.monto)::numeric,0)::float8  AS monto, op.nrodoc AS nrocausado, sp.nrodoc AS nrosoli, mp.nrodoc AS nrocompromiso FROM finanzas.relacion_orden_pago rop ";
			$q.= "INNER JOIN finanzas.orden_pago op ON (rop.id_orden_pago = op.nrodoc) ";
			$q.= "LEFT JOIN finanzas.solicitud_pago sp ON (op.nroref = sp.nrodoc) ";
			$q.= "LEFT JOIN puser.movimientos_presupuestarios mp ON (sp.nroref = mp.nrodoc) ";
			$q.= "WHERE mp.nrodoc = '$nrodoc' AND mp.status = '$st' AND op.status <> '3' ";
			$q.= "GROUP BY 2,3,4 ";
			//echo $q."<br>";
			//die($q);
			$r2 = $conn->Execute($q);
			$montocausado = 0;
			while(!$r2->EOF){
				$montocausado+= $r2->fields['monto'];
				$r2->movenext();
			}
				
			//	echo("causado=".$montocausado."-Compromiso=".$montocompromiso."<br>");
			//$nrodoc2 = $this->showNrodoc($nrodoc)
			//esto es en el caso de que el usuario cargue el numero de documento
			//if(!$auxNrodoc)
				$aux = explode("-", $nrodoc);
			$td = new tipos_documentos;
			$td->get($conn,$aux[0]); 	
			if(empty($montocausado))
				$montocausado = 0;//die("epa");
			if($montocausado < $montocompromiso){
				$o = new movimientos_presupuestarios;
				$o->id = $nrodoc;
				if(!$auxNrodoc){
					$id_aux = $aux[1]."-".$aux[2];
					$o->id2 = $id_aux;
					$o->descripcion	= $id_aux." - ".$td->descripcion." - ".$descripcion;
				} else {
					$o->id2 = $nrodoc;
					$o->descripcion	= $nrodoc." - ".$td->descripcion." - ".$descripcion;
				}
				$coleccion[] = $o;
				$i++;
			}
			$r->movenext();
		}
		//print_r($coleccion);
		//die("stop");
		return $coleccion;
	}
	
	function getMovimientosStatusPorTipDoc($conn, $status, $tipdoc){
		$st = $status - 1;
		$q = "SELECT nrodoc, descripcion, sum(monto) AS monto ";
		$q.= "FROM  ";
		$q.= "movimientos_presupuestarios mp ";
		$q.= "LEFT JOIN relacion_movimientos rm USING (nrodoc) ";
		$q.= "WHERE mp.status='$st' AND mp.tipdoc='$tipdoc'";
		$q.= "GROUP BY mp.nrodoc, mp.descripcion ";
		$q.= "ORDER BY mp.nrodoc, mp.descripcion ";
		//die($q);
		$i=1;
		$r = $conn->Execute($q);
		while(!$r->EOF){
			$nrodoc = $r->fields['nrodoc'];
			$descripcion = $r->fields['descripcion'];
			$montocompromiso = $r->fields['monto'];
			$q = "SELECT sum(monto) AS monto ";
			$q.= "FROM  ";
			$q.= "movimientos_presupuestarios mp ";
			$q.= "INNER JOIN relacion_movimientos rm USING (nrodoc) ";
			$q.= "WHERE mp.status=$status ";
			$q.= "AND mp.nrodoc='$nrodoc' ";
			$q.= "GROUP BY mp.nrodoc ";
			//die($q);
			$r2 = $conn->Execute($q);
			$montocausado = $r2->fields['monto'];
			//	echo("causado=".$montocausado."-Compromiso=".$montocompromiso."<br>");
			if(empty($montocausado))
				$montocausado = 0;//die("epa");
			if($montocausado < $montocompromiso){
				$o = new movimientos_presupuestarios;
				$o->id = $nrodoc;
				$o->descripcion	= $nrodoc." - ".$descripcion;
				$coleccion[] = $o;
				$i++;
			}
			$r->movenext();
		}
		//print_r($coleccion);
		//die("stop");
		return $coleccion;
	}
	
	function update_status_solicitud($conn,$nrodoc, $status){
	
		$q = "UPDATE movimientos_presupuestarios set status='$status' WHERE nrodoc = '$nrodoc'";
		//die($q);
		if ($conn->Execute($q)){
			
			return true;
			
		}else{
		
			return false;
			
		}
		
	}
	
	#ESTA FUNCION TRAE LOS MOVIMIENTOS PRESUPUESTARIOS POR PROVEEDOR Y TIPO DE DOCUMENTO#
	function GetMov($conn, $id_proveedor,$tipdoc){
	
		$q = 	"SELECT * FROM puser.movimientos_presupuestarios 
				Inner Join finanzas.solicitud_pago ON solicitud_pago.nrodoc = movimientos_presupuestarios.nrodoc
				WHERE id_proveedor='$id_proveedor' AND tipdoc='$tipdoc' AND solicitud_pago.status='2'";
		//die($q);
		$r = $conn->Execute($q);
		
		while (!$r->EOF){
		
			//$ue = new movimientos_presupuestarios;
			
			$ue = new movimientos_presupuestarios;
			$ue->nrodoc = $r->fields['nrodoc'];
			$ue->descripcion = $r->fields['descripcion'];
			$coleccion[] = $ue;
			$r->movenext();
		}
		
		return $coleccion;
	
	}
	
	function getNroRef($conn, $id, $id_momento){
		$q = "SELECT DISTINCT mp.*, td.descripcion AS tipo_documento, p.rif, p.nombre AS proveedor, c.nombre AS ciudadano, ";
		$q.= "momentos.descripcion AS momento, ue.descripcion AS unidad_ejecutora, ri.tipo_contribuyente, ";
		$q.= "ri.ingreso_periodo_fiscal ";
		$q.= "FROM movimientos_presupuestarios mp ";
		$q.= "INNER JOIN tipos_documentos td ON (mp.tipdoc = td.id) ";
		$q.= "INNER JOIN momentos_presupuestarios momentos ON (mp.status = momentos.id) ";
		$q.= "INNER JOIN unidades_ejecutoras ue ON (mp.id_unidad_ejecutora = ue.id) ";
		$q.= "LEFT JOIN proveedores p ON (mp.id_proveedor = p.id) ";
		$q.= "LEFT JOIN ciudadanos c ON (mp.id_proveedor = c.id) ";
		$q.= "LEFT JOIN puser.retencion_iva ri ON (ri.id_proveedor = p.id) ";
		$q.= "WHERE mp.nrodoc='$id' ";
		$q.= "AND mp.status=$id_momento";
		//REVISAR CON TODOS LOS DOCUMENTOS SI EL CAMBIO NO AFECTA
		//die($q);
		if(!$r = $conn->Execute($q))
			return false;
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->id_usuario = $r->fields['id_usuario'];
			$this->id_unidad_ejecutora = $r->fields['id_unidad_ejecutora'];
			$this->usuario = $r->fields['usuario'];
			$this->unidad_ejecutora = $r->fields['unidad_ejecutora'];
			$this->ano = $r->fields['ano'];
			$this->descripcion = $r->fields['descripcion'];
			$this->nrodoc = $r->fields['nrodoc'];
			$this->tipdoc = $r->fields['tipdoc'];
			$this->tipo_documento = $r->fields['tipo_documento'];
			$this->tipo_contribuyente = $r->fields['tipo_contribuyente'];
			$this->ingreso_periodo_fiscal = $r->fields['ingreso_periodo_fiscal'];
			$this->tipref = $r->fields['tipref'];
			$tdr = new tipos_documentos;
			$tdr->get($conn, $r->fields['tipref']);
			$this->tipo_documento_ref = $tdr;
			$this->nroref = $r->fields['nroref'];
			$this->documento = ($id_momento=='1' || $id_momento=='4' || $id_momento=='5')? $r->fields['nrodoc'] : $r->fields['nroref'] ;
			$this->fecharef = $this->get_fecha($conn, $r->fields['nroref']);
			$this->imppre = $r->fields['imppre'];
			$this->fecha = $r->fields['fechadoc'];
			$this->status = $r->fields['status'];
			$this->momento = $r->fields['momento'];
			$this->id_proveedor = $r->fields['id_proveedor'];
			if(!empty($r->fields['proveedor']))
				$this->proveedor = $r->fields['proveedor'];
			else
				$this->proveedor = $r->fields['ciudadano'];
			$this->rif = $r->fields['rif'];
			switch($r->fields['status']){
				case 1:
					$this->compromiso = $this->get_suma_monto($conn, $r->fields['nrodoc']);
				break;
				case 2:
					$this->causado = $this->get_suma_monto($conn, $r->fields['nrodoc']);
					// obtengo el nro de doc de quien hace referencia a el
					$nroDocComprometido = $this->get_nroref($conn, $r->fields['nrodoc']);
					// guardo en comprometido la suma del monto del doc que lo referencia
					$this->compromiso = $this->get_suma_monto($conn, $nroDocComprometido);
				break;
				case 3:
					$this->pagado = $this->get_suma_monto($conn, $r->fields['nrodoc']);
					// obtengo el nro de doc de quien hace referencia a el
					$nroDocCausado = $this->get_nroref($conn, $r->fields['nrodoc']);
					// guardo en causado la suma del monto del doc que lo referencia
					$this->causado = $this->get_suma_monto($conn, $nroDocCausado);
					// obtengo el nro de doc de quien hace referencia a el
					$nroDocComprometido = $this->get_nroref($conn, $nroDocCausado);
					// guardo en comprometido la suma del monto del doc que lo referencia
					$this->compromiso = $this->get_suma_monto($conn, $nroDocComprometido);
				break;
				case 4:
					$this->aumentos = $this->get_suma_monto($conn, $r->fields['nrodoc']);
					// si el documento es referenciado obtengo el nro de doc de quien hace referencia a el
					$nroDoc = $this->_get_nrodoc($conn, $r->fields['nrodoc']);
					// guardo en disminuciones la suma del monto del doc que lo referencia
					$this->disminuciones = $this->get_suma_monto($conn, $nroDoc);
				break;
				case 5:
					$this->disminuciones = $this->get_suma_monto($conn, $r->fields['nrodoc']);
					$this->aumentos = $this->get_suma_monto($conn, $r->fields['nroref']);
				break;
			}
			return true;
		}else
			return false;
	}
	
	function showNrodoc($nrodoc){
		$aux = explode("-",$nrodoc);
		$tipdoc = $aux[0];
		$nrodoc = $aux[1];
		return $nrodoc;	
	}
	
	function get_causado($conn,$nroref, $idparcat){
		$q= "SELECT nrodoc FROM puser.movimientos_presupuestarios ";
		$q.= "WHERE nroref = '$nroref' ";
		//die($q);
		$r = $conn->Execute($q);
		$mp = new movimientos_presupuestarios;
		$IdParCat = array();
		while (!$r->EOF){
			$sql= "SELECT monto AS total, id_parcat FROM puser.relacion_movimientos ";
			$sql.= "WHERE nrodoc = '".$r->fields['nrodoc']."' ";
			$sql.= "AND id_parcat = '$idparcat' ";
			//$sql.= "GROUP BY id_parcat";
			$row = $conn->Execute($sql);
			while(!$row->EOF){
				$indice = array_search($IdParCat,$row->fields['id_parcat']);
				if ($indice!==false){
					$Monto[$indice] = $Monto[$indice] + $row->fields['total'];
				} else {
					$IdParCat[] = $row->fields['id_parcat'];
					$Monto[] =  $row->fields['total'];
				} 
				$row->movenext();
			}
			$r->movenext();
		}
		$mp->idparcat = $idParCat;
		$mp->total_partida = $Monto;
		$coleccion[] = $mp;
		return $coleccion;		
	}
	
	function getImputacionReportes($conn,$nrodoc,$escEnEje){
		$q = "SELECT id_categoria_programatica AS id_categoria, id_partida_presupuestaria AS id_partida, monto, categorias_programaticas.descripcion AS categoria, ";
		$q.= "partidas_presupuestarias.descripcion AS partida ";
		$q.= "FROM puser.relacion_movimientos ";
		$q.= "INNER JOIN puser.categorias_programaticas ON relacion_movimientos.id_categoria_programatica = categorias_programaticas.id ";
		$q.= "INNER JOIN puser.partidas_presupuestarias ON relacion_movimientos.id_partida_presupuestaria = partidas_presupuestarias.id ";
		$q.= "WHERE nrodoc = '$nrodoc' AND categorias_programaticas.id_escenario = '$escEnEje' AND partidas_presupuestarias.id_escenario = '$escEnEje'";
		//die($q);
		$r = $conn->Execute($q) or die($q);
		while(!$r->EOF){
			$mp = new movimientos_presupuestarios;
			$mp->id_partida = $r->fields['id_partida'];
			$mp->id_categoria = $r->fields['id_categoria'];
			$mp->categoria = $r->fields['categoria'];
			$mp->partida = $r->fields['partida'];
			$mp->monto = $r->fields['monto'];
			$coleccion[] = $mp;
			$r->movenext();
		}
		return $coleccion;
	}
	
	function get_monto_aumentos($conn,$nrodoc){
		$q = "SELECT sum(monto) AS monto ";
		$q.= "FROM relacion_movimientos WHERE nrodoc='$nrodoc' ";		
		//$q.= "GROUP BY parcat";
		//die($q);
		$r = $conn->Execute($q);
		if($r){
			return $r->fields['monto'];
		}
	}
	
	
}
?>
