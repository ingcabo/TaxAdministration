<?
include('adodb/adodb-exceptions.inc.php');
require ("comun/ini.php");
// Creando el objeto orden_servicio_trabajo
$oOrdenServicioTrabajo = new orden_servicio_trabajo;
$accion = $_REQUEST['accion'];

if($accion == 'Guardar'){
	
	$oOrdenServicioTrabajo->add($conn, 
										$_POST['tipdoc'],
										$_POST['unidad_ejecutora'],
										guardafecha($_POST['fecha']),
										guardafecha($_POST['fecha_entrega']),
										$_POST['lugar_entrega'],
										$_POST['condicion_pago'],
										$_POST['condicion_operacion'],
										$_POST['proveedores'],
										$_POST['observaciones'],
										$_POST['nro_requisicion'],
										$_POST['nro_cotizacion'],
										$_POST['nro_factura'],
										guardafecha($_POST['fecha_factura']),
										$_POST['cod_contraloria'],
										$usuario->id,
										$_POST['contenedor_partidas'],
										$_REQUEST['productos_contenedor'],
										$_REQUEST['nrodoc'],
										$auxNroDoc);
}elseif($accion == 'Actualizar'){
	$oOrdenServicioTrabajo->set($conn, 
									$_POST['id'],
									$_POST['tipdoc'],
									$_POST['unidad_ejecutora'],
									guardafecha($_POST['fecha']),
									guardafecha($_POST['fecha_entrega']),
									$_POST['lugar_entrega'],
									$_POST['condicion_pago'],
									$_POST['condicion_operacion'],
									$_POST['proveedores'],
									$_POST['observaciones'],
									$_POST['nro_requisicion'],
									$_POST['nro_cotizacion'],
									$_POST['nro_factura'],
									guardafecha($_POST['fecha_factura']),
									$_POST['cod_contraloria'],
									$_POST['contenedor_partidas'],
									$_REQUEST['productos_contenedor'],
									$_REQUEST['nrodoc'],
									$auxNroDoc);
		
}elseif($accion == 'Aprobar'){
	$oOrdenServicioTrabajo->aprobar($conn, 
																$_POST['id'],
																$usuario->id,
																$_POST['unidad_ejecutora'],
																date("Y"),
																$_POST['observaciones'],
																$nrodoc,
																$_POST['tipdoc'],
																guardafecha($_POST['fecha']),
																guardafecha($_POST['fecha_factura']),
																'1',
																$_POST['proveedores'],
																$_POST['id_ciudadano'],
																$_POST['categorias_programaticas'],
																$_POST['contenedor_partidas'],
																$_POST['nrodoc'],
																$auxNroDoc,
																$_REQUEST['productos_contenedor'],
																$escEnEje);
																	
}elseif($accion == 'del'){
	$oOrdenServicioTrabajo->del($conn, $_GET['id']);
		
#SECCION DE ANULACION DEL DOCUMENTO#
}elseif ($accion == 'Anular'){
		
		$oOrdenServicioTrabajo->anular($conn, $_POST['id'],$usuario->id,
												$_POST['unidad_ejecutora'],
												date("Y"),
												$_POST['observaciones'],
												$_REQUEST['tipdoc'], 
												guardafecha($_POST['fecha']),
												$_REQUEST['status'], 
												$_POST['proveedores'],
												$_REQUEST['id_ciudadano'],
												$_REQUEST['contenedor_partidas'], 
												$_REQUEST['nrodoc'],
												$_REQUEST['productos_contenedor'],
												$escEnEje);
		
	}
$msj = $oOrdenServicioTrabajo->msj; // lleno esta variable con el mensaje de la operacion llevada a cabo

require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<script type="text/javascript">var mygrid,i=0, ipp=0</script>
<br />
<span class="titulo_maestro">Orden Servicio / Trabajo</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />
<div>
<fieldset id="buscador">
	<legend>Buscar:</legend>
	<table>
		<tr>
			<td colspan="3">Unidad Ejecutora</td>
		</tr>
		<tr>
			<td colspan="3"><?=helpers::combo_ue_cp($conn,'busca_ue','','','','','','',
			"SELECT DISTINCT id, (id || ' - ' || descripcion) AS descripcion FROM unidades_ejecutoras ORDER BY id")?></td>
		</tr>
		<tr>
			<td>Tipo de Documento</td>
			<td colspan="2">Proveedor</td>
			
		</tr>
		<tr>
			<td>
			<?=helpers::combo_ue_cp($conn, 
						'busca_tipdoc','','','','','','',
						"SELECT * FROM tipos_documentos WHERE abreviacion = 'OS' OR abreviacion = 'OT' ")?>
			</td>
			<td colspan="2">
			<?=helpers::combo_ue_cp($conn, 'busca_proveedores','','','','','','',
			"SELECT id, rif||' - '||nombre AS descripcion FROM proveedores")?></td>
			
		</tr>
		<tr>
			<td colspan="3">Descripci&oacute;n: </td>
		</tr>
		<tr>
			<td colspan="3"><input style="width:300px" type="text" name="busca_fecha" id="busca_observaciones" /></td>
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
								<img border="0" src="images/calendarA.png" width="20" height="20" alt="Calendario" />
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
								<img border="0" src="images/calendarA.png" width="20" height="20" alt="calendario" />
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

