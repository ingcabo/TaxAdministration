<?
set_time_limit(0);
require ("comun/ini.php");
// Creando el objeto contrato_obras
$hoy=date("Y-m-d");
$oRequisiciones = new requisiciones;
$accion = $_REQUEST['accion'];
if($accion == 'Guardar'){
	//die($_REQUEST['contrato']);
	 $oRequisiciones->add($conn, 
										$_POST['unidad_ejecutora'],
										$_POST['ano'],
										guardafecha($_POST['fecha']),
										$_POST['motivo'],
										'01',
										$usuario->id,
										$_REQUEST['requisicion']);
}elseif($accion == 'Aprobar'){
//die($_REQUEST['contrato']);
	$oRequisiciones->aprobar($conn, 
									$_POST['id'],
									$_POST['unidad_ejecutora'],
									$_POST['ano'],
									$_POST['motivo'],
									$usuario->id,
									guardafecha($_POST['fecha']),
									guardafecha($hoy),
									$_REQUEST['requisicion']);
}elseif($accion == 'Actualizar'){
	$oRequisiciones->set($conn, 
									$_POST['id'], 
									$_POST['unidad_ejecutora'],
									$_POST['ano'],
									$usuario->id,
									$_POST['motivo'],
									guardafecha($_POST['fecha']),
									$_REQUEST['requisicion']
									);
}elseif($accion == 'del'){
	//$oContratoObras->del($conn, $_POST['id']);
		$msj = ERROR;
	}elseif ($accion == 'Anular'){
		$oRequisiciones->anular($conn, $_POST['id'],$usuario->id,$_POST['unidad_ejecutora'],$_POST['ano'], $_POST['motivo'], guardafecha($_POST['fecha']), $_REQUEST['status'], $_REQUEST['requisicion']);
			
	}

$msj = $oRequisiciones->msj; // lleno esta variable con el mensaje de la operacion llevada a cabo
$cRequisiciones=$oRequisiciones->get_all($conn, $escEnEje);
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<script type="text/javascript">var mygrid,i=0, ipp=0</script>
<br />
<span class="titulo_maestro">Requisiciones</span>
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
			"SELECT DISTINCT id, id || ' - ' || descripcion AS descripcion FROM puser.unidades_ejecutoras")?></td>
		</tr>
		<tr>
			<td>Status:</td>
			<td><select name="busca_status" id="busca_status">
					<option value="0">Seleccione</option>
					<option value="01">Registrada</option>
					<option value="02">Aprobada</option>
					<option value="03">Anulada</option>
					<option value="04">Recibida por Compras</option>
					<option value="05">Solicitud de Cotizacion</option>
					<option value="06">Cotizada</option>
					<option value="07">Orden de Compra</option>
				</select></td>
		</tr>
		<tr>
			<td>N&ordm; Requisici&oacute;n </td>
			<td><input type="text" name="busca_nrequi" id="busca_nrequi" /></td>
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
function busca(id_ue, fecha_desde, fecha_hasta, pagina, status, nrequi){
	var url = 'updater_busca_requisiciones.php';
	var pars = '&id_ue=' + id_ue + '&fecha_desde=' + fecha_desde+ '&fecha_hasta=' + fecha_hasta + '&pagina=' + pagina + '&status=' + status + '&nrequi=' + nrequi;
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
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta') ,
	 '1',
	$F('busca_status'),
	$F('busca_nrequi'))
});

Event.observe('busca_status', "change", function () { 
	busca($F('busca_ue'), 
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta') ,
	 '1',
	$F('busca_status'),
	$F('busca_nrequi'))
});

Event.observe('busca_nrequi', "change", function () { 
	busca($F('busca_ue'), 
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta') ,
	 '1',
	$F('busca_status'),
	$F('busca_nrequi'))
});

