<?

class relacion_cc_pp{
	
	#PROPIEDADES#
	var $id_cuenta_contable;
	var $id_partida_presupuestaria;
	var $cuenta_contable;	// Objeto cuenta contable
	var $partida_presupuestaria;
	var $id;
	var $id_escenario;
		
	#FUNCIONES#
	
	function get($conn, $id, $escEnEje)
	{
		$q = "SELECT * FROM contabilidad.relacion_cc_pp WHERE id ='$id' AND id_escenario='$escEnEje' ";
		$r = $conn->execute($q);
		
		if (!$r->EOF)
		{
			$this->id				= 	$r->fields['id'];	
			$this->id_escenario	=	$r->fields['id_escenario'];				

			$pc = new plan_cuenta;
			$pc->get_by_id($conn, $r->fields['id_cuenta_contable']);
			$this->cuenta_contable 		=	$pc; 
			$this->id_cuenta_contable 	=	$r->fields['id_cuenta_contable'];

			$pp = new partidas_presupuestarias;
			$pp->get($conn, $r->fields['id_partida_presupuestaria'], $escEnEje);
			$this->partida_presupuestaria		= 	$pp;
			$this->id_partida_presupuestaria	=	$r->fields['id_partida_presupuestaria'];
			return true;	
		}
		else
			return false;
	}
	
	function get_all($conn,$orden="id"){
		
		$q = "SELECT * FROM contabilidad.relacion_cc_pp ";
		$q.= "ORDER BY $orden ";
		$r = $conn->Execute($q);
		while(!$r->EOF){
			$ue = new relacion_cc_pp;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}
	
	function add($conn,$id_cuenta_contable, $id_partida_presupuestaria, $id_escenario)
	{
		$q = "SELECT id FROM contabilidad.relacion_cc_pp ";
		$q.= "WHERE (id_cuenta_contable = $id_cuenta_contable AND id_escenario = $id_escenario) OR ";
		$q.= "(id_partida_presupuestaria = $id_partida_presupuestaria AND id_escenario = $id_escenario) ";
//		echo($q);
		$rs = $conn->Execute($q);
		
		$res = false;
		if ($rs->EOF)
		{
			$q = "INSERT INTO contabilidad.relacion_cc_pp (id_cuenta_contable, id_partida_presupuestaria, id_escenario) ";
			$q.= "VALUES ('$id_cuenta_contable', '$id_partida_presupuestaria', '$id_escenario') ";

			$rs = $conn->Execute($q);	
			if($rs !== false)
				$res = true;
		}
		else
			$res = 'Duplicado';		
			
		return $res;
	}
	
	function set($conn,$id, $id_cuenta_contable, $id_partida_presupuestaria, $id_escenario)
	{
		$q = "UPDATE contabilidad.relacion_cc_pp SET id_cuenta_contable = '$id_cuenta_contable', id_partida_presupuestaria = '$id_partida_presupuestaria', id_escenario='$id_escenario' ";
		$q.= "WHERE id=$id";	
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $id){
		try {
			$q = "DELETE FROM contabilidad.relacion_cc_pp WHERE id=$id";
			if($conn->Execute($q))
				return true;
			else
				return false;
		}
		
		catch( ADODB_Exception $e ){
			//die($conn->ErrorMsg());
				//$this->msj = $conn->ErrorMsg();
				return false;

		}
	}
	
	function buscar($conn, $id_cuenta_contable, $id_partida_presupuestaria,$id_escenario, $max=10, $from=1, $orden="id"){
		/*if(empty($id_cuenta_contable) && empty($id_partida_presupuestaria) && empty($id_escenario))
			return false;*/
		$q = "SELECT * FROM contabilidad.relacion_cc_pp ";
		$q.= "WHERE  1=1 ";
		$q.= !empty($id_cuenta_contable) ? "AND id_cuenta_contable = $id_cuenta_contable ":"";
		$q.= !empty($id_partida_presupuestaria) ? "AND id_partida_presupuestaria = '$id_partida_presupuestaria'  ":"";
		$q.= !empty($id_escenario) ? "AND id_escenario = '$id_escenario'  ":"";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new relacion_cc_pp;
			$ue->get($conn, $r->fields['id'], $r->fields['id_escenario']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		
	
		return $coleccion;
	}
	
	function total_registro_busqueda($conn, $id_cuenta_contable, $id_partida_presupuestaria, $id_escenario ,$max=10, $from=1, $orden="id"){
		/*if(empty($id_cuenta_contable) && empty($id_partida_presupuestaria) && empty($id_escenario))
			return false;*/
		$q = "SELECT * FROM contabilidad.relacion_cc_pp ";
		$q.= "WHERE  1=1 ";
		$q.= !empty($id_cuenta_contable) ? "AND id_cuenta_contable = $id_cuenta_contable ":"";
		$q.= !empty($id_partida_presupuestaria) ? "AND id_partida_presupuestaria = '$id_partida_presupuestaria'  ":"";
		$q.= !empty($id_escenario) ? "AND id_escenario = '$id_escenario'  ":"";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = $conn->Execute($q);
		$total = $r->RecordCount();
		return $total;
		
	}
	
			
}


?>