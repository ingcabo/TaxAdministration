<?
include('adodb/adodb-exceptions.inc.php');
require ("comun/ini.php");
// Creando el objeto obras
$oObras = new obras;
$accion = $_REQUEST['accion'];
$id = $_REQUEST['id'];
if($accion == 'Guardar'){
	 $oObras->add($conn,	$_REQUEST['obra_cod'],
									$_REQUEST['escenario'],
									$_REQUEST['unidad_ejecutora'],
									$_REQUEST['parroquias'],
									$_REQUEST['situaciones'],
									$_REQUEST['financiamiento'],
									$_REQUEST['descripcion'],
									$_REQUEST['denominacion'],
									guardafloat($_REQUEST['ctotal']),
									guardafloat($_REQUEST['caa']),
									guardafloat($_REQUEST['eaa']),
									guardafloat($_REQUEST['epre']),
									$_REQUEST['inicio'],
									$_REQUEST['culminacion'],
									guardafloat($_REQUEST['cav']),
									guardafloat($_REQUEST['eav']),
									guardafloat($_REQUEST['epos']),
									$_REQUEST['ano'],
									$_REQUEST['responsable'],
									$_REQUEST['obra']);
}elseif($accion == 'Actualizar'){
	 $oObras->set($conn, 	$id, 	
									$_REQUEST['obra_cod'],
									$_REQUEST['escenario'],
									$_REQUEST['unidad_ejecutora'],
									$_REQUEST['parroquias'],
									$_REQUEST['situaciones'],
									$_REQUEST['financiamiento'],
									$_REQUEST['descripcion'],
									$_REQUEST['denominacion'],
									guardafloat($_REQUEST['ctotal']),
									guardafloat($_REQUEST['caa']),
									guardafloat($_REQUEST['eaa']),
									guardafloat($_REQUEST['epre']),
									$_REQUEST['inicio'],
									$_REQUEST['culminacion'],
									guardafloat($_REQUEST['cav']),
									guardafloat($_REQUEST['eav']),
									guardafloat($_REQUEST['epos']),
									$_REQUEST['ano'],
									$_REQUEST['responsable'],
									$_REQUEST['obra']);
}elseif($accion == 'del'){
	 $oObras->del($conn, $id);
}

$msj = $oObras->msj;
//Seccion paginador
$page_size = 25;
if ($_GET['pg'])
	$start_record=($_GET['pg'] * $page_size) - $page_size;
else
	$start_record=0;
$cObras=$oObras->get_all($conn, $start_record,$page_size);
if(!empty($id)){
	$cRelacionObras=$oObras->get_relaciones($conn, $id, $escEnEje);
	$boton="Actualizar";
}else
	$boton = "Guardar";
$pag=new paginator($oObras->total,$page_size, self($_SERVER['PHP_SELF']));
$i=$pag->get_total_pages();
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<br />
<span class="titulo_maestro">Maestro de obras</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false">Agregar Nuevo Registro</a>
</div>
<br />
<div id="contenidobuscador">
<fieldset id="buscador">
	<legend>Buscar:</legend>
	<table>
		<tr>
			<td colspan="3">Unidad Ejecutora</td>
		</tr>
		<tr>
			<td colspan="3"><?=helpers::combo_ue_cp($conn,'busca_ue','','','','','','',
			"SELECT DISTINCT id, id || ' - ' || descripcion AS descripcion FROM unidades_ejecutoras ORDER BY id")?></td>
		</tr>
		<tr>
			<td colspan="3"><!--Financiadora:-->Fuente de Financiamiento:</td>
		</tr>
		<tr>
			<td colspan="3"><?=helpers::combo_ue_cp($conn,'busca_financiadora','','','','','','',
			"SELECT DISTINCT id, descripcion FROM financiamiento")?></td>
		</tr>
		<tr>
			<td>N&ordm; de Obra:</td>
			<td colspan="2">
				<table>
					<tr>
						<td style="width:125px">Desde</td>
						<td>Hasta</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td><input style="width:100px" type="text" name="busca_id" id="busca_id" /></td>
			<td colspan="2">
				<table>
					<tr>
						<td>
							<input style="width:100px"  type="text" name="busca_fecha_desde" id="busca_fecha_desde" 
							onchange="validafecha(this);"/>
						</td>
						<td>
							<a href="#" id="boton_busca_fecha_desde" onclick="return false;">
								<img border="0" alt="Seleccionar Fecha" src="images/calendarA.png" width="20" height="20" />
							</a>  
						<script type="text/javascript">
							new Zapatec.Calendar.setup({
								firstDay          : 1,
								weekNumbers       : true,
								showOthers        : false,
								showsTime         : false,
								timeFormat        : "24",
								step              : 2,
								range             : [1900.01, 2999.12],
								electric          : false,
								singleClick       : true,
								inputField        : "busca_fecha_desde",
								button            : "boton_busca_fecha_desde",
								ifFormat          : "%d/%m/%Y",
								daFormat          : "%Y/%m/%d",
								align             : "Br"
							 });
						</script>
						</td>
						
						<td>
							<input style="width:100px" type="text" name="busca_fecha_hasta" id="busca_fecha_hasta"
							onchange="validafecha(this); "/>
						</td>
						<td>
							<a href="#" id="boton_busca_fecha_hasta" onclick="return false;">
								<img border="0"  alt="Seleccionar Fecha" src="images/calendarA.png" width="20" height="20" />
							</a>  
						<script type="text/javascript">
							new Zapatec.Calendar.setup({
								firstDay          : 1,
								weekNumbers       : true,
								showOthers        : false,
								showsTime         : false,
								timeFormat        : "24",
								step              : 2,
								range             : [1900.01, 2999.12],
								electric          : false,
								singleClick       : true,
								inputField        : "busca_fecha_hasta",
								button            : "boton_busca_fecha_hasta",
								ifFormat          : "%d/%m/%Y",
								daFormat          : "%Y/%m/%d",
								align             : "Br"
							 });
						</script>
						</td>
					</tr>
					</table>
			</td>
		</tr>
	</table>
