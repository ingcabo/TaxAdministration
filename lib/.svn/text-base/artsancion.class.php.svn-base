<?
class artsancion{

	// Propiedades

	var $id;
	var $descripcion;
	var $monto;
	var $status;
	var $total;
	function get($conn, $id){
		$q = "SELECT * FROM publicidad.articulos_sanciones ";
		$q.= "WHERE cod_articulo='$id'";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['cod_articulo'];
			$this->descripcion = $r->fields['descripcion'];
			$this->monto = $r->fields['monto'];
			$this->status = $r->fields['status'];
			
															
			return true;
		}else
			return false;
	}

	function get_all($conn, $orden="cod_articulo"){
		$q = "SELECT * FROM publicidad.articulos_sanciones ";
		$q.= "ORDER BY $orden "; 
		$r = $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new artsancion;
			$ue->get($conn, $r->fields['cod_articulo']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $descripcion, $monto, $status){
		$q = "INSERT INTO publicidad.articulos_sanciones ";
		$q.= "(descripcion, monto, status) ";
		$q.= "VALUES ";
		$q.= "('$descripcion', $monto, $status) ";

		//die($q);
		
		if($conn->Execute($q)){
		
			return true;
		}
		else{
			return false;
		}	
		
	}

	function set($conn, $id, $descripcion, $status){
		$q = "UPDATE publicidad.articulos_sanciones SET descripcion='$descripcion', monto='$monto',  status=$status ";
		$q.= "WHERE cod_articulo=$id";	
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $id){
		$q = "DELETE FROM publicidad.articulos_sanciones WHERE cod_articulo='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
}
?>
