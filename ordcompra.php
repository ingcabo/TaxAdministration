<?
require ("comun/ini.php");
// Creando el objeto Proveedores
$oOrden_compra = new ordcompra;
//echo($_REQUEST['accion']);
$accion = $_REQUEST['accion'];
$today=date("d/m/Y");
$rif=$_REQUEST['rif_letra']."-".$_REQUEST['rif_numero']."-".$_REQUEST['rif_cntrl'];
	
	#ACCION QUE SE EJECUTA CUANDO SE GENERA UNA ORDEN DE COMPRA#
	if($accion == 'Guardar'){
		
		if($oOrden_compra->add($conn, 
								guardafecha($_POST['fecha']),
								$_POST['unidad_ejecutora'],
								$_REQUEST['anopres'], 
								guardafecha($_REQUEST['fechaent']),
								$_REQUEST['lugar'], 
								$_REQUEST['condpag'],
								guardafecha($_REQUEST['fechasol']), 
								$_REQUEST['nrosol'],
								$_POST['proveedores'],
								$_REQUEST['observ'],
								//$_REQUEST['contenedor_partidas'],
								$_REQUEST['productos_contenedor'],
								$_REQUEST['nrodoc'],
								'001',
								$auxNrodoc))
			$msj = REG_ADD_OK; 
		else
			$msj = ERROR;

	#ACCION QUE SE REALIZA CUANDO SE ACTUALIZA LA ORDEN DE COMPRA#
	}elseif($accion == 'Actualizar'){

		if($oOrden_compra->set($conn, 
								$_POST['id'],
								guardafecha($_POST['fecha']),
								$_REQUEST['anopres'],
								guardafecha($_REQUEST['fechaent']),
								$_REQUEST['lugar'],
								$_REQUEST['condpag'],
								guardafecha($_REQUEST['fechasol']),
								$_REQUEST['nrosol'],
								$_REQUEST['observ'],
								$_POST['unidad_ejecutora'],
								//$_REQUEST['contenedor_partidas'],
								$_REQUEST['productos_contenedor'],
								$_POST['nrodoc'],
								'001',
								$auxNrodoc,
								$_POST['id_proveedor']))
			$msj = REG_SET_OK; 
		else
			$msj = ERROR;
		
	#ACCION QUE SE REALIZA CUANDO SE APRUEBA LA ORDEN DE PAGO#	
	}elseif($accion == 'Aprobar'){
		
		if($oOrden_compra->aprobar($conn, 
									$_POST['id'],
									$_POST['nrif'],
									$usuario->id,
									$_POST['unidad_ejecutora'],
									date("Y"),
									$_POST['observ'],
									$nrodoc,
									'001',
									'',
									guardafecha($_POST['fecha']),
									guardafecha($_POST['fechaent']),
									'1',																
									$_POST['proveedores'],
									$_POST['categorias_programaticas'],
									//$_REQUEST['contenedor_partidas'],
									$_REQUEST['productos_contenedor'],
									$_REQUEST['nrodoc'],
									$escEnEje,
									$auxNrodoc))
			$msj = ORDEN_APROBADA;
		else
			$msj = ERROR;
	
	#ACCION DE ELIMIMAR UNA ORDEN DE COMPRA#		
	}elseif($accion == 'del'){
		if($oOrden_compra->del($conn, $_GET['id']) && movimientos_presupuestarios::del_relacion($conn,$_GET['nrodoc']) && movimientos_presupuestarios::del($conn,$_GET['nrodoc']) && movimientos_presupuestarios::del_relacion_productos($conn, $_GET['nrodoc']))
			$msj = REG_DEL_OK;
		else
			$msj = ERROR;
	}elseif ($accion == 'Anular'){
		if($oOrden_compra->anular($conn, $_POST['id'],$usuario->id,$_POST['unidad_ejecutora'],date("Y"),$_POST['observ'],
									'001', guardafecha($_POST['fecha']), $_REQUEST['status'], $_POST['proveedores'],
									$_REQUEST['productos_contenedor'], $_REQUEST['nrodoc'],$escEnEje)){
			$msj = ORDEN_COMPRA_ANULADA;
		}else{
			$msj = ERROR;
		}
	}