</fieldset>
</div>
<br />
<div style="margin-bottom:10px" id="busqueda"></div>
<div style="height:40px;padding-top:10px;">
<p id="cargando" style="display:none;margin-top:0px;">
  <img alt="Cargando" src="images/loading.gif" /> Cargando...
</p>
</div>

<?
$validator->create_message("error_codigo", "cod_obra", "*"); 
$validator->create_message("error_escenarios", "escenario", "*");
$validator->create_message("error_unidad_ejecutora", "unidad_ejecutora", "*");
$validator->create_message("error_parroquias", "parroquias", "*");
$validator->create_message("error_financiamiento", "financiamiento", "*");
$validator->create_message("error_ano", "ano", "*");
$validator->create_message("error_descripcion", "descripcion", "*");
$validator->create_message("error_denominacion", "denominacion", "*");
$validator->create_message("error_responsable", "responsable", "*");
$validator->create_message("error_inicio", "inicio", "*");
$validator->create_message("error_culminacion", "culminacion", "*");
$validator->create_message("error_situaciones", "situaciones", "*");
$validator->print_script();
?>
<script language="javascript" type="text/javascript">

i=0;

	//MANEJO DE LAS CATEGORIAS PROGRAMATICAS//
function AgregarO(){
	//alert($('unidad_ejecutora').value);
	
	if ($('unidad_ejecutora').value =="0"){
		
		alert("Primero debe Seleccionar una Unidad Ejecutora.");
		return;
		
	}else if($('categorias_programaticas').value=="0"){
	
		alert("Primero debe Seleccionar una Categoria Programatica.");
		return;
		
	}else if($('partidas_presupuestarias').value=="0"){
		
		alert("Primero debe Seleccionar una Partida Presupuestaria.");
		return;
		
	}else if($('montoip').value=="" || parseFloat(usaFloat($('montoip').value))<=0){
		
		alert("Primero debe colocar el monto de la Imputacion Presupuestaria.");
		return;
	
	}else if(parseFloat($('disponible').value) < usaFloat($('montoip').value)){
		/*alert('disponible: '+ parseFloat($('disponible').value));
		alert('imputado: '+usaFloat($('montoip').value));*/
		alert("El monto disponible en la partida es menor al requerido");
		$('montoip').value='0,00';
		return;	
	
	}else{
		
		for(j=0;j<i;j++){
		
			if(mygrido.getRowId(j)!=undefined){
				
				if (mygrido.cells(mygrido.getRowId(j),'0').getValue() == $('categorias_programaticas').value && mygrido.cells(mygrido.getRowId(j),'1').getValue() == $('partidas_presupuestarias').value){
						
					alert('Esta partida ya ha sido seleccionada, por favor seleccione otra partida');
					return false;

				}
			}
		}
		
		/*mygridco.getCombo(0).put(JsonData[j]['id_categoria_programatica'],JsonData[j]['categoria_programatica']);
		mygridco.getCombo(1).put(JsonData[j]['id_partida_presupuestaria'],JsonData[j]['partida_presupuestaria']);*/
		mygrido.addRow($('idParCat').value,$('categorias_programaticas').value+","+$('partidas_presupuestarias').value+","+usaFloat($('montoip').value));
		i++;
		sumaTotal();
		$('montoip').value='0,00';
	}
}

