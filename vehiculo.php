<?
require ("comun/ini.php");
// Creando el objeto Maestro vehiculo vehiculo
$ovehiculo = new vehiculo;
$accion = $_REQUEST['accion'];
$accion2 = $_REQUEST['accion2'];

/*if ($_REQUEST['accion3']=='Liquidar') {
	header("Location:liquidacion.pdf.php?&ultimo_pago=".$objeto->anio_pago."&tipo=."$objeto->cod_tip."&primera=".$objeto->primera_vez."&veh=".$objeto->id."&id_contribuyente=".$objeto->id_contribuyente."&anio_veh=".$objeto->anio_veh);
	exit;
	}*/
	
if (($accion = $_REQUEST['accion']=='Actualizar') && ($accion2 = $_REQUEST['accion2']=='Pagar')){
	$accion = '';
	$accion2 = 'Pagar';
}
elseif(($accion = $_REQUEST['accion']=='Guardar') && ($accion2 = $_REQUEST['accion2']=='')){
	$accion = 'Guardar';
	$accion2 = '';
}
elseif (($accion = $_REQUEST['accion']=='Actualizar') && ($accion2 = $_REQUEST['accion2']=='')){
	$accion = 'Actualizar';
	$accion2 = '';
}

$concesionario=$_REQUEST['concesionario'];
if(empty($concesionario)){ $concesionario='0'; }
$rif=$_REQUEST['rif_letra']."-".$_REQUEST['rif_numero']."-".$_REQUEST['rif_cntrl'];

$primera_vez=$_REQUEST['primera_vez'];
if(empty($primera_vez)){ $primera_vez=0; }


$anio_pago=$_REQUEST['anio_pago'];
if (empty($anio_pago)){ $anio_pago=0; }

$precio=guardafloat($_REQUEST['precio']);
if(empty($precio)){ $precio=0; }

if($accion == 'Guardar'){
	if($ovehiculo->add($conn, $_REQUEST['id_contribuyente'], $_REQUEST['serial_carroceria'], strtoupper($_REQUEST['placa']), date('Y-m-d'), $_REQUEST['anio_veh'], $_REQUEST['serial_motor'], $_REQUEST['marcas'], $_REQUEST['modelos'], $_REQUEST['cod_col'], $_REQUEST['cod_uso'], $_REQUEST['cod_tip'], $_REQUEST['fec_compra'], guardafloat($_REQUEST['peso_kg']), 0, $precio, $_REQUEST['observacion'], $_REQUEST['cod_exo'], $anio_pago, $_REQUEST['cod_lin'], $concesionario, $primera_vez))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
		
}elseif($accion == 'Actualizar'){
	if($ovehiculo->set($conn, $_REQUEST['id'], $_REQUEST['id_contribuyente'], $_REQUEST['serial_carroceria'], strtoupper($_REQUEST['placa']), date('Y-m-d'), $_REQUEST['anio_veh'],
						$_REQUEST['serial_motor'], $_REQUEST['marcas'], $_REQUEST['modelos'], $_REQUEST['cod_col'], $_REQUEST['cod_uso'], 
						$_REQUEST['cod_tip'], $_REQUEST['fec_compra'], guardafloat($_REQUEST['peso_kg']), 0, $precio, 
						$_REQUEST['observacion'], $exento, $anio_pago, $_REQUEST['cod_lin'], 
						$concesionario, $primera_vez ))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}elseif($accion == 'del'){
	if($ovehiculo->del($conn, $_REQUEST['id']))
		$msj = REG_DEL_OK;
	else
		$msj = ERROR;
		
}

