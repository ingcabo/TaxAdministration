<?
class conciliacionBancaria2{
	
	#PROPIEDADES#
	var $id;
	var $id_banco;
	var $id_cuenta;
	var $fecha_inic;
	var $fecha_fin;
	var $fecha_conciliacion;
	var $descripcion;
	var $saldo_inic;
	var $saldo_fin;
	var $saldo_inic_libro;
	var $saldo_final_libro;
	var $saldo_inic_banco;
	var $saldo_final_banco;
	var $saldo_conciliado_libro;
	var $saldo_conciliado_banco;
	var $saldo_transito_banco;
	var $saldo_transito_libro;
	var $asientosConciliados;
	var $creditos;
	var $debitos;
	//var $relacion;

	var $lista;
	var $lista_count;	

	var $cta_bancaria;
	
	//var $relacionConcEdoCta;

	#FUNCIONES#
	
	function get($conn, $id)
	{		
		$q = "SELECT a.id, a.fecha_conciliacion, a.descripcion, a.saldo_inicial_banco, a.saldo_final_banco, a.saldo_inicial_libro, a.saldo_final_libro, a.saldo_conciliado_banco, a.saldo_transito_banco, ";
		$q.= "a.saldo_conciliado_libro, a.saldo_transito_libro, ec.id_banco, ec.id_cuenta, ec.fech_inicio, ec.fech_fin, ec.saldo_inic, ec.saldo_fin ";
		$q.= "FROM contabilidad.conciliacion a ";
		$q.= "INNER JOIN contabilidad.estado_cuenta ec ON (a.id = ec.id_conciliacion)"; 
		$q.= "WHERE a.id = $id ";		
		//die($q);
		$r = $conn->execute($q);
//		die(var_dump($r->EOF));
		if (!$r->EOF)
		{
			$this->id 					=	$r->fields['id'];
			$this->id_banco				=	$r->fields['id_banco'];
			$this->id_cuenta			=	$r->fields['id_cuenta'];
			$this->cta_bancaria  = new cuentas_bancarias;
			$this->cta_bancaria->get($conn, $this->id_cuenta);			
			$this->fecha_desde			=	date('d/m/Y', strtotime($r->fields['fech_inicio']));
			$this->fecha_hasta			=	date('d/m/Y', strtotime($r->fields['fech_fin']));
			$this->fecha_conciliacion	=	date('d/m/Y', strtotime($r->fields['fecha_conciliacion']));
			$this->descripcion			=	$r->fields['descripcion'];
			$this->saldo_inico			=	$r->fields['saldo_inic'];
			$this->saldo_fin			=	$r->fields['saldo_fin'];
			$this->saldo_inicial_libro	=	$r->fields['saldo_inicial_libro'];
			$this->saldo_final_libro	=	$r->fields['saldo_final_libro'];
			$this->saldo_inicial_banco	=	$r->fields['saldo_inicial_banco'];
			$this->saldo_final_banco	=	$r->fields['saldo_final_banco'];
			$this->saldo_conciliado_libro=	$r->fields['saldo_conciliado_libro'];
			$this->saldo_conciliado_banco=	$r->fields['saldo_conciliado_banco'];
			$this->saldo_transito_banco =	$r->fields['saldo_transito_banco'];
			$this->saldo_transito_libro	=	$r->fields['saldo_transito_libro'];
			//$this->asientosConciliados($conn, $id);  

			return true;	
		}
		else
		{
			return false;
		}
	}
	
