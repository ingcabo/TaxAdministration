<? 
	require("comun/ini.php"); 
	require ("comun/header.php");
?>
<span style="text-align:left" class="titulo_maestro">
	Generar Reportes de Gastos Presupuestarios 
</span>
<center>
	<div align="left" id="formulario">
		<table width="454" border="0" align="center" style=" margin-left: auto; margin-right: auto; font-size:10px; ">
		  <tr>
  			<td width="72">Generar Reporte:</td>
  			<td width="194">
  			<select name="tipo" id="tipo">
  			  <option value="" selected>Seleccione...</option>
  			  <!--<option value="gral">General</option>-->
  			  <option value="pc">Partidas por Categorias</option>
  			</select></td>
  			<td width="174" align="center"><input name="button" type="button" onclick="imprimir()" value="Generar Reporte" /></td>
		  </tr>
		  <tr>
		  	<td width="72">Seleccione Escenario:</td>
  			<td width="194"><?=helpers::superCombo($conn, "SELECT id, descripcion FROM puser.escenarios ORDER BY id",0,'busca_escenario','busca_escenario', '', '', 'id', 'descripcion', '', '', '', 'Seleccione...', true)?>
  				
			</td>
		  </tr>
		</table>
		<table width="454" border="0" align="center" style=" margin-left: auto; margin-right: auto; font-size:10px; ">
		  <tr>
		    <td id="txt_cod_cp"></td> <td id="txt_combo_cp"></td>
		  </tr>
		  <tr>
		    <td> <div id="cod_cp"></div> </td> <td> <div id="combo_cp"></div></td>
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
			if($('tipo').value==''){
				alert("Debe Seleccionar un Tipo de Reporte a Generar");
				return false;	
			}else if($('busca_escenario').value==0){
				alert("Debe seleccionar un escenario para la busqueda");
				return false;
			}else if($('tipo').value == 'gral')
			{
				wxR = window.open("partidas_sectores.pdf.php?id_escenario=" + $('busca_escenario').value, "winX", "width=500, height=500, scrollbars=yes, resizable=yes, status=yes");
				wxR.focus()
			}
			else if($('tipo').value == 'pc')
			{
			  if ($('id_cp').value == '' && $('busca_cp').value == 0)
			     alert("Debe Introducir un Codigo o Seleccionar una Categoria Programatica");
			  else if ($('id_cp').value == '')
        {
  				wxR = window.open("reporte_presupuestario_gastos.pdf.php?id_cp=" + $('busca_cp').value + "&id_escenario=" + $('busca_escenario').value, "winX", "width=500, height=500, scrollbars=yes, resizable=yes, status=yes");
  				wxR.focus()
				}
				else
				{
  				wxR = window.open("reporte_presupuestario_gastos.pdf.php?id_cp=" + $('id_cp').value + "&id_escenario=" + $('busca_escenario').value, "winX", "width=500, height=500, scrollbars=yes, resizable=yes, status=yes");
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
    if (tipo == 'pc')
    {
      $('txt_cod_cp').innerHTML = '<br /><br />C&oacute;digo';
      $('cod_cp').innerHTML = "<input style:\"width:80px;\" name=\"id_cp\" id=\"id_cp\" maxlength=\"10\" onkeyup=\"desactivar(1);\" />";
      $('txt_combo_cp').innerHTML = '<br /><br />Categor&iacute;a Program&aacute;tica';
      $('combo_cp').innerHTML = <?=helpers::superCombo($conn, "SELECT id, id||' - '||descripcion AS descripcion FROM categorias_programaticas WHERE id_escenario='1111' ORDER BY id",0,'busca_cp','busca_cp', '', 'desactivar(0)', 'id', 'descripcion', '', '', '', 'Seleccione...', true)?>; 
      
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
