<?
require ("comun/ini.php");

// Creando el objeto movimientosPresupuestarios
$oMovimientosPresupuestarios = new movimientos_presupuestarios;
$id = $_REQUEST['id'];
if(!empty($id))
	$oMovimientosPresupuestarios->get($conn, $id);
if(!empty($id)){
	$cRelacionMovs=$oMovimientosPresupuestarios->get_relaciones($conn, $oMovimientosPresupuestarios->nrodoc, $escEnEje);
}	
// traigo los tipos de documentos del tipo de movimiento, lo uso para contruir el select "tipdoc"
$cTiposDocs = tipos_documentos::get_all_by_mp($conn, $oMovimientosPresupuestarios->status);
// traigo las categorias de la unidad, lo uso para contruir el select "categorias_programaticas[]"
$cCategorias = categorias_programaticas::get_all_by_ue($conn, $oMovimientosPresupuestarios->id_unidades_ejecutoras, $escEnEje);
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:none;"><?=$msj?></div><? } ?>
<br />
<span class="titulo_maestro">Maestro de Movimientos Presupuestarios</span>
<div id="formulario">

<form name="form1" action="movimientos_presupuestarios.php" method="post">
<table border="0">
<tr>
	<td>Tipo de Movimiento:</td>
	<td colspan="4" class="campoForm">
		<?=$oMovimientosPresupuestarios->momento?>
	</td>
	<td>A&ntilde;o:</td>
	<td class="campoForm">
		<?=$oMovimientosPresupuestarios->ano?>
	</td>
</tr>
<tr>
	<td>Unidad Ejecutora:</td>
	<td colspan="4" class="campoForm">
		<?=$oMovimientosPresupuestarios->unidad_ejecutora?>
	</td>
	<td>C&eacute;dula o Rif:</td>
	<td class="campoForm">
		<?=$oMovimientosPresupuestarios->rif?>
	</td>
</tr>
<tr>
	<td>Descripci&oacute;n:</td>
	<td colspan="6" class="campoForm">
		<?=empty($oMovimientosPresupuestarios->descripcion) ? "&nbsp": $oMovimientosPresupuestarios->descripcion?>
	</td>
</tr>
<tr>
	<td>Tipo Doc.:</td>
	<td class="campoForm">
		<?=$oMovimientosPresupuestarios->tipo_documento?>
	</td>
	<td>N&ordm; Doc.:</td>
	<td class="campoForm"><?=$oMovimientosPresupuestarios->nrodoc?></td>
	<td>Fecha Doc.:</td>
	<td class="campoForm">
		<?=$oMovimientosPresupuestarios->fecha?>
	</td>
	<td></td>
</tr>
<tr>
	<td>Tipo Ref.:</td>
	<td class="campoForm">
		<?=$oMovimientosPresupuestarios->tipref?>
	</td>
	<td>N&ordm; Ref.:</td>
	<td class="campoForm">
		<?=$oMovimientosPresupuestarios->nroref?>
	</td>
	<td>Fecha Ref.:</td>
	<td class="campoForm">
		<?=empty($oMovimientosPresupuestarios->fecharef) ? "&nbsp;" : $oMovimientosPresupuestarios->fecharef ?>
	</td>
	<td><input value="Facturas" name="facturas" type="button" /></td>
</tr>
<tr>
	<td colspan="7">
	<table align="center" border=0 id="tablita">
<? $cCategorias = categorias_programaticas::get_all_by_ue($conn, $oMovimientosPresupuestarios->id_unidad_ejecutora, $escEnEje);
if(is_array($cRelacionMovs)){
	foreach ($cRelacionMovs as $rMov){ 
	$i = 0;
	$cPartidas = partidas_presupuestarias::get_all_by_cp($conn, $rMov->id_categoria_programatica, $escEnEje);
	?>
		<tr>
			<td>Categoria:</td>
			<td class="campoForm">
				<?=$rMov->categoria_programatica?>
			</td>
			<td>Partida Presupuestaria:</td>
			<td class="campoForm">
				<?=$rMov->partida_presupuestaria?>
			</td>
			<td>Monto:</td>
			<td class="campoForm">
				<?=muestrafloat($rMov->monto)?>
			</td>
		</tr>
	<? $i++; } } ?>
	</table>
	</td>
</tr>
<tr>
	<td colspan="7">Por Documento:</td>
</tr>
<tr>
	<td width="20%">Compromiso</td>
	<td width="20%">Causado</td>
	<td width="20%">Pagado</td>
	<td width="20%">Aumentos</td>
	<td width="20%">Disminuciones</td>