//AGREGAR UNA FILA EN EL GRID DE PRODUCTOS//
function Agregar(){
	mygrid.addRow(i,";0,00;;");
	i++;
}

//ELIMINAR UNA FILA EN EL GRID DE PRODUCTOS//
function Eliminar(){
	mygrid.deleteRow(mygrid.getSelectedId());
	sumaSubTotalProductos();
	sumaIvaProductos();
	sumaTotalProductos();
}

//FUNCION QUE TRAE LOS DATOS DE UN PRODUCTO CUANDO SE SELECCIONA//
function traerProductos(stage,rowId,cellInd){
	if(stage==2){
		if(cellInd=='0'){
			var url = 'json.php';
			var pars = 'op=productos&id=' + mygrid.cells(rowId,'0').getValue();
			
			for(j=0;j<i;j++){
				if (mygrid.getSelectedId()!=j){
					if(mygrid.getRowIndex(j)!=-1){
						if (mygrid.cells(rowId,'0').getValue() == mygrid.cells(j,0).getValue()){
							
							alert("El Producto Ya Se ha Seleccionado");
							mygrid.cells(rowId,'0').setValue('0');
							
							return false;
						}
					}
				}
			}
	
			var myAjax = new Ajax.Request(
				url, 
				{
				method: 'get', 
				parameters: pars,
				onComplete: function(peticion){
					var jsonData = eval('(' + peticion.responseText + ')');
					if (jsonData == undefined) { return }
					mygrid.cells(rowId,'1').setValue(jsonData.id);
					mygrid.cells(rowId,'3').setValue(jsonData.unidad_medida);
					mygrid.cells(rowId,'5').setValue(jsonData.ultimo_costo);
					calcularprecio(rowId,'3',mygrid.cells(rowId,'2').getValue());
					}
				}
			);
		}
	}
}

//FUNCION QUE PERMITE CALCULAR EL MONTO TOTAL POR PRODUCTOS//
	function calcularprecio(rowId,cellInd){
	
	if(cellInd=='1'){
		
		var iva = mygrid.cells(rowId,'2').getValue();
		var totalIva = ((iva * usaFloat(mygrid.cells(rowId,'1').getValue())) / 100);
		mygrid.cells(rowId,'3').setValue(muestraFloat(totalIva));
		
	
		var totalprod = usaFloat(mygrid.cells(rowId,'1').getValue()) + usaFloat(mygrid.cells(rowId,'3').getValue());
		mygrid.cells(rowId,'4').setValue(muestraFloat(totalprod));
		
		sumaSubTotalProductos();
		sumaIvaProductos();
		sumaTotalProductos();
	}
	
}

//PERMITE SACAR EL TOTAL DE TODA LA COLUMNA DE IVA//
function sumaIvaProductos(){
	
	var sumaIva = 0;
	for(j=0;j<i;j++){
		if(mygrid.getRowIndex(j)!=-1){
			sumaIva += parseFloat(usaFloat(mygrid.cells(j,3).getValue())); 
		}
	}
	$('total_iva_fact').value = isNaN(sumaIva)? '0': muestraFloat(sumaIva);
}

//PERMITE CALCULAR EL SUBTOTAL DE LOS PRODUCTOS//
function sumaSubTotalProductos(){
	var sumaSubTotal = 0;
	for(j=0;j<i;j++){
		if(mygrid.getRowIndex(j)!=-1){
			sumaSubTotal += parseFloat(usaFloat(mygrid.cells(j,1).getValue())); 
		}
	}
	$('subtotal').value = isNaN(sumaSubTotal)? '0':muestraFloat(sumaSubTotal);
}

//PERMITE CALCULAR EL TOTAL DE PRODUCTOS//
function sumaTotalProductos(){
	
	sumaTotal = usaFloat($('subtotal').value) + usaFloat($('total_iva_fact').value); 
	$('total_general_fact').value = muestraFloat(sumaTotal);
}

