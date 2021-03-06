<?
class prestamo{

	// Propiedades
	var $int_cod;
	var $tra_cod;
	var $tra_nom;
	var $cont_cod;
	var $pres_fecha ;
	var $pres_monto;
	var $pres_cuotas;
	var $pres_estatus;
	var $pres_fecha_ini;
	var $dep_cod;
	var $pres_det;
	var $pres_desc;
	
	var $total;

	function get($conn, $int_cod){
		try {
			$q = "SELECT * FROM rrhh.prestamo WHERE int_cod=$int_cod";
			//die($q);
			$r = $conn->Execute($q);
			if(!$r->EOF){
				$this->int_cod = $r->fields['int_cod'];
				$this->tra_cod = $r->fields['tra_cod'];
				$this->cont_cod = $r->fields['cont_cod'];
				$this->pres_fecha = $r->fields['pres_fecha'];
				$this->pres_monto = $r->fields['pres_monto'];
				$this->pres_saldo = $r->fields['pres_saldo'];
				$this->pres_cuotas = $r->fields['pres_cuotas'];
				$this->pres_estatus = $r->fields['pres_estatus'];
				$this->pres_fecha_ini = $r->fields['pres_fecha_ini'];
				$this->pres_desc = $r->fields['pres_desc'];
				
				$q = "SELECT tra_nom,tra_ape,dep_cod FROM rrhh.trabajador WHERE int_cod=$this->tra_cod";
				$rAux = $conn->Execute($q);
				$this->dep_cod=$rAux->fields['dep_cod'];
				$this->tra_nom=$this->cadena($rAux->fields['tra_nom'])." ".$this->cadena($rAux->fields['tra_ape']);


				$q = "SELECT * FROM rrhh.prestamo_cuotas WHERE pres_cod=$int_cod ORDER BY cuota_nro";
				$rC = $conn->Execute($q);
				$i=0;
				while(!$rC->EOF){
					$CuotasAux[$i][0] = $rC->fields['cuota_nom_fec_ini'];
					$CuotasAux[$i][1] = $rC->fields['cuota_nom_fec_fin'];
					$CuotasAux[$i][2] = $rC->fields['cuota_porc'];
					$CuotasAux[$i][3] = $rC->fields['cuota_monto'];
					$CuotasAux[$i][4] = $rC->fields['cuota_estatus'];
					$CuotasAux[$i][5] = $rC->fields['cuota_nro'];
					$i++;
					$rC->movenext();
				}
				$this->pres_det = new Services_JSON();
				$this->pres_det = is_array($CuotasAux) ? $this->pres_det->encode($CuotasAux) : false;
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
	function get_all($conn, $orden="int_cod",$Tipo,$Valor){
		try {
			if(empty($Valor)){
				$q = "SELECT A.int_cod FROM rrhh.prestamo AS A INNER JOIN rrhh.trabajador AS B ON A.tra_cod=B.int_cod WHERE B.tra_estatus = 0 OR B.tra_estatus = 3 ORDER BY $orden  ";
			}elseif($Tipo==0){
				$q = "SELECT A.int_cod FROM rrhh.prestamo AS A INNER JOIN rrhh.trabajador AS B ON A.tra_cod=B.int_cod WHERE B.tra_ced ILIKE '$Valor%' AND (tra_estatus = 0 OR tra_estatus = 3) ORDER BY $orden  ";
			}elseif($Tipo==1){
				$q = "SELECT A.int_cod FROM rrhh.prestamo AS A INNER JOIN rrhh.trabajador AS B ON A.tra_cod=B.int_cod WHERE B.tra_nom ILIKE '$Valor%' AND (tra_estatus = 0 OR tra_estatus = 3) ORDER BY $orden  ";
			}elseif($Tipo==2){
				$q = "SELECT A.int_cod FROM rrhh.prestamo AS A INNER JOIN rrhh.trabajador AS B ON A.tra_cod=B.int_cod WHERE B.tra_ape ILIKE '$Valor%' AND (tra_estatus = 0 OR tra_estatus = 3) ORDER BY $orden  ";
			}
			//die($q);
			$r = $conn->Execute($q);
			$collection=array();
			while(!$r->EOF){
				$ue = new prestamo;
				$ue->get($conn, $r->fields['int_cod']);
				$coleccion[] = $ue;
				$r->movenext();
			}
			$this->total = $r->RecordCount();
			//die($r->RecordCount());
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
	function add($conn, $tra_cod, $cont_cod, $pres_fecha_ini, $pres_monto, $pres_cuotas, $pres_estatus,$pres_desc, $pres_det){
		try {
			$fecha=date("Y-m-d");
			$q = "INSERT INTO rrhh.prestamo ";
			$q.= "(tra_cod, cont_cod, pres_fecha_ini, pres_monto, pres_cuotas, pres_estatus,pres_fecha,pres_desc)";
			$q.= "VALUES ";
			$q.= "($tra_cod, $cont_cod, '$pres_fecha_ini', $pres_monto, $pres_cuotas, $pres_estatus,'$fecha','$pres_desc') ";
			//die($q);
			$conn->Execute($q);
			$q = "SELECT max(int_cod) as int_cod FROM rrhh.prestamo";
			$r = $conn->Execute($q);
			$this->GuardarDetalle($conn,$r->fields['int_cod'],$pres_det);
		}
		catch( ADODB_Exception $e ){
			if($e->getCode()==-1)
				return ERROR_CATCH_VFK;
			elseif($e->getCode()==-5)
				return ERROR_CATCH_VUK;
			else
				return ERROR_CATCH_GENERICO;
			return $e;
		}
	}

	function set($conn, $int_cod, $tra_cod, $cont_cod, $pres_fecha_ini, $pres_monto, $pres_cuotas, $pres_estatus,$pres_desc, $pres_det){
		try {
			$q = "UPDATE rrhh.prestamo SET tra_cod=$tra_cod,cont_cod=$cont_cod,pres_fecha_ini='$pres_fecha_ini',pres_monto=$pres_monto,pres_cuotas=$pres_cuotas,pres_estatus=$pres_estatus,pres_desc='$pres_desc'";
			$q.= " WHERE int_cod=$int_cod";	
			//die($q);
			$conn->Execute($q);
			$this->GuardarDetalle($conn,$int_cod,$pres_det);
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
	function del($conn, $int_cod){
		try {
			$q = "SELECT pres_estatus FROM rrhh.prestamo WHERE int_cod='$int_cod'";
			$r = $conn->Execute($q);
			if($r->fields['pres_estatus']==2){
				$q = "DELETE FROM rrhh.prestamo WHERE int_cod='$int_cod'";
				$conn->Execute($q);
			}else{
				return "Solo puede eliminar los prestamos que han sido rechazados";
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
	function GuardarDetalle($conn,$pres_cod,$pres_det){
		try {
			$JsonRec = new Services_JSON();
			$JsonRec=$JsonRec->decode(str_replace("\\","",$pres_det));
			if(is_array($JsonRec->CuotasDet)){
				$q = "DELETE FROM rrhh.prestamo_cuotas WHERE pres_cod=$pres_cod";
				$r = $conn->Execute($q);
				foreach($JsonRec->CuotasDet as $CuotasDetAux){
					$q = "INSERT INTO rrhh.prestamo_cuotas ";
					$q.= "(pres_cod, cuota_nom_fec_ini, cuota_nom_fec_fin, cuota_porc, cuota_monto, cuota_estatus,cuota_nro) ";
					$q.= "VALUES ";
					$q.= "($pres_cod, '$CuotasDetAux[1]', '$CuotasDetAux[2]', '$CuotasDetAux[3]', '$CuotasDetAux[4]', '$CuotasDetAux[5]', '$CuotasDetAux[0]') ";
					//die($q);
					$conn->Execute($q);
				}
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
	function Cadena($Cadena){
		$Vector=split(" ",$Cadena,500);
		if($Vector[1]){
			$Palabra=$Vector[1];
			$Palabra=$Palabra[0].".";
		}
		return $Vector[0]." ".$Palabra; 
	}		 
	function TraeSaldo($conn, $pres_cod){
		try {
			$q = "SELECT (pres_monto- CASE WHEN ( (SELECT SUM(cuota_monto) FROM rrhh.prestamo_cuotas WHERE pres_cod=$pres_cod AND cuota_estatus='Cancelado') IS NULL) THEN 0 ELSE (SELECT SUM(cuota_monto) FROM rrhh.prestamo_cuotas WHERE pres_cod=$pres_cod AND cuota_estatus='Cancelado') END) AS saldo FROM rrhh.prestamo  WHERE int_cod=$pres_cod";
			//die($q);
			$r = $conn->Execute($q);
			return !empty($r->fields['saldo']) ? $r->fields['saldo'] : 0.00;
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
