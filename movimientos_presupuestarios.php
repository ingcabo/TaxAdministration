<?
require ("comun/ini.php");
// Creando el objeto movimientosPresupuestarios
$oMovimientosPresupuestarios = new movimientos_presupuestarios;
$accion = $_REQUEST['accion'];
if($accion == 'Guardar'){
	if($oMovimientosPresupuestarios->add($conn, 
									$usuario->id,
									$_REQUEST['unidad_ejecutora'],
									$_REQUEST['ano'],
									$_REQUEST['descripcion'],
									$_REQUEST['nrodoc'],
									$_REQUEST['tipdoc'],
									$_REQUEST['tipref'],
									$_REQUEST['nroref'],
									$_REQUEST['fechadoc'],
									$_REQUEST['fecharef'],
									$_REQUEST['momentos_presupuestarios'],
									$_REQUEST['proveedores'],
									$_REQUEST['contenedor_partidas'])){
		$msj = REG_ADD_OK;
		// Aca actualizo la tabla relacion_pp_cp (parcat) con los nuevos montos y la nueva cantidad disponible
		$JsonRec = new Services_JSON();
		$JsonRec = $JsonRec->decode(str_replace("\\","",$_REQUEST['contenedor_partidas']));
		
		foreach($JsonRec->partidaspresupuestarias as $partidas){
		
			$aIdParCat[] = $partidas[3];
			$aCategoriaProgramatica[] = $partidas[0];
			$aPartidaPresupuestaria[] = $partidas[1];
			$aMonto[] = $partidas[2];
		}
		
		switch($_REQUEST['momentos_presupuestarios']){
			case 1:
				relacion_pp_cp::set_desde_movpre($conn, 
																		$aIdParCat, 
																		$aMonto,
																		'',
																		'',
																		'',
																		'',
																		$aMonto);
				break;
			case 2:
				relacion_pp_cp::set_desde_movpre($conn, 
																		$aIdParCat, 
																		'',
																		$aMonto,
																		'',
																		'',
																		'',
																		'');
				break;
			case 3:
				relacion_pp_cp::set_desde_movpre($conn, 
																		$aIdParCat, 
																		'',
																		'',
																		$aMonto,
																		'',
																		'',
																		'');
				break;
			case 4:
				relacion_pp_cp::set_desde_movpre($conn, 
																		$aIdParCat, 
																		'',
																		'',
																		'',
																		$aMonto,
																		'',
																		$aMonto);
				break;
			case 5:
				relacion_pp_cp::set_desde_movpre($conn, 
																		$aIdParCat, 
																		'',
																		'',
																		'',
																		'',
																		$aMonto,
																		$aMonto);
				break;
		}
	}else
		$msj = ERROR;
}elseif($accion == 'del'){
	if($oMovimientosPresupuestarios->del($conn, $_REQUEST['id']))
		$msj = REG_DEL_OK;
	else
		$msj = ERROR;
}

require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<br />
<script type="text/javascript">var mygridpp,ipp=0</script>
<span class="titulo_maestro">Maestro de Movimientos Presupuestarios</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />
<div id="contenidomovpre">
<fieldset id="buscador">
	<legend>Buscar:</legend>
<table>
<tr>
	<td>N&ordm; de Documento:</td>
	<td>
		<input type="text" style="width:90px" name="consulta_nrodoc" id="consulta_nrodoc" />
	</td>
	<td>Descripci&oacute;n:</td>
	<td>
		<input type="text" style="width:200px" name="consulta_descripcion" id="consulta_descripcion" />
	</td>
