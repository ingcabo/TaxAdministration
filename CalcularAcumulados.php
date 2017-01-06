<? if(empty($_REQUEST['M'])) {	
	require ("comun/ini.php");
	require ("comun/header.php"); 
?>
 <br />
<span class="titulo_maestro">Calcular Acumulados</span>
<div id="formulario">
<table>
	<tr>
		<td >Contrato:</td>
		<td ><?=helpers::combonomina($conn, '', '','','','Contrato','int_cod','cont_nom','Contrato','','SELECT * FROM rrhh.contrato WHERE emp_cod='.$_SESSION['EmpresaL'].' ORDER BY int_cod','','true');?></td>
	</tr>
	<tr>
		<td>Periodo:</td>
		<td><input type="text" id="Periodo" name="Periodo"></td>
	</tr>
	<tr>
		<td colspan="2" align="right"><input type="button" value="Calcular" onClick="Calcular()"></td>
	</tr>

</table>
</div>
<div style="height:40px;padding-top:10px;">
	<p id="Procesando" style="display:none;margin-top:0px;">
		<img alt="Cargando" src="images/loading.gif" /> Procesando...
	</p>
</div>
<br />
<? }else{ 
		require ('lib/config.php'); ?>
		<script src="js/json.js" type="text/javascript"></script>
		<script src="js/prototype.js" type="text/javascript"></script>
		
		<input type="hidden" id="Pass" name="Pass" value="<?=$_REQUEST['Pass']?>">
		<input type="text" style="display:none " id="Periodo" name="Periodo" value="<?=$_REQUEST['Periodo']?>">
<? } ?>
<script language="javascript"  type="text/javascript"> 
<? if(!empty($_REQUEST['M'])){ echo "Calcular();\n"; } ?>
function Calcular(){
	try{
		Element.show('Procesando'); 
		Contrato = $('Contrato').options[$('Contrato').selectedIndex].value;
		PeriodoAux=$('Periodo').value;
		JsonAux={"Contrato":parseInt(Contrato),"Accion":1,"Periodo":PeriodoAux};
		var url = 'ConsultarHistoricoNomina.php';
		var pars = 'JsonEnv=' + JsonAux.toJSONString();
		var Request = new Ajax.Request(
			url,
			{
			method: 'post',
			parameters: pars,
			//asynchronous:true, 
			onComplete:function(request){
					var JsonRec = eval( '(' + request.responseText + ')');
					if(JsonRec!=-1){
							JsonAux={"Contrato":parseInt(Contrato),"Accion":0,"Periodo":PeriodoAux};					
							var url = 'CalcularAcumuladosA.php';
							var pars = 'JsonEnv=' + JsonAux.toJSONString();
							var Request = new Ajax.Request(
								url,
								{
									method: 'post',
									parameters: pars,
									asynchronous:true, 
									onComplete:function(request){
										var JsonRec = eval( '(' + request.responseText + ')');
										if(JsonRec && JsonRec!="-1T" && JsonRec!="-1C"){
											for(var i=0;i<JsonRec.length;i++){
												//alert(JsonRec[i]['V']);
												JsonRec[i]['V']=eval(JsonRec[i]['V']);
												JsonRec[i]['D']=eval(JsonRec[i]['D']);
											}					
											Guardar(JsonRec);
										}
									}
								}
							);
				}else{
					alert("NO SE PUEDE GENERAR ACUMULADOS DEL MES INDICADO, NO HAY SUFICIENTES NOMINAS CERRADAS");
					Element.hide('Procesando');
				}
			}
		});
	}catch(e) {alert("Error:" + " - "+e.message);}	
	var ventana = window.self;
	ventana.opener = window.self;
	ventana.close(); 
} 
function Guardar(JsonRec){
	Contrato = $('Contrato').options[$('Contrato').selectedIndex].value;
	PeriodoAux=$('Periodo').value;
	JsonAux={"Contrato":parseInt(Contrato),"Accion":1,"Periodo":PeriodoAux};
	var url = 'CalcularAcumuladosA.php';
	var pars = 'JsonEnv=' + JsonAux.toJSONString() + '&JsonEnvI=' + JsonRec.toJSONString();
	var Request = new Ajax.Request(
		url,
		{
			method: 'post',
			parameters: pars,
	//		asynchronous:true, 
			onComplete:function(request){
			Element.hide('Procesando'); 
			alert(request.responseText);			
			}
		}
	); 
}
//"C:\Archivos de programa\Mozilla Firefox\firefox.exe" "localhost\libertador\CalcularAcumulados.php?M=1&Pass=123"
//"C:\Archivos de programa\Internet Explorer\IEXPLORE.EXE" "http:\\localhost\libertador\CalcularAcumulados.php?M=1&Pass=123"

</script>
<? if(empty($_REQUEST['M'])) { require ("comun/footer.php"); } ?>