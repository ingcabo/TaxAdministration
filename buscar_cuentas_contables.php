<?
include ("comun/ini.php");

$descripcion = $_REQUEST['descripcion'];
$codigo = $_REQUEST['codigo'];
$opcion = $_REQUEST['op'];
$movim = $_REQUEST['movim'];

$cPlancuenta = plan_cuenta::buscar($conn, $codigo, $descripcion);
//var_dump($cPlancuenta);echo "<br />";
if ($opcion != 2)
{
?>
<span style="float:right; padding:2px; cursor:pointer;">
	<img onclick="Dialog.okCallback();" src="images/close_div.gif" />
</span>
<span class="titulo_maestro">Busqueda de Cuentas Contables</span>
<table>
	<tr>
		<td>Descripcion:</td>
		<td><input type="text" name="search_descrip" id="search_descrip" size="30" onkeyup="busca_popup()" /></td>
	</tr>
	<tr>
		<td>C&oacute;digo</td>
		<td><input type="text" name="search_codigo" id="search_codigo" size="30" onkeyup="busca_popup()" /></td>
	</tr>
</table>
<br /><br />
<?
}

$cantCtas = 0;
//var_dump($cPlancuenta);
if(is_array($cPlancuenta)){
	foreach ($cPlancuenta as $cuenta){
		if (empty($movim) || $cuenta->movim==$movim)
			$cantCtas++;
	}
}

if(is_array($cPlancuenta) && $cantCtas>0)
{
?>
<div id="divPlanCtas">
	<span class="titulo_maestro">Seleccione una Cuenta</span>
	<table class="sortable" id="grid" cellpadding="0" cellspacing="1" width="600">
		<tr class="cabecera"> 
			<td>C&oacute;digo</td>
			<td>Descripci&oacute;n</td>
		</tr>
		<? 
		foreach($cPlancuenta as $cuenta) 
		{ 
			if (empty($movim) || $cuenta->movim==$movim)
			{
		?> 
			<tr class="filas"> 
				<td><span onclick="selDocumento('<?=$cuenta->codcta?>', '<?=$cuenta->descripcion?>');" style="cursor:pointer"><?=$cuenta->codcta?></span>
				<td><span onclick="selDocumento('<?=$cuenta->codcta?>', '<?=$cuenta->descripcion?>');" style="cursor:pointer"><?=$cuenta->descripcion?></span></td>
			</tr>
		<?
			}
		}
		?>
	</table>
</div>
<? 
}
else
{
	echo "No hay registros en la bd";
} 
?>
