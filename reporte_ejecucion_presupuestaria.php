<? 
require ("comun/ini.php");
require ("comun/header.php");
?>
<br />
<span class="titulo_maestro">Generar Reporte</span>
<div id="formulario">
	<table width="400" border="0" >
		<tr>
			<td>Tipo de reporte</td>
			<td colspan="3">
				<select name="tipo" id="tipo">
					<option value="0">Seleccione</option>
					<option value="1">General</option>
					<option value="2">Detallado</option>
				</select>
			</td>
		</tr>
		<!--<tr >
			<td width="100" >Escenario:</td>
			<td colspan="3"><?=helpers::combo_ue_cp($conn, 'escenarios','','','id','busca_escenarios','busca_escenarios')?></td>
		</tr>-->
		<tr >
			<td width="100" >Unidades Ejecutoras:</td>
			<td colspan="3">
				<?=helpers::combo_ue_cp($conn,'busca_ue','','','','','','traeCatProDesdeUpdater(this.value)',
			"SELECT DISTINCT id, id || ' - ' || descripcion AS descripcion FROM unidades_ejecutoras WHERE id_escenario = '1111' ORDER BY id")?>
			</td>
		</tr>
		<tr>
			<td>Categorias Program&aacute;ticas</td>
			<td colspan="3">
				<span id="cont_categorias">
					<select name="catPro" id="catPro">
						<option value="0">Seleccione</option>
					</select>
				</span>
			</td>
		</tr>
		<tr>
			<td >Partida inicial</td>
			<td>
				<span id="cont_pinicial">
					<select name="pinicial" id="pinicial">
						<option value="0">Seleccione</option>
					</select>
				</span>
			</td>
			<td >Partida final</td>
			<td>
				<span id="cont_pfinal">
					<select name="pfinal" id="pfinal">
						<option value="0">Seleccione</option>
					</select>
				</span>
			</td>
		</tr>
<tr>
	<td>Fecha desde:</td>
	<td>
	<table>
		<tr>
			<td>
			<input size="12" id="busca_fecha_desde" onchange="validafecha(this);" type="text"  />
			</td>
			<td>
				<a href="#" id="boton_busca_fecha_desde" onclick="return false;">
					<img border="0" src="images/calendarA.png" alt="Seleccione una Fecha" width="20" height="20" />
				</a>  
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
					inputField        : "busca_fecha_desde",
					button            : "boton_busca_fecha_desde",
					ifFormat          : "%d/%m/%Y",
					daFormat          : "%Y/%m/%d",
					align             : "Br"
				 });
			</script>
			</td>
		</tr>
		</table>
	</td>
	<td>Fecha hasta:</td>
	<td>
	<table>
		<tr>
			<td>
			<input size="12" id="busca_fecha_hasta" onchange="validafecha(this);" type="text"  />
			</td>
			<td>
				<a href="#" id="boton_busca_fecha_hasta" onclick="return false;">
					<img border="0" src="images/calendarA.png" alt="Seleccione una Fecha" width="20" height="20" />
				</a>  
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
					inputField        : "busca_fecha_hasta",
					button            : "boton_busca_fecha_hasta",
					ifFormat          : "%d/%m/%Y",
					daFormat          : "%Y/%m/%d",
					align             : "Br"
				 });
			</script>
			</td>
		</tr>
		</table>
	</td>
</tr>


		<tr>
			<td align="right" colspan="4"><br /><input  type="button"  value="Generar Reporte" onClick="imprimir()" ></td>
		</tr>
</table>
</div>

<div style="height:40px;padding-top:10px;">
	<p id="Procesando" style="display:none;margin-top:0px;">
		<img alt="Cargando" src="images/loading.gif" /> Cargando...
	</p>
</div>

<script language="javascript"  type="text/javascript"> 
var wxR;
function imprimir(){
	var JsonAux;
	var dir;
	if($('tipo').value == 0)
		alert('Debe seleccionar el tipo del reporte');
	else if($('busca_ue').value=='0')
		alert("Debe Seleccionar una Unidad Ejecutora");
	/*else if($('busca_escenarios').value=='0')
		alert("Debe Seleccionar un Escenario");*/
	else if($('pinicial').value > $('pfinal').value)
		alert('La partida inicial debe ser menor a la partida final');
	else if($('pinicial').value == 0 && $('pfinal').value != 0)
		alert('Debe seleccionar la partida inicial');
	else{
		var pars = "fecha_desde=" + $('busca_fecha_desde').value + "&fecha_hasta=" + $('busca_fecha_hasta').value;
		pars += "&id_ue="+$('busca_ue').value + "&pinicial=" + $('pinicial').value + "&pfinal=" + $('pfinal').value;
		pars += "&esc=1111&categoria=" + $('catPro').value;
		if($('tipo').value == 1)
			dir = "ejecucion_presupuestaria.pdf.php";
		else if($('tipo').value == 2)
			dir = "ejecucion_presupuestaria_detallado.pdf.php";
		var dirCompleta = dir + "?" + pars;
		wxR = window.open(dirCompleta,"winX","width=500,height=500,scrollbars=yes,resizable=yes,status=yes");
		wxR.focus()
	}
} 

function validafecha(fecha){
	var upper = 31;
	if(/^(\d{2})\/(\d{2})\/(\d{4})$/.test(fecha.value)) { // dd/mm/yyyy
		if(RegExp.$2 == '02') upper = 29;
		if((RegExp.$1 <= upper) && (RegExp.$2 <= 12)) {
		} else {
			alert("Fecha incorrecta");
			fecha.value = "";
		}
	}else if(fecha.value != '') {
		alert("Fecha incorrecta");
		fecha.value = "";
	}
}

function traeCatProDesdeUpdater(idUe){
	var url = 'updater_selects.php';
	var pars = 'combo=catProPorUnidad&esc=1111&ue=' + $('busca_ue').value;
	var updater = new Ajax.Updater('cont_categorias', 
		url,
		{
			method: 'get',
			parameters: pars,
			asynchronous:true, 
			evalScripts:true,
			onLoading:function(request){Element.show('cargando')}, 
			onComplete:function(request){Element.hide('cargando')}
		}); 
}

function traeParPre(idElem, catPro){
	var url = 'updater_selects.php';
	var pars = 'combo=parPreByCatPro&esc=1111&cp=' + $('catPro').value + '&npartida=' + idElem;
	var updater = new Ajax.Updater('cont_' + idElem, 
		url,
		{
			method: 'get',
			parameters: pars,
			asynchronous:true, 
			evalScripts:true,
			onLoading:function(request){Element.show('cargando')}, 
			onComplete:function(request){Element.hide('cargando')}
		}); 
} 
</script>
<? require ("comun/footer.php"); ?>
