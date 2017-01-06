<? 
	require("comun/ini.php"); 
	require ("comun/header.php");
?>
<span style="text-align:left" class="titulo_maestro">
	Reportes Financieros 
</span>
<center>
	<div align="left" id="formulario">
		<table width="454" border="0" align="center" style=" margin-left: auto; margin-right: auto; font-size:10px; ">
		  <tr>
  			<td width="72">Generar Reporte:</td>
  			<td width="194">
  			<select name="tipo" id="tipo">
  			  <option value="" selected>Seleccione...</option>
  			  <option value="op1">Reporte Ordenes de Pago por Grupo</option>
			  <option value="op2">Reporte Ordenes de Pago General</option>
			  <option value="op3">Reporte Ordenes de Pago por Mes</option>
			  <option value="ch1">Reporte de Pagos</option>
  			  <!--<option value="pc">Partidas por Categorias</option>-->
  			</select></td>
  			<td width="174" align="center"><input name="button" type="button" onClick="imprimir()" value="Generar Reporte" /></td>
		  </tr>
		</table>
		<table width="454" border="0" align="center" style=" margin-left: auto; margin-right: auto; font-size:10px; ">
		  <tr>
		    <td id="txt_cta_bancaria" colspan="2"></td> 
		  </tr>
		  <tr>
		    <td colspan="2"> <div id="combo_ctas_bancarias"></div> </td>
		  </tr>
		  <tr>
		  <tr>
		    <td id="txt_proveedor" colspan="2"></td> 
		  </tr>
		  <tr>
		    <td colspan="2"> <div id="combo_proveedor"></div> </td>
		  </tr>
		  <tr>
		    <td id="txt_tipo_proveedor" colspan="2"></td> 
		  </tr>
		  <tr>
		    <td colspan="2"> <div id="combo_tipo_proveedor"></div> </td>
		  </tr>
		  <tr>
		    <td id="txt_unidad_ejecutora" colspan="2"></td> 
		  </tr>
		  <tr>
		    <td colspan="2"> <div id="combo_unidad_ejecutora"></div> </td>
		  </tr>
		  <tr>
		    <td id="txt_status"></td><td id="txt_tipo_compromiso"></td> 
		  </tr>
		  <tr>
		    <td> <div id="combo_status"></div> </td><td><div id="combo_tipo_compromiso"></div></td>
		  </tr>
		   <tr>
		    <td id="txt_fecha_desde" align="left"></td><td id="txt_fecha_hasta" style="text-align:left"></td> 
		  </tr>
		  <tr>
		    <td colspan="2"> <div id="fecha"></div> </td>
		  </tr>
		</table>
	</div>
</center>
<br>
<br>
<br>
<div style="height:40px;padding-top:10px;">
	<p id="cargando" style="display:none;margin-top:0px;">
  		<img alt="Cargando" src="images/loading.gif" /> Cargando...
	</p>
