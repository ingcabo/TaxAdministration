<?

class plan_cuenta{
	
	#PROPIEDADES#
	var $id;
	var $codcta;
	var $descripcion;
	var $ano;
	var $saldo_inicial;
	var $naturaleza;
	var $movim;
	var $nominal;
	var $id_escenario;
	var $id_acumuladora;
	var $codctaAux;
	
	#FUNCIONES#
	
	function get($conn, $codcta)
	{
		$q = "SELECT * FROM contabilidad.plan_cuenta WHERE codcta = $codcta ";
		$r = $conn->execute($q);
//		die(var_dump($r->EOF));
		if (!$r->EOF)
		{
			$this->id 					=	$r->fields['id'];
			$this->codcta 				=	$r->fields['codcta'];
			$this->descripcion		=	$r->fields['descripcion'];
			$this->ano					=	$r->fields['ano'];
			$this->saldo_inicial		=	$r->fields['saldo_inicial'];						
			$this->naturaleza			=	$r->fields['naturaleza'];
			$this->movim				=	$r->fields['movim'];
			$this->nominal				=	$r->fields['nominal'];
			$this->id_escenario		=	$r->fields['id_escenario'];
			$this->id_acumuladora	=	$r->fields['id_acumuladora'];
			$n = strlen($r->fields['codcta']);
			switch ($n){
			case 1:
				$this->codctaAux = $r->fields['codcta'];
				break;
			case 3:
				$this->codctaAux = substr($r->fields['codcta'],0,1).'.'.substr($r->fields['codcta'],1,2);
				break;
			case 5:
				$this->codtaAux = substr($r->fields['codcta'],0,1).'.'.substr($r->fields['codcta'],1,2).'.'.substr($r->fields['codcta'],3,2);
				break;
			case 7:
				$this->codctaAux = substr($r->fields['codcta'],0,1).'.'.substr($r->fields['codcta'],1,2).'.'.substr($r->fields['codcta'],3,2).'.'.substr($r->fields['codcta'],5,2);
				break;
			case 9:
				$this->codctaAux = substr($r->fields['codcta'],0,1).'.'.substr($r->fields['codcta'],1,2).'.'.substr($r->fields['codcta'],3,2).'.'.substr($r->fields['codcta'],5,2).'.'.substr($r->fields['codcta'],7,2);
				break;
			case 14:
				$this->codctaAux = substr($r->fields['codcta'],0,1).'.'.substr($r->fields['codcta'],1,2).'.'.substr($r->fields['codcta'],3,2).'.'.substr($r->fields['codcta'],5,2).'.'.substr($r->fields['codcta'],7,2).'.'.substr($r->fields['codcta'],9,5);
				break;
			}
			return true;	
		}
		else
		{
			return false;
		}
	}

	function get_by_id($conn, $id)
	{
		$q = "SELECT * FROM contabilidad.plan_cuenta WHERE id ='$id' ";
		$r = $conn->execute($q);
		//die(var_dump($q));
		if (!$r->EOF)
		{
			$this->id 					=	$r->fields['id'];
			$this->codcta 				=	$r->fields['codcta'];
			$this->descripcion		=	$r->fields['descripcion'];
			$this->ano					=	$r->fields['ano'];
			$this->saldo_inicial		=	$r->fields['saldo_inicial'];						
			$this->naturaleza			=	$r->fields['naturaleza'];
			$this->movim				=	$r->fields['movim'];
			$this->nominal				=	$r->fields['nominal'];
			$this->id_escenario		=	$r->fields['id_escenario'];
			$this->id_acumuladora	=	$r->fields['id_acumuladora'];
			$n = strlen($r->fields['codcta']);
			switch ($n){
			case 1:
				$this->codctaAux = $r->fields['codcta'];
				break;
			case 3:
				$this->codctaAux = substr($r->fields['codcta'],0,1).'.'.substr($r->fields['codcta'],1,2);
				break;
			case 5:
				$this->codtaAux = substr($r->fields['codcta'],0,1).'.'.substr($r->fields['codcta'],1,2).'.'.substr($r->fields['codcta'],3,2);
				break;
			case 7:
				$this->codctaAux = substr($r->fields['codcta'],0,1).'.'.substr($r->fields['codcta'],1,2).'.'.substr($r->fields['codcta'],3,2).'.'.substr($r->fields['codcta'],5,2);
				break;
			case 9:
				$this->codctaAux = substr($r->fields['codcta'],0,1).'.'.substr($r->fields['codcta'],1,2).'.'.substr($r->fields['codcta'],3,2).'.'.substr($r->fields['codcta'],5,2).'.'.substr($r->fields['codcta'],7,2);
				break;
			case 14:
				$this->codctaAux = substr($r->fields['codcta'],0,1).'.'.substr($r->fields['codcta'],1,2).'.'.substr($r->fields['codcta'],3,2).'.'.substr($r->fields['codcta'],5,2).'.'.substr($r->fields['codcta'],7,2).'.'.substr($r->fields['codcta'],9,5);
				break;
			}
			return true;	
		}
		else
			return false;
	}
	
