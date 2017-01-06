<?
class inspeccion{

	// Propiedades

	var $cod_asignacion;
	var $cod_inspector;
	var $res_inspeccion;
	var $patente;
	var $status;
	
	function get($conn, $id){
	
		$q = "SELECT * FROM publicidad.resultado_inspeccion ";
		$q.= "WHERE cod_asignacion='$id'";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->cod_asignacion = $r->fields['cod_asignacion'];
			$this->cod_inspector = $r->fields['cod_inspector'];
			$this->res_inspeccion = $r->fields['res_inspeccion'];
			$this->patente = $r->fields['patente'];
			$this->status = $r->fields['status'];
					
			return true;
		}else
			return false;
	}

	function get_all($conn, $from=0, $max=0,$orden="cod_asignacion"){
		$q = "SELECT * FROM publicidad.resultado_inspeccion ";
		$q.= "ORDER BY $orden ";
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new inspeccion;
			$ue->get($conn, $r->fields['cod_asignacion']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $cod_inspector, $res_inspeccion, $patente, $status){
		$q = "INSERT INTO publicidad.resultado_inspeccion ";
		$q.= "(cod_inspector, res_inspeccion, patente, status) ";
		$q.= "VALUES ";
		$q.= "($cod_inspector, $res_inspeccion, $patente, $status) ";

		die($q);
		
		if($conn->Execute($q)){
		
			return true;
		}
		else{
			return false;
		}	
		
	}

	function set($conn, $cod_asignacion, $res_inspeccion, $status){
		$q = "UPDATE publicidad.resultado_inspeccion SET cod_asignacion = $cod_asignacion, res_inspeccion='$res_inspeccion', status=$status ";
		$q.= "WHERE cod_asignacion=$cod_asignacion";	
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $cod_asignacion){
		$q = "DELETE FROM publicidad.resultado_asignacion WHERE cod_asignacion='$cod_asignacion'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
}
?>
