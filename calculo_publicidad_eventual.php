<?
	require ("comun/ini.php");
	// Creando el objeto publicidad
	$oPublicidad = new publicidad;
	$cRelacionPublicidad = new publicidad;
?>
<script>var mygrid,i=0</script>
<table align="center" border="0" id="tablitaPubFija" width="47%">
  <tr>
    <td colspan="3"><span class="titulo">Agregar Tipo de Publicidad </span></td>
  </tr>
  <tr>
    <td align="left"  ><input name="button" type="button" onclick="Agregar()" value="Agregar" /></td>
    <td align="right"  ><input name="button2" type="button" onclick="Eliminar()" value="Eliminar" /></td>
  </tr>
  <tr>
    <td colspan="3"><div id="gridbox" width="502" height="150" class="gridbox"></div></td>
  </tr>
</table>
<script type="text/javascript">

buildGrid();
function buildGrid(){
	//set grid parameters
	mygrid = new dhtmlXGridObject('gridbox');
	mygrid.selMultiRows = true;
	mygrid.setImagePath("js/Grid/imgs/");
	mygrid.setHeader("Tipo Entrada,Cantidad,Valor,Aforo");
	mygrid.setInitWidths("200,100,100,100");
	mygrid.setColAlign("center,left,left,center");
	mygrid.setColTypes("coro,ed,ed,ed");
	mygrid.setColSorting("str,int,int,int");
	mygrid.setColumnColor("yellow,white,white,white");
	mygrid.rowsBufferOutSize = 0;
	mygrid.setMultiLine(false);
	mygrid.selmultirows="true";
	mygrid.init();
} 
function Agregar(){
	mygrid.addRow(i,",,,",i);
	i++;
}
function Eliminar(){
	mygrid.deleteRow(mygrid.getSelectedId());
}

</script>
  