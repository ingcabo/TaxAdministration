<?php

require ("comun/ini.php"); 
require ("comun/header.php"); ?>
<br />
<span class="titulo_maestro">Listar Proveedores</span>
<div id="formulario">
	<table width="200" border="0" >
    	<tr >
			<td >Status:</td>
			<td colspan="3">
				<SELECT name="status" id="status" >
                	<option value=""> Todos </option>
                    <option value="A" id="A"> Activos </option>
                    <option value="I" id="I"> Inactivos </option>
                </SELECT>
            </td>
        </tr>  
		<tr>
			<td align="center" colspan="2">
                <br />
                <input type="submit" name="btn_imprimir" onClick="Imprimir()" value="Imprimir Reporte">
            </td>
        </tr>
	</table>
</div>

<div style="height:40px;padding-top:10px;">
	<p id="Procesando" style="display:none;margin-top:0px;">
		<img alt="Cargando" src="images/loading.gif" /> Procesando...
	</p>
</div>

<script language="javascript"  type="text/javascript"> 
var wx;
function Imprimir(){
var JsonAux;
	if (!wx || wx.closed) { 
			wx = window.open("lista_proveedores_pdf.php?status="+$('status').options[$('status').selectedIndex].value,"winX","width=500,height=500,scrollbars=yes,resizable=yes,status=yes");
			wx.focus()
		} 
} 
</script>
<? require ("comun/footer.php"); ?>