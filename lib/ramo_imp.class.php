<?
class ramo_imp{

	// Propiedades

	var $id;
	var $ramo;
	var $descripcion;
	var $tipo_imp;
	var $anio;
	var $status;

	function get($conn, $id){
		$q = "SELECT * FROM vehiculo.ramo_imp ";
		$q.= "WHERE id='$id'";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->ramo = $r->fields['ramo'];
			$this->descripcion = $r->fields['descripcion'];
			$this->tipo_imp = $r->fields['tipo_imp'];			
			$this->anio = $r->fields['anio'];
			$this->status = $r->fields['status'];
			
															
			return true;
		}else
			return false;
	}

	function get_all($conn, $from=0, $max=0,$orden="id"){
		$q = "SELECT * FROM vehiculo.ramo_imp ";
		$q.= "ORDER BY $orden ";
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new ramo_imp;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $ramo, $descripcion, $tipo_imp, $anio, $status){
		$q = "INSERT INTO vehiculo.ramo_imp ";
		$q.= "(ramo, descripcion, tipo_imp, anio, status) ";
		$q.= "VALUES ";
		$q.= "($ramo, '$descripcion', '$tipo_imp', $anio, $status) ";

		//die($q);
		
		if($conn->Execute($q)){
		
			return true;
		}
		else{
			return false;
		}	
		
	}

	function set($conn, $id, $ramo, $descripcion, $tipo_imp,  $anio, $status){
		$q = "UPDATE vehiculo.ramo_imp SET ramo=$ramo, descripcion='$descripcion' , tipo_imp='$tipo_imp', anio=$anio, status=$status ";
		$q.= "WHERE id=$id";	
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $id){
		$q = "DELETE FROM vehiculo.ramo_imp WHERE id='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
}
?>
