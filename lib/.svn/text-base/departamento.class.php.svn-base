<?
class departamento{

	// Propiedades
	var $int_cod;
	var $dep_cod;
	var $dep_nom;
	var $div_cod;
	var $div_nom;
	var $emp_cod;
	var $dep_estatus;
	var $dep_ord;
	var $uni_cod;
	var $uni_nom;
	//PARA LISTADO
	var $dep_estatus_desc;
	
	
	var $total;

	function get($conn, $int_cod){
		try {
			$q = "SELECT * FROM rrhh.departamento WHERE int_cod=$int_cod";
			//die($q);
			$r = $conn->Execute($q);
			if(!$r->EOF){
				$this->int_cod = $r->fields['int_cod'];
				$this->dep_cod = $r->fields['dep_cod'];
				$this->dep_nom = $r->fields['dep_nom'];
				$this->div_cod = $r->fields['div_cod'];
				$this->dep_estatus = $r->fields['dep_estatus'];
				$this->dep_ord = $r->fields['dep_ord'];
				$this->uni_cod = $r->fields['unidad_ejecutora_cod'];
				$this->dep_estatus_desc = $r->fields['dep_estatus']==0 ? 'Activo' : 'Inactivo' ;

				$qD = "SELECT * FROM rrhh.division WHERE int_cod=$this->div_cod";
				$rD = $conn->Execute($qD);
				$this->div_nom = $rD->fields['div_nom'];

				$qD = "SELECT * FROM puser.unidades_ejecutoras WHERE id='".$this->uni_cod."'";
				$rD = $conn->Execute($qD);
				$this->uni_nom = $rD->fields['descripcion'];
				
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

	function get_all($conn,$emp_cod, $orden="A.int_cod",$EstatusB=0){
		try {
			$q = "SELECT A.int_cod FROM rrhh.departamento as A INNER JOIN rrhh.division as B ON A.div_cod=B.int_cod WHERE B.emp_cod=$emp_cod AND (A.dep_estatus=$EstatusB OR $EstatusB=2)";
			$q.= " ORDER BY $orden ";
			$r = $conn->Execute($q);
			$collection=array();
			while(!$r->EOF){
				$ue = new departamento;
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

	function add($conn, $dep_cod, $dep_nom, $div_cod, $dep_estatus, $dep_ord,$uni_cod){
		try {
			$uni_cod = empty($uni_cod) ? '' : $uni_cod;
			$q = "INSERT INTO rrhh.departamento ";
			$q.= "(dep_cod, dep_nom, div_cod, dep_estatus, dep_ord,unidad_ejecutora_cod) ";
			$q.= "VALUES ";
			$q.= "('$dep_cod', '$dep_nom', $div_cod, $dep_estatus, $dep_ord, '$uni_cod') ";
			//die($q);
			$conn->Execute($q);
			$q = "SELECT MAX(int_cod) AS int_cod ";
			$q.= "FROM rrhh.departamento ";
			//die($q);
			$r = $conn->Execute($q);
			if($dep_estatus==0){	
				$ue = new departamento;
				$ue->reorder($conn, $dep_ord,$r->fields['int_cod']);
			}
		}
		catch(ADODB_Exception $e ){
			if($e->getCode()==-1)
				return ERROR_CATCH_VFK;
			elseif($e->getCode()==-5)
				return ERROR_CATCH_VUK;
			else
				return ERROR_CATCH_GENERICO;
		}
	}

	function set($conn, $int_cod, $dep_cod, $dep_nom, $div_cod, $dep_estatus, $dep_ord,$uni_cod){
		try {
			$uni_cod = empty($uni_cod) ? '' : $uni_cod;
			$q = "UPDATE rrhh.departamento SET dep_cod='$dep_cod',dep_nom='$dep_nom',div_cod='$div_cod',dep_estatus=$dep_estatus,dep_ord=$dep_ord, unidad_ejecutora_cod='$uni_cod'";
			$q.= "WHERE int_cod=$int_cod";	
			//die($q);
			$conn->Execute($q);
			if($dep_estatus==0){	
				$ue = new departamento;
				$ue->reorder($conn, $dep_ord, $int_cod);
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

	function del($conn, $int_cod){
		try {
			$q = "DELETE FROM rrhh.departamento WHERE int_cod='$int_cod'";
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
	function reorder($conn, $dep_ord,$int_cod){
		try {
			$q = "SELECT int_cod,dep_ord FROM rrhh.departamento WHERE dep_ord=$dep_ord AND dep_estatus=0 AND int_cod!=$int_cod";
			$r = $conn->Execute($q);
			if($r->EOF){
				return false;
			}else{
				$nuevo = $r->fields['dep_ord'] + 1; 
				$ue = new departamento;
				$ue->reorder($conn, $nuevo,$int_cod);
				while(!$r->EOF){
					$int_cod = $r->fields['int_cod'];
					$q = "UPDATE rrhh.departamento SET dep_ord=$nuevo";
					$q.= "WHERE int_cod=$int_cod";
					$conn->Execute($q);
					$r->movenext();
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
			$listado[0]['C'] = 'dep_cod'; $listado[0]['C2'] = 'dep_cod'; $listado[0]['D'] = 'Codigo'; 

			$listado[1]['C'] = 'dep_nom'; $listado[1]['C2'] = 'dep_nom'; $listado[1]['D'] = 'Nombre'; 

			$listado[2]['C'] = 'div_nom'; $listado[2]['C2'] = 'div_cod'; $listado[2]['D'] = 'Division'; 

			$listado[3]['C'] = 'dep_estatus_desc'; $listado[3]['C2'] = 'dep_estatus'; $listado[3]['D'] = 'Estatus'; 

			$listado[4]['C'] = 'dep_ord'; $listado[4]['C2'] = 'dep_ord'; $listado[4]['D'] = 'Orden'; 

			$listado[5]['C'] = 'uni_nom'; $listado[5]['C2'] = 'unidad_ejecutora_cod'; $listado[5]['D'] = 'Unidad Ejecutora'; 


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
