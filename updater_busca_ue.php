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
$responsable = $_GET['responsable'];
$cUE = unidades_ejecutoras::buscar($conn, $id, $id_escenario, $descripcion, $responsable, 20, $inicio);
$total_ue = unidades_ejecutoras::total_registro_busqueda($conn, $id, $id_escenario, $descripcion, $responsable);
$total = $total_ue;
?>
<? if(is_array($cUE)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Descripción de la unidad</td>
<td>Escenario</td>
<td>Funcionario Responsable</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cUE as $UE) { 
?> 
<tr class="filas"> 
<td><?=$UE->id?></td>
<td><?=$UE->descripcion?></td>
<td><?=$UE->escenario->descripcion?></td>
<td><?=$UE->responsable?></td>
<td><a href="?accion=del&id=<?=$UE->id?>&id_escenario=<?=$UE->id_escenario?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
<td align="center">
<a href="#" onclick="updater('<?=$UE->id?>&id_escenario=<?=$UE->id_escenario?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
</tr>
<? $i++;
	}
?>
<? $total_paginas = ceil($total / 20);?>
<tr class="filas">
		<td colspan="7" align="center">
<? for ($j=1;$j<=$total_paginas;$j++){
		 if ($j==1){ ?>
			<span style="cursor:pointer" onClick="busca($('busca_id').value, $('busca_escenarios').value, $('busca_descripcion').value, $('busca_responsable').value, '<?=$j?>');"><?=$j?></span>
		<? }else {?>	
		<span style="cursor:pointer" onClick="busca($('busca_id').value, $('busca_escenarios').value, $('busca_descripcion').value, $('busca_responsable').value, '<?=$j?>');">- <?=$j?></span>
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