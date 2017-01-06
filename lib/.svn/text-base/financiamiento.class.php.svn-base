<?
class financiamiento{

	// Propiedades

	var $id;
	var $descripcion;
	var $genera_retenciones;
	var $genera_pagos_parciales;

	var $total;

	function get($conn, $id){
		$q = "SELECT * FROM financiamiento ";
		$q.= "WHERE id='$id'";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->descripcion = $r->fields['descripcion'];
			$this->genera_retenciones = $r->fields['genera_retenciones'];
			$this->genera_pagos_parciales = $r->fields['genera_pagos_parciales'];
			return true;
		}else
			return false;
	}

	function get_all($conn, $from=0, $max=0,$orden="id"){
		$q = "SELECT * FROM financiamiento ";
		$q.= "ORDER BY $orden ";
		
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new financiamiento;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $descripcion){    //, $genera_retenciones, $genera_pagos_parciales){
		try{
			$genera_retenciones = ($genera_retenciones)? "true" : "false";
			$genera_pagos_parciales = ($genera_pagos_parciales)? "true" : "false";
			$q = "INSERT INTO financiamiento ";
			$q.= "(descripcion, genera_retenciones, genera_pagos_parciales) ";
			$q.= "VALUES ";
			$q.= "('$descripcion', $genera_retenciones, $genera_pagos_parciales ) ";
			//die($q);
			$conn->Execute($q);
			return REG_ADD_OK;
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

	function set($conn, $id, $descripcion){    //, $genera_retenciones, $genera_pagos_parciales){
		try{
			$genera_retenciones = ($genera_retenciones)? "true" : "false";
			$genera_pagos_parciales = ($genera_pagos_parciales)? "true" : "false";
			$q = "UPDATE financiamiento SET descripcion='$descripcion', ";
			$q.= "genera_retenciones = $genera_retenciones, genera_pagos_parciales = $genera_pagos_parciales ";
			$q.= "WHERE id='$id' ";
			//die($q);
			$conn->Execute($q);
			return REG_SET_OK;
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

	function del($conn, $id){
		try{
			$q = "DELETE FROM financiamiento WHERE id='$id'";
			$conn->Execute($q);
			return REG_DEL_OK;
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