//Seccion paginador
	$page_size = 25;
if ($_GET['pg'])
	$start_record=($_GET['pg'] * $page_size) - $page_size;
else
	$start_record=0;

	$cOrden_compra=$oOrden_compra->get_all($conn, $start_record,$page_size);
	$pag=new paginator($oOrden_compra->total,$page_size, self($_SERVER['SCRIPT_NAME']));
	$i=$pag->get_total_pages();
	
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<script type="text/javascript">var mygrid,i=0, ipp=0</script>
<br />
<span class="titulo_maestro">Orden de Compra</span>
<div id="formulario">
<!--<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>-->
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
			"SELECT DISTINCT id,id||' - '||descripcion AS descripcion FROM unidades_ejecutoras")?></td>
		</tr>
		<tr>
			<td>Proveedor</td>
			<td>Observaciones</td>
		</tr>
		<tr>
			<td>
			<?=helpers::combo_ue_cp($conn, 'busca_proveedores','','','','','','',
			"SELECT id, nombre AS descripcion FROM proveedores ORDER BY descripcion")?></td>
			<td><input style="width:300px" type="text" name="busca_fecha" id="busca_observaciones" /></td>
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
<br />
<div style="height:40px;padding-top:10px;">
<p id="cargando" style="display:none;margin-top:0px;">
  <img alt="Cargando" src="images/loading.gif" /> Cargando...
</p>
</div>
<!-- <div id="xx"></div> -->
<!-- <a href="#" onclick="alert($('formulario').innerHTML)">AAAA</a> -->

<script type="text/javascript">


var i=0;
var t;

//ELIMINAR UNA FILA EN EL GRID DE PRODUCTOS//
function Eliminar(){
	mygrid.deleteRow(mygrid.getSelectedId());
	
	sumaTotal();
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
					mygrid.cells(rowId,'2').setValue(jsonData.unidad_medida);
					mygrid.cells(rowId,'4').setValue(jsonData.ultimo_costo);
					
					}
				}
			);
		}
	}
}

//FUNCION QUE PERMITE CALCULAR EL MONTO TOTAL POR PRODUCTOS//
function calcularprecio(rowId,cellInd){
	
	if(cellInd=='3'){
				
		var iva = 14;
		var totalIva = ((iva * mygrid.cells(rowId,'3').getValue() * mygrid.cells(rowId,'4').getValue() ) / 100);
		mygrid.cells(rowId,'5').setValue(totalIva);
		
	
		var totalprod = mygrid.cells(rowId,'3').getValue() * mygrid.cells(rowId,'4').getValue();
		mygrid.cells(rowId,'6').setValue(totalprod);
		
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
			sumaIva += parseFloat(mygrid.cells(j,5).getValue()); 
		}
	}
	$('total_iva').value = muestraFloat(sumaIva);
}

//PERMITE CALCULAR EL SUBTOTAL DE LOS PRODUCTOS//
function sumaSubTotalProductos(){
	var sumaSubTotal = 0;
	for(j=0;j<i;j++){
		if(mygrid.getRowIndex(j)!=-1){
			sumaSubTotal += parseFloat(mygrid.cells(j,6).getValue()); 
		}
	}
	$('subtotal').value = muestraFloat(sumaSubTotal);
}

//PERMITE CALCULAR EL TOTAL DE PRODUCTOS//
function sumaTotalProductos(){
	
	sumaTotal = usaFloat($('subtotal').value) + usaFloat($('total_iva').value); 
	$('total_general').value = muestraFloat(sumaTotal);
}

