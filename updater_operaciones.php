<?
include ("comun/ini.php");
$id_usuario = $_REQUEST['id_usuario'];
$id_operacion = $_REQUEST['id_operacion'];

$operaciones = new operaciones;
$aModulos = $operaciones->getAllMods($conn);

if(!empty($id_operacion) and !empty($id_usuario)){
	if($operaciones->has_permiso($conn, $id_usuario, $id_operacion))
		$operaciones->del_permiso($conn, $id_usuario, $id_operacion);
	else
		$operaciones->add_permiso($conn, $id_usuario, $id_operacion);
}
$i = 1;
foreach($aModulos as $modulo){
?>
	<span class="titulo_maestro" ><?=$modulo->descripcion?></span>
	<ul>
<?
	$j = 1;
	$aPermisos = $operaciones->get_all($conn, $modulo->id);
if(is_array($aPermisos)){
	foreach($aPermisos as $permiso){
		$tipo = $permiso->tipo == 'C' ? '<img src="images/folders/carpeta.gif" />': '';
	// revisa si el usuario tiene permisos sobre esa operacion
		$checked = ($operaciones->has_permiso($conn, $id_usuario, $permiso->id)) ? "checked=\"checked\"" : "";
?>
		<li><input type="checkbox" id="permiso_<?=$i?>_<?=$j?>"  onclick="setPermisos(<?=$id_usuario?>, <?=$permiso->id?>);" <?=$checked?> /><label onclick="new Effect.Highlight(this.parentNode);" for="permiso_<?=$i?>_<?=$j?>"><?= !empty($permiso->nom_padre) ? $permiso->nom_padre." \\ ".$permiso->descripcion : $permiso->descripcion?> <?=$tipo?></label></li>
<? $j++; }
}else{
	echo "No hay operaciones en este m&oacute;dulo";
}
?>
	</ul>
<? $i++; } ?>