</tr>
<tr>
	<td>Desde:</td>
	<td>
	<table>
		<tr>
			<td>
			<input size="12" id="consulta_fecha_desde" onchange="validafecha(this);" type="text"  />
			</td>
			<td>
				<a href="#" id="boton_consulta_fecha_desde" onclick="return false;">
					<img border="0" src="images/calendarA.png" alt="Seleccione una Fecha" width="20" height="20" />
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
					inputField        : "consulta_fecha_desde",
					button            : "boton_consulta_fecha_desde",
					ifFormat          : "%d/%m/%Y",
					daFormat          : "%Y/%m/%d",
					align             : "Br"
				 });
			</script>
			</td>
		</tr>
		</table>
	</td>
	<td>Hasta:</td>
	<td>
	<table>
		<tr>
			<td>
			<input size="12" id="consulta_fecha_hasta" onchange="validafecha(this);" type="text"  />
			</td>
			<td>
				<a href="#" id="boton_consulta_fecha_hasta" onclick="return false;">
					<img border="0" src="images/calendarA.png" alt="Seleccione una Fecha" width="20" height="20" />
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
					inputField        : "consulta_fecha_hasta",
					button            : "boton_consulta_fecha_hasta",
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
<tr>
	<td>Tipo de Documento:</td>
	<td colspan="3">
			<?=helpers::combo_ue_cp($conn, 
														'momentos_presupuestarios', 
														'',
														'',
														'id',
														'consulta_momentos_presupuestarios',
														'consulta_momentos_presupuestarios',
														"updater_consulta(
														\$F('consulta_fecha_desde'), 
														\$F('consulta_fecha_hasta'), 
														\$F('consulta_tipos_documentos'), 
														\$F('consulta_momentos_presupuestarios'),
														\$F('consulta_nrodoc'),
														\$F('consulta_descripcion'),
														1
													);")?>
	</td>
</tr>
<tr>
	<td>Tipo de Movimiento:</td>
	<td colspan="3">
			<?=helpers::combo_ue_cp($conn, 
														'tipos_documentos', 
														'',
														'',
														'id',
														'consulta_tipos_documentos',
														'consulta_tipos_documentos',
														"updater_consulta(
														\$F('consulta_fecha_desde'), 
														\$F('consulta_fecha_hasta'), 
														\$F('consulta_tipos_documentos'), 
														\$F('consulta_momentos_presupuestarios'),
														\$F('consulta_nrodoc'),
														\$F('consulta_descripcion'),
														1
													);")?>
	</td>
</tr>
</table>
</fieldset>
</div>
<br />
<div id="consulta"> </div>
<br />
<div style="height:40px;padding-top:10px;">
<p id="cargando" style="display:none;margin-top:0px;">
  <img alt="Cargando" src="images/loading.gif" /> Cargando...
</p>
</div>
<script src="js/movpre.js" type="text/javascript"></script>
<div id="contenedor_nodos_tmp"></div>
<div id="divNrodoc" style="display:none">0-0</div>
<script type="text/javascript">
function validafecha(fecha){
	var upper = 31;
	if(/^(\d{2})\/(\d{2})\/(\d{4})$/.test(fecha.value)) { // dd/mm/yyyy
		if(RegExp.$2 == '02') upper = 29;
		if((RegExp.$1 <= upper) && (RegExp.$2 <= 12)) {
			updater_consulta(
				$F('consulta_fecha_desde'), 
				$F('consulta_fecha_hasta'), 
				$F('consulta_tipos_documentos'), 
				$F('consulta_momentos_presupuestarios'),
				$F('consulta_nrodoc'),
				$F('consulta_descripcion'),
				1
			);
		} else {
			alert("Fecha incorrecta");
			fecha.value = "";
		}
	}else if(fecha.value != '') {
		alert("Fecha incorrecta");
		fecha.value = "";
	}
}
Event.observe('consulta_nrodoc', 'keyup', function () { 
	updater_consulta(
				$F('consulta_fecha_desde'), 
				$F('consulta_fecha_hasta'), 
				$F('consulta_tipos_documentos'), 
				$F('consulta_momentos_presupuestarios'),
				$F('consulta_nrodoc'),
				$F('consulta_descripcion'),
				1
	);
});
Event.observe('consulta_descripcion', 'keyup', function () { 
	updater_consulta(
				$F('consulta_fecha_desde'), 
				$F('consulta_fecha_hasta'), 
				$F('consulta_tipos_documentos'), 
				$F('consulta_momentos_presupuestarios'),
				$F('consulta_nrodoc'),
				$F('consulta_descripcion'),
				1
	);
});

var mygrid;

