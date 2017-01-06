<?
require ("comun/ini.php");
// Creando el objeto contrato_obras
$hoy=date("Y-m-d");
$RecepcionOC = new recepcion_orden_compra;
$accion = $_REQUEST['accion'];
if($accion == 'Recibir'){
	//die($_REQUEST['contrato']);
	 $RecepcionOC->add($conn, 
							$_POST['id'],
							$_POST['nrorequi'],
							$_POST['nrofact'],
							$_POST['total_parcial'],	
							$_POST['comentario'],
							date("Y-m-d"),
							$usuario->id,
							$_REQUEST['recepcion'],
							$_REQUEST['recepcionDet'],
							$_POST['nrocontrol']);
	 
} elseif($accion == 'Actualizar'){
	$RecepcionOC->set($conn,
						$_POST['id'],
						$_POST['nrofact'],
						$_POST['total_parcial'],	
						$_POST['comentario'],
						$_POST['nrorequi'],
						$_REQUEST['recepcion'],
						$_REQUEST['recepcionDet'],
						$_POST['nrocontrol']);
}

$msj = $RecepcionOC->msj; // lleno esta variable con el mensaje de la operacion llevada a cabo
$cRecepcion=$RecepcionOC->get_all($conn, $escEnEje);
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? } ?>
<script type="text/javascript">var mygrid,i=0, ipp=0</script>
<br />
<span class="titulo_maestro">Recepcion Orden de Compra</span>
<div id="formulario">

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
			"SELECT DISTINCT id, id||' - '||descripcion AS descripcion FROM unidades_ejecutoras ORDER BY descripcion")?></td>
		</tr>
		<tr>
			<td colspan="3">N&ordm; Requisici&oacute;n Global </td>
		</tr>
		<tr>
			<td colspan="3"><input type="text" name="nrorequi" id="nrorequi" size="20"></td>
		</tr>
        <tr>
			<td colspan="3">N&ordm; Orden de Compra: </td>
		</tr>
		<tr>
			<td colspan="3"><input type="text" name="nroOrdCompra" id="nroOrdCompra" size="20"></td>
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
							<a href="#" id="boton_busca_fecha_desde" onClick="return false;">
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
							<a href="#" id="boton_busca_fecha_hasta" onClick="return false;">
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
var oldCantGbl = 0;
var oldCantReq = 0;

var reqGbl = [];

var reqRpt = [];


function actapr(elemento){
	if(elemento.value == 'Recibir'){
		$('accion').value = 'Recibir';
	}else if(elemento.value == 'Actualizar') {
		$('accion').value = 'Actualizar'; 
	}
	 validate(); 
}

/* Metodos utilizados en el buscador */
function busca(id_ue, fecha_desde, fecha_hasta,nrorequi,nroordcompra,pagina){
	var url = 'updater_busca_recepcion_ordcompra.php';
	var pars = '&id_ue=' + id_ue + '&fecha_desde=' + fecha_desde+ '&fecha_hasta=' + fecha_hasta + '&nrorequi=' + nrorequi + '&nroordcompra=' + nroordcompra + '&estado=08' + '&pagina=' + pagina ;
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
	$F('nrorequi'),
	$F('nroOrdCompra'),1)
});

Event.observe('nrorequi', "change", function () { 
	busca($F('busca_ue'), 
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta'),
	$F('nrorequi'),
	$F('nroOrdCompra'),1)
});

Event.observe('nroOrdCompra', "change", function () { 
	busca($F('busca_ue'), 
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta'),
	$F('nrorequi'),
	$F('nroOrdCompra'),1)
});

