<?
require ("comun/ini.php");

$oTransferencia = new transferencia;

$accion = $_REQUEST['accion'];
$id = $_POST['id'];

$descrip = trim($_POST['descripcion']); 
$origen = $_POST['tipo_doc'];
$num_doc = trim($_POST['num_doc']);
$status = (trim($_POST['estatus'])=='Registrado')?0:1;
$cedente  =  $_POST['id_cuenta_cedente'];
$receptora =  $_POST['id_cuenta_receptora'];
$monto = guardafloat($_POST['monto']);

if (!empty($_POST['fecha']))
{
	$fecha = guardafecha($_POST['fecha']);
	$mes=date('n',strtotime($fecha));
	$ano=date('Y',strtotime($fecha));
}
else 
{
	$ano=date('Y');
	$mes=date('n');
	$fecha=date('Y-m-d');	
}


if($accion == 'Guardar')
{
	$msj = $oTransferencia->add($conn, $num_doc, $origen, $status, $escEnEje, $descrip, $cedente, $receptora, $fecha, $monto);
	if($msj)
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
}
elseif($accion == 'Actualizar'){
	if($oTransferencia->anular($conn, $id,$num_doc, $origen, $status, $escEnEje, $descrip, $cedente, $receptora, $fecha))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}
elseif($accion == 'del'){
	if($oTransferencia->delete($_REQUEST['id']))
		$msj = REG_DEL_OK;
	else
		$msj = ERROR;
}


require ("comun/header.php");

