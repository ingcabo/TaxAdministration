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
$id_concepto = $_GET['id_concepto'];
$cRelacion_conc_pp = relacion_conc_pp::buscar($conn, $id_escenario, $id_concepto, 20, $inicio);
$total_conc_pp = relacion_conc_pp::total_registro_busqueda($conn, $id_escenario, $id_concepto);
$total = $total_conc_pp;
if(is_array($cRelacion_conc_pp)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<!--td>Escenario</td-->
<td>Concepto</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cRelacion_conc_pp as $relacion_conc_pp) { 
?> 
<tr class="filas"> 
<td><?=$relacion_conc_pp->concepto?></td>
<td><a href="?accion=del&id=<?=$relacion_conc_pp->int_cod?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
<td align="center">
<a href="#" onclick="updater('<?=$relacion_conc_pp->int_cod?>&id_escenario=<?=$relacion_conc_pp->id_escenario?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
</tr>
<? $i++;
	}
?>

<? $total_paginas = ceil($total / 20);?>
<tr class="filas">
		<td colspan="7" align="center">
<? for ($j=1;$j<=$total_paginas;$j++){
		 if ($j==1){ ?>
			<span style="cursor:pointer" onclick="busca($F('busca_escenarios'),  $F('busca_cp'), $F('busca_id_pp'), $F('busca_pp'),'<?=$j?>');"><?=$j?></span>
		<? }else {?>	
		<span style="cursor:pointer" onclick="busca($F('busca_escenarios'), $F('busca_cp'), $F('busca_id_pp'), $F('busca_pp'),'<?=$j?>');">- <?=$j?></span>
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
