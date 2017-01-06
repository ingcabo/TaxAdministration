<?
class tipo_transaccion{

	// Propiedades

	var $id;
	var $id_par_pre;
	var $descripcion;
	var $anio;
	var $status;

	function get($conn, $id){
		$q = "SELECT * FROM vehiculo.tipo_transaccion ";
		$q.= "WHERE id='$id'";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->id_par_pre = $r->fields['id_par_pre'];
			$this->descripcion = $r->fields['descripcion'];
			$this->anio = $r->fields['anio'];
			$this->status = $r->fields['status'];
			
															
			return true;
		}else
			return false;
	}

	function get_all($conn, $from=0, $max=0,$orden="id"){
		$q = "SELECT * FROM vehiculo.tipo_transaccion ";
		$q.= "ORDER BY $orden ";
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new tipo_transaccion;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $descripcion, $partidas_presupuestarias,  $anio, $status){
		$q = "INSERT INTO vehiculo.tipo_transaccion ";
		$q.= "(descripcion, id_par_pre, anio, status) ";
		$q.= "VALUES ";
		$q.= "('$descripcion', '$partidas_presupuestarias', $anio, $status) ";

		//die($q);
		
		if($conn->Execute($q)){
		
			return true;
		}
		else{
			return false;
		}	
		
	}

	function set($conn, $id, $descripcion, $partidas_presupuestarias,  $anio, $status){
		$q = "UPDATE vehiculo.tipo_transaccion SET descripcion='$descripcion', id_par_pre='$partidas_presupuestarias', anio=$anio, status=$status ";
		$q.= "WHERE id=$id";	
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $id){
		$q = "DELETE FROM vehiculo.tipo_transaccion WHERE id='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
}
?>
