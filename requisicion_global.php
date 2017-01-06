<?
require ("comun/ini.php");
$hoy=date("Y-m-d");
$oRequisiciones = new requisicion_global;
$accion = $_REQUEST['accion'];
if ($accion=='Agrupar'){
	$oRequisiciones->add($conn, $_POST['ano'], guardaFecha($_POST['fecha']),
	$_POST['motivo'], $usuario->id, '05', $_REQUEST['requisicion']);
}else if ($accion =='Eliminar'){
	$oRequisiciones->del($conn,$_POST['id']);
}
	
$msj = $oRequisiciones->msj; // lleno esta variable con el mensaje de la operacion llevada a cabo
$cRequisiciones=$oRequisiciones->get_all($conn, $escEnEje);
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? } ?>
<script type="text/javascript">var mygrid,i=0, ipp=0</script>
<br />
<span class="titulo_maestro">Requisicion Global</span>
<div id="formulario">
	<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />
<div id="contenidobuscador">
<fieldset id="buscador">
	<legend>Buscar:</legend>
	<table border="0">
		<tr>
			<td colspan="3">Codigo:</td>
		</tr>
		<tr>
			<td colspan="3"><input type="text" name="nrorequi" id="nrorequi" maxlength="9"></td>
		</tr>
		<tr>
			<td colspan="3">Motivo:</td>
		</tr>
		<tr>
			<td colspan="3"><input type="text" name="search_motivo" id="search_motivo" style="width:250px"></td>
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
	if(parseFloat($('cantREG').value)>0){	
		if(elemento.value == 'Agrupar'){
			$('accion').value = 'Agrupar';
		} else if(elemento.value == 'Aceptar'){
			$('accion').value = 'Aceptar';
		} else if(elemento.value == 'Eliminar'){
			$('accion').value = 'Eliminar';
		}
			validate(); 
	} else {
		alert('Debe seleccionar al menos una requisicion para agrupar');
		return false;
	}	
}

/* Metodos utilizados en el buscador */
function busca(nrorequi, fecha_desde, fecha_hasta,  motivo, pagina){
	var url = 'updater_busca_requisicion_global.php';
	var pars = 'nrorequi='+ nrorequi +'&fecha_desde=' + fecha_desde+ '&fecha_hasta=' + fecha_hasta +'&motivo=' + motivo + '&pagina=' + pagina;
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
	busca($F('nrorequi'), 
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta'),
	$F('search_motivo'),
	1)
});

Event.observe('search_motivo', "change", function () { 
	busca($F('nrorequi'), 
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta'),
	$F('search_motivo'),
	1)
});


function validafecha(fecha){
	var upper = 31;
	if(/^(\d{2})\/(\d{2})\/(\d{4})$/.test(fecha.value)) { // dd/mm/yyyy
		if(RegExp.$2 == '02') upper = 29;
		if((RegExp.$1 <= upper) && (RegExp.$2 <= 12)) {
			busca($F('nrorequi'),
			$F('busca_fecha_desde'), 
			$F('busca_fecha_hasta'),
			$F('search_motivo'),
			1)
		} else {
			alert("Fecha incorrecta");
			fecha.value = "";
		}
	}else if(fecha.value != '') {
		alert("Fecha incorrecta");
		fecha.value = "";
	}
}

/*	//MANEJO DE LAS CATEGORIAS PROGRAMATICAS//
	function traeCategoriasProgramaticas(ue){
	
	var url = 'updater_selects.php';
	var pars = 'combo=categorias_programaticas_x_productos&ue=' + ue +'&ms='+new Date().getTime();
		
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


/*//TRAE LAS PARTIDAS SEGUN EL PRODUCTO Y LA CATEGORIA QUE SE ESCOJA	
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
		var idprod = $('productos').value;	
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
						
						 if( parseFloat($('disponible').value) < parseFloat($('total_prod').value)){
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
}*/

	//ESTA FUNCION PERMITE SUMAR EL TOTAL DE MONTOS ASIGNADOS DE CADA PARTIDA PRESUPUESTARIA POR CATEGORIA PROGRAMATICA AGREGADOS AL GRID


	function Guardar()
	{
		var JsonAux,requisicion=new Array;
			mygridreg.clearSelection()
			for(j=0;j<mygridreg.getRowsNum();j++)
			{
				if(!isNaN(mygridreg.getRowId(j)))
				{
					
						if(mygridreg.cells(mygridreg.getRowId(j),3).isChecked()){
							requisicion[j] = new Array;
							requisicion[j][0]= mygridreg.cells(mygridreg.getRowId(j),0).getValue();
							requisicion[j][1]= mygridreg.cells(mygridreg.getRowId(j),1).getValue();
							requisicion[j][2]= mygridreg.cells(mygridreg.getRowId(j),2).getValue();
							requisicion[j][3]= mygridreg.getRowId(j);
						}						
				}
			}
			JsonAux={"requisicion":requisicion};
			$("requisicion").value=JsonAux.toJSONString(); 
						
	}
	
	function ver_partpre(){
		Effect.toggle('partpreDiv', 'blind');
	}	
	
	
	function mostrar_ventana(){
		 
		 popup('buscar_proveedores_cotizacion.php?id_requisicion='+$('id').value, 'Cotizaciones', 'menubar=no, height=310, width=320, top=300, left=400, scrollbars=no, resizable=no ')
	}
	
	function CargarGrid(){
		
		var ano = $('ano').value
		var url = 'json.php';
		var pars = 'op=getProgRG&ano=' + ano;
	
		var myAjax = new Ajax.Request(
					url, 
					{
					method: 'get', 
					parameters: pars,
					onComplete: function(peticion){
					//alert(peticion.responseText);
						var jsonData = eval('(' + peticion.responseText + ')');
						mygridreg.clearAll();
						//alert(jsonData);
						if (jsonData == undefined) { return }
						
						for(i=0;i<jsonData.length;i++){
							
							mygridreg.addRow(i,jsonData[i]['id']+";"+jsonData[i]['unidad_ejecutora']+";"+jsonData[i]['motivo']+";0");
						}

						
					}
					}
				);
			 
	}
	
	function busca_ano(){
		if($('ano').value.length == 4)
			CargarGrid();
	}
	
	function checkMarcado(rowId, checked, cellInd)
	{
	//alert("rowId: "+rowId+" cell: "+ cellInd+" checked:"+checked);
		var cuenta = parseFloat($('cantREG').value);
		if (checked == 1)
				cuenta+= 1;
		else
				cuenta-=1;
		$('cantREG').value = cuenta.toString();	
	}

</script>
<div id="xxx"></div>

<? 
//$validator->create_message("error_nrodoc", "nrodoc", "*");
$validator->create_message("error_fecha", "fecha", "*");
$validator->create_message("error_ano", "ano", "*");
$validator->create_message("error_motivo", "motivo", "*");
$validator->print_script();
require ("comun/footer.php");
?>
