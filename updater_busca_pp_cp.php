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
$cod_categoria = $_GET['cod_cp'];
$id_categoria = $_GET['id_cp'];
$cod_partida = $_GET['cod_pp'];
$id_partida = $_GET['id_pp'];
$cRelacion_pp_cp = relacion_pp_cp::buscar($conn, $id_escenario, $cod_categoria, $id_categoria, $cod_partida, $id_partida, 20, $inicio);
$total_pp_cp = relacion_pp_cp::total_registro_busqueda($conn, $id_escenario, $cod_categoria, $id_categoria, $cod_partida, $id_partida);
$total = $total_pp_cp;
if(is_array($cRelacion_pp_cp)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<!--td>Escenario</td-->
<td>Categor&iacute;as Program&aacute;ticas</td>
<td>Partida Presupuestaria</td>
<td>Ppto. Original</td>
<td>Disponible</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cRelacion_pp_cp as $relacion_pp_cp) { 
?> 
<tr class="filas"> 
<!--td><?=$relacion_pp_cp->escenario?></td-->
<td><?=$relacion_pp_cp->id_categoria_programatica.'<br />'.$relacion_pp_cp->categoria_programatica?></td>
<td><?=$relacion_pp_cp->id_partida_presupuestaria.'<br />'.$relacion_pp_cp->partida_presupuestaria?></td>
<td><?=muestraFloat($relacion_pp_cp->presupuesto_original)?></td>
<td><?=muestraFloat($relacion_pp_cp->disponible)?></td>
<td><a href="?accion=del&id=<?=$relacion_pp_cp->id?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
<td align="center">
<a href="#" onclick="updater(<?=$relacion_pp_cp->id?>); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
</tr>
<? $i++;
	}
?>

<? $total_paginas = ceil($total / 20);?>
<tr class="filas">
		<td colspan="7" align="center">
<? for ($j=1;$j<=$total_paginas;$j++){
		 if ($j==1){ ?>
			<span style="cursor:pointer" onclick="busca($F('busca_escenarios'), $F('busca_id_cp'), $F('busca_cp'), $F('busca_id_pp'), $F('busca_pp'),'<?=$j?>');"><?=$j?></span>
		<? }else {?>	
		<span style="cursor:pointer" onclick="busca($F('busca_escenarios'), $F('busca_id_cp'), $F('busca_cp'), $F('busca_id_pp'), $F('busca_pp'),'<?=$j?>');">- <?=$j?></span>
		<? }
	 }?>
	</td>
	</tr>
	<tr class="filas">
		<td colspan="7" align="center"> Pagina <strong><?=$_REQUEST['pagina']?></strong> de <strong><?=$total_paginas?></strong></td>
	</tr>
</table>
<? }else {
		echo "No hay registros en la bd";
} ?>
