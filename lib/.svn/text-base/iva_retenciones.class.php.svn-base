<?

class iva_retenciones{

# PROPIEDADES #

	var $id;
	var $cod_partida;
	var $nombre_partida;
	var $iva;
	var $retencion;
	var $anio;

# METODOS #

	function get($conn, $id){
		$q = "SELECT * FROM finanzas.iva_retenciones ";
		$q.= "WHERE id='$id'";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->cod_partida = $r->fields['cod_partida'];
			$pp = new partidas_presupuestarias;
			$pp->get($conn, $r->fields['cod_partida'], $r->fields['anio']);
			$this->nombre_partida = $pp;
			$this->iva = $r->fields['iva'];
			$this->retencion = $r->fields['retencion'];
			$this->anio = $r->fields['anio'];
																		
			return true;
		}else
			return false;
	}
	
	function get_all($conn, $from=0, $max=0,$orden="id"){
		$q = "SELECT * FROM finanzas.iva_retenciones ";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new tipo_movimiento;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}
	
	function add($conn, $cod_partida, $iva,$retencion,$anio){
		$q = "INSERT INTO finanzas.iva_retenciones ";
		$q.= "(cod_partida, iva, retencion, anio) ";
		$q.= "VALUES ";
		$q.= "('$cod_partida', '$iva','$retencion','$anio') ";

		//die($q);
		
		if($conn->Execute($q)){
		
			return true;
		}
		else{
			return false;
		}	
		
	}
	
	function set($conn, $id, $cod_partida, $iva,$retencion,$anio){
		$q = "UPDATE finanzas.iva_retenciones SET cod_partida='$cod_partida', iva='$iva', retencion='$retencion', anio='$anio' ";
		$q.= "WHERE id=$id";	
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	function del($conn, $id){
		$q = "DELETE FROM finanzas.iva_retenciones WHERE id='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	function buscar($conn, $cod_partida, $orden="id"){
		if(empty($cod_partida))
			return false;
		$q = "SELECT * FROM finanzas.iva_retenciones ";
		$q.= "WHERE  1=1 ";
		$q.= !empty($cod_partida) ? "AND cod_partida='$cod_partida' ": "";
		$q.= "ORDER BY $orden ";
		//die($q);
		if(!$r = $conn->Execute($q))
			return false;
		$collection=array();
		while(!$r->EOF){
			$ue = new iva_retenciones;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		return $coleccion;
	}
}

?>
