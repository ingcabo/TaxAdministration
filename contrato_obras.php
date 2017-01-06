<?
include('adodb/adodb-exceptions.inc.php');
require ("comun/ini.php");
// Creando el objeto contrato_obras
$hoy=date("Y-m-d");
$oContratoObras = new contrato_obras;
$accion = $_REQUEST['accion'];
if($accion == 'Guardar'){
	//die($_REQUEST['contrato']);
	 $oContratoObras->add($conn, 
										$_POST['tipdoc'],
										$_POST['unidad_ejecutora'],
										$_POST['proveedores'],
										"0",       //$_POST['tipos_fianzas'],
										$_POST['id_obra'],
										$usuario->id,
										$_POST['descripcion'],
										$_POST['observaciones'],
										guardafecha($_POST['fecha']),
										/*$_POST['idParCat'],
										$_REQUEST['categorias_programaticas'],
										$_REQUEST['partidas_presupuestarias'],
										$_REQUEST['monto']);*/
										$_REQUEST['contrato'],
										guardafloat($_REQUEST['montoCO']),
										$_POST['nrodoc'],
										$auxNroDoc
										);
}elseif($accion == 'Aprobar'){
//die($_REQUEST['contrato']);
	$oContratoObras->aprobar($conn, 
									$_POST['id'],
									$_POST['nrif'], 
									$usuario->id,
									$_POST['unidad_ejecutora'],
									date("Y"),
									$_POST['observaciones'],
									$_POST['tipdoc'],
									$hoy,
									//guardafecha($_POST['fecha']),
									'1',
									$_POST['proveedores'],
									$_REQUEST['contrato'],
									$_POST['nrodoc'],
									$auxNroDoc,
									$escEnEje,
									$_POST['tipoProv']);
}elseif($accion == 'Actualizar'){
	$oContratoObras->set($conn, 
									$_POST['id'], 
									$_POST['tipdoc'],
									$_POST['unidad_ejecutora'],
									$_POST['proveedores'],
									//$_POST['tipos_fianzas'],
									$_POST['id_obra'],
									$usuario->id,
									$_POST['descripcion'],
									$_POST['observaciones'],
									guardafecha($_POST['fecha']),
									$_REQUEST['contrato'],
									guardafloat($_REQUEST['montoCO']),
									$_POST['nrodoc'],
									$auxNroDoc
									);
}elseif($accion == 'del'){
	//$oContratoObras->del($conn, $_POST['id']);
		$msj = ERROR;
	}elseif ($accion == 'Anular'){
		$msj = $oContratoObras->anular($conn, $_POST['id'],$usuario->id,$_POST['unidad_ejecutora'],date("Y"),$_POST['observaciones'],'011', $hoy, $_REQUEST['status'], $_POST['proveedores'], $_REQUEST['contrato'],$_REQUEST['nrodoc'],$_POST['porc_iva'],$escEnEje);
		
	}

$msj = $oContratoObras->msj; // lleno esta variable con el mensaje de la operacion llevada a cabo
$cContratoObras=$oContratoObras->get_all($conn, $escEnEje);
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<script type="text/javascript">var mygrid,i=0, ipp=0</script>
<br />
<span class="titulo_maestro">Contrato de Obras</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />
<div id="contenidobuscador">
<fieldset id="buscador">
	<legend>Buscar:</legend>
	<table border="0">
		<tr>
			<td colspan="3">Unidad Ejecutora</td>
		</tr>
		<tr>
			<td colspan="3"><?=helpers::combo_ue_cp($conn,'busca_ue','','','','','','',
			"SELECT DISTINCT id, id || ' - ' || descripcion AS descripcion FROM unidades_ejecutoras ORDER BY id")?></td>
		</tr>
		<tr>
			<td>Proveedor</td>
			<td>Descripci&oacute;n</td>
		</tr>
		<tr>
			<td>
			<?=helpers::combo_ue_cp($conn, 'busca_proveedores','','','','','','',
			"SELECT id, rif||' - '||nombre AS descripcion FROM proveedores")?></td>
			<td><input style="width:300px" type="text" name="busca_descripcion" id="busca_descripcion" /></td>
		</tr>
		<tr>
			<td>N&ordm; de Documento</td>
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
			<td><input style="width:100px" type="text" name="busca_nrodoc" id="busca_nrodoc" /></td>
			<td colspan="2">
				<table>
					<tr>
						<td>
							<input style="width:100px"  type="text" name="busca_fecha_desde" id="busca_fecha_desde" 
							onchange="validafecha(this);"/>
						</td>
						<td>
							<a href="#" id="boton_busca_fecha_desde" onclick="return false;">
								<img border="0" alt="fecha" src="images/calendarA.png" width="20" height="20" />
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
								<img border="0" alt="fecha" src="images/calendarA.png" width="20" height="20" />
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
<br />
<div style="height:40px;padding-top:10px;">
<p id="cargando" style="display:none;margin-top:0px;">
  <img alt="Cargando" src="images/loading.gif" /> Cargando...
