<?
include('adodb/adodb-exceptions.inc.php');
require ("comun/ini.php");
// Creando el objeto relacion_pp_cp

$oRelacion_conc_pp = new relacion_conc_pp;
$accion = $_REQUEST['accion'];
if($accion == 'Guardar'){
	$msj = $oRelacion_conc_pp->add($conn,
								$_REQUEST['escenarios'],
								$_REQUEST['Presupuesto'], 
								$_REQUEST['concepto']);
}elseif($accion == 'Actualizar'){
	$msj = $oRelacion_conc_pp->add($conn,
								$_REQUEST['escenarios'],
								$_REQUEST['presupuesto'], 
								$_REQUEST['concepto']);
}elseif($accion == 'del'){
	$msj = $oRelacion_conc_pp->del($conn, $_REQUEST['id']);
}


require ("comun/header.php");
//$select->print_script();
?>
<? if(!empty($msj)){ ?><div id="msj"><?=$msj?></div><? } ?>
<br />
<script>var mygrid,i=0</script>
<span class="titulo_maestro">Conceptos por Partidas Program&aacute;ticas</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />
<div>
<fieldset id="buscador">
	<legend>Buscar:</legend>
	<table>
		<tr>
			<td colspan="2">Escenario</td>
		</tr>
		
		<tr>
			<td colspan="2"><?=helpers::superCombo($conn, 'escenarios',0,'busca_escenarios','busca_escenarios')?></td>
		</tr>
		
		<tr>
			<td td colspan="2">Conceptos</td>
		</tr>
		
		<tr>
			<!--<td><input type="text" name="busca_id_cp" id="busca_id_cp" maxlength="10" /></td>-->
			<td colspan="2">
				<?=helpers::superCombo($conn, 
												"SELECT int_cod, conc_nom FROM rrhh.concepto ORDER BY conc_cod",
												0,
												'busca_cp',
												'busca_cp',
												'', 
												'', 
												'int_cod', 
												'conc_nom', 
												'', 
												'80', 
												'', 
												'Seleccione...')?>
			</td>
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
<?
$validator->create_message("error_esc", "escenarios", "Este campo no puede estar vacio");
$validator->create_message("error_parpre", "partidas_presupuestarias", "Este campo no puede estar vacio");
$validator->create_message("error_parpre", "categorias_programaticas", "Este campo no puede estar vacio");
$validator->print_script();
?>
<script type="text/javascript">
var i;
function traePartidasDesdeUpdater(escenario){
	var url = 'updater_selects.php';
	var pars = 'combo=parpreFormulacion&escenario=' + escenario;
	var updater = new Ajax.Updater('cont_partidas', 
		url,
		{
			method: 'get',
			parameters: pars,
			asynchronous:true, 
			evalScripts:true,
		}); 
} 

function traeCategoriasDesdeUpdater(escenario){
	var url = 'updater_selects.php';
	var pars = 'combo=catproPorEsc&escenario=' + escenario;
	var updater = new Ajax.Updater('cont_categorias', 
		url,
		{
			method: 'get',
			parameters: pars,
			asynchronous:true, 
			evalScripts:true,
			onLoading:function(request){Element.show('cargador_categorias_partidas')}, 
			onComplete:function(request){Element.hide('cargador_categorias_partidas')}
		}); 
} 

function validaFilasRepetidas(categoria, partida){
var existe = false;
var i;
for(i=0; i < mygrid.getRowsNum() && !existe; i++)
	{
	if(categoria==mygrid.cells(mygrid.getRowId(i),0).getValue() &&  partida == mygrid.cells(mygrid.getRowId(i),1).getValue())
								existe=true;
	}
return(existe);
}
/*
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
			var pars = 'combo=parprePorEsc&escenario=' + escenario + '&madre='+ madre;
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
} */
function busca(id_escenario, id_concepto, cod_pp, id_pp, pagina){
	var url = 'updater_busca_conc_pp.php';
	var pars = '&id_escenario=' + id_escenario + '&id_concepto=' + id_concepto + '&ms='+new Date().getTime()+ '&pagina='+pagina;
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

Event.observe('busca_escenarios', "change", function () {
	busca($F('busca_escenarios'), $F('busca_cp'),'1'); 
});

Event.observe('busca_cp', "change", function () { 
	 busca($F('busca_escenarios'), $F('busca_cp'),'1'); 
});

/*	function sumaTotal(){
		var disponible = usaFloat($('presupuesto_original').value) + usaFloat($('aumentos').value) - usaFloat($('disminuciones').value);
		$('disponible').value = muestraFloat(disponible);
		
	}*/
	
function Eliminar(){
	mygrid.deleteRow(mygrid.getSelectedId());
} 
function Agregar(){
	if($('partidas_presupuestarias').options[$('partidas_presupuestarias').selectedIndex].value==0){
		alert('Debe Escojer una Partida Presupuestaria');	
	}else if($('categorias_programaticas').options[$('categorias_programaticas').selectedIndex].value==0){
		alert('Debe Escojer una Categoria Programatica');
	}
	else if(validaFilasRepetidas($('categorias_programaticas').options[$('categorias_programaticas').selectedIndex].value, $('partidas_presupuestarias').options[$('partidas_presupuestarias').selectedIndex].value)){
		alert('Esta relacion ya existe');
	}
	else{
		mygrid.addRow(i,$('categorias_programaticas').options[$('categorias_programaticas').selectedIndex].value+','+$('partidas_presupuestarias').options[$('partidas_presupuestarias').selectedIndex].value)
		i++;
	}
}
function Guardar(){
var JsonAux,PresupuestoAux=new Array;
var j;
	mygrid.clearSelection();
	for(j=0;j<mygrid.getRowsNum();j++){
			PresupuestoAux[j] = new Array;
			PresupuestoAux[j][0]= mygrid.cells(mygrid.getRowId(j),0).getValue();
			PresupuestoAux[j][1]= mygrid.cells(mygrid.getRowId(j),1).getValue();
	}
	JsonAux={"Presupuesto":PresupuestoAux};
	$("Presupuesto").value=JsonAux.toJSONString();	
}  
</script>
<?
$validator->create_message("error_esc", "escenarios", "*");
$validator->create_message("error_catpro", "categorias_programaticas", "*");
$validator->create_message("error_parpre", "partidas_presupuestarias", "*");
$validator->print_script();
require ("comun/footer.php");
?>
