<?
class empresa{

	// Propiedades
	var $int_cod;
	var $emp_cod;
	var $emp_nom;
	var $emp_nit ;
	var $emp_telf;
	var $emp_dir;
	var $emp_rif;
	
	var $total;

	function get($conn, $int_cod){
		try {
			$q = "SELECT * FROM rrhh.empresa WHERE int_cod=$int_cod";
			//die($q);
			$r = $conn->Execute($q);
			if(!$r->EOF){
				$this->int_cod = $r->fields['int_cod'];
				$this->emp_cod = $r->fields['emp_cod'];
				$this->emp_nom = $r->fields['emp_nom'];
				$this->emp_rif = $r->fields['emp_rif'];
				$this->emp_nit = $r->fields['emp_nit'];
				$this->emp_telf = $r->fields['emp_telf'];
				$this->emp_dir = $r->fields['emp_dir'];
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

	function get_all($conn, $orden="int_cod"){
		try {
			$q = "SELECT * FROM rrhh.empresa ";
			$q.= "ORDER BY $orden ";
			$r = $conn->Execute($q);
			$collection=array();
			while(!$r->EOF){
				$ue = new empresa;
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

	function add($conn, $emp_cod, $emp_nom, $emp_rif, $emp_nit, $emp_telf, $emp_dir){
		try {
			$q = "INSERT INTO rrhh.empresa ";
			$q.= "(emp_cod, emp_nom, emp_rif, emp_nit, emp_telf, emp_dir) ";
			$q.= "VALUES ";
			$q.= "('$emp_cod', '$emp_nom', '$emp_rif', '$emp_nit', '$emp_telf', '$emp_dir') ";
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

	function set($conn, $int_cod, $emp_cod, $emp_nom, $emp_rif, $emp_nit, $emp_telf, $emp_dir){
		try {
			$q = "UPDATE rrhh.empresa SET emp_cod='$emp_cod',emp_nom='$emp_nom',emp_rif='$emp_rif',emp_nit='$emp_nit',emp_telf='$emp_telf',emp_dir='$emp_dir' ";
			$q.= "WHERE int_cod=$int_cod";	
			//die($q);
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
			$q = "DELETE FROM rrhh.empresa WHERE int_cod='$int_cod'";
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
}
?>
