<? require ("comun/ini.php"); require ("comun/header.php"); ?>
<br />
<span class="titulo_maestro">Generar Reporte</span>
<div id="formulario">
	<table width="400" border="0" >
		<tr >
			<td width="100" >Tipo Reporte:</td>
			<td >
				<SELECT name="Tipo" id="Tipo" onChange="ProcesosComboContrato()" >
					<option value="0">Recibos de pago</option>
					<option value="1">Historial de Nomina</option>
					<option value="2">Carta al Banco</option>
					<option value="3">Conceptos Acumulados Por Mes</option>
					<option value="4">Conceptos Acumulados Por Ano y Division</option>
					<option value="5">Historial de Nomina por Depatamentos</option>
					<option value="6">Pagos Con Cheque</option>
					<option value="7">Retenciones</option>
					<option value="8">Resumen Anual por Trabajador</option>
					<option value="9">Fondos</option>
					<option value="10">Pagos en Efectivo</option>
					<option value="11">Relacion de Pagos por Banco (Depositos)</option>
				</SELECT>
			</td>
		</tr>
		<tr >
			<td width="100" >Nomina:</td>
			<td ><SELECT name="Nomina" id="Nomina" onChange="ProcesosComboContrato()" ></SELECT></td>
		</tr>
		<tr>
			<td >Trabajador:</td>
			<td ><SELECT name="Trabajador" id="Trabajador" ></SELECT></td>
		</tr>
		<tr>
			<td >Concepto:</td>
			<td ><SELECT name="Concepto" id="Concepto" ></SELECT></td>
		</tr>
		<tr>
			<td >Cuenta - Banco:</td>
			<td ><?=helpers::combonominaIII($conn, '','' ,'','A.nro_cuenta,B.descripcion','Cuenta','id','nro_cuenta','descripcion','Cuenta','','SELECT A.id,A.nro_cuenta,B.descripcion FROM finanzas.cuentas_bancarias AS A INNER JOIN public.banco AS B ON A.id_banco=B.id','','true','true')?></td>
		</tr>
		<tr>
			<td >Banco:</td>
			<td ><?=helpers::combonomina($conn, '','' ,'','','Banco','id','descripcion','Banco','','SELECT A.id, A.descripcion FROM public.banco AS A','','true','Seleccione...')?></td>
		</tr>
		<tr>
			<td >Periodo:</td>
			<td ><input type="text" id="Periodo" name="Periodo"></td>
		</tr>
		<tr>
			<td >Division:</td>
			<td ><?=helpers::combonomina($conn, '','' ,'','div_nom','Division','int_cod','div_nom','Division','','SELECT int_cod,div_nom FROM rrhh.division WHERE emp_cod='.$_SESSION['EmpresaL'],'','true','Todas')?></td>
		</tr>
		<tr>
			<td >Fecha Inicio:</td>
			<td ><input type="text" id="FecIni" name="FecIni"></td>
		</tr>
		<tr>
			<td >Fecha Fin:</td>
			<td ><input type="text" id="FecFin" name="FecFin"></td>
		</tr>
		<tr>
			<td align="right" colspan="2"><br /><input  type="button"  value="Generar Reporte" onClick="Imprimir()" ></td>
		</tr>
	</table>
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
	ProcesosComboContrato()
	Element.hide('Procesando'); 
} 

