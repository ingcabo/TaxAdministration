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

$id_unidades_ejecutoras = $_GET['id'];
$id_escenario= $_GET['id_escenario'];


$cformulacion = formulacion::buscar($conn, $id_unidades_ejecutoras, $id_escenario, 20, $inicio);
$total_formulacion = formulacion::total_registro_busqueda($conn, $id_unidades_ejecutoras, $id_escenario);

?>

<? 
	if(is_array($cformulacion))
	{ 
?>
		<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
			<tr class="cabecera"> 
				<td>C&oacute;digo</td>
				<td>Unidad Ejecutora</td>
				<td>Status</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
<? 
			$i = 0;
			foreach($cformulacion as $formulacion) 
			{ 
				if(($formulacion->status) == 1)
				{
					$status = "Registro";
				}
				else
				{
					$status = "Aprobado";
				}
?> 
			<tr class="filas"> 
				<td><?=$formulacion->id_formulacion?></td>
				<td align="center"><?=$formulacion->desc_ue?></td>
				<td align="center"><?=$status?></td>
				<td align="center"><a href="#" onclick="updater('<?=$formulacion->id_formulacion?>'); return false;" title="Modificar &oacute; Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0" alt="actualizar"></a></td>
<? 
				if(($formulacion->status) == 1)
				{
?>
					<td align="center">
						<a href="formulacion.php?accion=del&id_form=<?=$formulacion->id_form?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}" title="Eliminar Registro" ><img src="images/eliminar.gif" width="16" height="10" border="0" alt="eliminar"></a>
					</td>
<?
				}else{?>
				
					<td>&nbsp;</td>
				
				<? }?>
	
			</tr>
<? $i++;
	}
?>
<? $total_paginas = ceil($total_formulacion / 20);?>
<tr class="filas">
		<td colspan="7" align="center">
<? for ($j=1;$j<=$total_paginas;$j++){
		 if ($j==1){ ?>
			<span style="cursor:pointer" onClick="busca($('busca_ue').value, $('busca_escenario').value,'<?=$j?>');"><?=$j?></span>
		<? }else {?>	
		<span style="cursor:pointer" onClick="busca($('busca_ue').value, $('busca_escenario').value, '<?=$j?>');">- <?=$j?></span>
		<? }
	 }?>
	</td>
	</tr>
	<tr class="filas">
		<td colspan="7" align="center"> Pagina <strong><?=$_REQUEST['pagina']?></strong> de <strong><?=$total_paginas?></strong></td>
	</tr>
		</table>
<? 
	}
	else 
	{
		echo "No hay registros en la bd";
	} 
?>