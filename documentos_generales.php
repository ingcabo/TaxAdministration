<?
include('adodb/adodb-exceptions.inc.php');
require ("comun/ini.php");
// Creando el objeto contrato_servicio
$oDocGen = new documentos_generales;
$accion = $_REQUEST['accion'];
//die("=".$_POST['nrodoc']);
if($accion == 'Guardar'){
	$oDocGen->add($conn, 
										$_POST['tipdoc'],
										$_POST['unidad_ejecutora'],
										$_POST['proveedores'],
										$usuario->id,
										$_POST['descripcion'],
										$_POST['observaciones'],
										guardafecha($_POST['fecha']),
										$_POST['servicio'],
										$_POST['nrodoc'],
										$auxNrodoc
										);
}elseif($accion == 'Aprobar'){
	$oDocGen->aprobar($conn, 
									$_POST['id'], 
									$usuario->id,
									$_POST['unidad_ejecutora'],
									$_POST['proveedores'],
									date("Y"),
									$_POST['descripcion'],
									$_POST['tipdoc'],
									guardafecha($_POST['fecha']),
									'1',
									$_REQUEST['servicio'],
									$_POST['nrodoc'],
									$auxNrodoc,
									$escEnEje);
}elseif($accion == 'Actualizar'){
	$oDocGen->set($conn, 
									$_POST['id'], 
									$_POST['tipdoc'],
									$_POST['unidad_ejecutora'],
									$_POST['proveedores'],
									$usuario->id,
									$_POST['descripcion'],
									$_POST['observaciones'],
									guardafecha($_POST['fecha']),
									$_REQUEST['servicio'],
									$_POST['nrodoc'],
									$auxNrodoc
									);
}elseif($accion == 'del'){
	$oDocGen->del($conn, $_POST['id']);
}elseif($accion == 'Anular'){
	$oDocGen->anular($conn, $_POST['id'],
									  $usuario->id,
									  $_POST['unidad_ejecutora'],
									  date("Y"),
									  $_POST['descripcion'],
									  $_POST['tipdoc'],
									  guardafecha($_POST['fecha']),
									  $_REQUEST['status'],
									  $_POST['proveedores'],
									  $_REQUEST['servicio'],
									  $_POST['nrodoc'],
									  $escEnEje);
}
$msj = $oDocGen->msj; // lleno esta variable con el mensaje de la operacion llevada a cabo
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>

<script type="text/javascript">var mygrid,i=0, ipp=0</script>
<br />
<span class="titulo_maestro">Documentos Generales</span>
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
								<img border="0" alt="Fecha" src="images/calendarA.png" width="20" height="20" />
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
							onchange="validafecha(this);"/>
						</td>
						<td>
							<a href="#" id="boton_busca_fecha_hasta" onclick="return false;">
								<img border="0" alt="Fecha" src="images/calendarA.png" width="20" height="20" />
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
	var pars = 'cp=' + cp + '&pp=' + pp + '&fila=' + fila;
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
	if(elemento.value == 'Actualizar')
		$('accion').value = 'Actualizar';
	else if(elemento.value == 'Aprobar')
		$('accion').value = 'Aprobar'; 
	 else
	 	$(accion).value = 'Anular';
	 //validate(); 
}

