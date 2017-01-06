<?
class traFondosTerceros{

	// Propiedades

	var $nrodoc;
	var $num_com;
	var $descripcion;
	var $fecha;
	var $id;
	var $status;
	var $fechaAnul;
	var $motivoAnul;
	var $detTransferencia;
	
	var $total;
	var $msj;

	function get($conn, $id){
		$q = "SELECT A.* FROM finanzas.tra_fondos_terceros AS A ";
		$q.= "WHERE id='$id' ";
		$q.= "ORDER BY id";
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->fecha = $r->fields['fecha'];
			$this->descripcion = $r->fields['descripcion'];
			$this->status = $r->fields['status'];	
			$this->nrodoc = $r->fields['nrodoc'];
			$this->num_com = $r->fields['num_com'];
			$this->fechaAnul = $r->fields['fecha_anulado'];
			$this->motivoAnul = $r->fields['motivo_anulado'];
			$this->getDetalle($conn, $id);
			//die('entro');
														
			return true;
		}else
			return false;
	}

	function get_all($conn, $from=0, $max=0,$orden="id"){
		$q = "SELECT * FROM finanzas.tra_fondos_terceros ";
		$q.= "ORDER BY $orden ";
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new traFondosTerceros;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $fecha, $nrodoc, $status, $num_com, $descripcion, $servicio){
		
		$q = "INSERT INTO finanzas.tra_fondos_terceros ";
		$q.= "(nrodoc, num_com, fecha, status, descripcion) ";
		$q.= " VALUES ";
		$q.= "('$nrodoc', '$num_com', '$fecha', $status, '$descripcion') ";
		$r = $conn->Execute($q);
		if($r){
			$this->add_detalle($conn, $servicio);
			$this->msj = REG_ADD_OK;
		}else{
			$this->msj = ERROR_ADD;
			return false;
		}
	}
	
	function add_detalle($conn, $json_det){
		$q = "SELECT MAX(id) AS id FROM finanzas.tra_fondos_terceros ";
		$r = $conn->Execute($q);
		$id = $r->fields['id'];
		$JsonRec = new Services_JSON();
		$JsonRec=$JsonRec->decode(str_replace("\\","",$json_det));
		if(is_array($JsonRec->transfer)){
			foreach ($JsonRec->transfer as $oTFT){
			
				$sql = "INSERT INTO finanzas.relacion_tra_fondos_terceros (id_tra, id_cta, monto) ";
				$sql.= "VALUES ";
				$sql.= "($id, '$oTFT[0]', ".guardaFloat($oTFT[1]).") ";
				$r = $conn->Execute($sql);
			} 
			if($r)
				return true;
			else
				return false;
		}
		
	}
	
	function getDetalle ($conn,$id){
		
		$q = "SELECT id_cta, monto FROM finanzas.relacion_tra_fondos_terceros ";
		$q.= "WHERE id_tra = $id ";
		$r = $conn->Execute($q);
		
		while (!$r->EOF){
			$tft = new traFondosTerceros;
			$tft->id_cta = $r->fields['id_cta'];
			$tft->monto = $r->fields['monto'];
			$coleccion[] = $tft;
			$r->movenext();
		}
		//die(var_dump($tft));
		$this->detTransferencia = new Services_JSON();
		$this->detTransferencia = is_array($coleccion) ? $this->detTransferencia->encode($coleccion) : false;
		return $coleccion;
	}
	
	
	function buscar($conn,$nrodoc, $fecha, $max=0, $from=0, $orden="id"){
		//die("aqui ".$grupo_prov);
		try{
			if(empty($nrodoc) and empty($fecha))
				return false;
			$q = "SELECT * FROM finanzas.tra_fondos_terceros ";
			$q.= "WHERE 1=1 ";
			$q.= !empty($nrodoc) ? "AND nrodoc = '$nrodoc'  ":"";
			$q.= !empty($fecha) ? "AND fecha = '".guardafecha($fecha)."'  ":"";
			$q.= "ORDER BY $orden ";
			//die($q);
			$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
			$collection=array();
			while(!$r->EOF){
				$ue = new traFondosTerceros;
				$ue->get($conn, $r->fields['id']);
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
	
	function total_registro_busqueda($conn,$nrodoc, $fecha, $orden="id"){
		if(empty($nrodoc) and empty($fecha))
				return false;
		$q = "SELECT * FROM finanzas.tra_fondos_terceros ";
			$q.= "WHERE 1=1 ";
			$q.= !empty($nrodoc) ? "AND nrodoc = '$nrodoc'  ":"";
			$q.= !empty($fecha) ? "AND fecha = '".guardafecha($fecha)."'  ":"";
			$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		$total = $r->RecordCount();

		return $total;
	}
	
	function anular($conn,$id,$fechaAnul,$status, $motivoAnul){
		$q = "UPDATE finanzas.tra_fondos_terceros SET status = 2, fecha_anulado = '$fechaAnul', motivo_anulacion = '$motivoAnul' ";
		$q.= "WHERE id = '$id' ";
		//die($q);
		$r = $conn->Execute($q);
		if($r){
			$this->msj = REG_DEL_OK;
			return true;
		} else {
			$this->msj = ERROR_DEL;
			return true;
		}

			
	}
	
	function getOrdenes($conn, $nrodoc){
		$q= "SELECT DISTINCT(op.nrodoc), op.descripcion ";
		$q.= "FROM finanzas.orden_pago op ";
		$q.= "LEFT JOIN finanzas.relacion_cheque rc ON (rc.nroref = op.nrodoc) ";
		$q.= "LEFT JOIN finanzas.relacion_otros_pagos rop ON (rop.nroref = op.nrodoc) ";
		$q.= "WHERE op.status = 2 AND transferido = '0' AND op.montoret > 0 "; 
		$q.= !empty($nrodoc) ? "AND op.nrodoc = '$nrodoc' " : "";
		//die($q);
		//echo $q.'<br>';
		$r = $conn->Execute($q);
		while(!$r->EOF){
			$tra = new traFondosTerceros;
			$tra->nrodoc = $r->fields['nrodoc'];
			$tra->descripcion = $r->fields['descripcion'];
			$coleccion[] = $tra;
			$r->movenext();
		}
		return $coleccion;
	}
	
	function getAsientos ($conn, $nrodoc, $ctaTra){
		$q = "SELECT A.id_cta, A.haber as monto, C.descripcion AS descripcion, (SELECT cta_contable FROM puser.proveedores WHERE cta_contable = id_cta) AS cta_proveedor FROM ";
		$q.= "contabilidad.com_det A ";
		$q.= "INNER JOIN contabilidad.com_enc B ON (A.id_com = B.id) ";
		$q.= "INNER JOIN contabilidad.plan_cuenta C ON (A.id_cta = C.id) ";
		$q.= "WHERE B.num_doc = '$nrodoc' AND A.haber > 0 AND A.id_cta <> $ctaTra ";
		//die($q);
		$r = $conn->Execute($q);
		while(!$r->EOF){
			$oTra = new traFondosTerceros;
			$oTra->idCta = $r->fields['id_cta'];
			$oTra->monto = $r->fields['monto'];
			$oTra->ctaProveedor = $r->fields['cta_proveedor'];
			$oTra->descripcion = $r->fields['descripcion'];
			$coleccion[] = $oTra;
			$r->movenext();
		}
		return $coleccion;
	}
	
	function setOrdenPago($conn,$nrodoc,$status){
		$q = "UPDATE finanzas.orden_pago SET transferido = '$status' WHERE nrodoc = '$nrodoc'";
		$r = $conn->Execute($q);
	}
	
	function getAporRetXpagar($conn, $descripcion){
		$q = "SELECT DISTINCT A.id, A.codcta, A.descripcion, COALESCE(SUM(D.debe),0) AS debe, COALESCE(SUM(D.haber),0) AS haber FROM contabilidad.plan_cuenta A ";
		$q.= "INNER JOIN finanzas.ret_apor_x_cancelar B ON (A.id = B.id_cta) ";
		$q.= "LEFT JOIN contabilidad.com_det D ON (A.id = D.id_cta) ";
		$q.= !empty($descripcion) ? "WHERE A.descripcion ILIKE '%$descripcion%' " : "";
		$q.= "GROUP BY 1,2,3 ";
		//die($q);
		$r = $conn->Execute($q);
		while(!$r->EOF){
			$tra = new traFondosTerceros;
			$tra->idCta = $r->fields['id'];
			$tra->codCta = $r->fields['codcta'];
			$tra->descripcion = $r->fields['descripcion'];
			//echo 'hab: '.$r->fields['haber'].' debe: '.$r->fields['debe'];
			$saldo = ($r->fields['haber'] - $r->fields['debe']);
			//die($saldo);
			$tra->saldo = $saldo;
			$coleccion[] = $tra;
			$r->movenext();
		}
		return $coleccion;
	}
	
	
}
?>