</tr>
<tr>
	<td class="campoForm" style="text-align:right"><?=empty($oMovimientosPresupuestarios->compromiso) ? 
													"&nbsp;" : 
													muestrafloat($oMovimientosPresupuestarios->compromiso)?>
	</td>
	<td class="campoForm" style="text-align:right"><?=empty($oMovimientosPresupuestarios->causado) ? 
													"&nbsp;" : 
													muestrafloat($oMovimientosPresupuestarios->causado)?>
	</td>
	<td class="campoForm" style="text-align:right"><?=empty($oMovimientosPresupuestarios->pagado) ? 
													"&nbsp;" : 
													muestrafloat($oMovimientosPresupuestarios->pagado)?>
	</td>
	<td class="campoForm" style="text-align:right"><?=empty($oMovimientosPresupuestarios->aumentos) ? 
													"&nbsp;" : 
													muestrafloat($oMovimientosPresupuestarios->aumentos)?>
	</td>
	<td class="campoForm" style="text-align:right"><?=empty($oMovimientosPresupuestarios->disminuciones) ? 
													"&nbsp;" : 
													muestrafloat($oMovimientosPresupuestarios->disminuciones)?>
	</td>
</tr>
</table>
<span style="position:absolute;top:4px;right:5px;cursor:pointer;">
<img onclick="close_div_movimientos();" src="images/close_div.gif" /></span>
</form>

</div>
<br />
<table>
<tr>
	<td>Desde:</td>
	<td>
	<table>
		<tr>
			<td>
			<input size="12" id="consulta_fecha_desde" onchange="updater_consulta(this.value, $('consulta_fecha_hasta').value, $('consulta_tipos_documentos').value, $('consulta_momentos_presupuestarios').value)" type="text" readonly />
			</td>
			<td>
				<a href="#" id="boton_consulta_fecha_desde" onclick="return false;">
					<img border="0" src="images/calendarA.png" width="20" height="20" />
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
					inputField        : "consulta_fecha_desde",
					button            : "boton_consulta_fecha_desde",
					ifFormat          : "%d/%m/%Y",
					daFormat          : "%Y/%m/%d",
					align             : "Br"
				 });
			</script>
			</td>
		</tr>
		</table>
	</td>
	<td>Hasta:</td>
	<td>
	<table>
		<tr>
			<td>
			<input size="12" id="consulta_fecha_hasta" onchange="updater_consulta($('consulta_fecha_desde').value, this.value, $('consulta_tipos_documentos').value, $('consulta_momentos_presupuestarios').value)" type="text" readonly />
			</td>
			<td>
				<a href="#" id="boton_consulta_fecha_hasta" onclick="return false;">
					<img border="0" src="images/calendarA.png" width="20" height="20" />
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
					inputField        : "consulta_fecha_hasta",
					button            : "boton_consulta_fecha_hasta",
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
	<td>Tipo de Documento:</td>
	<td colspan="3">
			<?=helpers::combo_ue_cp($conn, 
														'momentos_presupuestarios', 
														'',
														'',
														'id',
														'consulta_momentos_presupuestarios',
														'consulta_momentos_presupuestarios',
														"updater_consulta($('consulta_fecha_desde').value, $('consulta_fecha_hasta').value, $('consulta_tipos_documentos').value, this.value)")?>
	</td>
</tr>
<tr>
	<td>Tipo de Movimiento:</td>
	<td colspan="3">
			<?=helpers::combo_ue_cp($conn, 
														'tipos_documentos', 
														'',
														'',
														'id',
														'consulta_tipos_documentos',
														'consulta_tipos_documentos',
														"updater_consulta($('consulta_fecha_desde').value, $('consulta_fecha_hasta').value, this.value, $('consulta_momentos_presupuestarios').value)")?>
	</td>
</tr>
</table>
<br />
<div id="consulta"></div>
<div style="height:40px;padding-top:10px;">
<p id="cargando" style="display:none;margin-top:0px;">
  <img alt="Cargando" src="images/loading.gif" /> Cargando...
</p>
</div>
<script type="text/javascript">
function updater_consulta(fecha_desde, fecha_hasta, tipdoc, tipmov){
	var url = 'updater_busca_movpre.php';
	var pars = 'fecha_desde=' + fecha_desde + '&fecha_hasta=' + fecha_hasta + '&tipdoc=' + tipdoc + '&tipmov=' + tipmov;
	var updater = new Ajax.Updater('consulta', url,{
		parameters: pars,
		asynchronous:true, 
		evalScripts:true,
		onLoading:function(request){Element.show('cargando')}, 
		onComplete:function(request){Element.hide('cargando')}
	}); 
}
function close_div_movimientos(){
	$('formulario').innerHTML = '<a href="movimientos_presupuestarios.php">Agregar Nuevo Registro</a>';
}
</script>
<? require ("comun/footer.php"); ?>