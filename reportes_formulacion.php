<? 
	require("comun/ini.php"); 
	require ("comun/header.php");
?>
<span style="text-align:left" class="titulo_maestro">
	Generar Reportes de Formulacion
</span>
<center>
	<div align="left" id="formulario">
		<table width="454" border="0" align="center" style=" margin-left: auto; margin-right: auto; font-size:10px; ">
		  <tr>
  			<td width="72">Escenario:</td>
  			<td width="194"><?=helpers::superCombo($conn, "SELECT id, descripcion FROM puser.escenarios WHERE id <> '$escEnEje'",0,'busca_escenario','busca_escenario', '', 'traeUnidadesEjecutoras(this.value)', 'id', 'descripcion', '', '', '', 'Seleccione...', true)?></td>
  			<td width="174" align="center"><input name="button" type="button" onclick="imprimir()" value="Generar Reporte" /></td>
		  </tr>
		  <tr>
		  	<td width="72">Unidad Ejecutora:</td>
  			<td width="194"><div id="divcomboescenario">
			  		<select>
						<option>Seleccione...</option>
					</select>
				</div>
			</td>
		  </tr>
		  <tr>
		  	<td width="72">Codigo de Formulaci&oacute;n:</td>
  			<td width="194"><div id="divcombocodigo"><select name="Formulacion" id="Formulacion">
						<option>Seleccione...</option>
					</select>
				</div>
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
			if($('Formulacion').value==0 || $('Formulacion').value== 'Seleccione...'){
				alert("Debe seleccionar un codigo de Formulacion");
				return false;
			}else
			{
				wxR = window.open("reporte_formulacion.pdf.php?id_formulacion=" + $('Formulacion').value, "winX", "width=500, height=500, scrollbars=yes, resizable=yes, status=yes");
				wxR.focus()
			}
	}
	
	function traeUnidadesEjecutoras(id_escenario){
		var url = 'updater_selects.php';
		var pars = 'combo=id_unidades_reportes&escenario=' + id_escenario;
		var updater = new Ajax.Updater('divcomboescenario', 
		url,
		{
			method: 'post',
			parameters: pars,
			asynchronous:true, 
			evalScripts:true,
			onComplete:function(request){}
		}
		);
	}
	
	function traeCodigoFormulacion(id_unidad, id_escenario){
		var url = 'updater_selects.php';
		var pars = 'combo=id_formulacion&unidad=' + id_unidad +'&escenario=' + id_escenario ;
		var updater = new Ajax.Updater('divcombocodigo', 
		url,
		{
			method: 'post',
			parameters: pars,
			asynchronous:true, 
			evalScripts:true,
			onComplete:function(request){}
		}
		);
	}

</script>
<? 
	require ("comun/footer.php"); 
?>	