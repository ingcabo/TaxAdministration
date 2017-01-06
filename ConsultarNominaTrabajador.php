<? require ("comun/ini.php"); require ("comun/header.php"); ?>
<br />
<span class="titulo_maestro">Consulta de Nomina Por Trabajador</span>
<div id="formulario">
	<table width="500" border="0">
		<tr>
			<td width="100" >Contrato:</td>
			<td width="400"><?=helpers::combonomina($conn, '', '','','','Contrato','int_cod','cont_nom','Contrato','','SELECT * FROM rrhh.contrato WHERE emp_cod='.$_SESSION['EmpresaL'].' ORDER BY int_cod','ComboTrabajador(this.options[this.selectedIndex].value)','true');?></td>
		</tr>
		<tr>
			<td width="100">Trabajador:</td>
			<td width="400"><SELECT name="Trabajador" id="Trabajador" onChange="CargarGrid()" ></SELECT></td>
		</tr>
		<tr>
			<td colspan="2" ><br /></td>
		</tr>
		<tr>
			<td colspan="2"><div id="gridbox" width="500" height="250" class="gridbox"></div></td>
		</tr>
		<tr>
			<td colspan="2" ><br /></td>
		</tr>
		<tr>
			<td >Total Asignaciones:</td>
			<td ><input readonly name="TotalA" id="TotalA"  value="0.00"></td>
		</tr>
		<tr>
			<td >Total Deducciones:</td>
			<td ><input readonly name="TotalD" id="TotalD"  value="0.00"></td>
		</tr>
		<tr>
			<td >Total a Pagar:</td>
			<td ><input readonly name="Total" id="Total" value="0.00"></td>
		</tr>
		<tr>
			<td colspan="2" ><br /></td>
		</tr>
		<tr>
			<td align="left" ><input  type="button"  value="Eliminar Relacion" onClick="Eliminar()" ></td>
			<td align="right"><input  type="button"  value="Recibo de Pago" onClick="Imprimir()" ></td>
		</tr>
	</table>
</div>
<script> 
var mygrid;
buildGrid();
ComboTrabajador($('Contrato').options[$('Contrato').selectedIndex].value);
function ComboTrabajador(Contrato){
var JsonAux;
	$('Trabajador').length=0;
	mygrid.clearSelection();
	mygrid.clearAll();
	if(Contrato!=-1){
		JsonAux={"Contrato":parseInt(Contrato),"Forma":0};
		var url = 'CargarCombo.php';
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
							$('Trabajador').options[i]= new Option(Cadena(JsonRec[i]['N'])+" "+Cadena(JsonRec[i]['A']),JsonRec[i]['CI']);
						}
						CargarGrid();
					}
				}
			}
		); 
	}
} 
function buildGrid(){
	//set grid parameters
	mygrid = new dhtmlXGridObject('gridbox');
	mygrid.selMultiRows = true;
	mygrid.setImagePath("js/grid/imgs/");
	mygrid.setHeader("Codigo,Nombre,Valor");
	mygrid.setInitWidths("100,300,100")
	mygrid.setColAlign("center,left,center")
	mygrid.setColTypes("ro,ro,ro");
	mygrid.setColSorting("int,str,int")
	mygrid.setColumnColor("white,white,white")
	mygrid.rowsBufferOutSize = 0;
	mygrid.setMultiLine(false);
	mygrid.selmultirows="true";
	mygrid.delim=";";
	mygrid.init();
} 
function CargarGrid(){
var JsonAux;
	mygrid.clearSelection();
	mygrid.clearAll();
	if($('Trabajador').selectedIndex!=-1){
		JsonAux={"Contrato":parseInt($('Contrato').options[$('Contrato').selectedIndex].value),"Trabajador":parseInt($('Trabajador').options[$('Trabajador').selectedIndex].value),"Forma":2,"Accion":0};
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
							mygrid.addRow(i,JsonRec[i]['CU']+";"+JsonRec[i]['N']+";"+JsonRec[i]['V'],i);
						} 
						$('TotalA').value=JsonRec[JsonRec.length-1]['CU'];
						$('TotalD').value=JsonRec[JsonRec.length-1]['N'];
						$('Total').value=JsonRec[JsonRec.length-1]['V'];
						
					}
				}
			}
		); 
	}
} 
function Eliminar(){
var JsonAux,res;
	if($('Trabajador').selectedIndex==-1){
		alert("Debe escojer un Trabajador");
	}else{
		res=confirm("Se eliminaran los valores de los conceptos para el trabajador. ¿Esta Seguro que desea continuar?");
		if(res){
			JsonAux={"Contrato":parseInt($('Contrato').options[$('Contrato').selectedIndex].value),"Trabajador":parseInt($('Trabajador').options[$('Trabajador').selectedIndex].value),"Forma":2,"Accion":1};
			var url = 'OperarGrid.php';
			var pars = 'JsonEnv=' + JsonAux.toJSONString();
			var Request = new Ajax.Request(
				url,
				{
					method: 'post',
					parameters: pars,
			//		asynchronous:true, 
					onComplete:function(request){
						CargarGrid();
						alert(request.responseText);
					}
				}
			);  
		}
	}
} 
var wx;
function Imprimir(){
var JsonAux;
	if($('Contrato').options[$('Contrato').selectedIndex].value==-1){
		alert("Debe escojer un Contrato");
	}else{
		if (!wx || wx.closed) { 
			wx = window.open("precibopago.pdf.php?id="+$('Contrato').options[$('Contrato').selectedIndex].value+"&Tra="+$('Trabajador').options[$('Trabajador').selectedIndex].value+"&Conc=-1","winX","width=500,height=500,scrollbars=yes,resizable=yes,status=yes");
			wx.focus()
		} else { 
			wx.focus()
		} 
	}
}
</script>
<? require ("comun/footer.php"); ?>

