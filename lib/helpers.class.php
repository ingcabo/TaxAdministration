<?
class helpers{
	// obsoleto, usar this->superCombo
	function combo($conn, 
								$tabla, 
								$id_selected='', 
								$style='', 
								$order='id', 
								$nombre='', 
								$id='', 
								$atributo='',
								$sql=''){
	$q = empty($sql) ? "SELECT * FROM $tabla ORDER BY $order" : $sql;
	$r = $conn->Execute($q);
	$nombre = empty($nombre)? $tabla : $nombre;
	$id = empty($id) ? $tabla : $id;
	$combo = "<select  name=\"$nombre\" id=\"$id\"";
	$combo.= (!empty($atributo))? " \"$atributo\"" : "";
	$combo.= (!empty($style))? " style=\"$style\"" : "";
	//die($combo);
	$combo.= ">\n";
	$combo.="<option value=\"0\">Seleccione</option>\n";
	while(!$r->EOF){
		$id = $r->fields['id'];
		$descripcion = $r->fields['descripcion'];
		if($id == $id_selected)
			$combo.="<option value=\"$id\" selected=\"selected\">$descripcion</option>\n";
		else
			$combo.="<option value=\"$id\">$descripcion</option>\n";
		$r->movenext();
	}
	$combo.="</select>\n";
	return $combo;
	}
	
	// obsoleto, usar this->superCombo
	function combo_us($conn, $tabla, $style='', $order='id', $nombre='', $id='', $onchange){
	$q = "SELECT * FROM $tabla ORDER BY $order";
	$r = $conn->Execute($q);
	$nombre = (empty($nombre)? $tabla : $nombre);
	$id = (empty($id)? $tabla : $id);
	$combo = "<select onchange=\"$onchange\" name=\"$nombre\" id=\"$id\"";
	$combo.= (!empty($style))? "class=\"$style\"" : "";
	$combo.= ">\n";
	$combo.="<option value=\"0\">Seleccione</option>\n";
	while(!$r->EOF){
		$id = $r->fields['id'];
		$descripcion = $r->fields['nombre'] ." ".$r->fields['apellido'];
		if($id == $id_selected)
			$combo.="<option value=\"$id\" selected=\"selected\">$descripcion</option>\n";
		else
			$combo.="<option value=\"$id\">$descripcion</option>\n";
		$r->movenext();
	}
	$combo.="</select>\n";
	return $combo;
	}

	// obsoleto, usar this->superCombo
	function combo_pp_cp($conn, 
								$tabla, 
								$id_selected='', 
								$style='', 
								$order='id', 
								$nombre='', 
								$id='', 
								$onchange='',
								$sql=''){
		$q = empty($sql) ? "SELECT * FROM $tabla ORDER BY $order" : $sql;
		$r = $conn->Execute($q);
		$nombre = empty($nombre)? $tabla : $nombre;
		$id = empty($id) ? $tabla : $id;
		$combo = "<select onchange=\"$onchange\" name=\"$nombre\" id=\"$id\"";
		$combo.= (!empty($atributo))? " \"$atributo\"" : "";
		$combo.= (!empty($style))? " class=\"$style\"" : "";
		//die($combo);
		$combo.= ">\n";
		$combo.="<option value=\"0\">Seleccione</option>\n";
		while(!$r->EOF){
			$id = $r->fields['id'];
			$descripcion = $r->fields['descripcion'];
			if($id == $id_selected)
				$combo.="<option value=\"$id\" selected=\"selected\">$descripcion</option>\n";
			else
				$combo.="<option value=\"$id\">$descripcion</option>\n";
			$r->movenext();
		}
		$combo.="</select>\n";
		return $combo;
	}
	
	// obsoleto, usar this->superCombo
	//fecs 31/07/06 :: Select Tipo de Publicidad
	function combo_publicidad($conn, 
								$tabla, 
								$id_selected='', 
								$style='', 
								$order='id', 
								$nombre='', 
								$id='', 
								$onchange, 
								$sql='')
	{
			$q = empty($sql) ? "SELECT * FROM $tabla ORDER BY $order": $sql;// die($q);
			$r = $conn->Execute($q);
			$nombre = (empty($nombre)? $tabla : $nombre);
			$id = (empty($id)? $tabla : $id);
			$combo = "<select onchange=\"$onchange\" name=\"$nombre\" id=\"$id\"";
			$combo.= (!empty($style))? "class=\"$style\"" : "";
			$combo.= ">\n";
			$combo.="<option value=\"0\">Seleccione . . .</option>\n"; 
			while(!$r->EOF)
			{
				$id = $r->fields['id'];
				$descripcion = $r->fields['descripcion'];
				if($id == $id_selected)
					$combo.="<option value=\"$id\" selected=\"selected\">$descripcion</option>\n";
				else
					$combo.="<option value=\"$id\">$descripcion</option>\n";
				$r->movenext();
			}
			$combo.="</select>\n";
			return $combo;
	}

