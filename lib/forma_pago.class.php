<?
class forma_pago{

	// Propiedades

	var $id;
	var $descripcion;
	var $efectivo;
	var $nombre_corto;
	var $status;

	function get($conn, $id){
		$q = "SELECT * FROM vehiculo.forma_pago ";
		$q.= "WHERE id='$id'";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->descripcion = $r->fields['descripcion'];
			$this->efectivo = $r->fields['efectivo'];
			$this->nombre_corto = $r->fields['nombre_corto'];
			$this->status = $r->fields['status'];
			
															
			return true;
		}else
			return false;
	}

	function get_all($conn, $from=0, $max=0,$orden="id"){
		$q = "SELECT * FROM vehiculo.forma_pago ";
		$q.= "ORDER BY $orden ";
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new forma_pago;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $descripcion, $efectivo, $nombre_corto, $status){
		$q = "INSERT INTO vehiculo.forma_pago ";
		$q.= "(descripcion, efectivo, nombre_corto, status) ";
		$q.= "VALUES ";
		$q.= "('$descripcion', '$efectivo', '$nombre_corto', $status) ";

		//die($q);
		
		if($conn->Execute($q)){
		
			return true;
		}
		else{
			return false;
		}	
		
	}

	function set($conn, $id, $descripcion, $efectivo, $nombre_corto, $status){
		$q = "UPDATE vehiculo.forma_pago SET descripcion='$descripcion', efectivo='$efectivo', nombre_corto='$nombre_corto', status=$status ";
		$q.= "WHERE id=$id";	
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $id){
		$q = "DELETE FROM vehiculo.forma_pago WHERE id='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
}
?>
