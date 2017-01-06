<?
include('adodb/adodb-exceptions.inc.php');
include ("comun/ini.php");
// Creando el objeto categorias_programaticas
$oCategoriasProgramaticas = new categorias_programaticas;
$accion = $_REQUEST['accion'];
if($accion == 'Guardar'){
	$cod_categoria = $_REQUEST['cod1'].$_REQUEST['cod2'].$_REQUEST['cod3'].$_REQUEST['cod4'].$_REQUEST['cod5'];
	$msj = $oCategoriasProgramaticas->add($conn, $cod_categoria,
									$_REQUEST['escenarios'], 
									$_REQUEST['descripcion'], 
									$_REQUEST['os'], 
									$_REQUEST['dps'],
									escenarios::get_ano($conn, $_REQUEST['escenarios']), 
									$_REQUEST['dp'],
									$_REQUEST['status']);
}elseif($accion == 'Actualizar'){
	$cod_categoria = $_REQUEST['cod1'].$_REQUEST['cod2'].$_REQUEST['cod3'].$_REQUEST['cod4'].$_REQUEST['cod5'];
	$msj = $oCategoriasProgramaticas->set($conn, $cod_categoria,
									$_REQUEST['id'],
									$_REQUEST['escenarios'], 
									$_REQUEST['descripcion'], 
									$_REQUEST['os'], 
									$_REQUEST['dps'],
									escenarios::get_ano($conn, $_REQUEST['escenarios']),
									$_REQUEST['dp'],
									$_REQUEST['status']);
}elseif($accion == 'del'){
	$msj = $oCategoriasProgramaticas->del($conn, $_REQUEST['id'], $_REQUEST['id_escenario']);
}
require ("comun/header.php");
if(!empty($msj)){ ?><div id="msj"><?=$msj?></div><? } 
?>
<br />
<span class="titulo_maestro">Maestro de Categor&iacute;as Program&aacute;ticas</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />
<div>
<fieldset id="buscador">
	<legend>Buscar:</legend>
	<table>
		<tr>
			<td>C&oacute;digo</td>
			<td>Escenario</td>
			<td>Descripci&oacute;n</td>
		</tr>
		<tr>
			<td><input style="width:80px" type="text" name="busca_id" id="busca_id" maxlength="10" /></td>
			<td><?=helpers::combo_ue_cp($conn, 'escenarios','','','id','busca_escenarios','busca_escenarios')?></td>
			<td><input type="text" name="busca_descripcion" id="busca_descripcion" /></td>
		</tr>
	</table>
</fieldset>
</div>
<br />
<div style="margin-bottom:10px" id="busqueda"><div>
<br />
<div style="height:40px;padding-top:10px;">
<p id="cargando" style="display:none;margin-top:0px;">
  <img alt="Cargando" src="images/loading.gif" /> Cargando...
</p>
</div>
<script>
var t;
var muestraSect;
var muestraProg;
function auto_focus(campo,next,num){
	
	if(campo.value.length == num){
		if(campo.name!='cod5'){	
			next.disabled = false;
			next.focus();
		}
	}
	validar_codigo();
}

function busca(id, id_escenario, descripcion, pagina){
	var url = 'updater_busca_cp.php';
	var pars = '&id='+ id + '&id_escenario=' + id_escenario + '&descripcion=' + descripcion + '&pagina=' + pagina;
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


//ESTA FUNCION SE UTILIZA PARA VALIDAR CUAL CONTROL VA A SER VISIBLE
function validar_codigo()
	{
		var cadena;
		cadena = $('cod1').value + $('cod2').value +$('cod3').value + $('cod4').value + $('cod5').value
		if(cadena.length==10){
		//alert('sector: '+muestraSect + 'programa: '+muestraProg);
			if(cadena.substr(2,8) == '00000000'){
				if(muestraSect != 1){
					Effect.toggle('objSect', 'blind');
					muestraSect = 1;
				}
				if(muestraProg == 1){
					Effect.toggle('descProg', 'blind');
					muestraSect = 0;
				}
			}else if(cadena.substr(4,6) == '000000'){
				if(muestraSect == 1){
					Effect.toggle('objSect', 'blind');
					muestraSect = 0;
				}
				$('txtDescripcion').innerHTML = 'Descripcion del Programa: ';
				if(muestraProg != 1){
					Effect.toggle('descProg', 'blind');
					muestraProg = 1;
				}
			}else if(cadena.substr(6,4) == '0000'){
				if(muestraSect == 1){
					Effect.toggle('objSect', 'blind');
					muestraSect = 0;
				}
				$('txtDescripcion').innerHTML = 'Descripcion del Sub-Programa: ';
				if(muestraProg != 1){
					Effect.toggle('descProg', 'blind');
					muestraProg = 1;
				}
			}else if(cadena.substr(8,2) == '00'){
				if(muestraSect == 1){
					Effect.toggle('objSect', 'blind');
					muestraSect = 0;
				}
				$('txtDescripcion').innerHTML = 'Descripcion del Proyecto: ';
				if(muestraProg != 1){
					Effect.toggle('descProg', 'blind');
					muestraProg = 1;
				}
			}else if(cadena.substr(8,2) != '00'){
				if(muestraSect == 1){
					Effect.toggle('objSect', 'blind');
					muestraSect = 0;
				}
				if(muestraProg == 1){
					Effect.toggle('descProg', 'blind');
					muestraProg = 0;
				}
			}
		/*} else {
			if(cadena.substr(6,4) != '0000'){
				if(muestraSect = 1){
					Effect.toggle('objSect', 'blind');
					muestraSect = 0;
				}
				if(muestraProg == 1){
					Effect.toggle('descProg', 'blind');
					muestraProg = 0;
				}
			}*/
		}
	}

Event.observe('busca_id', "keyup", function () {	
  if ($F('busca_id').length == 10 || $F('busca_id') == '') 
	   busca($F('busca_id'), $F('busca_escenarios'), $F('busca_descripcion'), '1'); 
});
Event.observe('busca_escenarios', "change", function () { 
	busca($F('busca_id'), $F('busca_escenarios'), $F('busca_descripcion'), '1'); 
});
Event.observe('busca_descripcion', "keyup", function () {
  clearTimeout(t);
	t = setTimeout("busca($F('busca_id'), $F('busca_escenarios'), $F('busca_descripcion'), '1')", 1500); 
});
</script>
<?
$validator->create_message("error_cod", "cod1", "*");
$validator->create_message("error_cod", "cod2", "*");
$validator->create_message("error_cod", "cod3", "*");
$validator->create_message("error_cod", "cod4", "*");
$validator->create_message("error_cod", "cod5", "*");
$validator->create_message("error_esc", "escenarios", "*");
$validator->create_message("error_desc", "descripcion", "*");
$validator->print_script();
require ("comun/footer.php");
?>