	// obsoleto, usar this->superCombo
	function combo_ue_cp($conn, 
								$tabla, 
								$id_selected='', 
								$style='', 
								$orden='id', 
								$nombre='', 
								$id='', 
								$onchange='',
								$sql='',
								$disabled='',
								$class='',
								$caracteresDesc=''){
		$orden = !empty($orden) ? "ORDER BY $orden" : "";
		$q = empty($sql) ? "SELECT * FROM $tabla $orden " : $sql;//die($q);
		$nombre = empty($nombre)? $tabla : $nombre;
		$id = empty($id) ? $tabla : $id;
		$combo = "<select $disabled class=\"$class\" onchange=\"$onchange\" name=\"$nombre\" id=\"$id\"";
		$combo.= (!empty($atributo))? " \"$atributo\"" : "";
		$combo.= (!empty($style))? " style=\"$style\"" : "";
		$combo.= ">\n";
		$combo.="<option value=\"0\">Seleccione...</option>\n";
		$r = $conn->Execute($q);
		if(!$r){
			$combo.="</select>\n";
			return $combo;
		}
		while(!$r->EOF){
			$id = $r->fields['id'];
			$descripcion = $r->fields['descripcion'];
			if(!empty($caracteresDesc) && (strlen($descripcion) > $caracteresDesc))
				$descripcion = substr($descripcion, 0, $caracteresDesc)."(...)"; 
			if($id == $id_selected)
				$combo.="<option value=\"$id\" selected=\"selected\">$descripcion</option>\n";
			else
				$combo.="<option value=\"$id\">$descripcion</option>\n";
			$r->movenext();
		}
		$combo.="</select>\n";
		return $combo;
	}

	// obsoleto, usar this->superCombo
	function combo_por_objeto($colObjeto, $id_seleccionado='', $nombre='', $id='', $style='', $onchange=''){
		$combo = "<select  onchange=\"$onchange\" name=\"$nombre\" id=\"$id\"";
		$combo.= (!empty($style))? " style=\"$style\"" : "";
		//die($combo);
		$combo.= ">\n";
		$combo.="<option value=\"0\">Seleccione</option>\n";
		foreach($colObjeto as $objeto){
			$id = $objeto->id;
			$descripcion = $objeto->descripcion;
			if($id == $id_seleccionado)
				$combo.="<option value=\"$id\" selected=\"selected\">$descripcion</option>\n";
			else
				$combo.="<option value=\"$id\">$descripcion</option>\n";
		}
		$combo.="</select>\n";
		return $combo;
	}
	
	// obsoleto, usar this->superCombo
	function xmlCombo($colObjeto, 
									$id_seleccionado='', 
									$nombre='', 
									$id='', 
									$style='', 
									$onchange='',
									$descValor = 'id',
									$descDescripcion = 'descripcion',
									$union=false,
									$tamanoDesc=''){
		$xml = new DomDocument();
		$nodoSelect = $xml->createElement('select');
		if(!empty($nombre))
			$nodoSelect->setAttribute('name', $nombre);
		if(!empty($id))
			$nodoSelect->setAttribute('id', $id);
		if(!empty($style))
			$nodoSelect->setAttribute('style', $style);
		if(!empty($onchange))
			$nodoSelect->setAttribute('onchange', $onchange);
		$nodoOption = $xml->createElement('option');
		$nodoOption->setAttribute('value', 0);
		$nodoOption->appendChild($xml->createTextNode('Seleccione'));
		$nodoSelect->appendChild($nodoOption);
		if(is_array($colObjeto)){
			foreach($colObjeto as $objeto){
				$nodoOption = $xml->createElement('option');
				$nodoOption->setAttribute('value', $objeto->$descValor);
				if($union)
					$descripcion = $objeto->$descValor ." - ". $objeto->$descDescripcion;
				else
					$descripcion = $objeto->$descDescripcion;
				if(!empty($tamanoDesc) && (strlen($descripcion) > $tamanoDesc))
					$descripcion = substr($descripcion, 0, $tamanoDesc)."(...)";
				$nodoTextoDescripcion = $xml->createTextNode($descripcion);
				$nodoOption->appendChild($nodoTextoDescripcion);
				if($objeto->$descValor == $id_seleccionado)
					$nodoOption->setAttribute('selected', 'selected');
				$nodoSelect->appendChild($nodoOption);
			}
		}
		return $nodoSelect;
	}
	
