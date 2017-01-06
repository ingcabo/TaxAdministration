<?
class concepto{

	// Propiedades
	var $int_cod;
	var $conc_cod;
	var $conc_nom;
	var $conc_tipo ;
	var $conc_estatus;
	var $conc_form;
	var $conc_desc;
	var $conc_aporte;
	var $presupuesto;
	var $cuenta_contable;
	var $conc_retencion;
	var $idCtaAporte;
	//PARA LISTADO
	var $conc_tipo_desc;
	var $conc_estatus_desc;
	
	var $total;

	function get($conn, $int_cod){
		try {
			$q = "SELECT * FROM rrhh.concepto WHERE int_cod=$int_cod";
			//die($q);
			$r = $conn->Execute($q);
			if(!$r->EOF){
				$this->int_cod = $r->fields['int_cod'];
				$this->conc_cod = $r->fields['conc_cod'];
				$this->conc_nom = $r->fields['conc_nom'];
				$this->conc_tipo = $r->fields['conc_tipo'];
				$this->conc_estatus = $r->fields['conc_estatus'];
				$this->conc_form = $r->fields['conc_form'];
				$this->conc_desc = $r->fields['conc_desc'];
				$this->conc_aporte = $r->fields['conc_aporte'];
				$this->conc_tipo_desc = $r->fields['conc_tipo']==0 ? 'Asignacion' : ($r->fields['conc_tipo']==1 ? 'Deduccion' :  'Acumulado') ;
				$this->conc_estatus_desc = $r->fields['tra_estatus']==0 ? 'Activo' :  'Inactivo';
				$this->cuenta_contable = $r->fields['cuenta_contable'];
				$this->conc_retencion = $r->fields['conc_retencion'];
				$this->idCtaAporte = $r->fields['id_cuenta_aporte'];
			}
			$q = "SELECT * FROM rrhh.conc_part WHERE conc_cod=$int_cod";
			//die($q);
			$r = $conn->Execute($q);
			$i=0;
			while(!$r->EOF){
				$PresupuestoAux[$i][0] = $r->fields['cat_cod'];
				$PresupuestoAux[$i][1] = $r->fields['par_cod'];
				$i++;
				$r->movenext();
			}
			$this->presupuesto = new Services_JSON();
			$this->presupuesto = is_array($PresupuestoAux) ? $this->presupuesto->encode($PresupuestoAux) : false;
			 
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
			$q = "SELECT * FROM rrhh.concepto WHERE conc_estatus = $TipoE ";
			$q.= "ORDER BY $orden ";
			}
			elseif($Tipo==0){
			$q = "SELECT * FROM rrhh.concepto ";
			$q.= "where conc_cod ILIKE '%$Valor%' AND conc_estatus=$TipoE  ";
			$q.= "ORDER BY $orden ";
			}
			elseif($Tipo==1){
			$q = "SELECT * FROM rrhh.concepto ";
			$q.= "where conc_nom ILIKE '%$Valor%' AND conc_estatus=$TipoE  ";
			$q.= "ORDER BY $orden ";
			}
			//die($q);			
			$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
			$collection=array();
			while(!$r->EOF){
				$ue = new concepto;
				$ue->get($conn, $r->fields['int_cod']);
				$coleccion[] = $ue;
				$ue->__destruct();
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
	function __destruct(){
    }
	function add($conn, $conc_cod, $conc_nom, $conc_tipo, $conc_estatus, $conc_form,$conc_desc,$conc_aporte,$presupuesto,$cuenta_contable,$conc_retencion,$idCtaAporte){
		try {
			$conc_aporte = !empty($conc_aporte) ? "'".$conc_aporte."'" : "null";
			$cuenta_contable = !empty($cuenta_contable) ? $cuenta_contable : 0;
			$conc_retencion = !empty($conc_retencion) ? 1 : 0;
			$q = "INSERT INTO rrhh.concepto ";
			$q.= "(conc_cod, conc_nom, conc_tipo, conc_estatus, conc_form,conc_desc,conc_aporte,cuenta_contable,conc_retencion) "; // , id_cuenta_aporte) ";
			$q.= "VALUES ";
			$q.= "('$conc_cod', '$conc_nom', '$conc_tipo', '$conc_estatus', '$conc_form','$conc_desc', $conc_aporte,$cuenta_contable,$conc_retencion) "; //,$idCtaAporte) ";
			//die($q);
			$conn->Execute($q);

			$q = "SELECT MAX(int_cod) AS int_cod FROM rrhh.concepto ";
			$r = $conn->Execute($q);
			$this->GuardarRelacionPartida($conn, $r->fields['int_cod'],$presupuesto); 
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

	function set($conn, $int_cod, $conc_cod, $conc_nom, $conc_tipo, $conc_estatus, $conc_form,$conc_desc,$conc_aporte,$presupuesto,$cuenta_contable,$conc_retencion,$idCtaAporte){
		try {
			$conc_aporte = !empty($conc_aporte) ? "'".$conc_aporte."'" : "null";
			$cuenta_contable = !empty($cuenta_contable) ? $cuenta_contable : 0;
			$conc_retencion = !empty($conc_retencion) ? 1 : 0;
			//die($conc_desc);
			$q = "UPDATE rrhh.concepto SET conc_cod='$conc_cod',conc_nom='$conc_nom',conc_tipo='$conc_tipo',conc_estatus='$conc_estatus',conc_form='$conc_form',conc_desc='$conc_desc',conc_aporte=$conc_aporte, ";
			$q.= "cuenta_contable=$cuenta_contable,conc_retencion=$conc_retencion"; //,id_cuenta_aporte=$idCtaAporte)" ;
			$q.= " WHERE int_cod=$int_cod";	
			//die($q);
			$conn->Execute($q);
			$this->GuardarRelacionPartida($conn, $int_cod,$presupuesto);
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
			$q = "DELETE FROM rrhh.concepto WHERE int_cod='$int_cod'";
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
	function BuscarCombosPresupuesto($conn, $tabla){
		try {
			$q = "SELECT * FROM $tabla ";
			$r = $conn->Execute($q);
			$i=0;
			while(!$r->EOF){
				$Result[$i][0] = $r->fields['id'];
				$Result[$i][1] = $r->fields['descripcion'];
				$i++;
				$r->movenext();
			}
			$JsonEnv = new Services_JSON();
			return is_array($Result) ? $JsonEnv->encode($Result) : false;
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
	function get_conceptos_retenciones($conn){
		$q = "SELECT int_cod, conc_cod, conc_nom, conc_retencion FROM rrhh.concepto WHERE conc_retencion = 1 ORDER BY conc_nom";
		//die($q);
		$r = $conn->Execute($q);
		while(!$r->EOF){
			$conc = new concepto;
			$conc->id = $r->fields['conc_cod'];
			$conc->descripcion = $r->fields['conc_nom'];
			$coleccion[] = $conc;
			$r->movenext();	
		}
		return $coleccion;	
	}
	function GuardarRelacionPartida($conn, $conc_cod,$presupuesto){
		try {
			$JsonRec = new Services_JSON();
			$JsonRec=$JsonRec->decode(str_replace("\\","",$presupuesto));
			if(is_array($JsonRec->Presupuesto)){
				$q = "DELETE FROM rrhh.conc_part WHERE conc_cod=$conc_cod";
				$conn->Execute($q);
				foreach($JsonRec->Presupuesto as $PresupuestoAux){
					$q = "INSERT INTO rrhh.conc_part ";
					$q.= "(cat_cod,par_cod,conc_cod) ";
					$q.= "VALUES ";
					$q.= "('$PresupuestoAux[0]','$PresupuestoAux[1]',$conc_cod)";
					//die($q);
					$conn->Execute($q);
				}
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
	function getListadoKeys(){
		try {
			//PARA LISTADO
			$listado[0]['C'] = 'conc_cod'; $listado[0]['C2'] = 'conc_cod'; $listado[0]['D'] = 'Codigo'; 

			$listado[1]['C'] = 'conc_nom'; $listado[1]['C2'] = 'conc_nom'; $listado[1]['D'] = 'Nombre'; 
			
			$listado[2]['C'] = 'conc_tipo_desc'; $listado[2]['C2'] = 'conc_tipo'; $listado[2]['D'] = 'Tipo'; 

			$listado[3]['C'] = 'conc_estatus_desc'; $listado[3]['C2'] = 'conc_estatus'; $listado[3]['D'] = 'Estatus'; 


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
	function total_registro_busqueda($conn,$Tipo, $Valor, $TipoE, $orden="conc_cod")
	{
		$TipoE = empty($TipoE) ? 0 : $TipoE;
		if(empty($Valor)){
		$q = "SELECT * FROM rrhh.concepto WHERE conc_estatus=$TipoE ";
		}
		elseif($Tipo==0){
		$q = "SELECT * FROM rrhh.concepto ";
		$q.= "where conc_cod ILIKE '%$Valor%' AND conc_estatus=$TipoE ";
		$q.= "ORDER BY $orden ";
		}
		elseif($Tipo==1){
		$q = "SELECT * FROM rrhh.concepto ";
		$q.= "where conc_nom ILIKE '%$Valor%' AND conc_estatus=$TipoE ";
		$q.= "ORDER BY $orden ";
		}

		$r = $conn->Execute($q);
		$total = $r->RecordCount();
		return $total;
	}
}
?>