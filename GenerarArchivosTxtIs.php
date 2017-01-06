<? require ("comun/ini.php"); require ("comun/header.php"); ?>
<br />
<span class="titulo_maestro">Generar Archivo</span>
<div id="formulario">
<form name="form1" action="<? echo $PHP_SELF ?>">
	<table width="400"  >
		<tr>
			<td  align="right" >Formato:</td>
			<td >
				<select id="Tipo"  name="Tipo">
					<option value="NOMINA.txt">Nomina (Banco BOD)</option>
					<option value="HABPRIV.txt">Ley de Politica Habitacional (Banco BOD)</option>
					<option value="HISTRAB.txt">Historial de Trabajadores(Nomina Cerrada)</option>
					<option value="TRABIER.txt">Trabajadores(Nomina Abierta)</option>
					<option value="HISFON.txt">Fondo</option>
				</select>
			</td>
		</tr>	
		<tr >
			<td width="100" >Nomina:</td>
			<td ><SELECT name="Nomina" id="Nomina" ></SELECT></td>
		</tr>
		<tr>
			<td >Banco:</td>
			<td ><?=helpers::combonominaIII($conn, '','' ,'','A.nro_cuenta,B.descripcion','Cuenta','id','nro_cuenta','descripcion','Cuenta','','SELECT A.id,A.nro_cuenta,B.descripcion FROM finanzas.cuentas_bancarias AS A INNER JOIN public.banco AS B ON A.id_banco=B.id','','true','true')?></td>
		</tr>
		<tr>
			<td  >Contrato:</td>
			<td ><?=helpers::combonomina($conn, '', '','','','Contrato','int_cod','cont_nom','Contrato','','SELECT * FROM rrhh.contrato WHERE emp_cod='.$_SESSION['EmpresaL'].' ORDER BY int_cod','','true');?></td>
		</tr>	
		<tr>
			<td >Concepto:</td>
			<td ><?=helpers::combonomina($conn, '', '','','','Concepto','int_cod','conc_nom','Concepto','','SELECT int_cod,conc_nom FROM rrhh.concepto WHERE conc_tipo<>2 ORDER BY int_cod','','true');?></td>
		</tr>
		<tr>
			<td >Periodo:</td>
			<td > 
				<SELECT name="Mes" id="Mes" >
					<option value="1">Enero</option>
					<option value="2">Febrero</option>
					<option value="3">Marzo</option>
					<option value="4">Abril</option>
					<option value="5">Mayo</option>
					<option value="6">Junio</option>
					<option value="7">Julio</option>
					<option value="8">Agosto</option>
					<option value="9">Septiembre</option>
					<option value="10">Obtubre</option>
					<option value="11">Noviembre</option>
					<option value="12">Diciembre</option>
				</SELECT>
				<input name="Ano" id="Ano" >
			</td>
		</tr>
		<tr>
			<td align="right" colspan="2"><br /><input type="button" value="Generar Reporte" onclick="Generar()" ></td>
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
ComboNomina();
function ComboNomina(){
var JsonAux;
	Element.show('Procesando'); 
	$('Nomina').length=1;
	JsonAux={"Forma":1};
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
					$('Nomina').options[0]= new Option("Seleccione",-1);
					for(var i=1;i<=JsonRec.length;i++){
						$('Nomina').options[i]= new Option(JsonRec[i-1]['D'],JsonRec[i-1]['CI']);
					}
				}
			}
		}
	); 
	Element.hide('Procesando'); 
} 
function Generar(){
var JsonAux;
	if(!$('Ano').value && $('Tipo').options[$('Tipo').selectedIndex].value=="HABPRIV.txt"){
		alert("Debe especificar el Año");
	}else if($('Nomina').options[$('Nomina').selectedIndex].value== -1 && $('Tipo').options[$('Tipo').selectedIndex].value=="NOMINA.txt"){
		alert("Debe Escojer una Nomina");
	}else if($('Nomina').options[$('Nomina').selectedIndex].value== -1 && $('Tipo').options[$('Tipo').selectedIndex].value=="HISTRAB.txt"){
		alert("Debe Escojer una Nomina");
	}else if($('Nomina').options[$('Nomina').selectedIndex].value== -1 && $('Tipo').options[$('Tipo').selectedIndex].value=="HISFON.txt"){
		alert("Debe Escojer una Nomina");
	}else if($('Nomina').options[$('Nomina').selectedIndex].value!= -1 && $('Tipo').options[$('Tipo').selectedIndex].value=="HISFON.txt" && $('Concepto').options[$('Concepto').selectedIndex].value== -1){
		alert("Debe Escojer un Concepto");
	}else if($('Contrato').options[$('Contrato').selectedIndex].value== -1 && $('Tipo').options[$('Tipo').selectedIndex].value=="TRABIER.txt"){
		alert("Debe Escojer un Contrato");
	}else if($('Contrato').options[$('Contrato').selectedIndex].value!= -1 && $('Tipo').options[$('Tipo').selectedIndex].value=="TRABIER.txt" && $('Concepto').options[$('Concepto').selectedIndex].value== -1){
		alert("Debe Escojer un Concepto");
	}else{
		JsonAux={"Forma":7,"Accion":$('Tipo').options[$('Tipo').selectedIndex].value,"Contrato":$('Contrato').options[$('Contrato').selectedIndex].value,"Concepto":$('Concepto').options[$('Concepto').selectedIndex].value,"Mes":$('Mes').options[$('Mes').selectedIndex].value,"Ano":$('Ano').value,"Nomina":$('Nomina').options[$('Nomina').selectedIndex].value,"Cuenta":$('Cuenta').options[$('Cuenta').selectedIndex].value};
		var url = 'OtrosCalculos.php';
		var pars = 'JsonEnv=' + JsonAux.toJSONString();
		var Request = new Ajax.Request(
			url,
			{
				method: 'post',
				parameters: pars,
		//		asynchronous:true, 
				onComplete:function(request){
					//alert(request.responseText);
					var JsonRec = eval( '(' + request.responseText + ')');
					if(JsonRec){
						Popup= window.open("GenerarArchivosTxtA.php?archivo="+$('Tipo').options[$('Tipo').selectedIndex].value,"winX","width=500,height=500,scrollbars=yes,resizable=yes,status=yes");
					}else{
						alert("No se pudo generar el archivo");
					}
				}
			}
		); 
	} 
} 
</script>
<? require ("comun/footer.php"); ?>