/* COMPROMISO */
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
	}else if($('montoip').value==""){
		alert("Primero debe colocar el monto de la Partida Presupuestaria.");
		return;
	}else if((usaFloat($('montoip').value) > usaFloat($('disponible').value)) && ($('momentos_presupuestarios').value != 4 && $('momentos_presupuestarios').value != 5) ){
		alert("El Monto sobrepasa el monto disponible para esta partida.");
		$('montoip').value = 0;
		return;
	}else{
		for(j=0; j < mygridpp.getRowsNum(); j++){
			if (mygridpp.cells(mygridpp.getRowId(j),'0').getValue() == $('categorias_programaticas').value 
             && mygridpp.cells(mygridpp.getRowId(j),'1').getValue() == $('partidas_presupuestarias').value){
					alert('Esta partida ya ha sido seleccionada, por favor seleccione otra partida');
					return false;
			}
		}
		mygridpp.addRow($('idParCat').value,$('categorias_programaticas').value+";"+$('partidas_presupuestarias').value+";"+$('montoip').value);
		sumaTotalPartidas();
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
				
				Dialog.alert(request.responseText, {windowParameters: {width:600, height:400, 
								showEffect:Element.show,hideEffect:Element.hide,
								showEffectOptions: { duration: 1}, hideEffectOptions: { duration:1 }
								
								}});
				
				}
			}
	);     	   
}

//FUNCION QUE LLENA EL COMBO DE PARTIDAS PRESUPUESTARIAS AL SELECCIONAR UNA CATEGORIA PROGRAMATICA//
function traePartidasPresupuestarias(cp){
	
	var url = 'updater_selects.php';
	var pars = 'combo=partidas_presupuestarias&cp=' + cp +'&ms='+new Date().getTime();
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
} 

//FUNCION QUE SUMA EL TOTAL DE LA PARTIDAS PRESUPUESTARIAS//
function sumaTotalPartidas(){
	var totalPartidas = 0;
	for(j=0; j <mygridpp.getRowsNum(); j++){
		totalPartidas += usaFloat(mygridpp.cells(mygridpp.getRowId(j),2).getValue());
	}
	if ($('momentos_presupuestarios').value=='1'){
		$('ppCompromiso').value = (isNaN(totalPartidas))? '0' : muestraFloat(totalPartidas,'2');
	}else if ($('momentos_presupuestarios').value=='2'){
		$('ppCausado').value = (isNaN(totalPartidas))? '0' : muestraFloat(totalPartidas,'2');
	}else{
		$('total_grid').value = (isNaN(totalPartidas))? '0' : totalPartidas;	
	}
}


//FUNCION QUE TRAE LA DISPONIBILIDAD DE LAS PARTIDAS PRESUPUESTARIAS//
function traerDisponiblePartidas(cp, pp){
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
				$('disponible').value 			= muestraFloat(jsonData.disponible);
				$('presupuesto_original').value = muestraFloat(jsonData.presupuesto_original);
				$('compromisos').value 			= muestraFloat(jsonData.compromisos);
				$('causados').value 			= muestraFloat(jsonData.causados);
				$('pagados').value 				= muestraFloat(jsonData.pagados);
				$('aumentos').value 			= muestraFloat(jsonData.aumentos);
				$('disminuciones').value		= muestraFloat(jsonData.disminuciones);
				$('idParCat').value 			= jsonData.id;
			}
		}
	);
}


//FUNCION QUE TRAE LA DISPONIBILIDAD DE LAS PARTIDAS PRESUPUESTARIAS CUANDO SE SELECCIONA EN EL GRID//
function traerDisponiblePartidasSeleccionada(rowId){

	var cp = mygridpp.cells(rowId,0).getValue();
	var pp = mygridpp.cells(rowId,1).getValue();
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
				$('disponible').value 			= muestraFloat(jsonData.disponible);
				$('presupuesto_original').value = muestraFloat(jsonData.presupuesto_original);
				$('compromisos').value 			= muestraFloat(jsonData.compromisos);
				$('causados').value 			= muestraFloat(jsonData.causados);
				$('pagados').value 				= muestraFloat(jsonData.pagados);
				$('aumentos').value 			= muestraFloat(jsonData.aumentos);
				$('disminuciones').value		= muestraFloat(jsonData.disminuciones);
				$('nombre_categoria').value		= jsonData.nom_cat;
				$('nombre_partidas').value		= jsonData.nom_par;
			}
		}
	);
}

//FUNCION QUE PERMITE GUARDAR LAS PARTIDAS PRESUPUESTARIAS//
function GuardarPP(){
	
	var JsonAux;
	var PPAux = [];
	mygridpp.clearSelection();
	
	for(j=0; j < mygridpp.getRowsNum(); j++){
		if(!isNaN(mygridpp.getRowId(j))){
			PPAux[j] = [];
			PPAux[j][0]= mygridpp.cells(mygridpp.getRowId(j),0).getValue();
			PPAux[j][1]= mygridpp.cells(mygridpp.getRowId(j),1).getValue();
			PPAux[j][2]= mygridpp.cells(mygridpp.getRowId(j),2).getValue();
			PPAux[j][3]= mygridpp.getRowId(j);
		}
	}
	
	JsonAux={"partidaspresupuestarias":PPAux};
	$("contenedor_partidas").value=JsonAux.toJSONString();
}