	function asientosConciliados($conn, $idConciliacion){
		$q = "SELECT a.tipo_documento, a.num_documento, a.fecha_doc,  (a.debitos + a.creditos) as monto, c.numcom, c.fecha "; 
		$q.= "FROM contabilidad.relacion_estado_cuenta a ";
		$q.= "INNER JOIN contabilidad.com_enc c ON (a.num_documento = c.num_doc2 AND a.tipo_documento=c.origen) ";
		$q.= "INNER JOIN contabilidad.com_det b ON (c.id = b.id_com AND a.id_conciliacion = b.id_conciliacion) ";
		$q.= "WHERE a.id_conciliacion = $idConciliacion";				
		//die($q);
		$r = $conn->Execute($q);
		$lista = array();
		$i = 0;
		while (!$r->EOF)
		{
			$lista[$i]['tipo_doc'] = $r->fields['tipo_documento'];
			$lista[$i]['num_doc'] = $r->fields['num_documento'];
			$lista[$i]['fecha_doc'] = $r->fields['fecha_doc'];
			$lista[$i]['monto'] = $r->fields['monto'];
			$lista[$i]['numcom'] = $r->fields['numcom'];
			$lista[$i]['fecha_libro'] = $r->fields['fecha'];
			$r->movenext();
			$i++;
		}
		/*$relacion = new Services_JSON();
		$relacion = is_array($lista) ? $relacion->encode($lista) : false;*/
		return ($lista);
	}

	function get_by_id_cta($conn, $id_cta)
	{
		$q = "SELECT id FROM contabilidad.conciliacion WHERE id_cta_banc ='$id_cta' ";
		$r = $conn->execute($q);
		//die(var_dump($q));
		$lista = array();
		while (!$r->EOF)
		{
			$obj = new conciliacionBancaria;
			$obj->get($conn, $r->fields['id']);
			$lista[] = $obj;
			$r->movenext();
		}
		
		$this->lista = $lista;
		$this->lista_count = count($lista);
		
		return $lista;
	}
	
	function get_all($conn, $orden="id"){
		
		$q = "SELECT id FROM contabilidad.conciliacion ";
		if (trim($orden) != '')
			$q.= "ORDER BY $orden ";
	
		$r = $conn->Execute($q);
		$lista = array();
		while(!$r->EOF)
		{
			$obj = new conciliacionBancaria2;
			$obj->get($conn, $r->fields['id']);
			$lista[] = $obj;
			$r->movenext();
		}

		$this->lista = $lista;
		$this->lista_count = count($lista);
		
		return $lista;
	}
	
	function add($conn, $id_cta_banc, $fdesde, $fhasta, $fconciliacion, $descripcion, $saldo_inicial_banco, $saldo_final_banco, $saldo_inicial_libro, $saldo_final_libro, $saldo_conc_banco, $saldo_conc_libro, $saldo_trans_banco, $saldo_trans_libro, $jsonDet, $estCta, $ctaContable)
	{
		$q = "INSERT INTO contabilidad.conciliacion ";
		$q.= "(id_cta_banc, fecha_desde, fecha_hasta, fecha_conciliacion, descripcion, saldo_inicial_banco, saldo_final_banco, saldo_inicial_libro, saldo_final_libro, saldo_conciliado_banco, saldo_conciliado_libro, saldo_transito_banco, saldo_transito_libro) ";
		$q.= "VALUES ";
		$q.= "($id_cta_banc, '$fdesde', '$fhasta', '$fconciliacion', '$descripcion', $saldo_inicial_banco, $saldo_final_banco, $saldo_inicial_libro, $saldo_final_libro, $saldo_conc_banco, $saldo_conc_libro, $saldo_trans_banco, $saldo_trans_libro) ";
		//die($q);
		$r = $conn->Execute($q);
		if($r !== false)
		{				
			$q = "SELECT MAX(id) AS id FROM contabilidad.conciliacion ";
			$r = $conn->Execute($q);
			$id_conc = $r->fields['id'];
			$q = "UPDATE contabilidad.estado_cuenta SET id_conciliacion = $id_conc WHERE id = $estCta";
			//die($q);
			$r = $conn->Execute($q);
			//die(var_dump($jsonDet));
			$this->setDetallesAsientos($conn, $id_conc, $fconciliacion, $jsonDet, $estCta, $ctaContable);
			return true;
		}
		else
			return "add: ".$conn->ErrorMsg();
	}
	
