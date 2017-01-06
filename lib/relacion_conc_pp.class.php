<?
class relacion_conc_pp{

	// Propiedades
	var $id;
	var $id_escenario;
	var $id_categoria_programatica;
	var $id_partida_presupuestaria;
	var $id_concepto;
	var $escenario;
	var $categoria_programatica;
	var $partida_presupuestaria;
	var $concepto;
	var $total;

	function get($conn, $id, $escenario = ''){
		try{
			$q = "SELECT A.* ";
			$q.= "FROM rrhh.conc_part AS A ";
			$q.= "WHERE A.int_cod='$id' AND  A.escenario = $escenario ";
			$r = $conn->Execute($q);
			if($r){
				$this->int_cod = $r->fields['int_cod'];
				$this->id_escenario = $r->fields['escenario'];
				$this->id_partida_presupuestaria = $r->fields['par_cod'];
				$this->id_categoria_programatica = $r->fields['cat_cod'];
				$this->id_concepto = $r->fields['conc_cod'];
				$oconcepto = new concepto;
				$oconcepto->get($conn,$this->id_concepto);
				$this->concepto = $oconcepto->conc_nom;
				$this->json = $this->get_all_by_concepto_esc($conn, $this->id_concepto, $this->id_escenario);
				//var_dump($this->json);
			}
			return true;
		}
		catch( ADODB_Exception $e ){
			if($e->getCode()==-1)
				return ERROR_CATCH_VFK;
			elseif($e->getCode()==-5)
				return ERROR_CATCH_RELVUK;
			else
				return ERROR_CATCH_GENERICO;
		}
	}
/*
	function get_all($conn, $from=0, $max=0, $orden = "escenario, cat_cod, par_cod"){
		try{
			$q = "SELECT * FROM relacion_pp_cp  ";
			$q.= "ORDER BY $orden ";
			$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
			$collection=array();
			while(!$r->EOF){
				$ue = new relacion_pp_cp;
				$ue->get($conn, $r->fields['id']);
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
				return ERROR_CATCH_RELVUK;
			else
				return ERROR_CATCH_GENERICO;
		}
	}*/
	function __destruct(){
    }
	function add($conn, 
				$id_escenario,
				$presupuesto,
				$id_concepto){
			try{
				$JsonRec = new Services_JSON();
				$JsonRec=$JsonRec->decode(str_replace("\\","",$presupuesto));
				if(is_array($JsonRec->Presupuesto)){
					$q = "DELETE FROM rrhh.conc_part WHERE conc_cod=$id_concepto AND escenario = '$id_escenario'";
					$conn->Execute($q);
					foreach($JsonRec->Presupuesto as $PresupuestoAux){
						$q = "INSERT INTO rrhh.conc_part ";
						$q.= "(cat_cod, par_cod, conc_cod,escenario) ";
						$q.= "VALUES ";
						$q.= "('$PresupuestoAux[0]', '$PresupuestoAux[1]', '$id_concepto','$id_escenario') ";
						//die($q);
						$conn->Execute($q);
					}
					return REG_ADD_OK;
				}
			}
			catch( ADODB_Exception $e ){
				if($e->getCode()==-1)
					return ERROR_CATCH_VFK;
				elseif($e->getCode()==-5)
					return ERROR_CATCH_RELVUK;
				else
					return ERROR_CATCH_GENERICO;
			}
	}

	function set($conn, 
				$id,
				$id_escenario,
				$id_categoria_programatica,
				$id_partida_presupuestaria,
				$id_concepto){
				try{
					$q = "UPDATE rrhh.conc_part SET escenario='$id_escenario', ";
					$q.= "cat_cod = '$id_categoria_programatica', par_cod = '$id_partida_presupuestaria', ";
					$q.= "conc_cod = '$id_concepto'";
					$q.= "WHERE int_cod='$id' ";
					//die($q);
					$conn->Execute($q);
					return REG_SET_OK;
				}
				catch( ADODB_Exception $e ){
					if($e->getCode()==-1)
						return ERROR_CATCH_VFK;
					elseif($e->getCode()==-5)
						return ERROR_CATCH_RELVUK;
					else
						return ERROR_CATCH_GENERICO;
				}
	}

	function del($conn, $id){
		try{
			$q = "DELETE FROM rrhh.conc_part WHERE int_cod='$id' ";
			$conn->Execute($q);
			return REG_DEL_OK;
		}
		catch( ADODB_Exception $e ){
			if($e->getCode()==-1)
				return ERROR_CATCH_VFK;
			elseif($e->getCode()==-5)
				return ERROR_CATCH_RELVUK;
			else
				return ERROR_CATCH_GENERICO;
		}
	}

	function del_escenario($conn, $escenario){
		try{
			$q = "DELETE FROM rrhh.conc_part WHERE escenario='$escenario'";
			//die($q);
			$conn->Execute($q);
			return true;
		}
		catch( ADODB_Exception $e ){
			if($e->getCode()==-1)
				return ERROR_CATCH_VFK;
			elseif($e->getCode()==-5)
				return ERROR_CATCH_RELVUK;
			else
				return ERROR_CATCH_GENERICO;
		}
	}

