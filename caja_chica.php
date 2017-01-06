<?
include('adodb/adodb-exceptions.inc.php');
require ("comun/ini.php");
// Creando el objeto caja_chica
$oCajaChica = new caja_chica;
$accion = $_REQUEST['accion'];
if($accion == 'Guardar'){
	$oCajaChica->add($conn, 
										$_POST['tipdoc'],
										$_POST['unidad_ejecutora'],
										$_POST['id_ciudadano'],
										$_POST['nombre_ciudadano'],
										$_POST['dir_ciudadano'],
										$_POST['tlf_ciudadano'],
										$usuario->id,
										$_POST['descripcion'],
										$_POST['observaciones'],
										guardafecha($_POST['fecha']),
										$_REQUEST['caja_chica'],
										$_REQUEST['contenedor_facturas'],
										$_POST['nrodoc'],
										$auxNroDoc);
}elseif($accion == 'Aprobar'){
	$oCajaChica->aprobar($conn, 
									$_POST['id'], 
									$usuario->id,
									$_POST['unidad_ejecutora'],
									$_POST['id_ciudadano'],
									date("Y"),
									$_POST['descripcion'],
									$_POST['tipdoc'],
									guardafecha($_POST['fecha']),
									'1',
									$_REQUEST['caja_chica'],
									$_REQUEST['contenedor_facturas'],
									$_POST['nrodoc'],
									$auxNroDoc,
									$escEnEje);
}elseif($accion == 'Actualizar'){
	$oCajaChica->set($conn, 
									$_POST['id'], 
									$_POST['tipdoc'],
									$_POST['unidad_ejecutora'],
									$_POST['id_ciudadano'],
									$_POST['nombre_ciudadano'],
									$_POST['dir_ciudadano'],
									$_POST['tlf_ciudadano'],
									$_POST['descripcion'],
									$_POST['observaciones'],
									guardafecha($_POST['fecha']),
									$_REQUEST['caja_chica'],
									$_REQUEST['contenedor_facturas'],
									$_POST['nrodoc'],
									$auxNroDoc
									);
}elseif($accion == 'del'){
	$oCajaChica->del($conn, $_POST['id']);

}elseif($accion == 'Anular'){
	$oCajaChica->anular($conn, $_POST['id'],
									$usuario->id,
									$_POST['unidad_ejecutora'],
									date("Y"),
									$_POST['descripcion'],
									$_POST['tipdoc'],
									guardafecha($_POST['fecha']),
									$_POST['status'],
									$_POST['id_ciudadano'],
									$_REQUEST['caja_chica'],
									$_POST['nrodoc'],
									$escEnEje);
}
$msj = $oCajaChica->msj; // lleno esta variable con el mensaje de la operacion llevada a cabo
$cCajaChica=$oCajaChica->get_all($conn, $escEnEje);
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<br />
<span class="titulo_maestro">Caja Chica</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />
<fieldset id="buscador">
	<legend>Buscar:</legend>
	<table border="0">
		<tr>
			<td colspan="3">Unidad Ejecutora</td>
		</tr>
		<tr>
			<td colspan="3"><?=helpers::combo_ue_cp($conn,'busca_ue','','','','','','',
			"SELECT DISTINCT id, (id || ' - ' || descripcion) AS descripcion FROM unidades_ejecutoras ORDER BY id")?></td>
		</tr>
		<tr>
			<td>Ciudadano</td>
			<td>Descripci&oacute;n</td>
		</tr>
		<tr>
			<td>
			<?=helpers::combo_ue_cp($conn, 'busca_ciudadanos','','','','','','',
			"SELECT id, id||' - '||nombre AS descripcion FROM proveedores WHERE provee_contrat = 'B'")?></td>
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
								<img border="0" src="images/calendarA.png" width="20" height="20" />
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
								<img border="0" src="images/calendarA.png" width="20" height="20" />
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
<br />
<div style="margin-bottom:10px" id="busqueda"><div>
<br />
<div style="height:40px;padding-top:10px;">
<p id="cargando" style="display:none;margin-top:0px;">
  <img alt="Cargando" src="images/loading.gif" /> Cargando...
