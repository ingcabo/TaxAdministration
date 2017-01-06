<?
include ("comun/ini.php");

$pagina = $_REQUEST['pagina'];
if (!$pagina) {
    $inicio = 10;
    $pagina=1;
}
else {
    $inicio = ($pagina - 1) * 20;
} 
$id = $_GET['id'];
$id_escenario = $_GET['id_escenario'];
$descripcion = $_GET['descripcion'];
$cCategoriasProgramaticas = categorias_programaticas::buscar($conn, $id, $id_escenario, $descripcion, 20, $inicio);
$total_ue_cp = categorias_programaticas::total_registro_busqueda($conn, $id, $id_escenario, $descripcion);
$total = $total_ue_cp;
?>
<? if(is_array($cCategoriasProgramaticas)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Escenario</td>
<td>Descripción</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cCategoriasProgramaticas as $categoriasProgramaticas) { 
?> 
<tr class="filas"> 
<td><?=$categoriasProgramaticas->id?></td>
<td><?=$categoriasProgramaticas->escenario?></td>
<td><?=$categoriasProgramaticas->descripcion?></td>
<td><a href="?accion=del&id=<?=$categoriasProgramaticas->id?>&id_escenario=<?=$categoriasProgramaticas->id_escenario?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
<td align="center">
<a href="#" onclick="updater('<?=$categoriasProgramaticas->id?>&id_escenario=<?=$categoriasProgramaticas->id_escenario?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
</tr>
<? $i++;
	}
?>
<? $total_paginas = ceil($total / 20);?>
<tr class="filas">
		<td colspan="7" align="center">
<? for ($j=1;$j<=$total_paginas;$j++){
		 if ($j==1){ ?>
			<span style="cursor:pointer" onClick="busca($('busca_id').value, $('busca_escenarios').value, $('busca_descripcion').value, '<?=$j?>');"><?=$j?></span>
		<? }else {?>	
		<span style="cursor:pointer" onClick="busca($('busca_id').value, $('busca_escenarios').value, $('busca_descripcion').value, '<?=$j?>');">- <?=$j?></span>
		<? }
	 }?>
	</td>
	</tr>
	<tr class="filas">
		<td colspan="7" align="center"> Pagina <strong><?=$_REQUEST['pagina']?></strong> de <strong><?=$total_paginas?></strong></td>
	</tr>
</table>
</table>
<? }else {
		echo "No hay registros en la bd";
} ?>
