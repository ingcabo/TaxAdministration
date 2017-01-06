<? 
	require("comun/ini.php"); 
	require ("comun/header.php");
		
?>
<script type="text/javascript" language="javascript">
	function carga_combo_fechas(){
		var url = 'json.php';
		var pars = 'op=cargaMeses&ms=' + new Date().getTime();
		var Request = new Ajax.Request(
			url,{
			method: 'get',
			parameters: pars,
			onLoading:function(request){}, 
			onComplete:function(request){
				
				var jsonData = eval('(' + request.responseText + ')');
					if (jsonData == undefined) { return }
					for(var j=0;j<jsonData.length;j++){
						var option = document.createElement('OPTION');
						option.value = jsonData[j]['id'];
						option.innerHTML = jsonData[j]['descripcion'];
						try 
						{$('mesFinal').add(option, null);}
						catch( e )
						{$('mesFinal').add(option);}
	
					}						
			}
		}
		);	
	}     	   
		
	carga_combo_fechas();
</script>
<span style="text-align:left" class="titulo_maestro">
	Libro de Compras: 
</span>
<center>
	<div align="left" id="formulario">
		<table width="454" border="0" align="center" style=" margin-left: auto; margin-right: auto; font-size:10px; ">
		  <tr>
  			<td width="72">Generar Reporte Hasta:</td>
  			<td width="200">
			<div id="periodo">
  			<select name="mesFinal" id="mesFinal">
  			  <option value="" selected>Seleccione...</option>
  			</select></div></td>
		  </tr>
		  <tr>
		  	<td colspan="2" align="center"><input name="boton" id="boton" type="button" onClick="imprimir()" value="Generar Reporte" /></td>
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
			if($('mesFinal').value==''){
				alert("Debe Seleccionar Una Fecha Para Generar el Archivo");
			} 
			else{
				Popup= window.open("libro_compras.pdf.php?mes="+$('mesFinal').value,"winX","width=500,height=500,scrollbars=yes,resizable=yes,status=yes");
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
      $('combo_cp').innerHTML = <?=helpers::superCombo($conn, "SELECT * FROM categorias_programaticas WHERE id_escenario=$escEnEje",0,'busca_cp','busca_cp', '', 'desactivar(0)', 'id', 'descripcion', '', '', '', 'Seleccione...', true)?>; 
      
    }
    else
    {
      $('txt_cod_cp').innerHTML = '';
      $('cod_cp').innerHTML = '';
      $('txt_combo_cp').innerHTML = '';
      $('combo_cp').innerHTML = '';
    }
  } 
  
</script>
<? 
	require ("comun/footer.php"); 
?>