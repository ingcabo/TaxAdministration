<?
class categorias_programaticas{

	// Propiedades

	var $id;
	var $id_escenario;
	var $escenario;
	var $descripcion;
	var $os;  // objetivo sectorial
	var $dps;  // destinada programa social
	var $po; // presupuesto original
	var $compromisos;
	var $aumentos;
	var $causados;
	var $disminuciones;
	var $pagados;
	var $disponible;
	var $ano;
	var $dp;
	var $status;

	var $total;

	function get($conn, $id, $id_escenario){
		$q = "SELECT * FROM puser.categorias_programaticas WHERE id='$id' AND id_escenario::char(4) = '$id_escenario' ";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->id_escenario = $r->fields['id_escenario'];
			$this->escenario = escenarios::get_descripcion($conn, $r->fields['id_escenario']);
			$this->descripcion = $r->fields['descripcion'];
			$this->os = $r->fields['objetivo_sectorial'];
			$this->dps = $r->fields['destinada_programa_social'];
			$this->po = $r->fields['presupuesto_original'];
			$this->compromisos = $r->fields['compromisos'];
			$this->aumentos = $r->fields['aumentos'];
			$this->causados = $r->fields['causados'];
			$this->disminuciones = $r->fields['disminuciones'];
			$this->pagados = $r->fields['pagados'];
			$this->disponible = $r->fields['disponible'];
			$this->ano = $r->fields['ano'];
			$this->dp = $r->fields['descripcion_programa'];
			$this->status = $r->fields['status'];
			return true;
		}else
			return false;
	}

	function get_all($conn, $from=0, $max=0,$orden="id_escenario, id"){
		try{
			$q = "SELECT * FROM categorias_programaticas ";
			$q.= "ORDER BY $orden ";
			$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
			$collection=array();
			while(!$r->EOF){
				$ue = new categorias_programaticas;
				$ue->get($conn, $r->fields['id'], $r->fields['id_escenario']);
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

	function add($conn, 
							$id, 
							$id_escenario, 
							$desc, 
							$os, 
							$dps,
							$ano,
							$dp,
							$status){
		try{
			$dps = ($dps == 'on')? "true" : "false";
			$q = "INSERT INTO puser.categorias_programaticas ";
			$q.= "(id, id_escenario, descripcion, objetivo_sectorial, destinada_programa_social, ano, descripcion_programa) ";
			$q.= "VALUES ('$id', '$id_escenario', '$desc', '$os', $dps, '$ano', '$dp') ";
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

	function set($conn, 
				$id_nuevo, 
				$id, 
				$id_escenario, 
				$desc, 
				$os, 
				$dps,
				$ano,
				$dp,
				$status){
		try{
			$dps = ($dps == 'on')? "true" : "false";
			$q = "UPDATE puser.categorias_programaticas SET id = '$id_nuevo', id_escenario='$id_escenario', ";
			$q.= "descripcion = '$desc', objetivo_sectorial = '$os', destinada_programa_social = $dps, ano = '$ano', descripcion_programa='$dp'";
			$q.= "WHERE id='$id' AND id_escenario = '$id_escenario' ";
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
	
	function del($conn, $id, $id_escenario){
		try{
			$q = "DELETE FROM puser.categorias_programaticas WHERE id='$id' AND id_escenario = '$id_escenario' ";
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
	function del_escenario($conn, $id){
		try{
			$q = "DELETE FROM categorias_programaticas WHERE id_escenario='$id'"; 
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
	function get_all_by_esc($conn, $id_escenario, $from=0, $max=0,$orden="id"){
		try{
			$q = "SELECT * FROM puser.categorias_programaticas WHERE id_escenario::char(4) = '$id_escenario' ";
			$q.= "ORDER BY $orden ";
			//die($q);
			$r = $conn->Execute($q);
			while(!$r->EOF){
				$ue = new categorias_programaticas;
				$ue->get($conn, $r->fields['id'], $r->fields['id_escenario']);
				$coleccion[] = $ue;
				$r->movenext();
			}
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
	
	function set_by_esc($conn, $id_escenario, $factor){
		try{
			$q = "UPDATE categorias_programaticas SET ";
			$q.= "presupuesto_original = presupuesto_original * $factor";
			$q.= "WHERE id_escenario='$id_escenario' ";
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

	// esta funcion se utiliza al agregar un escenario
	function add_esc($conn, 
							$id, 
							$id_escenario, 
							$desc, 
							$os, 
							$dps, 
							$po, 
							$aumentos, 
							$dism, 
							$compromisos, 
							$causados, 
							$pagados, 
							$disponible,
							$ano,
							$dp){
		try{
			$dps = ($dps == 'on')? "true" : "false";
			$q = "INSERT INTO puser.categorias_programaticas ";
			$q.= "(id, id_escenario, descripcion, objetivo_sectorial, destinada_programa_social, ";
			$q.= "ano, descripcion_programa) ";
			$q.= "VALUES ('$id', '$id_escenario', '$desc', '$os', '$dps', ";
			$q.= "'$ano', '$dp') ";
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
	
	function get_all_by_ue($conn, $ue, $escEnEje, $from=0, $max=0){
		try{
			$q = "SELECT relacion_ue_cp.id_categoria_programatica AS id ";
			$q.= "FROM relacion_ue_cp ";
			$q.= "WHERE ";
			$q.= "relacion_ue_cp.id_unidad_ejecutora = '$ue' ";
			$q.= "AND relacion_ue_cp.id_escenario = '$escEnEje' ";
			//die($q);
			$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
			$collection=array();
			while(!$r->EOF){
				$ue = new categorias_programaticas;
				$ue->get($conn, $r->fields['id'], $escEnEje);
				$coleccion[] = $ue;
				$r->movenext();
			}
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

	function buscar($conn, $id, $id_escenario, $descripcion, $max=10, $from=1, $orden="id_escenario, id"){
		try{
			if(empty($id) && empty($id_escenario) && empty($descripcion))
				return false;
			$q = "SELECT * FROM categorias_programaticas ";
			$q.= "WHERE  1=1 ";
			$q.= !empty($id) ? "AND id = '$id'  ":"";
			$q.= !empty($id_escenario) ? "AND id_escenario = '$id_escenario'  ":"";
			$q.= !empty($descripcion) ? "AND descripcion ILIKE '%$descripcion%'  ":"";
			$q.= "ORDER BY $orden ";
			//die($q);
			$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
			$collection=array();
			while(!$r->EOF){
				$ue = new categorias_programaticas;
				$ue->get($conn, $r->fields['id'], $r->fields['id_escenario']);
				$coleccion[] = $ue;
				$r->movenext();
			}
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
	
	function total_registro_busqueda($conn, $id, $id_escenario, $descripcion, $orden="id_escenario, id"){
		if(empty($id_escenario) && empty($descripcion) && empty($id))
			return false;
		$q = "SELECT * FROM puser.categorias_programaticas ";
		$q.= "WHERE  1=1 ";
		$q.= !empty($id) ? "AND id = '$id'  ":"";
		$q.= !empty($id_escenario) ? "AND id_escenario = '$id_escenario'  ":"";
		$q.= !empty($descripcion) ? "AND descripcion ILIKE '%$descripcion%'  ":"";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		$total = $r->RecordCount();

		return $total;
	}

  // Devuele una coleccion de categorias_programaticas seleccionadas por un query.	
	/*function buscar_query($conn, $query)
  {
		try
    {
			if(empty($query))
				return false;
				
			$r = $conn->Execute($query);
			
			/*$collection=array();
			while(!$r->EOF)
      {
				$result = array();
				$cp->get($conn, $r->fields['id_cp'], $r->fields['id_escenario']);
				$coleccion[] = $ue;
				$r->movenext();
			}
			return $coleccion;*
			return $r;
		}
		catch( ADODB_Exception $e )
    {
			if($e->getCode()==-1)
				return ERROR_CATCH_VFK;
			elseif($e->getCode()==-5)
				return ERROR_CATCH_VUK;
			else
				return ERROR_CATCH_GENERICO;
		}
	}*/

	function get_reporte_cp($conn, $EscEnEje){
	try{
		$q = "SELECT ";
		$q.= "puser.categorias_programaticas.id AS id_cp, ";
		$q.= "puser.categorias_programaticas.descripcion AS categoria_programatica, ";
		$q.= "puser.unidades_ejecutoras.descripcion AS unidad_ejecutora ";
		$q.= "FROM ";
		$q.= "puser.relacion_ue_cp ";
		$q.= "INNER JOIN ";
		$q.= "puser.categorias_programaticas "; 
		$q.= "ON ";
		$q.= "puser.relacion_ue_cp.id_categoria_programatica = puser.categorias_programaticas.id ";
		$q.= "AND ";
		$q.= "puser.relacion_ue_cp.id_escenario = puser.categorias_programaticas.id_escenario ";
		$q.= "INNER JOIN ";
		$q.= "puser.unidades_ejecutoras ";
		$q.= "ON ";
		$q.= "puser.relacion_ue_cp.id_unidad_ejecutora = puser.unidades_ejecutoras.id ";
		$q.= "AND ";
		$q.= "puser.relacion_ue_cp.id_escenario = puser.unidades_ejecutoras.id_escenario ";
		$q.= "WHERE ";
		$q.= "puser.relacion_ue_cp.id_escenario =  '$EscEnEje' ";
		$q.= "ORDER BY id_cp ASC";
		//die($q);
		$r= $conn->Execute($q);
		while(!$r->EOF){
			$pp = new partidas_presupuestarias;
				$pp->id = $r->fields['id_cp'];
				$pp->descripcion = $r->fields['categoria_programatica'];
				$pp->ue = $r->fields['unidad_ejecutora'];
				$coleccion[] = $pp;
			$r->movenext();
		}
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
	
	function getReporteCategoriaSector($conn, $categoria, $escenario)
	{
	try{
		$q = "SELECT CP.id AS id_cp ";
		$q.= "FROM puser.categorias_programaticas AS CP ";
		$q.=  "WHERE CP.id = substr('$categoria', 1, 2) || '00000000' AND id_escenario = $escenario ";
		$r= $conn->Execute($q);
		$coleccion  = array();
		if(!$r->EOF){
			$ocategoria = new categorias_programaticas;
			$ocategoria->get($conn, $r->fields['id_cp'], $escenario);
			$coleccion[] = $ocategoria;
			return $coleccion;
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
	
	function getReporteCategoriaActividad($conn, $categoria, $escenario)
	{
	try{
		$q = "SELECT CP.id AS id_cp ";
		$q.= "FROM puser.categorias_programaticas AS CP ";
		$q.=  "WHERE CP.id = substr('$categoria', 1, 4) || '000000' AND id_escenario = $escenario ";
		$r= $conn->Execute($q);
		$coleccion  = array();
		if(!$r->EOF){
			$ocategoria = new categorias_programaticas;
			$ocategoria->get($conn, $r->fields['id_cp'], $escenario);
			$coleccion[] = $ocategoria;
			return $coleccion;
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

	function getReporteCategoriaUnidadEjecutora($conn, $categoria, $escenario)
	{
	try{
		$q = "SELECT UE.* FROM puser.relacion_ue_cp  AS CPU ";
		$q.= "INNER JOIN puser.unidades_ejecutoras AS UE ";
		$q.= "ON CPU.id_unidad_ejecutora = UE.id  AND CPU.id_escenario = UE.id_escenario ";
		$q.= "WHERE CPU.id_categoria_programatica = '$categoria' AND CPU.id_escenario = '$escenario'"; 
		$r= $conn->Execute($q);
		$coleccion  = array();
		if(!$r->EOF){
			$ue= new unidades_ejecutoras;
			$ue->id = $r->fields['id'];
			$ue->id_escenario = $r->fields['id_escenario'];
			$ue->descripcion = $r->fields['descripcion'];
			$ue->responsable = $r->fields['responsable'];
			$coleccion[] = $ue;
			return $coleccion;
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
	
	//Esta funcion se utiliza para traer solo las categorias que sean sector, para el reporte de objetivos sectoriales
	function getObjetivosSectoriales($conn, $idEscenario){
		$sql = "SELECT id, descripcion, objetivo_sectorial FROM puser.categorias_programaticas ";
		$sql.= "WHERE substring(id FROM 3 FOR 6) = '000000' AND id_escenario = '$idEscenario' ";
		$sql.= "ORDER BY id";
		$row = $conn->Execute($sql);
		while(!$row->EOF){
			$cp = new categorias_programaticas;
			$cp->get($conn, $row->fields['id'], $idEscenario);
			$coleccion[] = $cp;
			$row->movenext();
		}
		return $coleccion;
	}
	
	function getPrograma($conn, $idEscenario){
		$sql = "SELECT a.id,a.descripcion, a.descripcion_programa, (SELECT descripcion FROM puser.categorias_programaticas WHERE id = substring(a.id FROM 1 FOR 2)::char(2)||'00000000' AND id_escenario = $idEscenario) AS sector ";
		$sql.= "FROM puser.categorias_programaticas a ";
		$sql.= "WHERE substring(a.id FROM 5 FOR 6) = '000000' AND a.id_escenario = $idEscenario AND substring(a.id FROM 3 FOR 2) <> '00' ";
		$sql.= "ORDER BY a.id";
		//die($sql);
		$row = $conn->Execute($sql);
		while(!$row->EOF){
			$cp = new categorias_programaticas;
			$cp->id = $row->fields['id'];
			$cp->descripcion = $row->fields['descripcion'];
			$cp->dp = $row->fields['descripcion_programa'];
			$cp->sector = $row->fields['sector'];
			$coleccion[] = $cp;
			$row->movenext();
		}
		return $coleccion;
	}
	
	function getSubPrograma($conn, $idEscenario){
		$sql = "SELECT a.id,a.descripcion, a.descripcion_programa, (SELECT descripcion FROM puser.categorias_programaticas WHERE id = substring(a.id FROM 1 FOR 2)::char(2)||'00000000') AS sector, (SELECT descripcion FROM puser.categorias_programaticas WHERE id = substring(a.id FROM 1 FOR 4)::char(2)||'000000') AS programa ";
		$sql.= "FROM puser.categorias_programaticas a ";
		$sql.= "WHERE substring(a.id FROM 7 FOR 4) = '0000' AND a.id_escenario = $idEscenario AND substring(a.id FROM 5 FOR 2) <> '00' ";
		$sql.= "ORDER BY a.id";
		//die($sql);
		$row = $conn->Execute($sql);
		while(!$row->EOF){
			$cp = new categorias_programaticas;
			$cp->id = $row->fields['id'];
			$cp->descripcion = $row->fields['descripcion'];
			$cp->dp = $row->fields['descripcion_programa'];
			$cp->sector = $row->fields['sector'];
			$cp->programa = $row->fields['programa'];
			$coleccion[] = $cp;
			$row->movenext();
		}
		return $coleccion;
	}
	
	function getProyecto($conn, $idEscenario){
		$sql = "SELECT a.id,a.descripcion, a.descripcion_programa, (SELECT descripcion FROM puser.categorias_programaticas WHERE id = substring(a.id FROM 1 FOR 2)::char(2)||'00000000' AND id_escenario = $idEscenario) AS sector, ";
		$sql.= "(SELECT descripcion FROM puser.categorias_programaticas WHERE id = substring(a.id FROM 1 FOR 4)::char(4)||'000000'  AND id_escenario = $idEscenario) AS programa, ue.descripcion AS ue ";
		$sql.= "FROM puser.categorias_programaticas a ";
		$sql.= "INNER JOIN puser.relacion_ue_cp rcu ON (a.id = rcu.id_categoria_programatica AND rcu.id_escenario = 1111) ";
		$sql.= "INNER JOIN puser.unidades_ejecutoras ue ON  (rcu.id_unidad_ejecutora = ue.id AND ue.id_escenario = 1111) ";
		$sql.= "WHERE substring(a.id FROM 9 FOR 2) <> '00' AND a.id_escenario = $idEscenario AND substring(a.id FROM 1 FOR 4) <> '0000' ORDER BY a.id ";
		//die($sql);
		$row = $conn->Execute($sql);
		while(!$row->EOF){
			$cp = new categorias_programaticas;
			$cp->id = $row->fields['id'];
			$cp->descripcion = $row->fields['descripcion'];
			$cp->dp = $row->fields['descripcion_programa'];
			$cp->sector = $row->fields['sector'];
			$cp->programa = $row->fields['programa'];
			$cp->ue = $row->fields['ue'];
			$coleccion[] = $cp;
			$row->movenext();
		}
		return $coleccion;
	}	
}
?>