	// obsoleto, usar this->superCombo
	function superComboSQL($conn, 
									$tabla,
									$id_seleccionado='', 
									$nombre='', 
									$id='', 
									$style='', 
									$onchange='',
									$descValor = 'id',
									$descDescripcion = 'descripcion',
									$union=false,
									$orden='',
									$query='',
									$tamanoDesc='',
									$attr=''){
		$nombre = empty($nombre)? $tabla : $nombre;
		$id = empty($id) ? $tabla : $id;
		$xml = new DomDocument();
		$nodoSelect = $xml->createElement('select');
		$nodoSelect->setAttribute('name', $nombre);
		$nodoSelect->setAttribute('id', $id);
		if(!empty($style))
			$nodoSelect->setAttribute('style', $style);
		if(!empty($onchange))
			$nodoSelect->setAttribute('onchange', $onchange);
		if(!empty($attr))
			switch($attr){
				case "disabled":
					$nodoSelect->setAttribute('disabled', 'disabled');
					break;
				default:
					break;
			}
		$nodoOption = $xml->createElement('option');
		$nodoOption->setAttribute('value', 0);
		$nodoOption->appendChild($xml->createTextNode('Seleccione'));
		$nodoSelect->appendChild($nodoOption);
		$orden = !empty($orden) ? "ORDER BY $orden" : "";
		$q = empty($query) ? "SELECT * FROM $tabla $orden " : $query;
		//echo $q;
		if(!$r = $conn->Execute($q))
			return false;
		while(!$r->EOF){
			$nodoOption = $xml->createElement('option');
			$nodoOption->setAttribute('value', $r->fields[$descValor]);
			if($union)
				$descripcion = $r->fields[$descValor] ." - ". $r->fields[$descDescripcion];
			else
				$descripcion = $r->fields[$descDescripcion];
			if(!empty($tamanoDesc) && (strlen($descripcion) > $tamanoDesc))
				$descripcion = substr($descripcion, 0, $tamanoDesc)."(...)"; 
			$nodoTextoDescripcion = $xml->createTextNode($descripcion);
			$nodoOption->appendChild($nodoTextoDescripcion);
			if($r->fields[$descValor] == $id_seleccionado)
				$nodoOption->setAttribute('selected', 'selected');
			$nodoSelect->appendChild($nodoOption);
			$r->movenext();
		}
		return $xml->saveXML($nodoSelect);
	}

	// obsoleto, usar this->superCombo
	function superComboObj($colObjeto, 
									$id_seleccionado='', 
									$nombre='', 
									$id='', 
									$style='', 
									$onchange='',
									$descValor = 'id',
									$descDescripcion = 'descripcion',
									$union=false,
									$tamanoDesc='',
									$attr=''){
		$xml = new DomDocument();
		$nodoSelect = $xml->createElement('select');
		if(!empty($nombre))
			$nodoSelect->setAttribute('name', $nombre);
		if(!empty($id))
			$nodoSelect->setAttribute('id', $id);
		if(!empty($style))
			$nodoSelect->setAttribute('style', $style);
		if(!empty($onchange))
			$nodoSelect->setAttribute('onchange', $onchange);
		if(!empty($attr))
			switch($attr){
				case "disabled":
					$nodoSelect->setAttribute('disabled', 'disabled');
					break;
				default:
					break;
			}
		$nodoOption = $xml->createElement('option');
		$nodoOption->setAttribute('value', 0);
		$nodoOption->appendChild($xml->createTextNode('Seleccione...'));
		$nodoSelect->appendChild($nodoOption);
		if(is_array($colObjeto)){
			foreach($colObjeto as $objeto){
				$nodoOption = $xml->createElement('option');
				$nodoOption->setAttribute('value', $objeto->$descValor);
				if($union)
					$descripcion = $objeto->$descValor ." - ". $objeto->$descDescripcion;
				else
					$descripcion = $objeto->$descDescripcion;
				if(!empty($tamanoDesc) && (strlen($descripcion) > $tamanoDesc))
					$descripcion = substr($descripcion, 0, $tamanoDesc)."(...)";
				$nodoTextoDescripcion = $xml->createTextNode($descripcion);
				$nodoOption->appendChild($nodoTextoDescripcion);
				if($objeto->$descValor == $id_seleccionado)
					$nodoOption->setAttribute('selected', 'selected');
				$nodoSelect->appendChild($nodoOption);
			}
		}
		return $xml->saveXML($nodoSelect);
	}
	
