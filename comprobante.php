<?
require ("comun/ini.php");

$oComprobante = new comprobante($conn);

$accion = $_REQUEST['accion'];
$id = $_POST['id'];

$numcom = trim($_POST['numcom']); 
$descrip = trim($_POST['descripcion']); 
$origen = $_POST['tipo_doc'];
$num_doc = trim($_POST['num_doc']);
$status = trim($_POST['estatus']);

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

$json_det =  $_POST['json_det'];

if($accion == 'Guardar')
{
	$msj = $oComprobante->create($escEnEje, $ano, $mes, $numcom, $descrip, $fecha, $origen, substr($status,0,1), $num_doc, $json_det);
	if($msj)
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
}
elseif($accion == 'Actualizar'){
	if($oComprobante->set($escEnEje, $ano, $mes, $numcom, $descrip, $fecha, $origen, substr($status,0,1), $num_doc, $id, $json_det))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}
elseif($accion == 'del'){
	if($oComprobante->delete($_REQUEST['id']))
		$msj = REG_DEL_OK;
	else
		$msj = ERROR;
}
elseif($accion == 'anular')
{
	if ($oComprobante->anular($id))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}


require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<br />
<script type="text/javascript">var i=0;</script>
<span class="titulo_maestro">Movimientos y Transacciones</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />
<div>
<fieldset id="buscador">
	<legend>Buscar:</legend>
	<table width="600" border="0">
		<!--tr>
			<td colspan="2">Escenario</td>
		</tr>
		<tr>
			<td colspan="2">
			<?=helpers::superComboSQL($conn, 
											'',
											0,
											'busca_escenarios',
											'busca_escenarios',
											'',
											'buscador()',
											'id',
											'descripcion',
											false,
											'',
											"SELECT id, descripcion FROM puser.escenarios")?>
			</td>
		</tr-->
		<tr>
			<td colspan="2">Cuenta Contable</td>
		</tr>
		<tr>
			<td colspan="2">
			<?=helpers::superComboSQL($conn, 
											'',
											0,
											'busca_cc',
											'busca_cc', 
											'', 
											'buscador()', 
											'id', 
											'descripcion', 
											false,
											'', 
											"SELECT id, (codcta || ' - ' || descripcion)::varchar AS descripcion FROM contabilidad.plan_cuenta WHERE movim = 'S' ORDER BY codcta",
											'80')?>
			</td>
		</tr>
		<tr>
			<td colspan="2">Tipo de Documento</td>
		</tr>
		<tr>
			<td colspan="2">
				<select name="busca_td" id="busca_td" onchange="buscador()">
					<option value="0">Seleccione</option>
					<option value="OP">Orden de Pago</option>
					<option value="DEP">Dep&oacute;sito</option>
					<option value="CHQ">Cheque</option>
					<option value="CHM">Cheque Manual</option>
					<option value="TRA">Transferencia</option>
					<option value="TRM">Transferencia Manual</option>
					<option value="ND">Nota de Debito</option>
					<option value="NC">Nota de Credito</option>
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

