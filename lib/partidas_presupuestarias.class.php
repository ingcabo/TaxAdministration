<?
class partidas_presupuestarias{

	// Propiedades

	var $id;
	var $id_escenario;
	var $escenario;	
	var $descripcion;
	var $detalle;
	var $gastos_inv;
	var $id_contraloria;
	var $presupuesto_original;
	var $aumentos;
	var $disminuciones;
	var $compromisos;
	var $causados;
	var $pagados;
	var $disponible;
	var $ano;
	var $madre;
	var $estAnterior;
	var $estAjusAnterior;
	var $baseCalculo;
	var $porcMax;

	var $total;
	
	// OBTENEMOS LOS DATOS PARA EL REPORTE
	/*var $idp;
	var $descripcionp;
	var $madrep;
	var $suma;*/

	function get($conn, $id, $id_escenario){
		try{
			$q = "SELECT * FROM partidas_presupuestarias WHERE id='$id' AND id_escenario = '$id_escenario' ";
			//die($q);
			$r = $conn->Execute($q);
			$this->id = $r->fields['id'];
			$this->id_escenario = $r->fields['id_escenario'];
			$this->escenario = escenarios::get_descripcion($conn, $r->fields['id_escenario']);
			$this->descripcion = $r->fields['descripcion'];
			$this->detalle = $r->fields['detalle'];
			$this->gastos_inv = $r->fields['gastos_inv'];
			$this->id_contraloria = $r->fields['id_contraloria'];
			//$this->presupuesto_original = $r->fields['presupuesto_original'];
			//$this->aumentos = $r->fields['aumentos'];
			//$this->disminuciones = $r->fields['disminuciones'];
			//$this->compromisos = $r->fields['compromisos'];
			//$this->causados = $r->fields['causados'];
			//$this->pagados = $r->fields['pagados'];
			//$this->disponible = $r->fields['disponible'];
			$this->ano = $r->fields['ano'];
			$this->madre = $r->fields['madre'];
			$this->check_ing = $r->fields['check_ing'];
			$this->ingreso = $r->fields['ingreso'];
			$this->check_trans = $r->fields['check_transferencia'];
			$this->estAnterior = $r->fields['est_ano_anterior'];
			$this->estAjusAnterior = $r->fields['est_ajustada_ano_anterior'];
			$this->baseCalculo = $r->fields['base_calculo'];
			$this->porcMax = $r->fields['porc_max'];
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

	function get_all($conn, $from=0, $max=0,$orden="id_escenario, id"){
		try{
			$q = "SELECT * FROM partidas_presupuestarias ";
			$q.= "ORDER BY $orden ";
			//die($q);
			$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
			$collection=array();
			while(!$r->EOF){
				$ue = new partidas_presupuestarias;
				$ue->get($conn, $r->fields['id'], $r->fields['id_escenario']);
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
				return ERROR_CATCH_VUK;
			else
				return ERROR_CATCH_GENERICO;
		}
	}

	function add($conn, 
							$id,
							$id_escenario, 
							$descripcion,
							$detalle,
							$gastos_inv,
							$id_contraloria,
							$ano,
							$madre,
							$check_ing,
							$ingreso,
							$estAnt = '',
							$estAjusAnt = '',
							$baseCalc = '',
							$porcMax = ''){
		try{
			if(empty($ingreso))
			{
				$ingreso = 0;
			}
			$msj = $this->setMadres($id,$id_escenario,$conn);
			//die($msj);
			if($msj == 'ok'){
				$q = "INSERT INTO puser.partidas_presupuestarias ";
				$q.= "(id, id_escenario, descripcion, detalle, gastos_inv, id_contraloria, ano, madre, check_ing, ingreso, est_ano_anterior, est_ajustada_ano_anterior, base_calculo, porc_max) ";
				$q.= "VALUES ('$id', '$id_escenario', '$descripcion', '$detalle', $gastos_inv, '$id_contraloria', '$ano', 0, '$check_ing', '$ingreso', '$estAnt', '$estAjusAnt', '$baseCalc', '$porcMax') ";
				//die($q);
				$r = $conn->Execute($q);
				if($r)
					return REG_ADD_OK;
				else
					return ERROR_ADD;
			}else{
				return $msj;
			}
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

	function set($conn, $id_nuevo,
				$id,
				$id_escenario, 
				$descripcion,
				$detalle,
				$gastos_inv,
				$id_contraloria,
				$ano,
				$madre,
				$check_ing,
				$ingreso,
				$estAnt = '',
				$estAjusAnt = '',
				$baseCalc = '',
				$porcMax = ''){
		try{
			$q = "UPDATE partidas_presupuestarias SET  descripcion = '$descripcion', detalle = '$detalle', gastos_inv = $gastos_inv, ";
			$q.= "id_contraloria = '$id_contraloria', ano = '$ano', madre = '$madre', check_ing = $check_ing, ingreso = '$ingreso', ";
			$q.= "est_ano_anterior = $estAnt, est_ajustada_ano_anterior = $estAjusAnt, base_calculo = '$baseCalc', porc_max = $porcMax ";
			$q.= "WHERE id='$id' AND id_escenario = '$id_escenario' ";
			//die($q);
			$conn->Execute($q);
			return REG_SET_OK;
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

	function del($conn, $id, $id_escenario){
		try{
			$q = "DELETE FROM partidas_presupuestarias WHERE id='$id' AND id_escenario = '$id_escenario' ";
			$conn->Execute($q);
			return REG_DEL_OK;
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

	function del_escenario($conn, $id){
		try{
			$q = "DELETE FROM partidas_presupuestarias WHERE id_escenario='$id'";
			$conn->Execute($q);
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
	
	function get_all_by_esc($conn, $id_escenario, $madre='0', $from=0, $max=0,$orden="id", $relacion='',$descripcion=''){
		try{
			//die($madre);
			$q = "SELECT * FROM puser.partidas_presupuestarias WHERE id_escenario = '$id_escenario' ";
			//$q.= "AND id NOT LIKE '30%' ";
			/*if(empty($madre))
				$q.= " AND (madre <> '1' OR madre is null) ";*/
			if ($relacion)
				$q.= " AND id NOT IN (SELECT id_partida_presupuestaria FROM contabilidad.relacion_cc_pp WHERE id_escenario = '$id_escenario') ";
			if ($descripcion)
				$q.= " AND descripcion ILIKE '%$descripcion%' ";
			$q.= "ORDER BY $orden ";
			//die($q);
			$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
			$collection=array();
			while(!$r->EOF){
				$ue = new partidas_presupuestarias;
				$ue->get($conn, $r->fields['id'], $r->fields['id_escenario']);
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

	function set_by_esc($conn, $id_escenario, $factor){
		try{
			$q = "UPDATE partidas_presupuestarias SET ";
			$q.= "presupuesto_original = presupuesto_original * $factor";
			$q.= "WHERE id_escenario='$id_escenario' ";
			//die($q);
			$conn->Execute($q);
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

	function add_esc($conn, 
							$id,
							$id_escenario, 
							$descripcion,
							$detalle,
							$gastos_inv,
							$id_contraloria,
							$madre,
							$check_ing,
							$ingreso = '0',
							$estAnt = '0',
							$estAjusAnt = '0',
							$baseCalc = '',
							$porcMax = '0',
							$ano){
			try{
			$gastos_inv = ($gastos_inv == 't') ? "true" : "false";
			$ingreso = (empty($ingreso)) ? 0 : $ingreso;
			$estAnt = (empty($estAnt)) ? 0 : $estAnt;
			$estAjusAnt = (empty($estAjusAnt)) ? 0 : $estAjusAnt;
			$porcMax = (empty($porcMax)) ? 0 : $porcMax;
			$q = "INSERT INTO puser.partidas_presupuestarias ";
			$q.= "(id, id_escenario, descripcion, detalle, gastos_inv, id_contraloria, ano, madre, check_ing, ingreso, est_ano_anterior, est_ajustada_ano_anterior, base_calculo, porc_max) ";
			$q.= "VALUES ('$id', '$id_escenario', '$descripcion', '$detalle', $gastos_inv, '$id_contraloria', '$ano', $madre, '$check_ing', $ingreso, $estAnt, $estAjusAnt, '$baseCalc', $porcMax) ";
			//die($q);
			$conn->Execute($q);
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

	function get_all_by_cp($conn, $cp, $escEnEje, $id_partida='', $from=0, $max=0, $nombre=""){
		try{
			$q = "SELECT relacion_pp_cp.id_partida_presupuestaria AS id ";
			$q.= "FROM puser.relacion_pp_cp ";
			if ($nombre != "")
				$q.= "INNER JOIN puser.partidas_presupuestarias ON (relacion_pp_cp.id_partida_presupuestaria = partidas_presupuestarias.id AND relacion_pp_cp.id_escenario = partidas_presupuestarias.id_escenario) ";

			$q.= "WHERE ";
			$q.= "relacion_pp_cp.id_categoria_programatica = '$cp' ";
			$q.= "AND relacion_pp_cp.id_escenario = '$escEnEje' ";
			if (!empty($id_partida))
				$q.="AND relacion_pp_cp.id_partida_presupuestaria LIKE '$id_partida%' ";
			if ($nombre != "")
				$q.= " AND partidas_presupuestarias.descripcion ILIKE '%$nombre%' ";
			$q.= "ORDER BY id";
			//die($q);
			$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
			$collection=array();
			while(!$r->EOF){
				$ue = new partidas_presupuestarias;
				$ue->get($conn, $r->fields['id'], $escEnEje);
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

	function getAllParpreNomina($conn, $cp, $escEnEje){
		try{
			$q = "SELECT relacion_pp_cp.id_partida_presupuestaria AS id ";
			$q.= "FROM relacion_pp_cp ";
			$q.= "WHERE ";
			$q.= "relacion_pp_cp.id_categoria_programatica = '$cp' ";
			$q.= "AND substr(relacion_pp_cp.id_partida_presupuestaria, 1, 3) = '401' ";
			$q.= "AND relacion_pp_cp.id_escenario = '$escEnEje' ";
			//die($q);
			$r = $conn->Execute($q);
			$collection=array();
			while(!$r->EOF){
				$ue = new partidas_presupuestarias;
				$ue->get($conn, $r->fields['id'], $escEnEje);
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
	
	function buscar($conn, $id, $id_escenario, $descripcion, $max=10, $from=1, $orden="id_escenario, id"){
		try{
			if(empty($id_escenario) && empty($descripcion) && empty($id))
				return false;
			$q = "SELECT * FROM partidas_presupuestarias ";
			$q.= "WHERE  1=1 ";
			$q.= !empty($id) ? "AND id = '$id'  ":"";
			$q.= !empty($id_escenario) ? "AND id_escenario = '$id_escenario'  ":"";
			$q.= !empty($descripcion) ? "AND descripcion ILIKE '%$descripcion%'  ":"";
			$q.= "ORDER BY $orden ";
			//die($q. "; max: ".$max. " ; from:" . $from);
			$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
			$collection=array();
			while(!$r->EOF){
				$ue = new partidas_presupuestarias;
				$ue->get($conn, $r->fields['id'], $r->fields['id_escenario']);
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
	
	function total_registro_busqueda($conn, $id_escenario, $descripcion, $orden="id_escenario, id"){
		if(empty($id_escenario) && empty($descripcion))
			return false;
		$q = "SELECT * FROM puser.partidas_presupuestarias ";
		$q.= "WHERE  1=1 ";
		$q.= !empty($id_escenario) ? "AND id_escenario = '$id_escenario'  ":"";
		$q.= !empty($descripcion) ? "AND descripcion = '$descripcion'  ":"";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		$total = $r->RecordCount();

		return $total;
	}
	
	function get_reporte($conn, $EscEnEje,$asig=''){
	try{
		$q="SELECT DISTINCT puser.partidas_presupuestarias.descripcion, ";
		$q.="puser.partidas_presupuestarias.id, puser.partidas_presupuestarias.madre, ";
		$q.="Sum(puser.relacion_pp_cp.presupuesto_original) AS tpresupuesto, ";
		$q.="puser.partidas_presupuestarias.id_escenario ";
		$q.= !empty($asig) ? ",(SELECT SUM(relacion_pp_cp.presupuesto_original) AS monto1 FROM puser.relacion_pp_cp WHERE relacion_pp_cp.id_escenario = '1111' AND relacion_pp_cp.id_partida_presupuestaria = puser.partidas_presupuestarias.id  AND relacion_pp_cp.id_asignacion = 1) AS coordinado,
			(SELECT SUM(relacion_pp_cp.presupuesto_original) AS monto1 FROM puser.relacion_pp_cp WHERE relacion_pp_cp.id_escenario = '1111' AND  relacion_pp_cp.id_partida_presupuestaria = puser.partidas_presupuestarias.id AND relacion_pp_cp.id_asignacion = 2) AS fides,
			(SELECT SUM(relacion_pp_cp.presupuesto_original) AS monto1 FROM puser.relacion_pp_cp WHERE relacion_pp_cp.id_escenario = '1111' AND relacion_pp_cp.id_partida_presupuestaria = puser.partidas_presupuestarias.id AND relacion_pp_cp.id_asignacion = 3) AS laee,
			(SELECT SUM(relacion_pp_cp.presupuesto_original) AS monto1 FROM puser.relacion_pp_cp WHERE relacion_pp_cp.id_escenario = '1111' AND relacion_pp_cp.id_partida_presupuestaria = puser.partidas_presupuestarias.id AND relacion_pp_cp.id_asignacion = 4) AS ordinario " : "";
		$q.="FROM puser.relacion_pp_cp ";
		$q.="Right Outer Join puser.partidas_presupuestarias ON puser.relacion_pp_cp.id_partida_presupuestaria = puser.partidas_presupuestarias.id AND puser.relacion_pp_cp.id_escenario=puser.partidas_presupuestarias.id_escenario ";
		$q.="WHERE ";
		$q.="puser.partidas_presupuestarias.id_escenario = '$EscEnEje' AND partidas_presupuestarias.id LIKE '4%' ";
		$q.="GROUP BY ";
		$q.="puser.partidas_presupuestarias.descripcion, ";
		$q.="puser.partidas_presupuestarias.id, ";
		$q.="puser.partidas_presupuestarias.madre, ";
		$q.="puser.partidas_presupuestarias.id_escenario ";
		$q.="ORDER BY puser.partidas_presupuestarias.id";
		//die($q);
		$r= $conn->Execute($q);
		//var_dump($r);
		while(!$r->EOF){
			$pp = new partidas_presupuestarias;
				$pp->id= $r->fields['id'];
				$pp->descripcion= $r->fields['descripcion'];
				$pp->madre= $r->fields['madre'];
				$pp->ano= $r->fields['ano'];
				$pp->presupuesto= $r->fields['tpresupuesto'];
				$pp->compromiso= $r->fields['compromiso'];
				$pp->causado= $r->fields['causado'];
				$pp->pagado= $r->fields['pagado'];
				$pp->coordinado= $r->fields['coordinado'];
				$pp->fides= $r->fields['fides'];
				$pp->laee= $r->fields['laee'];
				$pp->ordinario= $r->fields['ordinario'];
				//die("aqui ".$pp->madre);
				$pp->disponible= $r->fields['disponible'];
				$coleccion[] = $pp;
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
	
	function get_reporte_ingresos($conn, $EscEnEje){
	try{
			$q = "SELECT "; 
			$q.= "substring(id,1,3)::varchar as partida,substring(id,4,2)::varchar as general, ";
			$q.= "substring(id,6,2)::varchar as especifica,substring(id,8,2)::varchar as sub_esp1, ";
			$q.= "substring(id,10,2)::varchar as sub_esp2,substring(id,12,2)::varchar as sub_esp3, "; 
			$q.= "descripcion, ingreso ";
			$q.= "FROM ";  
			$q.= "puser.partidas_presupuestarias "; 
			$q.= "WHERE "; 
			$q.= "puser.partidas_presupuestarias.id LIKE '30%' and id_escenario = '$EscEnEje' AND ingreso != 0 ";
			$q.= "order by id";
			//die($q);
			$r= $conn->Execute($q);
			while(!$r->EOF)
			{
				$pp = new partidas_presupuestarias;
				$pp->id = $r->fields['id'];
				$pp->descripcion = $r->fields['descripcion'];
				$pp->monto = $r->fields['ingreso'];
				$coleccion[] = $pp;
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
	
	function setMadres($id,$id_escenario,$conn){

		
		$flag = false;
		$aux = strstr($id,'000000');
		$aux2 = strlen($aux);
		//echo $aux2.'<br>';
		//ESTA BANDERA VA A SER VERDADERA SI LA PARTIDA SE CREA POR PRIMERA VEZ Y ES MADRE EJ: 4010000000000
		if($aux2==10)
			$flag = true;
		
		if($aux2 < 6)
			$q = "SELECT id FROM puser.partidas_presupuestarias WHERE id = '".substr($id,0,7).'000000'."' AND id_escenario = '$id_escenario'";
		else if($aux2 >= 6 && $aux2 <= 7)
			$q = "SELECT id FROM puser.partidas_presupuestarias WHERE id = '".substr($id,0,5).'00000000'."' AND id_escenario = '$id_escenario'";
		else if($aux2 >=8 && $aux2 <= 10)
			$q = "SELECT id FROM puser.partidas_presupuestarias WHERE id = '".substr($id,0,3).'0000000000'."' AND id_escenario = '$id_escenario'";
		else
			$q = "SELECT id FROM puser.partidas_presupuestarias WHERE id = '".$id."' AND id_escenario = '$id_escenario'";
		//echo $aux2.'<br>';
		//echo $q.'<br>';
		//die();
		$r = $conn->Execute($q);
		if($r->RecordCount()>0){
			$sql = "SELECT id FROM puser.relacion_pp_cp WHERE id_partida_presupuestaria = '".$r->fields['id']."' AND id_escenario = '$id_escenario'";
			//echo $sql.'<br>';
			//die();
			$row = $conn->Execute($sql);
			if($row->RecordCount() > 0){
				$msj = ERROR_PART_MADRE_MOV;
				return $msj;
			}else{
				$partMadre = $r->fields['id'];
				$q1 = "UPDATE puser.partidas_presupuestarias SET madre = 1 WHERE id = '".$partMadre."'";
				//echo $q1.'<br>';
				$r1 = $conn->Execute($q1);
				$msj = "ok";
				return $msj;
			}
		}else{
			if($flag){
				$msj = "ok";
				return $msj;
			}else{	
				$msj = ERROR_PART_MADRE_NO_EXISTE;
				return $msj;
			}
		}
	}
	
	function get_reporte_sectores($conn,$id_escenario){
	try{
			$q = "SELECT A.id as partida, A.descripcion as descripcion, A.ano, A.madre as madre, sum(B.presupuesto_original) as monto, substring(B.id_categoria_programatica,1,2) as sector "; 
			$q.= "FROM puser.partidas_presupuestarias as A ";
			$q.= "INNER JOIN puser.relacion_pp_cp AS B ON (A.id = B.id_partida_presupuestaria AND A.id_escenario = B.id_escenario) ";
			$q.= "where A.id_escenario = '1111' AND  A.id LIKE '4%' AND A.madre = '0' ";
			$q.= "group by A.id, sector,  A.descripcion, A.ano, A.madre ";
			$q.= "order by 1,6 ";
			//die($q);
			$r= $conn->Execute($q);
			$coleccion = array();
			while(!$r->EOF)
			{
				$coleccion[$r->fields['partida']][$r->fields['sector']] = $r->fields['monto'];
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
	
}
?>