	function combogrid($obj, $posicion, $id = 'id', $descripcion='descripcion', $textoSeleccion='Seleccionar..', $nombregrid='mygrid'){
		//die($descripcion);
		//die(print_r($obj));
			echo "$nombregrid.getCombo($posicion).put('0','".$textoSeleccion."');";
			if(is_array($obj)){
				foreach($obj as $objeto){
					 echo "$nombregrid.getCombo($posicion).put('".$objeto->$id."','".$objeto->$descripcion."');";
				}
			}
	}
	
	function combogrid_iva($obj, $posicion, $id = 'id', $descripcion='descripcion', $textoSeleccion='Seleccionar..', $nombregrid='mygrid'){
		//die($descripcion);
		//die(print_r($obj));
			$aux = 199;
			echo "$nombregrid.getCombo($posicion).put(".$aux.",'".$textoSeleccion."');";
			if(is_array($obj)){
				foreach($obj as $objeto){
					 echo "$nombregrid.getCombo($posicion).put(".$objeto->$id.",'".$objeto->$descripcion."');";
				}
			}
	}
	
//CEPV.190606.SN 
	function combonomina($conn, 
								$tabla, 
								$id_selected='', 
								$style='', 
								$order='id', 
								$nombre='',
								$Campo1='', 
								$Campo2='', 
								$id='', 
								$atributo='',
								$sql='',
								$onchange='',
								$Seleccione='',
								$SeleccioneDesc='Seleccione'){
		$q = empty($sql) ? "SELECT * FROM $tabla ORDER BY $order" : $sql;
		//echo $q;
		$r = $conn->Execute($q);
		$nombre = empty($nombre)? $tabla : $nombre;
		$id = empty($id) ? $nombre : $id;
		$combo = "<select onChange=\"$onchange\" name=\"$nombre\" id=\"$id\"";
		$combo.= (!empty($atributo))? "$atributo" : "";
		$combo.= (!empty($style))? " style=\"$style\"" : "";
		$combo.= ">\n";
		if(!empty($Seleccione)){ $combo.="<option value=\"-1\">$SeleccioneDesc</option>\n";}
		//die($combo);
		while(!$r->EOF){
			$id = $r->fields[$Campo1];
			$descripcion = $r->fields[$Campo2];
			if($id == $id_selected)
				$combo.="<option value=\"$id\" selected=\"selected\">$descripcion</option>\n";
			else
				$combo.="<option value=\"$id\">$descripcion</option>\n";
			$r->movenext();
		}
		$combo.="</select>\n";
		return $combo;
	}	
	function combonominaII($conn, 
								$tabla, 
								$id_selected='', 
								$style='', 
								$order='int_cod', 
								$nombre='',
								$Campo1='', 
								$Campo2='', 
								$id='', 
								$atributo='',
								$atributoII='',
								$onchange='',
								$sql=''){
		$q = empty($sql) ? "SELECT * FROM rrhh.$tabla ORDER BY $order" : $sql;
		$r = $conn->Execute($q);
		$nombre = empty($nombre)? $tabla : $nombre;
		$id = empty($id) ? $tabla : $id;
		$combo = "<select  onChange=\"$onchange\" name=\"$nombre\" id=\"$id\"";
		$combo.= (!empty($atributo))? "$atributo" : "";
		$combo.= (!empty($style))? " style=\"$style\"" : "";
		$combo.= ">\n";
		//die($combo);
		while(!$r->EOF){
			$id = $r->fields[$Campo1];
			$descripcion = $r->fields[$Campo2];
			$combo.="<option value=\"$id\" >[".$atributoII.":".$id."_".$descripcion."]</option>\n";
			$r->movenext();
		}
		$combo.="</select>\n";
		return $combo;
	}	
	function combonominaIII($conn, 
								$tabla, 
								$id_selected='', 
								$style='', 
								$order='int_cod', 
								$nombre='',
								$Campo1='', 
								$Campo2='', 
								$Campo3='', 
								$id='', 
								$atributo='',
								$sql='',
								$onchange='',
								$Seleccione='',
								$NoCadena='',
								$SeleccioneDesc='Seleccione'){
		$q = empty($sql) ? "SELECT * FROM $tabla ORDER BY $order" : $sql;
		//echo $q;
		$r = $conn->Execute($q);
		$nombre = empty($nombre)? $tabla : $nombre;
		$id = empty($id) ? $nombre : $id;
		$combo = "<select onChange=\"$onchange\" name=\"$nombre\" id=\"$id\"";
		$combo.= (!empty($atributo))? "$atributo" : "";
		$combo.= (!empty($style))? " style=\"$style\"" : "";
		$combo.= ">\n";
		if(!empty($Seleccione)){ $combo.="<option value=\"-1\">$SeleccioneDesc</option>\n";}
		//die($combo);
		while(!$r->EOF){
			$id = $r->fields[$Campo1];
			if($NoCadena){
				$descripcion = $r->fields[$Campo2]." ".$r->fields[$Campo3];
			}else{
				$descripcion = Cadena($r->fields[$Campo2])." ".Cadena($r->fields[$Campo3]);
			}
			if($id == $id_selected)
				$combo.="<option value=\"$id\" selected=\"selected\">$descripcion</option>\n";
			else
				$combo.="<option value=\"$id\">$descripcion</option>\n";
			$r->movenext();
		}
		$combo.="</select>\n";
		return $combo;
	}

