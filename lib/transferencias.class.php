<?
class transferencias{

	// Propiedades

	var $id;
	var $privPublic;
	var $esEnte;
	var $tipoEnte; 
	var $organismo;
	var $idEnte;
	var $asignacion;
	var $responsable;
	var $observaciones;
	var $id_escenario;
	var $id_categoria;
	var $id_partida;
	var $msj;

	var $total;

	function get($conn, $id){
		$q = "SELECT * FROM puser.transferencias WHERE id='$id' ";//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->privPublic = $r->fields['privpublic'];
			$this->esEnte = $r->fields['es_ente'];
			$this->tipoEnte = $r->fields['tipo_ente'];
			$this->idEnte = $r->fields['id_ente'];
			$this->organismo = $r->fields['organismo'];
			$this->asignacion = $r->fields['asignacion'];
			$this->responsable = $r->fields['responsable'];
			$this->observaciones = $r->fields['observaciones'];
			$this->id_escenario = $r->fields['id_escenario'];
			$this->id_categoria = $r->fields['id_categoria_programatica'];
			$this->id_partida = $r->fields['id_partida_presupuestaria'];
			
			return true;
		}else
			return false;
	}

	function get_all($conn, $from=0, $max=0,$orden="id"){
		$q = "SELECT * FROM transferencias ";
		$q.= "ORDER BY $orden ";
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		
		while(!$r->EOF){
			$ue = new transferencias;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}
	
	function add($conn,$privPublic, $esEnte='', $tipoEnte='', $idEnte, $organismo, $asignacion, $responsable='',$observaciones='', $idEscenario, $idCategoria, $idPartida){
		$q = "INSERT INTO puser.transferencias (privpublic, es_ente, tipo_ente, id_ente, organismo, asignacion, responsable, observaciones, id_escenario, id_categoria_programatica, id_partida_presupuestaria) ";
		$q.= "VALUES ('$privPublic', '$esEnte', '$tipoEnte', $idEnte, '$organismo', $asignacion, '$responsable', '$observaciones', $idEscenario, '$idCategoria', '$idPartida' )"; 
		//die($q);
		$r = $conn->Execute($q);
		if($r){
			$this->msj =  REG_ADD_OK;
			return true;
		} else {
			$this->msj =  ERROR;
			return ERROR;
		}
	}

	function set($conn, $id,$privPublic, $esEnte='', $tipoEnte='', $idEnte, $organismo, $asignacion, $responsable='',$observaciones='',$idEscenario, $idCategoria, $idPartida){
		$q = "UPDATE transferencias SET privpublic = '$privPublic', es_ente = '$esEnte', tipo_ente = '$tipoEnte', id_ente = $idEnte, ";
		$q.= "organismo = '$organismo', asignacion = $asignacion, responsable = '$responsable', ";
		$q.= "observaciones = '$observaciones', id_categoria_programatica = '$idCategoria', id_partida_presupuestaria = '$idPartida', id_escenario = $idEscenario";
		$q.= "WHERE id='$id' ";
		//die($q);
		if($conn->Execute($q)){
			$this->msj = REG_SET_OK; 
			return true;
		}else{
			$this->msj = ERROR;
			return false;
		}
	}

	function del($conn, $id){
		$q = "DELETE FROM puser.transferencias WHERE id='$id'";
		//die($q);
		if($conn->Execute($q)){
			$this->msj = REG_DEL_OK;
			return true;
		} else {
			$this->msj = ERROR;
			return false;
		}
	}

	function buscar($conn, $privPublic, $organismo, $orden="id",$from,$max){
		if(empty($privPublic) && empty($organismo))
			return false;
		$q = "SELECT * FROM puser.transferencias ";
		$q.= "WHERE  1=1 ";
		$q.= !empty($privPublic) ? "AND privpublic = $privPublic ":" ";
		$q.= !empty($organismo) ? "AND organismo ILIKE '%$organismo%'  ":" ";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new transferencias;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		return $coleccion;
	}
	
	function totalRegistroBusqueda($conn,$privPublic,$organismo,$orden="id"){
		if(empty($privPublic) && empty($organismo))
			return false;
		$q = "SELECT * FROM puser.transferencias ";
		$q.= "WHERE  1=1 ";
		$q.= !empty($privPublic) ? "AND privpublic = $privPublic  ":"";
		$q.= !empty($organismo) ? "AND organismo ILIKE '%$organismo%'  ":"";
		$q.= "ORDER BY $orden ";
		//die($q);
		if($r = $conn->Execute($q))
			return $r->RecordCount();
	}
	
	
}
?>
