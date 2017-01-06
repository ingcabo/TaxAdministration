<? require ("comun/ini.php"); require ("comun/header.php"); ?>
<br />
<span class="titulo_maestro">Estimaci&oacute;n de Gastos RRHH por Unidad Ejecutora</span>
<div id="formulario">
	<table width="500" border="0">
		<tr>
			<td width="100" >Escenario:</td>
			<td width="200"><?=helpers::combo_ue_cp($conn, 
										'escenarios', 
										$objeto->id_escenario,
										'',
										'',
										'escenario',
										'escenario',
										'traeAnioEscenarioDesdeXML(this.value); traeUnidadesEjecutoras(this.value)',
										"SELECT id, descripcion FROM puser.escenarios WHERE id <> '$escEnEje'",
										$objeto->disabled,
										'',
										'')?></td>
		  <td width="100"> A&ntilde;o</td>
		  <td width="100"><span class="Estilo2"><input type="text"  name="anio" id="anio" value="<?=$objeto->anio?>"  size="4" readonly="readonly"/></span></td>
		</tr>
		<tr>
			<td width="100" >Unidad Ejecutora:</td>
			<td width="400"><div id="divcomboescenario"><select>
						<option>Seleccione...</option>
					</select><div></td>
		</tr>
		<tr>
			<td width="100">Cargo:</td>
			<td width="400"><div id="divcombotrabajador"><select name="Cargo" id="Cargo">
						<option>Seleccione...</option>
					</select><div></td>
		</tr><!--<SELECT name="Trabajador" id="Trabajador" onChange="CargarGrid()" ></SELECT> -->
		<tr>
			<td width="100">Porcentaje de Aumento:</td>
			<td width="400"><input type="text" name="aumento" id="aumento" style="width:40px" onkeypress="return(formatoNumero(this,event));" value="0,00" onblur="calculaAumento(this.value)" /></td>
		</tr><!--<SELECT name="Trabajador" id="Trabajador" onChange="CargarGrid()" ></SELECT> -->
		<tr>
			<td colspan="2" ><br /></td>
		</tr>
		<tr>
			<td colspan="2"><div id="gridbox" width="700" height="250" class="gridbox"></div></td>
		</tr>
		<tr>
			<td colspan="2" ><br /></td>
		</tr>
		<tr>
			<td align="left"></td>
			<td align="right"><input name="button"  type="button" onclick="Guardar()"  value="Guardar" /></td>
		</tr>
	</table>
</div>
<div style="height:40px;padding-top:10px;">
	<p id="Procesando" style="display:none;margin-top:0px;">
		<img alt="Cargando" src="images/loading.gif" /> Procesando...
	</p>
</div>
<script> 
var mygrid;
buildGrid();

function traeAnioEscenarioDesdeXML(id_escenario)
{
	var url = 'xmlTraeAnioEscenario.php'; 
	var pars = 'id_escenario=' + id_escenario;
	var myAjax = new Ajax.Request(
		url, 
		{
			method: 'post', 
			parameters: pars,
			onComplete: traeAnioEscenario
		});
}

function traeAnioEscenario(originalRequest)
{
	var xmlDoc = originalRequest.responseXML;
	var x = xmlDoc.getElementsByTagName('anio_escenario');
	for(j=0;j<x[0].childNodes.length;j++)
	{ 
		if (x[0].childNodes[j].nodeType != 1) continue;
		var nombre = x[0].childNodes[j].nodeName
		$(nombre).value = x[0].childNodes[j].firstChild.nodeValue;
	}
}

function traeUnidadesEjecutoras(id_escenario){
	var url = 'updater_selects.php';
	var pars = 'combo=id_unidades_gastos&escenario=' + id_escenario;
	var updater = new Ajax.Updater('divcomboescenario', 
	url,
	{
		method: 'post',
		parameters: pars,
		asynchronous:true, 
		evalScripts:true,
		onComplete:function(request){}
	}
	);
}

function traeTrabajadores(id_unidad){
	var url = 'updater_selects.php';
	var pars = 'combo=id_trabajadores&unidad=' + id_unidad;
	var updater = new Ajax.Updater('divcombotrabajador', 
	url,
	{
		method: 'post',
		parameters: pars,
		asynchronous:true, 
		evalScripts:true,
		onComplete:function(request){}
	}
	);
}

function traeCargosxUnidad(id_unidad){
	var url = 'updater_selects.php';
	var pars = 'combo=cargos_estimacion&unidad=' + id_unidad;
	var updater = new Ajax.Updater('divcombotrabajador', 
	url,
	{
		method: 'post',
		parameters: pars,
		asynchronous:true, 
		evalScripts:true,
		onComplete:function(request){}
	}
	);
}