//ESTA FUNCION PERMITE QUE LOS DATOS DE PROPDUCTOS SE GUARDE EN BASE DE DATOS//
function Guardar(boton)
	{
		var JsonAux,ProductosAux=new Array;
		var aux = 0;
		if($('total_general').value!=0){
			mygrid.clearSelection()
			for(j=0;j<mygrid.getRowsNum();j++)
			{
				if(mygrid.cells(mygrid.getRowId(j),7).getValue()!=''){
				 if(!isNaN(mygrid.getRowId(j)))
				 {
				   ProductosAux[j] = new Array;
				   ProductosAux[j][0]= mygrid.cells(mygrid.getRowId(j),0).getValue();//id prod
				   ProductosAux[j][1]= mygrid.cells(mygrid.getRowId(j),1).getValue();//id cp
				   ProductosAux[j][2]= mygrid.cells(mygrid.getRowId(j),2).getValue();//id pp
				   ProductosAux[j][3]= mygrid.cells(mygrid.getRowId(j),3).getValue();//cantidad
				   ProductosAux[j][4]= mygrid.cells(mygrid.getRowId(j),4).getValue();//precio u
				   ProductosAux[j][5]= mygrid.cells(mygrid.getRowId(j),5).getValue();//iva %
				   ProductosAux[j][6]= mygrid.cells(mygrid.getRowId(j),6).getValue();//iva precio
				   ProductosAux[j][7]= mygrid.cells(mygrid.getRowId(j),7).getValue();//costo tot
				   ProductosAux[j][8]= mygrid.getRowId(j);			
				 }
				}else{
					alert('Los montos en la imputacion no pueden estar vacios');
					aux = 1;
				}
				
			}
			JsonAux={"productos":ProductosAux};
			$("productos_contenedor").value=JsonAux.toJSONString();
			if((aux!=1) && (mygrid.getRowsNum()!=0)){
				buscaNroDoc(boton);
			}
				
		}else{
			alert('Debe seleccionar productos para generar la orden de compra');
			}
}

function elimina_punto (valor){
	 var valornew = valor.replace(/\./g, ",")
	 return valornew;
} 
	


//MANEJO DE LAS CATEGORIAS PROGRAMATICAS//
function AgregarCP(){
	if ($('unidad_ejecutora').value =="0"){
		
		alert("Primero debe Seleccionar una Unidad Ejecutora.");
		return;
		
	}else if($('categorias_programaticas').value=="0"){
	
		alert("Primero debe Seleccionar una Categoria Programatica.");
		return;
		
	}else if($('partidas_presupuestarias').value=="0"){
		
		alert("Primero debe Seleccionar una Partida Presupuestaria.");
		return;
		
	}else if($('montoip').value=="" || $('montoip').value=="0,00"){
		
		alert("Primero debe colocar el monto de la Partida Presupuestaria y debe ser mayor a 0.");
		return;
	}/*else if(parseFloat($('montoip').value) > parseFloat($('disponible').value) ){
		
		alert("El Monto sobrepasa el monto disponible para esta partida.");
		$('montoip').value = 0;
		return;
		
	}*/else{
		
		for(j=0;j<ipp;j++){
		
			if(mygridpp.getRowId(j)!= undefined){
				
				if (mygridpp.cells(mygridpp.getRowId(j),'0').getValue() == $('categorias_programaticas').value && mygridpp.cells(mygridpp.getRowId(j),'1').getValue() == $('partidas_presupuestarias').value){
						
					alert('Esta partida ya ha sido seleccionada, por favor seleccione otra partida');
					return false;

				}
			}
		}
		
		
		mygridpp.addRow($('idParCat').value,$('categorias_programaticas').value+";"+$('partidas_presupuestarias').value+";"+$('montoip').value);
		ipp++;
		sumaTotalPartidas();
		$('montoip').value="0,00";
	}
}