elseif($accion2 == 'Pagar'){
	
			
	$opago = new pago;

	if($opago->add($conn, 
			 $ultimo_pago=$_REQUEST['anio_pago'], 
			 $tipo_vehiculo=$_REQUEST['cod_tip'],
			 $primera_vez=$_REQUEST['primera_vez'],
			 $contribuyente=$_REQUEST['id_contribuyente'],
			 $declaracion=$_REQUEST['id'],
			 $los_tipos_pagos=$_REQUEST['tipo_de_pago'],
			 $los_bancos=$_REQUEST['banco'],
			 $los_documentos=$_REQUEST['nro_documento'],
			 $los_montos=$_REQUEST['monto'],
			 $total=guardafloat($_REQUEST['monto_total']),
			 $calcomania=$_REQUEST['nro_calcomania']))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
	
 }

//Seccion paginador
$page_size = 25;
if ($_GET['pg'])
	$start_record=($_GET['pg'] * $page_size) - $page_size;
else
	$start_record=0;
	
$cvehiculo=$ovehiculo->get_all($conn, $start_record,$page_size);	
$pag=new paginator($ovehiculo->total,$page_size, self($_SERVER['SCRIPT_NAME']));
$i=$pag->get_total_pages();

require ("comun/header.php");

	//	$validator->create_message("id_title", "email", "(Inv&iacute;lido)", 3); //CREO EL MENSAJE DE VALIDACION Y EL OBJETO QUE VOY A CONTROLAR
	//	$validator->print_script();  //IMPRIMO EL SCRIPT
?>
<script type="text/javascript">var mygridsn,i=0</script>
<? if(!empty($msj)){ ?><div id="msj" style="display:none;"><?=$msj?></div><? echo "<br>"; } ?>
<br />
<span class="titulo_maestro">Maestro de Veh&iacute;culos </span>

<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a></div>
<br />

<table style=" margin-left: auto; margin-right: auto; font-size:10px; " align="center" border="0">
  <tr>
    <td width="69">Buscar Seg&uacute;n:</td>
    <td width="62">
		<select name="tipo" id="tipo">
			<option value="">Seleccione...</option>
			<option value="rif">Rif/C&eacute;dula</option>
			<option value="placa">Placa</option>
			<option value="serial">Serial Motor</option>
		</select>
	</td>
    <td width="60"><input type="text" name="valor" id="valor" value="" size="15"></td>
    <td width="86"><input type="button" value="Buscar" onClick="gettpl($('tipo').value, $('valor').value)"></td>
  </tr>
</table>
<br>
<div id="resultado"></div>

<br />
<div style="height:40px;padding-top:10px;">
<p id="cargando" style="display:none;margin-top:0px;">
  <img alt="Cargando" src="images/loading.gif" /> Cargando...
</p>
</div>

<!-- <div id="pars"></div>-->
<? require ("comun/footer.php"); ?>

	<script type="text/javascript">

	function realizarpago(a_ult_pago, deuda_hasta, ramo, tipo_vehiculo, anio_veh, placa, primera_vez, exento, contribuyente, id_veh, fec_compra)
	{
		if(primera_vez==true){
		primera_vez=1;
	}else{
		primera_vez=0;
	}
//	alert(primera_vez+'<-oops');
		var url = 'realizar.pago.php';
		var pars = 'a_ult_pago='+a_ult_pago+'&deuda_hasta='+deuda_hasta+'&ramo='+ramo+'&tipo_vehiculo='+tipo_vehiculo+'&anio_veh='+anio_veh;
		pars+= '&placa='+placa+'&primera_vez='+primera_vez+'&exento='+exento+'&id_contribuyente='+contribuyente+'&veh='+id_veh+'&fec_compra='+fec_compra;
	//		$('pars').innerHTML = pars;
	
		var myAjax = new Ajax.Updater(
			'deuda', 
			url, 
			{
				method: 'get', 
				parameters: pars
			});
		
	}

	function valida_ultimo_pago(primera_vez)
	{	
		if(primera_vez==true){
			$('anio_pago').value='';
			$('anio_pago').disabled=true;
		} else {
			$('anio_pago').value='';
			$('anio_pago').disabled=false;
		}
	
	}


	function agrega_forma_pago(){


		var x = $('tabla_pagos').insertRow($('tabla_pagos').rows.length);
		var i = $('tabla_pagos').rows.length;

		var y1=x.insertCell(0);
		var y2=x.insertCell(1);
		var y3=x.insertCell(2);
		var y4=x.insertCell(3);

		var cp = $('tipo_de_pago').cloneNode(true);
		cp.setAttribute('id', 'tipo_de_pago' + i);
		y1.appendChild(cp);

		var cp2 = $('banco').cloneNode(true);
		cp2.setAttribute('id', 'banco' + i);
		y2.appendChild(cp2);

		var cp3 = $('nro_documento').cloneNode(true);
		cp3.setAttribute('id', 'nro_documento' + i);
		y3.appendChild(cp3);
	
		var cp4 = $('monto').cloneNode(true);
		cp4.setAttribute('id', 'monto' + i);
		y4.appendChild(cp4);

		$('monto'+i).value='';
		$('nro_documento'+i).value='';

	}

	function quita_forma_pago(){
		if($('tabla_pagos').rows.length <= 2){
		alert('No puede eliminar mas formas de pago');
		}else{
			var x = $('tabla_pagos').deleteRow($('tabla_pagos').rows.length - 1);
		}
	}
	