//ESTA FUNCION PERMITE QUE LOS DATOS DE PROPDUCTOS SE GUARDE EN BASE DE DATOS//
function Guardar(){
	var JsonAux,ProductosAux=new Array;
	mygrid.clearSelection()
	for(j=0;j<i;j++){
		if(!isNaN(mygrid.getRowId(j))){
			ProductosAux[j] = new Array;
			ProductosAux[j][0]= mygrid.cells(mygrid.getRowId(j),0).getValue();
			ProductosAux[j][1]= usaFloat(mygrid.cells(mygrid.getRowId(j),1).getValue());
			ProductosAux[j][2]= usaFloat(mygrid.cells(mygrid.getRowId(j),2).getValue());
			ProductosAux[j][3]= usaFloat(mygrid.cells(mygrid.getRowId(j),3).getValue());
			ProductosAux[j][4]= usaFloat(mygrid.cells(mygrid.getRowId(j),4).getValue());
		}
	}
	JsonAux={"productos":ProductosAux};
	$("productos_contenedor").value=JsonAux.toJSONString();
		
}

//MANEJO DE LAS CATEGORIAS PROGRAMATICAS//
function AgregarCP(){
	if ($('unidad_ejecutora').value ==""){
		
		alert("Primero debe Seleccionar una Unidad Ejecutora.");
		return;
		
	}else if($('categorias_programaticas').value==""){
	
		alert("Primero debe Seleccionar una Categoria Programatica.");
		return;
		
	}else if($('partidas_presupuestarias').value==""){
		
		alert("Primero debe Seleccionar una Partida Presupuestaria.");
		return;
		
	}else if(usaFloat($('montoip').value) > parseFloat($('disponible').value)){ 
		alert("El Monto sobrepasa el monto disponible para esta partida.");
		$('montoip').value = 0;
		return; 
	}else if(parseFloat($('montoip').value) <= 0){
		alert("El monto debe ser mayor de 0");	
		$('montoip').value = 0;
		return;
	}else{
		
		for(j=0;j<ipp;j++){
			
			if (mygridpp.getRowId(j)!=undefined){
				if (mygridpp.cells(mygridpp.getRowId(j),'0').getValue() == $('categorias_programaticas').value && mygridpp.cells(mygridpp.getRowId(j),'1').getValue() == $('partidas_presupuestarias').value){
						
					alert('Esta partida ya ha sido seleccionada, por favor seleccione otra partida');
					return false;

				}
			
			}
			
			
		}
		
		mygridpp.addRow($('idParCat').value,$('categorias_programaticas').value+";"+$('partidas_presupuestarias').value+";"+$('montoip').value+";0;;0;0");
		ipp++;
		//sumaTotalPartidas();
		$('montoip').value = "0,00";
	}
}

function EliminarCP(){
	
	mygridpp.deleteRow(mygridpp.getSelectedId());
	sumaTotal();
	
}

function validarMontoPP(stage,rowId,cellInd){
			
	//EN ESTE ESTADO CONVIERTO EL MONTO DE FORMATO VENEZOLANO AL FORMATO IMPERIALISTA//
	if (stage==0){
	
		if (cellInd==1){
	
			var valor = usaFloat(mygrid.cells(rowId,cellInd).getValue());
			mygrid.cells(rowId,cellInd).setValue(valor);	
			
		}
			
	}
	
	// VALIDO QUE CUANDO ESTE VACIO COLOQUE 0,00, SINO DEVUELVO EL VALOR EN FORMATO VENEZOLANO //
	if (stage==2){
	
		if (cellInd==1){
		
		     if(mygrid.cells(rowId,cellInd).getValue()==''){
			
				mygrid.cells(rowId,cellInd).setValue('0,00');
				return;
			
		     }else{
				var valor = muestraFloat(mygrid.cells(rowId,cellInd).getValue());
				mygrid.cells(rowId,cellInd).setValue(valor);
				
			
			}
		}
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
	var pars = 'cp=' + cp +'&idp=403&ms='+new Date().getTime();
		
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
	t = setTimeout('buscaPartidasPresupuestarias()', 800);
}

function buscaPartidasPresupuestarias()
{

	var url = 'buscar_partidas.php';
	var pars = 'idp=403&cp=' + $('cp').value +'&nombre='+$('search_nombre_pp').value+'&codigo='+$('search_cod_pp').value+'&opcion=2&ms='+new Date().getTime();
		
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

function igualartotal(){
	$('monto_total_partidas').value = muestraFloat($('disponible').value);
}

function sumaTotalPartidas(){

	var totalPartidas = 0;
	for(j=0;j<ipp;j++){

		if(mygridpp.getRowId(j)!= undefined){
		
			totalPartidas += usaFloat(mygridpp.cells(mygridpp.getRowId(j),2).getValue()); 
		}
	}
	$('monto_total_partidas').value = (isNaN(totalPartidas))? '0' : muestraFloat(totalPartidas,'4');
}

function traerDisponiblePartidas(cp, pp){
	var disp;
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
					igualartotal();
				}
				}
			);
}

