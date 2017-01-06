<?
require ("comun/ini.php");
require ("comun/header.php");

?>
<div id="msj" style="display:none"></div><br /><br />
<span class="titulo_maestro">Reporte de Conciliaci&oacute;n Bancaria</span>
<div id="formulario">
	<table width="100%">
		<tr>
			<td width="70">Banco:</td>
			<td>
				<?=helpers::superComboSQL($conn,
												'',
												'',
												'bancos',
												'bancos',
												'',
												'traeCuentasBancarias(this.value, "nroCtas")',
												'id',
												'descripcion',
												false,
												'descripcion',
												"SELECT id, descripcion FROM public.banco")?>
				<span id="cargando_cuentas" style="display:none; font-size:11px"><img alt="Cargando" src="images/loading2.gif" /></span>
			</td>
		</tr>
		<tr>
			<td>Nro. Cuenta:</td>
			<td>
				<span id="nroCtas">
					<select name="nro_cuenta" id="nro_cuenta">
						<option value="0">Seleccione</option>
					</select>
				</span>
				<input type="hidden" name="id_cta_cont" id="id_cta_cont" />
				<span id="cargando_anios" style="display:none; font-size:11px"><img alt="Cargando" src="images/loading2.gif" /></span>
			</td>
		</tr>
		<tr>
			<td>A&ntilde;o:</td>
			<td>
				<span id="divAnio">
					<select name="anio" id="anio" disabled>
						<option value="0">Seleccione</option>
					</select>
				</span>
				<span id="cargando_meses" style="display:none; font-size:11px"><img alt="Cargando" src="images/loading2.gif" /></span>
			</td>
		</tr>
		<tr>
			<td>Mes:</td>
			<td>
				<div id="divMes">
					<select name="mes" id="mes" disabled>
						<option value="0">Seleccione</option>
					</select>
				</div>
			</td>
		</tr>
		<tr>
			<td>Incluye:</td>
			<td>
				<input type="checkbox" name="resumen" id="resumen" value="R" />&nbsp;Resumen&nbsp;&nbsp;&nbsp;
				<input type="checkbox" name="detalle" id="detalle" value="D" />&nbsp;Detalle
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
	
	//FUNCION QUE TRAE LAS CUENTAS BANCARIAS AL MOMENTO DE SELECCIONAR UN BANCOS//
	function traeCuentasBancarias(id_banco, div)
	{
		if (id_banco==0 || id_banco=='0')
			return false;
		
		var url = 'updater_selects.php';
		var pars = 'combo=cuentas_bancarias&id_banco=' + id_banco + '&onchange=traeAnio(this.value, "divAnio")&ms='+new Date().getTime();
		var updater = new Ajax.Updater(div, 
			url,
			{
				method: 'get',
				parameters: pars,
				asynchronous:true, 
				evalScripts:true,
				onLoading:function(request){Element.show('cargando_cuentas');}, 
				onComplete:function(request)
				{
					Element.hide('cargando_cuentas');
					$('mes').disabled = true;
					$('anio').disabled = true;
				}
			}); 
	}
	
	function traeAnio(id_cta_banc, div)
	{
		if (id_cta_banc==0 || id_cta_banc=='0')
		{
			$('mes').disabled = true;
			$('anio').disabled = true;
			return false;
		}
		
		var url = 'updater_selects.php';
		var pars = 'combo=ano_conc&id_cta=' + id_cta_banc + '&onchange=traeMes(this.value, "divMes")&ms='+new Date().getTime();
		var updater = new Ajax.Updater(div, 
			url,
			{
				method: 'get',
				parameters: pars,
				asynchronous:true, 
				evalScripts:true,
				onLoading:function(request){Element.show('cargando_anios');}, 
				onComplete:function(request)
				{
					Element.hide('cargando_anios');
					$('mes').disabled = true;
				}
			}); 
	}
	
	function traeMes(anio, div)
	{
		if (anio==0 || anio=='0')
		{
			$('mes').disabled = true;
			return false;
		}

		var url = 'updater_selects.php';
		var pars = 'combo=mes_conc&id_cta=' + $('nro_cuenta').value + '&anio=' + anio + '&ms='+new Date().getTime();
		var updater = new Ajax.Updater(div, 
			url,
			{
				method: 'get',
				parameters: pars,
				asynchronous:true, 
				evalScripts:true,
				onLoading:function(request){Element.show('cargando_meses');}, 
				onComplete:function(request){Element.hide('cargando_meses');}
			}); 
	}

	function verReporte()
	{
		if ((!$('resumen').checked && !$('detalle').checked) || $('bancos').value==0 || $('nro_cuenta').value==0 || $('anio').value==0 || $('mes').value==0)
		{
			if ($('bancos').value==0) alert("Debe seleccionar un banco");
			else if ($('nro_cuenta').value==0) alert("Debe seleccionar una cuenta bancaria");
			else if ($('anio').value==0) alert("Debe seleccionar un a"+String.fromCharCode(241)+"o");
			else if ($('mes').value==0) alert("Debe seleccionar un mes");
			else if (!$('resumen').checked && !$('detalle').checked) alert("Debe seleccionar al menos una opcion");

			return false;
		}
		
		var resumen = ($('resumen').checked) ? $('resumen').value:'';
		var detalle = ($('detalle').checked) ? $('detalle').value:'';
		wxR = window.open("reporteConciliacion.pdf.php?id_cta="+$('nro_cuenta').value+"&anio="+$('anio').value+"&mes="+$('mes').value+"&resumen="+resumen+"&detalle="+detalle+"&ms="+new Date().getTime(), "winX", "width=800, height=600, scrollbars=yes, resizable=yes, status=yes");
		wxR.focus()
	}
	
</script>

<? require ("comun/footer.php"); ?>