<?
require ("comun/ini.php");
require ("comun/header.php");

?>
<div id="msj" style="display:none"></div><br /><br />
<span class="titulo_maestro">Balance de Comprobaci&oacute;n</span>
<div id="formulario">
	<p class="titulo_maestro" style="text-align:center">Muestra el estado de la cuentas contables en el per&iacute;odo indicado</p><br />
	<table width="100%">
		<tr>
			<td valign="top">Tipo:</td>
			<td>
				<select name="tipo" id="tipo" onchange="traeAniosDesdeUpdater(this.value)">
					<option value="0">Seleccione</option>
					<option value="G">Balance General de la Hacienda P&uacute;blica Municipal</option>
					<option value="D">Balance de Comprobaci&oacute;n</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>A&ntilde;o:</td>
			<td>
				<div id="divAnios">
					<select name="anios" id="anios">
						<option value="0">Seleccione</option>
					</select>
				</div>
			</td>
		</tr>
		<tr>
			<td>Mes:</td>
			<td>
				<div id="divMeses">
					<select name="meses" id="meses">
						<option value="0">Seleccione</option>
					</select>
				</div>
			</td>
		</tr>
		<tr>
			<td width="60px">Detallado:</td>
			<td>
				<input type="radio" name="detallado" id="detallado_S" value="S" disabled />S&iacute;&nbsp;&nbsp;
				<input type="radio" name="detallado" id="detallado_N" value="N" disabled />No
			</td>
		</tr>
		<tr>
			<td colspan="4" align="center"><br /><br /><input type="button" name="accion" value="Generar Reporte" onclick="verReporte()" /></td>
		</tr>
	</table>
</div>

<div id="divReporte"></div>
<div style="height:40px;padding-top:10px;">
	<p id="cargando" style="display:none;margin-top:0px;">
	  <img alt="Cargando" src="images/loading.gif" /> Cargando...
	</p>
</div>

<script type="text/javascript">
	
	function traeAniosDesdeUpdater(tipo)
	{
		toggleDetalles(tipo);
		if (tipo == '0')
			return false;
		
		var url = 'updater_selects.php';
		var pars = 'combo=balance_comp_anios&tipo='+tipo+'&status=\'C\',\'A\'&ms='+new Date().getTime();
		var updater = new Ajax.Updater('divAnios', 
			url,
			{
				method: 'get',
				parameters: pars,
				asynchronous:true, 
				evalScripts:true,
				onLoading:function(request){Element.show('cargando')}, 
				onComplete:function(request)
				{
					Element.hide('cargando');
					while ($('meses').length > 1)
						$('meses').remove(1);
				}
			}); 
	}
	
	function traeMesesDesdeUpdater(anio)
	{
		tipo = $('tipo').value;
		var url = 'updater_selects.php';
		var pars = 'combo=balance_comp_meses&anio='+anio+'&tipo='+tipo+'&status=\'C\',\'A\'&ms='+new Date().getTime();
		var updater = new Ajax.Updater('divMeses', 
			url,
			{
				method: 'get',
				parameters: pars,
				asynchronous:true, 
				evalScripts:true,
				onLoading:function(request){Element.show('cargando')}, 
				onComplete:function(request){Element.hide('cargando')}
			}); 
	}
	
	function toggleDetalles(value)
	{
		if (value == "G")
		{
			$('detallado_S').checked = false;
			$('detallado_S').disabled = true;
			$('detallado_N').checked = false;
			$('detallado_N').disabled = true;
		}
		else
		{
			$('detallado_S').disabled = false;
			$('detallado_N').disabled = false;
		}
	}
	
	function verReporte()
	{
		if ($('tipo').value=='0' || $('anios').value=='0' || $('meses').value=='0')
		{
			alert("Debe llenar todos los campos");
			return false;
		}
		
		detallado = ($('detallado_S').checked) ? $('detallado_S').value:$('detallado_N').value;
		wxR = window.open("balanceComprobacion.pdf.php?tipo="+$('tipo').value+"&anio="+$('anios').value+"&mes="+$('meses').value+"&detallado="+detallado+"&ms="+new Date().getTime(), "winX", "width=800, height=600, scrollbars=yes, resizable=yes, status=yes");
		wxR.focus()
	}
	
</script>

<? require ("comun/footer.php"); ?>