function GuardarPP(){
var JsonAux,PPAux=new Array;
	mygridpp.clearSelection()
	
	
	for(j=0;j<ipp;j++){
		if(!isNaN(mygridpp.getRowId(j))){
		
			PPAux[j] = new Array;
			PPAux[j][0]= mygridpp.cells(mygridpp.getRowId(j),0).getValue();
			PPAux[j][1]= mygridpp.cells(mygridpp.getRowId(j),1).getValue();
			PPAux[j][2]= usaFloat(mygridpp.cells(mygridpp.getRowId(j),2).getValue());
			PPAux[j][3]= usaFloat(mygridpp.cells(mygridpp.getRowId(j),3).getValue());
			PPAux[j][4]= mygridpp.cells(mygridpp.getRowId(j),4).getValue();
			PPAux[j][5]= usaFloat(mygridpp.cells(mygridpp.getRowId(j),5).getValue());
			PPAux[j][6]= usaFloat(mygridpp.cells(mygridpp.getRowId(j),6).getValue());
			PPAux[j][7]= mygridpp.getRowId(j);	
			
		}
	}
	JsonAux={"partidaspresupuestarias":PPAux};
	$("contenedor_partidas").value=JsonAux.toJSONString();
		
}

/*Parte Vieja del codigo*/
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

function traeCatProDesdeUpdater(ue){
	var url = 'updater_selects.php';
	var pars = 'combo=catpro&ue=' + ue + '&fila=1';
	var updater = new Ajax.Updater('cont_categorias_programaticas_1', 
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

function traeParPreDesdeUpdater(cp, cont){
	var url = 'updater_selects.php';
	var pars = 'combo=parpre&cp=' + cp + '&cont=' + cont;
	var updater = new Ajax.Updater('cont_partidas_presupuestarias_' + cont, 
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
	var pars = 'cp=' + cp + '&pp=' + pp + '&pagina=orden_servicio_trabajo&fila=' + fila;
	$('xxx').innerHTML = pars;
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
				onComplete: function (originalRequest){
					var xmlDoc = originalRequest.responseXML;
					var x = xmlDoc.getElementsByTagName('parcat');
					for(j=0;j<x[0].childNodes.length;j++){
						if (x[0].childNodes[j].nodeType != 1) continue;
						var nombre = x[0].childNodes[j].nodeName
						if(nombre == 'disponible') $('nuevoDisponibleParCat_' + fila).value = x[0].childNodes[j].firstChild.nodeValue;
						if(nombre == 'idParCat') $('idParCat_'  + fila).value = x[0].childNodes[j].firstChild.nodeValue;
						if(nombre == 'compromisos') $('nuevoMontoParCat_'  + fila).value = x[0].childNodes[j].firstChild.nodeValue;
					}
				}
			}
		);
	}
}