</p>
</div>
<script type="text/javascript">

var t;

/***************       Seccion de las Partidas ******************************/
i=0, ifac=0;

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

function actapr(elemento){
	if(elemento.value == 'Actualizar')
		$('accion').value = 'Actualizar';
	else if(elemento.value == 'Aprobar')
		$('accion').value = 'Aprobar';
	else
		$('accion').value = 'Anular'; 
	 //validate(); 
}

/* Metodos utilizados en el buscador */
function busca(id_ue, id_ciudadano, descripcion, fecha_desde, fecha_hasta, nrodoc, pagina){
	var url = 'updater_busca_caja_chica.php';
	var pars = '&id_ue=' + id_ue + '&id_ciudadano=' + id_ciudadano+ '&descripcion=' + descripcion + '&pagina=' + pagina;
	pars += '&nrodoc=' + nrodoc + '&fecha_desde=' + fecha_desde+ '&fecha_hasta=' + fecha_hasta +'&ms='+new Date().getTime();
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
	$F('busca_ciudadanos'), 
	$F('busca_descripcion'), 
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta'),
	$F('busca_nrodoc'), 1); 
});
Event.observe('busca_ciudadanos', "change", function () { 
	busca($F('busca_ue'), 
	$F('busca_ciudadanos'), 
	$F('busca_descripcion'), 
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta'),
	$F('busca_nrodoc'), 1); 
});
Event.observe('busca_descripcion', "keyup", function () { 
	busca($F('busca_ue'), 
	$F('busca_ciudadanos'), 
	$F('busca_descripcion'), 
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta'),
	$F('busca_nrodoc'), 1); 
});
Event.observe('busca_nrodoc', "keyup", function () { 
	busca($F('busca_ue'), 
	$F('busca_ciudadanos'), 
	$F('busca_descripcion'), 
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta'),
	$F('busca_nrodoc'), 1); 
});

