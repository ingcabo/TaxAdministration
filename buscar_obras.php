<?
include ("comun/ini.php");

$nro= $_POST['nro'];
$ue = $_POST['ue'];
$oObra = new obras();

$cObras = $oObra->buscar($conn, 
						'', 
						$ue, 
						'', 
						'', 
						$nro,
						$escEnEje,
						$orden="id");
if($_POST['busca'] != 1){
?>
<input type="hidden" name="cp" id="cp" value="<?=$cp?>" />
	<span style="float:right;cursor:pointer;padding:5px;">
		<img onclick="Dialog.okCallback();" src="images/close_div.gif" />
	</span>
	<span class="titulo_maestro">Busqueda de Obras</span>
	<table>
		<tr class="filas">
			<td>N&ordm; de Obra:&nbsp;&nbsp;</td>
			<td><input type="text" name="search_nro" id="search_nro" size="30" onkeyup="busca_popup_obra()" /></td>
		</tr>
		<tr class="filas">
			<td>Unidad Ejecutora:</td>
	<td><?=helpers::combo_ue_cp($conn, 

														'search_ue', 

														'',

														'width:250px',

														'',

														'',

														'',

														"busca_popup_obra();",

														"SELECT id, id||' - '||descripcion AS descripcion FROM unidades_ejecutoras WHERE id_escenario='$escEnEje' ")?>
		</td>
		</tr>
	</table>
	<br /><br />
<? } ?>
	<div id="divObras">
<?

if(is_array($cObras))
{
?>
	<span class="titulo_maestro">Seleccione una Obra</span>
		<table class="sortable" id="grid" cellpadding="0" cellspacing="1" width="600">
			<tr class="cabecera"> 
				<td>Descripci&oacute;n:</td>
			</tr>
			<? 
			foreach($cObras as $obra)
			{
			?> 
				<tr class="filas"> 
					<td><span onclick="selObra('<?=$obra->id?>');" style="cursor:pointer"><?=$obra->id?> - <?=$obra->descripcion?></span></td>
				</tr>
			<?
				}
			?>
		</table>
<?
}
else
{
	echo "No hay registros en la bd";
}
?>
	</div>
