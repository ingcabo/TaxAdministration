<? require ("comun/ini.php"); require ("comun/header.php"); ?>
<br />
<span class="titulo_maestro">Valores de Variables</span>
<div id="formulario">
	<table width="500" border="0">
		<tr>
			<td width="100" >Nomina:</td>
			<td width="400"><?=helpers::combonomina($conn, '', '','','','Contrato','int_cod','cont_nom','Contrato','','SELECT * FROM rrhh.contrato WHERE emp_cod='.$_SESSION['EmpresaL'].' AND cont_estatus = 0 ORDER BY int_cod','ComboTrabajador(this.options[this.selectedIndex].value)','true');?></td>
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
			<td align="left"><input  type="button"  value="Eliminar Relacion" onClick="Eliminar()" ></td>
			<td align="right"><input  type="button"  value="Guardar" onClick="Guardar()" ></td>
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
	mygrid.clearSelection()
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
						//$('Trabajador').options[0]= new Option(" ",-1);
						for(var i=0;i<JsonRec.length;i++){
							$('Trabajador').options[i]= new Option(Cadena(JsonRec[i]['N'])+" "+Cadena(JsonRec[i]['A'])+" "+Cadena(JsonRec[i]['IU']),JsonRec[i]['CI']);
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
	mygrid.setColTypes("ro,ro,ed");
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
		JsonAux={"Trabajador":parseInt($('Trabajador').options[$('Trabajador').selectedIndex].value),"Forma":0,"Accion":0};
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
							mygrid.addRow(JsonRec[i]['CI'],JsonRec[i]['CU']+";"+JsonRec[i]['N']+";"+muestraFloat(parseFloat(JsonRec[i]['V']),2),i);
						}
					}
				}
			}
		); 
	}
} 
function Guardar(){
var JsonAux,Variables=new Array;
	if($('Trabajador').selectedIndex==-1){
		alert("Debe escojer un Trabajador");
	}else{
		mygrid.clearSelection()
		for(i=0;i<mygrid.getRowsNum();i++){
			Variables[i] = new Array;
			Variables[i][0]=mygrid.getRowId(i);
			Variables[i][1]= parseFloat(usaFloat(mygrid.cells(mygrid.getRowId(i),2).getValue()));
		}
		JsonAux={"Trabajador":parseInt($('Trabajador').options[$('Trabajador').selectedIndex].value),"Variables":Variables,"Forma":0,"Accion":1};
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
	if($('Trabajador').selectedIndex==-1){
		alert("Debe escojer un Trabajador");
	}else{
		res=confirm("Se eliminaran los valores de las variables para el trabajador. ¿Esta Seguro que desea continuar?");
		if(res){
			JsonAux={"Trabajador":parseInt($('Trabajador').options[$('Trabajador').selectedIndex].value),"Forma":0,"Accion":2};
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