	function get_all_by_esc($conn, $id_escenario, $from=0, $max=0,$orden="id"){
		try{
			$q = "SELECT * FROM rrhh.conc_part WHERE escenario = '$id_escenario' ";
			$q.= "ORDER BY $orden ";
			$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
			$collection=array();
			while(!$r->EOF){
				$ue = new relacion_pp_cp;
				$ue->get($conn, $r->fields['id']);
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
				return ERROR_CATCH_RELVUK;
			else
				return ERROR_CATCH_GENERICO;
		}
	}
	
	function get_all_by_concepto_esc($conn, $id_concepto, $escenario){
		$q="SELECT A.cat_cod, A.par_cod FROM rrhh.conc_part AS A  WHERE A.conc_cod='$id_concepto' AND A.escenario = $escenario";
		$r= $conn->execute($q);
		
		while(!$r->EOF){
		
			$conc_pp = new relacion_conc_pp;
			$conc_pp->id_categoria_programatica = $r->fields['cat_cod'];
			$conc_pp->id_partida_presupuestaria = $r->fields['par_cod'];
			$coleccion[] = $conc_pp;
			$r->movenext();
		
		}
		
		$json = new Services_JSON();
		return $json->encode($coleccion);
	}


	function buscar($conn, $id_escenario, $id_concepto, $max=10, $from=1, $orden="conc_cod, escenario, int_cod"){
		if(empty($id_escenario) && empty($id_concepto))
			return false;
		$q = "SELECT DISTINCT ON (conc_cod) conc_cod, * FROM rrhh.conc_part ";
		$q.= "WHERE  1=1 ";
		$q.= !empty($id_escenario) ? "AND escenario = '$id_escenario'  ":"";
		$q.= !empty($id_concepto) ? "AND conc_cod = '$id_concepto'":"";
		$q.= "ORDER BY conc_cod, cat_cod, par_cod ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new relacion_conc_pp;
			$ue->get($conn, $r->fields['int_cod'], $r->fields['escenario']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		
	
		return $coleccion;
	}
	
	function total_registro_busqueda($conn, $id_escenario, $id_concepto, $orden="conc_cod, escenario, int_cod"){
		if(empty($id_escenario) && empty($id_concepto))
			return false;
		$q = "SELECT DISTINCT ON (conc_cod) conc_cod FROM rrhh.conc_part ";
		$q.= "WHERE  1=1 ";
		$q.= !empty($id_escenario) ? "AND escenario = '$id_escenario'  ":"";
		$q.= !empty($id_concepto) ? "AND conc_cod = '$id_concepto'  ":"";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		$total = $r->RecordCount();
		return $total;
	}
	
	function busca_cp($conn, $id_ue, $escenario, $fecha_desde='', $fecha_hasta=''){
		$qcp = "SELECT * FROM relacion_ue_cp ";
		$qcp.= "WHERE id_unidad_ejecutora='$id_ue' AND id_escenario='$escenario' ";
//		$qcp.= !empty($fecha_desde) ? "AND  = '$id_pp' " : "";
		//die($qcp);
		$rcp = $conn->execute($qcp) or die($qcp);
		while(!$rcp->EOF){
			$cpp = new categorias_programaticas;
			$cpp->get($conn, $rcp->fields['id_categoria_programatica'], $escenario);
			$coleccion[] = $cpp;
			
			$rcp->movenext();
			$i++;
		}
		
		return $coleccion;
		
	
	}
	
	function get_cp($conn, $id, $id_escenario)
	{
		$q = "SELECT DISTINCT
				puser.relacion_ue_cp.id_categoria_programatica
			FROM
				puser.relacion_ue_cp
			INNER JOIN 
				puser.categorias_programaticas 
			ON 
				puser.relacion_ue_cp.id_categoria_programatica = puser.categorias_programaticas.id
			WHERE
				puser.relacion_ue_cp.id_unidad_ejecutora =  '$id'
			AND
				puser.relacion_ue_cp.id_escenario =  '$id_escenario'";

		//die($q);
		$r = $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new formulacion;
			$ue->id = $r->fields['id_categoria_programatica'];
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}
	
	function get_pp($conn, $id, $id_escenario, $pp)
	{
		$q = "SELECT DISTINCT"; 
		$q.= "	puser.partidas_presupuestarias.id AS id_partida_presupuestaria, 
				puser.partidas_presupuestarias.descripcion AS partida_presupuestaria ";
		$q.="FROM ";
		$q.="	puser.relacion_pp_cp ";
		$q.="INNER JOIN ";
		$q.="	puser.categorias_programaticas ";
		$q.="ON "; 
		$q.="	(puser.categorias_programaticas.id = puser.relacion_pp_cp.id_categoria_programatica) ";
		$q.="INNER JOIN ";
		$q.="	puser.partidas_presupuestarias ";
		$q.="ON "; 
		$q.="	(puser.partidas_presupuestarias.id = puser.relacion_pp_cp.id_partida_presupuestaria) ";
		$q.="WHERE ";
		$q.="	substr(puser.relacion_pp_cp.id_partida_presupuestaria, 1, 3) = '$pp' ";
		$q.="AND "; 
		$q.="	puser.categorias_programaticas.id_escenario = '$id_escenario' ";
		$q.="AND ";
		$q.="	puser.partidas_presupuestarias.id_escenario = '$id_escenario' ";
		$q.="ORDER BY ";
		$q.="	id_partida_presupuestaria, partida_presupuestaria";
		//die($q);
		$r = $conn->Execute($q);
		$collection=array();
		while(!$r->EOF)
		{
			$ue = new formulacion;
			$ue->id = $r->fields['id_partida_presupuestaria'];
			$ue->descripcion = $r->fields['partida_presupuestaria'];
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
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
}
?>
