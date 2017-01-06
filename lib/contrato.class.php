<?
class contrato{

	// Propiedades
	var $int_cod;
	var $cont_cod;
	var $cont_nom;
	var $cont_tipo;
	var $emp_cod;
	var $cont_fec_ini;
	var $cont_estatus;
	// PARA LISTADO
	var $cont_tipo_desc;
	var $cont_fec_ini_desc;
	
	var $total;

	function get($conn, $int_cod){
		try {
			$q = "SELECT * FROM rrhh.contrato WHERE int_cod=$int_cod";
			//die($q);
			$r = $conn->Execute($q);
			if(!$r->EOF){
				$this->int_cod = $r->fields['int_cod'];
				$this->cont_cod = $r->fields['cont_cod'];
				$this->cont_nom = $r->fields['cont_nom'];
				$this->cont_tipo = $r->fields['cont_tipo'];
				$this->emp_cod = $r->fields['emp_cod'];
				$this->cont_fec_ini = $r->fields['cont_fec_ini'];
				$this->cont_estatus = $r->fields['cont_estatus'];
				$this->cont_tipo_desc = $r->fields['cont_tipo']==0 ? 'Semanal' : ($r->fields['cont_tipo']==1 ? 'Quincenal' : ($r->fields['cont_tipo']==2 ? 'Mensual' : 'Otros')) ;
				$this->cont_fec_ini_desc = muestrafecha($r->fields['cont_fec_ini']);
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

	function get_all($conn,$emp_cod, $Tipo, $Valor, $TipoE, $orden="int_cod"){
		try {
			$TipoE = empty($TipoE) ? 0 : $TipoE;
			if(empty($Valor)){
			$q = "SELECT * FROM rrhh.contrato WHERE emp_cod=$emp_cod AND cont_estatus=$TipoE ";
			$q.= "ORDER BY $orden ";
			}
			elseif($Tipo==0){
			$q = "SELECT * FROM rrhh.contrato ";
			$q.= "where cont_cod ILIKE '%$Valor%' AND cont_estatus=$TipoE  ";
			$q.= "ORDER BY $orden ";
			}
			elseif($Tipo==1){
			$q = "SELECT * FROM rrhh.contrato ";
			$q.= "where cont_nom ILIKE '%$Valor%' AND cont_estatus=$TipoE  ";
			$q.= "ORDER BY $orden ";
			}
			$r = $conn->Execute($q);
			$collection=array();
			while(!$r->EOF){
				$ue = new contrato;
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

	function add($conn, $cont_cod, $cont_nom, $cont_tipo, $cont_fec_ini, $emp_cod, $cont_estatus){
		try {
			$q = "INSERT INTO rrhh.contrato ";
			$q.= "(cont_cod, cont_nom, cont_tipo, cont_fec_ini, emp_cod, cont_estatus) ";
			$q.= "VALUES ";
			$q.= "('$cont_cod', '$cont_nom','$cont_tipo', '".guardafecha($cont_fec_ini)."', $emp_cod, $cont_estatus) ";
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

	function set($conn, $int_cod, $cont_cod, $cont_nom, $cont_tipo, $cont_fec_ini, $status, $cont_estatus){
		try {
			$q = "UPDATE rrhh.contrato SET cont_cod='$cont_cod',cont_nom='$cont_nom',cont_estatus=$cont_estatus ";
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
			$q = "DELETE FROM rrhh.contrato WHERE int_cod='$int_cod'";
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
			$listado[0]['C'] = 'cont_cod'; $listado[0]['C2'] = 'cont_cod'; $listado[0]['D'] = 'Codigo'; 

			$listado[1]['C'] = 'cont_nom'; $listado[1]['C2'] = 'cont_nom'; $listado[1]['D'] = 'Nombre'; 
			
			$listado[2]['C'] = 'cont_tipo_desc'; $listado[2]['C2'] = 'cont_tipo'; $listado[2]['D'] = 'Tipo'; 

			$listado[3]['C'] = 'cont_fec_ini_desc'; $listado[3]['C2'] = 'cont_fec_ini'; $listado[3]['D'] = 'Fecha de Inicio'; 

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
