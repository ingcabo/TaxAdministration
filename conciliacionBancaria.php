<?
require ("comun/ini.php");
// Creando el objeto Proveedores
$oConciliacion = new conciliacionBancaria;
//die($_REQUEST['accion']);
$accion = $_REQUEST['accion'];
$json_det = $_REQUEST['json_det'];

if($accion == 'Guardar')
{
	$msj = $oConciliacion->add($conn, $_REQUEST['nro_cuenta'], $_REQUEST['fecha_desde'], $_REQUEST['fecha_hasta'], $_REQUEST['fecha_conc'], $_REQUEST['descripcion'], guardafloat($_REQUEST['saldo_ini_ban']), guardafloat($_REQUEST['saldo_fin_ban']), guardafloat($_REQUEST['saldo_ini_lib']), guardafloat($_REQUEST['saldo_fin_lib']), guardafloat($_REQUEST['saldo_conc']), guardafloat($_REQUEST['saldo_trans']), $json_det);
	if($msj===true)
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
}
elseif($accion == 'Actualizar')
{
	$msj = $oConciliacion->set($conn, $_REQUEST['id'], $_REQUEST['nro_cuenta'], $_REQUEST['fecha_desde'], $_REQUEST['fecha_hasta'], $_REQUEST['fecha_conc'], $_REQUEST['descripcion'], guardafloat($_REQUEST['saldo_ini_ban']), guardafloat($_REQUEST['saldo_fin_ban']), guardafloat($_REQUEST['saldo_ini_lib']), guardafloat($_REQUEST['saldo_fin_lib']), guardafloat($_REQUEST['saldo_conc']), guardafloat($_REQUEST['saldo_trans']));
	if ($msj === true)
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}
elseif($accion == 'del')
{
	$msj = $oConciliacion->del($conn, $_REQUEST['id']);
	if($msj===true)
		$msj = REG_DEL_OK;
	else
		$msj = ERROR;
}



require ("comun/header.php");
?>
<div id="msj" <?=empty($msj) ? 'style="display:none"':''?>><?=$msj?></div><br />
<br />
<span class="titulo_maestro">Conciliaci&oacute;n Bancaria</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />
<br />
<div>
<fieldset id="buscador">
	<legend>Buscar:</legend>
	<table width="350px">
		<tr>
			<td>Cuenta Bancaria</td>
		</tr>
		<tr>
			<td>
			<?=helpers::superCombo($conn,
											"SELECT A.id, (CASE WHEN B.descripcion is null THEN A.nro_cuenta ELSE A.nro_cuenta||' - '||B.descripcion END)  AS descripcion FROM finanzas.cuentas_bancarias AS A LEFT JOIN puser.clasificacion_cuenta AS B ON A.id_clasificacion_cuenta=B.id ORDER BY A.nro_cuenta::text",
											'', 
											'cta_banc', 
											'cta_banc',
											'',
											'buscador()',
											'id',
											'descripcion',
											'',
											'',
											'');
			?>
			</td>
		</tr>
		<tr>
			<td>Desde:</td>
			<td>Hasta:</td>
		<tr>
			<td style="width:150">
				<input style="width:100px"  type="text" name="busca_fecha_desde" id="busca_fecha_desde" onchange="validarRangoFechas(this);"/>
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
				<input style="width:100px"  type="text" name="busca_fecha_hasta" id="busca_fecha_hasta" onchange="validarRangoFechas(this);"/>
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
</div>
<br />
<div style="margin-bottom:10px" id="busqueda"><div>
<br />
<br />
<div style="height:40px;padding-top:10px;">
<p id="cargando" style="display:none;margin-top:0px;">
  <img alt="Cargando" src="images/loading.gif" /> Cargando...
</p>
</div>