function EliminarO(){
	mygrido.deleteRow(mygrido.getSelectedId());
	sumaTotal();
	
}


function traeCategoriasProgramaticas(ue){
	
	var url = 'updater_selects.php';
	var pars = 'combo=categorias_programaticas&ue=' + ue +'&ms='+new Date().getTime();
		
	var updater = new Ajax.Updater('divcombocat', 
		url,
		{
			method: 'get',
			parameters: pars,
			asynchronous:true, 
			evalScripts:true,
			onLoading:function(request){Element.show('cargador_categorias')}, 
			onComplete:function(request){Element.hide('cargador_categorias')}
		});
}

function traePartidasPresupuestarias(cp){
	
	var url = 'updater_selects.php';
	var pars = 'combo=partidas_presupuestarias&cp=' + cp +'&ms='+new Date().getTime() + '&idp=404';
		
	var updater = new Ajax.Updater('divcombopp', 
		url,
		{
			method: 'get',
			parameters: pars,
			asynchronous:true, 
			evalScripts:true,
			onLoading:function(request){Element.show('cargador_partidas')}, 
			onComplete:function(request){Element.hide('cargador_partidas')}
		});
} 

	function traerDisponiblePartidas(cp, pp){
	
	var esc = $('escenario').value;
	var url = 'json.php';
	var pars = 'op=parcat&cp=' + cp +'&pp='+ pp + '&escenario=' + esc;
			
	var myAjax = new Ajax.Request(
				url, 
				{
				method: 'get', 
				parameters: pars,
				onComplete: function(peticion){
					var jsonData = eval('(' + peticion.responseText + ')');
					if (jsonData == undefined) { return }
					$('disponible').value = jsonData.disponible;
					$('idParCat').value = jsonData.id;
				}
				}
			);
}

