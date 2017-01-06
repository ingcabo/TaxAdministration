<? require ("comun/ini.php");
#Creando el objeto Iva#
$oIva = new iva;
$accion = $_REQUEST['accion'];

#SECCION DE GUARDAR#
if($accion == 'Guardar' and !empty($_REQUEST['idpartida'])){
	if($oIva->add($conn, $_REQUEST['idpartida'], $_REQUEST['porc_iva'], $_REQUEST['anio']))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;

#SECCION DE ACTULIZAR#
}elseif($accion == 'Actualizar' and !empty($_REQUEST['idpartida'])){
	if($oIva->set($conn, $_REQUEST['id'], $_REQUEST['idpartida'], $_REQUEST['porc_iva'], $_REQUEST['anio']))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;

#SECCION DE ELIMINAR#
}elseif($accion == 'del'){
	if($oIva->del($conn, $_REQUEST['id']))
		$msj = REG_DEL_OK;
	else
		$msj = ERROR;
}

require ("comun/header.php");
?>

<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<br />
<span class="titulo_maestro">Maestro de Iva </span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />

<fieldset id="buscador">
	<legend>Buscar:</legend>
	<table>
		<tr>
			<td>A&ntilde;o:</td>
			<td><input type="text" name="busca_anio" id="busca_anio" size="6" maxlength="4" onkeypress="return (onlyNumbersCI(event));" onkeyup="buscador()" /></td>
		</tr>
	</table>
</fieldset>
<br />
<br />

<div id="busqueda">
<?
	$cIva = $oIva->get_all($conn);
	if (count($cIva) > 0)
	{
	?>
		<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
			<tr class="cabecera">
				<td>Partidas Presupuestarias</td>
				<td>Porcentaje</td>
				<td>A&ntilde;o</td>
				<td width="5%">&nbsp;</td>
				<td width="5%">&nbsp;</td>
			</tr>
	<?
		foreach($cIva as $iva)
		{
	?>
			<tr class="filas">
				<td><?=$iva->nombre_partida->descripcion?></td>
				<td align="center"><?=$iva->porc_iva?></td>
				<td align="center"><?=$iva->anio?></td>
				<td align="center"><a href="iva.php?accion=del&id=<?=$iva->id?>" title="Eliminar Registro" ><img src="images/eliminar.gif" width="16" height="10" border="0"></a></td>
				<td align="center"><a href="#" onclick="updater('<?=$iva->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
			</tr>
	<?
		}
	?>
		</table>
	<?
	}
	else
		echo "No hay registros en la bd";
?>
</div>
<br />

<div style="height:40px;padding-top:10px;">
<p id="cargando" style="display:none;margin-top:0px;">
  <img alt="Cargando" src="images/loading.gif" /> Cargando...
</p>
</div>


<script type="text/javascript">

/* Metodos utilizados en el buscador */

var t;

function buscador()
{
	clearTimeout(t);
	t = setTimeout("busca('"+$('busca_anio').value+"')", 800);
}

function busca(anio)
{
	if (anio.length < 4 && anio.length > 0)
		return;
		
	var url = 'updater_iva.php';
	var pars = '&anio=' + anio+'&ms='+new Date().getTime();
	var updater = new Ajax.Updater('busqueda', 
		url,
		{
			method: 'get',
			parameters: pars,
			asynchronous:true, 
			evalScripts:true,
			onLoading:function(request){Element.show('cargando')}, 
			onComplete:function(request){Element.hide('cargando')}
		}); 
} 
</script>
<?
$validator->create_message("error_partida", "idpartida", "*");
$validator->create_message("error_iva", "porc_iva", "*");
$validator->create_message("error_anio", "anio", "*");


$validator->print_script();
?>
<? require ("comun/footer.php"); ?>