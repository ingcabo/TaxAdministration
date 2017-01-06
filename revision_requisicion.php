<?
set_time_limit(0);
require ("comun/ini.php");
$hoy=date("Y-m-d");
$oRequisiciones = new revision_requisicion;
$accion = $_REQUEST['accion'];
if ($accion=='Recibir'){
	$oRequisiciones->set_status($conn, '04', $_POST['id']);
}
	
$msj = $oRequisiciones->msj; // lleno esta variable con el mensaje de la operacion llevada a cabo
$cRequisiciones=$oRequisiciones->get_all($conn, $escEnEje);
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? } ?>
<script type="text/javascript">var mygrid,i=0, ipp=0</script>
<br />
<span class="titulo_maestro">Revision de Requisiciones</span>
<div id="formulario">
<!--<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>-->
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
			"SELECT DISTINCT id, id ||' - '|| descripcion AS descripcion FROM puser.unidades_ejecutoras")?></td>
		</tr>
		<tr>
			<td colspan="3">Codigo:</td>
		</tr>
		<tr>
			<td colspan="3"><input type="text" name="nrorequi" id="nrorequi" maxlength="9"></td>
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
	
	if(elemento.value == 'Recibir'){
		$('accion').value = 'Recibir';
	} else if(elemento.value == 'Aceptar'){
		$('accion').value = 'Aceptar';
	}
		 document.form1.submit(); 
}

/* Metodos utilizados en el buscador */
function busca(id_ue, fecha_desde, fecha_hasta,  nrorequi){
	var url = 'updater_busca_revision_requisicion.php';
	var pars = '&id_ue=' + id_ue + '&fecha_desde=' + fecha_desde+ '&fecha_hasta=' + fecha_hasta +'&nrorequi=' + nrorequi;
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
	$F('busca_fecha_hasta'),
	$F('nrorequi'))
});

Event.observe('nrorequi', "change", function () { 
	busca($F('busca_ue'), 
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta'),
	$F('nrorequi'))
});


function validafecha(fecha){
	var upper = 31;
	if(/^(\d{2})\/(\d{2})\/(\d{4})$/.test(fecha.value)) { // dd/mm/yyyy
		if(RegExp.$2 == '02') upper = 29;
		if((RegExp.$1 <= upper) && (RegExp.$2 <= 12)) {
			busca($F('busca_ue'),  
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

	//MANEJO DE LAS CATEGORIAS PROGRAMATICAS//
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
}


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
}

	//ESTA FUNCION PERMITE SUMAR EL TOTAL DE MONTOS ASIGNADOS DE CADA PARTIDA PRESUPUESTARIA POR CATEGORIA PROGRAMATICA AGREGADOS AL GRID

function sumaTotal(){
	var totalRequis = 0;
	for(j=0;j<i;j++){
		if(mygridre.getRowId(j)!= undefined){
			totalRequis += parseFloat(mygridre.cells(mygridre.getRowId(j),7).getValue());
		}
	}
	$('montoRE').value = (isNaN(totalRequis))? '0' : muestraFloat(totalRequis);

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
	
	function traeProductos(cp){
	
	var url = 'updater_selects.php';
	var pars = 'combo=productos&cp=' + cp;
		
	var updater = new Ajax.Updater('divcomboprod', 
		url,
		{
			method: 'get',
			parameters: pars,
			asynchronous:true, 
			evalScripts:true,
			onLoading:function(request){Element.show('cargador_categorias')}, 
			onComplete:function(request){Element.hide('cargador_categorias')}
		});
}

	//FUNCION QUE TRAE LA DISPONIBILIDAD DE LAS PARTIDAS PRESUPUESTARIAS CUANDO SE SELECCIONA EN EL GRID//
function traerPartidasSeleccionada(rowId){

	var cp = mygridre.cells(rowId,1).getValue();
	var pp = mygridre.cells(rowId,2).getValue();
	$('despachado').value = mygridre.cells(rowId,4).getValue();
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
	//SIRVE PARA ACTUALIZAR LOS VALOERES DE DESPACHADO Y RECIBIDO DE CADA PRODUCTO EN LA REQUISICION
	
	function cambiaDespachado(rowId){
		var cantd = parseInt(mygridre.cells(rowId,4).getValue());
		var cants = parseInt(mygridre.cells(rowId,3).getValue());
		var id_produ = parseInt(mygridre.cells(rowId,0).getValue());

		
		if	(cants<cantd){
			alert("La cantidad despachada no puede ser mayor a la requerida");
			mygridre.cells(rowId,4).setValue(parseInt($('despachado').value));
		} else {
			mymygridre.cells(rowId,5).setValue(cants-cantd);
			var url = 'json.php';
			var pars = 'op=setdespacho&cd=' + cantd +'&id_prod='+ id_produ +'&id='+$('id').value;
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

	function mostrar_ventana(){
	
		 popup('buscar_proveedores_cotizacion.php?id_productos='+$('id_productos').value+'&id_requisicion='+$('id').value, 'Cotizaciones', 'menubar=no, height=310, width=320, top=300, left=400, scrollbars=no, resizable=no ')
}

</script>
<div id="xxx"></div>
<?
$validator->print_script();
require ("comun/footer.php");
?>