</p>
</div>
<script type="text/javascript">

var t;

/***************       Seccion de las Partidas ******************************/
function addTR() {
	var x = $('tablita').insertRow($('tablita').rows.length);
	var i = $('tablita').rows.length;
	var y1=x.insertCell(0);
	var y2=x.insertCell(1);
	var y3=x.insertCell(2);
	var y4=x.insertCell(3);
	var y5=x.insertCell(4);
	var y6=x.insertCell(5);
	var contenedor;
	y1.innerHTML= "Categoria:";
	var cp = $('categorias_programaticas_1').cloneNode(true);
	cp.setAttribute('id', 'categorias_programaticas_' + i);
	cp.setAttribute('onchange', 'traeParPreDesdeUpdater(this.value, ' + i + ')' );
	cp.value = 0;
	contenedor = Builder.node('div', {id:'cont_categorias_programaticas_' + i});
	contenedor.appendChild(cp);
	y2.appendChild(contenedor);
	y3.innerHTML= "Partida Presupuestaria:";
	var pp = $('partidas_presupuestarias_1').cloneNode(true);
	pp.value = 0;
	pp.setAttribute('id', 'partidas_presupuestarias_' + i);
	contenedor = Builder.node('div', {id:'cont_partidas_presupuestarias_' + i});
	contenedor.appendChild(pp);
	y4.appendChild(contenedor);
	y5.innerHTML= "Monto:";
	var cppp = $('cppp_1').cloneNode(false)
	cppp.setAttribute('id', 'cppp_' + i);
	cppp.value = 0;
	y6.appendChild(cppp)
	var idParCat = $('idParCat_1').cloneNode(false)
	idParCat.setAttribute('id', 'idParCat_' + i);
	idParCat.value = 0;
	y6.appendChild(idParCat)
	var nuevoDisponibleParCat = $('nuevoDisponibleParCat_1').cloneNode(false)
	nuevoDisponibleParCat.setAttribute('id', 'nuevoDisponibleParCat_' + i);
	nuevoDisponibleParCat.value = 0;
	y6.appendChild(nuevoDisponibleParCat);
	var m = $('monto_1').cloneNode(false)
	m.setAttribute('id', 'monto_' + i);
	m.removeAttribute('readonly');
	m.value = '';
	m.onblur = function(){ operacion(this.value, $('nuevoDisponibleParCat_' + i).value, this.id); };
	y6.appendChild(m);
	$('total').value = i;
}

function delTR() {
	if($('tablita').rows.length <= 1){
		$('partidas_presupuestarias_1').value = 0;
		$("cppp_1").value = 0;
		$("monto_1").value = "";
		alert('No puede eliminar mas partidas');
	}else{
		var montoTotal = 0;
		var x = $('tablita').deleteRow($('tablita').rows.length - 1);
		$A(document.getElementsByClassName('montos')).each( function(e){
			montoTotal += parseFloat(usaFloat(e.value));
		});
		$('monto_total_partidas').value = muestraFloat(montoTotal);
	}
}