	function del($conn, $id)
	{
		//Valida si es la ultima conciliacion asociada a una cuenta bancaria
		$q = "SELECT max(id) as ultimo FROM contabilidad.conciliacion WHERE id = $id ";
		$q.= "AND id_cta_banc = (SELECT id_cta_banc FROM contabilidad.conciliacion WHERE id = $id)";
		//die($q);
		$r = $conn->Execute($q);	
		if($id == $r->fields['ultimo']){	
			$q = "UPDATE contabilidad.relacion_estado_cuenta SET id_conciliacion = null WHERE id_conciliacion = $id";
			$r = $conn->Execute($q);
			
			$q = "UPDATE contabilidad.com_det SET id_conciliacion = null, fecha_conciliacion = null WHERE id_conciliacion = $id";
			$r = $conn->Execute($q);		
			
			$q = "UPDATE contabilidad.estado_cuenta SET id_conciliacion = null WHERE id_conciliacion = $id";
			$r = $conn->Execute($q);
			
			$q = "DELETE FROM contabilidad.conciliacion WHERE id = $id";
			$r = $conn->Execute($q);
			if($r !== false)
				$res = true;
			else
				$res = false;
			}
		else{
			$res = false;
		}
		return $res;		
	}
	
	function buscar($conn, $id_cta_banc='', $descripcion='', $fecha_desde='', $fecha_hasta='', $from=0, $max=0, $orden="id")
	{
		if(empty($id_cta_banc) && empty($descripcion) && empty($fecha_desde) && empty($fecha_hasta))
			return false;
			
		$q = "SELECT id FROM contabilidad.conciliacion ";
		$q.= "WHERE  1=1 ";
		$q.= !empty($id_cta_banc) ? "AND id_cta_banc = '$id_cta_banc' ":"";
		$q.= !empty($descripcion) ? "AND descripcion ILIKE '%$descripcion%'  ":"";
		$q.= !empty($fecha_desde) ? "AND fecha_desde >= '".guardafecha($fecha_desde)."'  ":"";
		$q.= !empty($fecha_hasta) ? "AND fecha_hasta <= '".guardafecha($fecha_hasta)."'  ":"";
		if (trim($orden) != '')
			$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		//die(var_dump($r));
		$lista = array();
		while(!$r->EOF)
		{
			$obj = new conciliacionBancaria2;
			$obj->get($conn, $r->fields['id']);
			$lista[] = $obj;
			$r->movenext();
		}
	
		$this->lista = $lista;
		$this->lista_count = count($lista);
		
		return $lista;
	}
	
	function total_registro_busqueda($conn, $id_cta_bac='', $descripcion='', $fecha_desde='', $fecha_hasta='', $from=0, $max=0)
	{
		/*if(empty($codcta) && empty($descripcion) && empty($ano))
			return false;*/
		$q = "SELECT id FROM contabilidad.conciliacion ";
		$q.= "WHERE  1=1 ";
		$q.= !empty($id_cta_banc) ? "AND id_cta_banc = '$id_cta_banc' ":"";
		$q.= !empty($descripcion) ? "AND descripcion ILIKE '%$descripcion%'  ":"";
		$q.= !empty($fecha_desde) ? "AND fecha_desde >= '".guardafecha($fecha_desde)."'  ":"";
		$q.= !empty($fecha_hasta) ? "AND fecha_hasta <= '".guardafecha($fecha_hasta)."'  ":"";
		//die($q);
		$r = $conn->Execute($q);
		$total = $r->RecordCount();
		
		return $total;
		
	}
	
