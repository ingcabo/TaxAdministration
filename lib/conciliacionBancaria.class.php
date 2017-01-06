<?
class conciliacionBancaria{
	
	#PROPIEDADES#
	var $id;
	var $id_cta_banc;
	var $fecha_desde;
	var $fecha_hasta;
	var $fecha_conciliacion;
	var $descripcion;
	var $saldo_inicial_banco;
	var $saldo_final_banco;
	var $saldo_inicial_libro;
	var $saldo_final_libro;
	var $saldo_conciliado;
	var $saldo_transitorio;

	var $lista;
	var $lista_count;	

	var $cta_bancaria;

	#FUNCIONES#
	
	function get($conn, $id)
	{
		$q = "SELECT * FROM contabilidad.conciliacion WHERE id = $id ";
		$r = $conn->execute($q);
//		die(var_dump($r->EOF));
		if (!$r->EOF)
		{
			$this->id 						=	$r->fields['id'];
			$this->id_cta_banc			=	$r->fields['id_cta_banc'];
			$this->cta_bancaria  = new cuentas_bancarias;
			$this->cta_bancaria->get($conn, $this->id_cta_banc);
			
			$this->fecha_desde			=	date('d/m/Y', strtotime($r->fields['fecha_desde']));
			$this->fecha_hasta			=	date('d/m/Y', strtotime($r->fields['fecha_hasta']));
			$this->fecha_conciliacion	=	date('d/m/Y', strtotime($r->fields['fecha_conciliacion']));
			$this->descripcion			=	$r->fields['descripcion'];
			$this->saldo_inicial_banco	=	$r->fields['saldo_inicial_banco'];
			$this->saldo_final_banco	=	$r->fields['saldo_final_banco'];
			$this->saldo_inicial_libro	=	$r->fields['saldo_inicial_libro'];
			$this->saldo_final_libro	=	$r->fields['saldo_final_libro'];
			$this->saldo_conciliado		=	$r->fields['saldo_conciliado'];
			$this->saldo_transitorio	=	$r->fields['saldo_transitorio'];

			return true;	
		}
		else
		{
			return false;
		}
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
			$obj = new conciliacionBancaria;
			$obj->get($conn, $r->fields['id']);
			$lista[] = $obj;
			$r->movenext();
		}

		$this->lista = $lista;
		$this->lista_count = count($lista);
		
