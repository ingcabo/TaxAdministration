<?
class veh_tasabanco{

	// Propiedades

	var $id;
	var $mes;
	var $anio;
	var $monto;
	var $total;
	function get($conn, $id){
		$q = "SELECT * FROM vehiculo.tasa_bancaria ";
		$q.= "WHERE cod_tas=$id"; //die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['cod_tas'];
			$this->mes = $r->fields['mes'];
			$this->anio = $r->fields['anio'];
			$this->monto = $r->fields['monto'];								
			return true;
		}else
			return false;
	}

	function get_all($conn, $orden="cod_tas"){
		$q = "SELECT * FROM vehiculo.tasa_bancaria ";
		$q.= "ORDER BY $orden "; //die($q);
		$r = $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new veh_tasabanco;
			$ue->get($conn, $r->fields['cod_tas']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $mes, $anio, $monto){
		$q = "INSERT INTO vehiculo.tasa_bancaria ";
		$q.= "(mes, anio, monto) VALUES ";
		$q.= "($mes, $anio, '$monto') "; //die($q);
		if($conn->Execute($q)){
			return true;
		}
		else{
			return false;
		}	
		
	}

	function set($conn, $id, $mes, $anio, $monto){
		$q = "UPDATE vehiculo.tasa_bancaria SET mes=$mes, anio=$anio, monto='$monto' ";
		$q.= "WHERE cod_tas=$id";	die($q);
		
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $id){
		$q = "DELETE FROM vehiculo.tasa_bancaria WHERE cod_tas='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
}
?>
