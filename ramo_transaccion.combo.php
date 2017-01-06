<?php //se7h:[mar][2006]
/* MUESTRO UN PULLDOWN EN RAMO_TRANSACCION.PHP, SEGUN EL VALOR SELECCIONADO MUESTRO UN RESULTADO */
require ("comun/ini.php");

function combo_($conn, $tabla){
	$q="SELECT * FROM vehiculo.$tabla WHERE status=1";
	$r=$conn->Execute($q);
		$cs='<select name="valor" id="valor">';
		while(!$r->EOF){
			$cs.='<option value="'.$r->fields['id'].'">'.$r->fields['descripcion'].'</option>';
		$r->MoveNext();
		}	
		$cs.='</select>';
		return $cs;
}
$btn='<input type="button" name="btn_buscar" value="Aceptar" OnClick="trae_resultado($(\'tipo\').value, $(\'valor\').value);">';
$i=$_REQUEST['tipo'];

switch ($i) {
    case 0:
        echo '';
        break;
    case 1://ramo
        echo combo_($conn, 'ramo_imp').' '.$btn;
        break;
    case 2:
        echo combo_($conn, 'tipo_transaccion').' '.$btn;
        break;
    case 3:
        echo '<input type="text" name="valor" id="valor" value="" maxlength="4" size="5"> '.$btn;
        break;		
}

?>