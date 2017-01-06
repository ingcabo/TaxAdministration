<?
class constante{

	// Propiedades
	var $int_cod;
	var $cons_cod;
	var $cons_nom;
	var $cons_val;
	// PARA LISTADO
	var $cons_val_desc;

	var $total;

	function get($conn, $int_cod){
		try {
			$q = "SELECT * FROM rrhh.constante WHERE int_cod=$int_cod";
			//die($q);
			$r = $conn->Execute($q);
			if(!$r->EOF){
				$this->int_cod = $r->fields['int_cod'];
				$this->cons_cod = $r->fields['cons_cod'];
				$this->cons_nom = $r->fields['cons_nom'];
				$this->cons_val = $r->fields['cons_val'];
				$this->cons_val_desc = muestrafloat($r->fields['cons_val']);
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
			$q = "SELECT * FROM rrhh.constante ";
			$q.= "ORDER BY $orden ";
			$r = $conn->Execute($q);
			$collection=array();
			while(!$r->EOF){
				$ue = new constante;
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
	function add($conn, $cons_cod, $cons_nom, $cons_val){
		try {
			$q = "INSERT INTO rrhh.constante ";
			$q.= "(cons_cod, cons_nom, cons_val) ";
			$q.= "VALUES ";
			$q.= "('$cons_cod', '$cons_nom', '$cons_val') ";
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

	function set($conn, $int_cod, $cons_cod, $cons_nom, $cons_val){
		try {
			$q = "UPDATE rrhh.constante SET cons_cod='$cons_cod',cons_nom='$cons_nom', cons_val='$cons_val'";
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
			$q = "DELETE FROM rrhh.constante WHERE int_cod='$int_cod'";
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
			$listado[0]['C'] = 'cons_cod'; $listado[0]['C2'] = 'cons_cod'; $listado[0]['D'] = 'Codigo'; 

			$listado[1]['C'] = 'cons_nom'; $listado[1]['C2'] = 'cons_nom'; $listado[1]['D'] = 'Nombre'; 

			$listado[2]['C'] = 'cons_val_desc'; $listado[2]['C2'] = 'cons_val'; $listado[2]['D'] = 'Valor'; 

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
