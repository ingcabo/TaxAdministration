<? require ("comun/ini.php"); require ("comun/header.php"); ?>
<br />
<span class="titulo_maestro">Consulta de Acumulados</span>
<div id="formulario">
	<table width="500" border="0">
		<tr>
			<td width="100" >Trabajador:</td>
			<td width="400"><?=helpers::combonominaIII($conn, '', '','','','Trabajador','int_cod','tra_nom','tra_ape','Trabajador','','SELECT A.int_cod,A.tra_nom,A.tra_ape FROM (rrhh.trabajador AS A INNER JOIN rrhh.departamento AS B ON A.dep_cod=B.int_cod) INNER JOIN rrhh.division AS C ON B.div_cod=C.int_cod WHERE C.emp_cod='.$_SESSION['EmpresaL'].'  ORDER BY int_cod','CargarGrid()','true','','Todos');?></td>
		</tr>
		<tr>
			<td width="100">Concepto:</td>
			<td width="400"><?=helpers::combonomina($conn, '', '','','','Concepto','int_cod','conc_nom','Concepto','','SELECT int_cod,conc_nom FROM rrhh.concepto WHERE conc_tipo=2 ORDER BY int_cod','CargarGrid()','true','Todos');?></td>
		</tr>
		<tr>
			<td width="100">A&ntilde;o</td>
			<td width="400">
				<select id="Ano" name="Ano" onChange="CargarGrid()">
					<? 	for($i=date('Y')-10;$i<=date('Y')+20;$i++){
							echo $i!=date('Y') ? "<option value=$i>$i</option>" : "<option value=$i selected>$i</option>";
						}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2"><br /><div id="gridbox" width="500" height="290" class="gridbox"></div><br /></td>
		</tr>
		<tr>
			<td >Total Acumulado en el A&ntilde;o:</td>
			<td ><input readonly name="Total" id="Total" value="0.00"></td>
		</tr>
		<tr>
			<td >Total Acumulado:</td>
			<td ><input readonly name="Total2" id="Total2" value="0.00"></td>
		</tr>
	</table>
</div>
<script> 
var mygrid;
buildGrid();
CargarGrid()
function buildGrid(){
	//set grid parameters
	mygrid = new dhtmlXGridObject('gridbox');
	mygrid.selMultiRows = true;
	mygrid.setImagePath("js/grid/imgs/");
	mygrid.setHeader("Mes,Acumulado,Pago-Anticipo-Liquidacion");
	mygrid.setInitWidths("200,150,150")
	mygrid.setColAlign("center,center,center")
	mygrid.setColTypes("ro,ro,ro");
	mygrid.setColSorting("int,int,int")
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
	$('Total').value="0.00";
	$('Total2').value="0.00";
	JsonAux={"Trabajador":parseInt($('Trabajador').options[$('Trabajador').selectedIndex].value),"Concepto":parseInt($('Concepto').options[$('Concepto').selectedIndex].value),"Ano":parseInt($('Ano').options[$('Ano').selectedIndex].value),"Forma":4,"Accion":0};
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
					mygrid.addRow(1,"Enero;"+JsonRec[1]['A']+";"+JsonRec[1]['P'],1);
					mygrid.addRow(2,"Febrero;"+JsonRec[2]['A']+";"+JsonRec[2]['P'],2);
					mygrid.addRow(3,"Marzo;"+JsonRec[3]['A']+";"+JsonRec[3]['P'],3);
					mygrid.addRow(4,"Abril;"+JsonRec[4]['A']+";"+JsonRec[4]['P'],4);
					mygrid.addRow(5,"Mayo;"+JsonRec[5]['A']+";"+JsonRec[5]['P'],5);
					mygrid.addRow(6,"Junio;"+JsonRec[6]['A']+";"+JsonRec[6]['P'],6);
					mygrid.addRow(7,"Julio;"+JsonRec[7]['A']+";"+JsonRec[7]['P'],7);
					mygrid.addRow(8,"Agosto;"+JsonRec[8]['A']+";"+JsonRec[8]['P'],8);
					mygrid.addRow(9,"Septiembre;"+JsonRec[9]['A']+";"+JsonRec[9]['P'],9);
					mygrid.addRow(10,"Octubre;"+JsonRec[10]['A']+";"+JsonRec[10]['P'],10);
					mygrid.addRow(11,"Noviembre;"+JsonRec[11]['A']+";"+JsonRec[11]['P'],11);
					mygrid.addRow(12,"Diciembre;"+JsonRec[12]['A']+";"+JsonRec[12]['P'],12);
					$('Total').value=JsonRec[13]['A'];
					$('Total2').value=JsonRec[14]['A'];
				}
			}
		}
	); 
} 
</script>
<? require ("comun/footer.php"); ?>

