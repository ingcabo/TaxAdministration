<?
require ("comun/ini.php");
// Creando el objeto contrato_obras
$hoy=date("Y-m-d");
$AnalisisCotizacion = new analisis_cotizacion;
$accion = $_REQUEST['accion'];
if($accion == 'Analizar'){
	//die($_REQUEST['contrato']);
	$id_requisicion = $_POST['id'];
	 if($ganador=$AnalisisCotizacion->getCotizacionesGanadoras($conn, 
										$_POST['id']	
										))
	 	if($AnalisisCotizacion->creaOrdenCompra($conn, $_POST['id'], $escEnEje))
	 		$AnalisisCotizacion->set_status($conn,'08',$_POST['id']);
	//die();
	 ?>
	 <input type="hidden" name="ganador" id="ganador" value="<?=$ganador?>" />
	 <script language="javascript" type="text/javascript">
	 function muestraReporte(idr,ganador){
		//var idr= $('id').value
		window.open('reporte_analisis_cotizacion.pdf.php?id_requisicion='+idr+'&prov_ganador='+ganador,'', ' menubar=yes, height=900, width=1200, top=1, left=1, scrollbars=no, resizable=no, toolbar=yes ');
	}
	</script>
	<script type="text/javascript">
		muestraReporte(<?="'".$id_requisicion."'"?>,<?=$ganador?>);
	</script>
<?	
}

$msj = $AnalisisCotizacion->msj; // lleno esta variable con el mensaje de la operacion llevada a cabo
$cActuCotizacion=$AnalisisCotizacion->get_all($conn, $escEnEje);
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? } ?>
<script type="text/javascript">var mygrid,i=0, ipp=0</script>
<br />
<span class="titulo_maestro">Analisis de Cotizaciones</span>
<div id="formulario">
	
</div>
<br />
<div id="contenidobuscador">
<fieldset id="buscador">
	<legend>Buscar:</legend>
	<table border="0">
		<tr>
			<td colspan="3">Codigo de Requisicion</td>
		</tr>
		<tr>
			<td colspan="3"><input type="text" name="nrorequi" id="nrorequi" size="20"></td>
		</tr>
	<tr>
			<td>Desde</td>
			<td colspan="2">Hasta</td>
				
		</tr>
		<tr>
			<td colspan="3">
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
</div>
<br />
<div style="margin-bottom:10px" id="busqueda"><div>
<br />
<div style="height:40px;padding-top:10px;">
<p id="cargando" style="display:none;margin-top:0px;">
  <img alt="Cargando" src="images/loading.gif" /> Cargando...
</p>
</div>
<script type="text/javascript">
/***************       Seccion de las Partidas ******************************/
function actapr(elemento){
	if(elemento.value == 'Actualizar'){
		$('accion').value = 'Actualizar';
	}else if(elemento.value == 'Aprobar') {
		$('accion').value = 'Aprobar'; 
	} else
		$('accion').value = 'Anular';
	 validate(); 
}

/* Metodos utilizados en el buscador */
function busca(fecha_desde, fecha_hasta,nrequi){
	var url = 'updater_busca_cotizaciones.php';
	var pars = '&fecha_desde=' + fecha_desde+ '&fecha_hasta=' + fecha_hasta + '&estado=07&nrequi=' + nrequi;
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


Event.observe('nrorequi', "change", function () { 
	busca( 
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta'),
	$F('nrorequi'))
});

function validafecha(fecha){
	var upper = 31;
	if(/^(\d{2})\/(\d{2})\/(\d{4})$/.test(fecha.value)) { // dd/mm/yyyy
		if(RegExp.$2 == '02') upper = 29;
		if((RegExp.$1 <= upper) && (RegExp.$2 <= 12)) {
			busca(
			$F('busca_fecha_desde'), 
			$F('busca_fecha_hasta'),
			$F('nrorequi'))
		} else {
			alert("Fecha incorrecta");
			fecha.value = "";
		}
	}else if(fecha.value != '') {
		alert("Fecha incorrecta");
		fecha.value = "";
	}
}


	//ESTA FUNCION PERMITE SUMAR EL TOTAL DE MONTOS ASIGNADOS DE CADA PARTIDA PRESUPUESTARIA POR CATEGORIA PROGRAMATICA AGREGADOS AL GRID

function sumaTotal(){
	var totalCoti = 0;
	for(j=0;j<i;j++){
		if(mygridre.getRowId(j)!= undefined){
			totalCoti += parseFloat(mygridre.cells(mygridre.getRowId(j),3).getValue());
		}
	}
	$('totalC').value = (isNaN(totalCoti))? '0' : muestraFloat(totalCoti);

}

	 function muestraReporte(){
		var idr= $('id').value
		window.open("reporte_analisis_cotizacion.pdf.php?id_requisicion="+idr+"&prov_ganador="+$('ganador').value,'', ' menubar=no, height=900, width=1200, top=1, left=1, scrollbars=no, resizable=no ');
	}
	
	//ESTA FUNCION SE USA PARA CERRAR EL DIVIDER SIN MOSTRAR EL MENSAJE DE AGREGAR NUEVO
	function close_div_compras(){
		$('formulario').innerHTML = '';
	
	}

	

	
	
</script>
<div id="xxx"></div>
<?
$validator->create_message("error_ue", "unidad_ejecutora", "*");
$validator->create_message("error_ano", "ano", "*");
$validator->print_script();
require ("comun/footer.php");
?>

