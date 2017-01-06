<?
class estadoCuenta{
	
	#PROPIEDADES#
	var $id;
	var $id_banco;
	var $cta_bancaria;
	var $fecha_desde;
	var $fecha_hasta;
	var $saldo_inicial_banco;
	var $saldo_final_banco;
	var $fecha;
	var $relacion;
	var $lista;
	var $lista_count;

	#FUNCIONES#
	
	function get($conn, $id)
	{
		$q = "SELECT * FROM contabilidad.estado_cuenta WHERE id = $id ";
		//die($q);
		$r = $conn->execute($q);
//		die(var_dump($r->EOF));
		if (!$r->EOF)
		{
			$this->id 					=	$r->fields['id'];
			$this->id_banco				=	$r->fields['id_banco'];
			$this->cta_bancaria  		=   $r->fields['id_cuenta'];
			$this->fecha_desde			=	muestraFecha($r->fields['fech_inicio']);
			$this->fecha_hasta			=	muestraFecha($r->fields['fech_fin']);
			$this->saldo_inicial_banco	=	$r->fields['saldo_inic'];
			$this->saldo_final_banco	=	$r->fields['saldo_fin'];
			$cb = new cuentas_bancarias; 
			$cb->get($conn, $r->fields['id_cuenta']);
			$this->cuenta = $cb;
			$this->getRelacion($conn, $r->fields['id']);
			return true;	
		}
		else
		{
			return false;
		}
	}

	function getRelacion($conn, $idEstadoCuenta)
	{
		$q = "SELECT * FROM contabilidad.relacion_estado_cuenta rec ";
		$q.= "WHERE rec.id_estado_cuenta = $idEstadoCuenta ";
		$q.= "ORDER BY fecha_doc ";
		$r = $conn->execute($q);
		//die(var_dump($q));
		while (!$r->EOF){
				
				$ec = new estadoCuenta;
				$ec->id_estado = $r->fields['id_estado_cuenta'];
				$ec->tipo_movimiento = $r->fields['tipo_documento'];
				$ec->num_documento = $r->fields['num_documento'];
				$ec->fecha = muestraFecha($r->fields['fecha_doc']);
				$ec->debito = $r->fields['debitos'];
				$ec->credito = $r->fields['creditos'];
				
				$coleccion[] = $ec;
				$r->movenext();
				
			}
			
		$this->relacion = new Services_JSON();
		$this->relacion = is_array($coleccion) ? $this->relacion->encode($coleccion) : false;
	}
	
	function get_all($conn, $orden="id"){
		
		$q = "SELECT id FROM contabilidad.estado_cuenta ";
		if (trim($orden) != '')
			$q.= "ORDER BY $orden ";
	
		$r = $conn->Execute($q);
		$lista = array();
		while(!$r->EOF)
		{
			$obj = new estadoCuenta;
			$obj->get($conn, $r->fields['id']);
			$lista[] = $obj;
			$r->movenext();
		}

		$this->lista = $lista;
		$this->lista_count = count($lista);
		
		return $lista;
	}
	
	function add($conn, $id_banco, $id_cuenta, $fecha_inicio, $fecha_fin, $saldo_inicial, $saldo_final, $jsonDet)
	{
		$q = "INSERT INTO contabilidad.estado_cuenta ";
		$q.= "(id_banco, id_cuenta, fech_inicio, fech_fin, saldo_inic, saldo_fin) ";
		$q.= "VALUES ";
		$q.= "($id_banco, $id_cuenta, '$fecha_inicio', '$fecha_fin', $saldo_inicial, $saldo_final) ";

		//die($q);
		
		$r = $conn->Execute($q);
		//die(var_dump($r));
		//$r = true;
		if($r !== false)
		{
			$q = "SELECT MAX(id) AS id FROM contabilidad.estado_cuenta ";
			$r = $conn->Execute($q);
			
			$this->addRelacionEstadoCuenta($conn, $r->fields['id'], $jsonDet);
			return true;
		}
		else
			return "add: ".$conn->ErrorMsg();
	}
	
	function set($conn, $id, $id_banco, $id_cuenta, $fecha_inicio, $fecha_fin, $saldo_inicial, $saldo_final, $jsonDet)
	{
		$q = "UPDATE contabilidad.estado_cuenta SET id_banco = $id_banco, id_cuenta = $id_cuenta, fech_inicio = '".guardaFecha($fecha_inicio)."', fech_fin = '".guardaFecha($fecha_fin)."', ";
		$q.= "saldo_inic = $saldo_inicial, saldo_fin = $saldo_final ";
		$q.= "WHERE id = $id";	
		//die($q);
		$r = $conn->Execute($q);
		if($r !== false){
			$this->delRelacionEstadoCuenta($conn, $id);
			$this->addRelacionEstadoCuenta($conn, $id, $jsonDet);
			return true;
		}else{
			return $conn->ErrorMsg();
		}
	}

