<?
include ("comun/ini.php");
$pagina = $_REQUEST['pagina'];
$tamano_pagina = 20;

if (!$pagina) 
{
	$inicio = 10;
	$pagina=1;
}
else 
{
	$inicio = ($pagina - 1) * $tamano_pagina;
} 
$nrocta = $_GET['nro_cuenta'];
$banco = $_GET['banco'];

$cCuentaBanc = cuentas_bancarias::buscar($conn, $nrocta, $banco, $tamano_pagina, $inicio);
//die(var_dump($cCuentaBanc));
$total = cuentas_bancarias::total_registro_busqueda($conn, $nrocta, $banco);

if(count($cCuentaBanc) > 0)
{
?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
	<tr class="cabecera"> 
		<td width="20%">Nro. Cuenta</td>
		<td width="70%">Banco</td>
		<td width="5%">&nbsp;</td>
		<td width="5%">&nbsp;</td>
	</tr>
<? 
	foreach($cCuentaBanc as $cb) 
	{ 
?> 
	<tr class="filas"> 
		<td><?=$cb->nro_cuenta?></td>
		<td><?=$cb->banco->descripcion?></td>
		<td align="center">
			<a href="?accion=del&id=<?=$plan_cuenta->id?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a>
		</td>
		<td align="center">
			<a href="#" onclick="updater(<?=$plan_cuenta->codcta?>); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a>
		</td>
	</tr>
<?
	}

	$total_paginas = ceil($total / $tamano_pagina);
?>
	<tr class="filas">
		<td colspan="7" align="center">
	<?
	for ($j=1;$j<=$total_paginas;$j++)
	{
		if ($j==1)
		{
			if ($j==$pagina)
				echo "<span>$j</span>";
			else
				echo '<span style="cursor:pointer" onclick="busca($(\'busca_nro_cta\').value,$(\'busca_bancos\').value , \'$j\');">$j</span>';
		}
		else
		{
			if ($j==$pagina)
				echo "<span>- $j</span>";
			else
				echo '<span style="cursor:pointer" onclick="busca($(\'busca_nro_cta\').value,$(\'busca_bancps\').value , \'$j\');">- $j</span>';
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
