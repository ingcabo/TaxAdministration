<?
class relacion_pp_cp{
	// Propiedades
	var $id;
	var $id_escenario;
	var $id_categoria_programatica;
	var $id_partida_presupuestaria;
	var $id_asignacion;
	var $escenario;
	var $categoria_programatica;
	var $partida_presupuestaria;
	var $asignaciones;
	var $presupuesto_original;
	var $aumentos;
	var $disminuciones;
	var $compromisos;
	var $causados;
	var $pagados;
	var $disponible;
	var $aingresos;
	var $agastos;
	var $ano;

	var $total;

	function get($conn, $id, $escEnEje=''){

		try{
			$q = "SELECT DISTINCT relacion_pp_cp.*, escenarios.descripcion AS escenario, ";
			$q.= "categorias_programaticas.descripcion AS categoria_programatica, ";
			$q.= "partidas_presupuestarias.descripcion AS partida_presupuestaria, ";
			$q.= "asignaciones.descripcion AS asignacion, ";
			$q.= "substr(relacion_pp_cp.id_partida_presupuestaria, 1, 3):: char(3) as cod_madre ";
			$q.= "FROM relacion_pp_cp ";
			$q.= "INNER JOIN partidas_presupuestarias ON (relacion_pp_cp.id_partida_presupuestaria = partidas_presupuestarias.id) ";
			$q.= "INNER JOIN escenarios ON (relacion_pp_cp.id_escenario = escenarios.id) ";
			$q.= "INNER JOIN categorias_programaticas ON (relacion_pp_cp.id_categoria_programatica = categorias_programaticas.id) ";
			$q.= "LEFT JOIN asignaciones ON (relacion_pp_cp.id_asignacion = asignaciones.id) ";
			$q.= "WHERE relacion_pp_cp.id='$id' ";

			$r = $conn->Execute($q);

			$this->id = $r->fields['id'];
			$this->id_escenario = $r->fields['id_escenario'];
			$this->id_partida_presupuestaria = $r->fields['id_partida_presupuestaria'];
			$this->id_categoria_programatica = $r->fields['id_categoria_programatica'];
			$this->id_asignacion = $r->fields['id_asignacion'];
			$this->escenario = $r->fields['escenario'];
			$this->partida_presupuestaria = $r->fields['partida_presupuestaria'];
			$this->categoria_programatica = $r->fields['categoria_programatica'];
			$this->presupuesto_original = $r->fields['presupuesto_original'];
			$this->asignacion = $r->fields['asignacion'];
			$this->aumentos = $r->fields['aumentos'];
			$this->disminuciones = $r->fields['disminuciones'];
			$this->compromisos = $r->fields['compromisos'];
			$this->causados = $r->fields['causados'];
			$this->pagados = $r->fields['pagados'];
			$this->disponible = $r->fields['disponible'];
			$this->aingresos = $r->fields['aingresos'];
			$this->agastos = $r->fields['agastos'];
			$this->ano = $r->fields['ano'];
			$this->madre = $r->fields['cod_madre'];
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

	function get_all($conn, $from=0, $max=0, $orden = "id_escenario, id_categoria_programatica, id_partida_presupuestaria"){
		try{
			$q = "SELECT * FROM relacion_pp_cp  ";
			$q.= "ORDER BY $orden ";
			$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
			$collection=array();
			while(!$r->EOF){
				$ue = new relacion_pp_cp;
				$ue->get($conn, $r->fields['id']);
				$coleccion[] = $ue;
				$ue->__destruct();
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
	function __destruct(){
    }
	function add($conn, 
				$id_escenario,
				$id_categoria_programatica,
				$id_partida_presupuestaria,
				$id_asignacion,
				$presupuesto_original,
				$aumentos,
				$disminuciones,
				$compromisos,
				$causados,
				$pagados,
				$disponible,
				$aingresos,
				$agastos,
				$ano){
		try{
			$aingresos = ($aingresos == 'on')? "true" : "false";
			$agastos = ($agastos == 'on')? "true" : "false";
			$presupuesto_original = (empty($presupuesto_original)) ? "0" : $presupuesto_original;
			$aumentos = (empty($aumentos)) ? "0" : $aumentos;
			$disminuciones = (empty($disminuciones)) ? "0" : $disminuciones;
			$compromisos = (empty($compromisos)) ? "0" : $compromisos;
			$causados = (empty($causados)) ? "0" : $causados;
			$pagados = (empty($pagados)) ? "0" : $pagados;
			$disponible = (empty($disponible)) ? "0" : $disponible;
			$q = "INSERT INTO relacion_pp_cp ";
			$q.= "(id_escenario, id_categoria_programatica, id_partida_presupuestaria, id_asignacion, ";
			$q.= "presupuesto_original, aumentos, ";
			$q.= "disminuciones, compromisos, causados, pagados, disponible, aingresos, agastos, ano ) ";
			$q.= "VALUES ";
			$q.= "('$id_escenario', '$id_categoria_programatica', '$id_partida_presupuestaria', '$id_asignacion', ";
			$q.= " '$presupuesto_original', '$aumentos', ";
			$q.= "'$disminuciones', '$compromisos', '$causados', '$pagados', '$disponible', $aingresos, $agastos, '$ano' ) ";
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

	function set($conn, 
				$id,
				$id_escenario,
				$id_categoria_programatica,
				$id_partida_presupuestaria,
				$id_asignacion,
				$presupuesto_original,
				$aumentos,
				$disminuciones,
				$compromisos,
				$causados,
				$pagados,
				$disponible,
				$aingresos,
				$agastos,
				$ano){
		try{
			$aingresos = ($aingresos == 'on')? "true" : "false";
			$agastos = ($agastos == 'on')? "true" : "false";
			$q = "UPDATE relacion_pp_cp SET id_escenario='$id_escenario', ";
			$q.= "id_categoria_programatica = '$id_categoria_programatica', id_partida_presupuestaria = '$id_partida_presupuestaria', ";
			$q.= "id_asignacion = '$id_asignacion', presupuesto_original = '$presupuesto_original', aumentos = '$aumentos', ";
			$q.= "disminuciones = '$disminuciones', compromisos = '$compromisos', ";
			$q.= "causados = '$causados', pagados = '$pagados', disponible = '$disponible', ";
			$q.= "aingresos = $aingresos, agastos = $agastos, ano = '$ano' ";
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
		try{
			$q = "DELETE FROM relacion_pp_cp WHERE id='$id'";
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
			$q = "DELETE FROM relacion_pp_cp WHERE id_escenario='$id'";
			//die($q);
			$conn->Execute($q);
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

	function has_escenario($conn, $id){
		$q = "SELECT 'X' FROM relacion_pp_cp ";
		$q.= "WHERE relacion_pp_cp.id_escenario = '$id' ";
		$r = $conn->Execute($q);
		//die($q);
		if($r->RecordCount() > 0)
			return true;
		else
			return false;
	}

	function get_all_by_esc($conn, $id_escenario, $from=0, $max=0,$orden="id"){
		try{
			$q = "SELECT * FROM relacion_pp_cp WHERE id_escenario = '$id_escenario' ";
			$q.= "ORDER BY $orden ";
			$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
			$collection=array();
			while(!$r->EOF){
				$ue = new relacion_pp_cp;
				$ue->get($conn, $r->fields['id']);
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
	
	function set_by_esc($conn, $id_escenario, $factor){
		$q = "UPDATE relacion_pp_cp SET ";
		$q.= "presupuesto_original = presupuesto_original * $factor ";
		$q.= "WHERE id_escenario='$id_escenario' ";
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	function get_all_by_cp_pp_esc($conn, $cp, $pp, $esc){
		$q = "SELECT DISTINCT puser.relacion_pp_cp.*, puser.categorias_programaticas.descripcion AS nomcat, puser.partidas_presupuestarias.descripcion AS nompar FROM puser.relacion_pp_cp ";
		$q.= "INNER JOIN puser.categorias_programaticas ON puser.relacion_pp_cp.id_categoria_programatica = puser.categorias_programaticas.id ";
		$q.= "INNER JOIN puser.partidas_presupuestarias ON puser.relacion_pp_cp.id_partida_presupuestaria = puser.partidas_presupuestarias.id ";
		$q.= "WHERE puser.relacion_pp_cp.id_escenario = '$esc' AND puser.relacion_pp_cp.id_categoria_programatica = '$cp' AND puser.relacion_pp_cp.id_partida_presupuestaria = '$pp' ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		if(!$r->EOF){
			$ue = new relacion_pp_cp;
			$ue->id = $r->fields['id'];
			$ue->presupuesto_original = $r->fields['presupuesto_original'];
			$ue->compromisos = $r->fields['compromisos'];
			$ue->causados = $r->fields['causados'];
			$ue->pagados = $r->fields['pagados'];
			$ue->aumentos = $r->fields['aumentos'];
			$ue->disminuciones = $r->fields['disminuciones'];
			$ue->disponible = $r->fields['disponible'];
			$ue->nom_cat = $r->fields['nomcat'];
			$ue->nom_par = $r->fields['nompar'];
			return $ue;
		}
	}
	
	function get_all_by_cp_pp_esc_new($conn, $cp, $pp, $esc){
		$q = "SELECT * FROM relacion_pp_cp ";
		$q.= "WHERE id_escenario = '$esc' AND id_categoria_programatica = '$cp' AND id_partida_presupuestaria = '$pp' ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		if(!$r->EOF){
			$ue = new relacion_pp_cp;
			$ue->id = $r->fields['id'];
			$ue->presupuesto_original = $r->fields['presupuesto_original'];
			$ue->compromisos = $r->fields['compromisos'];
			$ue->causados = $r->fields['causados'];
			$ue->pagados = $r->fields['pagados'];
			$ue->aumentos = $r->fields['aumentos'];
			$ue->disminuciones = $r->fields['disminuciones'];
			$ue->disponible = $r->fields['disponible'];
			$coleccion[] = $ue;
			return $coleccion;
		}
	}

	function set_desde_movpre($conn, 
				$aId,
				$aCompromisos='',
				$aCausados='',
				$aPagados='',
				$aAumentos='',
				$aDisminuciones='',
				$aDisponible=''){
		for($i=0; $i<count($aId);$i++){
			$q = "UPDATE relacion_pp_cp SET id='$aId[$i]' ";
			$q.= (!empty($aCompromisos[$i])) ? ", compromisos = compromisos + ".guardafloat($aCompromisos[$i]) : "";
			$q.= (!empty($aCausados[$i])) ? ", causados = causados + ".guardafloat($aCausados[$i]) : "";
			$q.= (!empty($aPagados[$i])) ? ", pagados = pagados + ".guardafloat($aPagados[$i]) : "";
			$q.= (!empty($aAumentos[$i])) ? ", aumentos = aumentos + ".guardafloat($aAumentos[$i]) : "";
			$q.= (!empty($aDisminuciones[$i])) ? ", disminuciones = disminuciones + ".guardafloat($aDisminuciones[$i]) : "";
			if (!empty($aAumentos[$i])){
				$q.= (!empty($aDisponible[$i])) ? ", disponible = disponible + ".guardafloat($aDisponible[$i]) : "";
			}elseif (!empty($aCompromisos[$i]) || !empty($aDisminuciones[$i])){
				$q.= (!empty($aDisponible[$i])) ? ", disponible = disponible - ".guardafloat($aDisponible[$i]) : "";
			}
			$q.= "WHERE id='$aId[$i]' ";
			//die($q);
			//echo $q."<br>";
			$r = $conn->Execute($q);
		}
		if($r)
			return true;
		else
			return false;
	}
	function set_desde_compromiso($conn, $aId, $aCompromisos){
		for($i=0; $i<count($aId);$i++){
			$q = "UPDATE puser.relacion_pp_cp SET  ";
			$q.= " compromisos = compromisos + ".guardafloat($aCompromisos[$i]);
			$q.= ", disponible = disponible - ".guardafloat($aCompromisos[$i]);
			$q.= "WHERE id='$aId[$i]' ";
			//echo $q."<br>";
			$r = $conn->Execute($q);
		}
		if($r)
			return true;
		else
			return false;
	}
	function set_desde_compromiso_nomina($conn, $aId, $aCompromisos){
		$q = "UPDATE puser.relacion_pp_cp SET  ";
		$q.= " compromisos = compromisos + ".$aCompromisos;
		$q.= ", disponible = disponible - ".$aCompromisos;
		$q.= "WHERE id='$aId' ";
		$r = $conn->Execute($q);
		if($r)
			return true;
		else
			return false;
	}
	function set_desde_pagado_cheque($conn, $aId, $aMonto){
		$q = "UPDATE puser.relacion_pp_cp SET  ";
		$q.= " pagados = pagados + ".$aMonto;
		$q.= ", causados = causados - ".$aMonto;
		$q.= "WHERE id='$aId' ";
		//echo $q."<br>";
		$r = $conn->Execute($q);
		if($r)
			return true;
		else
			return false;
	}
	function set_desde_pagado_cheque_anulado($conn, $aId, $aMonto){
		$q = "UPDATE puser.relacion_pp_cp SET  ";
		$q.= " pagados = pagados - ".$aMonto;
		$q.= ", causados = causados + ".$aMonto;
		$q.= "WHERE id='$aId' ";
		//echo $q."<br>";
		$r = $conn->Execute($q);
		if($r)
			return true;
		else
			return false;
	}
	
	#ESTE ES EN EL CASO DE QUE SE APRUEBA LA ORDEN DE PAGO#
	function set_desde_solicitud_pagos($conn, $aId, $aCausado){
		
		for($i=0; $i<count($aId);$i++){
			$q = "UPDATE puser.relacion_pp_cp SET  ";
			$q.= " causados = causados + ".guardafloat($aCausado[$i]);
			$q.= ", compromisos = compromisos - ".guardafloat($aCausado[$i]);
			$q.= " WHERE id='$aId[$i]' ";
			//die($q);
			//echo $q."<br>";
			$r = $conn->Execute($q);
		}
		if($r)
			return true;
		else
			return false;
	}
	
	#ESTE ES EN EL CASO DE QUE SE ANULA LA ORDEN DE PAGO#
	function set_desde_solicitud_pagos_anulada($conn, $aId, $aMonto, $status){
		for($i=0; $i<count($aId);$i++){
			$q = "UPDATE puser.relacion_pp_cp SET  ";
			$q.= ($status=='2')? " causados = causados + ".guardafloat($aMonto[$i]) : "compromisos = compromisos + ".guardafloat($aMonto[$i]);
			$q.= ", compromisos = compromisos - ".guardafloat($aMonto[$i]);
			$q.= " WHERE id='$aId[$i]' ";
			
			$r = $conn->Execute($q);
		}
		if($r)
			return true;
		else
			return false;
	}
	
	#ESTE ES EN EL CASO DE QUE SE ANULA CUALQUIER DOCUMENTO DE COMPROMISO#
	function set_desde_compromiso_anulado($conn, $aId, $aMonto){
		
		for($i=0; $i<count($aId);$i++){
			$q = "UPDATE puser.relacion_pp_cp SET  ";
			$q.= "compromisos = compromisos + ".guardafloat($aMonto[$i]);
			$q.= ", disponible = disponible - ".guardafloat($aMonto[$i]);
			$q.= " WHERE id='$aId[$i]' ";
			//echo $q."<br>";
			$r = $conn->Execute($q);
		}
		//die();
		if($r)
			return true;
		else
			return false;
	}
	
	function set_desde_pagado_anulado($conn, $aId, $aMonto){
		
		for($i=0; $i<count($aId);$i++){
			$q = "UPDATE puser.relacion_pp_cp SET  ";
			$q.= "pagados = pagados + ".guardafloat($aMonto[$i]);
			$q.= " WHERE id='$aId[$i]' ";
			
			$r = $conn->Execute($q);
		}
		if($r)
			return true;
		else
			return false;
	}

	function buscar($conn, $id_escenario, $cod_categoria_programatica, $id_categoria_programatica, $cod_partida_presupuestaria, $id_partida_presupuestaria, $max=10, $from=1, $orden="id_escenario, id"){
		if(empty($id_escenario) && empty($cod_partida_presupuestaria) && empty($id_partida_presupuestaria) && empty($cod_categoria_programatica) && empty($id_categoria_programatica))
			return false;
		$q = "SELECT * FROM relacion_pp_cp ";
		$q.= "WHERE  1=1 ";
		$q.= !empty($id_escenario) ? "AND id_escenario = '$id_escenario'  ":"";
		$q.= !empty($cod_partida_presupuestaria) ? "AND id_partida_presupuestaria = '$cod_partida_presupuestaria'  ":"";
		$q.= !empty($id_partida_presupuestaria) ? "AND id_partida_presupuestaria = '$id_partida_presupuestaria'  ":"";
		$q.= !empty($cod_categoria_programatica) ? "AND id_categoria_programatica = '$cod_categoria_programatica'  ":"";
		$q.= !empty($id_categoria_programatica) ? "AND id_categoria_programatica = '$id_categoria_programatica'  ":"";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new relacion_pp_cp;
			$ue->get($conn, $r->fields['id'], $r->fields['id_escenario']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		
	
		return $coleccion;
	}
	
	function total_registro_busqueda($conn, $id_escenario, $cod_categoria_programatica, $id_categoria_programatica, $cod_partida_presupuestaria, $id_partida_presupuestaria, $orden="id_escenario, id"){
		if(empty($id_escenario) && empty($cod_partida_presupuestaria) && empty($id_partida_presupuestaria) && empty($cod_categoria_programatica) && empty($id_categoria_programatica))
			return false;
		$q = "SELECT * FROM relacion_pp_cp ";
		$q.= "WHERE  1=1 ";
		$q.= !empty($id_escenario) ? "AND id_escenario = '$id_escenario'  ":"";
		$q.= !empty($cod_partida_presupuestaria) ? "AND id_partida_presupuestaria = '$cod_partida_presupuestaria'  ":"";
		$q.= !empty($id_partida_presupuestaria) ? "AND id_partida_presupuestaria = '$id_partida_presupuestaria'  ":"";
		$q.= !empty($cod_categoria_programatica) ? "AND id_categoria_programatica = '$cod_categoria_programatica'  ":"";
		$q.= !empty($id_categoria_programatica) ? "AND id_categoria_programatica = '$id_categoria_programatica'  ":"";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		$total = $r->RecordCount();

		return $total;
	}
	
	function busca_cp($conn, $id_ue, $escenario, $fecha_desde='', $fecha_hasta=''){
		$qcp = "SELECT * FROM relacion_ue_cp ";
		$qcp.= "WHERE id_unidad_ejecutora='$id_ue' AND id_escenario='$escenario' ";
//		$qcp.= !empty($fecha_desde) ? "AND  = '$id_pp' " : "";
		//die($qcp);
		$rcp = $conn->execute($qcp) or die($qcp);
		while(!$rcp->EOF){
			$cpp = new categorias_programaticas;
			$cpp->get($conn, $rcp->fields['id_categoria_programatica'], $escenario);
			$coleccion[] = $cpp;
			
			$rcp->movenext();
			$i++;
		}
		
		return $coleccion;
		
	
	}
	
	#ESTA FUNCION TRAE LOS SALDOS (COMPROMISO, CAUSADO, PAGADO, DISPONIBLE, AUMENTO, DISMINUCION)
	#DE LAS PARTIDAS PRESUPUESTARIAS POR CATEGORIAS PROGRAMATICAS#
	function estado_partidas($conn, $idCp, $escenario){//, $ppIni, $ppFin, $fechaDesde, $fechaHasta){
		$qpp = "SELECT * FROM relacion_pp_cp  ";
		$qpp.= "WHERE id_categoria_programatica='$idCp' "; 
		$qpp.= "AND id_escenario='$escenario' ";
		$qpp.= "AND id_escenario='$escenario' ";
		$qpp.= "AND id_escenario='$escenario' ";
		//die($qpp);
		$rpp = $conn->execute($qpp) or die($qpp);
		#ESTE WHILE ES PARA TRAER LOS DATOS DE LAS PARTIDAS#			
		while (!$rpp->EOF){
			$pp = new relacion_pp_cp;
			$pp->get($conn, $rpp->fields['id'], $escenario);
			$coleccion[] = $pp;
			$rpp->movenext();
		}
		//print_r($coleccion);
		//die(2);	
		return $coleccion;	
	}	
	
	function reporte_pp_cp($conn, $escEnEje, $id_cp)
	{
		$q = "SELECT
				puser.categorias_programaticas.id AS id_cp,
				puser.categorias_programaticas.descripcion AS desc_cp,
				puser.partidas_presupuestarias.id AS id_pp,
				puser.partidas_presupuestarias.madre AS madre_pp,
				puser.partidas_presupuestarias.descripcion AS desc_pp,
				puser.relacion_pp_cp.presupuesto_original AS monto
			FROM
				puser.categorias_programaticas
			INNER JOIN 
				puser.relacion_pp_cp 
			ON 
				puser.relacion_pp_cp.id_escenario = puser.categorias_programaticas.id_escenario 
			AND 
				puser.relacion_pp_cp.id_categoria_programatica = puser.categorias_programaticas.id
			INNER JOIN 
				puser.partidas_presupuestarias 
			ON 
				puser.relacion_pp_cp.id_escenario = puser.partidas_presupuestarias.id_escenario 
			AND 
				puser.relacion_pp_cp.id_partida_presupuestaria = puser.partidas_presupuestarias.id
			WHERE
				puser.relacion_pp_cp.id_escenario =  '$escEnEje' 
			AND
        puser.categorias_programaticas.id = '$id_cp'
			ORDER BY
				puser.partidas_presupuestarias.id";
		//die($q);
		$rq = $conn->Execute($q);
		while(!$rq->EOF)
		{
			$cpp = new relacion_pp_cp;
			$cpp->id_cp = $rq->fields['id_cp'];
			$cpp->desc_cp = $rq->fields['desc_cp'];
			$cpp->id_pp = $rq->fields['id_pp'];
			$cpp->desc_pp = $rq->fields['desc_pp'];
			$cpp->monto = $rq->fields['monto'];
			$coleccion[] = $cpp;
			$rq->movenext();
		}
			return $coleccion;
	}

	/* Devuelve una coleccion de objetos de las partidas x categorias, 
		este metodo es utilizado en el reporte de ejecucion
		Presupuestaria detallado
		*/
	function partidasXCategoriasRepDet($conn, 
													$escEnEje, 
													$idCp, 
													/* Partida Inicial */ $idPini, 
													/* Partida Final */ $idPfin){
		$q = "SELECT DISTINCT parcat.*, pp.descripcion AS desc_pp ";
		$q.= "FROM puser.relacion_pp_cp parcat ";
		$q.= "INNER JOIN puser.partidas_presupuestarias pp ON (pp.id = parcat.id_partida_presupuestaria) ";
		$q.= "WHERE parcat.id_escenario = '$escEnEje' AND parcat.id_categoria_programatica = '$idCp' ";
		$q.= "AND (parcat.id_partida_presupuestaria >= '$idPini' AND parcat.id_partida_presupuestaria <= '$idPfin') ";
		$q.= "ORDER BY parcat.id_partida_presupuestaria";
		//die($q);
		//echo "primer: ".$q."<br>";
		$rq = $conn->Execute($q);
		while(!$rq->EOF)
		{
			$cpp = new relacion_pp_cp;
			$cpp->id = $rq->fields['id'];
			$cpp->id_pp = $rq->fields['id_partida_presupuestaria'];
			$cpp->desc_pp = $rq->fields['desc_pp'];
			$cpp->ppo = $rq->fields['presupuesto_original'];
			$cpp->aumentos = $rq->fields['aumentos'];
			$cpp->disminuciones = $rq->fields['disminuciones'];
			$cpp->compromisos = $rq->fields['compromisos'];
			$cpp->causados = $rq->fields['causados'];
			$cpp->pagados = $rq->fields['pagados'];
			$cpp->disponible = $rq->fields['disponible'];
			$coleccion[] = $cpp;
			$rq->movenext();
		}
		return $coleccion;
	}

		/* Devuelve una coleccion de objetos de cada uno de los documentos que han alterado la tabla 
			relacion_pp_cp, 
			es decir, haya comprometido, causado o pagado 
			este metodo es utilizado en el reporte de ejecucion
			Presupuestaria detallado
		*/
	function docsRepDet($conn, 
								$idParcat, 
								$fechaDesde, 
								$fechaHasta){
		$q = "SELECT DISTINCT rm.id, rm.nrodoc, mp.descripcion, rm.monto, mp.fechadoc, mp.tipdoc, mp.nroref,  ";
		$q.= "td.id_momento_presupuestario AS id_momento, mp.status_movimiento AS status, rm.id_parcat, prov.nombre AS proveedor ";
		$q.= "FROM puser.relacion_movimientos rm ";
		$q.= "INNER JOIN puser.movimientos_presupuestarios mp ON (replace(rm.nrodoc, '-ANULADO', '')=mp.nrodoc) ";
		$q.= "INNER JOIN puser.tipos_documentos td ON (mp.tipdoc=td.id) ";
		$q.= "INNER JOIN puser.proveedores prov ON (mp.id_proveedor=prov.id)  ";
		$q.= "WHERE rm.id_parcat=$idParcat AND rm.monto <> 0 ";
		$q.= (!empty($fechaDesde)) ? "AND mp.fechadoc >= '".guardafecha($fechaDesde)."' " : "";
		$q.= (!empty($fechaHasta)) ? "AND mp.fechadoc <= '".guardafecha($fechaHasta)."' " : "";
		$q.= "ORDER BY id_momento, mp.fechadoc, rm.nrodoc ";
		
		//die($q);
		//echo $q."<br>";
		$rq = $conn->Execute($q);
		while(!$rq->EOF){
			$cpp = new relacion_pp_cp;
			$cpp->nrodoc = $rq->fields['nrodoc'];
			$cpp->descripcion = $rq->fields['descripcion'];
			$cpp->monto = $rq->fields['monto'];
			$cpp->fechadoc = $rq->fields['fechadoc'];
			$cpp->id_momento = $rq->fields['id_momento'];
			$cpp->nroref = $rq->fields['nroref'];
			$cpp->proveedor = $rq->fields['proveedor'];
			$cpp->status = $rq->fields['status'];
			$coleccion[] = $cpp;
			$rq->movenext();
		}
		return $coleccion;
	}
	
	function docsMontoGen($conn, 
								$idParcat, 
								$fechaDesde, 
								$fechaHasta){
		$q = "SELECT DISTINCT SUM(rm.monto) AS monto, td.id_momento_presupuestario AS id_momento ";
		$q.= "FROM puser.relacion_movimientos rm ";
		$q.= "INNER JOIN puser.movimientos_presupuestarios mp ON (replace(rm.nrodoc, '-ANULADO', '')=mp.nrodoc) ";
		$q.= "INNER JOIN puser.tipos_documentos td ON (mp.tipdoc=td.id) "; 
		$q.= "WHERE rm.id_parcat= $idParcat ";
		$q.= (!empty($fechaDesde)) ? "AND mp.fechadoc >= '".guardafecha($fechaDesde)."' " : "";
		$q.= (!empty($fechaHasta)) ? "AND mp.fechadoc <= '".guardafecha($fechaHasta)."' " : "";
		$q.= "GROUP BY id_momento ";
		$q.= "ORDER BY id_momento";
		
		//die($q);
		//echo $q."<br>";
		$rq = $conn->Execute($q);
		while(!$rq->EOF){
			$cpp = new relacion_pp_cp;
			$cpp->monto = $rq->fields['monto'];
			$cpp->id_momento = $rq->fields['id_momento'];
			$coleccion[] = $cpp;
			$rq->movenext();
		}
		//die(var_dump($coleccion));
		return $coleccion;
	}
}
?>
