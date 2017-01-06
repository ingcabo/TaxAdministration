<?
require ("comun/ini.php");
require ("comun/header.php");

?>
<div id="msj" style="display:none"></div><br /><br />
<span class="titulo_maestro">Reporte de Compromiso de N&oacute;mina</span>
<div id="formulario">
	<table width="100%">
		<tr>
			<td valign="top">Contrato:</td>
			<td>
				<?=helpers::superComboSql($conn,
												'',
												'',
												'contratos',
												'contratos',
												'',
												'cargarPeriodos(this.value)',
												'id',
												'descripcion',
												false,
												'',
												"SELECT int_cod AS id, cont_nom AS descripcion FROM rrhh.contrato ORDER BY cont_nom")?>
			</td>
		</tr>
		<tr>
			<td width="60px">Per&iacute;odo:</td>
			<td>
				<div id="divPeriodos">
					<select name="periodos" id="periodos">
						<option value="0">Seleccione</option>
					</select>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="4" align="center"><br /><br /><input type="button" name="accion" value="Generar Reporte" onclick="verReporte()" /></td>
		</tr>
	</table>
</div>

<div style="height:40px;padding-top:10px;">
	<p id="cargando" style="display:none;margin-top:0px;">
	  <img alt="Cargando" src="images/loading.gif" /> Cargando...
	</p>
</div>

<script type="text/javascript">
	
	function cargarPeriodos(value)
	{
		if (value=='0' || value==0)
			return false;
		
		var url = 'updater_selects.php';
		var pars = 'combo=periodosContrato&id_contrato='+value+'&name=peridos&id=periodos&ms='+new Date().getTime();
		var updater = new Ajax.Updater('divPeriodos', 
			url,
			{
				method: 'get',
				parameters: pars,
				asynchronous:true, 
				evalScripts:true,
				onLoading:function(request){Element.show('cargando');}, 
				onComplete:function(request){Element.hide('cargando');}
			}); 
	}
	
	function verReporte()
	{
		if ($('contratos').value=='0' || $('periodos').value=='0')
		{
			alert("Debe llenar todos los campos");
			return false;
		}
		
		wxR = window.open("reporteCompromisoNomina.pdf.php?id_contrato="+$('contratos').value+"&id_nomina="+$('periodos').value+"&ms="+new Date().getTime(), "winX", "width=800, height=600, scrollbars=yes, resizable=yes, status=yes");
		wxR.focus()
	}
	
</script>

<? require ("comun/footer.php"); ?>