function traeParPreDesdeUpdater(cp, fila){
	var url = 'updater_selects.php';
	var pars = 'combo=parpre&cp=' + cp + '&cont=' + fila;
	var updater = new Ajax.Updater('cont_partidas_presupuestarias_' + fila, 
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

function traeParCatDesdeXML(cp, pp, nombre){
	var cppp = cp+pp;
	var url = 'xmlTraeParCat.php';
	var fila = getFila(nombre);
	var pars = 'cp=' + cp + '&pp=' + pp + '&pagina=contrato_obras&fila=' + fila;
	//$('xxx').innerHTML = pars;
	var salir = false;
	var listaNodos = document.getElementsByClassName('cppps');
	var seleccionado = false;
	$A(listaNodos).each( function(e){
		if(cppp == e.value){
			alert('Esta partida ya ha sido seleccionada, por favor seleccione otra partida');
			$('partidas_presupuestarias_' + fila).value = 0;
			seleccionado = true;
			salir = true;
		}
	} );
	if(!seleccionado)
		$('cppp_' + fila).value = cppp;
	if(!salir){
		var myAjax = new Ajax.Request(
			url, 
			{
				method: 'get', 
				parameters: pars,
				onComplete: function(peticion){
					var doc = new XMLDoc(peticion.responseXML.documentElement);
					var hash = doc.asHash();
					hash.parcat.each ( function(e) { 
						$('nuevoDisponibleParCat_' + fila).value = e.disponible; 
						$('idParCat_' + fila).value = e.idParCat; 
					});
				}
			}
		);
	}
}

function operacion(monto, disponible, id_monto){
	//alert('m:' +monto+'d:' +disponible+'id:' +id_monto)
	var montoTotal = 0;
	monto = parseFloat(usaFloat(monto));
	if(monto <= disponible){
		$A(document.getElementsByClassName('montos')).each( function(e){
			montoTotal += parseFloat(usaFloat(e.value));
		});
		$('monto_total_partidas').value = muestraFloat(montoTotal);
	}else{
		alert('El monto es mayor que el disponible en la partida');
		$(id_monto).value = "0,00";
		$(id_monto).focus();
	}
}

function getFila(nombre){
	var aNombre = nombre.split("_");
	return aNombre[aNombre.length - 1];
}


function actapr(elemento){
	if(elemento.value == 'Actualizar'){
		$('accion').value = 'Actualizar';
	}else if(elemento.value == 'Aprobar') {
		$('accion').value = 'Aprobar'; 
	} else
		$('accion').value = 'Anular';
	 //validate(); 
}




/* Metodos utilizados en el buscador */
function busca(id_ue, id_proveedor, descripcion, fecha_desde, fecha_hasta, nrodoc, pagina){
	var url = 'updater_busca_contrato_obra.php';
	var pars = '&id_ue=' + id_ue + '&id_proveedor=' + id_proveedor+ '&descripcion=' + descripcion;
	pars += '&nrodoc=' + nrodoc + '&fecha_desde=' + fecha_desde+ '&fecha_hasta=' + fecha_hasta + "&pagina=" + pagina;
	//alert(pars);
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
	$F('busca_proveedores'), 
	$F('busca_descripcion'), 
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta'),
	$F('busca_nrodoc'),1); 
});
Event.observe('busca_proveedores', "change", function () { 
	busca($F('busca_ue'), 
	$F('busca_proveedores'), 
	$F('busca_descripcion'), 
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta'),
	$F('busca_nrodoc'),1); 
});
Event.observe('busca_descripcion', "keyup", function () { 
	busca($F('busca_ue'), 
	$F('busca_proveedores'), 
	$F('busca_descripcion'), 
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta'),
	$F('busca_nrodoc'),1); 
});
Event.observe('busca_nrodoc', "keyup", function () { 
	busca($F('busca_ue'), 
	$F('busca_proveedores'), 
	$F('busca_descripcion'), 
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta'),
	$F('busca_nrodoc'),1); 
});