$msj =  $oTransferencia->msg;
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<br />
<script type="text/javascript">var i=0;</script>
<span class="titulo_maestro">Transferencia entre Cuentas Bancarias</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />
<div>
<fieldset id="buscador">
	<legend>Buscar:</legend>
	<table width="600" border="0">
		<tr>
			<td colspan="2">Cuenta Cedente</td>
		</tr>
		<tr>
			<td colspan="2">
			<?=helpers::superComboSQL($conn, 
											'',
											'', 
											'busca_cuenta_cedente',
											'busca_cuenta_cedente',
											'', 
											'buscador()',
											'id',
											'descripcion',
											false,
											'',
											"SELECT cb.id, (b.descripcion || ' - ' || cb.nro_cuenta)::varchar as descripcion FROM finanzas.cuentas_bancarias as cb INNER JOIN public.banco as b ON cb.id_banco=b.id  order by descripcion",
											'80');?>
			</td>
		</tr>
		<tr>
			<td colspan="2">Cuenta Receptora</td>
		</tr>
		<tr>
			<td colspan="2">
			<?=helpers::superComboSQL($conn, 
											'',
											'', 
											'busca_cuenta_receptora',
											'busca_cuenta_receptora',
											'', 
											'buscador()',
											'id',
											'descripcion',
											false,
											'',
											"SELECT cb.id, (b.descripcion || ' - ' || cb.nro_cuenta)::varchar as descripcion FROM finanzas.cuentas_bancarias as cb INNER JOIN public.banco as b ON cb.id_banco=b.id  order by descripcion",
											'80');?>
			</td>
		</tr>
		<tr>
			<td>Nº Documento</td>
			<td>Tipo de Documento</td>
		</tr>
		<tr>
			<td><input type ="text" name="busca_documento" id="busca_documento"/> </td>
			<td>
				<select name="busca_td" id="busca_td" onchange="buscador()">
					<option value="0">Seleccione</option>
					<option value="CHQ">Cheque</option>
					<option value="TRA">Transferencia</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Desde:</td>
			<td>Hasta:</td>
		<tr>
			<td style="width:150">
				<input style="width:100px"  type="text" name="busca_fecha_desde" id="busca_fecha_desde" onchange="validafecha(this);"/>
				<a href="#" id="boton_busca_fecha_desde" onclick="return false;">
					<img border="0" alt="Seleccionar Fecha" src="images/calendarA.png" width="20" height="20" />
				</a>  
				<script type="text/javascript">
					new Zapatec.Calendar.setup
					({
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
				<input style="width:100px"  type="text" name="busca_fecha_hasta" id="busca_fecha_hasta" onchange="validafecha(this);"/>
				<a href="#" id="boton_busca_fecha_hasta" onclick="return false;">
					<img border="0" alt="Seleccionar Fecha" src="images/calendarA.png" width="20" height="20" />
				</a>  
				<script type="text/javascript">
					new Zapatec.Calendar.setup
					({
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
</fieldset>
<br />
<div style="margin-bottom:10px" id="busqueda"></div>
<br />
<br />
<div style="height:40px;padding-top:10px;">
<p id="cargando" style="display:none;margin-top:0px;">
  <img alt="Cargando" src="images/loading.gif" /> Cargando...
</p>
</div>
<?
//$validator->create_message("error_escenario", "escenario", "*");
$validator->create_message("error_fecha", "fecha", "Este campo no puede estar vacio");
$validator->create_message("error_descripcion", "descripcion", "Este campo no puede estar vacio");
$validator->create_message("error_tipo_doc", "tipo_doc", "Este campo no puede estar vacio");
$validator->create_message("error_num_doc", "num_doc", "Este campo no puede estar vacio");
$validator->create_message("error_cedente", "id_cuenta_cedente", "*");
$validator->create_message("error_receptora", "id_cuenta_receptora", "*");
$validator->print_script();
?>

<script type="text/javascript">
	
	var oldDebe;
	var oldHaber;
	var t;	

	function busca(cc, cr, nrodocumento, tipodocumento,fecha_desde, fecha_hasta, pagina)
	{
		//alert('cc: '+ cc +' td: ' +td+' fecha_desde: '+fecha_desde+ ' fecha_hasta: '+fecha_hasta+' pagina: '+pagina);
		var url = 'updater_busca_transferencia.php';
		var pars = 'cc=' + cc + '&cr=' + cr + '&escenario=' + <?=$escEnEje?> +'&nrodocumento=' + nrodocumento +'&tipodocumento=' + tipodocumento + '&fecha_desde='+fecha_desde+'&fecha_hasta='+fecha_hasta+'&ms='+new Date().getTime()+ '&pagina='+pagina;
		var updater = new Ajax.Updater
		(
			'busqueda', 
			url,
			{
				method: 'get',
				parameters: pars,
				asynchronous:true, 
				evalScripts:true,
				onLoading:function(request){Element.show('cargando')}, 
				onComplete:function(request){Element.hide('cargando')}
			}
		); 
	} 

	function buscador()
	{
		busca($('busca_cuenta_cedente').value, $('busca_cuenta_receptora').value, $('busca_documento').value,$('busca_td').value, $('busca_fecha_desde').value, $('busca_fecha_hasta').value, 1);
	}
	
	Event.observe('busca_cuenta_cedente', "change", function () { 
		busca($F('busca_cuenta_cedente'),
		$F('busca_cuenta_receptora'),
		$F('busca_documento'), 
		$F('busca_td'),
		$F('busca_fecha_desde'), 
		$F('busca_fecha_hasta'), 1); 
	}); 

	Event.observe('busca_cuenta_receptora', "change", function () { 
		busca($F('busca_cuenta_cedente'),
		$F('busca_cuenta_receptora'),
		$F('busca_documento'), 
		$F('busca_td'),
		$F('busca_fecha_desde'), 
		$F('busca_fecha_hasta'), 1);
	}); 

	Event.observe('busca_documento', "change", function () { 
		busca($F('busca_cuenta_cedente'),
		$F('busca_cuenta_receptora'),
		$F('busca_documento'), 
		$F('busca_td'),
		$F('busca_fecha_desde'), 
		$F('busca_fecha_hasta'), 1);
	}); 

	Event.observe('busca_td', "change", function () { 
		busca($F('busca_cuenta_cedente'),
		$F('busca_cuenta_receptora'),
		$F('busca_documento'), 
		$F('busca_td'),
		$F('busca_fecha_desde'), 
		$F('busca_fecha_hasta'), 1);
	});

	function validafecha(fecha)
	{
		var upper = 31;
		if(/^(\d{2})\/(\d{2})\/(\d{4})$/.test(fecha.value)) 
		{ // dd/mm/yyyy
			if(RegExp.$2 == '02')
				upper = 29;
				
			if((RegExp.$1 <= upper) && (RegExp.$2 <= 12)) 
			{
				fecha_desde = new Date();
				fecha_hasta = new Date();
				if ($('busca_fecha_desde').value!='' && $('busca_fecha_hasta').value!='')
				{
					fecha_desde.setDate($('busca_fecha_desde').value.substr(0,2));
					fecha_desde.setMonth(parseInt($('busca_fecha_desde').value.substr(3,2))-1);
					fecha_desde.setFullYear($('busca_fecha_desde').value.substr(6,4));
	
					fecha_hasta.setDate($('busca_fecha_hasta').value.substr(0,2));
					fecha_hasta.setMonth(parseInt($('busca_fecha_hasta').value.substr(3,2))-1);
					fecha_hasta.setFullYear($('busca_fecha_hasta').value.substr(6,4));
					
					if (fecha_desde.getTime() > fecha_hasta.getTime())
					{
						alert("La fecha 'Desde' debe ser menor que la fecha 'Hasta'");
						$(fecha).value = "";
						return false;
					}
				}
				
			busca($F('busca_cuenta_cedente'),
			$F('busca_cuenta_receptora'),
			$F('busca_documento'), 
			$F('busca_td'),
			$F('busca_fecha_desde'), 
			$F('busca_fecha_hasta'), 1);
			}
			else 
			{
				alert("Fecha incorrecta");
				$(fecha).value = "";
			}
		}
		else if(fecha.value != '') 
		{
			alert("Fecha incorrecta");
			$(fecha).value = "";
		}
	}

	function traeReceptora(id){
		var url = 'updater_selects.php';
		var pars = 'combo=cuentasReceptoras&id_cuenta=' + id;
		var updater = new Ajax.Updater('divctareceptora', 
			url,
			{
				method: 'get',
				parameters: pars,
				asynchronous:true, 
				evalScripts:true,
				onLoading:function(request){}, 
				onComplete:function(request){}
			});
	}
	
	function Anular(){
	$(estatus).value=1;
	$(accion).value='Actualizar';
	validate();
	}
	
</script>

<?
require ("comun/footer.php"); 
?>