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

$id_ue = $_GET['id_ue'];
$fecha_desde = $_GET['fecha_desde'];
$fecha_hasta = $_GET['fecha_hasta'];
$nrequi = $_GET['nrequi'];
$status = $_GET['status'];
$oRequisiciones = requisiciones::buscar($conn, 
						$id_ue, 
						$fecha_desde, 
						$fecha_hasta,
						'id',
						$escEnEje,
						20, 
						$inicio,
						$status,
						$nrequi
						);
						
$total_r = requisiciones::total_registro_busqueda($conn, $id_ue, $fecha_desde, $fecha_hasta,'id',$status,$nrequi);
$total = $total_r;
?>
<? if(is_array($oRequisiciones)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Unidad Ejecutora:</td>
<td>Status:</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($oRequisiciones as $requisiciones) { 
?> 
<tr class="filas"> 
<td><?=$requisiciones->id?></td>
<td><?=$requisiciones->unidad_ejecutora?></td>
<td><?=$requisiciones->nom_status?></td>
<td align="center"><? if($requisiciones->status!='01') echo '<a href="reporte_requisicion.pdf.php?id_requisicion='.$requisiciones->id.'" title="Emitir Reporte"><img style="width:16px; height:10px" src="images/reporte.jpg" border="0" /></a>'; ?></td>
<td align="center"><a href="" onClick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
<td align="center">
<a href="#" onClick="updater('<?=$requisiciones->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
</tr>
<? $i++;
	}
?>
<? $total_paginas = ceil($total / 20);?>
<tr class="filas">
		<td colspan="7" align="center">
<? for ($j=1;$j<=$total_paginas;$j++){
		 if ($j==1){ ?>
			<span style="cursor:pointer" onClick="busca($('busca_ue').value, $('busca_fecha_desde').value, 
	$('busca_fecha_hasta').value , '<?=$j?>', $('busca_status').value, $('busca_nrequi').value);"><?=$j?></span>
		<? }else {?>	
		<span style="cursor:pointer" onClick="busca($('busca_ue').value, $('busca_fecha_desde').value, 
	$('busca_fecha_hasta').value , '<?=$j?>', $('busca_status').value, $('busca_nrequi').value);">- <?=$j?></span>
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