function sumaTotal(){
	var totalPartidas = 0;
	for(j=0;j<i;j++){
		if(mygrido.getRowId(j)!= undefined){
			totalPartidas += parseFloat(mygrido.cells(mygrido.getRowId(j),2).getValue());
		}
	}
	$('ctotal').value = (isNaN(totalPartidas))? '0' : muestraFloat(totalPartidas);
	$('epre').value = $('ctotal').value;

}

	function Guardar()
	{
		var JsonAux,obra=new Array;
			mygrido.clearSelection()
			for(j=0;j<i;j++)
			{
				if(!isNaN(mygrido.getRowId(j)))
				{
					obra[j] = new Array;
					obra[j][0]= mygrido.cells(mygrido.getRowId(j),0).getValue();
					obra[j][1]= mygrido.cells(mygrido.getRowId(j),1).getValue();
					obra[j][2]= mygrido.cells(mygrido.getRowId(j),2).getValue();
					obra[j][3]= mygrido.getRowId(j);			
				}
			}
			JsonAux={"obra":obra};
			$("obra").value=JsonAux.toJSONString(); 
			if (parseFloat($('ctotal').value < 0)){
				alert("Debe seleccionar las partidas para imputar los montos"); 
				return false;
			}
			return false;
			//validate();
	}
	
	function busca(id_ue, id_financiadora, fecha_desde, fecha_hasta, id){
	var url = 'updater_busca_obras.php';
	var pars = '&id_ue=' + id_ue + '&id_financiadora=' + id_financiadora;
	pars += '&id=' + id + '&fecha_desde=' + fecha_desde+ '&fecha_hasta=' + fecha_hasta +'&ms='+new Date().getTime();
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

Event.observe('busca_ue', "change", function () { 
	busca($F('busca_ue'), 
	$F('busca_financiadora'), 
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta'),
	$F('busca_id')); 
});
Event.observe('busca_financiadora', "change", function () { 
	busca($F('busca_ue'), 
	$F('busca_financiadora'), 
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta'),
	$F('busca_id'));  
});
Event.observe('busca_id', "keyup", function () { 
	busca($F('busca_ue'), 
	$F('busca_financiadora'), 
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta'),
	$F('busca_id'));  
});

function validafecha(fecha){
	var upper = 31;
	if(/^(\d{2})\/(\d{2})\/(\d{4})$/.test(fecha.value)) { // dd/mm/yyyy
		if(RegExp.$2 == '02') upper = 29;
		if((RegExp.$1 <= upper) && (RegExp.$2 <= 12)) {
			busca($F('busca_ue'), 
			$F('busca_financiadora'), 
			$F('busca_fecha_desde'), 
			$F('busca_fecha_hasta'),
			$F('busca_id'));  
		} else {
			alert("Fecha incorrecta");
			$(fecha).value = "";
		}
	}else if(fecha.value != '') {
		alert("Fecha incorrecta");
		$(fecha).value = "";
	}
}

function mostrarBuscarCat(){
	mygrido.clearAll();
	$('txtcategorias_programaticas').value = '';
	$('txtpartidas_presupuestarias').value = '';
	$('categorias_programaticas').value = '';
	$('partidas_presupuestarias').value = '';
	$('bcategorias').style.display = 'inline';

}

function mostrarBuscarCat2(){
	$('bcategorias').style.display = 'inline';

}

function traeCategoriasProgramaticas(ue){
	
	var esc = $('escenario').value;
	
	var url = 'buscar_categorias.php';
	var pars = 'ue=' + ue + '&escenario=' + esc + '&ms='+new Date().getTime();
		
	var Request = new Ajax.Request(
		url,
		{
			method: 'get',
			parameters: pars,
			onLoading:function(request){}, 
			onComplete:function(request){
				
				Dialog.alert(request.responseText, {windowParameters: {width:600, height:400, 
								showEffect:Element.show,hideEffect:Element.hide,
								showEffectOptions: { duration: 1}, hideEffectOptions: { duration:1 }
								
								}});
				
				}
			}
	);     	   
}
	function traePartidasPresupuestarias(cp){
	
	var esc = $('escenario').value;
	var url = 'buscar_partidas.php';
	var pars = 'cp=' + cp +'&idp=404'+ '&escenario='+esc+'&ms='+new Date().getTime();
		
	var Request = new Ajax.Request(
		url,
		{
			method: 'get',
			parameters: pars,
			onLoading:function(request){}, 
			onComplete:function(request){
				
				Dialog.alert(request.responseText, {windowParameters: {width:600, height:400, 
								showEffect:Element.show,hideEffect:Element.hide,
								showEffectOptions: { duration: 1}, hideEffectOptions: { duration:1 }
								
								}});
				
				}
			}
	);     	   
}

function selCategorias(id, nombre){

	$('txtcategorias_programaticas').value = nombre;
	$('categorias_programaticas').value = id;
	$('bpartidas').style.display = 'inline';
	Dialog.okCallback();

}

function selPartidas(id, nombre){

	$('txtpartidas_presupuestarias').value = nombre;
	$('partidas_presupuestarias').value = id;
	Dialog.okCallback();

}

var t;
function busca_popup_pp()
{
	clearTimeout(t);
	setTimeout('buscaPartidasPresupuestarias()', 800);
}

function buscaPartidasPresupuestarias()
{
	var url = 'buscar_partidas.php';
	var pars = 'cp=' + $('cp').value +'&idp=404&nombre='+$('search_nombre_pp').value+'&codigo='+$('search_cod_pp').value+'&opcion=2&ms='+new Date().getTime();
		
	var updater = new Ajax.Updater('divPartidas', 
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
	function traerPartidasSeleccionada(rowId){

	var cp = mygrido.cells(rowId,0).getValue();
	var pp = mygrido.cells(rowId,1).getValue();
	var url = 'json.php';
	var pars = 'op=parcat&cp=' + cp +'&pp='+ pp +'&ms='+new Date().getTime();
			
	var myAjax = new Ajax.Request(
		url,
		{
			method: 'get',
			parameters: pars,
			onComplete: function(peticion){
			
				var jsonData = eval('(' + peticion.responseText + ')');
				if (jsonData == undefined) { return }
				$('nom_cat_pro').value 			= jsonData.nom_cat;
				$('nom_par_pre').value 			= jsonData.nom_par;
			}
		}
	);
}

	function traeUnidadesEjecutoras(esc,id_unidad){
	var url = 'updater_selects.php';
	var pars = 'combo=unidadesEjecutoras&escenario=' + esc + '&unidad=' + id_unidad +'&ms='+new Date().getTime();
	var updater = new Ajax.Updater('divcombounidades', 
		url,
		{
			method: 'get',
			parameters: pars,
			asynchronous:true,
			evalScripts:true,
			onLoading:function(request){Element.show('cargador_partidas')},
			onComplete:function(request){Element.hide('cargador_partidas')}
		});
} 
</script>
<? require ("comun/footer.php");?>