function Pagar(){
	var nodoMonto = document.getElementsByClassName('monto_fila');
	var sumaMonto = 0;
	$A(nodoMonto).each(function(e){ sumaMonto += parseFloat(usaFloat(e.value)); });
	if ( sumaMonto==parseFloat(usaFloat($('monto_total').value)) ){
			document.form1.submit();
		//	document.location.href='recibo.pago.pdf.php?nro_recibo=';
	}
	else 
	{
		alert('Monto de las formas de pago difieren del monto total');
	}
}	

	function gettpl(tipo, valor)
	{
		var url = 'resultado_vehiculo.php';
		var pars = 'tipo='+tipo+'&valor='+valor;

		var myAjax = new Ajax.Updater(
			'resultado', 
			url, 
			{
				method: 'get', 
				parameters: pars
			});
		
	}
//Funcion que trae el combo de las marcas de los vehiculos*******	
	function TraeSPDesdeXML(marca){
	var url = 'updater_selects.php';
	var pars = 'combo=buscaPorMarca&id_marcas=' + marca;
	var updater = new Ajax.Updater('divmodelos', 
		url,
		{
			method: 'get',
			parameters: pars,
			asynchronous:true, 
			evalScripts:true,
			onLoading:function(request){Element.show('cargando_solicitudes')}, 
			onComplete:function(request){Element.hide('cargando_solicitudes')}
		}); 
	}
	
	function cambio(sancion){
		if (document.form1.sancion.checked==true){
			document.form1.tipo_sancion.disabled=false;
			muestraRet();
			//buildGridSAN();
		} else {
			 document.form1.tipo_sancion.disabled=true;
			 muestraRet();
		}
	} 
	
	/*function cargarGridSAN(id){
	
	mygridpp.clearSelection();
	mygridpp.clearAll();
	var url = 'json.php';
	var pars = 'op=san_agregar&id='+ id;
	var Request = new Ajax.Request(
		url,
		{
			method: 'get',
			parameters: pars,
			onLoading:function(request){}, 
			onComplete:function(request){
				
				var JsonData = eval( '(' + request.responseText + ')');
				//var causado = 0;
				//var comprometido = 0;
				var IdParCat = new Array;
				if(JsonData){
					for(var j=0;j<JsonData.length;j++){
						
						IdParCat[j] = new Array;
						mygridpp.getCombo(0).put(JsonData[j]['id'],JsonData[j]['descripcion']);
						mygridpp.addRow(JsonData[j]['idParCat'],JsonData[j]['id_categoria_programatica']+","+JsonData[j]['id_partida_presupuestaria']+","+JsonData[j]['montoporcausar']+",0");
						IdParCat[j][0] = JsonData[j]['idParCat'];
						
						//ACUMULO EL CAUSADO Y EL COMPROMETIDO//	
						causado += parseFloat(JsonData[j]['causado']);
						comprometido += parseFloat(JsonData[j]['comprometido']);
						ipp++;
					}
				
				var JsonIdParCat={"IdPartCat":IdParCat};
				$("idParCat").value=JsonIdParCat.toJSONString();
						
				var disponible = comprometido - causado;
				$('compromiso').value = comprometido;
				$('causado').value = causado;
				$('disponibilidad').value = disponible;	
								
				}
			}
		}
	);  
} */
	
	function getInfo(id){
	
	var url = 'json.php';
	var pars = 'op=san_agregar&id=' + id;
	
	for(j=0;j<i;j++){
		
		if(mygridsn.getRowIndex(j)!=-1){
			if (mygridsn.cells(j,'0').getValue() == id){
						
				alert("Esta sancion ya ha sido seleccionada");
				return false;
				
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
				mygridsn.getCombo(0).put(jsonData.id,jsonData.descripcion);
				$('id_sancion').value = (jsonData.id==null)? '0' : muestraFloat(jsonData.id);
				$('desc_sancion').value =jsonData.descripcion;
				$('monto_sancion').value = jsonData.monto;
				
				
			}
		}
	);
}

	//ESTA FUNCION PERMITE AGREGAR LAS SOLICITUDES DE PAGO AL GRID//
