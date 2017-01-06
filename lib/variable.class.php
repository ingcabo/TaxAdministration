<?
class variable{

	// Propiedades
	var $int_cod;
	var $var_cod;
	var $var_nom;
	var $var_tipo;
	var $var_estatus;
	//PARA LISTADO
	var $var_tipo_desc;

	var $total;

	function get($conn, $int_cod){
		try {
			$q = "SELECT * FROM rrhh.variable WHERE int_cod=$int_cod";
			//die($q);
			$r = $conn->Execute($q);
			if(!$r->EOF){
				$this->int_cod = $r->fields['int_cod'];
				$this->var_cod = $r->fields['var_cod'];
				$this->var_nom = $r->fields['var_nom'];
				$this->var_tipo = $r->fields['var_tipo'];
				$this->var_estatus = $r->fields['var_estatus'];
				$this->var_tipo_desc = $r->fields['var_tipo']==0 ? 'Fija' : 'No Fija' ;
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

	function get_all($conn, $orden="int_cod", $Tipo, $Valor, $TipoE, $max=10, $from=1){
		try {
			$TipoE = empty($TipoE) ? 0 : $TipoE;
			if(empty($Valor)){
			$q = "SELECT * FROM rrhh.variable WHERE var_estatus=$TipoE ";
			$q.= "ORDER BY $orden ";
			}
			elseif($Tipo==0){
			$q = "SELECT * FROM rrhh.variable ";
			$q.= "where var_cod ILIKE '%$Valor%' AND var_estatus=$TipoE ";
			$q.= "ORDER BY $orden ";			
			}
			elseif($Tipo==1){
			$q = "SELECT * FROM rrhh.variable ";
			$q.= "where var_nom ILIKE '%$Valor%' AND var_estatus=$TipoE ";
			$q.= "ORDER BY $orden ";			
			}
			$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
			$collection=array();
			while(!$r->EOF){
				$ue = new variable;
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

	function add($conn, $var_cod, $var_nom, $var_tipo, $var_estatus){

		try {
			$q = "INSERT INTO rrhh.variable ";
			$q.= "(var_cod, var_nom, var_tipo, var_estatus) ";
			$q.= "VALUES ";
			$q.= "('$var_cod', '$var_nom', '$var_tipo', $var_estatus) ";
			$conn->Execute($q); 
		}
		catch( ADODB_Exception $e ){
			if($e->getCode()==-1)
				return ERROR_CATCH_VFK;
			elseif($e->getCode()==-5)
				return ERROR_CATCH_VUK;
			else
				return ERROR_CATCH_GENERICO; 
			///return	$e->getMessage();
		}
	}

	function set($conn, $int_cod, $var_cod, $var_nom, $var_tipo, $var_estatus){
		try {
			$q = "UPDATE rrhh.variable SET var_cod='$var_cod',var_nom='$var_nom', var_tipo='$var_tipo',var_estatus=$var_estatus";
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
			$q = "DELETE FROM rrhh.variable WHERE int_cod='$int_cod'";
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
			$listado[0]['C'] = 'var_cod'; $listado[0]['C2'] = 'var_cod'; $listado[0]['D'] = 'Codigo'; 

			$listado[1]['C'] = 'var_nom'; $listado[1]['C2'] = 'var_nom'; $listado[1]['D'] = 'Nombre'; 
			
			$listado[2]['C'] = 'var_tipo_desc'; $listado[2]['C2'] = 'var_tipo'; $listado[2]['D'] = 'Tipo'; 

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
	
	function total_registro_busqueda($conn,$Tipo, $Valor, $TipoE, $orden="var_cod")
	{
		$TipoE = empty($TipoE) ? 0 : $TipoE;
		if(empty($Valor)){
		$q = "SELECT * FROM rrhh.variable WHERE var_estatus=$TipoE ";
		}
		elseif($Tipo==0){
		$q = "SELECT * FROM rrhh.variable ";
		$q.= "where var_cod ILIKE '%$Valor%' AND var_estatus=$TipoE ";
		$q.= "ORDER BY $orden ";
		}
		elseif($Tipo==1){
		$q = "SELECT * FROM rrhh.variable ";
		$q.= "where var_nom ILIKE '%$Valor%' AND var_estatus=$TipoE ";
		$q.= "ORDER BY $orden ";
		}

		$r = $conn->Execute($q);
		$total = $r->RecordCount();
		return $total;
	}
}
?>