</div>
<script type="text/javascript">
	
	var wxR;
	function imprimir()
	{
		var JsonAux;
		var escEnEje = <?=$escEnEje?>;
			if($('tipo').value=='')
				alert("Debe Seleccionar un Tipo de Reporte a Generar");
			else if($('tipo').value == 'op1')
			{
				wxR = window.open("reporte_op_pendientes.pdf.php?fecha_desde=" + $('busca_fecha_desde').value + "&fecha_hasta=" + $('busca_fecha_hasta').value + "&id_proveedor=" + $('busca_id_proveedor').value + "&id_ue=" + $('busca_id_ue').value + "&tipocom=" + $('busca_tipocom').value + "&tipoprov=" + $('busca_tipoprov').value + "&status=" + $('busca_status').value, "winX", "width=500, height=500, scrollbars=yes, resizable=yes, status=yes");
				wxR.focus()
			}
			else if($('tipo').value == 'op2')
			{
				wxR = window.open("reporte_op_pendientes_general.pdf.php?fecha_desde=" + $('busca_fecha_desde').value + "&fecha_hasta=" + $('busca_fecha_hasta').value + "&id_proveedor=" + $('busca_id_proveedor').value + "&id_ue=" + $('busca_id_ue').value + "&tipocom=" + $('busca_tipocom').value + "&tipoprov=" + $('busca_tipoprov').value + "&status=" + $('busca_status').value, "winX", "width=500, height=500, scrollbars=yes, resizable=yes, status=yes");
				wxR.focus()
			}
			else if($('tipo').value == 'op3')
			{
				wxR = window.open("reporte_op_pendientes_fecha.pdf.php?fecha_desde=" + $('busca_fecha_desde').value + "&fecha_hasta=" + $('busca_fecha_hasta').value + "&id_proveedor=" + $('busca_id_proveedor').value + "&id_ue=" + $('busca_id_ue').value + "&tipocom=" + $('busca_tipocom').value + "&tipoprov=" + $('busca_tipoprov').value + "&status=" + $('busca_status').value, "winX", "width=500, height=500, scrollbars=yes, resizable=yes, status=yes");
				wxR.focus()
			}
			else if($('tipo').value == 'ch1')
			{
			  wxR = window.open("reporte_cheques.pdf.php?fecha_desde=" + $('busca_fecha_desde').value + "&fecha_hasta=" + $('busca_fecha_hasta').value + "&id_proveedor=" + $('busca_id_proveedor').value + "&tipoprov=" + $('busca_tipoprov').value + "&status=" + $('busca_status').value + "&id_cuenta=" + $('busca_cta_bancaria').value, "winX", "width=500, height=500, scrollbars=yes, resizable=yes, status=yes");
  			  wxR.focus()
			}
	}
  
  function desactivar(opcion)
  {
    if (opcion==1)
    {
      if ($F('id_cp') != '')
      {
        $('busca_cp').disabled = true;
      }
      else
      {
        $('busca_cp').disabled = false;
      }
    }
    else
    {
      if ($F('busca_cp') == 0)
      {
        $('id_cp').disabled = false;
      }
      else
      {
        $('id_cp').disabled = true;
      }
    }
  }
  
  function act_codigo(tipo)
  {
    if (tipo == 'op1' || tipo == 'op2' || tipo == 'op3')
    {
	  $('txt_cta_bancaria').innerHTML = '';
	  $('combo_ctas_bancarias').innerHTML = '';	
	  $('txt_proveedor').innerHTML = '<br /><br />Proveedor';
      $('combo_proveedor').innerHTML = <?=helpers::superCombo($conn, "SELECT id, nombre AS descripcion FROM proveedores ORDER BY nombre",0,'busca_id_proveedor','busca_id_proveedor', '', '', 'id', 'descripcion', '', '', '', 'Seleccione...', true)?>; 
      $('txt_tipo_proveedor').innerHTML = '<br />Tipo de Proveedor';
      $('combo_tipo_proveedor').innerHTML = "<select name=\"busca_tipoprov\" id=\"busca_tipoprov\"><option value=\"0\">Seleccione</option><option value=\"P\">Proveedor</option><option value=\"C\">Contratista</option><option value=\"A\">Ambos</option><option value=\"S\">Proveedor de Servicios</option><option value=\"B\">Ciudadanos</option></select>";
      $('txt_unidad_ejecutora').innerHTML = '<br />Unidad Ejecutora';
      $('combo_unidad_ejecutora').innerHTML = <?=helpers::superCombo($conn, "SELECT id, id||' - '||descripcion AS descripcion FROM unidades_ejecutoras WHERE id_escenario='$escEnEje' ORDER BY id",0,'busca_id_ue','busca_id_ue', '', '', 'id', 'descripcion', '', '', '', 'Seleccione...', true)?>; 
	  $('txt_status').innerHTML = '<br />Status';
	  $('txt_tipo_compromiso').innerHTML = '<br />Tipo de Compromiso';
	  $('combo_status').innerHTML = "<select name=\"busca_status\" id=\"busca_status\"><option value=\"0\">Seleccione</option><option value=\"1\">Registrada</option><option value=\"2\">Aprobada</option><option value=\"3\">Anulada</option></select>";
	  $('combo_tipo_compromiso').innerHTML = <?=helpers::superCombo($conn, "(SELECT id, descripcion FROM tipos_documentos WHERE id_momento_presupuestario = '1' ORDER BY descripcion) UNION (SELECT '099' as id, 'Sin Imputacion' as descripcion)",0,'busca_tipocom','busca_tipocom', '', '', 'id', 'descripcion', '', '', '', 'Todos', true)?>; 
      $('txt_fecha_desde').innerHTML = '<br />Desde';
	  $('txt_fecha_hasta').innerHTML = '<br />Hasta';
	  $('fecha').innerHTML = "<table><tr><td><input style=\"width:100px\"  type=\"text\" name=\"busca_fecha_desde\" id=\"busca_fecha_desde\"/></td><td><div id=\"boton_busca_fecha_desde\"><a href=\"#\" onclick=\"return false;\"><img border=\"0\" alt=\"Seleccionar Fecha\" src=\"images/calendarA.png\" width=\"20\" height=\"20\" /></a></div></td><td><input style=\"width:100px\" type=\"text\" name=\"busca_fecha_hasta\" id=\"busca_fecha_hasta\"/></td><td><div id=\"boton_busca_fecha_hasta\"><a href=\"#\" onclick=\"return false;\"><img border=\"0\"  alt=\"Seleccionar Fecha\" src=\"images/calendarA.png\" width=\"20\" height=\"20\" /></a></div></td></tr></table>";
      calendario_desde();
	  calendario_hasta();
 
	}
    else if(tipo == 'ch1')
    {
	  $('txt_cta_bancaria').innerHTML = '<br /><br />Cuentas Bancarias';
	  $('combo_ctas_bancarias').innerHTML = <?=helpers::superCombo($conn, "SELECT c.id, b.descripcion ||' - '|| c.nro_cuenta AS descripcion FROM finanzas.cuentas_bancarias c INNER JOIN public.banco b ON (c.id_banco = b.id) ORDER BY descripcion::varchar",0,'busca_cta_bancaria','busca_cta_bancaria', '', '', 'id', 'descripcion', '', '', '', 'Seleccione...', true)?>;		
	  $('txt_proveedor').innerHTML = '<br /><br />Proveedor';
      $('combo_proveedor').innerHTML = <?=helpers::superCombo($conn, "SELECT id, nombre AS descripcion FROM proveedores ORDER BY nombre",0,'busca_id_proveedor','busca_id_proveedor', '', '', 'id', 'descripcion', '', '', '', 'Seleccione...', true)?>; 
      $('txt_tipo_proveedor').innerHTML = '<br />Tipo de Proveedor';
      $('combo_tipo_proveedor').innerHTML = "<select name=\"busca_tipoprov\" id=\"busca_tipoprov\"><option value=\"0\">Seleccione</option><option value=\"P\">Proveedor</option><option value=\"C\">Contratista</option><option value=\"A\">Ambos</option><option value=\"S\">Proveedor de Servicios</option><option value=\"B\">Ciudadanos</option></select>";
      $('txt_unidad_ejecutora').innerHTML = '';
      $('combo_unidad_ejecutora').innerHTML = '';
	  $('txt_status').innerHTML = '<br />Status';
	  $('txt_tipo_compromiso').innerHTML = '';
	  $('combo_status').innerHTML = "<select name=\"busca_status\" id=\"busca_status\"><option value=\"0\">Seleccione</option><option value=\"1\">Emitido</option><option value=\"2\">Anulado</option><option value=\"0\">Ambos</option></select>";
	  $('combo_tipo_compromiso').innerHTML = ''; 
      $('txt_fecha_desde').innerHTML = '<br />Desde';
	  $('txt_fecha_hasta').innerHTML = '<br />Hasta';
	  $('fecha').innerHTML = "<table><tr><td><input style=\"width:100px\"  type=\"text\" name=\"busca_fecha_desde\" id=\"busca_fecha_desde\"/></td><td><div id=\"boton_busca_fecha_desde\"><a href=\"#\" onclick=\"return false;\"><img border=\"0\" alt=\"Seleccionar Fecha\" src=\"images/calendarA.png\" width=\"20\" height=\"20\" /></a></div></td><td><input style=\"width:100px\" type=\"text\" name=\"busca_fecha_hasta\" id=\"busca_fecha_hasta\"/></td><td><div id=\"boton_busca_fecha_hasta\"><a href=\"#\" onclick=\"return false;\"><img border=\"0\"  alt=\"Seleccionar Fecha\" src=\"images/calendarA.png\" width=\"20\" height=\"20\" /></a></div></td></tr></table>";
      calendario_desde();
	  calendario_hasta();
 
	}else{
	  $('txt_cta_bancaria').innerHTML = '';
	  $('combo_ctas_bancarias').innerHTML = '';	
      $('txt_proveedor').innerHTML = '';
      $('combo_proveedor').innerHTML = '';
      $('txt_tipo_proveedor').innerHTML = '';
      $('combo_tipo_proveedor').innerHTML = "";
      $('txt_unidad_ejecutora').innerHTML = '';
      $('combo_unidad_ejecutora').innerHTML = ''; 
	  $('txt_status').innerHTML = '';
	  $('txt_tipo_compromiso').innerHTML = '';
	  $('combo_status').innerHTML = "";
	  $('combo_tipo_compromiso').innerHTML = ''; 
      $('txt_fecha_desde').innerHTML = '';
	  $('txt_fecha_hasta').innerHTML = '';
	  $('fecha').innerHTML = '';
    }
  } 
  
Event.observe('tipo', "change", function () { 
	act_codigo($F('tipo')); 
});

/*Event.observe('id_cp', "change", function () {
  alert ($('id_cp'));
  *if ($F('id_cp') != '')
  {
    $('busca_cp').selectedIndex = 0; 
	  desactivar($('busca_cp'));
  }/ 
});*/

/*Event.observe('busca_cp', "change", function () {
  alert ($('busca_cp'));
*  if ($F('busca_cp') != '') 
  {
    $F('id').value = '';
	  desactivar($('id_cp'));
  }* 
});*/

 function calendario_desde(){
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
			inputField        : "busca_fecha_desde",
			button            : "boton_busca_fecha_desde",
			ifFormat          : "%d/%m/%Y",
			daFormat          : "%Y/%m/%d",
			align             : "Br"
			});
 }
 
 function calendario_hasta(){
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
			inputField        : "busca_fecha_hasta",
			button            : "boton_busca_fecha_hasta",
			ifFormat          : "%d/%m/%Y",
			daFormat          : "%Y/%m/%d",
			align             : "Br"
			});
 }

</script>
<? 

	require ("comun/footer.php"); 
?>	

