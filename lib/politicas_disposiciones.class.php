<?
class politicas_disposiciones{

	// Propiedades

	var $id;
	var $id_escenario;
	var $escenario;
	var $id_tipo_gaceta;
	var $tipo_gaceta;  
	var $ano; 
	var $texto1;
	var $texto2;
	var $texto3;
	var $texto4;

	var $total;

	function get($conn, $id, $escenario="" ){
		$q = "SELECT * FROM politicas_disposiciones WHERE id='$id'";
	
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->id_escenario = $r->fields['id_escenario'];
			$this->escenario = escenarios::get_descripcion($conn, $r->fields['id_escenario']);
			$this->id_tipo_gaceta = $r->fields['id_tipo_gaceta'];
			$this->tipo_gaceta = gacetas::get_descripcion($conn, $r->fields['id_tipo_gaceta']);
			$this->ano = $r->fields['ano'];
			$this->texto1 = $r->fields['texto1'];
			$this->texto2 = $r->fields['texto2'];
			$this->texto3 = $r->fields['texto3'];
			$this->texto4 = $r->fields['texto4'];
			return true;
		}else
			return false;
	}
	
	function __construct(){
	
	}
	
	function get_all($conn, $from=0, $max=0,$escEnEje,$orden="id"){
		$q = "SELECT * FROM politicas_disposiciones ";
		$q.= "ORDER BY $orden ";
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		
		while(!$r->EOF){
			$ue = new politicas_disposiciones();
			$ue->get($conn, $r->fields['id'], $escEnEje);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $id_escenario, $id_tipo_gaceta, $ano, $texto1, $texto2, $texto3, $texto4){
		$q = "INSERT INTO politicas_disposiciones ";
		$q.= "(id_escenario, id_tipo_gaceta, ano, texto1, texto2, texto3, texto4) ";
		$q.= "VALUES ('$id_escenario', '$id_tipo_gaceta', '$ano', '$texto1', '$texto2', '$texto3', '$texto4') ";
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function set($conn, $id, $id_escenario, $id_tipo_gaceta, $ano, $texto1, $texto2, $texto3, $texto4){
		$q = "UPDATE politicas_disposiciones SET id_escenario='$id_escenario', ";
		$q.= "id_tipo_gaceta = '$id_tipo_gaceta', ano = '$ano', ";
		$q.= "texto1 = '$texto1', texto2 = '$texto2', texto3 = '$texto3', texto4 = '$texto4' ";
		$q.= "WHERE id='$id' ";
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $id){
		$q = "DELETE FROM politicas_disposiciones WHERE id='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	function busca_politica_disposicion($conn, $id_tipo_gaceta, $ano){
		$q = "SELECT * FROM puser.politicas_disposiciones ";
		$q.= "WHERE id_tipo_gaceta = '$id_tipo_gaceta' AND ano = '$ano'";
		//die($q);
		$r = $conn->Execute($q);
		if($r) {
			$this->id = $r->fields['id'];
			$this->id_escenario = $r->fields['id_escenario'];
			$this->escenario = escenarios::get_descripcion($conn, $r->fields['id_escenario']);
			$this->id_tipo_gaceta = $r->fields['id_tipo_gaceta'];
			$this->tipo_gaceta = gacetas::get_descripcion($conn, $r->fields['id_tipo_gaceta']);
			$this->ano = $r->fields['ano'];
			$this->texto1 = $r->fields['texto1'];
			$this->texto2 = $r->fields['texto2'];
			$this->texto3 = $r->fields['texto3'];
			$this->texto4 = $r->fields['texto4'];
			return true;
		}else
			return false;
	}
	
	
}
?>
