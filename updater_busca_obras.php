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
$id_financiadora = $_GET['id_financiadora'];
$id_ue = $_GET['id_ue'];
$id = $_GET['id'];
$fecha_desde = $_GET['fecha_desde'];
$fecha_hasta = $_GET['fecha_hasta'];
//die('aqui '.$id_financiadora);
$cObras = obras::buscar($conn, 
						$id_financiadora, 
						$id_ue, 
						$fecha_desde, 
						$fecha_hasta, 
						$id,
						$escEnEje 
						);
$total_obras = obras::total_registro_busqueda($conn, $id_financiadora, $id_ue, $fecha_desde, $fecha_hasta, $id);
$total = $total_obras;
 if(is_array($cObras)){ ?>
<table id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Descripci&oacute;n</td>
<td>Financiadora</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<?
$i = 0;
foreach($cObras as $obras) { 
?> 
<tr class="filas"> 
<td><?=$obras->obra_cod?></td>
<td><?=$obras->descripcion?></td>
<td><?=$obras->financiamiento?></td>
<td><a href="?accion=del&id=<?=$obras->id?>" onClick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
<td align="center">
<a href="#" onClick="updater('<?=$obras->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a>
</td>
</tr>
<? $i++;
	}
?>
<? $total_paginas = ceil($total / 20);?>
<tr class="filas">
		<td colspan="7" align="center">
<? for ($j=1;$j<=$total_paginas;$j++){
		 if ($j==1){ ?>
			<span style="cursor:pointer" onClick="busca($F('busca_ue'), $F('busca_proveedores'), $F('busca_observaciones'), $F('busca_fecha_desde'), $F('busca_fecha_hasta'),$F('busca_nrodoc'),'<?=$j?>');"><?=$j?></span>
		<? }else {?>	
		<span style="cursor:pointer" onClick="busca($F('busca_ue'), $F('busca_proveedores'), $F('busca_observaciones'), $F('busca_fecha_desde'), $F('busca_fecha_hasta'),$F('busca_nrodoc'),'<?=$j?>');">- <?=$j?></span>
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