	function getAsientosConciliar($conn, /*$edoCuenta ,*/ $idEdosCuenta) //Pasar la cuenta contable
	{
		$q = "SELECT a.id, a.tipo_documento, a.num_documento, a.fecha_doc,  (a.debitos + a.creditos) as monto, c.numcom, c.fecha ";
		$q.= "FROM contabilidad.relacion_estado_cuenta a ";
		$q.= "INNER JOIN contabilidad.estado_cuenta b ON (a.id_estado_cuenta = b.id) ";
		$q.= "INNER JOIN contabilidad.com_enc c ON (c.origen = a.tipo_documento AND c.num_doc2 = a.num_documento) ";
		$q.= "INNER JOIN finanzas.cuentas_bancarias d ON (b.id_cuenta = d.id) ";
		$q.= "INNER JOIN contabilidad.com_det e ON (c.id = e.id_com AND d.id_plan_cuenta = e.id_cta AND a.debitos = e.haber AND e.debe= a.creditos AND e.id_conciliacion is null) ";
		$q.= "WHERE b.id IN ($idEdosCuenta) AND a.fecha_doc <= b.fech_fin AND c.fecha <= b.fech_fin ";
		$q.= "AND NOT  EXISTS (SELECT id FROM contabilidad.com_enc enc WHERE enc.status='A' AND enc.num_doc2=c.num_doc2 AND enc.num_doc=c.num_doc)";
		$q.= "ORDER BY a.fecha_doc";
		//die($q);
		$r = $conn->Execute($q);
		$coleccion = array();
		while(!$r->EOF)
		{
			$conc = new conciliacionBancaria2;
			$conc->idRel = $r->fields['id'];
			$conc->tipo_doc = $r->fields['tipo_documento'];
			$conc->num_doc = $r->fields['num_documento'];
			$conc->fecha_doc = $r->fields['fecha_doc'];
			$conc->monto = $r->fields['monto'];
			$conc->numcom  = $r->fields['numcom'];
			$conc->fecha_libro = $r->fields['fecha'];
			$coleccion[] = $conc;
			$r->movenext();
		}
		//$this->relacionConcEdoCta = new Services_JSON();
		//$this->relacionConcEdoCta = is_array($lista) ? $this->relacionConcEdoCta->encode($lista) : false;
		
		return $coleccion;
	}
	
	
	function setDetallesAsientos($conn, $id_conc, $fconciliacion, $jsonDet, $estCta, $ctaContable)
	{
		$json = new Services_JSON();
		$jsonDet = $json->decode(stripslashes($jsonDet));
		$com = new comprobante($conn);
		foreach($jsonDet as $conc)
		{
		//var_dump($conc);
			foreach($conc as $det){
			$com->setDatosConc($id_conc, $fconciliacion, $det[0], $det[1], $det[4], $estCta, $ctaContable);
			}
		}
	}
	
	
	function getSaldoInicialLibro($conn, $idCtaContable, $fechaIni, $fechaFin, $mes, $ano){
		/*$q = "SELECT  plan_cuenta.codcta, plan_cuenta.saldo_inicial, consolidado.saldo_ant, consolidado.saldo_act, SUM( com_det.debe) AS debe, SUM(com_det.haber) AS haber  ";
		$q.= "FROM contabilidad.plan_cuenta  ";
		$q.= "LEFT JOIN contabilidad.com_det ON (plan_cuenta.id = com_det.id_cta)  ";
		$q.= "LEFT JOIN contabilidad.com_enc ON (com_det.id_com = com_enc.id AND com_enc.fecha >= '$fechaIni' AND com_enc.fecha <= '$fechaFin' )  ";
		$q.= "LEFT JOIN contabilidad.consolidado ON (codcta = cod_cta AND consolidado.ano = $ano AND consolidado.mes = $mes)  ";
		$q.= "WHERE 1=1 AND plan_cuenta.id = $idCtaContable  ";
		$q.= "GROUP BY plan_cuenta.codcta, plan_cuenta.saldo_inicial, consolidado.saldo_ant, consolidado.saldo_act  ";*/

		$q = "SELECT  plan_cuenta.id, plan_cuenta.saldo_inicial, consolidado.saldo_ant, consolidado.saldo_act, SUM( com_det.debe) AS debe, SUM(com_det.haber) AS haber ";
		$q.= "FROM contabilidad.plan_cuenta ";
		$q.= "INNER JOIN contabilidad.com_det ON (plan_cuenta.id = com_det.id_cta) ";
		$q.= "INNER JOIN contabilidad.com_enc ON (com_det.id_com = com_enc.id) ";
		$q.= "LEFT JOIN contabilidad.consolidado ON (codcta = cod_cta AND consolidado.ano = $ano AND consolidado.mes = $mes) ";
		$q.= "WHERE 1=1 AND com_det.id_cta = $idCtaContable AND com_enc.fecha >= '$fechaIni' AND com_enc.fecha <= '$fechaFin' "; //AND com_enc.status = 'R' ";
		$q.= "GROUP BY plan_cuenta.id, plan_cuenta.saldo_inicial, consolidado.saldo_ant, consolidado.saldo_act ";
		//die($q);
		$r = $conn->Execute($q);
		
		if($r){
			if($r->fields['saldo_inicial']==null){
				$q = "SELECT  plan_cuenta.id, plan_cuenta.saldo_inicial ";
				$q.= "FROM contabilidad.plan_cuenta ";			
				$q.= "WHERE plan_cuenta.id = $idCtaContable ";
				$r = $conn->Execute($q);
			}
			$lista['codCuenta'] = $r->fields['id'];
			$lista['saldoInicial'] = $r->fields['saldo_inicial'];
			$lista['saldoAnterior'] = $r->fields['saldo_ant'];
			$lista['saldoActual'] = $r->fields['saldo_act'];
			$lista['debe'] = empty($r->fields['debe']) ? 0 : $r->fields['debe'];
			$lista['haber'] = empty($r->fields['haber']) ? 0 : $r->fields['haber'];
			return $lista;
		}else
			return false;
		
	}	
	