function EliminarCP(){
	
	mygridpp.deleteRow(mygridpp.getSelectedId());
	sumaTotalPartidas();
	
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

function sumaTotalPartidas(){

	var totalPartidas = 0;
	for(j=0;j<ipp;j++){
		
		if(mygridpp.getRowId(j)!= undefined){
		
			totalPartidas += usaFloat(mygridpp.cells(mygridpp.getRowId(j),2).getValue()); 
		}
	}
	$('monto_total_partidas').value = (isNaN(totalPartidas))? '0' : muestraFloat(totalPartidas,'2');
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


/*Parte Vieja del Codigo Rev*/
/***************       Seccion de las Partidas ******************************/

function traeProveedorDesdeXML(id_proveedor){
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

function actapr(elemento){
	//alert(elemento);
	if(elemento == 'Actualizar')
		$('accion').value = 'Actualizar';
	else if(elemento == 'Aprobar')
		$('accion').value = 'Aprobar';
	else if (elemento == 'Guardar')
		('accion').value = 'Guardar';
	else
		$('accion').value = 'Anular';	 
	validate();
}

/* Metodos utilizados en el buscador */
function busca(id_ue, id_proveedor, observaciones, fecha_desde, fecha_hasta, nrodoc, pagina){
	var url = 'updater_busca_orden_compra.php';
	var pars = '&id_ue=' + id_ue + '&id_proveedor=' + id_proveedor+ '&observaciones=' + observaciones;
	pars += '&nrodoc=' + nrodoc + '&fecha_desde=' + fecha_desde+ '&fecha_hasta=' + fecha_hasta + '&pagina=' + pagina + '&ms='+new Date().getTime();
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
	$F('busca_observaciones'), 
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta'),
	$F('busca_nrodoc'),1); 
});
Event.observe('busca_proveedores', "change", function () { 
	busca($F('busca_ue'), 
	$F('busca_proveedores'), 
	$F('busca_observaciones'), 
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta'),
	$F('busca_nrodoc'),1); 
});
Event.observe('busca_observaciones', "keyup", function () { 
	busca($F('busca_ue'), 
	$F('busca_proveedores'), 
	$F('busca_observaciones'), 
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta'),
	$F('busca_nrodoc'),1); 
});
Event.observe('busca_nrodoc', "keyup", function () { 
	busca($F('busca_ue'), 
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
			$F('busca_proveedores'), 
			$F('busca_observaciones'), 
			$F('busca_fecha_desde'), 
			$F('busca_fecha_hasta'),
			$F('busca_nrodoc'),1); 
		} else {
			alert("Fecha incorrecta");
			$(fecha).value = "";
		}
	}else if(fecha.value != '') {
		alert("Fecha incorrecta");
		$(fecha).value = "";
	}
}

function mostrar_ventana(){
	 
	//var tipo = "('P','A','B')";
	
	var url = 'buscar_proveedores.php';
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
	t = setTimeout("buscaProveedor()", 800);
}

function buscaProveedor()
{
	var tipo = "('P','A','B')";

	var url = 'buscar_proveedores.php';
	var pars = 'tipo='+tipo+'&status=A&rif='+$('search_rif_prov').value+ '&nombre='+ $('search_nombre_prov').value+'&opcion=2&ms'+new Date().getTime();
		
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
	$('bproductos').style.display = 'inline';
	Dialog.okCallback();

}

function selPartidas(id, nombre){

	$('txtpartidas_presupuestarias').value = nombre;
	$('partidas_presupuestarias').value = id;
	Dialog.okCallback();

}

function mostrarBuscarCat(){
	mygrid.clearAll();
	$('bcategorias').style.display = 'inline';

}