function operacion(monto, disponible, id_monto){
	//alert('m:' +monto+'d:' +disponible+'id:' +id_monto)
	var fila = getFila(id_monto);
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

/***************       Seccion de los Productos ******************************/

function traeProductoDesdeXML(id_producto, nombre){
	var fila = getFila(nombre);
	var url = 'xmlTraeProdOCT.php';
	var pars = 'id=' + id_producto + '&fila='+ fila;
	var salir = false;
	var listaNodos = document.getElementsByClassName('prodSeleccionado');
	var seleccionado = false;
	$A(listaNodos).each( function(e){
		if(id_producto == e.value){
			alert('Este producto ya ha sido seleccionado, por favor seleccione otro producto');
			$(nombre).value = 0;
			seleccionado = true;
			salir = true;
		}
	} );
	if(!seleccionado)
		$('prodSeleccionado_' + fila).value = id_producto;
	if(!salir){
	$('id_producto_' + fila).value = id_producto;
	var myAjax = new Ajax.Request(
		url, 
		{
			method: 'get', 
			parameters: pars,
			onComplete: function (originalRequest){
				var xmlDoc = originalRequest.responseXML;
				var x = xmlDoc.getElementsByTagName('producto');
				for(j=0;j<x[0].childNodes.length;j++){
					if (x[0].childNodes[j].nodeType != 1) continue;
					var nombre = x[0].childNodes[j].nodeName
					$(nombre).value = muestraFloat(x[0].childNodes[j].firstChild.nodeValue);
				}
			}
		});
	}
}

function traeCiudadanoDesdeXML(id_ciudadano){
	var url = 'xmlTraeCiudadano.php';
	var pars = 'id=' + id_ciudadano;
	var myAjax = new Ajax.Request(
		url, 
		{
			method: 'get', 
			parameters: pars,
			onComplete: function(respuesta){
				var xmlDoc = respuesta.responseXML;
				var x = xmlDoc.getElementsByTagName('ciudadano');
				for(j=0;j<x[0].childNodes.length;j++){
					if (x[0].childNodes[j].nodeType != 1) continue;
					var nombre = x[0].childNodes[j].nodeName
					$(nombre).value = x[0].childNodes[j].firstChild.nodeValue;
				}
			}
		}
	);
}

function getFila(nombre){
	var aNombre = nombre.split("_");
	return aNombre[aNombre.length - 1];
}

function addPR(){ 
	var x=$('tablitaProd').insertRow($('tablitaProd').rows.length);
	var i = $('tablitaProd').rows.length - 2;
	var y1=x.insertCell(0);
	var y2=x.insertCell(1);
	var y3=x.insertCell(2);
	var y4=x.insertCell(3);
	var y5=x.insertCell(4);
	var y6=x.insertCell(5);
	var y7=x.insertCell(6);
	var onchange;
	var productos = $('productos_1').cloneNode(true);
	productos.setAttribute('id', 'productos_' + i);
	productos.value = 0;
	y1.appendChild(productos);
	
	var id_producto = $('id_producto_1').cloneNode(false);
	id_producto.setAttribute('id', 'id_producto_' + i);
	id_producto.value = "";
	y2.appendChild(id_producto);
	
	var unidad_medida = $('unidad_medida_1').cloneNode(false);
	unidad_medida.setAttribute('id', 'unidad_medida_' + i);
	unidad_medida.value = "";
	y3.appendChild(unidad_medida);
	
	var cantidad = $('cantidad_1').cloneNode(false);
	cantidad.setAttribute('id', 'cantidad_' + i);

	cantidad.onchange = function(){
										       calculaIva(this.value,$('costo_'+i).value, this.id); 
										       montoTotal(this.value,$('costo_'+i).value, this.id); 
										       sumaIva(); 
										       sumaSubTotal();
										       sumaTotal();
											};
	
	cantidad.value = "";
	y4.appendChild(cantidad);
	
	var costo = $('costo_1').cloneNode(false);
	costo.setAttribute('id', 'costo_' + i);
	onchange = "";
	costo.onchange = function(){
                                           calculaIva($('cantidad_'+i).value,this.value, this.id); 
                                           montoTotal($('cantidad_'+i).value,this.value, this.id); 
                                           sumaIva(); 
                                           sumaSubTotal();
                                           sumaTotal();
                                        };
	costo.value = "";
	y5.appendChild(costo);
	
	var iva = $('iva_1').cloneNode(false);
	iva.setAttribute('id', 'iva_' + i);
	iva.value = "";
	y6.appendChild(iva);

	var prodSeleccionado = $('prodSeleccionado_1').cloneNode(false);
	prodSeleccionado.setAttribute('id', 'prodSeleccionado_' + i);
	prodSeleccionado.value = 0;
	y6.appendChild(prodSeleccionado);

	var total = $('total_1').cloneNode(false);
	total.setAttribute('id', 'total_' + i);
	total.value = "";
	y7.appendChild(total);
}

function delPR() {
	if($('tablitaProd').rows.length <= 3){
		$('productos_1').value = 0;
		$("prodSeleccionado_1").value = 0;
		$('id_producto_1').value = "";
		$('unidad_medida_1').value = "";
		$('cantidad_1').value = "";
		$('costo_1').value = "";
		$('iva_1').value = "";
		$('total_1').value = "";
		alert('No puede eliminar mas productos');
	}else{
		var montoTotal = 0;
		var x = $('tablitaProd').deleteRow($('tablitaProd').rows.length - 1);
		sumaIva();
		sumaSubTotal();
		sumaTotal();
	}
}

function calculaIva(cantidad, costo, id){
	var fila = getFila(id)
	var iva = parseFloat(usaFloat($("setIva").value));
	$('iva_'+ fila).value = muestraFloat((iva * parseInt(cantidad) * parseFloat(usaFloat(costo)) ) / 100);
}

function montoTotal(cantidad, costo, id){
	var fila = getFila(id);
	$('total_'+ fila).value = muestraFloat(parseInt(cantidad) * parseFloat(usaFloat(costo)));
}

function sumaIva(){
	var nodosIva = document.getElementsByClassName('conjuntoIva');
	var sumaIva = 0;
	$A(nodosIva).each(function(e){ sumaIva += parseFloat(usaFloat(e.value)); });
	$('total_iva_fact').value = muestraFloat(sumaIva);
}

function sumaSubTotal(){
	var nodoSubTotal = document.getElementsByClassName('total');
	var sumaSubTotal = 0;
	$A(nodoSubTotal).each(function(e){ sumaSubTotal += parseFloat(usaFloat(e.value)); });
	$('subtotal').value = muestraFloat(sumaSubTotal);
}

function sumaTotalxx(){
	var nodosTotal = $('total_iva', 'subtotal');
	var sumaTotal = 0;
	$A(nodosTotal).each(function(e){ sumaTotal += parseFloat(usaFloat(e.value)); });
	$('total_general_fact').value = muestraFloat(sumaTotal);
}

function chequeaMontos(){
	tg = $('subtotal').value.split(',');
	tg1 = tg[0];
	tg2 = tg[1];
	mtp = $('monto_total_partidas').value.split(',');
	mtp1 = mtp[0];
	mtp2 = mtp[1];
//	alert('tg1: ' + tg1 + ' tg2: '+ tg2 + ' largo: ' + tg.length);
	if(tg.length == 2)
		totalGeneral = $('subtotal').value;
	else if(tg.length == 1)
		totalGeneral = $('subtotal').value + ',00';

	if(mtp.length == 2)
		montoTotalPartidas = $('monto_total_partidas').value;
	else if(mtp.length == 1)
		montoTotalPartidas = $('monto_total_partidas').value + ',00';
		
//	if(usaFloat(montoTotalPartidas) > usaFloat(totalGeneral) ){
		validadorTipoDoc()
		
//	}else
		//alert('No Hay disponibilidad suficiente en la Partida Presupuestaria');
}

function toggleProvCiudadano(){
	var proveedores = document.getElementsByClassName('proveedores');
	var ciudadano = document.getElementsByClassName('ciudadano');
	var tipdoc = $('tipdoc').value;
	
	if(tipdoc == '002'){
		//$A(proveedores).each( function(el){  el.setAttribute('disabled','disabled'); el.value='';});
		$A(proveedores).each(function(el){  el.removeAttribute('disabled'); });
		$('buscadorpro').style.display = 'inline';
	}else if(tipdoc == '009'){
		//$A(proveedores).each( function(el){  el.setAttribute('disabled','disabled'); el.value='';});
		$A(proveedores).each(function(el){  el.removeAttribute('disabled'); });
		$('buscadorpro').style.display = 'inline';
	}else{
		$A(ciudadano).each( function(el){ el.setAttribute('disabled','disabled'); el.value='';});
		$A(proveedores).each( function(el){  el.setAttribute('disabled','disabled'); el.value='';});
	}
}

function actapr(elemento){
	if(elemento == 'Actualizar'){
		$('accion').value = 'Actualizar';
	}else if(elemento == 'Guardar'){
		$('accion').value = 'Guardar';
	}else if(elemento == 'Aprobar'){
		$('accion').value = 'Aprobar';
	}else
		$('accion').value = 'Anular';	 
	chequeaMontos(); 
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
// trae el ciudadano de la tabla de proveedores
function traeCiudadanoDesdeXML2(id_ciudadano){
	$('id_proveedor').value = id_ciudadano;
	var url = 'xmlTraeCiudadano.php';
	var pars = 'id=' + id_ciudadano;
	var myAjax = new Ajax.Request(
		url, 
		{
			method: 'get', 
			parameters: pars,
			onComplete: traeCiudadano
		});
}

function traeProveedor(originalRequest){
	
	var xmlDoc = originalRequest.responseXML;
	var x = xmlDoc.getElementsByTagName('proveedor');
	for(j=0;j<x[0].childNodes.length;j++){
		if (x[0].childNodes[j].nodeType != 1) continue;
		var nombre = x[0].childNodes[j].nodeName
		cadena = unescape(x[0].childNodes[j].firstChild.nodeValue);
		cadenafinal = cadena.replace(/\+/gi," ");
		$(nombre).value = cadenafinal;
	}
}

function traeCiudadano(originalRequest){
	
	var xmlDoc = originalRequest.responseXML;
	var x = xmlDoc.getElementsByTagName('proveedor');
	for(j=0;j<x[0].childNodes.length;j++){
		if (x[0].childNodes[j].nodeType != 1) continue;
		var nombre = x[0].childNodes[j].nodeName
		$(nombre).value = x[0].childNodes[j].firstChild.nodeValue;
	}
}


//VERSION PARA BUSCAR POR EL RIF//
function traeProveedorDesdeXML2(rif){
	$('proveedores').value = "0";
	var url = 'xmlTraeProveedor.php';
	var pars = 'rif=' + rif;
	var myAjax = new Ajax.Request(
		url, 
		{
			method: 'get', 
			parameters: pars,
			onComplete: traeProveedor
		});
}

/*function traeProveedorDesdeXML(id){
	$('id_proveedor').value = id;
	
	var url = 'xmlTraeProveedor.php';
	var pars = 'id=' + id;
	var myAjax = new Ajax.Request(
		url, 
		{
			method: 'get', 
			parameters: pars,
			onComplete: function(peticion){
				
				var doc = (peticion.responseXML);
				//alert(doc);
				var hash = doc.asHash();
				doc.proveedor.each ( function(e) { 
					$('nombre').value = e.nombre; 
					$('direccion').value = e.direccion; 
				});
			}
		});
}*/

/* Metodos utilizados en el buscador */
function busca(id_ue, id_tipdoc, id_proveedor, observaciones, fecha_desde, fecha_hasta, nrodoc, pagina){
	var url = 'updater_busca_orden_st.php';
	var pars = '&id_ue=' + id_ue + '&id_tipdoc=' + id_tipdoc+ '&id_proveedor=' + id_proveedor+ '&observaciones=' + observaciones+ '&pagina=' + pagina;
	pars += '&nrodoc=' + nrodoc + '&fecha_desde=' + fecha_desde+ '&fecha_hasta=' + fecha_hasta +'&ms='+new Date().getTime();
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
	$F('busca_tipdoc'),
	$F('busca_proveedores'), 
	$F('busca_observaciones'), 
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta'),
	$F('busca_nrodoc'),1); 
});
Event.observe('busca_tipdoc', "change", function () { 
	busca($F('busca_ue'), 
	$F('busca_tipdoc'),
	$F('busca_proveedores'), 
	$F('busca_observaciones'), 
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta'),
	$F('busca_nrodoc'),1); 
});
Event.observe('busca_proveedores', "change", function () { 
	busca($F('busca_ue'), 
	$F('busca_tipdoc'),
	$F('busca_proveedores'), 
	$F('busca_observaciones'), 
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta'),
	$F('busca_nrodoc'),1); 
});
Event.observe('busca_observaciones', "keyup", function () { 
	busca($F('busca_ue'), 
	$F('busca_tipdoc'),
	$F('busca_proveedores'), 
	$F('busca_observaciones'), 
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta'),
	$F('busca_nrodoc'),1); 
});
Event.observe('busca_nrodoc', "keyup", function () { 
	busca($F('busca_ue'), 
	$F('busca_tipdoc'),
	$F('busca_proveedores'), 
	$F('busca_observaciones'), 
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
			$F('busca_tipdoc'),
			$F('busca_proveedores'), 
			$F('busca_observaciones'), 
			$F('busca_fecha_desde'), 
			$F('busca_fecha_hasta'),
			$F('busca_nrodoc'), 1); 
		} else {
			alert("Fecha incorrecta");
			$(fecha).value = "";
		}
	}else if(fecha.value != '') {
		alert("Fecha incorrecta");
		$(fecha).value = "";
	}
}