<script type="text/javascript">
	
	function validarRangoFechas(fecha)
	{
		var valido = validafecha(fecha);
		if (valido == 0)
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
					valido = -2;
			}

			if (valido==0)
			{
				busca($('cta_banc').value, $('busca_fecha_desde').value, $('busca_fecha_hasta').value, 1);
				return true;
			}
		}

		if (valido == -1)
		{
			alert("Fecha Incorrecta");
			$(fecha).value = "";
			return false;
		}
		else if (valido == -2)
		{
			alert("Rango de fechas invalido");
			$(fecha).value = "";
			return false;
		}
	}
	
	function validafecha(fecha)
	{
		var upper = 31;
		if(/^(\d{2})\/(\d{2})\/(\d{4})$/.test(fecha.value)) 
		{ // dd/mm/yyyy
			if(RegExp.$2 == '02')
			{
				upper = 28;
				if (!(RegExp.$3%4) && ((RegExp.$3%100) || !(RegExp.$3%400)))
					upper = 29;
			}
				
			if((RegExp.$1 > upper) || (RegExp.$2 > 12)) 
				return -1;
		}
		else if(fecha.value != '') 
			return -1;
			
		return 0;
	}

	function buscador()
	{
		busca($('cta_banc').value, $('busca_fecha_desde').value, $('busca_fecha_hasta').value, 1);
	}
	
	function busca(id_cta, fecha_desde, fecha_hasta, pagina)
	{
		var url = 'updater_busca_conciliacion.php';
		var pars = '&id_cuenta=' + id_cta + '&fecha_desde=' + fecha_desde+ '&fecha_hasta=' + fecha_hasta +'&ms='+new Date().getTime()+ '&pagina='+pagina;
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

	//FUNCION QUE TRAE LAS CUENTAS BANCARIAS AL MOMENTO DE SELECCIONAR UN BANCOS//
	function traeCuentasBancarias(id_banco, div, id_cuenta, disabled)
	{
		var url = 'updater_selects.php';
		var pars = 'combo=cuentas_bancarias&id_banco=' + id_banco + '&id_cuenta=' + id_cuenta +'&onchange=llamarSaldo(this.value)&disabled='+disabled+'&ms='+new Date().getTime();
		var updater = new Ajax.Updater(div, 
			url,
			{
				method: 'get',
				parameters: pars,
				asynchronous:true, 
				evalScripts:true,
				onLoading:function(request){if (!id_cuenta)Element.show('cargando_cuentas')}, 
				onComplete:function(request)
				{
					Element.hide('cargando_cuentas');
					$('fecha_desde').value = '';
					$('fecha_hasta').value = '';
					$('saldo_ini_ban').value = '0,00';
					$('saldo_fin_ban').value = '0,00';
					$('saldo_ini_lib').value = '0,00';
					$('saldo_fin_lib').value = '0,00';
					$('saldo_conc').value = '0,00';
					$('saldo_trans').value = '0,00';
					mygrid.clearAll();
					mygrid.clearSelection();
					Element.hide('msj');
				}
			}); 
	}
	
	function doOnCheck(rowId, value, stage)
	{
		var monto = usaFloat(mygrid.cells(rowId, 6).getValue());
		if (isNaN(monto))
			monto = 0;
		var trans = usaFloat($('saldo_trans').value);
		if (isNaN(trans))
			trans = 0;
		var conc = usaFloat($('saldo_conc').value);
		if (isNaN(conc))
			conc = 0;

		if (value)
		{
			trans -= monto;
			conc += monto;
		}
		else
		{
			trans += monto;
			conc -= monto;
		}

		$('saldo_trans').value = muestraFloat(trans);
		$('saldo_conc').value = muestraFloat(conc);
	}
	
	function actualizarSaldoConc(fld)
	{
		var saldoBanco = usaFloat(fld.value);
		if (isNaN(saldoBanco))
			saldobanco = 0;
		//Esto lo voy a colocar en 0 porque aqui no deberia sumar todavia los movimientos en transito
		//var saldoTrans = usaFloat($('saldo_trans').value);
		var saldoTrans = 0;
		
		$('saldo_conc').value = muestraFloat(saldoBanco + saldoTrans);
	}
	
	function llamarSaldo(value)
	{
		if (value==0 || value=='0')
			return false;
		
		var url = 'json.php';
		var pars = 'op=traeFechasConc&id_cta_banc='+value+'&ms='+new Date().getTime;
		var Request = new Ajax.Request(
			url,
			{
				method: 'get',
				parameters: pars,
				onLoading: function(request){Element.show('cargando_cta');},
				onComplete: function(request)
				{
					jsonData = eval('(' + request.responseText + ')');
					$('fecha_desde').value = jsonData[0];
					$('fecha_hasta').value = jsonData[1];
					$('id_cta_cont').value = jsonData[2].plan_cuenta;
					$('saldo_ini_lib').value = muestraFloat(jsonData[3]);
					var fecha_hasta = new Date();
					var hoy = new Date();
					
					fecha_hasta.setDate($('fecha_hasta').value.substr(0,2));
					fecha_hasta.setMonth(parseInt($('fecha_hasta').value.substr(3,2)) - 1);
					fecha_hasta.setFullYear($('fecha_hasta').value.substr(6,4));
					
					if (fecha_hasta.getTime() > hoy.getTime())
					{
						$('msj').innerHTML = "No puede conciliar un mes que no ha culminado";
						Element.show('msj');
						Element.hide('cargando_cta');
						$('boton').disabled = true;
						return false;
					}
					else
						$('boton').disabled = false;

					traerSaldo($('fecha_desde').value, 'D');
					traerSaldo($('fecha_hasta').value, 'H');
				}
			}
		);
	}
	
	function traerSaldo(fecha, op)
	{
		if ($('nro_cuenta').value==0 || $('nro_cuenta').value=='0')
			return false;
			
		var url = 'json.php';
		var pars = 'op=saldoConc&tipo='+op+'&fecha='+fecha+'&id_cta_banc='+$('nro_cuenta').value+'&ms='+new Date().getTime();
		var Request = new Ajax.Request(
			url,
			{
				method: 'get',
				parameters: pars,
				onLoading:function(request){/*Element.show('cargando');*/}, 
				onComplete:function(request)
				{
//					Element.hide('cargando');
					if (op=='D')
					{
						//$('saldo_ini_lib').value = muestraFloat(request.responseText);
						//$('saldo_conc').value = $('saldo_ini_lib').value;
					}
					else if (op=='H')
					{
						$('saldo_fin_lib').value = muestraFloat(request.responseText);
						cargarAsientos(fecha);
					}
				}
			}
		);     	   
	}
	
	function cargarAsientos(fecha)
	{
		var total = 0;
		var url = 'json.php';
		var pars = 'op=asientosConc&fecha='+fecha+'&id_cta_banc='+$('nro_cuenta').value+'&orden=fecha, origen&opcion=1&ms='+new Date().getTime();
		var Request = new Ajax.Request(
			url,
			{
				method: 'get',
				parameters: pars,
				onLoading:function(request){/*Element.show('cargando');*/},
				onComplete:function(request)
				{
					mygrid.clearAll();
					mygrid.clearSelection();
					
					var jsonData = eval('(' + request.responseText + ')');
					if (jsonData.length > 0)
					{
						for (i=0; i<jsonData.length; i++)
						{
							monto = 0;
							for (j=0; j<jsonData[i].det.length && monto==0; j++)
								if (jsonData[i].det[j].id_cta == $('id_cta_cont').value)
									monto = parseFloat(jsonData[i].det[j].debe) - parseFloat(jsonData[i].det[j].haber);

							total += parseFloat(monto);
							mygrid.addRow(jsonData[i].id, '0;'+jsonData[i].origen+';'+jsonData[i].num_doc+';'+((jsonData[i].beneficiario) ? jsonData[i].beneficiario.nombre:'')+';'+jsonData[i].descrip+';'+jsonData[i].fecha+';'+muestraFloat(monto));
						}
	
						$('saldo_trans').value = muestraFloat(total);
						Element.hide('msj');
						$('boton').disabled = false;
					}
					else
					{
						$('msj').innerHTML = "No existen documentos sin conciliar para esa cuenta";
						Element.show('msj');
					}

					Element.hide('cargando_cta');
				}
			}
		);		
	}
	
	function guardarAsientos()
	{
		if (usaFloat($('saldo_ini_ban').value)==0 || usaFloat($('saldo_fin_ban').value)==0)
		{
			if ($('saldo_ini_ban').value=='' || usaFloat($('saldo_ini_ban').value)==0)
				$('error_saldo_ini_ban').innerHTML = '*';
			else
				$('error_saldo_ini_ban').innerHTML = '';

			if ($('saldo_fin_ban').value=='' || usaFloat($('saldo_fin_ban').value)==0)
				$('error_saldo_fin_ban').innerHTML = '*';
			else
				$('error_saldo_fin_ban').innerHTML = '';
	
			return false;
		}
		
		var det = [];
		for (i=0; i<mygrid.getRowsNum(); i++)
		{
			var rowId = mygrid.getRowId(i);
			var status = (mygrid.cells(rowId, 0).isChecked()) ? 'C':'T';
			
			det.push({status:status, id:rowId});
		}
		
		$('json_det').value = det.toJSONString();
		validate();
	}
	
</script>

<?
$validator->create_message("error_bancos", "bancos", "*");
$validator->create_message("error_nro_cuenta", "nro_cuenta", "*");
$validator->create_message("error_fecha_conc", "fecha_conc", "*");
$validator->create_message("error_saldo_ini_ban", "saldo_ini_ban", "*");
$validator->create_message("error_saldo_fin_ban", "saldo_fin_ban", "*");
$validator->print_script();
?>
<? require ("comun/footer.php"); ?>