function validafecha(fecha){
	var upper = 31;
	if(/^(\d{2})\/(\d{2})\/(\d{4})$/.test(fecha.value)) { // dd/mm/yyyy
		if(RegExp.$2 == '02') upper = 29;
		if((RegExp.$1 <= upper) && (RegExp.$2 <= 12)) {
			busca($F('busca_ue'),  
			$F('busca_fecha_desde'), 
			$F('busca_fecha_hasta'),
			'1',
			$F('busca_status'),
			$F('busca_nrequi'))
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
	//MANEJO DE LAS CATEGORIAS PROGRAMATICAS//
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

	function selCategorias(id, nombre){

	$('txtcategorias_programaticas').value = nombre;
	$('categorias_programaticas').value = id;
	$('bproductos').style.display = 'inline';
	Dialog.okCallback();

}

function mostrarBuscarCat(){
	mygridre.clearAll();
	$('bcategorias').style.display = 'inline';

}


	/*function traePartidasPresupuestarias(cp){
	
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
} */

//TRAE LAS PARTIDAS SEGUN EL PRODUCTO Y LA CATEGORIA QUE SE ESCOJA	
function traePartidasPorProductos(){
	//alert("entro");
	if ($('unidad_ejecutora').value =="0"){
		
		alert("Primero debe Seleccionar una Unidad Ejecutora.");
		return;
		
	}else if($('categorias_programaticas').value=="0"){
	
		alert("Primero debe Seleccionar una Categoria Programatica.");
		return;
	} else if($('cantidad').value=="" || parseFloat($('cantidad').value) < 0.1){
		
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
						if (jsonData == undefined) { return }
						$('disponible').value = jsonData['disponible'];
						$('idParCat').value = jsonData.idparcat;
						$('total_prod').value = jsonData.total;
						$('partidas_presupuestarias').value = jsonData.id_partida;
						$('puedo').value = jsonData.puedo;
						$('precio').value = jsonData.precio;
						var acumula_total_partida = 0;
						for(j=0;j<i;j++){
							if(mygridre.cells(mygridre.getRowId(j),2).getValue()==$('partidas_presupuestarias').value){
								acumula_total_partida = acumula_total_partida + parseFloat(mygridre.cells(mygridre.getRowId(j),7).getValue());
							}
						}
						acumula_total_partida = parseFloat($('total_prod').value) +acumula_total_partida;
						 /*if( parseFloat($('disponible').value) < acumula_total_partida){
							alert("El monto disponible en la partida es menor al requerido");
							$('cantidad').value='0';
							return;	
					
								}else{*/
								AgregarRE();
								//}
					}
					}
				);
			 
	}

function AgregarRE(){
	//alert($('unidad_ejecutora').value);
		
	 
	
	
		
		for(j=0;j<i;j++){
			
			if (mygridre.getRowId(j)!=undefined){
				if (mygridre.cells(mygridre.getRowId(j),'1').getValue() == $('categorias_programaticas').value && mygridre.cells(mygridre.getRowId(j),'2').getValue() == $('partidas_presupuestarias').value &&  mygridre.cells(mygridre.getRowId(j),'0').getValue() == $('id_producto').value){
						
					alert('Esta producto ya ha sido seleccionado para esta categoria, por favor seleccione otra categoria');
					return false;

				}
			
			}
			
			
		}
		
		//mygridre.addRow(1,";;;;;;;");
		mygridre.addRow(i,$('id_producto').value+";"+$('categorias_programaticas').value+";"+$('partidas_presupuestarias').value+";"+$('cantidad').value+";0;0;"+parseFloat($('precio').value)+";"+parseFloat($('total_prod').value));
		i++;
		sumaTotal();
		$('cantidad').value='0';
		
	}
}

function EliminarRE(){
	mygridre.deleteRow(mygridre.getSelectedId());
	i--;
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

function sumaTotal(){
	var totalRequis = 0;
	for(j=0;j<i;j++){
		if(mygridre.getRowId(j)!= undefined){
			totalRequis += parseFloat(mygridre.cells(mygridre.getRowId(j),7).getValue());
		}
	}
	$('montoRE').value = (isNaN(totalRequis))? '0' : muestraFloat(totalRequis);
	//$('montoCOB').value = (isNaN(totalRequis))? '0' : muestraFloat(totalRequis);

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
		var JsonAux,requisicion=new Array;
			mygridre.clearSelection()
			for(j=0;j<i;j++)
			{
				if(!isNaN(mygridre.getRowId(j)))
				{
					requisicion[j] = new Array;
					requisicion[j][0]= mygridre.cells(mygridre.getRowId(j),0).getValue();
					requisicion[j][1]= mygridre.cells(mygridre.getRowId(j),1).getValue();
					requisicion[j][2]= mygridre.cells(mygridre.getRowId(j),2).getValue();
					requisicion[j][3]= mygridre.cells(mygridre.getRowId(j),3).getValue();
					requisicion[j][4]= mygridre.cells(mygridre.getRowId(j),4).getValue();
					requisicion[j][5]= mygridre.cells(mygridre.getRowId(j),5).getValue();
					requisicion[j][6]= mygridre.cells(mygridre.getRowId(j),6).getValue();
					requisicion[j][7]= mygridre.cells(mygridre.getRowId(j),7).getValue();
					requisicion[j][8]= mygridre.getRowId(j);			
				}
			}
			JsonAux={"requisicion":requisicion};
			$("requisicion").value=JsonAux.toJSONString(); 
	}
	
	function ver_partpre(){
		Effect.toggle('partpreDiv', 'blind');
	}	
	
	function traeProductos(id, descripcion){
	
	$('productos').value = descripcion;
	$('id_producto').value = id;
	$('cantidad').value = '0';
	Dialog.okCallback();

}

	//FUNCION QUE TRAE LA DISPONIBILIDAD DE LAS PARTIDAS PRESUPUESTARIAS CUANDO SE SELECCIONA EN EL GRID//
function traerPartidasSeleccionada(rowId){

	var cp = mygridre.cells(rowId,1).getValue();
	var pp = mygridre.cells(rowId,2).getValue();
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

	function mostrar_ventana(){
	 
	var url = 'buscar_productos.php';
	var pars = 'id_cp='+$('categorias_programaticas').value+'&ms'+new Date().getTime();
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

//BUSCA EL PRODUCTO POR LA DESCRIPCION

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

function imprimir()
	{
		var id_requisicion = $('id').value;
		wxR = window.open("reporte_requisicion.pdf.php?id_requisicion=" +id_requisicion+"&preRequisicion=1", "winX", "width=500, height=500, scrollbars=yes, resizable=yes, status=yes");
		wxR.focus()
	}


</script>
<div id="xxx"></div>
<?
$validator->create_message("error_ue", "unidad_ejecutora", "*");
$validator->create_message("error_ano", "ano", "*");
$validator->create_message("error_motivo", "motivo", "*");
$validator->print_script();
require ("comun/footer.php");
?>
