<? require ("comun/ini.php"); require ("comun/header.php"); 

	function getNroDoc($conn, $tipdoc){
		$q = "SELECT max(id_new) AS nrodoc FROM historico.reasignadas ";
		$r = $conn->execute($q);
		$nrodoc = substr($r->fields['nrodoc'], 4, 4);
		if(!$nrodoc)
			$nrodoc = '499';
		//die($r->fields['nrodoc']);
		//die($nrodoc);
		return $tipdoc."-".str_pad($nrodoc + 1, 4, 0, STR_PAD_LEFT)."-".date('Y');
	}

?>
<br />
<span class="titulo_maestro">Reasignar correlativos</span>
<div id="formulario">
<form name="form1" id="form1" method="post">
	<table width="300" border="0" align="center" >
		<tr>
			<td >OP Destino:</td>
			<td ><input type="text" name="opReasignada" id="opReasignada" value="" ></td>
		</tr>
		<tr>
			<td >Correlativo asignado en BD:</td>
			<? $nroAsignado = getNroDoc($conn, '004');
				?>
			<td ><input type="text" name="opAsignada" id="opAsignada" value="<? echo $nroAsignado;?>" disabled="disabled" ></td>
		</tr>
		<tr>
			<td >OP Origen:</td>
			<td>
				<input type="text" name="opModificar" id="opModificar" value="" >
			</td>
		</tr>
		
		<tr>
			<td align="center" colspan="2" ><input  type="button"  value="Actualizar" onClick="AsignarCorrelativos();" ></td>
			
		</tr>
	</table>
</form>
</div>

<div style="height:40px;padding-top:10px;">
	<p id="Procesando" style="display:none;margin-top:0px;">
		<img alt="Cargando" src="images/loading.gif" /> Procesando...
	</p>
</div>

<script language="javascript"  type="text/javascript"> 

function AsignarCorrelativos(){
	
	if($('opReasignada').value == ''){
		alert("Debe asignar un numero de correlativo destino");
	
	}else if($('opModificar').value == ''){
		alert("Debe asignar un numero de correlativo origen");
	
	}else{
		JsonAux={"destino":'004-'+$('opReasignada').value+'-2007',"reasignada":$('opAsignada').value,"origen":'004-'+$('opModificar').value+'-2007'};
		//alert(JsonAux);
		//return false;
		var url = 'reordenarCorrelativosMP.php';
		var pars = 'JsonEnv=' + JsonAux.toJSONString();
		var Request = new Ajax.Request(
			url,
			{
				method: 'post',
				parameters: pars,
				asynchronous:true, 
				onComplete:function(request){
					document.form1.submit();
				}	
			}
		); 
	} 
} 
function Guardar(JsonRec){
	JsonAux={"Contrato":parseInt($('Contrato').options[$('Contrato').selectedIndex].value),"Trabajador1":parseInt($('Trabajador1').options[$('Trabajador1').selectedIndex].value),"Trabajador2":parseInt($('Trabajador2').options[$('Trabajador2').selectedIndex].value),"FechaIni":$('FechaIni').value,"FechaFin":$('FechaFin').value,"Accion":1};
	var url = 'CalcularNominaA.php';
	var pars = 'JsonEnv=' + JsonAux.toJSONString() + '&JsonEnvI=' + JsonRec.toJSONString();
	var Request = new Ajax.Request(
		url,
		{
			method: 'post',
			parameters: pars,
	//		asynchronous:true, 
			onComplete:function(request){
				Element.hide('Procesando'); 
				alert(request.responseText)
			}
		}
	); 
}

</script>
<? require ("comun/footer.php"); ?>