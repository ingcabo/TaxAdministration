<? set_time_limit(0); require ("comun/ini.php"); require ("comun/header.php"); ?>
<br />
<span class="titulo_maestro">Reversar Nomina</span>
<div id="formulario">
	<table width="200" border="0" >
		<tr >
			<td >Contrato:</td>
			<td ><?=helpers::combonomina($conn, '', '','','','Contrato','int_cod','cont_nom','Contrato','','SELECT A.int_cod AS int_cod,A.cont_nom AS cont_nom FROM rrhh.contrato as A WHERE A.emp_cod='.$_SESSION['EmpresaL'].' AND A.cont_estatus=0 ORDER BY A.int_cod','CalcularFechas(this.options[this.selectedIndex].value)','true');?></td>
		</tr>
		<tr>
			<td >Fecha Inicio:</td>
			<td ><input name="FechaIni" id="FechaIni" type="text" disabled ></td>
		</tr>
		<tr>
			<td >Fecha Fin:</td>
			<td ><input name="FechaFin" id="FechaFin" type="text" disabled ></td>
		</tr>
		<tr>
			<td colspan="2" ><br /></td>
		</tr>
		<tr>
			<td colspan="2" align="right">
			<div id="consulta">
				<input  type="button"  value="Reversar" onClick="Reversar($('Contrato').options[$('Contrato').selectedIndex].value, $('FechaIni').value, $('FechaFin').value)" >
			</div>
			</td>
		</tr>
	</table>
</div>

<div style="height:40px;padding-top:10px;">
	<p id="Procesando" style="display:none;margin-top:0px;">
		<img alt="Cargando" src="images/loading.gif" /> Procesando...
	</p>
</div>

<script language="javascript"  type="text/javascript"> 

function Reversar(Contrato,FechaIni,FechaFin){
	if(Contrato==-1){
		alert("Debe escojer un Contrato");
	}else{
		Element.show('Procesando'); 
		JsonAux={"Contrato":parseInt(Contrato),"FechaIni":FechaIni,"FechaFin":FechaFin};
		var url = 'ConsultarEstatusNomina.php';
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
						JsonAux={"Contrato":parseInt(Contrato),"Nomina":JsonRec,"FechaIni":FechaIni,"FechaFin":FechaFin,"Accion":4};
						var url = 'CalcularNominaA.php';
						var pars = 'JsonEnv=' + JsonAux.toJSONString();
						var Request = new Ajax.Request(
							url,
							{
								method: 'post',
								parameters: pars,
								//asynchronous:true, 
								onComplete:function(request){
									alert(request.responseText);
									Element.hide('Procesando');
									top.contenido.location='ReversarNomina.php';
								}
							}
						); 
					}else{
						alert("HA OCURRIDO UN ERROR, POSIBLE CAUSA: NOMINA EN PROCESO PRESUPUESTARIO");
						Element.hide('Procesando');
					}
				}
			}
		); 
	} 
} 
function InicializarVariables(Contrato){
var JsonAux;
	Element.show('Procesando'); 
	JsonAux={"Forma":3,"Contrato":parseInt(Contrato)};
	var url = 'OtrosCalculos.php';
	var pars = 'JsonEnv=' + JsonAux.toJSONString();
	var Request = new Ajax.Request(
		url,
		{
			method: 'post',
			parameters: pars,
			asynchronous:false, 
			onComplete:function(request){
				alert(request.responseText);
				Element.hide('Procesando'); 
			}
		}
	); 
}

function CalcularFechas(Contrato){
var JsonAux;
	if(Contrato!=-1){
		Element.show('Procesando'); 
		JsonAux={"Contrato":parseInt(Contrato),"Forma":13};
		var url = 'OtrosCalculos.php';
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
						$('FechaIni').value=JsonRec[0];
						$('FechaFin').value=JsonRec[1];
					}
					Element.hide('Procesando'); 
				}
			}
		); 
	}else{
		$('FechaIni').value="";
		$('FechaFin').value="";
	} 
}
</script>
<? require ("comun/footer.php"); ?>