	function get_all($conn,$orden="codcta"){
		
		$q = "SELECT * FROM contabilidad.plan_cuenta ";
		$q.= "ORDER BY $orden ";
		$r = $conn->Execute($q);
		while(!$r->EOF){
			$ue = new plan_cuenta;
			$ue->get($conn, $r->fields['codcta']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		//$this->total = $r->RecordCount();
		return $coleccion;
	}
	
	function add($conn, $codcta, $descripcion, $naturaleza, $movim, $nominal, $saldo_inicial, $id_escenario, $id_acumuladora)
	{
		$codcta = trim($codcta);
		$q = "SELECT id FROM contabilidad.plan_cuenta WHERE codcta = $codcta AND id_escenario = '$id_escenario' ";
		$r = $conn->Execute($q);
		if (!$r->EOF)
			return 'Duplicado';
			
		$lon = strlen($codcta);
		if ($lon==1 || $lon==3 || $lon==5 || $lon==7 || $lon==9 || $lon==14)
		{
			
			if ($lon==14)
				$cod_acum = substr($codcta, 0, $lon-5);
			else
				$cod_acum = substr($codcta, 0, $lon-2);
				
			$q = "SELECT * FROM contabilidad.plan_cuenta WHERE codcta = '$cod_acum' AND id_escenario = $id_escenario AND codcta <> '$codcta'";
			
			$r = $conn->Execute($q);
			if ($r->EOF)
				return 'NO EXISTE NIVEL INMEDIATAMENTE SUPERIOR';
			else if ($r->fields['movim'] == 'S')
				return 'ACUMULAR EN CUENTA DE MOVIMIENTO';
			else
				$id_acumuladora = $r->fields['id'];
		}
		else if ($lon!=1)
			return 'CODIGO CONTABLE INVALIDO';
		else if ($lon==1)
			$id_acumuladora = 'null';
		
			
		$q = "SELECT ano FROM puser.escenarios WHERE id = $id_escenario ";
		$r = $conn->Execute($q);
		$ano = $r->fields['ano'];
		
		$q = "INSERT INTO contabilidad.plan_cuenta ";
		$q.= "(codcta, descripcion, ano, naturaleza, movim, nominal, saldo_inicial, id_escenario, id_acumuladora) ";
		$q.= "VALUES ";
		$q.= "($codcta, '$descripcion', $ano, '$naturaleza', '$movim', '$nominal', $saldo_inicial, '$id_escenario', $id_acumuladora) ";
		
		//echo $q;
		
		$r = $conn->Execute($q);
		//die(var_dump($r));
		if($r !== false)
			return true;
		else
			return $conn->ErrorMsg();
	}
	
	function set($conn, $id, $codcta, $descripcion, $naturaleza, $movim, $nominal, $saldo_inicial, $id_escenario, $id_acumuladora)
	{
		$codcta = trim($codcta);
		$q = "SELECT id FROM contabilidad.plan_cuenta WHERE codcta = $codcta AND id_escenario = '$id_escenario' AND id <> $id";
		$r = $conn->Execute($q);
		if (!$r->EOF)
			return 'Duplicado';
			
		$lon = strlen($codcta);
		if ($lon==1 || $lon==3 || $lon==5 || $lon==7 || $lon==9 || $lon==14)
		{
			
			if ($lon==14)
				$cod_acum = substr($codcta, 0, $lon-5);
			else
				$cod_acum = substr($codcta, 0, $lon-2);
				
			$q = "SELECT * FROM contabilidad.plan_cuenta WHERE codcta = '$cod_acum' AND id_escenario = $id_escenario AND movim = 'N' AND id <> '$id'";
			$r = $conn->Execute($q);
			if ($r->EOF)
				return 'NO EXISTE NIVEL INMEDIATAMENTE SUPERIOR';
			else
				$id_acumuladora = $r->fields['id'];
		}
		else if ($lon!=1)
			return 'CODIGO CONTABLE INVALIDO';
		else if ($lon==1)
			$id_acumuladora = 'null';

		$q = "SELECT ano FROM puser.escenarios WHERE id = $id_escenario ";
		$r = $conn->Execute($q);
		$ano = $r->fields['ano'];

		$q = "UPDATE contabilidad.plan_cuenta SET codcta=$codcta, descripcion='$descripcion', ano=$ano, naturaleza = '$naturaleza',movim= '$movim',nominal = '$nominal', saldo_inicial= $saldo_inicial, id_escenario = '$id_escenario', id_acumuladora = $id_acumuladora ";
		$q.= "WHERE id=$id";	
		//die($q);
		$r = $conn->Execute($q);
		if($r !== false)
			return true;
		else
			return $conn->ErrorMsg();
	}

	function del($conn, $id)
	{
		$q = "SELECT id FROM contabilidad.relacion_cc_pp WHERE id_cuenta_contable = $id";
		$rs = $conn->Execute($q);
		
		$res = false;
		if ($rs->EOF)
		{
			$q = "DELETE FROM contabilidad.plan_cuenta WHERE id=$id";
			if($conn->Execute($q))
				$res = true;
		}
		else
			$res = 'Relacionado';
			
		return $res;
	}
	
	function buscar($conn, $codcta='', $descripcion='', $ano='', $from=0, $max=0, $orden="id"){
		/*if(empty($codcta) && empty($descripcion) && empty($ano))
			return false;*/
		$q = "SELECT * FROM contabilidad.plan_cuenta ";
		$q.= "WHERE  1=1 ";
		$q.= !empty($codcta) ? "AND codcta ILIKE '$codcta%' ":"";
		$q.= !empty($descripcion) ? "AND descripcion ILIKE '%$descripcion%'  ":"";
		$q.= !empty($ano) ? "AND ano = '$ano'  ":"";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		//die(var_dump($r));
		//$collection=array();
		while(!$r->EOF)
		{
			$ue = new plan_cuenta;
			$ue->get($conn, $r->fields['codcta']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		//die($coleccion);
		return $coleccion;
	}
	
	function total_registro_busqueda($conn, $codcta='', $descripcion='', $ano='', $max=0, $from=0, $orden="id"){
		/*if(empty($codcta) && empty($descripcion) && empty($ano))
			return false;*/
		$q = "SELECT * FROM contabilidad.plan_cuenta ";
		$q.= "WHERE  1=1 ";
		$q.= !empty($codcta) ? "AND codcta ILIKE '%$codcta%' ":"";
		$q.= !empty($descripcion) ? "AND descripcion ILIKE '%$descripcion%'  ":"";
		$q.= !empty($ano) ? "AND ano = '$ano'  ":"";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = $conn->Execute($q);
		$total = $r->RecordCount();
		return $total;
		
	}
	
	function buscaCuenta($conn, $descripcion = '', $id_cuenta='', $tipo ='', $escEnEje){
		$q = "SELECT id, descripcion, codcta FROM contabilidad.plan_cuenta WHERE id_escenario ='$escEnEje' AND movim='S'";
		$q.= ($tipo!=1) ? "AND id NOT IN (SELECT COALESCE(id_cuenta_contable::int8, 0) FROM contabilidad.relacion_cc_pp WHERE id_escenario = '$escEnEje' " .(!empty($id_cuenta) ? "AND id_cuenta_contable <> ".$id_cuenta : " ").") " : "";
		$q.= ($tipo!=2) ? "AND id NOT IN (SELECT COALESCE(id_plan_cuenta::int8, 0) FROM finanzas.cuentas_bancarias ".(!empty($id_cuenta) ? "WHERE id_plan_cuenta <> ".$id_cuenta : " ").") ": "";
		$q.= ($tipo!=3) ? "AND id NOT IN (SELECT COALESCE(cta_contable::int8, 0) FROM puser.proveedores ".(!empty($id_cuenta) ? "WHERE cta_contable <> ".$id_cuenta : " ").") " : "";
		$q.= ($tipo!=4) ? "AND id NOT IN (SELECT COALESCE(cuenta_contable::int8, 0) FROM finanzas.tipos_solicitud_sin_imp ".(!empty($id_cuenta) ? "WHERE cuenta_contable <> ".$id_cuenta : " ").") " : "";
		$q.= ($tipo!=5) ? "AND id NOT IN (SELECT COALESCE(cuenta_contable::int8, 0) FROM rrhh.concepto ".(!empty($id_cuenta) ? "WHERE (cuenta_contable <> ".$id_cuenta." OR cuenta_aporte <> ".$id_cuenta.")": " ").") " : "";
		$q.= ($tipo!=6) ? "AND id NOT IN (SELECT COALESCE(id_cta::int8, 0) FROM finanzas.retenciones_adiciones ".(!empty($id_cuenta) ? "WHERE id_cta <> ".$id_cuenta : " ").") " : "";
		$q.= !empty($descripcion) ? "AND descripcion ILIKE '%$descripcion%' " : "";
		$q.= "ORDER BY codcta::text ";
		//die($q);
		$r = $conn->Execute($q);
		if($r){
			while(!$r->EOF){
				$ue = new plan_cuenta;
				$ue->id = $r->fields['id'];	
				$ue->codcta = $r->fields['codcta'];
				$ue->descripcion = $r->fields['descripcion'];
				$coleccion[] = $ue;
				$r->movenext();
			}
			return $coleccion;
		}else{
			return 'No se consiguen registros';
		}	
		
	}
		
}


?>
