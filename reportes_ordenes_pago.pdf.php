<? 
	require("comun/ini.php"); 
	require ("comun/header.php");
?>
<span style="text-align:left" class="titulo_maestro">
	Reportes Ordenes de Compra 
</span>
<center>
	<div align="left" id="formulario">
		<table width="454" border="0" align="center" style=" margin-left: auto; margin-right: auto; font-size:10px; ">
		  <tr>
  			<td width="72">Generar Reporte:</td>
  			<td width="194">
  			<select name="tipo" id="tipo">
  			  <option value="" selected>Seleccione...</option>
  			  <option value="op1">Reporte Ordenes de Pago</option>
  			  <!--<option value="pc">Partidas por Categorias</option>-->
  			</select></td>
  			<td width="174" align="center"><input name="button" type="button" onclick="imprimir()" value="Generar Reporte" /></td>
		  </tr>
		</table>
		<table width="454" border="0" align="center" style=" margin-left: auto; margin-right: auto; font-size:10px; ">
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
		    <td id="txt_fecha_desde"></td><td id="txt_fecha_hasta"></td> 
		  </tr>
		  <tr>
		    <td> <div id="fecha_desde"></div> </td><td><div id="fecha_hasta"></div></td>
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
			  if ($('id_cp').value == '' && $('busca_cp').value == 0)
			     alert("Debe Introducir un Codigo o Seleccionar una Categoria Programatica");
			  else if ($('id_cp').value == '')
        {
  				wxR = window.open("reporte_presupuestario.pdf.php?id_cp=" + $('busca_cp').value, "winX", "width=500, height=500, scrollbars=yes, resizable=yes, status=yes");
  				wxR.focus()
				}
				else
				{
  				wxR = window.open("reporte_presupuestario.pdf.php?id_cp=" + $('id_cp').value, "winX", "width=500, height=500, scrollbars=yes, resizable=yes, status=yes");
  				wxR.focus()
        }
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
    if (tipo == 'op1')
    {
      $('txt_proveedor').innerHTML = '<br /><br />Proveedor';
      $('combo_proveedor').innerHTML = <?=helpers::superCombo($conn, "SELECT id, rif||' - '||nombre AS descripcion FROM proveedores ORDER BY rif",0,'busca_id_proveedor','busca_id_proveedor', '', '', 'id', 'descripcion', '', '', '', 'Seleccione...', true)?>; 
      $('txt_tipo_proveedor').innerHTML = '<br /><br />Tipo de Proveedor';
      $('combo_tipo_proveedor').innerHTML = "<select name:\"busca_tipoprov\" id:\"busca_tipo_prov\"><option value:\"0\">Seleccione</option><option value:\"P\">Proveedor</option><option value:\"C\">Contratista</option><option value:\"A\">Ambos</option><option value:\"S\">Proveedor de Servicios</option><option value:\"B\">Ciudadanos</option></select>";
      $('txt_unidad_ejecutora').innerHTML = '<br /><br />Unidad Ejecutora';
      $('combo_unidad_ejecutora').innerHTML = <?=helpers::superCombo($conn, "SELECT id, id||' - '||descripcion AS descripcion FROM unidades_ejecutoras WHERE id_escenario='$escEnEje' ORDER BY id",0,'busca_id_ue','busca_id_ue', '', '', 'id', 'descripcion', '', '', '', 'Seleccione...', true)?>; 
	  $('txt_status').innerHTML = '<br /><br />Status';
	  $('txt_tipo_compromiso').innerHTML = '<br /><br />Tipo de Compromiso';
	  $('combo_status').innerHTML = "<select name:\"busca_status\" id:\"busca_status\"><option value:\"0\">Seleccione</option><option value:\"1\">Registrada</option><option value:\"2\">Aprobada</option><option value:\"3\">Anulada</option>";
	  $('combo_tipo_compromiso').innerHTML = <?=helpers::superCombo($conn, "SELECT id, descripcion FROM tipos_documentos WHERE id_momento_presupuestario = '1' ORDER BY descripcion",0,'busca_tipocom','busca_tipocom', '', '', 'id', 'descripcion', '', '', '', 'Seleccione...', true)?>; 
    }
    else
    {
      $('txt_cod_cp').innerHTML = '';
      $('cod_cp').innerHTML = '';
      $('txt_combo_cp').innerHTML = '';
      $('combo_cp').innerHTML = '';
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

</script>
<? 

	require ("comun/footer.php"); 
?>	

