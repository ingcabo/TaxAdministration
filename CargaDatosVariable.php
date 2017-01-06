<? require ("comun/ini.php"); require ("comun/header.php"); ?>
<br />
<span class="titulo_maestro">Valores de Variables</span>
<div id="formulario">
	<table width="500" border="0">
		<tr>
			<td width="80" >Contrato:</td>
			<td width="420"><?=helpers::combonomina($conn, '', '','','','Contrato','int_cod','cont_nom','Contrato','','SELECT * FROM rrhh.contrato WHERE emp_cod='.$_SESSION['EmpresaL'].' ORDER BY int_cod','CargarGrid()','true');?></td>
		</tr>
		<tr>
			<td width="80">Variable:</td>
			<td width="420"><?=helpers::combonomina($conn, 'rrhh.variable', '','','int_cod','Variable','int_cod','var_nom','','','','CargarGrid()','true');?></td>
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
			<td align="left"><input  type="button"  value="Eliminar Relacion" onClick="Eliminar()" ></td>
			<td align="right"><input  type="button"  value="Guardar" onClick="Guardar()" ></td>
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
	mygrid.setHeader("Codigo,Nombre,Cedula,Valor");
	mygrid.setInitWidths("100,300,100,100")
	mygrid.setColAlign("center,left,left,center")
	mygrid.setColTypes("ro,ro,ro,ed");
	mygrid.setColSorting("int,str,,int,int")
	mygrid.setColumnColor("white,white,white,white")
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
	if($('Contrato').options[$('Contrato').selectedIndex].value!=-1 && $('Variable').options[$('Variable').selectedIndex].value!=-1){
		JsonAux={"Contrato":parseInt($('Contrato').options[$('Contrato').selectedIndex].value),"Variable":parseInt($('Variable').options[$('Variable').selectedIndex].value),"Forma":1,"Accion":0};
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
							mygrid.addRow(JsonRec[i]['CI'],JsonRec[i]['CU']+";"+Cadena(JsonRec[i]['N'])+" "+Cadena(JsonRec[i]['A'])+";"+JsonRec[i]['IU']+";"+muestraFloat(parseFloat(JsonRec[i]['V']),2),i);
						}
					}
				}
			}
		); 
	}
} 
function Guardar(){
var JsonAux,Trabajadores=new Array;
	if($('Contrato').options[$('Contrato').selectedIndex].value==-1){
		alert("Debe escojer un Contrato");
	}else if($('Variable').options[$('Variable').selectedIndex].value==-1){
		alert("Debe escojer una Variable");
	}else{
		mygrid.clearSelection();
		for(i=0;i<mygrid.getRowsNum();i++){
			Trabajadores[i] = new Array;
			Trabajadores[i][0]=mygrid.getRowId(i);
			Trabajadores[i][1]= parseFloat(usaFloat(mygrid.cells(mygrid.getRowId(i),3).getValue()));
		}
		JsonAux={"Variable":parseInt($('Variable').options[$('Variable').selectedIndex].value),"Trabajadores":Trabajadores,"Forma":1,"Accion":1};
		var url = 'OperarGrid.php';
		var pars = 'JsonEnv=' + JsonAux.toJSONString();
		var Request = new Ajax.Request(
			url,
			{
				method: 'post',
				parameters: pars,
		//		asynchronous:true, 
				onComplete:function(request){
					alert(request.responseText);
				}
			}
		);  
		//CargarGrid();
	}
} 
function Eliminar(){
var JsonAux,res;
	if($('Variable').options[$('Variable').selectedIndex].value==-1){
		alert("Debe escojer una Variable");
	}else{
		res=confirm("Se eliminaran los valores de la variable para los trabajadores. ¿Esta Seguro que desea continuar?");
		if(res){
			JsonAux={"Variable":parseInt($('Variable').options[$('Variable').selectedIndex].value),"Forma":1,"Accion":2};
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

