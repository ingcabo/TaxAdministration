<?
class tipos_documentos{

	// Propiedades

	var $id;
	var $id_momento_presupuestario;
	var $momento_presupuestario;
	var $abreviacion;
	var $descripcion;
	var $observacion;
	var $colocar_op;

	var $total;

	function get($conn, $id){
		$q = "SELECT tipos_documentos.*, momentos_presupuestarios.descripcion AS momento_presupuestario ";
		$q.= "FROM tipos_documentos ";
		$q.= "LEFT JOIN momentos_presupuestarios ON (tipos_documentos.id_momento_presupuestario = momentos_presupuestarios.id) ";
		$q.= "WHERE tipos_documentos.id='$id'";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->id_momento_presupuestario = $r->fields['id_momento_presupuestario'];
			$this->momento_presupuestario = $r->fields['momento_presupuestario'];
			$this->abreviacion = $r->fields['abreviacion'];
			$this->descripcion = $r->fields['descripcion'];
			$this->observacion = $r->fields['observacion'];
			$this->colocar_op = $r->fields['colocar_op'];
			return true;
		}else
			return false;
	}

	function get_all($conn, $from=0, $max=0,$orden="id"){
		$q = "SELECT * FROM tipos_documentos ";
		$q.= "ORDER BY $orden ";
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new tipos_documentos;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $id, $id_momento_presupuestario, $abreviacion, $descripcion, $observacion, $colocar_op){
		$q = "INSERT INTO tipos_documentos ";
		$q.= "(id, id_momento_presupuestario, abreviacion, descripcion, observacion, colocar_op) ";
		$q.= "VALUES ";
		$q.= "('$id', '$id_momento_presupuestario', '$abreviacion', '$descripcion', '$observacion', $colocar_op) ";
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function set($conn, $id_nuevo, $id, $id_momento_presupuestario, $abreviacion, $descripcion, $observacion, $colocar_op){
		$q = "UPDATE tipos_documentos SET id = '$id_nuevo', id_momento_presupuestario = '$id_momento_presupuestario', ";
		$q.= "abreviacion = '$abreviacion', descripcion='$descripcion' , observacion = '$observacion', colocar_op = $colocar_op ";
		$q.= "WHERE id='$id' ";
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $id){
		$q = "DELETE FROM tipos_documentos WHERE id='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	// obtengo los tipos de documento a partir de los momentos presupuestarios
	function get_all_by_mp($conn, $id_momento_presupuestario, $from=0, $max=0,$orden="id"){
		$q = "SELECT id FROM tipos_documentos ";
		$q.= "WHERE id_momento_presupuestario = '$id_momento_presupuestario' ";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new tipos_documentos;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		return $coleccion;
	}

	function buscar($conn, $id, $id_momento_presupuestario, $descripcion, $orden="id"){
		if(empty($id) && empty($id_momento_presupuestario) && empty($descripcion))
			return false;
		$q = "SELECT * FROM tipos_documentos ";
		$q.= "WHERE  1=1 ";
		$q.= !empty($id) ? "AND id = '$id'  ":"";
		$q.= !empty($id_momento_presupuestario) ? "AND id_momento_presupuestario = '$id_momento_presupuestario'  ":"";
		$q.= !empty($descripcion) ? "AND descripcion ILIKE '%$descripcion%'  ":"";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new tipos_documentos;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		return $coleccion;
	}
}
?>