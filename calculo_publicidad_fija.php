<?
	require ("comun/ini.php");
	// Creando el objeto publicidad
	$oPublicidad = new publicidad;
	$cRelacionPublicidad = new publicidad;
?>

<div>Periodo Desde:<span class="Estilo8">
		    <input name="fec_ini" type="text" id="fec_ini" value="<?=muestrafecha($objeto->fec_ini)?>" size="12" readonly="readonly" />
          </span> <a href="#" id="boton_fechainicio" onclick="return false;"> <img  border="0" src="images/calendarA.png" width="14" height="14" /> </a>
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
						inputField        : "fec_ini",
						button            : "boton_fechainicio",
						ifFormat          : "%d/%m/%Y",
						daFormat          : "%Y/%m/%d",
						align             : "Br"
					 });
				</script>
</div>
		  <div>Periodo Hasta:<span class="Estilo8">
		    <input  name="fec_fin" type="text" id="fec_fin" value="<?=muestrafecha($objeto->fec_fin)?>" size="12" readonly="readonly" />
            <a href="#" id="boton_fechafin" onclick="return false;"><img  border="0" src="images/calendarA.png" width="14" height="14" /> </a>
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
						inputField        : "fec_fin",
						button            : "boton_fechafin",
						ifFormat          : "%d/%m/%Y",
						daFormat          : "%Y/%m/%d",
						align             : "Br"
					 });
		       	</script>
		  </span></div>
          </div>
          <table align="center" border="0" id="tablitaPubFija" width="47%">
            <tr>
              <td colspan="3"><span class="titulo">Agregar Tipo de Publicidad </span></td>
            </tr>
            <tr>
              <td align="left"  ><input name="button" type="button" onclick="Agregar()" value="Agregar" /></td>
              <td align="right"  ><input name="button2" type="button" onclick="Eliminar()" value="Eliminar" /></td>
            </tr>
            <tr>
              <td colspan="3"><div id="gridbox" width="600" height="150" class="gridbox"></div></td>
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
</script>