function validadorTipoDoc () {
	var vfocus;
	sw=0; 
	var tipdoc = $('tipdoc').value;
	
	if (tipdoc=='002'){	
		
		if (document.form1.proveedores.value=='' ||
			
			document.form1.proveedores.value=='0') { 
			$('error_prov').innerHTML = '*';
			vfocus = 'proveedores';
			sw=1; 
		
		}else{ 
			
			$('error_prov').innerHTML = '';
			
		}
			
		if (document.form1.nro_factura.value=='' ||
			
			document.form1.nro_factura.value=='0') { 
			$('error_nro_fact').innerHTML = '*';
			vfocus = 'nro_factura';
			sw=1; 
			
		} else { 
			
			$('error_nro_fact').innerHTML = '';
			
		}
		
			
	}else if (tipdoc=='009'){
	
		if (document.form1.proveedores.value=='' ||
			document.form1.proveedores.value=='0') { 
			$('error_prov').innerHTML = '*';
			vfocus = 'proveedores';
			sw=1; 
		} else { 
			$('error_prov').innerHTML = '';
		}
	}					
	
	if (sw==1 && (tipdoc=='002')) {
		
		document.form1.proveedores.focus();	
		return;
	
	}else if(sw==1 && tipdoc=='009'){
		
		document.form1.proveedores.focus();
		return;
		
	}else{
	
		validate();
	
	}
}