function validafecha(fecha){
	var upper = 31;
	if(/^(\d{2})\/(\d{2})\/(\d{4})$/.test(fecha.value)) { // dd/mm/yyyy
		if(RegExp.$2 == '02') upper = 29;
		if((RegExp.$1 <= upper) && (RegExp.$2 <= 12)) {
			busca($F('busca_ue'), 
			$F('busca_proveedores'), 
			$F('busca_descripcion'), 
			$F('busca_fecha_desde'), 
			$F('busca_fecha_hasta'),
			$F('busca_nrodoc'),1); 
		} else {
			alert("Fecha incorrecta");
			fecha.value = "";
		}
	}else if(fecha.value != '') {
		alert("Fecha incorrecta");
		fecha.value = "";
	}
}

	function traeProveedorDesdeXML(id_proveedor){
	$('id_proveedor').value = id_proveedor;
	
	var url = 'xmlTraeProveedor.php';
	var pars = 'id=' + id_proveedor;
	var myAjax = new Ajax.Request(
		url, 
		{
			method: 'get', 
			parameters: pars,
			onComplete: traeProveedor
		});
}
	function traeProveedor(originalRequest){
	var xmlDoc = originalRequest.responseXML;
	var x = xmlDoc.getElementsByTagName('proveedor');
	for(j=0;j<x[0].childNodes.length;j++){
		if (x[0].childNodes[j].nodeType != 1) continue;
		var nombre = x[0].childNodes[j].nodeName
		$(nombre).value = x[0].childNodes[j].firstChild.nodeValue;
	}
}