	function partEdoCtanoLibro($conn,$idEdoCta,$idCta,$fecha){//Falta utilizar la fecha o solo con IdoEdoCta es suficiente
		$q = "SELECT a.tipo_documento, a.num_documento, a.fecha_doc, a.debitos, a.creditos ";
		$q.= "FROM contabilidad.relacion_estado_cuenta a ";
		$q.= "INNER JOIN contabilidad.estado_cuenta b ON (a.id_estado_cuenta = b.id) ";
		$q.= "WHERE b.id IN ($idEdoCta) AND a.id NOT IN (SELECT rec.id ";
		$q.= "FROM contabilidad.relacion_estado_cuenta rec ";
		$q.= "INNER JOIN contabilidad.estado_cuenta ec ON (rec.id_estado_cuenta = ec.id) ";
		$q.= "INNER JOIN contabilidad.com_enc c ON (c.origen = rec.tipo_documento AND c.num_doc2 = rec.num_documento)  "; 
		$q.= "INNER JOIN finanzas.cuentas_bancarias d ON (ec.id_cuenta = d.id)  ";
		$q.= "INNER JOIN contabilidad.com_det e ON (c.id = e.id_com AND d.id_plan_cuenta = e.id_cta AND rec.debitos = e.haber AND e.debe= rec.creditos)  ";
		$q.= "WHERE ec.id IN ($idEdoCta) AND c.fecha <= ec.fech_fin )	";	
		
		$r = $conn->Execute($q);
		$coleccion = array();
		while(!$r->EOF)
		{
			$conc = new conciliacionBancaria2;
			$conc->tipo_doc = $r->fields['tipo_documento'];
			$conc->num_doc = $r->fields['num_documento'];
			$conc->fecha_doc = $r->fields['fecha_doc'];
			$conc->debitos = $r->fields['debitos'];
			$conc->creditos = $r->fields['creditos'];
			$coleccion[] = $conc;
			$r->movenext();
		}
		return $coleccion;	
	}
	
	function partLibronoEdoCta($conn, $idCtaContable, $idEdoCta, $fecha, $fechaIni){
		$q = "SELECT a.origen, a.numcom, a.num_doc2, a.fecha, b.debe ,b.haber ";
		$q.= "FROM contabilidad.com_enc a ";
		$q.= "INNER JOIN contabilidad.com_det b ON (a.id = b.id_com) ";
		$q.= "WHERE a.fecha <= '".guardaFecha($fecha)."' AND a.fecha >= '".guardaFecha($fechaIni)."' AND b.id_cta = $idCtaContable AND a.num_doc2 NOT IN (SELECT rec.num_documento ";
		$q.= "FROM contabilidad.relacion_estado_cuenta rec ";
		$q.= "WHERE rec.id_estado_cuenta IN ($idEdoCta) AND a.origen = rec.tipo_documento AND a.num_doc2 = rec.num_documento AND rec.debitos = b.haber AND b.debe= rec.creditos AND b.id_conciliacion is null) ";
		$q.= "AND NOT EXISTS (SELECT id FROM contabilidad.com_enc enc WHERE enc.status='A' AND enc.num_doc2=a.num_doc2)";
		//die($q);
		$r = $conn->Execute($q);
		$coleccion = array();
		while(!$r->EOF)
		{
			//die($r->fields['origen']);
			$conc = new conciliacionBancaria2;
			$conc->tipo_doc = $r->fields['origen'];
			$conc->numcom = $r->fields['numcom'];
			$conc->num_doc = $r->fields['num_doc2'];
			$conc->fecha_doc = $r->fields['fecha'];
			$conc->debitos = $r->fields['haber'];
			$conc->creditos = $r->fields['debe'];
			$coleccion[] = $conc;
			$r->movenext();
		}
		
		return $coleccion;	
	}
	
