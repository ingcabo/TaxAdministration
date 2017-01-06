<?

class iva{

# PROPIEDADES #

	var $id;
	var $cod_partida;
	var $porc_iva;
	var $anio;
//	var $nombre_partida;

# METODOS #

	function get($conn, $id){
		$q = "SELECT * FROM finanzas.iva ";
		$q.= "WHERE id='$id'";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->cod_partida = $r->fields['cod_partida'];
			$pp = new partidas_presupuestarias;
			$esc = escenarios::buscar($conn, '', '', $r->fields['anio'], '');
			
			$pp->get($conn, $r->fields['cod_partida'], $esc[0]->id);
			$this->nombre_partida = $pp;
			$this->porc_iva = $r->fields['porc_iva'];
			$this->anio = $r->fields['anio'];
																		
			return true;
		}else
			return false;
	}
	
	function get_all($conn, $from=0, $max=0,$orden="id"){
		$q = "SELECT * FROM finanzas.iva ";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new iva;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}
	
	function add($conn, $cod_partida, $porc_iva, $anio){
		$q = "INSERT INTO finanzas.iva ";
		$q.= "(cod_partida, porc_iva, anio) ";
		$q.= "VALUES ";
		$q.= "('$cod_partida', '$porc_iva','$anio') ";

		//die($q);
		
		if($conn->Execute($q)){
		
			return true;
		}
		else{
			return false;
		}	
		
	}
	
	function set($conn, $id, $cod_partida, $porc_iva, $anio){
		$q = "UPDATE finanzas.iva SET cod_partida='$cod_partida', porc_iva='$porc_iva', anio='$anio' ";
		$q.= "WHERE id=$id";	
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	function del($conn, $id){
		$q = "DELETE FROM finanzas.iva WHERE id='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	function buscar($conn, $anio='', $cod_partida='', $orden="id"){
		$q = "SELECT * FROM finanzas.iva ";
		$q.= "WHERE  1=1 ";
		$q.= !empty($anio) ? " AND anio = $anio ":"";
		$q.= !empty($cod_partida) ? "AND cod_partida='$cod_partida' ": "";
		$q.= "ORDER BY $orden ";
		
//		return $q;
		//die($q);
		if(!$r = $conn->Execute($q))
			return false;

		$collection=array();
		while(!$r->EOF){
			$ue = new iva;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		return $coleccion;
	}
	
	function iva_anio($conn, $anoCurso){
		$iva = "SELECT porc_iva FROM finanzas.iva WHERE anio = '$anoCurso'";
		$r = $conn->Execute($iva);
		while(!$r->EOF){
			$imp = new iva;
			$imp->porc = guardaFloat($r->fields['porc_iva']);
			$imp->descripcion = $r->fields['porc_iva'].'%';
			$coleccion[] = $imp;
			$r->movenext();	
		}
		return $coleccion;
	}
	
	function get_periodos($conn,$dia,$mes,$ano){
		$ano = date('Y');
		$mes = date('m');
		$dia = date('d');
		if($dia>= 1 && $dia<=15)
			$mesAux = $mes-1;
		else
			$mesAux = $mes;
		$periodo = array();	
			
		for($i=1;$i<=$mesAux;$i++){
			$ue = new iva;
			$ultimo_dia=28;
			while (checkdate($i,$ultimo_dia + 1,$ano)){
			   $ultimo_dia++;
			}
			$strMes = sprintf("%02d", $i);
			$ue->id = $ano.'-'.$strMes.'-01 '.$ano.'-'.$strMes.'-15';
			$ue->descripcion = 'Del 01/'.$strMes.'/'.$ano.' al 15/'.$strMes.'/'.$ano;
			$periodo[] = $ue; 
			//print_r($periodo);
			if($i<$mes){
				$ue = new iva;
				$ue->id = $ano.'-'.$strMes.'-16 '.$ano.'-'.$strMes.'-'.$ultimo_dia; 
				$ue->descripcion = 'Del 16/'.$strMes.'/'.$ano.' al '.$ultimo_dia.'/'.$strMes.'/'.$ano;
				$periodo[] = $ue;
				//echo "<br>";
				//print_r($periodo);
			}
			//die(); 
		}
		
		return $periodo;
	}
	
	function get_meses($conn){
		
		$mes = date('m');
		$periodo = array();	
			
		for($i=1;$i<=$mes;$i++){
			$ue = new iva;
			$strMes = sprintf("%02d", $i);
			switch($strMes){
				case '01':
				 	$nomMes = 'Enero';
				break;
				case '02':
					$nomMes = 'Febrero';
				break;
				case '03':
					$nomMes = 'Marzo';
				break;	
				case '04':
					$nomMes = 'Abril';
				break;	
				case '05':
					$nomMes = 'Mayo';
				break;
				case '06':
					$nomMes = 'Junio';
				break;
				case '07':
					$nomMes = 'Julio';
				break;
				case '08':
					$nomMes = 'Agosto';
				break;
				case '09':
					$nomMes = 'Septiembre';
				break;
				case '10':
					$nomMes = 'Octubre';
				break;
				case '11':
					$nomMes = 'Noviembre';
				break;
				case '12':
					$nomMes = 'Diciembre';
				break;
			}
			$ue->id = $strMes;
			$ue->descripcion = $nomMes;
			$periodo[] = $ue; 
			
		}
		
		return $periodo;
	}
}

?>