/* Metodos utilizados en el buscador */
function busca(id_ue, id_proveedor, descripcion, fecha_desde, fecha_hasta, nrodoc,pagina){
	var url = 'updater_busca_documentos_generales.php';
	var pars = '&id_ue=' + id_ue + '&id_proveedor=' + id_proveedor+ '&descripcion=' + descripcion;
	pars += '&nrodoc=' + nrodoc + '&fecha_desde=' + fecha_desde+ '&fecha_hasta=' + fecha_hasta + '&pagina=' + pagina;
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

//Nueva revision************ 27/09/2006

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
	var pars = 'filtro=&cp=' + cp +'&ms='+new Date().getTime();
		
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
	var pars = 'filtro=&cp=' + $('cp').value +'&nombre='+$('search_nombre_pp').value+'&codigo='+$('search_cod_pp').value+'&opcion=2&ms='+new Date().getTime();
		
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

	//MANEJO DE LAS PARTIDAS PROGRAMATICAS
	function AgregarCS(){
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
		
	}else if($('montoip').value=="" || parseFloat($('montoip').value)<=0){
		
		alert("Primero debe colocar el monto de la Imputacion Presupuestaria.");
		return;
	
	}else if(parseFloat($('disponible').value) < usaFloat($('montoip').value)){
		alert("El monto disponible en la partida es menor al requerido");
		$('montoip').value='0,00';
		return;
	
	}else{
		
		for(j=0;j<mygridcs.getRowsNum();j++){
			
			if (mygridcs.getRowId(j)!=undefined){
				if (mygridcs.cells(mygridcs.getRowId(j),'0').getValue() == $('categorias_programaticas').value && mygridcs.cells(mygridcs.getRowId(j),'1').getValue() == $('partidas_presupuestarias').value){
						
					alert('Esta partida ya ha sido seleccionada, por favor seleccione otra partida');
					return false;

				}
			
			}
			
			
		}
		
		/*mygridco.getCombo(0).put(JsonData[j]['id_categoria_programatica'],JsonData[j]['categoria_programatica']);
		mygridco.getCombo(1).put(JsonData[j]['id_partida_presupuestaria'],JsonData[j]['partida_presupuestaria']);*/
		mygridcs.addRow($('idParCat').value,$('categorias_programaticas').value+","+$('partidas_presupuestarias').value+","+usaFloat($('montoip').value)+",0,,0,0");
		i++;
		sumaTotal();
		$('montoip').value='0,00';
	}
} 

	function EliminarCS(){
	mygridcs.deleteRow(mygridcs.getSelectedId());
	sumaTotal();
	
}

	function sumaTotal_old(){
	var totalPartidas = 0;
	for(j=0;j<i;j++){
		if(mygridcs.getRowId(j)!= undefined){
			totalPartidas += parseFloat(mygridcs.cells(mygridcs.getRowId(j),2).getValue());
		}
	}
	$('montoCS').value = (isNaN(totalPartidas))? '0' : muestraFloat(totalPartidas);
	$('montoCSERV').value = (isNaN(totalPartidas))? '0' : muestraFloat(totalPartidas);
	calculaImp();

}

function Guardar()
	{
		var JsonAux,servicio=new Array;
			mygridcs.clearSelection()
			for(j=0;j<mygridcs.getRowsNum();j++)
			{
				if(!isNaN(mygridcs.getRowId(j)))
				{
					servicio[j] = new Array;
					servicio[j][0]= mygridcs.cells(mygridcs.getRowId(j),0).getValue();
					servicio[j][1]= mygridcs.cells(mygridcs.getRowId(j),1).getValue();
					servicio[j][2]= mygridcs.cells(mygridcs.getRowId(j),2).getValue();
					servicio[j][3]= mygridcs.cells(mygridcs.getRowId(j),3).getValue();
					servicio[j][4]= mygridcs.cells(mygridcs.getRowId(j),4).getValue();
					servicio[j][5]= mygridcs.cells(mygridcs.getRowId(j),5).getValue();
					servicio[j][6]= mygridcs.cells(mygridcs.getRowId(j),6).getValue();
					servicio[j][7]= mygridcs.getRowId(j);			
				}
			}
			JsonAux={"servicio":servicio};
			$("servicio").value=JsonAux.toJSONString(); 
			if (parseFloat($('montoCS').value) < 1){
				alert("Debe seleccionar las partidas para imputar los montos"); 
				return false;
			} else {
				validate();
			}	
	}
	
	function ver_partpre(){
		Effect.toggle('partpreDiv', 'blind');
	}	
	
	function traeProveedorDesdeXML2(rif_proveedor){
	//$('id_proveedor').value = id_proveedor;
	
	var url = 'xmlTraeProveedor.php';
	var pars = 'rif=' + rif_proveedor;
	var myAjax = new Ajax.Request(
		url, 
		{
			method: 'get', 
			parameters: pars,
			onComplete: traeProveedor
		});
}

	function mostrar_ventana(){
	 
	//var tipo = "('B')";
	var url = 'buscar_proveedores.php';
	var pars = 'status=&tipo=&ms='+new Date().getTime();
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
	//var tipo = "('B')";

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
	//mygridcs.clearAll();
	$('bcategorias').style.display = 'inline';

}

