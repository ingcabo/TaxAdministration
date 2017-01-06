<?
require ("comun/ini.php");
// Creando el objeto inspeccion
$oinspeccion = new inspeccion;
$accion = $_REQUEST['accion'];
if (!empty($_REQUEST['res_inspeccion']))
{
	$status = '0';
}
if($accion == 'Guardar'){
	if($oinspeccion->add($conn, $_REQUEST['cod_inspector'], $_REQUEST['res_inspeccion'], $_REQUEST['patente'], $_REQUEST['status']))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
		
}elseif($accion == 'Actualizar'){
	if($oinspeccion->set($conn, $_REQUEST['cod_asignacion'], $_REQUEST['res_inspeccion'], $status))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}elseif($accion == 'del'){
	if($oinspeccion->del($conn, $_REQUEST['cod_asignacion']))
		$msj = REG_DEL_OK;
	else
		$msj = ERROR;
		
}
//Seccion paginador
$page_size = 25;
if ($_GET['pg'])
	$start_record=($_GET['pg'] * $page_size) - $page_size;
else
	$start_record=0;

$cinspeccion=$oinspeccion->get_all($conn, $start_record,$page_size);
$pag=new paginator($oinspeccion->total,$page_size, self($_SERVER['SCRIPT_NAME']));
$i=$pag->get_total_pages();
require ("comun/header.php");

	//	$validator->create_message("id_title", "email", "(Inv&iacute;lido)", 3); //CREO EL MENSAJE DE VALIDACION Y EL OBJETO QUE VOY A CONTROLAR
	//	$validator->print_script();  //IMPRIMO EL SCRIPT
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:none;"><?=$msj?></div><? echo "<br>"; } ?>
<br />
<span class="titulo_maestro">Resultado Inspecci&oacute;n </span>

<div id="formulario">
</div>
<br />

<table style=" margin-left: auto; margin-right: auto; font-size:10px; " align="center" border="0">
  <tr>
    <td width="69">Buscar Seg&uacute;n:</td>
    <td width="62">
		<select name="tipo" id="tipo">
			<option value="">Seleccione...</option>
			<option value="cedula">C&eacute;dula</option>
			<option value="patent">Patente</option>
		</select>
	</td>
    <td width="60"><input type="text" name="valor" id="valor" value="" size="15"></td>
    <td width="86"><input type="button" value="Buscar" onClick="gettpl($('tipo').value, $('valor').value)"></td>
  </tr>
</table>
<br>
<div id="resultado"></div>

<br />
<div style="height:40px;padding-top:10px;">
<p id="cargando" style="display:none;margin-top:0px;">
  <img alt="Cargando" src="images/loading.gif" /> Cargando...
</p>
</div>

<!--<div id="pars"></div>-->
<? require ("comun/footer.php"); ?>


<script type="text/javascript">
		
	function gettpl(tipo, valor)
	{
		var url = 'resultado_inspeccion.php';
		var pars = 'tipo='+tipo+'&valor='+valor;

		var myAjax = new Ajax.Updater(
			'resultado', 
			url, 
			{
				method: 'get', 
				parameters: pars
			});
		
	}  
</script>