function ProcesosComboContrato(){
	Element.show('Procesando'); 
	ComboTrabajador($('Nomina').options[$('Nomina').selectedIndex].value);
	ComboConcepto($('Nomina').options[$('Nomina').selectedIndex].value);
	Element.hide('Procesando'); 
}
function ComboTrabajador(Nomina){
var JsonAux;
	$('Trabajador').length=1;
	if(Nomina!=-1){
		JsonAux={"Nomina":parseInt(Nomina),"Forma":2};
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
						$('Trabajador').options[0]= new Option("Todos",-1);
						for(var i=1;i<=JsonRec.length;i++){
							$('Trabajador').options[i]= new Option(Cadena(JsonRec[i-1]['N'])+" "+Cadena(JsonRec[i-1]['A']),JsonRec[i-1]['CI']);
						}
					}
				}
			}
		); 
	}
} 
function ComboConcepto(Nomina){
var JsonAux;
	$('Concepto').length=1;
	if(Nomina!=-1){
		JsonAux={"Nomina":parseInt(Nomina),"Forma":5};
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
						$('Concepto').options[0]= new Option("Todos",-1);
						for(var i=1;i<=JsonRec.length;i++){
							$('Concepto').options[i]= new Option(JsonRec[i-1]['N'],JsonRec[i-1]['CI']);
						}
					}
				}
			}
		); 
	}
} 
var wxR;
function Imprimir(){
var JsonAux;
	if(($('Nomina').options[$('Nomina').selectedIndex].value==-1 || !($('Nomina').value)) && $('Tipo').options[$('Tipo').selectedIndex].value!=3 && $('Tipo').options[$('Tipo').selectedIndex].value!=4 && $('Tipo').options[$('Tipo').selectedIndex].value!=5 && $('Tipo').options[$('Tipo').selectedIndex].value!=8){
		alert("Debe escojer una Nomina");
	}else if($('Tipo').options[$('Tipo').selectedIndex].value==2 && $('Cuenta').options[$('Cuenta').selectedIndex].value==-1){
		alert("Debe escojer un Banco");
	}else if($('Tipo').options[$('Tipo').selectedIndex].value==7 && $('Concepto').options[$('Concepto').selectedIndex].value==-1){
		alert("Debe escojer un Concepto");
	}else if($('Tipo').options[$('Tipo').selectedIndex].value==8 && ($('FecIni').value=='' || $('FecFin').value=='')){
		alert("Debe colocar un fecha de inicio y fin");
	}else if($('Tipo').options[$('Tipo').selectedIndex].value==9 && $('Concepto').options[$('Concepto').selectedIndex].value==-1){
		alert("Debe escojer un Concepto");
	}else{
		if($('Tipo').options[$('Tipo').selectedIndex].value==0){
			if (!wxR || wxR.closed) { 
				wxR = window.open("hrecibopago.pdf.php?id="+$('Nomina').options[$('Nomina').selectedIndex].value+"&Tra="+$('Trabajador').options[$('Trabajador').selectedIndex].value+"&Conc="+$('Concepto').options[$('Concepto').selectedIndex].value,"winX","width=500,height=500,scrollbars=yes,resizable=yes,status=yes");
				wxR.focus()
			} else { 
				wxR.focus()
			} 
		}
		if($('Tipo').options[$('Tipo').selectedIndex].value==1){
			if (!wxR || wxR.closed) { 
				wxR = window.open("hnomina.pdf.php?id="+$('Nomina').options[$('Nomina').selectedIndex].value+"&Tra="+$('Trabajador').options[$('Trabajador').selectedIndex].value+"&Conc="+$('Concepto').options[$('Concepto').selectedIndex].value,"winX","width=500,height=500,scrollbars=yes,resizable=yes,status=yes");
				wxR.focus()
			} else { 
				wxR.focus()
			} 
		}
		if($('Tipo').options[$('Tipo').selectedIndex].value==2){
			if (!wxR || wxR.closed) { 
				wxR = window.open("hcartabanco.pdf.php?id="+$('Nomina').options[$('Nomina').selectedIndex].value+"&Cuenta="+$('Cuenta').options[$('Cuenta').selectedIndex].value,"winX","width=500,height=500,scrollbars=yes,resizable=yes,status=yes");
				wxR.focus()
			} else { 
				wxR.focus()
			} 
		}
		if($('Tipo').options[$('Tipo').selectedIndex].value==3){
			if (!wxR || wxR.closed) { 
				wxR = window.open("acumuladosMes.pdf.php?Periodo="+$('Periodo').value,"winX","width=500,height=500,scrollbars=yes,resizable=yes,status=yes");
				wxR.focus()
			} else { 
				wxR.focus()
			} 
		}
		if($('Tipo').options[$('Tipo').selectedIndex].value==4){
			if (!wxR || wxR.closed) { 
				wxR = window.open("acumuladosAno.pdf.php?Periodo="+$('Periodo').value+"&Division="+$('Division').options[$('Division').selectedIndex].value,"winX","width=500,height=500,scrollbars=yes,resizable=yes,status=yes");
				wxR.focus()
			} else { 
				wxR.focus()
			} 
		}
		if($('Tipo').options[$('Tipo').selectedIndex].value==5){
			if (!wxR || wxR.closed) { 
				wxR = window.open("hnominaA.pdf.php?id="+$('Nomina').options[$('Nomina').selectedIndex].value+"&Tra="+$('Trabajador').options[$('Trabajador').selectedIndex].value+"&Conc="+$('Concepto').options[$('Concepto').selectedIndex].value,"winX","width=500,height=500,scrollbars=yes,resizable=yes,status=yes");
				wxR.focus()
			} else { 
				wxR.focus()
			} 
		}
		if($('Tipo').options[$('Tipo').selectedIndex].value==6){
			if (!wxR || wxR.closed) { 
				wxR = window.open("hrelacion_pago_cheque.pdf.php?id="+$('Nomina').options[$('Nomina').selectedIndex].value+"&Cuenta="+$('Cuenta').options[$('Cuenta').selectedIndex].value,"winX","width=500,height=500,scrollbars=yes,resizable=yes,status=yes");
				wxR.focus()
			} else { 
				wxR.focus()
			} 
		}
		if($('Tipo').options[$('Tipo').selectedIndex].value==7){
			if (!wxR || wxR.closed) { 
				wxR = window.open("hretenciones.pdf.php?id="+$('Nomina').options[$('Nomina').selectedIndex].value+"&Tra="+$('Trabajador').options[$('Trabajador').selectedIndex].value+"&Conc="+$('Concepto').options[$('Concepto').selectedIndex].value,"winX","width=500,height=500,scrollbars=yes,resizable=yes,status=yes");
				wxR.focus()
			} else { 
				wxR.focus()
			} 
		}
		if($('Tipo').options[$('Tipo').selectedIndex].value==8){
			if (!wxR || wxR.closed) { 
				wxR = window.open("hResumenAnual.pdf.php?Nomina="+$('Nomina').options[$('Nomina').selectedIndex].value+"&FecIni="+$('FecIni').value+"&FecFin="+$('FecFin').value+"&Trabajador="+$('Trabajador').options[$('Trabajador').selectedIndex].value,"winX","width=500,height=500,scrollbars=yes,resizable=yes,status=yes");
				wxR.focus()
			} else { 
				wxR.focus()
			} 
		}
		if($('Tipo').options[$('Tipo').selectedIndex].value==9){
			if (!wxR || wxR.closed) { 
				wxR = window.open("hfondo.pdf.php?id="+$('Nomina').options[$('Nomina').selectedIndex].value+"&Tra="+$('Trabajador').options[$('Trabajador').selectedIndex].value+"&Conc="+$('Concepto').options[$('Concepto').selectedIndex].value,"winX","width=500,height=500,scrollbars=yes,resizable=yes,status=yes");
				wxR.focus()
			} else { 
				wxR.focus()
			} 
		}
		if($('Tipo').options[$('Tipo').selectedIndex].value==10){
			if (!wxR || wxR.closed) { 
				wxR = window.open("hrelacion_pago_efectivo.pdf.php?id="+$('Nomina').options[$('Nomina').selectedIndex].value+"&Cuenta="+$('Cuenta').options[$('Cuenta').selectedIndex].value,"winX","width=500,height=500,scrollbars=yes,resizable=yes,status=yes");
				wxR.focus()
			} else { 
				wxR.focus()
			} 
		}
		if($('Tipo').options[$('Tipo').selectedIndex].value==11){
			if (!wxR || wxR.closed) { 
				wxR = window.open("hrelacionBanco.pdf.php?id="+$('Nomina').options[$('Nomina').selectedIndex].value+"&Banco="+$('Banco').options[$('Banco').selectedIndex].value,"winX","width=500,height=500,scrollbars=yes,resizable=yes,status=yes");
				wxR.focus()
			} else { 
				wxR.focus()
			} 
		}
	}
} 
</script>
<? require ("comun/footer.php"); ?>