function validafecha(fecha){
	var upper = 31;
	if(/^(\d{2})\/(\d{2})\/(\d{4})$/.test(fecha.value)) { // dd/mm/yyyy
		if(RegExp.$2 == '02') upper = 29;
		if((RegExp.$1 <= upper) && (RegExp.$2 <= 12)) {
			busca($F('busca_ue'),  
			$F('busca_fecha_desde'), 
			$F('busca_fecha_hasta'),
			$F('nrorequi'),
			$F('nroOrdCompra'),1)
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

function cambiaDespachoDet(Stage,rowId,index){
	//alert('Detalle Stage: '+Stage+' rowId'+rowId+'index:'+index)
	if((Stage == 0) && (index == 5) ){
			oldCantDet = mygridre.cells(rowId,5).getValue();
			//alert('aqui ' + oldCantGbl);
		}	
	
	if((Stage==2) && (index == 5)){
		
		if(mygridre.cells(rowId,5).getValue()==''){
			alert("Debe introducir la cantidad despachada");
			mygridre.cells(rowId,5).setValue(oldCantDet.toString());
			return false;
		} else {	
			var cantd = parseFloat(mygridre.cells(rowId,5).getValue());
			var cants = parseFloat(mygridre.cells(rowId,3).getValue());
			var cantr = parseFloat(mygridre.cells(rowId,4).getValue());
			if(!isNaN(cantd)){
				cantd = parseFloat(cantd);
				cantr = parseFloat(cantr);
			}else{
				cantd = 0;
				cantr = 0;
			}	
			var pendiente = cants-cantd;
			if	(cants<cantd){
				alert("La cantidad despachada no puede ser mayor a la requerida");
				mygridre.cells(rowId,5).setValue(oldCantDet.toString());
				return false;
			} else if(cantd<cantr){
				alert('No puede registrar menos ingreso de lo que ya tiene');
				mygridre.cells(rowId,5).setValue(oldCantDet.toString());
				return false;
			} else {
				//alert(typeof(reqGbl));
					var oldDespGbl = reqGbl[mygridre.cells(rowId,2).getValue()][0];
					var oldDespDet = reqGbl[mygridre.cells(rowId,2).getValue()][1];
					var aux = oldDespDet - oldCantDet + cantd;
					//alert('aux: oldDespDet '+oldDespDet+' oldCantDet '+oldCantDet+' cantd '+ cantd+' = '+aux )
					if(oldDespGbl<aux){
						alert('La suma del detalle para ese tipo de producto no puede ser mayor que la recibida en la global');
						mygridre.cells(rowId,5).setValue(oldCantDet.toString());
						return false;
					}else{
						mygridre.cells(rowId,5).setValue(cantd.toFixed(2));
						mygridre.cells(rowId,6).setValue(pendiente.toFixed(2));
						reqGbl[mygridre.cells(rowId,2).getValue()][1] = cantd.toFixed(2);
					}
			}
		}		
	}
}

	function Guardar(){
		
		//Aqui se carga el json del grid de requisicion global
		var JsonAux,recepcion=new Array;
			mygridregbl.clearSelection()
			var pend_gbl = 0;
			for(j=0;j<mygridregbl.getRowsNum();j++)
			{
				if(!isNaN(mygridregbl.getRowId(j)))
				{
						recepcion[j] = new Array;
						recepcion[j][0]= mygridregbl.cells(mygridregbl.getRowId(j),0).getValue();
						recepcion[j][1]= mygridregbl.cells(mygridregbl.getRowId(j),1).getValue();
						recepcion[j][2]= mygridregbl.cells(mygridregbl.getRowId(j),2).getValue();
						recepcion[j][3]= mygridregbl.cells(mygridregbl.getRowId(j),4).getValue();
						recepcion[j][4]= mygridregbl.getRowId(j);
						pend_gbl = 	pend_gbl + parseFloat(mygridregbl.cells(mygridregbl.getRowId(j),5).getValue())
				}
			}
			JsonAux={"recepcion":recepcion};
			$("recepcion").value=JsonAux.toJSONString(); 
			
			//Aqui se carga el json del grid de detalle
			
			var JsonAux,recepcionDet=new Array;
			mygridre.clearSelection()
			var pend_det = 0;
			for(j=0;j<mygridre.getRowsNum();j++)
			{
				if(!isNaN(mygridre.getRowId(j)))
				{
						recepcionDet[j] = new Array;
						recepcionDet[j][0]= mygridre.cells(mygridre.getRowId(j),0).getValue();
						recepcionDet[j][1]= mygridre.cells(mygridre.getRowId(j),1).getValue();
						recepcionDet[j][2]= mygridre.cells(mygridre.getRowId(j),2).getValue();
						recepcionDet[j][3]= mygridre.cells(mygridre.getRowId(j),3).getValue();
						recepcionDet[j][4]= mygridre.cells(mygridre.getRowId(j),4).getValue();
						recepcionDet[j][5]= mygridre.cells(mygridre.getRowId(j),5).getValue();
						recepcionDet[j][6]= mygridre.cells(mygridre.getRowId(j),6).getValue();
						recepcionDet[j][7]= mygridre.getRowId(j);
						
						pend_det = 	pend_det + parseFloat(mygridre.cells(mygridre.getRowId(j),6).getValue())	
				}
			}
			JsonAux={"recepcionDet":recepcionDet};
			$("recepcionDet").value=JsonAux.toJSONString();
			//alert(pend_gbl + ' - ' + pend_det);
			//return;
			if ((pend_gbl == 0) && (pend_det == 0)){
				$('total_parcial').value = 1;
			} else {
				$('total_parcial').value = 2;
			}
			
	}
	
	function cambiaDespachoGbl(Stage,rowId,index){
		//alert('Global RowId: '+rowId +' Stage: '+Stage +' Index: '+index);
		if((Stage == 0) && (index == 4) ){
			oldCantGbl = mygridregbl.cells(rowId,4).getValue();
			//alert('aqui ' + oldCantGbl);
		}	
	
	if((Stage==2) && (index == 4)){
		if(mygridregbl.cells(rowId,4).getValue()==''){
			alert("Debe introducir la cantidad despachada");
			mygridregbl.cells(rowId,4).setValue(oldCantGbl.toString());
			return false;
		} else {	
			var cantd = parseFloat(mygridregbl.cells(rowId,4).getValue());
			var cants = parseFloat(mygridregbl.cells(rowId,2).getValue());
			var cantr = parseFloat(mygridregbl.cells(rowId,3).getValue());
			if(!isNaN(cantd)){
				cantd = parseFloat(cantd);
				cantr = parseFloat(cantr);
			}else{
				cantd = 0;
				cantr = 0;
			}	
			var pendiente = cants-cantd;
			if	(cants<cantd){
				alert("La cantidad despachada no puede ser mayor a la requerida");
				mygridregbl.cells(rowId,4).setValue(oldCantGbl.toString());
				return false;
			} else if(cantd<cantr){
				alert('No puede registrar menos ingreso de lo que ya tiene');
				mygridregbl.cells(rowId,4).setValue(oldCantGbl.toString());
				return false;
			} else if(cantd<reqGbl[mygridregbl.cells(rowId,1).getValue()][1]) {
				alert('La suma del detalle para este producto es incorrecta, por favor verifique');
				mygridregbl.cells(rowId,4).setValue(oldCantGbl.toString());
				return false;
			} else {
					//var oldDespGbl = reqGbl[mygridregbl.cells(rowId,1).getValue()][0];
					//var oldDespDet = reqGbl[mygridregbl.cells(rowId,1).getValue()][1];
					mygridregbl.cells(rowId,4).setValue(cantd.toFixed(2));
					mygridregbl.cells(rowId,5).setValue(pendiente.toFixed(2));
					reqGbl[mygridregbl.cells(rowId,1).getValue()][0] = cantd.toFixed(2);
					
			}
		
		}		
	}
}

	function carga_vector_global(indice,cantidad){
		reqGbl[indice] = new Array;
		reqGbl[indice][0] = cantidad;
		reqGbl[indice][1] = 0;
	}
	
	function carga_vector_detalle(indice,cantidad){
		reqGbl[indice][1] += cantidad;
	}
	
	function carga_vector_reporte(id_ue,id_producto,cantidad,cantidad_anterior,id_requisicion,solicitada,indice){
		reqRpt[indice] = new Array;
		var despacho_anterior = cantidad - cantidad_anterior;
		reqRpt[indice][0] = id_ue;
		reqRpt[indice][1] = id_producto;
		reqRpt[indice][2] = despacho_anterior;
		reqRpt[indice][3] = id_requisicion;
		reqRpt[indice][4] = solicitada;
		reqRpt[indice][5] = cantidad;
		
	}
	
	function Imprime_reporte(){
		var json_aux = reqRpt.toJSONString();	
		 popup('reporte_entrega_mat_sum.pdf.php?vector=' + json_aux, 'Entrega de Materiales y Suministros', 'menubar=no, height=600, width=800, scrollbars=no, resizable=yes ');

	}

	

	
	
</script>
<div id="xxx"></div>
<?
$validator->create_message("error_nrofact", "nrofact", "*");
$validator->create_message("error_nrocontrol", "nrocontrol", "*");
//$validator->create_message("error_total_parcial", "tot_par[]", "*",2);
$validator->print_script();
require ("comun/footer.php");
?>