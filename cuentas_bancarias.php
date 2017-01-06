<? require ("comun/ini.php");
// Creando el objeto motivo
$ocuentas_bancarias = new cuentas_bancarias;
$accion = $_REQUEST['accion'];

#SECCION DE GUARDAR#
if($accion == 'Guardar' and !empty($_REQUEST['nro_cuenta'])){
	if($ocuentas_bancarias->add($conn, $_REQUEST['nro_cuenta'], $_REQUEST['id_banco'], $_REQUEST['id_tipo_cuenta'], $_REQUEST['id_clasificacion_cuenta'], $_REQUEST['id_plan_cuenta'], 
				$_REQUEST['id_fuente_financiamiento'], 0, guardafloat($_REQUEST['saldo_inicial']), guardafloat($_REQUEST['debitos']), 
				 guardafloat($_REQUEST['creditos'])))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;

#SECCION DE ACTULIZAR#
}elseif($accion == 'Actualizar' and !empty($_REQUEST['nro_cuenta'])){
	if ($ocuentas_bancarias->set($conn, $_REQUEST['id'], $_REQUEST['nro_cuenta'], $_REQUEST['id_banco'], $_REQUEST['id_tipo_cuenta'], $_REQUEST['id_clasificacion_cuenta'], $_REQUEST['id_plan_cuenta'], 
				 $_REQUEST['id_fuente_financiamiento'], guardafloat($_REQUEST['saldo_inicial']), guardafloat($_REQUEST['debitos']), 
				 guardafloat($_REQUEST['creditos'])))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;

#SECCION DE ELIMINAR#
}elseif($accion == 'del'){
	if($ocuentas_bancarias->del($conn, $_REQUEST['id']))
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

$ccuentas_bancarias=$ocuentas_bancarias->get_all($conn, $start_record,$page_size);
$pag=new paginator($ocuentas_bancarias->total,$page_size, self($_SERVER['SCRIPT_NAME']));
$i=$pag->get_total_pages();
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<br />
<span class="titulo_maestro">Maestro de Cuentas Bancarias </span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />

<fieldset id="buscador">
	<legend>Buscar:</legend>
	<table>
		<tr>
			<td>Nro. de Cuenta:</td>
			<td width="130"><input type="text" name="busca_nro_cta" id="busca_nro_cta" onkeypress="buscador()" /></td>
			<td>Banco:</td>
			<td>
				<?=helpers::superComboSQL($conn,
													'',
													'',
													'busca_bancos',
													'busca_bancos',
													'',
													'buscador()',
													'id',
													'descripcion',
													false,
													'',
													"SELECT id, descripcion FROM public.banco ORDER BY descripcion")?>
			</td>
		</tr>
	</table>
</fieldset>
<br />

<div id="busqueda">
<?
if(is_array($ccuentas_bancarias))
{
?>
	<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
		<tr class="cabecera"> 
			<td>Nro. de Cuenta</td>
			<td>Banco</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
<? 
	foreach($ccuentas_bancarias as $cb) 
	{ 
?> 
		<tr class="filas"> 
			<td><?=$cb->nro_cuenta?></td>
			<td><?=$cb->banco->descripcion?></td>
			<td align="center"><a href="cuentas_bancarias.php?accion=del&id=<?=$cb->id?>" title="Modificar ó Actualizar Registro" ><img src="images/eliminar.gif" width="16" height="10" border="0"></a></td>
			<td align="center"><a href="#" onclick="updater('<?=$cb->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
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
<br />
<div style="height:40px;padding-top:10px;">
<p id="cargando" style="display:none;margin-top:0px;">
  <img alt="Cargando" src="images/loading.gif" /> Cargando...
</p>
</div>

<script type="text/javascript">

	var t;
	
	function buscador()
	{
		clearTimeout(t);
		t = setTimeout("busca('" + $('busca_nro_cta').value + "'," + $('busca_bancos').value + ",1)", 800);
	}
	
	function busca(nro_cta, banco, pagina)
	{
		var url = 'updater_busca_cuentas_bancarias.php';
		var pars = 'nro_cuenta='+nro_cta+'&banco='+banco+'&pagina='+pagina+'&ms='+new Date().getTime();
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
	
	function traeCuentasContables(){
	
	var url = 'buscar_cuentas.php';
	var pars = 'id_cuenta='+$('id_plan_cuenta').value+'&ms='+new Date().getTime();
		
	var Request = new Ajax.Request(
		url,
		{
			method: 'get',
			parameters: pars,
			onLoading:function(request){}, 
			onComplete:function(request){
				
				Dialog.closeInfo();
				Dialog.alert(request.responseText, {windowParameters: {width:600, height:400, 
								showEffect:Element.show,hideEffect:Element.hide,
								showEffectOptions: { duration: 1}, hideEffectOptions: { duration:1 }
								
								}});
				
				}
			}
	);     	   
}

function traeCuentasContablesDesc(){
	
	var url = 'buscar_cuentas.php';
	var pars = 'descripcion='+$('search_descrip').value+'&id_cuenta='+$('id_plan_cuenta').value+'&ms='+new Date().getTime();
		
	var Request = new Ajax.Request(
		url,
		{
			method: 'get',
			parameters: pars,
			onLoading:function(request){}, 
			onComplete:function(request){
				
				Dialog.closeInfo();
				Dialog.alert(request.responseText, {windowParameters: {width:600, height:400, 
								showEffect:Element.show,hideEffect:Element.hide,
								showEffectOptions: { duration: 1}, hideEffectOptions: { duration:1 }
								
								}});
				
				}
			}
	);     	   
}

	function selDocumento(id, nombre){
	
	$('txtCuentaContable').value = nombre;
	$('id_plan_cuenta').value = id;
	Dialog.okCallback();

}

var t;

	function busca_popup()
{
	clearTimeout(t);
	t = setTimeout('traeCuentasContablesDesc()', 800);
}
</script>
<?
$validator->create_message("error_banco", "id_banco", "*");
$validator->create_message("error_nro_cuenta", "nro_cuenta", "*");
$validator->create_message("error_tipo_cuenta", "id_tipo_cuenta", "*");
$validator->create_message("error_clasificacion_cuenta", "id_clasificacion_cuenta", "*");
$validator->create_message("error_desc", "id_plan_cuenta", "*");
$validator->create_message("error_fuente_financiamiento", "id_fuente_financiamiento", "*");
$validator->create_message("error_saldo", "saldo_inicial", "*");
$validator->create_message("error_creditos", "creditos", "*");
$validator->create_message("error_debitos", "debitos", "*");
$validator->print_script();
?>
<? require ("comun/footer.php"); ?>
