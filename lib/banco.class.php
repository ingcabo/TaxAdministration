<?
class banco{

	// Propiedades

	var $id;
	var $descripcion;
	var $codigo;
	var $nombre_corto;
	var $status;

	function get($conn, $id){
		$q = "SELECT * FROM public.banco ";
		$q.= "WHERE id='$id'";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->descripcion = $r->fields['descripcion'];
			$this->codigo = $r->fields['codigo'];
			$this->nombre_corto = $r->fields['nombre_corto'];
			$this->status = $r->fields['status'];
			
															
			return true;
		}else
			return false;
	}

	function get_all($conn, $from=0, $max=0,$orden="id"){
		$q = "SELECT * FROM public.banco ";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new banco;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $descripcion, $codigo, $nombre_corto, $status){
		$q = "INSERT INTO public.banco ";
		$q.= "(descripcion, codigo, nombre_corto, status) ";
		$q.= "VALUES ";
		$q.= "('$descripcion', '$codigo', '$nombre_corto', $status) ";

		//die($q);
		
		if($conn->Execute($q)){
		
			return true;
		}
		else{
			return false;
		}	
		
	}

	function set($conn, $id, $descripcion, $codigo, $nombre_corto, $status){
		$q = "UPDATE public.banco SET descripcion='$descripcion', codigo='$codigo', nombre_corto='$nombre_corto', status=$status ";
		$q.= "WHERE id=$id";	
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $id){
		$q = "DELETE FROM public.banco WHERE id='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
}
?>