		return $lista;
	}
	
	function add($conn, $id_cta_banc, $fdesde, $fhasta, $fconciliacion, $descripcion, $saldo_inicial_banco, $saldo_final_banco, $saldo_inicial_libro, $saldo_final_libro, $saldo_conc, $saldo_trans, $jsonDet)
	{
		$q = "INSERT INTO contabilidad.conciliacion ";
		$q.= "(id_cta_banc, fecha_desde, fecha_hasta, fecha_conciliacion, descripcion, saldo_inicial_banco, saldo_final_banco, saldo_inicial_libro, saldo_final_libro, saldo_conciliado, saldo_transitorio) ";
		$q.= "VALUES ";
		$q.= "($id_cta_banc, '$fdesde', '$fhasta', '$fconciliacion', '$descripcion', $saldo_inicial_banco, $saldo_final_banco, $saldo_inicial_libro, $saldo_final_libro, $saldo_conc, $saldo_trans) ";

		//echo $q;
		
		$r = $conn->Execute($q);
		//die(var_dump($r));
		if($r !== false)
		{
			$q = "SELECT MAX(id) AS id FROM contabilidad.conciliacion ";
			$r = $conn->Execute($q);
			
			$this->setDetallesAsientos($conn, $r->fields['id'], $fconciliacion, $jsonDet);
			return true;
		}
		else
			return "add: ".$conn->ErrorMsg();
	}
	
	function set($conn, $id, $id_cta_banc, $fdesde, $fhasta, $fconciliacion, $descripcion, $saldo_inicial_banco, $saldo_final_banco, $saldo_inicial_libro, $saldo_final_libro, $saldo_conc, $saldo_trans)
	{
		$q = "UPDATE contabilidad.plan_cuenta SET id_cta_banc=$id_cta_banc, fecha_desde='$fdesde', fecha_hasta = '$fhasta', fecha_conciliacion= '$fconciliacion', descripcion='$descripcion', ";
		$q.= "saldo_inicial_banco = $saldo_inicial_banco, saldo_final_banco = $saldo_final_banco, saldo_inicial_libro = $saldo_inicial_libro, saldo_final_libro = $saldo_final_libro, saldo_conciliado = $saldo_conc, saldo_transitorio = $saldo_trans ";
		$q.= "WHERE id = $id";	
		//die($q);
		$r = $conn->Execute($q);
		if($r !== false)
			return true;
		else
			return $conn->ErrorMsg();
	}

	function del($conn, $id)
	{
		$asientos = $this->getAsientos($conn, $id);
		foreach($asientos as $asiento)
		{
			$asiento->setDatosConc('null', 'now', 'R', $asiento->id);
		}

		$q = "DELETE FROM contabilidad.conciliacion WHERE id = $id";
		$r = $conn->Execute($q);
		if($r !== false)
			$res = true;
		else
			$res = false;

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
		$q.= !empty($fecha_desde) ? "AND fecha_desde >= '$fecha_desde'  ":"";
		$q.= !empty($fecha_hasta) ? "AND fecha_hasta <= '$fecha_hasta'  ":"";
		if (trim($orden) != '')
			$q.= "ORDER BY $orden ";

		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		//die(var_dump($r));
		$lista = array();
		while(!$r->EOF)
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
	
	function total_registro_busqueda($conn, $id_cta_bac='', $descripcion='', $fecha_desde='', $fecha_hasta='', $from=0, $max=0)
	{
		/*if(empty($codcta) && empty($descripcion) && empty($ano))
			return false;*/
		$q = "SELECT id FROM contabilidad.conciliacion ";
		$q.= "WHERE  1=1 ";
		$q.= !empty($id_cta_banc) ? "AND id_cta_banc = '$id_cta_banc' ":"";
		$q.= !empty($descripcion) ? "AND descripcion ILIKE '%$descripcion%'  ":"";
		$q.= !empty($fecha_desde) ? "AND fecha_desde >= '$fecha_desde'  ":"";
		$q.= !empty($fecha_hasta) ? "AND fecha_hasta <= '$fecha_hasta'  ":"";
		//die($q);
		$r = $conn->Execute($q);
		$total = $r->RecordCount();
		
		return $total;
		
	}
	
	function getSaldo($conn, $id_cta_banc, $fecha, $op)
	{
		if ($op == 'D')
		{
			$menor = '<';
			$status = " = 'C' ";
		}
		else if ($op=='H')
		{
			$menor = '<=';
			$status = " NOT IN ('A', 'C') ";
		}
			
		$q = "SELECT COALESCE(SUM(debe), 0) AS debe, COALESCE(SUM(haber), 0) AS haber, id_cta FROM contabilidad.com_det ";
		$q.= "INNER JOIN contabilidad.com_enc ON (com_det.id_com = com_enc.id) ";
		$q.= "INNER JOIN finanzas.cuentas_bancarias ON (cuentas_bancarias.id_plan_cuenta = com_det.id_cta) ";
		$q.= "WHERE cuentas_bancarias.id = $id_cta_banc AND com_enc.fecha $menor '$fecha' AND com_enc.status $status ";
		$q.= "GROUP BY id_cta";
		$r = $conn->Execute($q);
		$q = "SELECT COALESCE(saldo_act, 0) AS saldo_act FROM contabilidad.consolidado ";
		$q.= "INNER JOIN contabilidad.plan_cuenta ON (consolidado.cod_cta = plan_cuenta.codcta) ";
		$q.= "INNER JOIN finanzas.cuentas_bancarias ON (cuentas_bancarias.id_plan_cuenta = plan_cuenta.id) ";
		$q.= "WHERE mes = (SELECT MAX(mes) FROM contabilidad.consolidado WHERE ano = (SELECT MAX(ano) FROM contabilidad.consolidado) AND status <> 'R') ";
		$q.= "AND cuentas_bancarias.id = $id_cta_banc ";
		$rs = $conn->Execute($q);
		if($rs->RecordCount() > 0)
			$saldo_actual = $rs->fields['saldo_act'];
		else{
			$q = "SELECT saldo_inicial FROM finanzas.cuentas_bancarias WHERE id = $id_cta_banc";
			$rs = $conn->Execute($q);
			$saldo_actual = $rs->fields['saldo_inicial'];
		}	
		return $saldo_act = $saldo_actual + $r->fields['debe'] - $r->fields['haber'];
	}
	
	function getAsientosConciliar($conn, $id_cta_banc, $fecha, $orden="id")
	{
		$q = "SELECT com_enc.id FROM contabilidad.com_enc ";
		$q.= "INNER JOIN contabilidad.com_det ON (com_det.id_com = com_enc.id) ";
		$q.= "INNER JOIN finanzas.cuentas_bancarias ON (cuentas_bancarias.id_plan_cuenta = com_det.id_cta) ";
		$q.= "WHERE cuentas_bancarias.id = $id_cta_banc AND com_enc.fecha <= '$fecha' AND com_enc.status NOT IN ('A', 'C') ";
		if (trim($orden) != "")
			$q.= "ORDER BY $orden ";
		
		$r = $conn->Execute($q);
		$lista = array();
		while(!$r->EOF)
		{
			$obj = new comprobante($conn);
			$obj->get($r->fields['id']);
			$lista[] = $obj;
			$r->movenext();
		}
		
		$this->lista = $lista;
		$this->lista_count = count($lista);
		
		return $lista;
	}
	
	function getAsientos($conn, $id_conc)
	{
		$q = "SELECT id FROM contabilidad.com_enc WHERE id_conciliacion = $id_conc ";
		$r = $conn->Execute($q);
		$lista = array();
		while (!$r->EOF)
		{
			$obj = new comprobante($conn);
			$obj->get($r->fields['id']);
			$lista[] = $obj;
			$r->movenext();
		}
		
		$this->lista = $lista;
		$this->lista = count($lista);
		
		return $lista;
	}
	
	function setDetallesAsientos($conn, $id_conc, $fconciliacion, $jsonDet)
	{
		$json = new Services_JSON();
		$jsonDet = $json->decode(stripslashes($jsonDet));
		$com = new comprobante($conn);
		
		foreach($jsonDet as $det)
		{
			$com->setDatosConc($id_conc, $fconciliacion, $det->status, $det->id);
		}
	}
	
	function getFechaDesde($conn, $id_cta_banc, $escEnEje)
	{
		$q = "SELECT fecha_hasta FROM contabilidad.conciliacion WHERE id = (SELECT MAX(id) AS id FROM contabilidad.conciliacion WHERE id_cta_banc = $id_cta_banc) ";
		$r = $conn->Execute($q);
		if ($r->EOF)
			//$fecha_desde = '01/01/'.escenarios::get_ano($conn, $escEnEje);
			// Al terminar el reconducido colocar la funcion original que es la de arriba
			$fecha_desde = '01/01/2007';
		else
		{
			$mes = substr($r->fields['fecha_hasta'], 5, 2);
			$anio = substr($r->fields['fecha_hasta'], 0, 4);
			$mes++;
			if ($mes == 13)
			{
				$mes = '01';
				$anio++;
			}
			
			$fecha_desde = '01/'.sprintf("%02d", $mes).'/'.$anio;
		}
		
		return $fecha_desde;
	}
	
	function getFechaHasta($conn, $id_cta_banc, $escEnEje)
	{
		$fecha_desde = $this->getFechaDesde($conn, $id_cta_banc, $escEnEje);
		$mes = (int)substr($fecha_desde, 3, 2);
		$anio = (int)substr($fecha_desde, 6);

		if ($mes == 2)
		{
			$dia = 28;
			if (!($anio%4) && (($anio%100) || !($anio%400)))
				$dia++;
		}
		else if ((!($mes%2) && $mes<8) || (($mes%2) && $mes>7))
			$dia = 30;
		else
			$dia = 31;
			
		return sprintf("%02d", $dia).'/'.sprintf("%02d", $mes).'/'.$anio;
	}
	
	function getSaldoInicialLibro($conn, $id_cta, $fecha_desde){
		$q = "SELECT a.id, a.saldo_inicial, SUM(b.debe) AS debe, SUM(b.haber) AS haber  FROM finanzas.cuentas_bancarias a ";
		$q.= "INNER JOIN contabilidad.com_det b ON (a.id_plan_cuenta = b.id_cta) ";
		$q.= "LEFT JOIN contabilidad.com_enc c ON (b.id_com = c.id) ";
		$q.= "WHERE a.id = $id_cta AND c.fecha < '".guardafecha($fecha_desde)."' ";
		$q.= "GROUP BY 1,2 ";
		$r = $conn->Execute($q);
		if(!$r)
			return false;
		else{
			if($r->RecordCount() > 0){
				$saldo_inicial = $r->fields['saldo_inicial'];
				$debe = $r->fields['debe'];
				$haber = $r->fields['haber'];
				$saldo = $saldo_inicial + $debe - $haber;
				
			} else {
				$q = "SELECT saldo_inicial FROM finanzas.cuentas_bancarias WHERE id = $id_cta";
				$r = $conn->Execute($q);
				$saldo = $r->fields['saldo_inicial'];
			}
			return $saldo;	
		}
			  
	}
}

?>