function mostrarBuscarCat2(){
	$('bcategorias').style.display = 'inline';

}

function buscaNroDoc(boton){
	//alert(boton);
	/*if(boton=='Guardar'){
		var tabla = 'puser.contrato_servicio';
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
		var montoImp = parseFloat(usaFloat($('montoCS').value)) * (valor/100);
		
		$('montoIva').value = muestraFloat(montoImp);	
		
	}
	
	function generaTotal(rowId){
		if (mygridcs.cells(rowId,4).getValue()==''){
			alert("Debe seleccionar un valor para el IVA");
			return false;
		} else if (parseFloat(mygridcs.cells(rowId,3).getValue()) > parseFloat(usaFloat(mygridcs.cells(rowId,2).getValue()))){
				alert("El monto excento no puede ser mayor que el monto a imputar");
				return false;
		} else { 
			var costo = parseFloat(mygridcs.cells(rowId,2).getValue());
			var excento = parseFloat(mygridcs.cells(rowId,3).getValue());
			var iva = parseInt(mygridcs.cells(rowId,4).getValue());
			var costo_neto = costo - excento;
			//alert('costo: '+costo+' excento: '+excento+' iva: '+iva);
			var impuesto = costo_neto * (iva/100);
			var total = costo + impuesto;
			//alert('impuesto: '+impuesto+' total: '+total);
					/*var subtotal = costo * cant;
					$('subtot').value = subtotal;*/
			mygridcs.cells(rowId,5).setValue(impuesto.toFixed(2));		
			mygridcs.cells(rowId,6).setValue(total.toFixed(2));
			sumaTotal();
	   }
	}
	
	function sumaTotal(){
	var totalpar = 0;
	var totaliva = 0;
	for(j=0;j<mygridcs.getRowsNum();j++){
		if(mygridcs.getRowId(j)!= undefined){
			totalpar += parseFloat(mygridcs.cells(mygridcs.getRowId(j),6).getValue());
			totaliva += parseFloat(mygridcs.cells(mygridcs.getRowId(j),5).getValue());
		}
	}
	$('total_iva').value = (isNaN(totaliva))? '0' : muestraFloat(totaliva);
	$('montoCS').value = (isNaN(totalpar))? '0' : muestraFloat(totalpar);
	$('montoCSERV').value = (isNaN(totalpar))? '0' : muestraFloat(totalpar);

}

	function traerPartidasSeleccionada(rowId){

	var cp = mygridcs.cells(rowId,0).getValue();
	var pp = mygridcs.cells(rowId,1).getValue();
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

</script>
<div id="xxx"></div>
<?
//$validator->create_message("error_nrodoc", "nrodoc", "*");
$validator->create_message("error_fecha", "fecha", "*");
$validator->create_message("error_tipo_doc", "tipdoc", "*");
$validator->create_message("error_desc", "descripcion", "*");
//$validator->create_message("error_obser", "observaciones", "*");
$validator->create_message("error_prov", "proveedores", "*");
$validator->create_message("error_ue", "unidad_ejecutora", "*"); 
//$validator->create_message("error_iva", "porc_iva", "*",14);
$validator->print_script();
require ("comun/footer.php");
?>
