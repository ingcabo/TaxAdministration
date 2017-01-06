<? require ("comun/ini.php"); require ("comun/header.php"); ?>
<br />
<span class="titulo_maestro">Consulta de Nomina Por Concepto</span>
<div id="formulario">
	<table width="500" border="0">
		<tr>
			<td width="100" >Contrato:</td>
			<td width="400"><?=helpers::combonomina($conn, '', '','','','Contrato','int_cod','cont_nom','Contrato','','SELECT * FROM rrhh.contrato WHERE emp_cod='.$_SESSION['EmpresaL'].' ORDER BY int_cod','CargarGrid()','true');?></td>
		</tr>
		<tr>
			<td width="100">Concepto:</td>
			<td width="400"><?=helpers::combonomina($conn, '', '','','','Concepto','int_cod','conc_nom','Concepto','','SELECT int_cod,conc_nom FROM rrhh.concepto WHERE conc_tipo<>2 ORDER BY int_cod','CargarGrid()','true');?></td>
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
			<td >Total Concepto:</td>
			<td ><input readonly name="Total" id="Total" value="0.00"></td>
		</tr>
		<tr>
			<td colspan="2" ><br /></td>
		</tr>
		<tr>
			<td align="left" colspan="2" ><input  type="button"  value="Eliminar Relacion" onClick="Eliminar()" ></td>
		</tr>
	</table>
</div>
<script> 
var mygrid;
buildGrid();
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
	if($('Contrato').options[$('Contrato').selectedIndex].value!=-1 && $('Concepto').options[$('Concepto').selectedIndex].value!=-1){
		JsonAux={"Contrato":parseInt($('Contrato').options[$('Contrato').selectedIndex].value),"Concepto":parseInt($('Concepto').options[$('Concepto').selectedIndex].value),"Forma":3,"Accion":0};
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
							mygrid.addRow(i,JsonRec[i]['CU']+";"+Cadena(JsonRec[i]['N'])+" "+Cadena(JsonRec[i]['A'])+";"+JsonRec[i]['V'],i);
						} 
						$('Total').value=JsonRec[JsonRec.length-1]['CU'];
					}
				}
			}
		); 
	}
} 
function Eliminar(){
var JsonAux,res;
	if($('Contrato').options[$('Contrato').selectedIndex].value==-1){
		alert("Debe escojer un Contrato");
	}else if($('Concepto').options[$('Concepto').selectedIndex].value==-1){
		alert("Debe escojer un Concepto");
	}else{
		res=confirm("Se eliminaran los valores del concepto para los trabajadores. ¿Esta Seguro que desea continuar?");
		if(res){
			JsonAux={"Contrato":parseInt($('Contrato').options[$('Contrato').selectedIndex].value),"Concepto":parseInt($('Concepto').options[$('Concepto').selectedIndex].value),"Forma":3,"Accion":1};
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

</script>
<? require ("comun/footer.php"); ?>