	/*********************************************************************************************************************	
    Dibuja un combo (nodo select) a partir de un patron
    
   superCombo(   $conn, << conexion adodb
                 $patron, << esta variable puede recibir 3 elementos:
                          1) El nombre de una tabla, de ser utilizado este patron los atributos name y id seran iguales
                             al nombre de la misma.
                             a la cual se va a hacer la consulta tipo "SELECT * FROM $tabla",
                          2) Un query sql, en caso que sea una consulta mas complicada
                          3) Un array de objetos
                 $id_seleccionado='', << si tenemos un registro seleccionado, aca enviamos el ID (value)
                 $nombre='', << atributo name del nodo Select
                 $id='', << atributo id del nodo Select
                 $style='', << atributo style del nodo Select
                 $onchange='', << atributo onchange del nodo Select
                 $descValor = 'id', << nombre del campo en la BD que sera el value del nodo Select
                 $descDescripcion = 'descripcion', << nombre del campo en la BD que sera la descripcion del nodo Select
                 $orden='', << SQL: "ORDER BY $orden"
                 $tamanoDesc='', << cantidad de caracteres maximo para la descripcion
                 $attr='', << algun atributo adicional como por ejemplo, readonly=readonly
                 $valorCero='Seleccione') << descripcion del valor cero del combo
	*********************************************************************************************************************/
function combonominaIV($conn, 
								$tabla, 
								$id_selected='', 
								$style='', 
								$order='int_cod', 
								$nombre='',
								$Campo1='', 
								$Campo2='', 
								$Campo3='', 
								$Campo4='',
								$id='', 
								$atributo='',
								$sql='',
								$onchange='',
								$Seleccione='',
								$tamanoDesc='',
								$SeleccioneDesc='Seleccione'){
		$q = empty($sql) ? "SELECT * FROM $tabla ORDER BY $order" : $sql;
		//die($sql);
		//echo $q;
		$r = $conn->Execute($q);
		$nombre = empty($nombre)? $tabla : $nombre;
		$id = empty($id) ? $nombre : $id;
		$combo = "<select onChange=\"$onchange\" name=\"$nombre\" id=\"$id\"";
		$combo.= (!empty($atributo))? "$atributo" : "";
		$combo.= (!empty($style))? " style=\"$style\"" : "";
		$combo.= ">\n";
		if(!empty($Seleccione)){ $combo.="<option value=\"-1\">$SeleccioneDesc</option>\n";}
		//die($combo);
		while(!$r->EOF){
			$id = $r->fields[$Campo1];
			$descripcion = $r->fields[$Campo2]." ".$r->fields[$Campo3]." - ".$r->fields[$Campo4];
			if(!empty($tamanoDesc) && (strlen($descripcion) > $tamanoDesc))
						$descripcion = substr($descripcion, 0, $tamanoDesc)."(...)"; 

			if($id == $id_selected)
				$combo.="<option value=\"$id\" selected=\"selected\">$descripcion</option>\n";
			else
				$combo.="<option value=\"$id\">$descripcion</option>\n";
			$r->movenext();
		}
		$combo.="</select>\n";
		return $combo;
	}

