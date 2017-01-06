<? 
	require("comun/ini.php"); 
	require ("comun/header.php");
?>
<span style="text-align:left" class="titulo_maestro">
	Generar Reporte Indice de Categorias Programaticas 
</span>
<center>
	<div align="left" id="formulario">
		<table width="454" border="0" align="center" style=" margin-left: auto; margin-right: auto; font-size:10px; ">
		  <tr>
		  	<td width="72">Seleccione Escenario:</td>
  			<td width="194"><?=helpers::superCombo($conn, "SELECT id, descripcion FROM puser.escenarios ORDER BY id",0,'busca_escenario','busca_escenario', '', '', 'id', 'descripcion', '', '', '', 'Seleccione...', true)?>
  			</td>
		  </tr>
		  <tr>
		  	<td colspan="2" align="center"><input name="button" type="button" onclick="imprimir()" value="Generar Reporte" /></td>
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
			if($('busca_escenario').value==0){
				alert("Debe seleccionar un escenario para la busqueda");
				return false;
			}else
			{
				wxR = window.open("reporte_indice_cp.pdf.php?id_escenario=" + $('busca_escenario').value, "winX", "width=500, height=500, scrollbars=yes, resizable=yes, status=yes");
				wxR.focus()
			}
			
	}
  
  

</script>
<? 
	require ("comun/footer.php"); 
?>	