//FUNCION QUE CARGA LAS PARTIDAS PRESUPUESTARIAS EN EL CASO DE SELECCIONAR UN NUMERO DE REFERENCIA//
function CargarGridPP(id){
	
	mygridpp.clearSelection();
	mygridpp.clearAll();
	var url = 'json.php';
	var pars = 'op=pp_solicitud&id='+ id;
	var Request = new Ajax.Request(
		url,
		{
			method: 'get',
			parameters: pars,
			onLoading:function(request){}, 
			onComplete:function(request){
				
				var JsonData = eval( '(' + request.responseText + ')');
				var causado = 0;
				var comprometido = 0;
				var IdParCat = new Array;
				if(JsonData){
					for(var j=0;j<JsonData.length;j++){
						
						IdParCat[j] = new Array;
						mygridpp.addRow(JsonData[j]['idParCat'],JsonData[j]['id_categoria_programatica']+";"+JsonData[j]['id_partida_presupuestaria']+";0,00"+";"+muestraFloat(JsonData[j]['montoporcausar']));
											
						//ACUMULO EL CAUSADO Y EL COMPROMETIDO//	
						causado += parseFloat(JsonData[j]['causado']);
						comprometido += parseFloat(JsonData[j]['comprometido']);
						ipp++;
					}
				
				var disponible = comprometido - causado;
				$('ppCompromiso').value = muestraFloat(comprometido);
				$('ppCausado').value = muestraFloat(causado);
				
								
				}
			}
		}
	);  
}

//ESTA FUNCION LLENA EL GRID CUANDO ESTAS PAGANDO UN DOCUMENTO// 
function CargarGridPPPagado(id){
	
	mygridpp.clearSelection();
	mygridpp.clearAll();
	var url = 'json.php';
	var pars = 'op=pp_solicitud&id='+ id + '&status=2';
	var Request = new Ajax.Request(
		url,
		{
			method: 'get',
			parameters: pars,
			onLoading:function(request){}, 
			onComplete:function(request){
				
				var JsonData = eval( '(' + request.responseText + ')');
				var causado = 0;
				var comprometido = 0;
				var IdParCat = new Array;
				var pagado =0;
				if(JsonData){
					for(var j=0;j<JsonData.length;j++){
						
						IdParCat[j] = new Array;
						mygridpp.addRow(JsonData[j]['idParCat'],JsonData[j]['id_categoria_programatica']+";"+JsonData[j]['id_partida_presupuestaria']+";"+muestraFloat(JsonData[j]['montoporcausar']));
											
						//ACUMULO EL CAUSADO Y EL COMPROMETIDO//	
						causado = parseFloat(JsonData[j]['causados']);
						comprometido = parseFloat(JsonData[j]['compromiso']);
						pagado += parseFloat(JsonData[j]['monto']);
						ipp++;
					}
				
				var disponible = comprometido - causado;
				$('ppCompromiso').value = muestraFloat(comprometido);
				$('ppCausado').value = muestraFloat(causado);
				$('ppPagado').value = muestraFloat(causado);
				
								
				}
			}
		}
	);  
}

//ESTA FUNCION SE CARGA CUANDO SELECCION EN EL COMBO TIPO DE MOVIMIENTO LA OPCION COMPROMISO Y CARGA UN GRID PARA EL MISMO//
function buildGridPPCO(){
	//set grid parameters
	mygridpp.destructor();	
	mygridpp = new dhtmlXGridObject('gridboxpp');
	mygridpp.selMultiRows = true;
	mygridpp.setImagePath("js/grid/imgs/");
	mygridpp.setHeader("Categoria,Partida Presupuestaria,Monto");
	mygridpp.setInitWidths("260,260,205");
	mygridpp.setColAlign("center,center,center");
	mygridpp.setColTypes("ed,ed,ed");
	mygridpp.setColSorting("str,str,int");
	mygridpp.setColumnColor("white,white,white");
	mygridpp.rowsBufferOutSize = 0;
	mygridpp.setMultiLine(false);
	mygridpp.selmultirows="true";
	mygridpp.setOnRowSelectHandler(traerDisponiblePartidasSeleccionada);
	mygridpp.delim=';';
	
	
	//mygridpp.setOnEditCellHandler(traePartidasPresupuestarias);
	mygridpp.setOnEnterPressedHandler(sumaTotalPartidas);
	//start grid
	mygridpp.init();
}