	/*********************************************************************************************************************	
    Dibuja un combo (nodo select) a partir de un patron
    
   superCombo(   $conn, << conexion adodb
                 $patron, << esta variable puede recibir 3 elementos:
                          1) El nombre de una tabla, de ser utilizado este patron los atributos name y id seran iguales
                             al nombre de la misma.
                             a la cual se va a hacer la consulta tipo "SELECT * FROM $tabla",
                          2) Un query sql, en caso que sea una consulta mas complicada
                          3) Un array de objetos
                 $id_seleccionado='', << si tenemos un registro seleccionado, aca enviamos el ID (value)
                 $nombre='', << atributo name del nodo Select
                 $id='', << atributo id del nodo Select
                 $style='', << atributo style del nodo Select
                 $onchange='', << atributo onchange del nodo Select
                 $descValor = 'id', << nombre del campo en la BD que sera el value del nodo Select
                 $descDescripcion = 'descripcion', << nombre del campo en la BD que sera la descripcion del nodo Select
                 $orden='', << SQL: "ORDER BY $orden"
                 $tamanoDesc='', << cantidad de caracteres maximo para la descripcion
                 $attr='', << algun atributo adicional como por ejemplo, readonly=readonly
                 $valorCero='Seleccione') << descripcion del valor cero del combo
	*********************************************************************************************************************/
	function superCombo($conn,
                       $patron,
                       $id_seleccionado='', 
                       $nombre='', 
                       $id='', 
                       $style='', 
                       $onchange='',
                       $descValor = 'id',
                       $descDescripcion = 'descripcion',
                       $orden='',
                       $tamanoDesc='',
                       $attr='',
                       $valorCero='Seleccione',
                       $class=''){
		$xml = new DomDocument();
		$nodoSelect = $xml->createElement('select');
		$nodoSelect->setAttribute('name', $nombre);
		$nodoSelect->setAttribute('id', $id);
		if(!empty($style))
			$nodoSelect->setAttribute('style', $style);
		if(!empty($class))
			$nodoSelect->setAttribute('class', $class);
		if(!empty($onchange))
			$nodoSelect->setAttribute('onchange', $onchange);
		if(!empty($attr))
			switch($attr){
				case "disabled":
					$nodoSelect->setAttribute('disabled', 'disabled');
					break;
				default:
					break;
			}
		$nodoOption = $xml->createElement('option');
		$nodoOption->setAttribute('value', 0);
		$nodoOption->appendChild($xml->createTextNode($valorCero));
		$nodoSelect->appendChild($nodoOption);

		switch (gettype($patron)) {
			case 'array':
				foreach($patron as $objeto){
					$nodoOption = $xml->createElement('option');
					$nodoOption->setAttribute('value', $objeto->$descValor);
					$descripcion = $objeto->$descDescripcion;
					if(!empty($tamanoDesc) && (strlen($descripcion) > $tamanoDesc))
						$descripcion = substr($descripcion, 0, $tamanoDesc)."(...)";
					$nodoTextoDescripcion = $xml->createTextNode($descripcion);
					$nodoOption->appendChild($nodoTextoDescripcion);
					if($objeto->$descValor == $id_seleccionado)
						$nodoOption->setAttribute('selected', 'selected');
					$nodoSelect->appendChild($nodoOption);
				}
				break;
			case 'string':
				if(str_word_count($patron) == 1){
					$nombre = empty($nombre)? $patron : $nombre;
					$id = empty($id) ? $patron : $id;
				}
				$orden = !empty($orden) ? "ORDER BY $orden" : "";
				$q = strstr($patron, " ") == false ? "SELECT * FROM $patron $orden " : $patron;
				if(!$r = $conn->Execute($q))
					return false;
				while(!$r->EOF){
					$nodoOption = $xml->createElement('option');
					$nodoOption->setAttribute('value', $r->fields[$descValor]);
					$descripcion = $r->fields[$descDescripcion];
					if(!empty($tamanoDesc) && (strlen($descripcion) > $tamanoDesc))
						$descripcion = substr($descripcion, 0, $tamanoDesc)."(...)"; 
					$nodoTextoDescripcion = $xml->createTextNode($descripcion);
					$nodoOption->appendChild($nodoTextoDescripcion);
					if($r->fields[$descValor] == $id_seleccionado)
						$nodoOption->setAttribute('selected', 'selected');
					$nodoSelect->appendChild($nodoOption);
					$r->movenext();
				}
		}
		return $xml->saveXML($nodoSelect);
	}
}
?>