	function mesesAnteriores($conn, $idEdoCta, $idCtaBan){
		$q = "SELECT  fech_inicio, id FROM contabilidad.estado_cuenta "; 
		$q.= "WHERE id_cuenta = $idCtaBan AND id <= $idEdoCta ";
		$q.= "GROUP BY id, fech_inicio ";
		$q.= "ORDER BY id DESC ";
		$q.= "LIMIT 6";
		$r = $conn->Execute($q);
		$EdosCta = array();
		while(!$r->EOF)
		{
			$EdosCta[] = $r->fields['id'];
			$r->movenext();
		}
		return $EdosCta;
	}
	
	function reportePartEdoCtanoLibro($conn,$idConciliacion,$idCta,$fecha){
		$q = "SELECT a.tipo_documento, a.fecha_doc, a.num_documento, a.debitos, a.creditos ";
		$q.= "FROM contabilidad.relacion_estado_cuenta as a ";
		$q.= "INNER JOIN contabilidad.estado_cuenta as b ON a.id_estado_cuenta = b.id ";
		$q.= "WHERE (a.id_conciliacion > $idConciliacion OR a.id_conciliacion is null)  AND b.id_cuenta = $idCta AND b.fech_inicio <= '".guardafecha($fecha)."'";
		//die($q);
		$r = $conn->Execute($q);
		$coleccion = array();
		while(!$r->EOF)
		{
			$conc = new conciliacionBancaria2;
			$conc->tipo_doc = $r->fields['tipo_documento'];
			$conc->num_doc = $r->fields['num_documento'];
			$conc->fecha_doc = $r->fields['fecha_doc'];
			$conc->debitos = $r->fields['debitos'];
			$conc->creditos = $r->fields['creditos'];
			$coleccion[] = $conc;
			$r->movenext();
		} 
		return $coleccion;	
	}
	
	function reportepartLibronoEdoCta($conn,$idConciliacion,$idCtaCtb,$fecha){
		$q = "SELECT  a.origen , a.fecha, a.numcom, a.num_doc2, b.debe, b.haber ";  
		$q.= "FROM contabilidad.com_enc as a ";
		$q.= "INNER JOIN contabilidad.com_det as b ON a.id = b.id_com ";
		$q.= "WHERE (b.id_conciliacion > $idConciliacion OR b.id_conciliacion is null)  AND b.id_cta = $idCtaCtb ";
		$q.= "AND date_part('month', a.fecha)  = date_part('month', timestamp '".guardafecha($fecha)."') ";
		$q.= "AND a.num_doc NOT IN (SELECT num_doc FROM contabilidad.com_enc WHERE status = 'A' AND date_part('month', fecha) = date_part('month', timestamp '".guardafecha($fecha)."')) "; 
		//die($q);
		$r = $conn->Execute($q);
		$coleccion = array();
		while(!$r->EOF)
		{
			$conc = new conciliacionBancaria2;
			$conc->tipo_doc = $r->fields['origen'];
			$conc->numcom = $r->fields['numcom'];
			$conc->num_doc = $r->fields['num_doc2'];
			$conc->fecha_doc = $r->fields['fecha'];
			$conc->debitos = $r->fields['haber'];
			$conc->creditos = $r->fields['debe'];
			$coleccion[] = $conc;
			$r->movenext();
		}
		return $coleccion;	
	}	
	
}

?>