function buildGrid(){
	//set grid parameters
	mygrid = new dhtmlXGridObject('gridbox');
	mygrid.selMultiRows = true;
	mygrid.setImagePath("js/grid/imgs/");
	mygrid.setHeader("Cod,Descripcion,Cant.,Sueldo Mensual,Sueldo Estimado,Valor Anual");
	mygrid.setInitWidths("25,280,35,105,105,150")
	mygrid.setColAlign("center,left,center,right,right,right")
	mygrid.setColTypes("ro,ro,ed,ro,ro,ro");
	mygrid.setColSorting("int,str,int,int,int,int")
	mygrid.setColumnColor("white,white,white,white,white,white")
	mygrid.rowsBufferOutSize = 0;
	mygrid.setMultiLine(false)
	mygrid.selmultirows="true"
	mygrid.delim=";";
	mygrid.init();
} 
function CargarGrid(){
var JsonAux;
	mygrid.clearSelection();
	mygrid.clearAll();
	JsonAux={"Cargo":parseInt($('Cargo').options[$('Cargo').selectedIndex].value),"Unidad": $('id_ue').options[$('id_ue').selectedIndex].value,"Forma":8,"Accion":0};
	//alert(JsonAux[0]);
	var url = 'OperarGrid.php';
	var pars = 'JsonEnv=' + JsonAux.toJSONString();
	var Request = new Ajax.Request(
		url,
		{
			method: 'post',
			parameters: pars,
	//		asynchronous:true, 
			onComplete:function(request){
				var JsonRec = eval( '(' + request.responseText + ')');
				if(JsonRec){
					for(var i=0;i<JsonRec.length;i++){
						mygrid.addRow(JsonRec[i]['cod'],JsonRec[i]['cod']+";"+JsonRec[i]['nomb']+";"+JsonRec[i]['cant']+";"+muestraFloat(JsonRec[i]['suel'])+";"+muestraFloat(JsonRec[i]['suelEst'])+";"+muestraFloat(JsonRec[i]['suelEst']*12*JsonRec[i]['cant']));
					}
				}
			}
		}
	); 
} 
function Guardar(){
var JsonAux,Cargos=new Array;
	mygrid.clearSelection();
	if(mygrid.getRowsNum() == 0){
		alert("Debe seleccionar al menos un cargo");
	}
	else{
		for(i=0;i<mygrid.getRowsNum();i++){
			Cargos[i] = new Array;
			Cargos[i][0]=mygrid.getRowId(i);
			Cargos[i][1]= usaFloat(mygrid.cells(mygrid.getRowId(i),4).getValue());
			Cargos[i][2]= mygrid.cells(mygrid.getRowId(i),2).getValue()
		}
		JsonAux={"Cargo":Cargos,"Unidad":$('id_ue').options[$('id_ue').selectedIndex].value,"Forma":8,"Accion":1};
		var url = 'OperarGrid.php';
		var pars = 'JsonEnv=' + JsonAux.toJSONString();
		var Request = new Ajax.Request(
			url,
			{
				method: 'post',
				parameters: pars,
		//		asynchronous:true, 
				onComplete:function(request){
					//alert(request.responseText);
					Calcular_Nomina_Paralela();
				}
			}
		);
	}
} 
	function Calcular_Nomina_Paralela(){
		Element.show('Procesando'); 
		JsonAux={"Cargo":parseInt($('Cargo').options[$('Cargo').selectedIndex].value),"Unidad": $('id_ue').options[$('id_ue').selectedIndex].value,"Accion":5};
		var url = 'CalcularNominaA.php';
		var pars = 'JsonEnv=' + JsonAux.toJSONString();
		var Request = new Ajax.Request(
			url,
			{
				method: 'post',
				parameters: pars,
				asynchronous:true, 
				onComplete:function(request){
					var JsonRec = eval( '(' + request.responseText + ')');
					//alert(JsonRec);
					if(JsonRec && JsonRec!="-1T" && JsonRec!="-1C"){
						for(var i=0;i<JsonRec.length;i++){
							try {
								JsonRec[i]['F']=eval(JsonRec[i]['F']);
								if(JsonRec[i]['F']=='Infinity'){ 
									JsonRec[i]['F']=0;
								}
							}catch(e) {
								JsonRec[i]['F']=0;
							}
						}
						Guardar_Nomina_Paralela(JsonRec);
					}
				}
			}
		); 
	}
	function Guardar_Nomina_Paralela(JsonRec){
		JsonAux={"Escenario":parseInt($('escenario').options[$('escenario').selectedIndex].value),"Unidad": $('id_ue').options[$('id_ue').selectedIndex].value,"Accion":6};
		var url = 'CalcularNominaA.php';
		var pars = 'JsonEnv=' + JsonAux.toJSONString() + '&JsonEnvI=' + JsonRec.toJSONString();
		var Request = new Ajax.Request(
			url,
			{
				method: 'post',
				parameters: pars,
		//		asynchronous:true, 
				onComplete:function(request){
					Element.hide('Procesando'); 
					alert(request.responseText)
				}
			}
		); 
	}
	var monto_old;
	
	
	function ValidarSueldo(stage,rowId,cellInd)
	{
		if(cellInd == '3')
		{
			if(stage == 0){
			 	monto_old = usaFloat(mygrid.cells(rowId,3).getValue());
			}
			if(stage == 2)
			{
				if((mygrid.cells(rowId,3).getValue()=='')){
					alert("Debe introducir un monto ");
					mygrid.cells(rowId,3).setValue(monto_old.toString());
					return false;
				} else {	
					var monto_new = usaFloat(mygrid.cells(rowId,3).getValue());
					if(isNaN(monto_new))
						monto_new = monto_old.toString();
					monto_new = muestraFloat(monto_new);
					mygrid.cells(rowId,3).setValue(monto_new);
					mygrid.cells(rowId,4).setValue(muestraFloat(usaFloat(mygrid.cells(rowId,3).getValue()) * 12,2));	
				}			
			}
		}
	}
	
	function calculaAumento(porcentaje){
	var porc = usaFloat(porcentaje);
		
		for(i=0;i<mygrid.getRowsNum();i++){
		if(!isNaN(mygrid.getRowId(i)))
			{
				rowId = mygrid.getRowId(i);
				//det.push(rowId)
				sueldoActual = usaFloat(mygrid.cells(rowId,3).getValue());
				sueldoAumento = sueldoActual + (sueldoActual * (porc/100));
				cant = parseFloat(mygrid.cells(rowId,2).getValue());
				mygrid.cells(rowId,4).setValue(muestraFloat(sueldoAumento.toFixed(2)));
				mygrid.cells(rowId,5).setValue(muestraFloat(sueldoAumento.toFixed(2) * cant * 12));
			}
		}	
	}
</script>
<? require ("comun/footer.php"); ?>