function traeCategoriasProgramaticas(ue){
	
	var url = 'buscar_categorias.php';
	var pars = 'ue=' + ue +'&ms='+new Date().getTime();
		
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

	function traePartidasPresupuestarias(cp){
	
	var url = 'buscar_partidas.php';
	var pars = 'cp=' + cp +'&idp=404&ms='+new Date().getTime();
		
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

	//MANEJO DE LAS CATEGORIAS PROGRAMATICAS//
function AgregarCO(){
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
		
	}else if($('montoip').value==""){
		
		alert("Primero debe colocar el monto de la Imputacion Presupuestaria.");
		return;
	
	}else if(parseFloat($('disponible').value) < usaFloat($('montoip').value)){
		alert("El monto disponible en la partida es menor al requerido");
		$('montoip').value='0,00';
		return;	
	}else{
		
		for(j=0;j<i;j++){
			
			if (mygridco.getRowId(j)!=undefined){
				if (mygridco.cells(mygridco.getRowId(j),'0').getValue() == $('categorias_programaticas').value && mygridco.cells(mygridco.getRowId(j),'1').getValue() == $('partidas_presupuestarias').value){
						
					alert('Esta partida ya ha sido seleccionada, por favor seleccione otra partida');
					return false;

				}
			
			}
			
			
		}
		
		/*mygridco.getCombo(0).put(JsonData[j]['id_categoria_programatica'],JsonData[j]['categoria_programatica']);
		mygridco.getCombo(1).put(JsonData[j]['id_partida_presupuestaria'],JsonData[j]['partida_presupuestaria']);*/
		mygridco.addRow($('idParCat').value,$('categorias_programaticas').value+";"+$('partidas_presupuestarias').value+";"+$('montoip').value+";0;;0;0");
		i++;
		sumaTotal();
		$('montoip').value='0,00';
		
	}
}

function EliminarCO(){
	mygridco.deleteRow(mygridco.getSelectedId());
	sumaTotal();
	
}

/*function traeCategoriasProgramaticas(ue){
	
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
}*/

	//ESTA FUNCION PERMITE SUMAR EL TOTAL DE MONTOS ASIGNADOS DE CADA PARTIDA PRESUPUESTARIA POR CATEGORIA PROGRAMATICA AGREGADOS AL GRID

function sumaTotal_old(){
	var totalPartidas = 0;
	for(j=0;j<i;j++){
		if(mygridco.getRowId(j)!= undefined){
			totalPartidas += parseFloat(mygridco.cells(mygridco.getRowId(j),2).getValue());
		}
	}
	$('montoCO').value = (isNaN(totalPartidas))? '0' : muestraFloat(totalPartidas);
	$('montoCOB').value = (isNaN(totalPartidas))? '0' : muestraFloat(totalPartidas);
	calculaImp();

}

function sumaTotal_too_old(){
	var total = 0;
	var r = 0;
	for(j=0;j<i;j++){
		if(mygridco.getRowIndex(j)!=-1){
			total += parseFloat(mygridco.cells(j,2).getValue()); 
		}
	}	
	r = muestraFloat(total);
	$('montoCO').value  = r;
}

function traerDisponiblePartidas(cp, pp){
	var url = 'json.php';
	var pars = 'op=parcat&cp=' + cp +'&pp='+ pp;
			
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

	function Guardar()
	{
		var JsonAux,contrato=new Array;
			mygridco.clearSelection()
			if(parseFloat($('montoCO').value)>0){
				for(j=0;j<mygridco.getRowsNum();j++)
				{
					if(!isNaN(mygridco.getRowId(j)))
					{
						contrato[j] = new Array;
						contrato[j][0]= mygridco.cells(mygridco.getRowId(j),0).getValue();
						contrato[j][1]= mygridco.cells(mygridco.getRowId(j),1).getValue();
						contrato[j][2]= usaFloat(mygridco.cells(mygridco.getRowId(j),2).getValue());
						contrato[j][3]= usaFloat(mygridco.cells(mygridco.getRowId(j),3).getValue());
						contrato[j][4]= mygridco.cells(mygridco.getRowId(j),4).getValue();
						contrato[j][5]= usaFloat(mygridco.cells(mygridco.getRowId(j),5).getValue());
						contrato[j][6]= usaFloat(mygridco.cells(mygridco.getRowId(j),6).getValue());
						contrato[j][7]= mygridco.getRowId(j);			
					}
				}
				JsonAux={"contrato":contrato};
				$("contrato").value=JsonAux.toJSONString();
				validate();
			} else {
				alert('Debe selecionar partidas para hacer la imputacion presupuestaria');
			} 
	}
	
	function ver_partpre(){
		Effect.toggle('partpreDiv', 'blind');
	}
	
	function mostrar_ventana(){
	//var tipo = "('P','A')"; 
	var url = 'buscar_proveedores.php';
	//el parametro status se pasa vacio para que no filtre solo los activos, en caso de que se quiera esto se tiene que pasar status=A
	var pars = 'tipo=&status=&ms='+new Date().getTime();
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

function busca_popup()
{
	clearTimeout(t);
	t = setTimeout('buscaProveedores()', 800);
}

function buscaProveedores()
{
	//var tipo = "('P','A')"; 
	var url = 'buscar_proveedores.php';
	var pars = 'tipo='+$('tipo_prov').value+'&status=&rif='+$('search_rif_prov').value+ '&nombre='+ $('search_nombre_prov').value+'&opcion=2&ms'+new Date().getTime();
		
	var updater = new Ajax.Updater('divProveedores', 
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

function selDocumento(id, nombre){
	
	$('nombrepro').value = nombre;
	$('proveedores').value = id;
	Dialog.okCallback();

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

function mostrarBuscarCat(){
	mygridco.clearAll();
	$('bcategorias').style.display = 'inline';
	$('txtcategorias_programaticas').value = '';
	$('categorias_programaticas').value = '';
	$('txtpartidas_presupuestarias').value = '';
	$('partidas_presupuestarias').value = '';
}

function mostrarBuscarCat2(){
	$('bcategorias').style.display = 'inline';

}

function buscaNroDoc(boton){
	//alert(boton);
	/*if(boton=='Guardar'){
		var tabla = 'puser.contrato_obras';
		var nrodoc = $('nrodoc').value;
		var tipdoc = $('tipdoc').value;
		var id_ue = $('unidad_ejecutora').value;
		var url = 'json.php';
		var pars = 'op=busca_nrodoc&nrodoc=' + nrodoc +'&tipdoc='+ tipdoc +'&id_ue='+ id_ue +'&tabla='+ tabla +'&ms='+new Date().getTime();
				
		var myAjax = new Ajax.Request(
			url,
			{
				method: 'get',
				parameters: pars,
				onComplete: function(peticion){
				
					var jsonData = peticion.responseText;
					//alert('jsonData')
					if (jsonData != '')  
						alert('Este numero de documento ya se encuentra registrado');
					else 
						Guardar();
					
				}
			}
		);
	}else{*/
		Guardar();
		//}
}
	
	function calculaImp(){
		//alert('val: ' + $('porc_iva').value)
		if($('porc_iva').value!='')
			var valor = $('porc_iva').value;
		else
			valor = 0;
		var montoImp = parseFloat(usaFloat($('montoCO').value)) * (valor/100);
		
		$('montoIva').value = muestraFloat(montoImp);	
		
	}
	
	function generaTotal(rowId){
		if (mygridco.cells(rowId,4).getValue()==''){
			alert("Debe seleccionar un valor para el IVA");
			return false;
		} else if (parseFloat(mygridco.cells(rowId,3).getValue()) > parseFloat(usaFloat(mygridco.cells(rowId,2).getValue()))){
				alert("El monto excento no puede ser mayor que el monto a imputar");
				return false;
		} else { 
			var costo = usaFloat(mygridco.cells(rowId,2).getValue());
			var excento = usaFloat(mygridco.cells(rowId,3).getValue());
			var iva = parseInt(mygridco.cells(rowId,4).getValue());
			var costo_neto = costo - excento;
			var impuesto = costo_neto * (iva/100);
			var total = costo + impuesto;
					/*var subtotal = costo * cant;
					$('subtot').value = subtotal;*/
			mygridco.cells(rowId,5).setValue(muestraFloat(impuesto.toFixed(2)));		
			mygridco.cells(rowId,6).setValue(muestraFloat(total.toFixed(2)));
			sumaTotal();
	   }
	}
	
	function sumaTotal(){
	var totalpar = 0;
	var totaliva = 0;
	for(j=0;j<mygridco.getRowsNum();j++){
		if(mygridco.getRowId(j)!= undefined){
			totalpar += usaFloat(mygridco.cells(mygridco.getRowId(j),6).getValue());
			totaliva += usaFloat(mygridco.cells(mygridco.getRowId(j),5).getValue());
		}
	}
	$('total_iva').value = (isNaN(totaliva))? '0' : muestraFloat(totaliva);
	$('montoCO').value = (isNaN(totalpar))? '0' : muestraFloat(totalpar);
	$('montoCOB').value = (isNaN(totalpar))? '0' : muestraFloat(totalpar);

}

	function traerPartidasSeleccionada(rowId){

	var cp = mygridco.cells(rowId,0).getValue();
	var pp = mygridco.cells(rowId,1).getValue();
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

function traeObras(){
	
	var url = 'buscar_obras.php';
	var pars = 'ms='+new Date().getTime();
		
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
		
function busca_popup_obra()
{
	clearTimeout(t);
	setTimeout('buscaObras()', 800);
}

function buscaObras()
{
	var url = 'buscar_obras.php';
	var pars = 'busca=1&nro=' + $('search_nro').value +'&ue='+$('search_ue').value+'&ms='+new Date().getTime();
		
	var updater = new Ajax.Updater('divObras', 
		url,
		{
			method: 'post',
			parameters: pars,
			asynchronous:true, 
			evalScripts:true,
			onLoading:function(request){Element.show('cargando')}, 
			onComplete:function(request){Element.hide('cargando')}
		});
}
function selObra(id){
	var url = 'json.php';
	var pars = 'op=obra&id=' + id +'&ms='+new Date().getTime();
	mygridco.clearAll();	
	var Request = new Ajax.Request(
		url,
		{
			method: 'get',
			parameters: pars,
			onLoading:function(request){}, 
			onComplete:function(request){
				obra = eval('(' + request.responseText + ')' );
				obra.relacion = eval('(' + obra.relacion + ')' );
				$('id_obra').value = obra.id;
				$('descripcion').value = obra.descripcion;
				$('unidad_ejecutora').value = obra.id_unidad_ejecutora;
				for(i=0;i<obra.relacion.length;i++){
					mygridco.addRow(obra.relacion[i]['idParCat'],obra.relacion[i]['id_categoria_programatica'] + ";"
																				+obra.relacion[i]['id_partida_presupuestaria']+";"
																				+obra.relacion[i]['monto']
																				+';0;0;0',i);

				}
			}
		}
	);
	Dialog.okCallback();
}
</script>
<div id="xxx"></div>
<?
//$validator->create_message("error_nrodoc", "nrodoc", "*");
$validator->create_message("error_fecha", "fecha", "*");
$validator->create_message("error_tipo_doc", "tipdoc", "*");
$validator->create_message("error_desc", "descripcion", "*");
$validator->create_message("error_id_obra", "id_obra", "*");
$validator->create_message("error_prov", "proveedores", "*");
$validator->create_message("error_ue", "unidad_ejecutora", "*");
//$validator->create_message("error_observaciones", "observaciones", "*");
$validator->print_script();
require ("comun/footer.php");
?>
