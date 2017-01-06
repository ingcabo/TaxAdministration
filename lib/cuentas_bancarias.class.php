<?
class cuentas_bancarias{

# PROPIEDADES #

	var $id;
	var $nro_cuenta;
	var $banco;
	var $tipo_cuenta;
	var $clasificacion_cuenta;
	var $plan_cuenta;
	var $entidad;
	var $fuente_financiamiento;
	var $fines_cuenta;
	var $saldo_inicial;
	var $debitos;
	var $creditos;
	var $fecha;
	var $desc_cuenta;
	
	var $total;

# METODOS #

	function get($conn, $id){
		$q = "SELECT CB.*, PC.descripcion AS desc_cuenta FROM finanzas.cuentas_bancarias CB ";
		$q.= "INNER JOIN contabilidad.plan_cuenta PC ON (CB.id_plan_cuenta = PC.id) ";
		$q.= "WHERE CB.id='$id'";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$bn = new banco;
			$bn->get($conn,$r->fields['id_banco']);
			$this->banco = $bn;
			$tc = new tipos_cuentas_bancarias;
			$tc->get($conn,$r->fields['id_tipo_cuenta']);
			$this->tipo_cuenta = $tc;
			$cc = new clasificacion_cuenta;
			$cc->get($conn,$r->fields['id_clasificacion_cuenta']);
			$this->clasificacion_cuenta = $cc;
			$this->plan_cuenta = $r->fields['id_plan_cuenta']; # TODAVIA NO SE HA ELABORADO #
			$en = new entidades;
			$en->get($conn,$r->fields['id_entidad']);
			$this->entidad = $en; 
			$ff = new financiamiento;
			$ff->get($conn,$r->fields['id_fuente_financiamiento']);
			$this->fuente_financiamiento = $ff;
			$this->fines_cuenta = $r->fields['id_fines_cuenta']; # TODAVIA NO SE A ELABORADO #
			$this->saldo_inicial = $r->fields['saldo_inicial'];
			//$this->debitos = $r->fields['debitos'];
			//$this->creditos = $r->fields['creditos'];
			$this->actualiza_movimientos($conn,$r->fields['id_plan_cuenta']);
			$this->nro_cuenta = $r->fields['nro_cuenta'];
			$this->desc_cuenta = $r->fields['desc_cuenta'];
																					
			return true;
		}else
			return false;
	}
	
	function get_all($conn, $from=0, $max=0,$orden="id"){
		$q = "SELECT * FROM finanzas.cuentas_bancarias ";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new cuentas_bancarias;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}
	
	function add($conn, $nro_cuenta, $id_banco, $id_tipo_cuenta, $id_clasificacion_cuenta, $id_plan_cuenta, 
				$id_fuente_financiamiento, $id_fines_cuenta, $saldo_inicial, $debitos, 
				 $credito){
		$q = "INSERT INTO finanzas.cuentas_bancarias ";
		$q.= "(id_banco,
			   nro_cuenta,	 
			   id_tipo_cuenta, 
			   id_clasificacion_cuenta, 
			   id_plan_cuenta, 
			   id_fuente_financiamiento, 
			   id_fines_cuenta, 
			   saldo_inicial, 
			   debitos, 
			   creditos) ";
		$q.= " VALUES ";
		$q.= "($id_banco,
			   '$nro_cuenta', 
			   $id_tipo_cuenta, 
			   $id_clasificacion_cuenta, 
			   $id_plan_cuenta, 
			   $id_fuente_financiamiento, 
			   $id_fines_cuenta, 
			   $saldo_inicial, 
			   0, 
			   0) ";

		//die($q);
		
		if($conn->Execute($q)){
		
			return true;
		}
		else{
		
			return false;
		}	
		
	}
	
	function set($conn, $id, $nro_cuenta, $id_banco, $id_tipo_cuenta, $id_clasificacion_cuenta, $id_plan_cuenta, 
				 $id_fuente_financiamiento, $saldo_inicial, $debitos, 
				 $credito){
		$q  = "UPDATE finanzas.cuentas_bancarias SET ";
		$q .= "id_banco = $id_banco, ";
		$q .= "nro_cuenta = '$nro_cuenta', ";
		$q .= "id_tipo_cuenta = $id_tipo_cuenta, ";
		$q .= "id_clasificacion_cuenta = $id_clasificacion_cuenta, ";
		$q .= "id_plan_cuenta = $id_plan_cuenta, ";
		$q .= "id_fuente_financiamiento = $id_fuente_financiamiento, ";
		$q .= "saldo_inicial = $saldo_inicial ";
		//$q .= "debitos = $debitos, "; ESTOS CAMPOS NO DEBERIAN CARGARSE DE LA CONTABILIDAD
		//$q .= "creditos = $credito ";
		$q .= "WHERE id=$id";	
		//die($q);
		$r = $conn->Execute($q);
		if($r !== false)
			return true;
		else
			return false;
	}
	
	function del($conn, $id){
		$q = "DELETE FROM finanzas.cuentas_bancarias WHERE id='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	#ESTA FUNCION TRAE LA CUENTAS BANCARIAS POR EL ID DEL BANCO#
	function cuentasxbanco($conn, $id_banco, $id_tipo_cuenta, $conciliacion=""){
	
		$q = "SELECT * FROM finanzas.cuentas_bancarias WHERE id_banco=$id_banco ";
		if(!empty($conciliacion))
			$q.= "AND id_tipo_cuenta=$id_tipo_cuenta";
//		echo $q;
		$r = $conn->execute($q);
		
		while(!$r->EOF){
		
			$cb = new cuentas_bancarias;
			$cb->get($conn, $r->fields['id']);
			$coleccion[] = $cb;
			$r->movenext();
		
		}
		return $coleccion;
	}
	
	function cuentasxbancoxchequera($conn, $id_banco, $id_tipo_cuenta, $conciliacion=""){
	
		$q = "SELECT  cb.* FROM finanzas.cuentas_bancarias as cb INNER JOIN finanzas.control_chequera as cc ";
		$q .="ON cb.id=cc.nro_cuenta WHERE cb.id_banco=$id_banco AND cc.activa = 1";
		if(!empty($conciliacion))
			$q.= "AND id_tipo_cuenta=$id_tipo_cuenta";
		//die($q);
		$r = $conn->execute($q);
		
		while(!$r->EOF){
		
			$cb = new cuentas_bancarias;
			$cb->get($conn, $r->fields['id']);
			$coleccion[] = $cb;
			$r->movenext();
		
		}
		return $coleccion;
	}
	
	function buscar($conn, $nro_cuenta='', $banco='', $from=0, $max=0)
	{
		$q = "SELECT id FROM finanzas.cuentas_bancarias WHERE 1=1 ";
		if ($nro_cuenta != '')
			$q.= " AND nro_cuenta ILIKE '%$nro_cuenta%' ";
		if ($banco != '' && $banco != '0')
			$q.= " AND id_banco = $banco ";
			
//		return $q;
		$rs = ($max!=0) ? $conn->SelectLimit($q, $max, $from):$conn->Execute($q);
		$coleccion = array();
		while (!$rs->EOF)
		{
			$obj = new cuentas_bancarias;
			$obj->get($conn, $rs->fields['id']);
			$coleccion[] = $obj;
			$rs->movenext();
		}
		
		return $coleccion;
	}
	
	function total_registro_busqueda($conn, $nro_cuenta, $banco)
	{
		$q = "SELECT id FROM finanzas.cuentas_bancarias WHERE 1=1 ";
		if ($nro_cuenta != '')
			$q.= " AND nro_cuenta ILIKE '%$nro_cuenta%' ";
		if ($banco != '' && $banco != '0')
			$q.= " AND id_banco = $banco ";

		$rs = $conn->Execute($q);
		return $rs->RecordCount();			
	}
	
	function actualiza_movimientos($conn, $id_plan_cuenta){
		$q = "SELECT SUM(debe) AS creditos, SUM(haber) AS debitos FROM contabilidad.com_det WHERE id_cta = $id_plan_cuenta";
		$r = $conn->Execute($q);
		if($r){
			$this->debitos = $r->fields['debitos'];
			$this->creditos = $r->fields['creditos'];
			return true;
		}
		return false;
	}
}

?>