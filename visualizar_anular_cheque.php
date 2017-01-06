<?
require ("comun/ini.php");
$oCheque = new cheque;
$accion = $_REQUEST['accion'];
	
	#ACCION DE GUARDAR EL CHEQUE#
	if($accion == 'Anular'){
	
		$oCheque->anular($conn,$_REQUEST['nrodoc'], $escEnEje);
		
	}
require ("comun/header.php");
?>
<span class="titulo_maestro">Visualuzar y Anular Cheques</span>
<div id="formulario">

</div>
<br />

<br />
<div id="contenidobuscador">
<fieldset id="buscador">
	<legend>Buscar:</legend>
	<table>
		<tr>
			<td colspan="3">Banco</td>
		</tr>
		<tr>
			<td colspan="3"><?=helpers::combo_ue_cp($conn,'busca_bancos','','','','','','',
			"SELECT id, descripcion FROM public.banco")?></td>
		</tr>
		<tr>
			<td>Proveedores</td>
			<td>Cuentas Bancarias</td>
		</tr>
		<tr>
			<td>
			<?=helpers::combo_ue_cp($conn, 'busca_proveedores','','','','','','',
			"SELECT id, rif||' - '||nombre AS descripcion FROM proveedores")?></td>
			<td><?=helpers::combo_ue_cp($conn,'busca_cuentas','','','','','','',
			"SELECT id, nro_cuenta as descripcion FROM finanzas.cuentas_bancarias")?></td>
		</tr>
		<tr>
			<td>N&ordm; de Documento</td>
			<td colspan="2">
				<table>
					<tr>
						<td style="width:125px">Desde</td>
						<td>Hasta</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td><input style="width:100px" type="text" name="busca_nrodoc" id="busca_nrodoc" /></td>
			<td colspan="2">
				<table>
					<tr>
						<td>
							<input style="width:100px"  type="text" name="busca_fecha_desde" id="busca_fecha_desde" 
							onchange="validafecha(this);"/>
						</td>
						<td>
							<a href="#" id="boton_busca_fecha_desde" onclick="return false;">
								<img border="0" alt="Seleccionar Fecha" src="images/calendarA.png" width="20" height="20" />
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
						
						<td>
							<input style="width:100px" type="text" name="busca_fecha_hasta" id="busca_fecha_hasta"
							onchange="validafecha(this); "/>
						</td>
						<td>
							<a href="#" id="boton_busca_fecha_hasta" onclick="return false;">
								<img border="0"  alt="Seleccionar Fecha" src="images/calendarA.png" width="20" height="20" />
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
	</table>
</fieldset>
</div>
<br />
<div style="margin-bottom:10px" id="busqueda"></div>
<br />
<div style="height:40px;padding-top:10px;">
<p id="cargando" style="display:none;margin-top:0px;">
  <img alt="Cargando" src="images/loading.gif" /> Cargando...
</p>
</div>
<script type="text/javascript">
function busca(id_proveedor, fecha_desde, fecha_hasta, nrodoc, id_banco, id_cuenta){
	var url = 'updater_busca_cheque.php';
	var pars = 'id_proveedor=' + id_proveedor;
	pars += '&nrodoc=' + nrodoc + '&fecha_desde=' + fecha_desde+ '&fecha_hasta=' + fecha_hasta+ '&id_banco=' + id_banco+ '&id_cuenta=' + id_cuenta+'&ms='+new Date().getTime();
	//alert(pars);
	var updater = new Ajax.Updater('busqueda', 
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

Event.observe('busca_proveedores', "change", function () { 
	busca( 
	$F('busca_proveedores'), 
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta'),
	$F('busca_nrodoc'),
	$F('busca_bancos'),
	$F('busca_cuentas')); 
});

Event.observe('busca_bancos', "change", function () { 
	busca( 
	$F('busca_proveedores'), 
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta'),
	$F('busca_nrodoc'),
	$F('busca_bancos'),
	$F('busca_cuentas')); 
});

Event.observe('busca_cuentas', "change", function () { 
	busca( 
	$F('busca_proveedores'), 
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta'),
	$F('busca_nrodoc'),
	$F('busca_bancos'),
	$F('busca_cuentas')); 
});

Event.observe('busca_fecha_desde', "change", function () { 
	busca( 
	$F('busca_proveedores'), 
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta'),
	$F('busca_nrodoc'),
	$F('busca_bancos'),
	$F('busca_cuentas')); 
});

Event.observe('busca_fecha_hasta', "change", function () { 
	busca( 
	$F('busca_proveedores'), 
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta'),
	$F('busca_nrodoc'),
	$F('busca_bancos'),
	$F('busca_cuentas')); 
});


Event.observe('busca_nrodoc', "keyup", function () { 
	busca( 
	$F('busca_proveedores'), 
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta'),
	$F('busca_nrodoc'),
	$F('busca_bancos'),
	$F('busca_cuentas')); 
});

function close_div2(){
	$('formulario').innerHTML = '';
	
}

function sumaTotalCheque(){
	var total = 0;
	for(var j=0; j<mygrid.getRowsNum(); j++){
	
		total = total + usaFloat(mygrid.cells(mygrid.getRowId(j),1).getValue());
		
	}
	
	$('monto').value = muestraFloat(total);
	
	
}
</script>
<? require ("comun/footer.php");?>