<?
class menu{

	// Propiedades

	var $id;
	var $descripcion;
	var $pagina;
	var $tipo;
	var $nivel;
	var $id_padre;

	var $total;

	function getAllModulosPorUsuario($conn, $id_usuario, $orden="modulos.orden"){
		$q = "SELECT operaciones.id_modulo AS id, modulos.descripcion ";
		$q.= "FROM relacion_us_op  ";
		$q.= "INNER JOIN operaciones ON (relacion_us_op.id_operacion = operaciones.id) ";
		$q.= "INNER JOIN usuarios ON (relacion_us_op.id_usuario = usuarios.id) ";
		$q.= "INNER JOIN modulos ON (operaciones.id_modulo = modulos.id) ";
		$q.= "WHERE usuarios.id = '$id_usuario' ";
		$q.= "GROUP BY operaciones.id_modulo, modulos.descripcion,$orden ";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = $conn->Execute($q);
		while(!$r->EOF){
			$ue = new menu;
			$ue->id = $r->fields['id'];
			$ue->descripcion = $r->fields['descripcion'];
			$coleccion[] = $ue;
			$r->movenext();
		}
		return $coleccion;
	}
	
	function get_all_op_by_user_sistema($conn, $id_usuario, $id_modulo, $orden="orden"){
		$q = "SELECT operaciones.* ";
		$q.= "FROM relacion_us_op  ";
		$q.= "INNER JOIN operaciones ON (relacion_us_op.id_operacion = operaciones.id) ";
		$q.= "INNER JOIN usuarios ON (relacion_us_op.id_usuario = usuarios.id) ";
		$q.= "WHERE usuarios.id = '$id_usuario' AND operaciones.id_modulo = '$id_modulo' ";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new menu;
			$ue->id = $r->fields['id'];
			$ue->descripcion = $r->fields['descripcion'];
			$ue->pagina = $r->fields['pagina'];
			$ue->nivel = $r->fields['nivel'];
			$ue->id_padre = $r->fields['id_padre'];
			$ue->tipo = $r->fields['tipo'];
			$coleccion[] = $ue;
			$r->movenext();
		}
		return $coleccion;
	}
	
	function getAllOpsPorUsuarioModuloNivel($conn, $id_usuario, $id_modulo, $nivel, $orden="orden"){
		$q = "SELECT operaciones.* ";
		$q.= "FROM relacion_us_op  ";
		$q.= "INNER JOIN operaciones ON (relacion_us_op.id_operacion = operaciones.id) ";
		$q.= "INNER JOIN usuarios ON (relacion_us_op.id_usuario = usuarios.id) ";
		$q.= "WHERE usuarios.id = '$id_usuario' AND operaciones.id_modulo = '$id_modulo' AND operaciones.nivel = '$nivel' ";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new menu;
			$ue->id = $r->fields['id'];
			$ue->descripcion = $r->fields['descripcion'];
			$ue->pagina = $r->fields['pagina'];
			$ue->nivel = $r->fields['nivel'];
			$ue->id_padre = $r->fields['id_padre'];
			$ue->tipo = $r->fields['tipo'];
			$coleccion[] = $ue;
			$r->movenext();
		}
		return $coleccion;
	}
	function getAllOpsHijas($conn, $id_usuario, $id_padre, $orden="orden"){
		$q = "SELECT operaciones.* ";
		$q.= "FROM relacion_us_op  ";
		$q.= "INNER JOIN operaciones ON (relacion_us_op.id_operacion = operaciones.id) ";
		$q.= "INNER JOIN usuarios ON (relacion_us_op.id_usuario = usuarios.id) ";
		$q.= "WHERE usuarios.id = '$id_usuario' AND operaciones.id_padre = '$id_padre' ";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new menu;
			$ue->id = $r->fields['id'];
			$ue->descripcion = $r->fields['descripcion'];
			$ue->pagina = $r->fields['pagina'];
			$ue->nivel = $r->fields['nivel'];
			$ue->id_padre = $r->fields['id_padre'];
			$ue->tipo = $r->fields['tipo'];
			$coleccion[] = $ue;
			$r->movenext();
		}
		return $coleccion;
	}

	// contruye las subcarpetas del menu en JS a partir de una nodo padre
	function getSubCarpetas($conn, $idUsuario, $idNodoPadre, $nombreNodoPadre){
		$nodos = menu::getAllOpsHijas($conn, $idUsuario, $idNodoPadre);
		if(is_array($nodos)){
			for($k=0; $k<count($nodos);$k++){
				if($nodos[$k]->tipo != 'C'){
					$js.= "\t\t\t\t\tmyobj = { label: \"".$nodos[$k]->descripcion."\", href: \"".$nodos[$k]->pagina."\", target: \"contenido\"};\n";
					$js.= "\t\t\t\t\tvar ".$nombreNodoPadre."_".$k." = new YAHOO.widget.TextNode(myobj, ".$nombreNodoPadre.", false);\n";
				}else{
					$js.= "\t\t\t\t\tmyobj = { label: \"".$nodos[$k]->descripcion."\"};\n";
					$js.= "\t\t\t\t\tvar ".$nombreNodoPadre."_".$k." = new YAHOO.widget.TextNode(myobj, ".$nombreNodoPadre.", false);\n";
					$js.= menu::getSubCarpetas($conn, $idUsuario, $nodos[$k]->id, $nombreNodoPadre."_".$k);
				}
			}
			return $js;
		}
	}

}
?>