<? require ("comun/ini.php"); require ("comun/header.php"); ?>
<br />
<span class="titulo_maestro">Generar Listado</span>
<div id="formulario">
	<form action="<?=$PHP_SELF?>" name="form1" >
	<table width="600" border="0" >
		<tr >
			<td width="100" >Listados:</td>
			<td >
				<?=helpers::combonomina($conn, '','' ,'','int_cod','listado','int_cod','lis_titulo','listado','','SELECT int_cod,lis_titulo FROM rrhh.listado ','cargarGridBD(this.value)','false')?>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input  type="button"  value="Generar Listado" onClick="Imprimir()" >
			</td>
		</tr>
		<tr>
			<td colspan="2"><br /></td>
		</tr>
		<tr>
			<td colspan="2"  onClick="visibleListado('constructor');" style="cursor:pointer "  >--> Constructor de Listado</td>
		</tr>
		<tr  >
			<td colspan="2">
				<table id="constructor" style="padding:4px ">
					<tr>
						<td colspan="2"><br /></td>
					</tr>
					<tr >
						<td width="100" >Tabla:</td>
						<td >
							<SELECT name="tabla" id="tabla" onChange="cargarGrid(this.value)" >
								<option value="0">Seleccione...</option>
								<option value="trabajador">Trabajadores</option>
								<option value="concepto">Conceptos</option>
								<option value="variable">Variables</option>
								<option value="contrato">Contratos</option>
								<option value="division">Divisiones</option>
								<option value="departamento">Departamentos</option>
								<option value="cargo">Cargos</option>
								<option value="constante">Constantes</option>
								<option value="grupoconceptos">Grupos de Conceptos</option>
								<option value="Rac">Rac</option>
							</SELECT>
						</td>
					</tr>
					<tr>
						<td colspan="2"><br /></td>
					</tr>
					<tr>
						<td  >Titulo de Reporte:</td>
						<td ><input type="text" name="titulo" id="titulo" style="width:250px " /></td>
					</tr>
					<tr>
						<td  ><input type="checkbox" name="logo" id="logo">Colocar Logo</td>
						<td  ><input type="checkbox" name="fecha" id="fecha">Colocar Fecha &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type="checkbox" name="nroP" id="nroP">Colocar Nro de Paginas</td>
					</tr>
					<tr>
						<td  >Tipo de Hoja</td>
						<td  >
							<select name="tipo_hoja" id="tipo_hoja" style="width:150px">
								<option value="A3">A3</option>
								<option value="A4">A4</option>
								<option value="A5">A5</option>
								<option value="Letter" selected>Carta</option>
								<option value="Legal">Oficio</option>
							</select>
						</td>
					</tr>
					<tr>
						<td  >Orientacion de Hoja</td>
						<td  >
							<select name="orientacion_hoja" id="orientacion_hoja" style="width:150px">
								<option value="P">Vertical</option>
								<option value="L">Horizontal</option>
							</select>
						</td>
					</tr>
					<tr>
						<td  ><?=utf8_encode('Tamaño de letra') ?></td>
						<td  >
							<select name="tamano_letra" id="tamano_letra" style="width:150px">
								<? for($i=6;$i<=16;$i++){ ?>
									<option value=<?=$i?> <?=$i==10 ? 'selected' : ''?> ><?=$i?></option>
								<? } ?>
							</select>
						</td>
					</tr>
					<tr>
						<td colspan="2"><br /></td>
					</tr>
					<tr>
						<td colspan="2"  onClick="visibleListado('campos');" style="cursor:pointer "  >--> Campos</td>
					</tr>
					<tr>
						<td colspan="2" >
							<table id="campos">
								<tr >
									<td align="right" colspan="2">
										<input  type="button"  style="background-image:url(js/grid/imgs/btn_up1.gif); background-repeat:no-repeat ; background-position:center " onClick="moverfila(1,0)" style="width:50" >
										<input  type="button"  style="background-image:url(js/grid/imgs/btn_up2.gif); background-repeat:no-repeat ; background-position:center  " onClick="moverfila(1,1)" style="width:50" >
									</td>
								</tr>
								<tr>
									<td colspan="2"><div id="gridbox" width="510px" height="150" class="gridbox"></div></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="2" onClick="visibleListado('condiciones');" style="cursor:pointer "  >--> Condiciones</td>
					</tr>
					<tr>
						<td colspan="2" >
							<table id="condiciones">
								<tr>
									<td ><div id="gridbox2" width="500px" height="150" class="gridbox"></div></td>
								</tr>
								<tr>
									<td >Maestros</td>
								</tr>
								<tr>
									<td >
										<table>
											<tr>
												<td ><input  type="button"  value="Departamentos" onClick="mostrar_ventana('dep_cod')" style="width:125" ></td>
												<td ><input  type="button"  value="Cargos" onClick="mostrar_ventana('car_cod')" style="width:125" > </td>
												<td ><input  type="button"  value="Bancos" onClick="mostrar_ventana('ban_cod')" style="width:125" > </td>
												<td ><input  type="button"  value="Sexo" onClick="mostrar_ventana('tra_sex')" style="width:125" ></td>
											</tr>
											<tr>
												<td ><input  type="button"  value="Estatus de Trab" onClick="mostrar_ventana('tra_estatus')" style="width:125" ></td>
												<td ><input  type="button"  value="Nacionalidad" onClick="mostrar_ventana('tra_nac')" style="width:125" ></td>
												<td ><input  type="button"  value="Tipos de Pagos" onClick="mostrar_ventana('tra_tip_pag')" style="width:125" ></td>
												<td ><input  type="button"  value="Tipos de Cuentas" onClick="mostrar_ventana('tra_tipo_cta')" style="width:125" ></td>
											</tr>
											<tr>
												<td ><input  type="button"  value="Tipos de Conceptos" onClick="mostrar_ventana('conc_tipo')" style="width:125" ></td>
												<td ><input  type="button"  value="Estatus de Conceptos" onClick="mostrar_ventana('conc_estatus')" style="width:125" ></td>
												<td ><input  type="button"  value="Tipos de Variables" onClick="mostrar_ventana('var_tipo')" style="width:125" ></td>
												<td ><input  type="button"  value="Tipos de Contrato" onClick="mostrar_ventana('cont_tipo')" style="width:125" ></td>
											</tr>
											<tr>
												<td ><input  type="button"  value="Divisiones" onClick="mostrar_ventana('div_cod')" style="width:125" ></td>
												<td ><input  type="button"  value="Estatus de Depart" onClick="mostrar_ventana('dep_estatus')" style="width:125" ></td>
												<td ><input  type="button"  value="Unidades Ejecutoras" onClick="mostrar_ventana('uni_cod')" style="width:125" ></td>
												<td ><input  type="button"  value="Contratos" onClick="mostrar_ventana('cont_cod')" style="width:125" ></td>
											</tr>
										</table>
									 </td>			
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="2" onClick="visibleListado('orden');" style="cursor:pointer "  >-->Orden</td>
					</tr>
					<tr>
						<td colspan="2">
							<table id="orden">
								<tr >
									<td align="right">
										<input  type='button'  style='background-image:url(js/grid/imgs/btn_up1.gif); background-repeat:no-repeat ; background-position:center' onClick='moverfila(3,0)' style='width:50' >
										<input  type='button'  style='background-image:url(js/grid/imgs/btn_up2.gif); background-repeat:no-repeat ; background-position:center' onClick='moverfila(3,1)' style='width:50' >
									</td>
								</tr>
								<tr>
									<td ><div id="gridbox3" width="510px" height="150" class="gridbox" ></div></td>
								</tr>
							</table>
						</td>							
					</tr>
					<tr>
						<td colspan="2"><br /></td>
					</tr>
					<tr>
						<td >
							<input  type="button"  value="Guardar Listado" onClick="Guardar()" >
							<input type="button" onClick="copia_id()" id="copia_ids" style="display:none " />
							<input type="submit" id="Submit" name="Submit" style="display:none " />
							<input type="hidden" id="ids" />
						</td>
						<td >
							<input  type="button"  value="Eliminar Listado" onClick="Eliminar()" >
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	</form>
</div>

