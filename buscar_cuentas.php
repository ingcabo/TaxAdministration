<?
include ("comun/ini.php");
$descripcion = $_REQUEST['descripcion'];
$id_cuenta = $_REQUEST['id_cuenta'];
$tipo = $_REQUEST['tipo'];

$cCuentas = plan_cuenta::buscaCuenta($conn, $descripcion, $id_cuenta, $tipo, $escEnEje);
?>
<span style="float:right; cursor:pointer; padding:2px;">
	<img onclick="Dialog.okCallback();" src="images/close_div.gif" />
</span>

<span class="titulo_maestro">Cuentas Contables</span>

<table>
	<tr class="filas">
		<td>Nombre:&nbsp;&nbsp;</td>
		<td><input type="text" name="search_descrip" id="search_descrip" size="30" onkeyup="busca_popup()" /></td>
	</tr>
</table>
<br /><br />
<?
if(is_array($cCuentas))
{
?>
	<div id="divProveedores">
		<span class="titulo_maestro">Seleccione Una Cuenta</span>
		<table class="sortable" id="grid" cellpadding="0" cellspacing="1" width="600">
			<tr class="cabecera"> 
				<td>Cuentas:</td>
			</tr>
		<? 
		foreach($cCuentas as $pro)
		{ 
		?> 
			<tr class="filas">
			
				<td>
					<span onclick="selDocumento('<?=$pro->id?>', '<?=$pro->descripcion?>');" style="cursor:pointer"><?=$pro->codcta?> - <?=$pro->descripcion?></span>
				</td>
			
			</tr>
		<?
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