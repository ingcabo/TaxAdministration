<?
class grupoconceptos{

	// Propiedades
	var $int_cod;
	var $gconc_cod;
	var $gconc_nom;

	var $total;

	function get($conn, $int_cod){
		try {
			$q = "SELECT * FROM rrhh.grupoconceptos WHERE int_cod=$int_cod";
			//die($q);
			$r = $conn->Execute($q);
			if(!$r->EOF){
				$this->int_cod = $r->fields['int_cod'];
				$this->gconc_cod = $r->fields['gconc_cod'];
				$this->gconc_nom = $r->fields['gconc_nom'];
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
			$q = "SELECT * FROM rrhh.grupoconceptos ";
			$q.= "ORDER BY $orden ";
			$r = $conn->Execute($q);
			$collection=array();
			while(!$r->EOF){
				$ue = new grupoconceptos;
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

	function add($conn, $gconc_cod, $gconc_nom){
		try {
			$q = "INSERT INTO rrhh.grupoconceptos ";
			$q.= "(gconc_cod, gconc_nom) ";
			$q.= "VALUES ";
			$q.= "('$gconc_cod', '$gconc_nom') ";
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

	function set($conn, $int_cod, $gconc_cod, $gconc_nom){
		try {
			$q = "UPDATE rrhh.grupoconceptos SET gconc_cod='$gconc_cod',gconc_nom='$gconc_nom' ";
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
			$q = "DELETE FROM rrhh.grupoconceptos WHERE int_cod='$int_cod'";
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
			$listado[0]['C'] = 'gconc_cod'; $listado[0]['C2'] = 'gconc_cod'; $listado[0]['D'] = 'Codigo'; 

			$listado[1]['C'] = 'gconc_nom'; $listado[1]['C2'] = 'gconc_nom'; $listado[1]['D'] = 'Nombre'; 

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
