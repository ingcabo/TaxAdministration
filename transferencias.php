<?
require ("comun/ini.php");
$oTransferencias = new transferencias;
$del = $_REQUEST['del'];
//var_dump($_REQUEST);
//echo 'aqui '.$accion;
$accion = $_REQUEST['accion'];
//echo $accion;
if($accion == 'Guardar'){
//var_dump($_REQUEST);
//die("aqui ".$_REQUEST['privPublic']);
	$oTransferencias->add($conn, 
						$_REQUEST['privPublic'],
						$_REQUEST['chbEnte'], 
						$_REQUEST['tipoEnte'], 
						$_REQUEST['entes'], 
						$_REQUEST['organismo'],
						guardaFloat($_REQUEST['asignacion']),
						$_REQUEST['responsable'],
						$_REQUEST['observaciones'],
						$_REQUEST['escenarios'],
						$_REQUEST['categorias_programaticas'],
						$_REQUEST['partidas_presupuestarias']);
						
}elseif($accion == 'Actualizar'){
	$oTransferencias->set($conn, $_REQUEST['id'], 
						$_REQUEST['privPublic'],
						$_REQUEST['chbEnte'], 
						$_REQUEST['tipoEnte'], 
						$_REQUEST['entes'], 
						$_REQUEST['organismo'],
						guardaFloat($_REQUEST['asignacion']),
						$_REQUEST['responsable'],
						$_REQUEST['observaciones'],
						$_REQUEST['escenarios'],
						$_REQUEST['categorias_programaticas'],
						$_REQUEST['partidas_presupuestarias']);
		
}elseif($accion == 'del'){
	$oTransferencias->del($conn, $_REQUEST['id']);
		
}
#LLENO LA VARIABLE CON EL MENSAJE DE LA OPERACION REALIZADA#
	$msj = $oTransferencias->msj; 
	
$cEntes=$oTransferencias->get_all($conn);
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<br />
<span class="titulo_maestro">Maestro de Transferencias</span>
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
			<td><select name="busca_privPublic" id="busca_privPublic">
					<option value="0">Seleccione</option>
					<option value="1">Privado</option>
					<option value="2">Publico</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Organismo:</td>
			<td><input type="text" name="busca_organismo" id="busca_organismo" /></td>
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

function busca(privPublic, organismo, pagina){
	var url = 'updater_busca_transferencias.php';
	var pars = '&privPublic='+ privPublic + '&organismo=' + organismo + '&pagina=' + pagina;
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

Event.observe("busca_privPublic", "change", function () {
	   busca($F('busca_privPublic'), $F('busca_organismo'),1); 
});

Event.observe("busca_organismo", "keyup", function () {
  clearTimeout(t); 
	t = setTimeout("busca($F('busca_privPublic'), $F('busca_organismo'),1)", 1500); 
});

function usaCampos(valor){
 //alert(valor);
 if(valor==2){
	$$('.tipoEnte').each(function(a){a.disabled = false;});
 }else
	$$('.tipoEnte').each(function(a){a.disabled = true;});
}

function usaCampos2(objeto){
//alert(objeto.checked);
 if(objeto.checked==true){
	$$('.publico').each(function(a){a.disabled = false;});
 }else
	$$('.publico').each(function(a){a.disabled = true;});
}

function guardar(valor){
	//alert(valor);
	$('accion').value = valor;
	$$('.publico').each(function(a){a.disabled = false;});
	$$('.tipoEnte').each(function(a){a.disabled = false;});
	validate();
	$$('.publico').each(function(a){a.disabled = true;});
	$$('.tipoEnte').each(function(a){a.disabled = true;});
}

function usaNombre(){
	//alert(document.getElementById('entes').options.text);
	//alert($('entes').options[document.getElementById('entes').value].text);
	$('organismo').value = $('entes').options[document.getElementById('entes').value].text;
}

function traeCategoriasDesdeUpdater(escenario){
	var url = 'updater_selects.php';
	var pars = 'combo=catproPorEsc&escenario=' + escenario + '&onchange=buscaTipo(this.value,'+escenario+')';
	var updater = new Ajax.Updater('cont_categorias', 
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

function buscaTipo (idCategoria, escenario){
	//alert(idCategoria);
	
	var url = 'json.php';
	var pars = 'op=tipoCategoria&idCat=' + idCategoria + '&escenario='+escenario;
	var myAjax = new Ajax.Request(
		url, 
		{
			method: 'get', 
			parameters: pars,
			onComplete: function(peticion){
				//var jsonData = eval('(' + peticion.responseText + ')');
				var tipo = peticion.responseText;
				//var jsonData = peticion.resposeText;
				traeParPreDesdeUpdater(escenario,tipo);
			}
		});
	
}	

function traeParPreDesdeUpdater(escenario, madre){	
	var url = 'updater_selects.php';
			var pars = 'combo=parprePorEsc&escenario=' + escenario + '&madre='+ madre +'&generica=407&categoria='+$('categorias_programaticas').value;
			var updater = new Ajax.Updater('cont_partidas' , 
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
$validator->create_message("error_privPublic", "privPublic", "*");
$validator->create_message("error_organismo", "organismo", "*");
$validator->create_message("error_asignacion", "asignacion", "*");
$validator->create_message("error_escenario", "escenarios", "*");
$validator->create_message("error_catpro", "categorias_programaticas", "*");
$validator->create_message("error_parpre", "partidas_presupuestarias", "*");
$validator->print_script();
require ("comun/footer.php");
?>
