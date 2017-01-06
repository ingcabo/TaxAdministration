<?
class obras{

	// Propiedades

	var $id;
	var $obra_cod;
	var $id_escenario;
	var $id_unidad_ejecutora;
	var $id_parroquia;
	var $id_relacion_obras;
	var $id_situacion;
	var $id_financiamiento;
	var $financiamiento;
	var $descripcion;
	var $denominacion;
	var $ctotal;								// costo total
	var $caa; 								// comprometido años anteriores
	var $eaa; 								// ejecucion anhos anteriores
	var $epre; 								// estimado presupuestario
	var $inicio; 								// fecha de inicio
	var $culminacion; 					// fecha de culminacion
	var $cav; 								// comprometido año vigente
	var $eav;  								// ejecucion año vigente
	var $epos; 								// estimado posterior
	var $ano;
	var $responsable; 					// funcionario responsable
	
	var $total;  // guarda la cantidad de obras
	
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
	var $msj;

	function get($conn, $id, $escEnEje){
		$q = "SELECT puser.obras.*, puser.financiamiento.descripcion AS financiamiento, ";
		$q.= "puser.unidades_ejecutoras.descripcion AS unidad_ejecutora, ";
		$q.= "puser.escenarios.descripcion AS escenario, puser.parroquias.descripcion AS parroquia ";
		$q.= "FROM puser.obras ";
		$q.= "LEFT JOIN financiamiento ON (puser.obras.id_financiamiento = puser.financiamiento.id) ";
		$q.= "LEFT JOIN unidades_ejecutoras ON (puser.obras.id_unidad_ejecutora = puser.unidades_ejecutoras.id) ";
		$q.= "LEFT JOIN escenarios ON (puser.obras.id_escenario = puser.escenarios.id) ";
		$q.= "LEFT JOIN parroquias ON (puser.obras.id_parroquia = puser.parroquias.id) ";
		$q.= "WHERE puser.obras.id='$id' ";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->obra_cod = $r->fields['obra_cod'];			
			$this->id_escenario = $r->fields['id_escenario'];
			$this->id_unidad_ejecutora = $r->fields['id_unidad_ejecutora'];
			$this->id_parroquia = $r->fields['id_parroquia'];
			$this->id_relacion_obras = $r->fields['id_relacion_obras'];
			$this->id_situacion = $r->fields['id_situacion'];
			$this->id_financiamiento = $r->fields['id_financiamiento'];
			$this->financiamiento = $r->fields['financiamiento'];
			$this->escenario = $r->fields['escenario'];
			$this->unidad_ejecutora = $r->fields['unidad_ejecutora'];
			$this->parroquia = $r->fields['parroquia'];
			$this->relacion_obras = $r->fields['relacion_obras'];
			$this->descripcion = $r->fields['descripcion'];
			$this->denominacion = $r->fields['denominacion'];
			$this->ctotal = $r->fields['ctotal'];
			$this->caa = $r->fields['caa'];
			$this->cav = $r->fields['cav'];
			$this->eaa = $r->fields['eaa'];
			$this->epre = $r->fields['epre'];
			$this->inicio = muestrafecha($r->fields['inicio']);
			$this->culminacion = muestrafecha($r->fields['culminacion']);
			$this->eav = $r->fields['eav'];
			$this->epos = $r->fields['epos'];
			$this->ano = $r->fields['ano'];
			$this->responsable = $r->fields['responsable'];
			if(!empty($id))
				$this->get_relaciones($conn, $id, $r->fields['id_escenario']);
			$this->completo = $r->fields['id']." :: ".$r->fields['descripcion'];
			return true;
		}else
			return false;
	}

	function get_all($conn, $from=0, $max=0,$orden="id"){
		$q = "SELECT * FROM obras ";
		$q.= "ORDER BY $orden ";
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		while(!$r->EOF){
			$ue = new obras;
			$ue->get($conn, $r->fields['id'], $r->fields['id_escenario']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}
	function add($conn,	 	$obra_cod,
								$id_escenario,
								 $id_unidad_ejecutora,
								 $id_parroquia,
								 $id_situacion,
								 $id_financiamiento, 
								 $descripcion,
								 $denominacion,
								 $ctotal,	
								 $caa='', 	
								 $eaa='', 	
								 $epre, 	
								 $inicio, 	
								 $culminacion,
								 $cav, 				
								 $eav,  				
								 $epos, 				
								 $ano,
								 $responsable,
								 $aObras){
		try{
			$q = "INSERT INTO puser.obras ";
			$q.= "(obra_cod,id_escenario, id_unidad_ejecutora, id_parroquia, id_situacion, id_financiamiento, ";
			$q.= "descripcion, denominacion, ";
	 		$q.= "ctotal, caa,  eaa,  epre, inicio, culminacion, cav, eav, epos, ano, responsable) ";
			$q.= "VALUES ";
			$q.= "('$obra_cod','$id_escenario', '$id_unidad_ejecutora', '$id_parroquia', '$id_situacion', ";
			$q.= "'$id_financiamiento', '$descripcion', ";
			$q.= "'$denominacion', '$ctotal', '$caa', '$eaa', '$epre', '".guardafecha($inicio)."', ";
			$q.= "'".guardafecha($culminacion)."', ";
			$q.= "'$cav', '$eav', '$epos', '$ano', '$responsable') ";
			//die($q);
			$conn->Execute($q);
			if($this->add_relacion($conn, getLastId($conn, 'id', 'puser.obras'), $aObras)){
				$this->msj = REG_ADD_OK; 
				return true;
			}
		}
		catch( ADODB_Exception $e ){
			if($e->getCode()==-1){
				$this->msj = ERROR_CATCH_VFK; 
				return false;
			}elseif($e->getCode()==-5){
				$this->msj = ERROR_CATCH_VUK;
				return false;
			}else{
				$this->msj = ERROR_CATCH_GENERICO; 
				return false;
			}
		}
	}

	function set($conn, $id,
								 $obra_cod,
								 $id_escenario,
								 $id_unidad_ejecutora,
								 $id_parroquia,
								 $id_situacion,
								 $id_financiamiento, 
								 $descripcion,
								 $denominacion,
								 $ctotal,	
								 $caa, 	
								 $eaa, 	
								 $epre, 	
								 $inicio, 	
								 $culminacion,
								 $cav, 				
								 $eav,  				
								 $epos, 				
								 $ano,
								 $responsable,
								 $aObra){
		try{
			$q = "UPDATE obras SET ";
			$q.= "obra_cod='$obra_cod', ";
			$q.= "id_escenario='$id_escenario', ";
			$q.= "id_unidad_ejecutora='$id_unidad_ejecutora', ";
			$q.= "id_parroquia='$id_parroquia', ";
			$q.= "id_situacion='$id_situacion', ";
			$q.= "id_financiamiento='$id_financiamiento', ";
			$q.= "descripcion='$descripcion', ";
			$q.= "denominacion='$denominacion', ";
			$q.= "ctotal='$ctotal', ";
			$q.= "caa='$caa', ";
			$q.= "eaa='$eaa', ";
			$q.= "epre='$epre', ";
			$q.= "inicio='".guardafecha($inicio)."', ";
			$q.= "culminacion='".guardafecha($culminacion)."', ";
			$q.= "cav='$cav', ";
			$q.= "eav='$eav', ";
			$q.= "epos='$epos', ";
			$q.= "ano='$ano', ";
			$q.= "responsable='$responsable' ";
			$q.= "WHERE id='$id' ";
			//die($q);
			$this->del_relacion($conn, $id);
			$conn->Execute($q);
			$this->add_relacion($conn, $id, $aObra);
			$this->msj = REG_SET_OK; 
			return true;
		}
		catch( ADODB_Exception $e ){
			if($e->getCode()==-1){
				$this->msj = ERROR_CATCH_VFK; 
				return false;
			}elseif($e->getCode()==-5){
				$this->msj = ERROR_CATCH_VUK;
				return false;
			}else{
				$this->msj = ERROR_CATCH_GENERICO; 
				return false;
			}
		}
	}

	function del($conn, $id){
		try{
			$q = "DELETE FROM puser.obras WHERE id='$id'";
			$r = $conn->Execute($q);
			$this->msj = REG_DEL_OK;
			//echo "msj = ".$this->msj;
			return true;
		}
		catch( ADODB_Exception $e ){
			if($e->getCode()==-1){
				$this->msj = ERROR_OBRA_VFK;
				//die("msj = ".$this->msj);
				return false;
			}elseif($e->getCode()==-5){
				$this->msj = ERROR_CATCH_VUK;
				//die("msj = ".$this->msj);
				return false;
			}else{
				$this->msj = ERROR_CATCH_GENERICO;
				//die("msj = ".$this->msj);
				return false;
			}
		}
	}
	
	function get_relaciones($conn, $id, $escEnEje){
		try{
			$q = "SELECT relacion_obras.*, categorias_programaticas.descripcion AS categoria_programatica, ";
			$q.= "partidas_presupuestarias.descripcion AS partida_presupuestaria ";
			$q.= "FROM puser.relacion_obras ";
			$q.= "INNER JOIN puser.categorias_programaticas ON (relacion_obras.id_categoria_programatica = categorias_programaticas.id) ";
			$q.= "INNER JOIN puser.partidas_presupuestarias ON (relacion_obras.id_partida_presupuestaria = partidas_presupuestarias.id) ";
			$q.= "WHERE relacion_obras.id_obra='$id' AND categorias_programaticas.id_escenario='$escEnEje' ";
			$q.= "AND partidas_presupuestarias.id_escenario='$escEnEje' ";
			//die($q);
			$r = $conn->Execute($q);
			while(!$r->EOF){
				$ue = new obras;
				$ue->id = $r->fields['id'];
				$ue->idParCat	= $r->fields['id_parcat'];
				$ue->id_partida_presupuestaria	= $r->fields['id_partida_presupuestaria'];
				$ue->id_categoria_programatica = $r->fields['id_categoria_programatica'];
				$ue->partida_presupuestaria	= $r->fields['partida_presupuestaria'];
				$ue->categoria_programatica = $r->fields['categoria_programatica'];
				$ue->monto = $r->fields['monto'];
				$coleccion[] = $ue;
				$r->movenext();
			}
			$this->relacion = new Services_JSON();
			$this->relacion = is_array($coleccion) ? $this->relacion->encode($coleccion) : false;
			return $coleccion;
		}
		catch( ADODB_Exception $e ){
			if($e->getCode()==-1){
				$this->msj = ERROR_CATCH_VFK;
				die("msj = ".$this->msj);
				return false;
			}elseif($e->getCode()==-5){
				$this->msj = ERROR_CATCH_VUK;
				die("msj = ".$this->msj);
				return false;
			}else{
				$this->msj = ERROR_CATCH_GENERICO;
				die("msj = ".$this->msj);
				return false;
			}
		}
	}
	
	function add_relacion($conn, 
								$id_obra,
								$aObra){
		$JsonRec = new Services_JSON();
		$JsonRec=$JsonRec->decode(str_replace("\\","",$aObra));
		if(is_array($JsonRec->obra)){
			foreach ($JsonRec->obra as $oO_Aux){
				$O_Aux_2= guardafloat($oO_Aux[2]);
				$q = "INSERT INTO puser.relacion_obras ";
				$q.= "(id_parcat, id_categoria_programatica, id_partida_presupuestaria, id_obra, monto) ";
				$q.= "VALUES ";
				$q.= "('$oO_Aux[3]', '$oO_Aux[0]', '$oO_Aux[1]', '$id_obra', $O_Aux_2) ";
				//die($q);
				$r = $conn->Execute($q);
			}
		}
		if($r)
			return true;
		else
			return false;
	}
	
	function del_relacion($conn, $id){
		$q = "DELETE FROM relacion_obras WHERE id_obra='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	function buscar($conn, 
						$id_financiadora, 
						$id_ue, 
						$fecha_desde, 
						$fecha_hasta, 
						$id,
						$escEnEje,
						$orden="id"){
		/*
		if(empty($id_financiadora) 
			&& empty($id_ue)
			&& empty($fecha_desde)
			&& empty($fecha_hasta)
			&& empty($id)
			)
			return false;
		*/
		$q = "SELECT * FROM puser.obras ";
		$q.= "WHERE  1=1 ";
		$q.= !empty($id) ? "AND id='$id' ": "";
		$q.= !empty($fecha_desde) ? "AND inicio >='".guardafecha($fecha_desde)."' ": "";
		$q.= !empty($fecha_hasta) ? "AND inicio <='".guardafecha($fecha_hasta)."' ": "";
		$q.= !empty($id_financiadora) ? "AND id_financiamiento = '$id_financiadora'  ":"";
		$q.= !empty($id_ue) ? "AND id_unidad_ejecutora = '$id_ue'  ":"";
		$q.= "ORDER BY $orden ";
		//die($q);
		if(!$r = $conn->Execute($q))
			return false;
		$collection=array();
		while(!$r->EOF){
			$ue = new obras;
			$ue->get($conn, $r->fields['id'],$escEnEje);
			$coleccion[] = $ue;
			$r->movenext();
		}
		return $coleccion;
	}
	
	function total_registro_busqueda($conn, 
						$id_financiadora, 
						$id_ue, 
						$fecha_desde, 
						$fecha_hasta, 
						$id,
						$orden="id"){
		if(empty($id_financiadora) && empty($id_ue) && empty($fecha_desde) && empty($fecha_hasta) && empty($id))
			return false;
		$q = "SELECT * FROM puser.obras ";
		$q.= "WHERE  1=1 ";
		$q.= !empty($id) ? "AND id='$id' ": "";
		$q.= !empty($fecha_desde) ? "AND inicio >='".guardafecha($fecha_desde)."' ": "";
		$q.= !empty($fecha_hasta) ? "AND inicio <='".guardafecha($fecha_hasta)."' ": "";
		$q.= !empty($id_financiadora) ? "AND id_financiamiento = '$id_financiadora'  ":"";
		$q.= !empty($id_ue) ? "AND id_unidad_ejecutora = '$id_ue'  ":"";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		$total = $r->RecordCount();

		return $total;
	}
	function get_all_escenario($conn, $escenario, $from=0, $max=0,$orden="id"){
		$q = "SELECT * FROM obras  WHERE id_escenario = '$escenario' ";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		while(!$r->EOF){
			$ue = new obras;
			$ue->get($conn, $r->fields['id'], $r->fields['id_escenario']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}
}
?>
