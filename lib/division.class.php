<?
class division{

	// Propiedades
	var $int_cod;
	var $div_cod;
	var $div_nom;
	var $emp_cod;

	var $total;

	function get($conn, $int_cod){
		try {
			$q = "SELECT * FROM rrhh.division WHERE int_cod=$int_cod";
			//die($q);
			$r = $conn->Execute($q);
			if(!$r->EOF){
				$this->int_cod = $r->fields['int_cod'];
				$this->div_cod = $r->fields['div_cod'];
				$this->div_nom = $r->fields['div_nom'];
				$this->emp_cod = $r->fields['emp_cod'];
			}
		}
		catch( ADODB_Exception $e ){
			if($e->getCode()==-1)
				return ERROR_CATCH_VFK;
			elseif($e->getCode()==-5)
				return ERROR_CATCH_VUK;
			else
				return ERROR_CATCH_GENERICO;
		}
	}

	function get_all($conn,$emp_cod,$orden="int_cod"){
		try {
			$q = "SELECT * FROM rrhh.division WHERE emp_cod=$emp_cod ";
			$q.= "ORDER BY $orden ";
//			die($q);
			$r = $conn->Execute($q);
			$collection=array();
			while(!$r->EOF){
				$ue = new division;
				$ue->get($conn, $r->fields['int_cod']);
				$coleccion[] = $ue;
				$r->movenext();
			}
			$this->total = $r->RecordCount();
			return $coleccion;
		}
		catch( ADODB_Exception $e ){
			if($e->getCode()==-1)
				return ERROR_CATCH_VFK;
			elseif($e->getCode()==-5)
				return ERROR_CATCH_VUK;
			else
				return ERROR_CATCH_GENERICO;
		}
	}

	function add($conn, $div_cod, $div_nom, $emp_cod){
		try {
			$q = "INSERT INTO rrhh.division ";
			$q.= "(div_cod, div_nom, emp_cod) ";
			$q.= "VALUES ";
			$q.= "('$div_cod', '$div_nom', $emp_cod) ";
	//		die($q);
			$conn->Execute($q);
		}
		catch( ADODB_Exception $e ){
			if($e->getCode()==-1)
				return ERROR_CATCH_VFK;
			elseif($e->getCode()==-5)
				return ERROR_CATCH_VUK;
			else
				return ERROR_CATCH_GENERICO;
		}
	}

	function set($conn, $int_cod, $div_cod, $div_nom){
		try {
			$q = "UPDATE rrhh.division SET div_cod='$div_cod',div_nom='$div_nom' ";
			$q.= "WHERE int_cod=$int_cod";	
		//	die($q);
			$conn->Execute($q);
		}
		catch( ADODB_Exception $e ){
			if($e->getCode()==-1)
				return ERROR_CATCH_VFK;
			elseif($e->getCode()==-5)
				return ERROR_CATCH_VUK;
			else
				return ERROR_CATCH_GENERICO;
		}
	}

	function del($conn, $int_cod){
		try {
			$q = "DELETE FROM rrhh.division WHERE int_cod='$int_cod'";
			$conn->Execute($q);
		}
		catch( ADODB_Exception $e ){
			if($e->getCode()==-1)
				return ERROR_CATCH_VFK;
			elseif($e->getCode()==-5)
				return ERROR_CATCH_VUK;
			else
				return ERROR_CATCH_GENERICO;
		}
	}
	function getListadoKeys(){
		try {
			//PARA LISTADO
			$listado[0]['C'] = 'div_cod'; $listado[0]['C2'] = 'div_cod'; $listado[0]['D'] = 'Codigo'; 

			$listado[1]['C'] = 'div_nom'; $listado[1]['C2'] = 'div_nom'; $listado[1]['D'] = 'Nombre'; 

			return $listado;
		}
		catch( ADODB_Exception $e ){
			if($e->getCode()==-1)
				return ERROR_CATCH_VFK;
			elseif($e->getCode()==-5)
				return ERROR_CATCH_VUK;
			else
				return ERROR_CATCH_GENERICO;
		}
	}
}
?>