function validafecha(fecha){
	var upper = 31;
	if(/^(\d{2})\/(\d{2})\/(\d{4})$/.test(fecha.value)) { // dd/mm/yyyy
		if(RegExp.$2 == '02') upper = 29;
		if((RegExp.$1 <= upper) && (RegExp.$2 <= 12)) {
			busca($F('busca_ue'), 
			$F('busca_ciudadanos'), 
			$F('busca_descripcion'), 
			$F('busca_fecha_desde'), 
			$F('busca_fecha_hasta'),
			$F('busca_nrodoc'), 1); 
		} else {
			alert("Fecha incorrecta");
			fecha.value = "";
		}
	}else if(fecha.value != '') {
		alert("Fecha incorrecta");
		fecha.value = "";
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
	var pars = 'cp=' + cp +'&filtro=402,403,404&ms='+new Date().getTime();
		
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

function busca_popup_pp()
{
	clearTimeout(t);
	setTimeout('buscaPartidasPresupuestarias()', 800);
}

function buscaPartidasPresupuestarias()
{
	var url = 'buscar_partidas.php';
	var pars = 'filtro=402,403,404&cp=' + $('cp').value +'&nombre='+$('search_nombre_pp').value+'&codigo='+$('search_cod_pp').value+'&opcion=2&ms='+new Date().getTime();
		
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
function AgregarCC(){
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
		
	}else if($('montoip').value=="" || parseFloat($('montoip').value)<1){
		
		alert("Primero debe colocar el monto de la Imputacion Presupuestaria.");
		return;
	
	}else if(parseFloat($('disponible').value) < usaFloat($('montoip').value)){
		alert("El monto disponible en la partida es menor al requerido");
		$('montoip').value='0,00';
		return;	 
	
	}else{
		
		for(j=0;j<mygridcc.getRowsNum();j++){
			
			if (mygridcc.getRowId(j)!=undefined){
				if (mygridcc.cells(mygridcc.getRowId(j),'0').getValue() == $('categorias_programaticas').value && mygridcc.cells(mygridcc.getRowId(j),'1').getValue() == $('partidas_presupuestarias').value){
						
					alert('Esta partida ya ha sido seleccionada, por favor seleccione otra partida');
					return false;

				}
			
			}
			
			
		}
		
		/*mygridco.getCombo(0).put(JsonData[j]['id_categoria_programatica'],JsonData[j]['categoria_programatica']);
		mygridco.getCombo(1).put(JsonData[j]['id_partida_presupuestaria'],JsonData[j]['partida_presupuestaria']);*/
		mygridcc.addRow($('idParCat').value,$('categorias_programaticas').value+";"+$('partidas_presupuestarias').value+";"+usaFloat($('montoip').value)+";0;;0,0");
		i++;
		sumaTotal();
		$('montoip').value='0,00';
		
	}
}

function EliminarCC(){
	mygridcc.deleteRow(mygridcc.getSelectedId());
	sumaTotal();
	
}

	function sumaTotal_old(){
	var totalPartidas = 0;
	for(j=0;j<i;j++){
		if(mygridcc.getRowId(j)!= undefined){
			totalPartidas += parseFloat(mygridcc.cells(mygridcc.getRowId(j),2).getValue());
		}
	}
	$('montoCC').value = (isNaN(totalPartidas))? '0' : muestraFloat(totalPartidas);
	$('montoCCB').value = (isNaN(totalPartidas))? '0' : muestraFloat(totalPartidas);

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
	if (parseFloat($('montoCC').value) > 0) {
		if (parseFloat($('montoCC').value) == parseFloat($('total').value)){	
		//if(parseFloat($('montoCC').value) == parseFloat($('total').value)) {
			var JsonAux,cajac=new Array;
			mygridcc.clearSelection()
			for(j=0;j<i;j++)
			{
				if(!isNaN(mygridcc.getRowId(j)))
				{
					cajac[j] = new Array;
					cajac[j][0]= mygridcc.cells(mygridcc.getRowId(j),0).getValue();
					cajac[j][1]= mygridcc.cells(mygridcc.getRowId(j),1).getValue();
					cajac[j][2]= mygridcc.cells(mygridcc.getRowId(j),2).getValue();
					cajac[j][3]= mygridcc.cells(mygridcc.getRowId(j),3).getValue();
					cajac[j][4]= mygridcc.cells(mygridcc.getRowId(j),4).getValue();
					cajac[j][5]= mygridcc.cells(mygridcc.getRowId(j),5).getValue();
					cajac[j][6]= mygridcc.cells(mygridcc.getRowId(j),6).getValue();
					cajac[j][7]= mygridcc.getRowId(j);			
				}
			}
			JsonAux={"cajac":cajac};
			$("caja_chica").value=JsonAux.toJSONString();
			
		var JsonAux2,facturas = new Array;
		mygridfac.clearSelection()
		for(j=0;j<ifac;j++) {
			if(!isNaN(mygridfac.getRowId(j))) {
				facturas[j] = new Array;
				facturas[j][0] = mygridfac.cells(mygridfac.getRowId(j),0).getValue();
				facturas[j][1] = mygridfac.cells(mygridfac.getRowId(j),1).getValue();
				facturas[j][2] = mygridfac.cells(mygridfac.getRowId(j),2).getValue();
				facturas[j][3] = mygridfac.cells(mygridfac.getRowId(j),3).getValue();
				facturas[j][4] = mygridfac.cells(mygridfac.getRowId(j),4).getValue();
				facturas[j][5] = mygridfac.cells(mygridfac.getRowId(j),5).getValue();
				facturas[j][6] = mygridfac.cells(mygridfac.getRowId(j),6).getValue();
				facturas[j][7] = mygridfac.cells(mygridfac.getRowId(j),7).getValue();
				facturas[j][8] = mygridfac.cells(mygridfac.getRowId(j),8).getValue();
				facturas[j][9] = mygridfac.getRowId(j);
			}
		}
		JsonAux2={"facturas":facturas};
		$('contenedor_facturas').value=JsonAux2.toJSONString();
		validate();
	} else {
		alert("No coinciden los montos en las facturas y los montos imputados en las partidas presupuestarias");
		return;		
	}
	} else {
		alert("Debe seleccionar partidas programaticas para imputar");
	}
}
	
	function ver_partpre(){
		Effect.toggle('partpreDiv', 'blind');
	}	
	
	//AGREGA UNA FILA EN EL GRIS DE FACTURAS
function Agregar(){
	mygridfac.addRow(ifac,",,,,0,0,,0,0");
	ifac++;
}

//ELIMINAR UNA FILA EN EL GRID DE FACTURAS//
function Eliminar(){
	mygridfac.deleteRow(mygridfac.getSelectedId());
	sumaTotalFacturas();
}

//CALCULAR LOS VALORES DE EL GRID DE FACTURAS//
	var entroComboFact = 0;
	function calcularMontoBaseImp(stage,rowId,cellInd){
		
		//alert ('CellInd = '+ cellInd + ' stage = '+ stage);
		if(cellInd==6 && stage == 2 && entroComboFact==0) {
			if(mygridfac.cells(rowId,'6').getValue()==0)
				alert('Debe seleccionar el monto del Impuesto');
			else if((mygridfac.cells(rowId,'3').getValue()==null) || (mygridfac.cells(rowId,'3').getValue()==0)){
				alert('Debe existir un monto para la factura');
			}else{	
				//entroComboFact++;
				
						var r = 0;	
						if((mygridfac.cells(rowId,'5').getValue())==null)
							mygridfac.cells(rowId,'5').setValue(r.toString());	
						
						if((mygridfac.cells(rowId,'4').getValue())==null)
							mygridfac.cells(rowId,'4').setValue(r.toString());	
							
						//CALCULO BASE IMPONIBLE
						
						r = ((parseFloat(mygridfac.cells(rowId,'3').getValue()) - parseFloat(mygridfac.cells(rowId,'5').getValue())) * 100 )  / (100 + parseFloat(mygridfac.cells(rowId,'6').getValue()));
						//alert('((' + parseFloat(mygridfac.cells(rowId,'4').getValue()) + '-' + parseFloat(mygridfac.cells(rowId,'6').getValue()) + ')' +' * '+ '100)/(100 + ' + parseFloat(mygridfac.cells(rowId,'3').getValue())+')');
						//alert(r);
						//Se evalua si el campo base imponible no ha sido calculado, si es la primera vez el sistema realiza el calculo automatico
						if(mygridfac.cells(rowId,'7').getValue()==0)
							r = isNaN(r) ? '0' : r.toFixed(2);
						else
							r = parseFloat(mygridfac.cells(rowId,'7').getValue());
						//alert('BI ' + r);
						mygridfac.cells(rowId,'7').setValue(r);
						
						//CALCULO DEL MONTO IVA//
						r = 0;
						if(mygridfac.cells(rowId,'8').getValue()==0){
							//alert('entro');
							r = parseFloat(mygridfac.cells(rowId,'7').getValue()) *  (parseFloat(mygridfac.cells(rowId,'6').getValue()) / 100);
							r = isNaN(r) ? '0' : r.toFixed(2);
						}else{	
							r = parseFloat(mygridfac.cells(rowId,'8').getValue());
						}
						//alert('IVA ' + r);	
						mygridfac.cells(rowId,'8').setValue(r);	
						//CALCULAR MONTO IVA RETENCIONES//
						sumaTotalFacturas();
				}
					
		} else if(cellInd==7 && stage == 2 && entroComboFact==0) {
			if(mygridfac.cells(rowId,'7').getValue()==0)
				r = isNaN(r) ? '0' : r.toFixed(2);
			else
				r = parseFloat(mygridfac.cells(rowId,'7').getValue());
			//alert('BI ' + r);
			mygridfac.cells(rowId,'7').setValue(r);
			sumaTotalFacturas();
		
		} else if(cellInd==8 && stage == 2 && entroComboFact==0) {
			r = 0;
			if(mygridfac.cells(rowId,'8').getValue()==0){
				r = parseFloat(mygridfac.cells(rowId,'8').getValue()) *  (parseFloat(mygridfac.cells(rowId,'5').getValue()) / 100);
				r = isNaN(r) ? '0' : r.toFixed(2);
			}else{	
				r = parseFloat(mygridfac.cells(rowId,'8').getValue());
			}
			//alert('IVA ' + r);	
			mygridfac.cells(rowId,'8').setValue(r);	
			sumaTotalFacturas();
		}
					
	}
//SUMA EL TOTAL DE LAS FACTURAS//
function sumaTotalFacturas(){
	var total = 0;
	var total_iva = 0;
	var subtotal = 0;
	var r = 0;
	for(j=0;j<mygridfac.getRowsNum();j++){
		if(mygridfac.getRowIndex(j)!=-1){
			subtotal += parseFloat(mygridfac.cells(j,7).getValue());
			total += parseFloat(mygridfac.cells(j,3).getValue()) - parseFloat(mygridfac.cells(j,4).getValue()); 
			total_iva += parseFloat(mygridfac.cells(j,8).getValue());
		}
	}
	r = muestraFloat(total);
	$('subtotal').value = muestraFloat(subtotal);
	$('totaliva').value = muestraFloat(total_iva);
	$('total').value  = r;
	//total_sol();
}


	//SUMA EL TOTAL DE LAS FACTURAS//
/*function sumaTotalFacturas(){
	var iva, total, subtotal, totaliva;
	var ctotaliva = 0;
	var csubtotal = 0;
	var ctotal = 0;
	var r = 0;
	//alert('filas: '+ mygridfac.getRowsNum());
	for(j=0;j<mygridfac.getRowsNum();j++){
		if(mygridfac.getRowIndex(j)!=-1){
			if(isNaN(mygridfac.cells(j,3).getValue()))
				iva = 0;
			else
				iva = parseFloat(mygridfac.cells(j,3).getValue())/100;
			totaliva = parseFloat(mygridfac.cells(j,4).getValue()) * iva;
			total = parseFloat(mygridfac.cells(j,4).getValue()) + totaliva; 
			subtotal = total - totaliva;
			ctotaliva+= totaliva;
			csubtotal+= subtotal;
			ctotal+= total 
			
		}
		//alert('tiva ' + ctotaliva + ' sub ' + csubtotal)
	}
	r = muestraFloat(ctotal);
	$('subtotal').value = muestraFloat(csubtotal);
	$('totaliva').value = muestraFloat(ctotaliva);
	$('total').value  = r;
}

	function validaGrid_fact(rowId, cellInd){
		var fecha = mygridfac.cells(rowId,'2').getValue();
		var upper = 31;
		if(/^(\d{2})\/(\d{2})\/(\d{4})$/.test(fecha)) { // dd/mm/yyyy
			if(RegExp.$2 == '02') upper = 29;
				if((RegExp.$1 <= upper) && (RegExp.$2 <= 12)) {
					sumaTotalFacturas();
					return true;
				} else {
					alert("Fecha incorrecta");
					fecha = "";
			}
		}else if(fecha.value != '') {
			alert("Fecha incorrecta");
			fecha = "";
		}
	}*/
	
	function mostrar_ventana(pc){
	
	//var tipo = "('B')"; 
	var url = 'buscar_proveedores.php';
	var pars = 'status=&tipo=&pc='+pc+'&ms='+new Date().getTime();
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

function busca_popup()
{
	clearTimeout(t);
	t = setTimeout("buscaProveedor()", 800);
}

function buscaProveedor()
{
	//var tipo = "('P','A','B')";

	var url = 'buscar_proveedores.php';
	var pars = 'tipo='+$('tipo_prov').value+'&status=&pc='+$('pc').value+'&rif='+$('search_rif_prov').value+ '&nombre='+ $('search_nombre_prov').value+'&opcion=2&ms'+new Date().getTime();
		
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


function selDocumento2(id, nombre){
	
	$('id_ciudadano').value = nombre;
	//$('proveedores').value = id;
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
	//mygridcc.clearAll();
	$('bcategorias').style.display = 'inline';

}

// trae el ciudadano de la tabla de proveedores
function traeCiudadanoDesdeXML2(id_ciudadano){
	//$('id_proveedor').value = id_ciudadano;
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

function traeCiudadano(originalRequest){
	
	var xmlDoc = originalRequest.responseXML;
	var x = xmlDoc.getElementsByTagName('proveedor');
	for(j=0;j<x[0].childNodes.length;j++){
		if (x[0].childNodes[j].nodeType != 1) continue;
		var nombre = x[0].childNodes[j].nodeName
		$(nombre).value = x[0].childNodes[j].firstChild.nodeValue;
	}
}

function mostrarBuscarCat2(){
	$('bcategorias').style.display = 'inline';

}

function buscaNroDoc(boton){
	//alert(boton);
	/*if(boton=='Guardar'){
		var tabla = 'puser.caja_chica';
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
		//alert($('montoCC').value + "   " + $('total').value);
		if(parseFloat($('montoCC').value) == parseFloat($('total').value)){
			Guardar();
		} else{
			alert('El monto a Imputar y el de la factura no coinciden');
			return false;
		}
		//}
}

	function generaTotal(rowId){
		if (mygridcc.cells(rowId,4).getValue()==''){
			alert("Debe seleccionar un valor para el IVA");
			return false;
		} else if (parseFloat(mygridcc.cells(rowId,3).getValue()) > parseFloat(mygridcc.cells(rowId,2).getValue())){
				alert("El monto excento no puede ser mayor que el monto a imputar");
				return false;
		} else { 
			var costo = parseFloat(mygridcc.cells(rowId,2).getValue());
			var excento = parseFloat(mygridcc.cells(rowId,3).getValue());
			var iva = parseFloat(mygridcc.cells(rowId,4).getValue());
			var costo_neto = costo - excento;
			//alert(iva);
			var impuesto = costo_neto * (iva/100);
			var total = costo + impuesto;
					/*var subtotal = costo * cant;
					$('subtot').value = subtotal;*/
			mygridcc.cells(rowId,5).setValue(impuesto.toFixed(2));		
			mygridcc.cells(rowId,6).setValue(total.toFixed(2));
			sumaTotal();
	   }
	}
	
	function sumaTotal(){
	var totalpar = 0;
	var totaliva = 0;
	for(j=0;j<mygridcc.getRowsNum();j++){
		if(mygridcc.getRowId(j)!= undefined){
			totalpar += parseFloat(mygridcc.cells(mygridcc.getRowId(j),6).getValue());
			totaliva += parseFloat(mygridcc.cells(mygridcc.getRowId(j),5).getValue());
		}
	}
	$('total_iva').value = (isNaN(totaliva))? '0' : muestraFloat(totaliva);
	$('montoCC').value = (isNaN(totalpar))? '0' : muestraFloat(totalpar);
	$('montoCCB').value = (isNaN(totalpar))? '0' : muestraFloat(totalpar);

}

	function traerPartidasSeleccionada(rowId){

	var cp = mygridcc.cells(rowId,0).getValue();
	var pp = mygridcc.cells(rowId,1).getValue();
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
$validator->create_message("error_ciud", "id_ciudadano", "*");
$validator->create_message("error_ue", "unidad_ejecutora", "*");
$validator->print_script();
require ("comun/footer.php");
?>
