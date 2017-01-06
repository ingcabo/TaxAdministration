<?
require ("comun/ini.php");
// Creando el objeto retenciones y adiciones
$oRA = new retenciones_adiciones;

$accion = $_REQUEST['accion'];
if($accion == 'Guardar'){
	$msj = $oRA->add($conn, 
							$_POST['id_nuevo'], 
							$_POST['abreviatura'], 
							$_POST['descripcion'], 
							$_POST['cta_contable'], 
							$_POST['condicion'], 
							guardafloat($_POST['porcentaje']), 
							guardafloat($_POST['sustraendo']), 
							$_POST['fijavariable'], 
							$_POST['tipo_persona'], 
							$_POST['cta_presup'],
							$_POST['porcret'],
							$_POST['es_iva'],
							$_POST['expresion']);

	if ($msj === true)
		$msj = REG_ADD_OK;
	else
		$msj = CODIGO_YA_EXISTE;
}elseif($accion == 'Actualizar'){
	$msj = $oRA->set($conn, 
							$_POST['id_nuevo'],
							$_POST['id'],
							$_POST['abreviatura'], 
							$_POST['descripcion'], 
							$_POST['cta_contable'], 
							$_POST['condicion'], 
							guardafloat($_POST['porcentaje']), 
							guardafloat($_POST['sustraendo']), 
							$_POST['fijavariable'], 
							$_POST['tipo_persona'], 
							$_POST['cta_presup'],
							$_POST['porcret'],
							$_POST['es_iva'],
							$_POST['expresion']);
	if ($msj === true)
		$msj = REG_SET_OK;
	/*else
		$msj = ERROR;*/
}elseif($accion == 'del'){
	if($oRA->del($conn, $_REQUEST['id']))
		$msj = REG_DEL_OK;
	else
		$msj = ERROR;
}

require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<br />
<span class="titulo_maestro">Maestro de Retenciones<!-- y Adiciones--></span>
<div id="formulario">
<a href="#" onclick="updater(0)">Agregar Nuevo Registro</a>
</div>
<br />
<div id="panelbuscador">
	<fieldset id="buscador">
		<legend>Buscar:</legend>
		<table>
			<tr>
				<td>C&oacute;digo</td>
				<td>Descripci&oacute;n</td>
			</tr>
			<tr>
				<td><input style="width:80px" type="text" name="busca_id" id="busca_id" /></td>
				<td><input type="text" name="busca_descripcion" id="busca_descripcion" /></td>
			</tr>
		</table>
	</fieldset>
</div>
<br />

<div style="margin-bottom:10px" id="busqueda">
	<?
	$cReten = $oRA->getALL($conn);
	if (count($cReten) > 0)
	{
	?>
		<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
			<tr class="cabecera"> 
				<td>C&oacute;digo</td>
				<td>Descripci&oacute;n de la unidad</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
	<?
		foreach($cReten as $ret)
		{
	?>
			<tr class="filas">
				<td><?=$ret->id?></td>
				<td><?=$ret->descripcion?></td>
				<td align="center"><a href="?accion=del&id=<?=$ret->id?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
				<td align="center"><a href="#" onclick="updater('<?=$ret->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
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
<div>
<br />

<div style="height:40px;padding-top:10px;">
	<p id="cargando" style="display:none;margin-top:0px;">
	  <img alt="Cargando" src="images/loading.gif" /> Cargando...
	</p>
</div>
<script>
function busca(id, descripcion){
	var url = 'updater_busca_ra.php';
	var pars = '&id='+ id + '&descripcion=' + descripcion;
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

Event.observe('busca_id', 'keyup', function () { 
	busca($F('busca_id'), $F('busca_descripcion')); 
});
Event.observe('busca_descripcion', 'keyup', function () { 
	busca($F('busca_id'), $F('busca_descripcion')); 
});

	function traeCuentasContables(){
	
	var url = 'buscar_cuentas.php';
	var pars = 'id_cuenta='+$('cta_contable').value+'&tipo=6&ms='+new Date().getTime();
		
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
	var pars = 'descripcion='+$('search_descrip').value+'&id_cuenta='+$('cta_contable').value+'&tipo=6&ms='+new Date().getTime();
		
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
	$('cta_contable').value = id;
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
$validator->create_message("error_cod", "id_nuevo", "*");
$validator->create_message("error_abrv", "abreviatura", "*");
$validator->create_message("error_desc", "descripcion", "*");
$validator->create_message("error_cc", "cta_contable", "*");
$validator->create_message("error_cond", "condicion", "*");
$validator->create_message("error_porcret", "porcret", "*");
$validator->create_message("error_porc", "porcentaje", "*");
$validator->create_message("error_sust", "sustraendo", "*");
$validator->create_message("error_exp", "expresion", "*");
//$validator->create_message("error_fj", "fijavariable", "*");
//$validator->create_message("error_per", "tipo_persona", "*");
//$validator->create_message("error_cp", "cta_presup", "*"); 
$validator->print_script();
require ("comun/footer.php");
?>