<script type="text/javascript">
	
	var oldDebe;
	var oldHaber;
	var t;	

	function busca(cc, td, fecha_desde, fecha_hasta, pagina)
	{
		//alert('cc: '+ cc +' td: ' +td+' fecha_desde: '+fecha_desde+ ' fecha_hasta: '+fecha_hasta+' pagina: '+pagina);
		var url = 'updater_busca_comprobante.php';
		var pars = 'cc=' + cc + '&escenario=' + <?=$escEnEje?> +'&td=' + td + '&fecha_desde='+fecha_desde+'&fecha_hasta='+fecha_hasta+'&ms='+new Date().getTime()+ '&pagina='+pagina;
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
		busca($('busca_cc').value, $('busca_td').value, $('busca_fecha_desde').value, $('busca_fecha_hasta').value, 1);
	}

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
				
				busca($('busca_cc').value, $('busca_td').value, $('busca_fecha_desde').value, $('busca_fecha_hasta').value, 1);
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

	function doOnCellEdit(stage, rowId, cellInd)
	{ 
		var debe;
		var haber;
		var totalDebe;
		var totalhaber;
		
		if (stage==0  && (cellInd == 2 || cellInd == 3 ))
		{
			var valor = usaFloat(mygrid.cells(rowId,2).getValue());
			oldDebe = valor;
				
			var valor = usaFloat(mygrid.cells(rowId,3).getValue());
			oldHaber = valor;
	
			//alert(oldDebe);
			
			if (cellInd == 2)
				mygrid.cells(rowId,2).setValue(oldDebe.toString());	
			else
				mygrid.cells(rowId,3).setValue(oldHaber.toString());	
		}
		else if(stage==1 && cellInd==4)
		{ //antes
			if(confirm(String.fromCharCode(191) + "Esta seguro que desea eliminar este asiento?"))
			{
				var rowId = mygrid.getSelectedId();
				debe = usaFloat(mygrid.cells(rowId, 2).getValue());
				haber = usaFloat(mygrid.cells(rowId, 3).getValue());
				
				totalDebe = usaFloat($('total_debe').value);
				totalDebe -= debe;
				$('total_debe').value = muestraFloat(totalDebe);
				if ($('total_debe').value == '0')
					$('total_debe').value = '0,00';
				
				totalHaber = usaFloat($('total_haber').value);
				totalHaber -= haber;
				$('total_haber').value = muestraFloat(totalHaber);
				if ($('total_haber').value == '0')
					$('total_haber').value = '0,00';
	
				mygrid.deleteSelectedItem(); //eliminar la fila seleccionada
				mygrid.clearSelection();
			}
		}
		else if(stage==2 && (cellInd == 2 || cellInd == 3 ))
		{
			debe = usaFloat(mygrid.cells(rowId, 2).getValue());
			haber = usaFloat(mygrid.cells(rowId, 3).getValue());
			//alert('debe: '+ debe +' haber: '+ haber);
			if (isNaN(debe))
				debe = 0;
	
			if (debe == 0)
				mygrid.cells(rowId, 2).setValue('0,00');
			else
				mygrid.cells(rowId, 2).setValue(muestraFloat(debe.toString()));
			
			if(isNaN(haber))
				haber = 0;
			
			if (haber == 0)
				mygrid.cells(rowId, 3).setValue('0,00');
			else
				mygrid.cells(rowId, 3).setValue(muestraFloat(haber.toString()));
	
			// Valida que el debe y el haber no puedan ser distintos de cero al mismo tiempo
			if (mygrid.cells(rowId,2).getValue()!='0,00' && mygrid.cells(rowId,3).getValue()!='0,00')
			{
				alert("Los valores de Debe y Haber no pueden contener valores distintos de cero al mismo tiempo.");
				mygrid.cells(rowId,cellInd).setValue('0,00');
				return false;
				
				if (cellInd == 3)
					mygrid.cells(rowId,2).setValue('0,00');
				else
					mygrid.cells(rowId,3).setValue('0,00');
			}
	
			var auxDebe = usaFloat($('total_debe').value);
			totalDebe = auxDebe.toFixed(2);
			if (isNaN(totalDebe))
				totalDebe = 0;

			totalHaber = usaFloat($('total_haber').value);
			if (isNaN(totalHaber))
				totalHaber = 0;
			
			totalDebe -= parseFloat(oldDebe.toFixed(2));
			totalDebe += parseFloat(debe.toFixed(2));
			
			totalHaber -= parseFloat(oldHaber.toFixed(2));
			totalHaber += parseFloat(haber.toFixed(2));
			
			$('total_debe').value = muestraFloat(totalDebe.toFixed(2));
			if ($('total_debe').value == '0')
				$('total_debe').value = '0,00';
				
			$('total_haber').value = muestraFloat(totalHaber.toFixed(2));
			if ($('total_haber').value == '0')
				$('total_haber').value = '0,00';
		}
	}

	function Agregar()
	{
		if ($('cuenta').value=='')
		{
			alert("Por favor, escriba el codigo de la cuenta.");
		}
		else
		{
			if ($('descripcion_cuenta').value == '')
			{
				var url = 'json.php';
				var pars = 'op=buscar_plan_cta&codcta='+$('cuenta').value+'&ms='+new Date().getTime();
				var Request = new Ajax.Request
				(
					url,
					{
						method: 'get',
						parameters: pars,
						onLoading:function(request){}, 
						onComplete:function(request)
						{
							var jsonData = eval('('+request.responseText+')');
							if (jsonData.id)
							{
								if (jsonData.movim!='S')
								{
									alert("Esa cuenta no es de movimiento");
									var esta = true
								}
								else
									var esta = false;
									
								for (j=0; j<mygrid.getRowsNum() && !esta; j++)
								{
									if (jsonData.codcta == mygrid.cells(mygrid.getRowId(j), 0).getValue())
									{
										alert("Esa cuenta ya ha sido agregada");
										esta = true;
									}
								}
								
								if (!esta)
								{
									mygrid.addRow(i,jsonData.codcta+";"+jsonData.descripcion+";0,00;0,00;images/delete.gif");
									i++;
								}
							}
							else
								alert("No existe una cuenta con ese codigo");
						}
					}
				);
			}
			else
			{
				esta = false;
				for (j=0; j<mygrid.getRowsNum() && !esta; j++)
				{
					if ($('cuenta').value == mygrid.cells(mygrid.getRowId(j), 0).getValue())
					{
						alert("Esa cuenta ya ha sido agregada");
						esta = true;
					}
				}
				
				if (!esta)
				{
					mygrid.addRow(i,$('cuenta').value+";"+$('descripcion_cuenta').value+";0,00;0,00;images/delete.gif");
					i++;
				}
			}
			
			$('cuenta').value = '';
		}
	}
	
	function mostrar_ventana(){
	 
		var url = 'buscar_cuentas_contables.php';
		var pars = 'movim=S&ms='+new Date().getTime();
		var Request = new Ajax.Request(
			url,
			{
				method: 'get',
				parameters: pars,
				onLoading:function(request){}, 
				onComplete:function(request){
					
					Dialog.alert(request.responseText, {windowParameters: {width:600, height:400, 
									showEffect:Element.show,hideEffect:Element.hide,
									showEffectOptions: { duration: 1}, hideEffectOptions: { duration:1 }
									
									}});
					
					}
				}
		);     	   
	}
	
	function busca_popup()
	{
		clearTimeout(t);
		t = setTimeout('buscaPlanCtas()', 800);
	}
	
	function buscaPlanCtas()
	{
		var url = 'buscar_cuentas_contables.php';
		var pars = 'movim=S&op=2&descripcion='+$('search_descrip').value+'&codigo='+$('search_codigo').value+'&ms='+new Date().getTime();
		var updater = new Ajax.Updater('divPlanCtas', 
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

	function selDocumento(codcta, descripcion)
	{
		$('cuenta').value = codcta;
		$('descripcion_cuenta').value = descripcion;
		Dialog.okCallback();
		Agregar();
	
	}
	
	function GuardarDet()
	{
		if ($('total_debe').value != $('total_haber').value)
		{
			alert("Los montos del debe y el haber no coinciden");
			return false;
		}
		
		var JsonAux = new Array;
		var lon = mygrid.getRowsNum();
		
		if (lon == 0) 
			return false;

		for(j=0; j<lon; j++)
		{
			rowId = mygrid.getRowId(j);
			JsonAux.push({id_cta:mygrid.cells(rowId,0).getValue(), descrip:mygrid.cells(rowId,1).getValue(), debe:usaFloat(mygrid.cells(rowId,2).getValue()), haber:usaFloat(mygrid.cells(rowId,3).getValue()), id_esc:<?=$escEnEje?>});
		}

		$('json_det').value = JsonAux.toJSONString();
		validate();
	}
	
	function Anular()
	{
		if (confirm(String.fromCharCode(191)+"Esta seguro que desea anular este asiento?"))
		{
			$('accion').value = 'anular';
			$('form1').submit();
		}
	}
	
	function actualizarNumCom(fecha, numcom)
	{
		var upper = 31;
		var hoy = new Date();
		var dia = parseInt(hoy.getDate());
		var mes = parseInt(hoy.getMonth())+1;
		var anio = hoy.getFullYear();
		
		if (dia < 10)
			dia = '0'+dia;
		if (mes < 10)
			mes = '0'+mes;
		
		if(/^(\d{2})\/(\d{2})\/(\d{4})$/.test(fecha.value)) 
		{ // dd/mm/yyyy
			if(RegExp.$2 == '02')
				upper = 29;
				
			if((RegExp.$1 > upper) || (RegExp.$2 > 12)) 
				fecha.value = dia + '/' + mes + '/' + anio;
		}
		else
			fecha.value = dia + '/' + mes + '/' + anio;

		$('numcom').value = fecha.value.substr(6,4) + '-' + fecha.value.substr(3,2) + '-' + numcom.substr(8,4);
		$(fecha).value = fecha.value;
	}

</script>

<?
//$validator->create_message("error_escenario", "escenario", "*");
$validator->create_message("error_fecha", "fecha", "*");
$validator->create_message("error_descripcion", "descripcion", "*");
$validator->create_message("error_tipo_doc", "tipo_doc", "*");
$validator->create_message("error_num_doc", "num_doc", "*");
$validator->print_script();

require ("comun/footer.php"); 
?>