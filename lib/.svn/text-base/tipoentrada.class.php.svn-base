<?
class tipoentrada{

	// Propiedades

	var $id;
	var $descripcion;
	var $mpersona;
	var $precio;
	var $imp;
	var $aforo;
	var $total;
	
	function get($conn, $id){
		$q = "SELECT * FROM publicidad.entradas ";
		$q.= "WHERE id='$id'"; 
		//die($q);
		
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->descripcion = $r->fields['descripcion'];
			$this->mpersona = $r->fields['max_per'];
			$this->precio = $r->fields['tiene_precio'];
			$this->imp = $r->fields['imp_exon'];
			$this->aforo = $r->fields['aforo'];
																		
			return true;
		}else
			return false;
	}

		function get_all($conn, $orden="id"){
		$q = "SELECT * FROM publicidad.entradas ";
		$q.= "ORDER BY $orden ";
		$r = $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new tipoentrada;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $descripcion, $mpersona, $precio, $imp, $aforo){
		$q = "INSERT INTO publicidad.entradas ";
		$q.= "(descripcion, max_per, tiene_precio, imp_exon , aforo) ";
		$q.= "VALUES ";
		$q.= "('$descripcion', '$mpersona', '$precio', '$imp', '$aforo') "; //die($q);
		if($conn->Execute($q)){
			return true;
		}
		else{
			return false;
		}	
		
	}

	function set($conn, $id, $descripcion, $mpersona, $precio, $imp, $aforo){
		$q = "UPDATE publicidad.entradas SET descripcion='$descripcion', max_per='$mpersona', tiene_precio='$precio', imp_exon='$imp' , aforo='$aforo'";
		$q.= "WHERE id=$id";	
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $id){
		$q = "DELETE FROM publicidad.entradas WHERE id='$id'"; //die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
}
?>