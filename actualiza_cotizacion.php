<?
require ("comun/ini.php");
// Creando el objeto contrato_obras
$hoy=date("Y-m-d");
$ActuCotizacion = new actualiza_cotizacion;
$accion = $_REQUEST['accion'];
if($accion == 'Guardar'){
	//die($_REQUEST['contrato']);
	 $ActuCotizacion->setCostoArticuloProveedor($conn, 
										$_POST['id'],
										$_REQUEST['cotizacion'],
										$_POST['proveedores']		
										);
}

$msj = $ActuCotizacion->msj; // lleno esta variable con el mensaje de la operacion llevada a cabo
$cActuCotizacion=$ActuCotizacion->get_all($conn, $escEnEje);
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? } ?>
<script type="text/javascript">var mygrid,i=0, ipp=0</script>
<? if(!empty($_POST['id'])){
	$aux = new actualiza_cotizacion;
	$aux->get($conn,$_POST['id'],$escEnEje);
	//var_dump($aux->status);
	//die("aqui ".$aux->status);
	if ($aux->status == '06'){?>
		<script type="text/javascript">
			setTimeout("updater('<?=$_POST['id']?>');",1000);
		</script>
	<? } 
	} ?>
<br />
<span class="titulo_maestro">Actualizacion de cotizaciones</span>
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
			<td colspan="3"><input type="text" name="nrorequi" id="nrorequi" size="20" maxlength="9"></td>
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
	var pars = '&id_ue=&fecha_desde=' + fecha_desde+ '&fecha_hasta=' + fecha_hasta + '&estado=06&nrequi=' + nrequi;
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
			totalCoti += usaFloat(mygridre.cells(mygridre.getRowId(j),4).getValue());
		}
	}
	$('totalC').value = (isNaN(totalCoti))? '0' : muestraFloat(totalCoti);

}

	function Guardar()
	{
		var JsonAux,cotizacion=new Array;
			mygridre.clearSelection()
			for(j=0;j<mygridre.getRowsNum();j++)
			{
				if(!isNaN(mygridre.getRowId(j)))
				{
					//if(usaFloat(mygridre.cells(mygridre.getRowId(j),4).getValue())>0){
						var iva = parseFloat(mygridre.cells(mygridre.getRowId(j),2).getValue())
						//alert(iva);
						//Se decidio utilizar este valor porque el grid no acepta valores nulos, los convierte a 0, y existe la posibilidad de que haya un IVA de valor 0
						if(iva != 199){ 
							cotizacion[j] = new Array;
							cotizacion[j][0]= mygridre.cells(mygridre.getRowId(j),0).getValue();
							cotizacion[j][1]= mygridre.cells(mygridre.getRowId(j),1).getValue();
							cotizacion[j][2]= mygridre.cells(mygridre.getRowId(j),2).getValue();
							cotizacion[j][3]= mygridre.getRowId(j);	
						}else{
							alert('Debe seleccionar el valor del iva para cada uno de los productos');
							return false;
						}
					/*} else {
						alert("No pueden existir valores menores a 0 Bs")
						return false;
					}*/		
				}
			}
			JsonAux={"cotizacion":cotizacion};
			$("cotizacion").value=JsonAux.toJSONString();
			$("form1").submit(); 
	}
	
	
	
	function buscaProductosProveedor(id){
		var id_requi = $('id').value;
		var url = 'json.php';
		var pars = 'op=prodcotizacion&idp=' + id +'&idr='+ id_requi;		
		mygridre.clearAll();
		var myAjax = new Ajax.Request(
					url, 
					{
					method: 'get', 
					parameters: pars,
					onComplete: function(peticion){
						var jsonData = eval('(' + peticion.responseText + ')');
						if (jsonData == undefined) { return }
						for(i=0;i<jsonData.length;i++){
							//alert(jsonData[i]['iva']);
							
							var impuesto = (jsonData[i]['costo'] * (jsonData[i]['iva']/100)) * jsonData[i]['cantidad'];
							var total = (jsonData[i]['costo'] * jsonData[i]['cantidad'] + impuesto);
							if(!jsonData[i]['costo'])
								var costo = 0
							else
								var costo = jsonData[i]['costo']; 
							mygridre.addRow(i,jsonData[i]['id']+";"+muestraFloat(costo)+";"+jsonData[i]['iva']+";"+muestraFloat(jsonData[i]['cantidad'])+";"+muestraFloat(total));
						}
					}
					}
				);
				setTimeout('sumaTotal()',2000);
	}
	
	
	//SIRVE PARA ACTUALIZAR LOS VALOERES DE DESPACHADO Y RECIBIDO DE CADA PRODUCTO EN LA REQUISICION
	function actualizaPrecio(RowId){
		var costo = parseFloat(mygridre.cells(rowId,1).getValue());
		var id_produ = parseInt(mygridre.cells(rowId,0).getValue());
		var iva = parseFloat(mygridre.cells(rowId,2).getValue());
		var id_requi = $('id').value;
		
		if	(costo<1){
			alert("El costo no puede ser 0 Bs");
		} else if(iva == 199){
			alert("Debe seleccionar un valor para el iva")
		} else {
			var url = 'json.php';
			var pars = 'op=setdespacho&costo=' + costo +'&id_prod='+ id_produ +'&idr='+ id_requi;
			var myAjax = new Ajax.Request(
			url,
			{
				method: 'get',
				parameters: pars,
				onComplete: function(peticion){
				
					var jsonData = eval('(' + peticion.responseText + ')');
					if (jsonData == undefined) { return }
						alert(peticion.responseText);
				}
			}
		);
	  }		
	}
	
	function generaTotal(rowId){
		if (mygridre.cells(rowId,2).getValue()==199){
			alert("Debe introducir el valor del IVA para el producto");
			return false;
			} else 
				if (mygridre.cells(rowId,1).getValue()==''){
					alert("Debe introducir el valor del producto");
					return false;
				} else { 
				var costo = parseFloat(mygridre.cells(rowId,1).getValue());
				var cant = usaFloat(mygridre.cells(rowId,3).getValue());
				var iva = parseFloat(mygridre.cells(rowId,2).getValue());
				//alert('costo: '+costo+' cant: '+cant+' iva: '+iva);
				var impuesto = (costo * (iva/100)) * cant
				var total = costo * cant + impuesto;
				mygridre.cells(rowId,1).setValue(muestraFloat(costo));
				mygridre.cells(rowId,4).setValue(muestraFloat(total));
				sumaTotal();
			}
	}
	
</script>
<div id="xxx"></div>
<?
$validator->create_message("error_ue", "unidad_ejecutora", "*");
$validator->create_message("error_ano", "ano", "*");
$validator->print_script();
require ("comun/footer.php");
?>
