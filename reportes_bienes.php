<? 
	require("comun/ini.php"); 
	require ("comun/header.php");
?>
<span style="text-align:left" class="titulo_maestro">
	Reportes de Bienes 
</span>
<center>
	<div align="left" id="formulario">
		<table width="454" border="0" align="center" style=" margin-left: auto; margin-right: auto; font-size:10px; ">
		  <tr>
  			<td width="72">Generar Reporte:</td>
  			<td width="194">
  			<select name="tipo" id="tipo">
  			  <option value="" selected>Seleccione...</option>
  			  <option value="1">Cuadro de Bienes Adquiridos</option>
			  <option value="2">Inventario de Bienes Inmuebles</option>
  			  <!--<option value="pc">Partidas por Categorias</option>-->
  			</select></td>
  			<td width="174" align="center"><input name="button" type="button" onClick="imprimir()" value="Generar Reporte" /></td>
		  </tr>
		</table>
		<table width="454" border="0" align="center" style=" margin-left: auto; margin-right: auto; font-size:10px; ">
		  <tr>
		    <td id="txt_unidad_ejecutora" colspan="2"></td> 
		  </tr>
		  <tr>
		    <td colspan="2"> <div id="combo_unidad_ejecutora"></div> </td>
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
			else if($('tipo').value == '1')
			{
				wxR = window.open("cuadro_bienes.pdf.php", "winX", "width=500, height=500, scrollbars=yes, resizable=yes, status=yes");
				wxR.focus()
			}
			else if($('tipo').value == '2')
			{
				if($('busca_id_ue').value == '0'){
					alert('Debe seleccionar una Unidad Ejecutora para poder emitir el reporte');
					$('busca_id_ue').focus;	
					return false;				
				} else {
					wxR = window.open("inventario_bienes.pdf.php?fecha_desde=" + $('busca_fecha_desde').value + "&fecha_hasta=" + $('busca_fecha_hasta').value + "&id_unidad_ejecutora=" + $('busca_id_ue').value, "winX", "width=500, height=500, scrollbars=yes, resizable=yes, status=yes");
				  	wxR.focus();
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
    if (tipo == '2')
    {
      $('txt_unidad_ejecutora').innerHTML = '<br />Unidad Ejecutora';
      $('combo_unidad_ejecutora').innerHTML = <?=helpers::superCombo($conn, "SELECT id, id||' - '||descripcion AS descripcion FROM unidades_ejecutoras WHERE id_escenario='$escEnEje' ORDER BY id",0,'busca_id_ue','busca_id_ue', '', '', 'id', 'descripcion', '', '', '', 'Seleccione...', true)?>; 
	  $('txt_fecha_desde').innerHTML = '<br />Desde';
	  $('txt_fecha_hasta').innerHTML = '<br />Hasta';
	  $('fecha').innerHTML = "<table><tr><td><input style=\"width:100px\"  type=\"text\" name=\"busca_fecha_desde\" id=\"busca_fecha_desde\"/></td><td><div id=\"boton_busca_fecha_desde\"><a href=\"#\" onclick=\"return false;\"><img border=\"0\" alt=\"Seleccionar Fecha\" src=\"images/calendarA.png\" width=\"20\" height=\"20\" /></a></div></td><td><input style=\"width:100px\" type=\"text\" name=\"busca_fecha_hasta\" id=\"busca_fecha_hasta\"/></td><td><div id=\"boton_busca_fecha_hasta\"><a href=\"#\" onclick=\"return false;\"><img border=\"0\"  alt=\"Seleccionar Fecha\" src=\"images/calendarA.png\" width=\"20\" height=\"20\" /></a></div></td></tr></table>";
      calendario_desde();
	  calendario_hasta();
 
	}else{
      $('txt_unidad_ejecutora').innerHTML = '';
      $('combo_unidad_ejecutora').innerHTML = ''; 
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

