<?
require ("comun/ini.php");
require ("comun/header.php");

?>
<div id="msj" style="display:none"></div><br /><br />
<span class="titulo_maestro">Mayor Anal&iacute;tico</span>
<div id="formulario">
	<p class="titulo_maestro" style="text-align:center">Muestra el movimiento de las cuentas contables en el per&iacute;odo indicado</p><br />
	<table width="100%">
		<tr>
			<td width="90px">Cuenta Contable:</td>
			<td>
			<?=helpers::superComboSQL($conn, 
											'',
											0,
											'cta_cont',
											'cta_cont', 
											'', 
											'', 
											'id', 
											'descripcion', 
											false,
											'', 
											"SELECT id, (codcta || ' - ' || descripcion)::varchar AS descripcion FROM contabilidad.plan_cuenta ORDER BY codcta::text",
											'80')?>
			</td>
		</tr>
		<tr>
			<td>Desde:</td>
			<td width="150px">
				<input type="text" name="fecha_desde" id="fecha_desde"  maxlength="10" onchange="if (!validarFecha(this)){this.value='';alert('Fecha Inv'+String.fromCharCode(225)+'lida');}" />
				<a href="#" id="boton_fecha_desde" onclick="return false;">
					<img border="0"  alt="Seleccionar Fecha" src="images/calendarA.png" width="20" height="20" />
				</a>  
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
						inputField        : "fecha_desde",
						button            : "boton_fecha_desde",
						ifFormat          : "%d/%m/%Y",
						daFormat          : "%Y/%m/%d",
						align             : "Br"
					 });
				</script>
			</td>
		</tr>
		<tr>
			<td>Hasta:</td>
			<td>
				<input type="text" name="fecha_hasta" id="fecha_hasta"  maxlength="10" onchange="if (!validarFecha(this)){this.value='';alert('Fecha Inv'+String.fromCharCode(225)+'lida');}" />
				<a href="#" id="boton_fecha_hasta" onclick="return false;">
					<img border="0"  alt="Seleccionar Fecha" src="images/calendarA.png" width="20" height="20" />
				</a>  
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
						inputField        : "fecha_hasta",
						button            : "boton_fecha_hasta",
						ifFormat          : "%d/%m/%Y",
						daFormat          : "%Y/%m/%d",
						align             : "Br"
					 });
				</script>
			</td>
		</tr>
        <tr>
        	<td>Tipo Reporte: </td>
			<td align="left"><select name="tReporte" id="tReporte">
            					<option value="0">Seleccione</option>
                                <option value="1">PDF</option>
                                <option value="2">Excel</option>
                             </select>	</td>
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
	
	function validarFecha(fecha)
	{
		var upper = 31;
		if(/^(\d{2})\/(\d{2})\/(\d{4})$/.test(fecha.value)) 
		{ // dd/mm/yyyy
			if(RegExp.$2 == '02')
				upper = 29;
				
			if((RegExp.$1 > upper) || (RegExp.$2 > 12)) 
				return false;
		}
		else if(fecha.value != '')
			return false;
			
		return true;
	}

	function verReporte()
	{
		if ($('fecha_desde').value == '' || $('fecha_hasta').value == '')
		{
			alert("Debe ingresar ambas fechas");
			return false;
		}
		
		fecha_desde = new Date();
		fecha_hasta = new Date();

		fecha_desde.setDate($('fecha_desde').value.substr(0,2));
		fecha_desde.setMonth(parseInt($('fecha_desde').value.substr(3,2))-1);
		fecha_desde.setFullYear($('fecha_desde').value.substr(6,4));

		fecha_hasta.setDate($('fecha_hasta').value.substr(0,2));
		fecha_hasta.setMonth(parseInt($('fecha_hasta').value.substr(3,2))-1);
		fecha_hasta.setFullYear($('fecha_hasta').value.substr(6,4));
		
		if (fecha_desde.getTime() > fecha_hasta.getTime())
		{
			alert("Rango de fechas inv"+String.fromCharCode(225)+"lido");
			return false;
		}
		
		id_cta_cont = ($('cta_cont').value==0) ? '':$('cta_cont').value;
		if($('tReporte').value == 1){
			wxR = window.open("mayorAnalitico.pdf.php?id_cta_cont="+id_cta_cont+"&fecha_desde="+$('fecha_desde').value+"&fecha_hasta="+$('fecha_hasta').value+"&ms="+new Date().getTime(), "winX", "width=800, height=600, scrollbars=yes, resizable=yes, status=yes");
			wxR.focus();
		}else if ($('tReporte').value == 2){
			wxR = window.open("mayorAnaliticoExcel.php?id_cta_cont="+id_cta_cont+"&fecha_desde="+$('fecha_desde').value+"&fecha_hasta="+$('fecha_hasta').value+"&ms="+new Date().getTime(), "winX", "width=800, height=600, scrollbars=yes, resizable=yes, status=yes");
			wxR.focus();
		}else
			alert('Debe Seleccionar el Tipo de Reporte a Consultar');
	}
	
</script>

<? require ("comun/footer.php"); ?>