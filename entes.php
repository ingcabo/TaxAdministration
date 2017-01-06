<?
require ("comun/ini.php");
$oEntes = new entes;
$del = $_REQUEST['del'];
//var_dump($_REQUEST);
//echo 'aqui '.$accion;
$accion = $_REQUEST['accion'];
if($accion == 'Guardar'){
	$oEntes->add($conn, 
						$_REQUEST['nombre'],
						$_REQUEST['siglas'], 
						$_REQUEST['direccion'], 
						$_REQUEST['datos_crea'], 
						$_REQUEST['actividad'] 
						);
		
}elseif($accion == 'Actualizar'){
	$oEntes->set($conn, $_REQUEST['id'], 
						$_REQUEST['nombre'],
						$_REQUEST['siglas'], 
						$_REQUEST['direccion'], 
						$_REQUEST['datos_crea'], 
						$_REQUEST['actividad'] 
						);
		
}elseif($accion == 'del'){
	$oEntes->del($conn, $_REQUEST['id']);
		
}
#LLENO LA VARIABLE CON EL MENSAJE DE LA OPERACION REALIZADA#
	$msj = $oEntes->msj; 
	
$cEntes=$oEntes->get_all($conn);
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<br />
<span class="titulo_maestro">Maestro de Entes</span>
<div id="formulario">
<a href="#" onClick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />
<div>
<fieldset id="buscador">
	<legend>Buscar:</legend>
	<table>
		<tr>
			<td>Nombre:</td>
			<td><input type="text" name="busca_nombre" id="busca_nombre" /></td>
		</tr>
		<tr>
			<td>Siglas:</td>
			<td><input type="text" name="busca_siglas" id="busca_siglas" /></td>
		</tr>
	</table>

</fieldset></div>
<br />
<div style="margin-bottom:10px" id="busqueda"></div>
<br />
<div style="height:40px;padding-top:10px;">
<p id="cargando" style="display:none;margin-top:0px;">
  <img alt="Cargando" src="images/loading.gif" /> Cargando...
</p>
</div>
<script type="text/javascript">
var t;

function busca(nombre, siglas, pagina){
	var url = 'updater_busca_entes.php';
	var pars = '&nombre='+ nombre + '&siglas=' + siglas + '&pagina=' + pagina;
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

//Event.observe(window, "keypress", function (e) { if(e.keyCode == Event.KEY_RETURN){ validate(); } });

Event.observe("busca_nombre", "keyup", function () {
	   clearTimeout(t); 
	   t = setTimeout("busca($F('busca_nombre'), $F('busca_siglas'),1)",1500); 
});

Event.observe("busca_siglas", "keyup", function () {
  clearTimeout(t); 
	t = setTimeout("busca($F('busca_nombre'), $F('busca_siglas'),1)", 1500); 
});
</script>
<?
$validator->create_message("error_nombre", "nombre", "*");
$validator->create_message("error_direccion", "direccion", "*");
$validator->create_message("error_creacion", "datos_crea", "*");
$validator->create_message("error_actividad", "actividad", "*");
$validator->print_script();
require ("comun/footer.php");
?>
