<?
class cargo{

	// Propiedades
	var $int_cod;
	var $car_cod;
	var $car_nom;
	var $car_sueldo;
	var $car_ord;
	var $car_cant;
	var $car_actuales;
	var $car_vac;
	var $car_estatus;

	var $total;

	function get($conn, $int_cod){
		try {
			$q = "SELECT * FROM rrhh.cargo WHERE int_cod=$int_cod";
			//die($q);
			$r = $conn->Execute($q);
			if(!$r->EOF){
				$this->int_cod = $r->fields['int_cod'];
				$this->car_cod = $r->fields['car_cod'];
				$this->car_nom = $r->fields['car_nom'];
				$this->car_sueldo = muestrafloat($r->fields['car_sueldo']);
				$this->car_ord = $r->fields['car_ord'];
				$this->car_cant = $r->fields['car_cant'];
				$this->car_estatus = $r->fields['car_estatus'];
				$q = "select count (int_cod) as cant from rrhh.trabajador 
					where tra_vac = 0 AND car_cod = $this->int_cod  AND tra_estatus!= 4  AND tra_estatus != 5";
				//die($q);
				$rC = $conn->Execute($q);
				$this->car_actuales = $rC->fields['cant'];
				$q = "select count (int_cod) as cant from rrhh.trabajador 
					where  tra_vac = 1 AND car_cod = $this->int_cod  AND tra_estatus!= 4  AND tra_estatus != 5";
				//die($q);
				$rC = $conn->Execute($q);
				$this->car_vac = $rC->fields['cant'];
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
			$q = "SELECT * FROM rrhh.cargo WHERE car_estatus=$TipoE ";
			$q.= "ORDER BY $orden ";
			}
			elseif($Tipo==0){
			$q = "SELECT * FROM rrhh.cargo ";
			$q.= "where car_cod ILIKE '%$Valor%' AND car_estatus=$TipoE ";
			$q.= "ORDER BY $orden ";
			}
			elseif($Tipo==1){
			$q = "SELECT * FROM rrhh.cargo ";
			$q.= "where car_nom ILIKE '%$Valor%' AND car_estatus=$TipoE ";
			$q.= "ORDER BY $orden ";
			}
			//die($q);				
			$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
			$collection=array();
			while(!$r->EOF){
				$ue = new cargo;
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

	function add($conn, $car_cod, $car_nom,$car_sueldo,$car_cant,$car_estatus,$departamentos){
		try {
			$q = "INSERT INTO rrhh.cargo ";
			$q.= "(car_cod, car_nom,car_sueldo, car_cant, car_estatus) ";
			$q.= "VALUES ";
			$q.= "('$car_cod', '$car_nom',$car_sueldo,$car_cant,$car_estatus) ";
			//die($q);
			$conn->Execute($q);
			$q = "SELECT MAX(int_cod) AS int_cod ";
			$q.= "FROM rrhh.cargo ";
			//die($q);
			$r = $conn->Execute($q);
			$ue = new cargo;
			$ue->reorder($conn, $car_ord,$r->fields['int_cod']);
			$this->GuardarOrdenDepartamento($conn,$car_cod,$departamentos);
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

	function set($conn, $int_cod, $car_cod, $car_nom,$car_sueldo,$car_cant,$car_estatus,$departamentos){
		try {
			$q = "UPDATE rrhh.cargo SET car_cod='$car_cod',car_nom='$car_nom',car_sueldo=$car_sueldo,car_cant=$car_cant,car_estatus=$car_estatus ";
			$q.= "WHERE int_cod=$int_cod";	
			//die($q);
			$conn->Execute($q);
			$ue = new cargo;
			$ue->reorder($conn, $car_ord, $int_cod);
			$this->GuardarOrdenDepartamento($conn,$car_cod,$departamentos);
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
			$q = "DELETE FROM rrhh.cargo WHERE int_cod='$int_cod'";
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
	function reorder($conn, $car_ord,$int_cod){
		try {
			$q = "SELECT int_cod,car_ord FROM rrhh.cargo WHERE car_ord=$car_ord AND int_cod!=$int_cod";
			$r = $conn->Execute($q);
			if($r->EOF){
				return false;
			}else{
				$nuevo = $r->fields['car_ord'] + 1; 
				$ue = new cargo;
				$ue->reorder($conn, $nuevo,$int_cod);
				while(!$r->EOF){
					$int_cod = $r->fields['int_cod'];
					$q = "UPDATE rrhh.cargo SET car_ord=$nuevo";
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
			$listado[0]['C'] = 'car_cod'; $listado[0]['C2'] = 'car_cod'; $listado[0]['D'] = 'Codigo'; 

			$listado[1]['C'] = 'car_nom'; $listado[1]['C2'] = 'car_nom'; $listado[1]['D'] = 'Nombre'; 

			$listado[2]['C'] = 'car_sueldo'; $listado[2]['C2'] = 'car_sueldo'; $listado[2]['D'] = 'Sueldo'; 

			$listado[3]['C'] = 'car_ord'; $listado[3]['C2'] = 'car_ord'; $listado[3]['D'] = 'Orden'; 

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
	
	function getCargosxUE($conn, $idUE){
		try {
			$q = "SELECT c.car_nom, c.car_sueldo, t.car_cod,COUNT(t.car_cod) AS cantidad FROM rrhh.trabajador AS t ";
			$q.= "INNER JOIN rrhh.departamento AS d ON (t.dep_cod = d.int_cod) ";
			$q.= "INNER JOIN rrhh.cargo AS c ON (t.car_cod = c.int_cod) ";
			$q.= "WHERE t.dep_cod = $idUE AND t.tra_estatus = 0 ";
			$q.= "GROUP BY 1,2,3 ";
			$q.= "ORDER BY 2 DESC ";
			//die($q);
			$r = $conn->Execute($q);
			$coleccion=array();
			while(!$r->EOF){
				$ue = new cargo;
				$ue->id_car = $r->fields['car_cod'];
				$ue->sueldo = $r->fields['car_sueldo'];
				$ue->cantidad = $r->fields['cantidad'];
				$coleccion[] = $ue;
				$r->movenext();
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
	
	function getAll_by_UE($conn, $idUE){
		try {
			$q = "SELECT c.car_nom AS descripcion, t.car_cod AS id FROM rrhh.trabajador AS t ";
			$q.= "INNER JOIN rrhh.departamento AS d ON (t.dep_cod = d.int_cod) ";
			$q.= "INNER JOIN rrhh.cargo AS c ON (t.car_cod = c.int_cod) ";
			$q.= "WHERE t.dep_cod = $idUE AND t.tra_estatus = 0 ";
			$q.= "ORDER BY car_nom ";
			//die($q);
			$r = $conn->Execute($q);
			$coleccion=array();
			while(!$r->EOF){
				$ue = new cargo;
				$ue->id = $r->fields['id'];
				$ue->descripcion = $r->fields['descripcion'];
				$coleccion[] = $ue;
				$r->movenext();
			}
			//die(var_dump($coleccion));
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
	
	function total_registro_busqueda($conn,$Tipo, $Valor, $TipoE, $orden="car_cod")
	{
		$TipoE = empty($TipoE) ? 0 : $TipoE;
		if(empty($Valor)){
		$q = "SELECT * FROM rrhh.cargo WHERE car_estatus=$TipoE ";
		}
		elseif($Tipo==0){
		$q = "SELECT * FROM rrhh.cargo ";
		$q.= "where car_ord ILIKE '%$Valor%' AND car_estatus=$TipoE ";
		$q.= "ORDER BY $orden ";
		}
		elseif($Tipo==1){
		$q = "SELECT * FROM rrhh.cargo ";
		$q.= "where car_nom ILIKE '%$Valor%' AND car_estatus=$TipoE ";
		$q.= "ORDER BY $orden ";
		}
		$r = $conn->Execute($q);
		$total = $r->RecordCount();
		return $total;
	}
	function GuardarOrdenDepartamento($conn,$car_cod,$departamentos){
		try {
			$JsonRec = new Services_JSON();
			$JsonRec=$JsonRec->decode(str_replace("\\","",$departamentos));
			if(is_array($JsonRec->departamentos)){
				$q = "SELECT int_cod FROM rrhh.cargo WHERE car_cod=$car_cod ";
				$r = $conn->Execute($q);
				$CargoAux=$r->fields['int_cod'];
				$q = "DELETE FROM rrhh.dep_carg WHERE car_cod=$CargoAux";
				$r = $conn->Execute($q);
				foreach($JsonRec->departamentos as $Departamento){
					$VariableAux = (!empty($Departamento[1])) ? $Departamento[1] : 0;
					if($VariableAux){
						$q = "INSERT INTO rrhh.dep_carg (dep_cod,car_cod,orden) VALUES ($Departamento[0],$CargoAux,$VariableAux)";
						$r = $conn->Execute($q);
					}
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
}
?>
