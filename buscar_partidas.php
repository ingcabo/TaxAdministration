<?
include ("comun/ini.php");

$nombre = $_REQUEST['nombre'];
$codigo = $_REQUEST['codigo'];
$opcion = $_REQUEST['opcion'];
$cp = $_REQUEST['cp'];
$filtro = explode(",", urldecode($_REQUEST['filtro']));

$escenario = !empty($_REQUEST['escenario']) ? $_REQUEST['escenario'] : $escEnEje;

if (empty($_REQUEST['filtro'])){
	if(empty($cp))
		$cPartidas = partidas_presupuestarias::get_all_by_esc($conn, $escenario='1111', '',0,1000,'id','', $nombre);
	else if(empty($codigo))
		$cPartidas = partidas_presupuestarias::get_all_by_cp($conn, $cp, $escenario, $_GET['idp'], 0, 0, $nombre);
	else
		$cPartidas = partidas_presupuestarias::get_all_by_cp($conn, $cp, $escenario, $codigo, 0, 0, $nombre);
}else
	$cPartidas = partidas_presupuestarias::get_all_by_cp($conn, $cp, $escenario, $codigo, 0, 0, $nombre);
	
	//die(var_dump($cPartidas));
?>

<input type="hidden" name="cp" id="cp" value="<?=$cp?>" />
<?
if ($opcion != 2)
{
?>
	<span style="float:right;cursor:pointer;padding:5px;">
		<img onclick="Dialog.okCallback();" src="images/close_div.gif" />
	</span>
	<span class="titulo_maestro">Busqueda de Partidas Presupuestarias</span>
	<table>
		<tr class="filas">
			<td>Nombre:&nbsp;&nbsp;</td>
			<td><input type="text" name="search_nombre_pp" id="search_nombre_pp" size="30" onkeyup="busca_popup_pp()" /></td>
		</tr>
		<tr class="filas">
			<td>C&oacute;digo:</td>
			<td><input type="text" name="search_cod_pp" id="search_cod_pp" size="30" maxlength="13" onkeyup="busca_popup_pp()" /></td>
		</tr>
	</table>
	<br /><br />
<?
}

if(is_array($cPartidas))
{
	if ($opcion != 2)
	{
?>
	<span class="titulo_maestro">Seleccione una Partida Presupuestaria</span>
<?
	}
?>
	<div id="divPartidas">
		<table class="sortable" id="grid" cellpadding="0" cellspacing="1" width="600">
			<tr class="cabecera"> 
				<td>Partidas Presupuestarias:</td>
			</tr>
			<? 
			foreach($cPartidas as $par)
			{
				if (empty($_REQUEST['filtro']) || in_array(substr($par->id, 0, 3), $filtro))
				{
			?> 
				<tr class="filas"> 
					<td><span onclick="selPartidas('<?=$par->id?>', '<?=$par->descripcion?>');traerDisponiblePartidas($('categorias_programaticas').value, '<?=$par->id?>');" style="cursor:pointer"><?=$par->id?> - <?=$par->descripcion?></span></td>
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