function mostrar_ventana(pc){
	if(pc==1)
		var tipo = "('P', 'A')";
	if(pc==2)
		var tipo = "('B')";
	 
	var url = 'buscar_proveedores.php';
	var pars = 'tipo=&status=&pc='+pc+'&ms='+new Date().getTime();
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
	t = setTimeout("buscaProveedor()", 800);
}

function buscaProveedor()
{
	var tipo;

	if ($('pc').value==1)
		tipo = "('P','A')";
	else
		tipo = "('B')";

	var url = 'buscar_proveedores.php';
	var pars = 'pc='+$('pc').value+'&tipo='+$('tipo_prov').value+'&status=&rif='+$('search_rif_prov').value+ '&nombre='+ $('search_nombre_prov').value+'&opcion=2&ms'+new Date().getTime();
		
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
//Esta funcion se usa para traer el beneficiario en caso de que sea una orden de trabajo
function selDocumento2(id, nombre){
	
	var aux = nombre.split('-');
	$('id_ciudadano').value = aux[1];
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
	//mygridpp.clearAll();
	$('bcategorias').style.display = 'inline';

}

function buscaNroDoc(boton){
	//alert(boton);
	/*if(boton=='Guardar'){
		var tabla = 'puser.orden_servicio_trabajo';
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
						actapr(boton);
					
				}
			}
		);
	}else{*/
		if(parseFloat($('total_general').value) !== parseFloat($('total_general_fact').value)){
			alert('El monto de la factura y el total de las partidas no coinciden');
			return false;
		/*} else if(parseFloat($('total_iva').value) !== parseFloat($('total_iva_fact').value)){
			alert('El monto del IVA no coincide con el de la imputacion presupuestaria');
			return false;*/
		} else
			actapr(boton);
		//}
}

function traerPartidasSeleccionada(rowId){

	var cp = mygridpp.cells(rowId,0).getValue();
	var pp = mygridpp.cells(rowId,1).getValue();
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

function generaTotal(rowId){
		if (mygridpp.cells(rowId,4).getValue()==''){
			alert("Debe seleccionar un valor para el IVA");
			return false;
		} else if (parseFloat(mygridpp.cells(rowId,3).getValue()) > parseFloat(usaFloat(mygridpp.cells(rowId,2).getValue()))){
				alert("El monto excento no puede ser mayor que el monto a imputar");
				return false;
		} else { 
			var costo = parseFloat(usaFloat(mygridpp.cells(rowId,2).getValue()));
			var excento = parseFloat(mygridpp.cells(rowId,3).getValue());
			var iva = parseInt(mygridpp.cells(rowId,4).getValue());
			var costo_neto = costo - excento;
			//alert(iva);
			var impuesto = costo_neto * (iva/100);
			var total = costo + impuesto;
					/*var subtotal = costo * cant;
					$('subtot').value = subtotal;*/
			mygridpp.cells(rowId,5).setValue(muestraFloat(impuesto.toFixed(2)));		
			mygridpp.cells(rowId,6).setValue(muestraFloat(total.toFixed(2)));
			sumaTotal();
	   }
	}
	
	function sumaTotal(){
	var totalpar = 0;
	var totaliva = 0;
	for(j=0;j<mygridpp.getRowsNum();j++){
		if(mygridpp.getRowId(j)!= undefined){
			totalpar += usaFloat(mygridpp.cells(mygridpp.getRowId(j),6).getValue());
			totaliva += usaFloat(mygridpp.cells(mygridpp.getRowId(j),5).getValue());
		}
	}
	$('total_iva').value = (isNaN(totaliva))? '0' : muestraFloat(totaliva);
	$('total_general').value = (isNaN(totalpar))? '0' : muestraFloat(totalpar);
	//$('montoCOB').value = (isNaN(totalRequis))? '0' : muestraFloat(totalRequis);

}


</script>

<?

$validator->create_message("error_fecha", "fecha", "*");
//$validator->create_message("error_nrodoc", "nrodoc", "*");
//$validator->create_message("error_fechadoc", "fechadoc", "*");
$validator->create_message("error_fecha_entrega", "fecha_entrega", "*");
$validator->create_message("error_lugar_entrega", "lugar_entrega", "*");
//$validator->create_message("error_obser", "observaciones", "*");
$validator->create_message("error_cond_pago", "condicion_pago", "*");
$validator->create_message("error_ue", "unidad_ejecutora", "*");
$validator->print_script();
require ("comun/footer.php");
?>
