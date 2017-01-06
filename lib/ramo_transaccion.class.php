<?
class ramo_transaccion{

	// Propiedades

	var $id;
	var $anio;
	var $id_ramo_imp;
	var $id_tipo_transaccion;

	function get($conn, $id){
		$q = "SELECT * FROM vehiculo.ramo_transaccion ";
		$q.= "WHERE id=".$id;
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->anio = $r->fields['anio'];
			$this->id_ramo_imp = $r->fields['id_ramo_imp'];
			$this->id_tipo_transaccion = $r->fields['id_tipo_transaccion'];
			
															
			return true;
		}else
			return false;
			
	}

	function get_all($conn, $from=0, $max=0,$orden="id"){
		$q = "SELECT * FROM vehiculo.ramo_transaccion ";
		$q.= "ORDER BY $orden ";
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new ramo_transaccion;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $id_ramo_imp, $anio,  $tipo_transaccion){
		
		foreach($tipo_transaccion as $id_t){
			$q = "INSERT INTO vehiculo.ramo_transaccion ";
			$q.= "(id_ramo_imp, id_tipo_transaccion, anio) ";
			$q.= "VALUES ";
			$q.= "($id_ramo_imp, $id_t, $anio) ";

			$r=$conn->Execute($q);
		}
		if($r){
			return true;
		}else{
			return false;
		}

	}

	function set($conn, $id, $id_ramo_imp, $anio,  $tipo_transaccion){
		//$d="DELETE FROM ramo_transaccion WHERE id_ramo_imp=".$id_ramo_imp;
//borro
//inserto:foreach
		
		$q = "UPDATE vehiculo.ramo_transaccion SET id_ramo_imp=$id_ramo_imp, id_tipo_transaccion=$tipo_transaccion, anio=$anio";
		$q.= "WHERE id=$id";	
		die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $id){
		$q = "DELETE FROM vehiculo.ramo_transaccion WHERE id='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
}
?>
