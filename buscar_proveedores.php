<?
include ("comun/ini.php");
$tipo = $_REQUEST['tipo'];
$status = $_REQUEST['status'];
$pc = $_REQUEST['pc'];
$rif = $_REQUEST['rif'];
$nombre = $_REQUEST['nombre'];
$opcion = $_REQUEST['opcion'];
$cProveedores = proveedores::buscarProveedoresContrato($conn, $tipo, $status, $rif, $nombre);
?>
<input type="hidden" name="pc" id="pc" value="<?=$pc?>" />
<?
if ($opcion != 2)
{
?>
<span style="float:right; cursor:pointer; padding:2px;">
	<img onclick="Dialog.okCallback();" src="images/close_div.gif" />
</span>

<?
if ($pc == 2)
	echo '<span class="titulo_maestro">Busqueda de Ciudadanos</span>';
else
	echo '<span class="titulo_maestro">Busqueda de Proveedores</span>';
?>

<table>
	<tr class="filas">
		<td>Nombre:&nbsp;&nbsp;</td>
		<td><input type="text" name="search_nombre_prov" id="search_nombre_prov" size="30" onkeyup="busca_popup()" /></td>
	</tr>
	<tr class="filas">
		<td>
		<?
		if ($pc == 2)
			echo "C&eacute;dula:";
		else
			echo "RIF:";
		?>
		</td>
		<td><input type="text" name="search_rif_prov" id="search_rif_prov" size="30" maxlength="12" onkeyup="busca_popup()" /></td>
	</tr>
	<tr class="filas">
		<td>
		Tipo:
		</td>
		<td><select name="tipo_prov" id="tipo_prov" onchange="busca_popup()" style="width:180px" >
			<option value="">Seleccione</option>
			<option value="P">Proveedor</option>
			<option value="C">Contratista</option>
			<option value="A">Ambos</option>
			<option value="B">Ciudadano</option>
			<option value="S">Proveedor de Servicios</option>
			<option value="G">Gobiernos Comunitarios</option>
			</select></td>
	</tr>
</table>
<br /><br />
<?
}

if(is_array($cProveedores))
{
?>
	<div id="divProveedores">
		<?
		if ($pc==2)
			echo '<span class="titulo_maestro">Seleccione Un Ciudadano</span>';
		else
			echo '<span class="titulo_maestro">Seleccione Un Proveedor</span>';
		?>
		<table class="sortable" id="grid" cellpadding="0" cellspacing="1" width="600">
			<tr class="cabecera"> 
				<td>Proveedores:</td>
			</tr>
		<? 
		foreach($cProveedores as $pro)
		{ 
		?> 
			<tr class="filas">
			<?
			if ($pc!=2)
			{
			?> 
				<td>
					<span onclick="selDocumento('<?=$pro->id?>', '<?=$pro->rif?>');traeProveedorDesdeXML('<?=$pro->id?>')" style="cursor:pointer"><?=$pro->rif?> - <?=$pro->nombre?></span>
				</td>
			<?
			}
			else
			{
			?>
				<td>
					<span onclick="selDocumento2('<?=$pro->id?>', '<?=$pro->rif?>');traeCiudadanoDesdeXML2('<?=$pro->id?>')" style="cursor:pointer"><?=$pro->rif?> - <?=$pro->nombre?></span>
				</td>
			<?
			}
			?>
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