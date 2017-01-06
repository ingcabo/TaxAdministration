<? require ("comun/ini.php"); require ("comun/header.php"); ?>
<br />
<span class="titulo_maestro">Calcular Nomina</span>
<div id="formulario">
	<table width="200" border="0" >
    	<tr >
			<td >Empresa:</td>
			<td colspan="3">
				<?=helpers::combo($conn,'empresa','0','','emp_cod','Empresa','Empresa','emp_nom',
			"SELECT DISTINCT emp_cod, emp_cod || ' - ' || emp_nom AS descripcion FROM rrhh.empresa ORDER BY emp_cod")?></td>
			</tr>    
    	<tr >
			<td >Nomina:</td>
			<td ><?=helpers::combonomina($conn, '','','','','Contrato','int_cod','cont_nom','Contrato','','SELECT * FROM rrhh.contrato WHERE emp_cod='.$_SESSION['EmpresaL'].' AND cont_estatus = 0 ORDER BY int_cod','ProcesosComboContrato()','true');?></td>
		</tr>
		<tr>
			<td >Trabajador Inicio:</td>
			<td ><SELECT name="Trabajador1" id="Trabajador1" ></SELECT></td>
		</tr>
		<tr>
			<td >Trabajador Fin:</td>
			<td ><SELECT name="Trabajador2" id="Trabajador2" ></SELECT></td>
		</tr>
		<tr>
			<td >Fecha Inicio:</td>
			<td>
				<table>
					<tr>
						<td ><input name="FechaIni" id="FechaIni" type="text"  ></td>
						<td >
							<div id="boton_fecha_ini" ><a href="#" onclick="return false;"><img border="0" src="images/calendarA.png" width="20" height="20" /></a></div>  
							<script type="text/javascript">
								new Zapatec.Calendar.setup({
									firstDay          : 1,
									weekNumbers       : true,
									showOthers        : false,
									showsTime         : false,
									timeFormat        : "24",
									step              : 2,
									range             : [1900.01, 2999.12],
									electric          : false,
									singleClick       : true,
									inputField        : "FechaIni",
									button            : "boton_fecha_ini",
									ifFormat          : "%d/%m/%Y",
									daFormat          : "%Y/%m/%d",
									align             : "Br"
								});
							</script>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td >Fecha Fin:</td>
			<td>
				<table>
					<tr>
						<td ><input name="FechaFin" id="FechaFin" type="text"  ></td>
						<td>
							<div id="boton_fecha_fin" ><a href="#"  onclick="return false;"><img border="0" src="images/calendarA.png" width="20" height="20" /></a></div>  
							<script type="text/javascript">
								new Zapatec.Calendar.setup({
									firstDay          : 1,
									weekNumbers       : true,
									showOthers        : false,
									showsTime         : false,
									timeFormat        : "24",
									step              : 2,
									range             : [1900.01, 2999.12],
									electric          : false,
									singleClick       : true,
									inputField        : "FechaFin",
									button            : "boton_fecha_fin",
									ifFormat          : "%d/%m/%Y",
									daFormat          : "%Y/%m/%d",
									align             : "Br"
								});
							</script>
						</td>
					</tr>
				</table>
			<br /></td>
		</tr>
		<tr>
			<td align="left"><input  type="button"  value="Eliminar" onClick="Eliminar()" ></td>
			<td align="right"><input  type="button"  value="Calcular" onClick="Calcular($('Contrato').options[$('Contrato').selectedIndex].value)" ></td>
<? /*			<td align="right"><input  type="button"  value="Pre-Nomina" onClick="Imprimir()" ></td> */ ?>
			<td align="right"><input  type="button"  value="Pre-Nomina" onClick="Imprimir2()" ></td>
			<td align="right"><input  type="button"  value="Resumen Nomina" onClick="Imprimir3()" ></td>
		</tr>
	</table>
</div>

<div style="height:40px;padding-top:10px;">
	<p id="Procesando" style="display:none;margin-top:0px;">
		<img alt="Cargando" src="images/loading.gif" /> Procesando...
	</p>
</div>

