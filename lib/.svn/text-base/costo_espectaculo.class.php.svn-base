<?
class costo_espectaculo{

	// Propiedades

	var $id_espectaculo;
	var $descripcion;
	var $valor;
	var $status;
	var $categoria;
	var $nacionalidad;
	
	function get($conn, $cod_espectaculo){
		$q = "SELECT * FROM publicidad.tipo_espectaculo ";
		$q.= "WHERE cod_espectaculo='$cod_espectaculo'";
		//die($q);
		
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id_espectaculo = $r->fields['cod_espectaculo'];
			$this->descripcion = $r->fields['descripcion'];
			$this->categoria= $r->fields['categoria'];
			$this->nacionalidad = $r->fields['nacionalidad'];
			$this->valor = $r->fields['valor'];
			$this->status = $r->fields['status'];		
			return true;
		}else
			return false;
	}

	function get_all($conn, $from=0, $max=0,$orden="cod_espectaculo"){
		$q = "SELECT * FROM publicidad.tipo_espectaculo ";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new costo_espectaculo;
			$ue->get($conn, $r->fields['cod_espectaculo']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $descripcion, $valor, $status, $categoria, $nacionalidad){
		$q = "INSERT INTO publicidad.tipo_espectaculo ";
		$q.= "(descripcion, valor, status, categoria, nacionalidad) ";
		$q.= "VALUES ";
		$q.= "('$descripcion', '$valor', '$status', '$categoria', '$nacionalidad') "; //die($q);

		//die($q);
		
		if($conn->Execute($q)){
		
			return true;
		}
		else{
			return false;
		}	
		
	}

	function set($conn, $cod_espectaculo, $descripcion, $valor, $status, $categoria, $nacionalidad){
		$q = "UPDATE publicidad.tipo_espectaculo SET descripcion='$descripcion', valor='$valor', status='$status', ";
		$q.= "categoria='$categoria', nacionalidad='$nacionalidad'";
		$q.= "WHERE cod_espectaculo=$cod_espectaculo";	
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $cod_espectaculo){
		$q = "DELETE FROM publicidad.tipo_espectaculo WHERE cod_espectaculo='$cod_espectaculo'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
}
?>
