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
$id_unidad = $_GET['id_ue'];
$descripcion = $_GET['descripcion'];
$cRelacion_ue_cp = relacion_ue_cp::buscar($conn, 
                                          $id_escenario, 
														$id_unidad,
                            							$cod_categoria, 
														$id_categoria, 
														$descripcion,
														$escEnEje,
														20, 
														$inicio);
$total_ue_cp = relacion_ue_cp::total_registro_busqueda($conn, $id_escenario, $id_unidad, $cod_categoria, $id_categoria, $descripcion);
$total = $total_ue_cp;
//var_dump($total);echo"<br/>";
?>
<? if(is_array($cRelacion_ue_cp)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>Escenario</td>
<td>C&oacute;digo de Categor&iacute;as</td>
<td>Categor&iacute;as Program&aacute;ticas</td>
<td>C&oacute;digo de Unidades</td>
<td>Unidades Ejecutoras</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cRelacion_ue_cp as $relacion_ue_cp) { 
?> 
<tr class="filas"> 
<td><?=$relacion_ue_cp->escenario?></td>
<td><?=$relacion_ue_cp->id_categoria_programatica?></td>
<td><?=$relacion_ue_cp->categoria_programatica?></td>
<td><?=$relacion_ue_cp->id_unidad_ejecutora?></td>
<td><?=$relacion_ue_cp->unidad_ejecutora?></td>
<td><a href="?accion=del&id=<?=$relacion_ue_cp->id?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
<td align="center"><a href="#" onclick="updater(<?=$relacion_ue_cp->id?>); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
</tr>
<? $i++;
	}
?>
<? $total_paginas = ceil($total / 20);?>
<tr class="filas">
		<td colspan="7" align="center">
<? for ($j=1;$j<=$total_paginas;$j++){
		 if ($j==1){ ?>
			<span style="cursor:pointer" onClick="busca($('busca_escenarios').value,$('busca_ue').value , $('busca_id_cp').value, $('busca_cp').value , $('busca_descripcion').value, '<?=$j?>');"><?=$j?></span>
		<? }else {?>	
		<span style="cursor:pointer" onClick="busca($('busca_escenarios').value,$('busca_ue').value , $('busca_id_cp').value, $('busca_cp').value , $('busca_descripcion').value, '<?=$j?>');">- <?=$j?></span>
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