//ESTE GRID SE CARGA CUANDO SELECCIONAMOS LA OPCION CAUSADO//
function buildGridPPC(){
	
	mygridpp.destructor()
	//set grid parameters
	mygridpp = new dhtmlXGridObject('gridboxpp');
	mygridpp.selMultiRows = true;
	mygridpp.setImagePath("js/grid/imgs/");
	mygridpp.setHeader("Categoria,Partida Presupuestaria,Monto, Monto Por Causar");
	mygridpp.setInitWidths("180,180,185,180");
	mygridpp.setColAlign("center,center,center,center");
	mygridpp.setColTypes("ed,ed,ed,ro");
	mygridpp.setColSorting("str,str,int,int");
	mygridpp.setColumnColor("white,white,white,white");
	mygridpp.rowsBufferOutSize = 0;
	mygridpp.setMultiLine(false);
	mygridpp.selmultirows="true";
	mygridpp.setOnRowSelectHandler(traerDisponiblePartidasSeleccionada);
	mygridpp.setOnEditCellHandler(validarMontoPP);
	mygridpp.delim=';';
	mygridpp.setOnEnterPressedHandler(sumaTotalPartidas);
	mygridpp.init();
}

//ESTA FUNCION ES PARA VALIDAR EL MONTO COLOCADO CUANDO SE ESTA CAUSANDO UN DOCUMENTO//
function validarMontoPP(stage,rowId,cellInd){
	
	//EN ESTE ESTADO CONVIERTO EL MONTO DE FORMATO VENEZOLANO AL FORMATO IMPERIALISTA//
	if (stage==0){
	
		if (cellInd==2){
			
			var valor = usaFloat(mygridpp.cells(rowId,2).getValue());
			mygridpp.cells(rowId,'2').setValue(valor);	
			
		}
			
	}
	
	//EN ESTE ESTADO VERIFICO SI EL MONTO SE SOBREPASA, VALIDO QUE CUANDO ESTE VACIO COLOQUE 0,00, SUMO EL TOTAL DE LAS PARTIDAS SI SE COLOCO UN VALOR//
	if (stage==2){
	
		if (cellInd==2){
		
			if (parseFloat(mygridpp.cells(rowId,2).getValue()) > parseFloat(usaFloat(mygridpp.cells(rowId,3).getValue()))){
			
				alert("El Monto Sobrepasa el Limite de del monto permitido para causar");
				mygridpp.cells(rowId,'2').setValue('0,00');
				
				return false;
			
			}else if(mygridpp.cells(rowId,2).getValue()==''){
			
				mygridpp.cells(rowId,'2').setValue('0,00');
				return;
			
			}else{
				var valor = muestraFloat(mygridpp.cells(rowId,2).getValue());
				mygridpp.cells(rowId,'2').setValue(valor);
				sumaTotalPartidas();
			
			}
		}
	}
}

function mostrarBuscarCat(){
	mygridpp.clearAll();
	$('bcategorias').style.display = 'inline';
	$('txtcategorias_programaticas').value = '';
	$('categorias_programaticas').value = '';
	$('txtpartidas_presupuestarias').value = '';
	$('partidas_presupuestarias').value = '';
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

function traePartidasPresupuestarias(cp){
	var url = 'buscar_partidas.php';
	var pars = 'cp=' + cp +'&idp=&ms='+new Date().getTime();
		
	var Request = new Ajax.Request(
		url,
		{
			method: 'get',
			parameters: pars,
			onLoading:function(request){}, 
			onComplete:function(request){
				Dialog.alert(request.responseText, {
						  											windowParameters: { 
																		width:600, 
																		height:400, 
																		showEffect:Element.show,
																		hideEffect:Element.hide,
																		showEffectOptions: { duration: 1}, 
																		hideEffectOptions: { duration:1 }
																	}
																}
								);
				
			}
		}
	);     	   
}
</script>
<? require ("comun/footer.php"); ?>