function agregarSN(){
	if ($('id_contribuyente').value =="0"){
		
		alert("Primero debe Seleccionar un Contribuyente.");
		return;
		
	}else if($('tipo_sancion').value=="0"){
	
		alert("Primero debe Seleccionar una Sancion para Agregar.");
		return;
		
	}else{
		
		for(j=0;j<i;j++){
		
			if(mygridsn.getRowIndex(j)!=-1){
				
				if (mygridsn.cells(mygridsn.getRowId(j),'0').getValue() == $('tipo_sancion').value){
						
					alert("La Sancion Ya Se ha Agregado");
					return false;

				}
			}
		}
		
		
		mygridsn.addRow(i, $('id_sancion').value+","+ $('monto_sancion').value);
		
		//mygridsn.addRow(i,$('tipo_sancion').value+","+$('monto_fac').value+","+$('monto_ret').value+","+$('total_sol').value);
		i++;
		sumaTotal();
	}
}

//ESTA FUNCION PERMITE SUMAR EL TOTAL DE TODAS LAS SANCIONES AGREGADAS AL GRID
function sumaTotal(){
	var total = 0;
	var r = 0;
	for(j=0;j<i;j++){
		if(mygridsn.getRowIndex(j)!=-1){
			total += parseFloat(mygridsn.cells(j,1).getValue()); 
		}
	}
	r = muestraFloat(total);
	$('total').value  = r;
}

	function muestraRet(){
	//if($F('nroref') != '0')
		Effect.toggle('facrelDiv', 'blind');
	/*else{
		alert('Debe seleccionar un documento');
		$('nroref').focus();
	}*/
}

	function EliminarSP(){

	mygridns.deleteRow(mygridns.getSelectedId());
	sumaTotal();

} 
	
</script>

<?
$validator->create_message("error_placa", "placa", "*");
$validator->create_message("error_serial_carroceria", "serial_carroceria", "*");
$validator->create_message("error_serial_motor", "serial_motor", "*");
//$validator->create_message("error_anio_pago", "anio_pago", "*");
$validator->create_message("error_anio_veh", "anio_veh", "*");
$validator->create_message("error_fec_compra", "fec_compra", "*");
$validator->create_message("error_precio", "precio", "*");
$validator->create_message("error_peso_kg", "peso_kg", "*");
$validator->create_message("error_id_contribuyente", "id_contribuyente", "*");  
$validator->print_script();
?>