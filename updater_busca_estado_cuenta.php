<?
include ("comun/ini.php");
$pagina = $_REQUEST['pagina'];
$tamano_pagina = 10;

if (!$pagina) 
{
	$inicio = 10;
	$pagina=1;
}
else 
{
	$inicio = ($pagina - 1) * $tamano_pagina;
} 
$id_banco = $_GET['id_banco'];
$id_cta_banc= $_GET['id_cuenta'];
$fecha_desde = $_GET['fecha_desde'];
$fecha_hasta = $_GET['fecha_hasta'];

$oEstadoCuenta = new estadoCuenta;
$cEstadoCuenta = $oEstadoCuenta->buscar($conn, $id_banco, $id_cta_banc, $fecha_desde, $fecha_hasta, $tamano_pagina, $inicio);
//die(var_dump($cConciliacion));
$total = $oEstadoCuenta->total_registro_busqueda($conn, $id_banco, $id_cta_banc, $fecha_desde, $fecha_hasta);

if(is_array($cEstadoCuenta) && count($cEstadoCuenta) > 0)
{
?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
	<tr class="cabecera"> 
		<td width="20%">Codigo Cuenta</td>
		<td width="">Descripci&oacute;n</td>
		<td width="12%">Fecha</td>
		<td width="5%">&nbsp;</td>
		<td width="5%">&nbsp;</td>
	</tr>
<? 
	foreach($cEstadoCuenta as $estadoCuenta) 
	{ 
	//die(var_dump($estadoCuenta));
?> 
	<tr class="filas"> 
	
		<td><?=$estadoCuenta->cuenta->nro_cuenta?></td>
		<td><?=$estadoCuenta->cuenta->banco->descripcion?></td>
		<td align="center"><?=$estadoCuenta->fecha_desde?></td>
		<td align="center"><a href="" onClick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
		<td align="center"><a href="#" onClick="updater(<?=$estadoCuenta->id?>); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
	</tr>
<?
	}

	$total_paginas = ceil($total / $tamano_pagina);
?>
	<tr class="filas">
		<td colspan="7" align="center">
	<?
	for ($j=1; $j<=$total_paginas; $j++)
	{
		if ($j==$pagina)
		{
		?>
			<span><?=(($j>1) ? ' - ':'').$j?></span>
		<?
		}
		else
		{
		?>
			<span style="cursor:pointer" onClick="busca($('cta_banc').value, $('busca_fecha_desde').value, $('busca_fecha_hasta').value, '<?=$j?>');"><?=(($j>1) ? ' - ':'').$j?></span>
		<?
		}
	}
	?>
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
