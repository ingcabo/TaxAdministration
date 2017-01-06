<?
class control_chequera{

# PROPIEDADES #

	var $id;
	var $nro_chequera;
	var $nro_cuenta;
	var $fecha;
	var $cheque_desde;
	var $cheque_hasta;
	var $ultimo_cheque;
	var $id_banco;
	var $id_cuenta;
	var $activa;
		
	var $total;

# METODOS #

	function get($conn, $id){
		$q = "SELECT finanzas.control_chequera.*, finanzas.cuentas_bancarias.id_banco FROM finanzas.control_chequera ";
		$q.= " LEFT JOIN finanzas.cuentas_bancarias ON finanzas.control_chequera.nro_cuenta = finanzas.cuentas_bancarias.id ";
		$q.= "WHERE finanzas.control_chequera.id='$id'";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->nro_chequera = $r->fields['nro_chequera'];
			$cb = new cuentas_bancarias;
			$cb->get($conn, $r->fields['nro_cuenta']);
			$this->nro_cuenta = $cb;
			$this->fecha = $r->fields['fecha'];
			$this->cheque_desde = $r->fields['cheque_desde'];
			$this->cheque_hasta = $r->fields['cheque_hasta'];			
			$this->ultimo_cheque = $r->fields['ultimo_cheque'];
			$this->id_banco = $r->fields['id_banco'];
			$this->id_cuenta = $cb->id;
			$this->activa = $r->fields['activa'];
			return true;
		}else
			return false;
	}
	
	function get_all($conn, $from=0, $max=0,$orden="id"){
		$q = "SELECT * FROM finanzas.control_chequera ";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new control_chequera;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}
	
	function add($conn, $nro_chequera, $nro_cuenta, $fecha, $cheque_desde, $cheque_hasta, 
				 $ultimo_cheque,$activa){
		$q = "INSERT INTO finanzas.control_chequera ";
		$q.= "(nro_chequera,
			   nro_cuenta,	 
			   fecha, 
			   cheque_desde, 
			   cheque_hasta, 
			   ultimo_cheque 
			   ) ";
		$q.= " VALUES ";
		$q.= "('$nro_chequera',
			   $nro_cuenta,	 
			   '$fecha', 
			   '$cheque_desde', 
			   '$cheque_hasta', 
			   '$ultimo_cheque') ";
		$r=$conn->Execute($q);

		$q = "SELECT max(id) as id FROM finanzas.control_chequera ";
		$r = $conn->Execute($q);
		
		if($activa){
			control_chequera::activarChequera($conn,$r->fields['id'],$nro_cuenta);
		}
		if($conn->Execute($q)){
		
			return true;
		}
		else{
		
			return false;
		}	
		
	}
	
	function set($conn, $id, $nro_chequera, $nro_cuenta, $fecha, $cheque_desde, $cheque_hasta, 
				 $ultimo_cheque){
		$q  = "UPDATE finanzas.control_chequera SET ";
		$q .= "nro_chequera = '$nro_chequera', ";
		$q .= "nro_cuenta = $nro_cuenta, ";
		$q .= "fecha = '$fecha', ";
		$q .= "cheque_desde = '$cheque_desde', ";
		$q .= "cheque_hasta = '$cheque_hasta', ";
		$q .= "ultimo_cheque = '$ultimo_cheque' ";
		$q .= "WHERE id=$id";	
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	function del($conn, $id){
		$q = "DELETE FROM finanzas.control_chequera WHERE id='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	function activarChequera($conn,$id_chequera,$id_cuenta){
		$q = "UPDATE finanzas.control_chequera SET activa=0 WHERE nro_cuenta=$id_cuenta";
		$r=$conn->Execute($q);
		$q = "UPDATE finanzas.control_chequera SET activa=1 WHERE id=$id_chequera";
		$r=$conn->Execute($q);
		if($r)
			return true;
		else
			return false;
	}
	
	#ESTA FUNCION TRAE LA ULTIMA CHEQUERA QUE SE ESTA UTLIZANDO#
	function ChequeraxCuenta($conn, $id_cuenta){
	
		$q = "SELECT * FROM finanzas.control_chequera WHERE nro_cuenta = $id_cuenta AND activa=1";
		$r = $conn->execute($q);
		
		if (!$r->EOF){
		
			$cc = new control_chequera;
			$cc->ultimo_cheque = $r->fields['ultimo_cheque']+1;
			$coleccion = $cc;
			if($cc->ultimo_cheque>$r->fields['cheque_hasta']){
				return -1;
			}
		}
		return $coleccion;
	}
}

?>