<?
class retenciones_adiciones{

	// Propiedades

	var $id;
	var $abreviatura;
	var $descripcion;
	var $ctaContable;
	var $condicion;
	var $porcentaje;
	var $sustra;
	var $fijaVariable;
	var $tipoPersona;
	var $ctaPresup;
	var $porcRet;
	var $es_iva;
	var $expresion;
	var $desc_cuenta;
	## VARIABLE SE USA PARA CALCULAR EL MONTO DEL SUSTRAENDO EN CASO DE QUE SEA EXPRESADA EN UNID TRAIBUTARIA O DIRECTO EN Bs.
	var $sustraendo;

	var $total;

	function get($conn, $id){
		$q = "SELECT RA.*, PC.descripcion AS desc_cuenta, ";
		$q.= "CASE WHEN factor = 1 THEN sustra ";
		$q.= "WHEN factor = 2 THEN sustra  * (SELECT monto FROM publicidad.unidad_tributaria WHERE status = 1) ";
		$q.= "END AS sustraendo ";
		$q.= "FROM finanzas.retenciones_adiciones RA ";
		$q.= "INNER JOIN contabilidad.plan_cuenta PC ON (RA.id_cta = PC.id) ";
		$q.= "WHERE RA.id= '$id'";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->abreviatura = $r->fields['abrevi'];
			$this->descripcion = $r->fields['descri'];
			$this->ctaContable = $r->fields['id_cta'];
			$this->condicion = $r->fields['condic'];
			$this->porcentaje = $r->fields['porcen'];
			$this->sustra = $r->fields['sustra'];
			$this->fijaVariable = $r->fields['tipofv'];
			$this->tipoPersona = $r->fields['tipper'];
			$this->ctaPresup = $r->fields['ctapre'];
			$this->porcRet = $r->fields['porcret'];
			$this->es_iva = $r->fields['es_iva'];
			$this->expresion = $r->fields['factor'];
			$this->sustraendo = $r->fields['sustraendo'];
			$pc = new plan_cuenta;
			$pc->get_by_id($conn,$r->fields['id_cta']);
			$this->desc_cuenta = $pc; 
			return true;
		}else
			return false;
	}

	function getAll($conn, $orden="id", $iva=""){
		$q = "SELECT * FROM finanzas.retenciones_adiciones ";
		$q.= !empty($iva) ? "WHERE es_iva <> '1' " : "";
		$q.= "ORDER BY $orden ";
		$r = $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new retenciones_adiciones;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, 
		$id, 
		$abreviacion,
		$descripcion,
		$ctaContable,
		$condicion,
		$porcentaje,
		$sustraendo,
		$fijaVariable,
		$tipoPersona,
		$ctaPresup,
		$porcret,
		$es_iva,
		$expresion){
		$es_iva = !empty($es_iva) ? $es_iva : 0;
		$q = "INSERT INTO finanzas.retenciones_adiciones ";
		$q.= "(id, abrevi, descri, id_cta, condic, porcen, sustra, tipofv, tipper, ctapre, porcret, es_iva, factor) ";
		$q.= "VALUES ";
		$q.= "('$id', '$abreviacion', '$descripcion', '$ctaContable', '$condicion', '$porcentaje', '$sustraendo', '$fijaVariable', '$tipoPersona', '$ctaPresup', $porcret, $es_iva, '$expresion') ";
		//die($q);
		$rs = $conn->Execute($q);
		if($rs !== false)
			return true;
		else
			return $conn->ErrorMsg();//false;
	}

	function set($conn, 
		$id_nuevo, 
		$id, 
		$abreviacion,
		$descripcion,
		$ctaContable,
		$condicion,
		$porcentaje,
		$sustraendo,
		$fijaVariable,
		$tipoPersona,
		$ctaPresup,
		$porcret,
		$es_iva,
		$expresion){
		$q = "UPDATE finanzas.retenciones_adiciones SET id = '$id_nuevo', abrevi='$abreviacion', descri='$descripcion', ";
		$q.= "id_cta='$ctaContable', condic='$condicion', porcen='$porcentaje', sustra='$sustraendo', ";
		$q.= "tipofv='$fijaVariable', tipper='$tipoPersona', ctapre='$ctaPresup', porcret = $porcret, es_iva = '$es_iva', factor = '$expresion' ";	
		$q.= "WHERE id='$id' ";	
		//die($q);
		
		$rs = $conn->Execute($q);
		if($rs !== false)
			return true;
		else
			return $conn->ErrorMsg();//false;
	}

	function del($conn, $id){
		$q = "DELETE FROM finanzas.retenciones_adiciones WHERE id='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	function buscar($conn, $id, $descripcion, $orden="id")
	{
		$q = "SELECT * FROM finanzas.retenciones_adiciones ";
		$q.= "WHERE  1=1 ";
		$q.= !empty($id) ? "AND id= '$id'  ":"";
		$q.= !empty($descripcion) ? "AND descri ILIKE '%$descripcion%'  ":"";
		$q.= "ORDER BY $orden ";
		//die($q);
		if(!$r = $conn->Execute($q))
			return false;
		$collection=array();
		while(!$r->EOF){
			$o = new retenciones_adiciones;
			$o->get($conn, $r->fields['id']);
			$coleccion[] = $o;
			$r->movenext();
		}
		return $coleccion;
	}
	
	function get_retencion_iva($conn){
		$q = "SELECT id, porcret, id_cta, condic, porcen, (porcen::char(2)||'% retencion '||porcret::char(3)||'%')::varchar AS descripcion ";
		$q.= "FROM finanzas.retenciones_adiciones ";
		$q.= "WHERE es_iva = '1' ORDER BY id ";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r)
			return false;
		$coleccion = array();
		//$imp = new retenciones_adiciones;
		while(!$r->EOF){
			$imp = new retenciones_adiciones;
			$imp->id = $r->fields['id'];
			//$imp->porcret = $r->fields['porcret'];
			//$imp->cta_contable = $r->fields['id_cta'];
			//$imp->porcentaje = $r->fields['porcen'];
			$imp->descripcion = $r->fields['descripcion'];
			$coleccion[] = $imp;
			/*echo $imp->id."<br>";
			echo $imp->descripcion."<br>";  */
			$r->movenext();
		}
		//die(var_dump($coleccion));
		return $coleccion;
	}
		
}
?>