function traerPartidasSeleccionada(rowId){

	var cp = mygrid.cells(rowId,1).getValue();
	var pp = mygrid.cells(rowId,2).getValue();
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

function mostrar_ventana_prod(){
	 
	var url = 'buscar_productos.php';
	var pars = 'id_cp='+$('categorias_programaticas').value+'&ms'+new Date().getTime();
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

	function traeProductos(id, descripcion){
	
		$('productos').value = descripcion;
		$('id_producto').value = id;
		$('cantidad').value = '0';
		Dialog.okCallback();
	}
	
	function busca_prod(){
	
	var url = 'buscar_productos.php';
	var pars = 'id_cp='+$('id_cp').value+'&descripcion='+$('descrip').value+'&opcion=2&ms'+new Date().getTime();
		
	var updater = new Ajax.Updater('divproductos', 
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
var t;
function xxxxx(descripcion) {
 	
  clearTimeout(t); 
	t = setTimeout("busca_prod()", 1500); 
};

	
	//TRAE LAS PARTIDAS SEGUN EL PRODUCTO Y LA CATEGORIA QUE SE ESCOJA	
function traePartidasPorProductos(){
	//alert("entro");
	if ($('unidad_ejecutora').value =="0"){
		
		alert("Primero debe Seleccionar una Unidad Ejecutora.");
		return;
		
	}else if($('categorias_programaticas').value=="0"){
	
		alert("Primero debe Seleccionar una Categoria Programatica.");
		return;
	} else if($('cantidad').value=="" || parseFloat($('cantidad').value) < 1){
		
		alert("Primero debe colocar la cantidad de productos que necesita.");
		return;
	} else {
		var cp = $('categorias_programaticas').value;
		var idprod = $('id_producto').value;	
		var cant = $('cantidad').value;
		
		var url = 'json.php';
		var pars = 'op=prodpar&cp=' + cp +'&prod='+ idprod +'&cant='+ cant;
	
		var myAjax = new Ajax.Request(
					url, 
					{
					method: 'get', 
					parameters: pars,
					onComplete: function(peticion){
						var jsonData = eval('(' + peticion.responseText + ')');
						if (jsonData == undefined) { 		return  }
						$('disponible').value = jsonData.disponible;
						$('idParCat').value = jsonData.idparcat;
						$('total_prod').value = jsonData.total;
						$('partidas_presupuestarias').value = jsonData.id_partida;
						$('puedo').value = jsonData.puedo;
						$('precio').value = jsonData.precio;
						var acumula_total_partida = 0;
						for(j=0;j<mygrid.getRowsNum();j++){
							if(mygrid.cells(mygrid.getRowId(j),2).getValue()==$('partidas_presupuestarias').value){
								acumula_total_partida = acumula_total_partida + parseFloat(mygrid.cells(mygrid.getRowId(j),7).getValue());
							
							}
						}
						acumula_total_partida = parseFloat($('total_prod').value) +acumula_total_partida;
						 if( parseFloat($('disponible').value) < acumula_total_partida){
							alert("El monto disponible en la partida es menor al requerido");
							$('cantidad').value='0';
							return;	
					
								}else{
								AgregarRE();
								}
					}
					}
				);
			 
	}

function AgregarRE(){
	//alert($('unidad_ejecutora').value);
		for(j=0;j<i;j++){
			
			if (mygrid.getRowId(j)!=undefined){
				if (mygrid.cells(mygrid.getRowId(j),'1').getValue() == $('categorias_programaticas').value && mygrid.cells(mygrid.getRowId(j),'2').getValue() == $('partidas_presupuestarias').value &&  mygrid.cells(mygrid.getRowId(j),'0').getValue() == $('id_producto').value){
						
					alert('Esta producto ya ha sido seleccionado para esta categoria, por favor seleccione otra categoria');
					return false;

				}
			
			}
			
			
		}
		
		//mygridre.addRow(1,";;;;;;;");
		/*mygrid.addRow(i,$('id_producto').value+";"+$('categorias_programaticas').value+";"+$('partidas_presupuestarias').value+";"+$('cantidad').value+";0;0;"+parseFloat($('precio').value)+";"+parseFloat($('total_prod').value));*/
		mygrid.addRow(i,$('id_producto').value+";"+$('categorias_programaticas').value+";"+$('partidas_presupuestarias').value+";"+$('cantidad').value+";"+parseFloat($('precio').value)+";;");
		i++;
		//sumaTotal();
		$('cantidad').value='0';
		
	}
}

	function generaTotal(rowId){
		if (mygrid.cells(rowId,5).getValue()==''){
			alert("Debe introducir el valor del IVA para el producto");
			return false;
			} else { 
				var costo = parseFloat(mygrid.cells(rowId,4).getValue());
				var cant = parseInt(mygrid.cells(rowId,3).getValue());
				var iva = parseInt(mygrid.cells(rowId,5).getValue());
				var impuesto = (costo * (iva/100)) * cant
				var total = costo * cant + impuesto;
				/*var subtotal = costo * cant;
				$('subtot').value = subtotal;*/
				mygrid.cells(rowId,6).setValue(impuesto.toFixed(2));
				mygrid.cells(rowId,7).setValue(total);
				sumaTotal();
			}
	}
	
	function sumaTotal(){
	var totalord = 0;
	var totaliva = 0;
	for(j=0;j<i;j++){
		if(mygrid.getRowId(j)!= undefined){
			totalord += parseFloat(mygrid.cells(mygrid.getRowId(j),7).getValue());
			totaliva += parseFloat(mygrid.cells(mygrid.getRowId(j),6).getValue());
		}
	}
	
	$('total_iva').value = (isNaN(totaliva))? '0' : muestraFloat(totaliva);
	$('total_general').value = (isNaN(totalord))? '0' : muestraFloat(totalord);
	//$('montoCOB').value = (isNaN(totalRequis))? '0' : muestraFloat(totalRequis);

}

	function buscaNroDoc(boton){
	//alert(boton); FUNCION QUE BUSCA SI EXISTE YA UN NUMERO DE DOCUMENTO ANTES DE QUE PUEDA SER INSERTADO A LA BASE DE DATOS
	//if(boton=='Guardar'){
		/*var tabla = 'puser.orden_compra';
		var nrodoc = $('nrodoc').value;
		var tipdoc = '001';
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
		actapr(boton);
		//}
		
	//ESTA FUNCION SE USA PARA CERRAR EL DIVIDER SIN MOSTRAR EL MENSAJE DE AGREGAR NUEVO
	
}

	function close_div_compras(){
		$('formulario').innerHTML = '';
	
	}
	
	function actualizaPrecios(id_proveedor,id_requisicion){
		//alert(id_requisicion);
		var url = 'json.php';
		var pars = 'op=actualizaPrecio&id_proveedor=' + id_proveedor +'&id_requisicion='+ id_requisicion;
	
		var myAjax = new Ajax.Request(
					url, 
					{
					method: 'get', 
					parameters: pars,
					onComplete: function(peticion){
						//alert(peticion.responseText);
						var jsonData = eval('(' + peticion.responseText + ')');
						//var jsonData =  peticion.responseText;
						//alert(jsonData);
						if (jsonData == undefined) { return }
							//alert('entro')
						///*var acumula_total_partida = 0;
						mygrid.clearSelection();
						mygrid.clearAll();
						i = 0;
						var total_iva = 0;
						var total_monto = 0;
						for(i=0;i<jsonData.length;i++){
							//var ctotal = Relaciones[i]['costo']*Relaciones[i]['cantidad'];
							var costo_iva = (jsonData[i]['costo'] * jsonData[i]['cantidad'] * jsonData[i]['iva'])/100; 
							var monto_total = (jsonData[i]['costo'] * jsonData[i]['cantidad']) + costo_iva;
							total_iva += costo_iva;
							total_monto += monto_total;
							mygrid.addRow(i,jsonData[i]['id_producto']+";"+jsonData[i]['categoria']+";"+jsonData[i]['partida']+";"+jsonData[i]['cantidad']+";"+muestraFloat(jsonData[i]['costo'])+";"+jsonData[i]['iva']+";"+muestraFloat(costo_iva)+";"+muestraFloat(monto_total));
						}
						$('total_iva').value = muestraFloat(total_iva);
						$('total_general').value = muestraFloat(total_monto);
					}
					}
					);
	}
	
	function datosProveedor(id_proveedor){
		var url = 'json.php';
		var pars = 'op=datosProveedor&id_proveedor=' + id_proveedor;
		var myAjax = new Ajax.Request(
					url, 
					{
					method: 'get', 
					parameters: pars,
					onComplete: function(peticion){
						var jsonData = eval('(' + peticion.responseText + ')');
						//alert(jsonData);
						if (jsonData == undefined) { return }
						$('ciudad').value = jsonData['estado'];
						$('telefono').value = jsonData['telefono'];
						$('direccion').value = jsonData['direccion'];
					}
					}
					);	
	}


</script>

<? 
//$validator->create_message("error_nrodoc", "nrodoc", "*");
//$validator->create_message("error_fechadoc", "fechadoc", "*");
$validator->create_message("error_proveedores", "proveedores", "*");
$validator->create_message("error_condpag", "condpag", "*");
$validator->create_message("error_observacion", "observ", "*");
$validator->print_script();
require ("comun/footer.php");
?>