<div style="height:40px;padding-top:10px;">
	<p id="Procesando" style="display:none;margin-top:0px;">
		<img alt="Cargando" src="images/loading.gif" /> Cargando...
	</p>
</div>

<script language="javascript"  type="text/javascript"> 
buildGrid()
function buildGrid(){
	//set grid parameters
	mygrid = new dhtmlXGridObject('gridbox');
	mygrid.selMultiRows = true;
	mygrid.setImagePath("js/grid/imgs/");
	mygrid.setHeader("Imprimir,Descripcion,Campo,Longitud,Posicion");
	mygrid.setInitWidths("50,150,130,75,75")
	mygrid.setColAlign("center,left,center,center,center")
	mygrid.setColTypes("ch,ed,ro,ed,ed");
	mygrid.setColSorting("int,str,str,int,int")
	mygrid.setColumnColor("white,white,white,white,white")
	mygrid.rowsBufferOutSize = 0;
	mygrid.setMultiLine(false);
	mygrid.selmultirows="true";
	mygrid.delim=";";
	mygrid.init();

	mygrid2 = new dhtmlXGridObject('gridbox2');
	mygrid2.selMultiRows = true;
	mygrid2.setImagePath("js/grid/imgs/");
	mygrid2.setHeader("Aplicar,Campo,Operador,Valor");
	mygrid2.setInitWidths("50,200,100,130")
	mygrid2.setColAlign("center,left,center,center")
	mygrid2.setColTypes("ch,ro,coro,ed");
	mygrid2.setColSorting("int,str,str,str")
	mygrid2.setColumnColor("white,white,white,white")
	mygrid2.rowsBufferOutSize = 0;
	mygrid2.setMultiLine(false);
	mygrid2.selmultirows="true";
	mygrid2.delim=";";
	mygrid2.getCombo(2).put("=","IGUAL");
	mygrid2.getCombo(2).put(">","MAYOR");
	mygrid2.getCombo(2).put(">=","MAYOR IGUAL");
	mygrid2.getCombo(2).put("<","MENOR");
	mygrid2.getCombo(2).put("<=","MENOR IGUAL");
	mygrid2.getCombo(2).put("!=","DIFERENTE");
	mygrid2.getCombo(2).put("ILIKE","PARECIDO");
	mygrid2.getCombo(2).put("IN","DONDE");
	mygrid2.init();

	mygrid3 = new dhtmlXGridObject('gridbox3');
	mygrid3.selMultiRows = true;
	mygrid3.setImagePath("js/grid/imgs/");
	mygrid3.setHeader("Aplicar,Campo");
	mygrid3.setInitWidths("100,380")
	mygrid3.setColAlign("center,left")
	mygrid3.setColTypes("ch,ro");
	mygrid3.setColSorting("int,str")
	mygrid3.setColumnColor("white,white")
	mygrid3.rowsBufferOutSize = 0;
	mygrid3.setMultiLine(false);
	mygrid3.selmultirows="true";
	mygrid3.delim=";";
	mygrid3.init();

} 
var wxR;
function Imprimir(){
var fields=new Array,condiciones=new Array,orders=new Array,JsonAux;
	mygrid.clearSelection()
	mygrid2.clearSelection()
	for(i=0;i<mygrid.getRowsNum();i++){
		if(mygrid.cells(mygrid.getRowId(i),0).isChecked()){
			fields[i] = new Array;
			fields[i][0]=mygrid.getRowId(i);
			fields[i][1]= mygrid.cells(mygrid.getRowId(i),1).getValue();
			fields[i][2]= mygrid.cells(mygrid.getRowId(i),3).getValue();
			fields[i][3]= mygrid.cells(mygrid.getRowId(i),4).getValue();
			
		}
	}
	for(i=0;i<mygrid2.getRowsNum();i++){
		if(mygrid2.cells(mygrid2.getRowId(i),0).isChecked()){
			condiciones[i] = new Array;
			condiciones[i][0]=mygrid2.getRowId(i);
			condiciones[i][1]=mygrid2.cells(mygrid2.getRowId(i),2).getValue();
			condiciones[i][2]=mygrid2.cells(mygrid2.getRowId(i),3).getValue();
		}
	}
	for(i=0;i<mygrid3.getRowsNum();i++){
		if(mygrid3.cells(mygrid3.getRowId(i),0).isChecked()){
			orders[i]=mygrid3.getRowId(i);
		}
	}
	if($('tabla').options[$('tabla').selectedIndex].value==0 ){
		alert("Debe escojer una Tabla");
	}else if($('titulo').value=='' ){
		alert("Debe colocar un Titulo");
	}else{
		if (!wxR || wxR.closed) {
			if($('tabla').options[$('tabla').selectedIndex].value!='Rac'){
				wxR = window.open("listados.pdf.php?tabla="+$('tabla').options[$('tabla').selectedIndex].value+"&Titulo="+$('titulo').value+"&logo="+$('logo').checked+"&nroP="+$('nroP').checked+"&fecha="+$('fecha').checked+"&tipo_hoja="+$('tipo_hoja').options[$('tipo_hoja').selectedIndex].value+"&orientacion_hoja="+$('orientacion_hoja').options[$('orientacion_hoja').selectedIndex].value+"&tamano_letra="+$('tamano_letra').options[$('tamano_letra').selectedIndex].value+"&fields="+fields.toJSONString()+"&condiciones="+condiciones.toJSONString()+"&orders="+orders.toJSONString(),"winX","width=500,height=500,scrollbars=yes,resizable=yes,status=yes");
			}else{
				wxR = window.open("hrac.pdf.php?Titulo="+$('titulo').value+"&logo="+$('logo').checked+"&nroP="+$('nroP').checked+"&fecha="+$('fecha').checked+"&tipo_hoja="+$('tipo_hoja').options[$('tipo_hoja').selectedIndex].value+"&orientacion_hoja="+$('orientacion_hoja').options[$('orientacion_hoja').selectedIndex].value+"&tamano_letra="+$('tamano_letra').options[$('tamano_letra').selectedIndex].value+"&fields="+fields.toJSONString()+"&condiciones="+condiciones.toJSONString()+"&orders="+orders.toJSONString(),"winX","width=500,height=500,scrollbars=yes,resizable=yes,status=yes");
			}
			wxR.focus()
		} else { 
			wxR.focus()
		} 
	}
} 
function cargarGrid(option){
	mygrid.clearSelection();
	mygrid.clearAll();
	mygrid2.clearSelection();
	mygrid2.clearAll();
	mygrid3.clearSelection();
	mygrid3.clearAll();
	option = option=='Rac' ? 'trabajador' : option;
	JsonAux={"tabla":option,"Forma":12,"Accion":0};
	if(option!=0){
		var url = 'OtrosCalculos.php';
		var pars = 'JsonEnv=' + JsonAux.toJSONString();
		var Request = new Ajax.Request(
			url,
			{
				method: 'post',
				parameters: pars,
		//			asynchronous:false, 
				onComplete:function(request){
					if(request.responseText){
						var campos = eval('(' + request.responseText + ')');
						for(var i=0;i<campos.length;i++){
							mygrid.addRow(campos[i]['C'],'0;'+campos[i]['D']+';'+campos[i]['D']+';40;C');
							mygrid2.addRow(campos[i]['C2'],'0;'+campos[i]['D']+';=;');
							mygrid3.addRow(campos[i]['C2'],'0;'+campos[i]['D']);
						}
					}
				}
			}
		); 
	}else{
		alert('Debe escojer una tabla');
	}
}
function cargarGridBD(option){
	mygrid.clearSelection();
	mygrid.clearAll();
	mygrid2.clearSelection();
	mygrid2.clearAll();
	mygrid3.clearSelection();
	mygrid3.clearAll();
	JsonAux={"reporte":option,"Forma":12,"Accion":1};
	if(option!=-1){
		var url = 'OtrosCalculos.php';
		var pars = 'JsonEnv=' + JsonAux.toJSONString();
		var Request = new Ajax.Request(
			url,
			{
				method: 'post',
				parameters: pars,
		//			asynchronous:false, 
				onComplete:function(request){
					if(request.responseText){
						var resul = eval('(' + request.responseText + ')');
						$('tabla').value=resul[0];
						$('titulo').value=resul[1];
						$('logo').checked=resul[3]==1 ? true : false;
						$('nroP').checked=resul[4]==1 ? true : false;
						$('fecha').checked=resul[5]==1 ? true : false;
						$('orientacion_hoja').value=resul[6];
						$('tipo_hoja').value=resul[7];
						$('tamano_letra').value=resul[8];
						for(var i=0;i<resul[2].length;i++){
							mygrid.addRow(resul[2][i]['C'],resul[2][i]['Com1']+';'+resul[2][i]['Com2']+';'+resul[2][i]['D']+';'+resul[2][i]['Com3']+';'+resul[2][i]['Com4']);
							mygrid2.addRow(resul[2][i]['C2'],resul[2][i]['Con1']+';'+resul[2][i]['D']+';'+resul[2][i]['Con2']+';'+resul[2][i]['Con3']);
							mygrid3.addRow(resul[2][i]['C2'],resul[2][i]['O']+';'+resul[2][i]['D']);
						}  
					}
				}
			}
		); 
	}else{
		$('tabla').selectedIndex=0;
		$('titulo').value='';
		$('logo').checked= false;
		$('nroP').checked= false;
		$('fecha').checked=false;
		$('tipo_hoja').selectedIndex=3;
		$('orientacion_hoja').selectedIndex=0;
		$('tamano_letra').selectedIndex=3;
		alert('Debe escojer un reporte');
		
	}
}
function moverfila(tabla,opcion){
	switch(tabla){
		case 1:{
			if(mygrid.getSelectedId()){			
				switch(opcion){
					case 0:
						mygrid.moveRowUp(mygrid.getSelectedId())
						break;
					case 1:
						mygrid.moveRowDown(mygrid.getSelectedId())
						break;
				}
			}else{
				alert('Debe Seleccionar una fila del grid');
			}
			break;
		}
		case 3:{
			if(mygrid3.getSelectedId()){			
				switch(opcion){
					case 0:
						mygrid3.moveRowUp(mygrid3.getSelectedId())
						break;
					case 1:
						mygrid3.moveRowDown(mygrid3.getSelectedId())
						break;
				}
			}else{
				alert('Debe Seleccionar una fila del grid');
			}
			break;
		}
	}
}
var wx
function mostrar_ventana(campo){
	if(mygrid2.getSelectedId()){ 
		if (!wx || wx.closed) { 
			wx = window.open("listadoV.php?campo="+campo,"winX","width=500,height=500,scrollbars=yes,resizable=yes,status=yes");
			wx.focus()
		} else { 
			wx.focus()
		} 
	}else{
		alert('Debe selecionar el campo destino en el grid');
	}
}
function copia_id(){
	if($('ids').value!=-1 && mygrid2.getSelectedId()){
		mygrid2.cells(mygrid2.getSelectedId(),3).setValue($('ids').value);
	}
}
function Guardar(){
var fields=new Array,condiciones=new Array,orders=new Array,JsonAux;
	mygrid.clearSelection()
	mygrid2.clearSelection()
	for(i=0;i<mygrid.getRowsNum();i++){
		if(mygrid.cells(mygrid.getRowId(i),0).isChecked()){
			fields[i] = new Array;
			fields[i][0]=mygrid.getRowId(i);
			fields[i][1]= mygrid.cells(mygrid.getRowId(i),1).getValue();
			fields[i][2]= mygrid.cells(mygrid.getRowId(i),3).getValue();
			fields[i][3]= mygrid.cells(mygrid.getRowId(i),4).getValue();
			
		}
	}
	for(i=0;i<mygrid2.getRowsNum();i++){
		if(mygrid2.cells(mygrid2.getRowId(i),0).isChecked()){
			condiciones[i] = new Array;
			condiciones[i][0]=mygrid2.getRowId(i);
			condiciones[i][1]=mygrid2.cells(mygrid2.getRowId(i),2).getValue();
			condiciones[i][2]=mygrid2.cells(mygrid2.getRowId(i),3).getValue();
		}
	}
	for(i=0;i<mygrid3.getRowsNum();i++){
		if(mygrid3.cells(mygrid3.getRowId(i),0).isChecked()){
			orders[i]=mygrid3.getRowId(i);
		}
	}
	var url = 'OtrosCalculos.php';
	JsonAux={"tabla":$('tabla').options[$('tabla').selectedIndex].value,"titulo":$('titulo').value,"logo":$('logo').checked,"nroP":$('nroP').checked,"fecha":$('fecha').checked,"tipo_hoja":$('tipo_hoja').options[$('tipo_hoja').selectedIndex].value,"orientacion_hoja":$('orientacion_hoja').options[$('orientacion_hoja').selectedIndex].value,"tamano_letra":$('tamano_letra').options[$('tamano_letra').selectedIndex].value,"campos":fields,"condiciones":condiciones,"orden":orders,"Forma":12,"Accion":2};
	var pars = 'JsonEnv=' + JsonAux.toJSONString();
	var Request = new Ajax.Request(
		url,
		{
			method: 'post',
			parameters: pars,
	//			asynchronous:false, 
			onComplete:function(request){
				if(request.responseText){
					alert('Operacion Realizada con exito');
					window.document.form1.submit();
				}else{
					alert('Ha ocurrido una falla durante la operacion');
				}
			}
		}
	); 
} 
function Eliminar(){
	var url = 'OtrosCalculos.php';
	JsonAux={"reporte":$('listado').options[$('listado').selectedIndex].value,"Forma":12,"Accion":3};
	var pars = 'JsonEnv=' + JsonAux.toJSONString();
	var Request = new Ajax.Request(
		url,
		{
			method: 'post',
			parameters: pars,
	//			asynchronous:false, 
			onComplete:function(request){
				if(request.responseText){
					alert('Operacion Realizada con exito');
					window.document.form1.submit();
				}else{
					alert('Ha ocurrido una falla durante la operacion');
				}
			}
		}
	); 
} 
visibleListado('constructor');
visibleListado('campos');
visibleListado('condiciones');
visibleListado('orden');
function visibleListado(objeto){
	$(objeto).style.display= $(objeto).style.display=='none' ? 'inline' : 'none';
}
</script>
<? require ("comun/footer.php"); ?>