	function del($conn, $id)
	{
		$q = "DELETE FROM contabilidad.estado_cuenta WHERE id = $id";
		$r = $conn->Execute($q);
		if($r !== false)
			$res = true;
		else
			$res = false;

		return $res;
	}
	
	function buscar($conn, $id_banco='', $id_cuenta='', $fecha_desde='', $fecha_hasta='', $from=0, $max=0, $orden="id")
	{
		if(empty($id_banco) && empty($id_cuenta) && empty($fecha_desde) && empty($fecha_hasta))
			return false;
			
		$q = "SELECT id FROM contabilidad.estado_cuenta ";
		$q.= "WHERE  1=1 ";
		$q.= !empty($id_banco) ? "AND id_banco = '$id_cta_banc' ":"";
		$q.= !empty($id_cuenta) ? "AND id_cuenta = '$id_cuenta' ":"";
		$q.= !empty($fecha_desde) ? "AND fech_inicio >= '$fecha_desde'  ":"";
		$q.= !empty($fecha_hasta) ? "AND fech_fin <= '$fecha_hasta'  ":"";
		if (trim($orden) != '')
			$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		//die(var_dump($r));
		$lista = array();
		while(!$r->EOF)
		{
			$obj = new estadoCuenta;
			$obj->get($conn, $r->fields['id']);
			$lista[] = $obj;
			$r->movenext();
		}
	
		$this->lista = $lista;
		$this->lista_count = count($lista);
		
		return $lista;
	}
	
	function total_registro_busqueda($conn, $id_banco='', $id_cta_bac='', $fecha_desde='', $fecha_hasta='', $from=0, $max=0)
	{
		/*if(empty($codcta) && empty($descripcion) && empty($ano))
			return false;*/
		$q = "SELECT id FROM contabilidad.estado_cuenta ";
		$q.= "WHERE  1=1 ";
		$q.= !empty($id_banco) ? "AND id_banco = '$id_banco' ":"";
		$q.= !empty($id_cuenta) ? "AND id_cta_banc = '$id_cta_banc' ":"";
		$q.= !empty($fecha_desde) ? "AND fech_inicio >= '$fecha_desde'  ":"";
		$q.= !empty($fecha_hasta) ? "AND fech_fin <= '$fecha_hasta'  ":"";
		if (trim($orden) != '')
			$q.= "ORDER BY $orden ";
		//die($q);
		$r = $conn->Execute($q);
		$total = $r->RecordCount();
		
		return $total;
		
	}
	
	
	
	function addRelacionEstadoCuenta($conn, $idEstado, $aEstado)
	{
		$JsonRec = new Services_JSON();
		$JsonRec = $JsonRec->decode(str_replace("\\","",$aEstado));
		//die(var_dump($JsonRec->edoCta));
		if(is_array($JsonRec->edoCta)){
			foreach($JsonRec->edoCta as $estado){
				$q = "INSERT INTO  contabilidad.relacion_estado_cuenta ";
				$q.= "( id_estado_cuenta, tipo_documento, num_documento, fecha_doc, debitos, creditos) ";
				$q.= "VALUES ";
				$q.= "('$idEstado', '".$estado[0]."', '".$estado[1]."', '".guardaFecha($estado[2])."', ".$estado[3].", ".$estado[4].") ";
				//$q.= "($idEstado, '$estado[0]', '$estado[1]', '".guardaFecha($estado[2])."', $estado[3], $estado[4]) ";
				//die($q);
				$r = $conn->Execute($q) or die($q);
			}
		} 
		if($r)
			return true;
		else
			return false;
	}
	
	function delRelacionEstadoCuenta($conn, $id){
		$q = "DELETE FROM contabilidad.relacion_estado_cuenta WHERE id_estado_cuenta ='$id'";
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}	
	
	function getFechaDesde($conn, $id_cta_banc, $escEnEje)
	{
		$q = "SELECT fech_fin FROM contabilidad.estado_cuenta WHERE id = (SELECT MAX(id) AS id FROM contabilidad.estado_cuenta WHERE id_cuenta = $id_cta_banc) ";
		//die($q);
		$r = $conn->Execute($q);
		//$num = $r->RecordCount();
			//die("numero: ".$num);
		if ($r->fields['fech_fin'] == NULL){
			
			//$fecha_desde = '01/01/'.escenarios::get_ano($conn, $escEnEje);
			// Al terminar el reconducido colocar la funcion original que es la de arriba
			$fecha_desde = '01/01/2008';
		} else
		{
			//die("aqui no debe entrar");
			$mes = substr($r->fields['fech_fin'], 5, 2);
			$anio = substr($r->fields['fech_fin'], 0, 4);
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