<script language="javascript"  type="text/javascript"> 
function ProcesosComboContrato(){
	Element.show('Procesando'); 
	ComboTrabajador1($('Contrato').options[$('Contrato').selectedIndex].value);
	ComboTrabajador2($('Contrato').options[$('Contrato').selectedIndex].value);
	CalcularFechas($('Contrato').options[$('Contrato').selectedIndex].value)
}
function ComboTrabajador1(Contrato){
var JsonAux;
	$('Trabajador1').length=1;
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
						$('Trabajador1').options[0]= new Option("Todos",-1);
						for(var i=1;i<=JsonRec.length;i++){
							$('Trabajador1').options[i]= new Option(Cadena(JsonRec[i-1]['N'])+" "+Cadena(JsonRec[i-1]['A']),JsonRec[i-1]['CI']);
						}
					}
				}
			}
		); 
	}
} 
function ComboTrabajador2(Contrato){
var JsonAux;
	$('Trabajador2').length=1;
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
						$('Trabajador2').options[0]= new Option("Todos",-1);
						for(var i=1;i<=JsonRec.length;i++){
							$('Trabajador2').options[i]= new Option(Cadena(JsonRec[i-1]['N'])+" "+Cadena(JsonRec[i-1]['A']),JsonRec[i-1]['CI']);
						}
					}
				}
			}
		); 
	}
} 
function Calcular(Contrato){
	if(Contrato==-1){
		alert("Debe escojer un Contrato");
	}else if((parseInt($('Trabajador1').options[$('Trabajador1').selectedIndex].value)>parseInt($('Trabajador2').options[$('Trabajador2').selectedIndex].value)) && parseInt($('Trabajador1').options[$('Trabajador1').selectedIndex].value)!=-1 && parseInt($('Trabajador2').options[$('Trabajador2').selectedIndex].value)!=-1 ){
		alert("Error... El Primer Trabajador debe ser Menor que el segundo Trabajador");
	}else{
		Element.show('Procesando'); 
		JsonAux={"Contrato":parseInt(Contrato),"Trabajador1":parseInt($('Trabajador1').value ? $('Trabajador1').options[$('Trabajador1').selectedIndex].value : -1),"Trabajador2":parseInt($('Trabajador2').value ? $('Trabajador2').options[$('Trabajador2').selectedIndex].value : -1),"FechaIni":$('FechaIni').value,"FechaFin":$('FechaFin').value,"Accion":0};
		var url = 'CalcularNominaA.php';
		var pars = 'JsonEnv=' + JsonAux.toJSONString();
		var Request = new Ajax.Request(
			url,
			{
				method: 'post',
				parameters: pars,
				asynchronous:true, 
				onComplete:function(request){
					var JsonRec = eval( '(' + request.responseText + ')');
					if(JsonRec=="-1T"){
						alert("No hay trabajadores asociado al contrato")
						Element.hide('Procesando'); 
					}
					if(JsonRec=="-1C"){
						alert("No hay conceptos asociado al contrato")
						Element.hide('Procesando'); 
					}
					if(JsonRec && JsonRec!="-1T" && JsonRec!="-1C"){
						for(var i=0;i<JsonRec.length;i++){
							try {
								JsonRec[i]['F']=eval(JsonRec[i]['F']);
								if(JsonRec[i]['F']=='Infinity'){ 
									JsonRec[i]['F']=0;
								}
							}catch(e) {
								JsonRec[i]['F']=0;
							}
							try {	
								JsonRec[i]['D']=eval(JsonRec[i]['D']);
							}catch(e) {
								JsonRec[i]['D']='';
							} 
							try {
								JsonRec[i]['A']=eval(JsonRec[i]['A']);
								if(JsonRec[i]['A']=='Infinity'){ 
									JsonRec[i]['A']=0;
								}
							}catch(e) {
								JsonRec[i]['A']=0;
							}
						} 
						Guardar(JsonRec);
					}
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
function CalcularFechas(Contrato){
var JsonAux;
	if(Contrato!=-1){
		JsonAux={"Contrato":parseInt(Contrato),"Forma":0};
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
					/*	if(JsonRec[2]==false){
							$('FechaIni').disabled=true;
							$('FechaFin').disabled=true;
							$('boton_fecha_ini').style.display='none';
							$('boton_fecha_fin').style.display='none';
						}else{
							$('FechaIni').disabled=false;
							$('FechaFin').disabled=false; 
							$('boton_fecha_ini').style.display='inline';
							$('boton_fecha_fin').style.display='inline';
						} */
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
function Eliminar(){
	if($('Contrato').selectedIndex==0){
		alert("Debe escojer un Contrato");
	}else{
		res=confirm("Se eliminaran los valores de la nomina calculada para el contrato. ¿Esta Seguro que desea continuar?");
		if(res){
			JsonAux={"Contrato":parseInt($('Contrato').options[$('Contrato').selectedIndex].value),"Accion":2};
			var url = 'CalcularNominaA.php';
			var pars = 'JsonEnv=' + JsonAux.toJSONString();
			var Request = new Ajax.Request(
				url,
				{
					method: 'post',
					parameters: pars,
			//		asynchronous:true, 
					onComplete:function(request){
						alert(request.responseText)
					}
				}
			);
		}
	}
}
var wx;
function Imprimir(){
var JsonAux;
	if($('Contrato').options[$('Contrato').selectedIndex].value==-1 ){
		alert("Debe escojer un Contrato");
	}else{
		if (!wx || wx.closed) { 
			wx = window.open("pnomina.pdf.php?id="+$('Contrato').options[$('Contrato').selectedIndex].value,"winX","width=500,height=500,scrollbars=yes,resizable=yes,status=yes");
			wx.focus()
		} else { 
			wx.focus()
		} 
	}
} 
var wx2;
function Imprimir2(){
var JsonAux;
	if($('Contrato').options[$('Contrato').selectedIndex].value==-1 ){
		alert("Debe escojer un Contrato");
	}else{
		if (!wx2 || wx2.closed) { 
			wx2 = window.open("pnominaA.pdf.php?id="+$('Contrato').value+"&empresa="+$('Empresa').selectedIndex,"winX","width=500,height=500,scrollbars=yes,resizable=yes,status=yes");
			wx2.focus()
		} else { 
			wx2.focus()
		} 
	}
} 
var wx3;
function Imprimir3(){
var JsonAux;
	if($('Contrato').options[$('Contrato').selectedIndex].value==-1 ){
		alert("Debe escojer un Contrato");
	}else{
		if (!wx3 || wx3.closed) { 
			wx3 = window.open("pcartabanco.pdf.php?id="+$('Contrato').options[$('Contrato').selectedIndex].value,"winX","width=500,height=500,scrollbars=yes,resizable=yes,status=yes");
			wx3.focus()
		} else { 
			wx3.focus()
		} 
	}
} 
</script>
<? require ("comun/footer.php"); ?>