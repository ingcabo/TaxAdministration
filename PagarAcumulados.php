<? require ("comun/ini.php"); require ("comun/header.php"); ?>
<style>
    div.autocomplete {
      position:absolute;
      width:250px;
      background-color:white;
      border:1px solid #888;
      margin:0px;
      padding:0px;
    }
    div.autocomplete ul {
      list-style-type:none;
      margin:0px;
      padding:0px;
    }
    div.autocomplete ul li.selected { background-color: #ffb;}
    div.autocomplete ul li {
      list-style-type:none;
      display:block;
      margin:0;
      padding:2px;
      height:16px;
      cursor:pointer;
    }
</style>
<br />
<span class="titulo_maestro">Pagos-Anticipos-Liquidaciones</span>
<div id="formulario">
	<table width="500" border="0">
		<tr>
			<td colspan="1" width="100" >Trabajador:</td>
			<td colspan="1" width="200"><?=helpers::combonominaIII($conn, '', '','','','Trabajador','int_cod','tra_nom','tra_ape','Trabajador','','SELECT A.int_cod,A.tra_nom,A.tra_ape FROM (rrhh.trabajador AS A INNER JOIN rrhh.departamento AS B ON A.dep_cod=B.int_cod) INNER JOIN rrhh.division AS C ON B.div_cod=C.int_cod WHERE C.emp_cod='.$_SESSION['EmpresaL'].' AND A.tra_estatus<>4  ORDER BY int_cod','CargarGrid()','true');?></td>
			<td colspan="2" align="right" ><input id="Tipo0" name="Tipo" type="radio" onClick="Liquidar(this.value)" value="0" checked>&nbsp;Pago&nbsp;&nbsp;<input  id="Tipo1" name="Tipo" type="radio" onClick="Liquidar(this.value)" value="1" >&nbsp;Anticipio&nbsp;&nbsp;<input id="Tipo2" name="Tipo" type="radio" onClick="Liquidar(this.value)" value="2">&nbsp;Liquidacion</td>
		</tr>
		<tr>
			<td colspan="1" width="100" >Por Concepto de:</td>
			<td colspan="3" width="400"><textarea id="Descripcion" name="Descripcion" cols="50" rows="1"></textarea></td>
		</tr>
		<tr>
			<td colspan="4" ><br />Conceptos Acumulados:</td>
		</tr>
		<tr>
			<td colspan="4"><div id="gridbox" width="600" height="150" class="gridbox"></div><br /></td>
		</tr>
		<tr>
			<td >Total Acumulado:</td>
			<td ><input readonly name="Acumulado" id="Acumulado" value="0,00"></td>
			<td >Total a Pagar:</td>
			<td ><input readonly name="Monto" id="Monto" value="0,00"></td>
		</tr>
		<tr>
			<td >Resta:</td>
			<td colspan="3" ><input readonly name="Resto" id="Resto" value="0,00" ></td>
		</tr>
		<tr>
			<td colspan="1" width="100" >Observaciones:</td>
			<td colspan="3" width="400"><textarea id="Obser" name="Obser" cols="50" rows="1"></textarea></td>
		</tr>
		<tr>
			<td colspan="4" ><br /><div id="label" onClick="Visible()" style="cursor:pointer;width:150">Agregar Conceptos Adicionales:</div></td>
		</tr>
		<tr>
			<td colspan="4">
				<table border="0" id="ConceptosAdicionales" width="600">
					<tr>
						<td  >
							<input type="text" id="ConceptoCodigo" name="ConceptoCodigo" readonly="true" style="width:50">
							<input type="text" id="ConceptoNombre" name="ConceptoNombre"  onKeyUp="traeCodigo(this.value)" onMouseOut="traeCodigo(this.value)" style="width:250">
							<div id="autocomplete_choices" class="autocomplete"></div>
							<input type="button" value="Agregar" onClick="Agregar()">
						</td>
						<td align="right"  ><input type="button" value="Eliminar" onClick="Eliminar()"></td>
					</tr>
					<tr>
						<td colspan="2" ><div id="gridbox2" width="600" height="150" class="gridbox2"></div><br /></td>
					</tr>
					<tr>
						<td align="right" >Total Concepto:</td>
						<td ><input readonly name="TotalConcepto" id="TotalConcepto" value="0,00"></td>
					</tr>
					<tr>
						<td align="right" >Total a Pagar:</td>
						<td ><input readonly name="Total" id="Total" value="0,00"></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="4" align="right" ><br><input type="button" value="Realizar Operacion" onClick="Guardar()"></td>
		</tr>
	</table>
</div>
<br>
<div id="Tabla"></div>
<script>
new Ajax.Autocompleter("ConceptoNombre", "autocomplete_choices", "AutoCompleteConceptos.php", {});
var mygrid;
var Indice=1;
buildGrid();
Tabla();
function buildGrid(){
	//set grid parameters
	mygrid = new dhtmlXGridObject('gridbox');
	mygrid.selMultiRows = true;
	mygrid.setImagePath("js/grid/imgs/");
	mygrid.setHeader("Seleccionar,Concepto,Acumulado,Porcentaje, Monto");
	mygrid.setInitWidths("70,230,100,100,100")
	mygrid.setColAlign("center,center,center,center,center")
	mygrid.setColTypes("ch,ro,ro,ed,ed");
	mygrid.setColSorting("int,str,int,int,int")
	mygrid.setColumnColor("white,white,white,white,white")
	mygrid.rowsBufferOutSize = 0;
	mygrid.setMultiLine(false);
	mygrid.selmultirows="true";
	mygrid.delim=";";
	mygrid.setOnEditCellHandler(CalcularMontoGrid);
	mygrid.setOnCheckHandler(Check);
	mygrid.init();

	mygrid2 = new dhtmlXGridObject('gridbox2');
	mygrid2.selMultiRows = true;
	mygrid2.setImagePath("js/grid/imgs/");
	mygrid2.setHeader("Concepto,Monto");
	mygrid2.setInitWidths("400,200")
	mygrid2.setColAlign("center,center")
	mygrid2.setColTypes("ro,ro");
	mygrid2.setColSorting("str,int")
	mygrid2.setColumnColor("white,white")
	mygrid2.rowsBufferOutSize = 0;
	mygrid2.setMultiLine(false);
	mygrid2.selmultirows="true";
	mygrid2.delim=";";
	mygrid2.init();
} 

function Liquidar(Tipo){
	if($('Trabajador').options[$('Trabajador').selectedIndex].value!=-1){
		
		if(Tipo==2){
			for(k=0;k<mygrid.getRowsNum();k++){
				mygrid.cells(mygrid.getRowId(k),'3').setValue('100');
				CalcularMontoGrid(2,mygrid.getRowId(k),'3');
			}
			//mygrid.setColTypes("ch,ro,ro,ro,ro");
			BuscarPrestamos();
			
		}else{
			for(k=0;k<mygrid.getRowsNum();k++){
				mygrid.cells(mygrid.getRowId(k),'3').setValue('0');
				CalcularMontoGrid(2,mygrid.getRowId(k),'3');
			}
			//mygrid.setColTypes("ch,ro,ro,ed,ed");
			mygrid2.clearSelection();
			mygrid2.clearAll();
			CalcularMontoTotalGrid2();
		}
	}else{
		$('Tipo0').checked=true;
		alert("Debe escojer un Trabajador");
	}
}
function BuscarPrestamos(){
	JsonAux={"Trabajador":$('Trabajador').options[$('Trabajador').selectedIndex].value,"Forma":5,"Accion":3};
	var url = 'OtrosCalculos.php';
	var pars = 'JsonEnv=' + JsonAux.toJSONString();
	var Request = new Ajax.Request(
		url,
		{
			method: 'post',
			parameters: pars,
			//asynchronous:false, 
			onComplete:function(request){
				var JsonRec = eval( '(' + request.responseText + ')');
				if(JsonRec){
					mygrid2.addRow(JsonRec[0],JsonRec[1]+";"+muestraFloat(JsonRec[2],2),Indice);
					Indice++;
					CalcularMontoTotalGrid2();
					Element.show('ConceptosAdicionales');
					$('label').innerHTML="Conceptos Adicionales:";
					EstaVisible=true;				
				}
			}
		}
	); 
}
function Check(rowId,cellInd,stage){
	if(!stage && !$('Tipo2').checked){
		mygrid.cells(rowId,'3').setValue('0');
		mygrid.cells(rowId,'4').setValue('0,00');
		CalcularMontoTotalGrid();
		CalcularMontoTotalGrid2();
	}
	if($('Tipo2').checked){
		mygrid.cells(rowId,'0').setChecked(true);
	}
}
function CalcularMontoGrid(stage,rowId,cellInd){
var Porcentaje,Acumulado,Monto;
	if(stage==2){
		if(cellInd=='3'){
			Porcentaje=mygrid.cells(rowId,'3').getValue();
			if(Porcentaje>=0 && Porcentaje<=100){
				Acumulado=usaFloat(mygrid.cells(rowId,'2').getValue())
				Monto=(parseFloat(Acumulado)*parseFloat(Porcentaje))/100;
				mygrid.cells(rowId,'4').setValue(muestraFloat(Monto,2));
				if(Monto>0){
					mygrid.cells(rowId,'0').setChecked(true);
				}
			}else{
				alert("El valor debe estar comprendido entre 0 y 100");
				mygrid.cells(rowId,'3').setValue('0');
				mygrid.cells(rowId,'4').setValue('0.00');
				mygrid.cells(rowId,'0').setChecked(false);
			}
			CalcularMontoTotalGrid();
			CalcularMontoTotalGrid2();
		}
		if(cellInd=='4'){
			Acumulado=parseFloat(usaFloat(mygrid.cells(rowId,'2').getValue()));
			Monto=parseFloat(usaFloat(mygrid.cells(rowId,'4').getValue()));
			if(Monto<=Acumulado){
				Porcentaje=(Monto*100)/Acumulado;
				mygrid.cells(rowId,'3').setValue(Porcentaje);
				mygrid.cells(rowId,'4').setValue(muestraFloat(Monto,2));
				mygrid.cells(rowId,'0').setChecked(true);
			}
			else{
				alert("El Monto no puede ser mayor que el acumulado");
				mygrid.cells(rowId,'3').setValue('0');
				mygrid.cells(rowId,'4').setValue('0.00');
				mygrid.cells(rowId,'0').setChecked(false);
			}
			CalcularMontoTotalGrid();
			CalcularMontoTotalGrid2();
		}
	}
}
function CalcularMontoTotalGrid(){
var Monto=0,Acumulado;
	for(i=0;i<mygrid.getRowsNum();i++){
		Monto+=parseFloat(usaFloat(mygrid.cells(mygrid.getRowId(i),'4').getValue()));
	}
	$('Monto').value=muestraFloat(Monto,2);
	Acumulado=usaFloat($('Acumulado').value);
	$('Resto').value=muestraFloat(parseFloat(Acumulado)-parseFloat(Monto),2);
}
function CalcularMontoTotalGrid2(){
var Total=0,Monto=0,Ids;
	if(mygrid2.getRowsNum()>0){
		Ids=mygrid2.getAllItemIds(";").split(";");
		if(Ids.length==1){
			Total=parseFloat(usaFloat(mygrid2.cells(Ids,'1').getValue()));
		}
		if(Ids.length>1){
			for(i=0;i<Ids.length;i++){
				Total+=parseFloat(usaFloat(mygrid2.cells(Ids[i],'1').getValue()));
			}
		}
	}
	Monto=usaFloat($('Monto').value);
	$('TotalConcepto').value=muestraFloat(parseFloat(Total),2);
	$('Total').value=muestraFloat(parseFloat(Monto)+parseFloat(Total),2);
}
var EstaVisible=false;
Element.hide('ConceptosAdicionales');
function Visible(){
	if(EstaVisible){
		Element.hide('ConceptosAdicionales');
		$('label').innerHTML="Agregar Conceptos Adicionales"
		EstaVisible=false;
	}else{
		if($('Trabajador').options[$('Trabajador').selectedIndex].value!=-1){
			Element.show('ConceptosAdicionales');
			$('label').innerHTML="Conceptos Adicionales:";
			EstaVisible=true;
		}else{
			alert("Debe escojer un Trabajador");
		}
	}
}
function Agregar(){
var Bandera=true,i,ConceptoValor;
	if($('Trabajador').options[$('Trabajador').selectedIndex].value!=-1){
		if($('ConceptoCodigo').value){
			if(!mygrid2.isItemExists($('ConceptoCodigo').value)){
				CalcularValorConcepto($('Trabajador').options[$('Trabajador').selectedIndex].value,$('ConceptoCodigo').value);
				mygrid2.addRow($('ConceptoCodigo').value,$('ConceptoNombre').value+";0",Indice);
				Indice++;
			}else{
				alert("El concepto ya fue agregado");
			}
		}else{
			alert("Concepto no existe");
		}
	}else{
		alert("Debe escojer un Trabajador");
	}
}
function CalcularValorConcepto(Trabajador,Concepto){
var Valor;
	JsonAux={"Trabajador":Trabajador,"Concepto":Concepto,"Forma":5,"Accion":1};
	var url = 'OtrosCalculos.php';
	var pars = 'JsonEnv=' + JsonAux.toJSONString();
	var Request = new Ajax.Request(
		url,
		{
			method: 'post',
			parameters: pars,
			//asynchronous:false, 
			onComplete:function(request){
				var JsonRec = eval( '(' + request.responseText + ')');
				if(JsonRec){
					Valor= eval(JsonRec['F']);
					if(JsonRec['T']==1){
						Valor=parseFloat("-"+Valor);
					}
				}else{
					Valor=0;
				}
				mygrid2.cells(Concepto,'1').setValue(muestraFloat(Valor,2));
				CalcularMontoTotalGrid2();
			}
		}
	); 
}
function traeCodigo(Concepto){
	JsonAux={"Concepto":Concepto,"Forma":5,"Accion":0};
	var url = 'OtrosCalculos.php';
	var pars = 'JsonEnv=' + JsonAux.toJSONString();
	var Request = new Ajax.Request(
		url,
		{
			method: 'post',
			parameters: pars,
	//		asynchronous:true, 
			onComplete:function(request){
				if(request.responseText){
					$('ConceptoCodigo').value=request.responseText;
				}else{
					$('ConceptoCodigo').value="";
				}
			}
		}
	); 
}
function Eliminar(){
	mygrid2.deleteRow(mygrid2.getSelectedId());
	CalcularMontoTotalGrid2();
}
function CargarGrid(){
var JsonAux;
	$('Tipo0').checked=true;
	mygrid.clearSelection();
	mygrid.clearAll();
	mygrid2.clearSelection();
	mygrid2.clearAll();
	$('Acumulado').value="0,00";
	$('Monto').value="0,00";
	$('Resto').value="0,00";
	CalcularMontoTotalGrid();
	CalcularMontoTotalGrid2();
	if($('Trabajador').options[$('Trabajador').selectedIndex].value!=-1 ){
		JsonAux={"Trabajador":parseInt($('Trabajador').options[$('Trabajador').selectedIndex].value),"Forma":5,"Accion":0};
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
						for(var i=0;i<JsonRec.length-1;i++){
							mygrid.addRow(JsonRec[i]['CI'],"0;"+JsonRec[i]['N']+";"+JsonRec[i]['V']+";0;0",i);
						} 
						$('Acumulado').value=JsonRec[JsonRec.length-1]['CI'];
						$('Resto').value=JsonRec[JsonRec.length-1]['CI'];
					}
				}
			}
		); 
	} 
} 
function Guardar(){
var Conceptos=new Array(),Ids,TipoAux;
	if($("Tipo0").checked){
		TipoAux=0;
	}else if($("Tipo1").checked){
		TipoAux=1;
	}else if($("Tipo2").checked){
		TipoAux=2;
	}else{
		TipoAux=3;
	}
	if($('Trabajador').options[$('Trabajador').selectedIndex].value!=-1 ){
		if($('Total').value!='0.00'){
			if(TipoAux!=3){
				if(confirm("¿Esta seguro que desea realizar la operacion?")){
					for(var i=0;i<mygrid.getRowsNum();i++){
						if(mygrid.cells(mygrid.getRowId(i),'0').isChecked() && mygrid.cells(mygrid.getRowId(i),'4').getValue()!='0,00' ){
							Conceptos[i]= new Array();
							Conceptos[i]['0']=parseInt(mygrid.getRowId(i));
							Conceptos[i]['1']=parseFloat(usaFloat(mygrid.cells(mygrid.getRowId(i),'4').getValue()));
							Conceptos[i]['2']=2;
						}
					}
					if(mygrid2.getRowsNum()>0){
						Ids=mygrid2.getAllItemIds(";").split(";");
						i++;
						if(Ids.length==1){
							Conceptos[i]= new Array();
							Conceptos[i]['0']=parseInt(Ids);
							Conceptos[i]['1']=parseFloat(usaFloat(mygrid2.cells(Ids,'1').getValue()));
							Conceptos[i]['2']= parseFloat(usaFloat(mygrid2.cells(Ids,'1').getValue()))>0 ? 0:1;
						}
						if(Ids.length>1){
							for(j=0;j<Ids.length;j++){
								Conceptos[i+j]= new Array();
								Conceptos[i+j]['0']=parseInt(Ids[j]);
								Conceptos[i+j]['1']=parseFloat(usaFloat(mygrid2.cells(Ids[j],'1').getValue()));
								Conceptos[i+j]['2']= parseFloat(usaFloat(mygrid2.cells(Ids[j],'1').getValue()))>0 ? 0:1;
							}
						}
					}
					JsonAux={"Trabajador":$('Trabajador').options[$('Trabajador').selectedIndex].value,"Conceptos":Conceptos,"Descripcion":$('Descripcion').value,"Obser":$('Obser').value,"Tipo":TipoAux,"Forma":5,"Accion":2};
					var url = 'OtrosCalculos.php';
					var pars = 'JsonEnv=' + JsonAux.toJSONString();
					var Request = new Ajax.Request(
						url,
						{
							method: 'post',
							parameters: pars,
					//		asynchronous:true, 
							onComplete:function(request){
								if(request.responseText){
									alert("Operacion Realizada");
									CargarGrid();
									CalcularMontoTotalGrid();
									CalcularMontoTotalGrid2();
									Tabla();
								}else{
									alert("Ha ocurrido un error durante la operacion");
								}
							}
						}
					); 
				}
			}else{
				alert("Debe escojer un Tipo");
			}
		}else{
			alert("Debe pagar al menos un Concepto");
		}
	}else{
		alert("Debe escojer un Trabajador");
	}
}
function Tabla(){
	var url = 'pagar_acumulados_tabla.php';
	var pars = '';
	var updater = new Ajax.Updater('Tabla', url,{
		parameters: pars,
		asynchronous:true, 
		evalScripts:true,
		onLoading:function(request){Element.show('cargando')}, 
		onComplete:function(request){Element.hide('cargando')},
		onSuccess:function(){ 
			//new Effect.Highlight('formulario', {startcolor:'#fff4f4', endcolor:'#ffffff'});
		} 
	}); 
}
</script>
<? require ("comun/footer.php"); ?>


