<?
require ("comun/ini.php");
// Creando el objeto Proveedores
$oConciliacion = new conciliacionBancaria2;
//die($_REQUEST['accion']);
$accion = $_REQUEST['accion'];
$json_det = $_REQUEST['json_det'];
//die($json_det);

if($accion == 'Guardar')
{
	$msj = $oConciliacion->add($conn, $_REQUEST['nro_cuenta'], guardafecha($_REQUEST['fecha_desde']), guardafecha($_REQUEST['fecha_hasta']), guardafecha($_REQUEST['fecha_conc']), $_REQUEST['descripcion'], guardafloat($_REQUEST['saldo_ini_ban']), guardafloat($_REQUEST['saldo_fin_ban']), guardafloat($_REQUEST['saldo_ini_lib']), guardafloat($_REQUEST['saldo_fin_lib']), guardafloat($_REQUEST['saldo_conc_ban']), 
guardafloat($_REQUEST['saldo_conc_lib']), guardafloat($_REQUEST['saldo_trans_ban']), guardafloat($_REQUEST['saldo_trans_lib']),  $json_det,  $_REQUEST['num_mes'], $_REQUEST['cta_contable']);
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
<a href="#" onClick="updater(0); return false;">Agregar Nuevo Registro</a>
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
			<td >
				<input style="width:100px"  type="text" name="busca_fecha_desde" id="busca_fecha_desde" onChange="validarRangoFechas(this);"/>
				<a href="#" id="boton_busca_fecha_desde" onClick="return false;">
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
				<input style="width:100px"  type="text" name="busca_fecha_hasta" id="busca_fecha_hasta" onChange="validarRangoFechas(this);"/>
				<a href="#" id="boton_busca_fecha_hasta" onClick="return false;">
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
	
	var t;
	var saldoConciliadoLibro =0;
	var saldoConciliadoBanco =0;
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
		var pars = 'combo=cuentas_bancarias&id_banco=' + id_banco + '&id_cuenta=' + id_cuenta +'&onchange=llamarMes(this.value,nroMes)&disabled='+disabled+'&ms='+new Date().getTime();
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
					$('saldo_conc_ban').value = '0,00';
					$('saldo_conc_lib').value = '0,00';
					$('saldo_trans_lib').value = '0,00';
					$('saldo_trans_ban').value = '0,00';
					$('cta_contable').value = '';
					mygrid.clearAll();
					mygrid.clearSelection();
					Element.hide('msj');
				}
			}); 
	}
	
	function llamarMes(id_cuenta, div)
	{
		var url = 'updater_selects.php';
		var pars = 'combo=mesConciliar&id_cuenta=' + id_cuenta +'&ms='+new Date().getTime();
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
					$('saldo_conc_ban').value = '0,00';
					$('saldo_conc_lib').value = '0,00';
					$('saldo_trans_lib').value = '0,00';
					$('saldo_trans_ban').value = '0,00';
					$('cta_contable').value = '';
					mygrid.clearAll();
					mygrid.clearSelection();
					Element.hide('msj');
				}
			}); 
			
					
	}
	
	function buscaConciliacion(idEdoCta, id_cuenta)
	{
		
		var url = 'json.php';
		var pars = 'op=datosEdoCta&idEdoCta='+idEdoCta+'&ms='+new Date().getTime();
		var Request = new Ajax.Request(
			url,
			{
				method: 'get',
				parameters: pars,
				asynchronous:true, 
				evalScripts:true,
				onLoading:function(request){/*Element.show('cargando');*/},
				onComplete:function(request)
				{
					
					//alert(request.responseText);
					var jsonData = eval('(' + request.responseText + ')');
					$('fecha_desde').value = jsonData[0];
					$('fecha_hasta').value = jsonData[1];
					$('saldo_ini_ban').value = muestraFloat(jsonData[2]);
					$('saldo_fin_ban').value = muestraFloat(jsonData[3]);
					saldoFinBanco = jsonData[3];
					//$('idCtaContable').value = jsonData[4]
					getSaldoIni(id_cuenta);
				}
			}
		);
		
		var total = 0;
		var origen;
		var url = 'json.php';
		var pars = 'op=partidasAconciliar&edoCta='+idEdoCta+'&ctaBan='+id_cuenta+'&ms='+new Date().getTime();
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
						//var saldoConciliado = 0
						for (i=0; i<jsonData.length; i++)
						{
							//saldoConciliado = saldoConciliado + parseFloat(jsonData[i]['monto']);
							if(jsonData[i]['tipo_doc']=='DEP')
								origen='Deposito';
							if(jsonData[i]['tipo_doc']=='CHQ')
								origen='Cheque';
							if(jsonData[i]['tipo_doc']=='CHM')
								origen='Cheque por transferencia';
							if(jsonData[i]['tipo_doc']=='TRA')
								origen='Pago Electronico';
							if(jsonData[i]['tipo_doc']=='TRM')
								origen='Transferencia';
							if(jsonData[i]['tipo_doc']=='TFT')								
								origen='Fondos a Terceros';
							if(jsonData[i]['tipo_doc']=='ND')
								origen='Nota de Debito';
							if(jsonData[i]['tipo_doc']=='NC')
								origen='Nota de Credito';
							mygrid.addRow(jsonData[i]['idRel'], origen+';'+jsonData[i]['num_doc']+';'+jsonData[i]['fecha_doc']+';'+muestraFloat(jsonData[i]['monto'])+';'+jsonData[i]['numcom']+';'+jsonData[i]['fecha_libro']);
						}
						//$('saldo_trans').value = muestraFloat(saldoConciliado);
					}
					Element.hide('cargando_cta');
				}
			}
		);		
	
		clearTimeout(t); 
		t = setTimeout("buscaPartEdoCta("+idEdoCta+","+id_cuenta+")",15000);
		t = setTimeout("buscaPartLibro("+idEdoCta+","+id_cuenta+")",10000);	
	}
	
	function guardaConciliacion(elemento){
		if (parseFloat($('saldo_conc_lib').value) == parseFloat($('saldo_conc_ban').value)){	
			var JsonAux,conc=new Array;
			mygrid.clearSelection()
			for(j=0;j<mygrid.getRowsNum();j++)
			{
				if(!isNaN(mygrid.getRowId(j)))
				{
					conc[j] = new Array;
					conc[j][0]= mygrid.cells(mygrid.getRowId(j),0).getValue();
					conc[j][1]= mygrid.cells(mygrid.getRowId(j),1).getValue();
					conc[j][2]= mygrid.cells(mygrid.getRowId(j),2).getValue();
					conc[j][3]= mygrid.cells(mygrid.getRowId(j),3).getValue();
					conc[j][4]= mygrid.cells(mygrid.getRowId(j),4).getValue();
					conc[j][5]= mygrid.cells(mygrid.getRowId(j),5).getValue();
					conc[j][6]= mygrid.getRowId(j);		
				}
			}
			JsonAux={"conc":conc};
			$("json_det").value=JsonAux.toJSONString();
			//actapr(elemento);
			validate();
		}else{
			alert('El mes no se logra conciliar, por favor verifique los saldos');
			return false;
		}
	} 
	
	/*function actapr(elemento){
		if(elemento.value == 'Guardar')
			$('accion').value = 'Guardar';
		if(elemento.value == 'Actualizar')
			$('accion').value = 'Actualizar';
		if(elemento.value == 'Aprobar')
			$('accion').value = 'Aprobar';
		if(elemento.value == 'Anular')
			$('accion').value = 'Anular'; 
		 validate(); 
	}*/
	
	function getSaldoIni(id_cuenta){
		var saldoFin = 0;
		var url = 'json.php';
			var pars = 'op=saldoMes&idCta='+id_cuenta+'&fechaIni='+$('fecha_desde').value+'&fechaFin='+$('fecha_hasta').value+'&ms='+new Date().getTime();
			var Request = new Ajax.Request(
				url,
				{
					method: 'get',
					parameters: pars,
					asynchronous:true, 
					evalScripts:true,
					onLoading:function(request){},
					onComplete:function(request)
					{
						//alert(request.responseText);
						var saldoIni = 0;
						var jsonData = eval('(' + request.responseText + ')');
						if((jsonData['saldoAnterior']>0) && (jsonData['saldoAnterior'] != null)){
							saldoIni = parseFloat(jsonData['saldoAnterior']);
						}else
							saldoIni = parseFloat(jsonData['saldoInicial']);
						saldoFin = saldoIni + parseFloat(jsonData['debe']) - parseFloat(jsonData['haber']);
						$('saldo_ini_lib').value = muestraFloat(saldoIni);
						$('saldo_fin_lib').value = muestraFloat(saldoFin);
						$('cta_contable').value = jsonData['codCuenta'];  
					}
				}
			);
	}
	
	function buscaPartEdoCta(idEdoCta, idCta){
		var saldoFinLibro = usaFloat($('saldo_fin_lib').value);
		saldoConciliadoLibro = saldoFinLibro;
		var partBanco = 0;
		var debe = 0; //Debito
		var haber = 0; //Credito
		var url = 'json.php';
		var pars = 'op=buscaPartEdoCtanoLibro&idEdoCta='+idEdoCta+'&idCta='+idCta+'&fecha='+$('fecha_desde').value+'&ms='+new Date().getTime();
		var Request = new Ajax.Request(
			url,
			{
				method: 'get',
				parameters: pars,
				onLoading:function(request){/*Element.show('cargando');*/},
				onComplete:function(request)
				{
					var jsonData = eval('(' + request.responseText + ')');
					if (jsonData.length > 0)
					{
						for (i=0; i<jsonData.length; i++)
						{
							haber += parseFloat(jsonData[i]['creditos']);
							debe += parseFloat(jsonData[i]['debitos']);
						}
						$('saldo_trans_lib').value = muestraFloat(haber-debe);
						saldoConciliadoLibro = saldoConciliadoLibro + (haber - debe);
					}
				}
			}
		);
		t = setTimeout("$('saldo_conc_lib').value = muestraFloat(saldoConciliadoLibro.toFixed(2))", 5000); 
	}
	
	function buscaPartLibro(idEdoCta, idCta){
		var debe = 0; //Credito
		var haber = 0; //Debito
		var saldoFinBanco = usaFloat($('saldo_fin_ban').value);
		saldoConciliadoBanco = saldoFinBanco;
		var url = 'json.php';
		var pars = 'op=buscaPartLibronoEdoCta&idEdoCta='+idEdoCta+'&idCta='+idCta+'&fecha='+$('fecha_hasta').value+'&fechaIni='+$('fecha_desde').value+'&ms='+new Date().getTime();
		var Request = new Ajax.Request(
			url,
			{
				method: 'get',
				parameters: pars,
				onLoading:function(request){/*Element.show('cargando');*/},
				onComplete:function(request)
				{
					var jsonData = eval('(' + request.responseText + ')');
					if (jsonData.length > 0)
					{
						for (i=0; i<jsonData.length; i++)
						{
							haber += parseFloat(jsonData[i]['debitos']);
							debe += parseFloat(jsonData[i]['creditos']);
						}
						$('saldo_trans_ban').value = muestraFloat(debe - haber);
						saldoConciliadoBanco = saldoConciliadoBanco + (debe - haber);
					}
				}
			}
		);
	//clearTimeout(t); 
	t = setTimeout("$('saldo_conc_ban').value = muestraFloat(saldoConciliadoBanco.toFixed(2))", 5000); 
	}
	
var wx;
	function reportePreConciliacion(idEdoCta,IniBook,FinBook){
	if($('num_mes').options[$('num_mes').selectedIndex].value==0 ){ 
		alert("Debe seleccionar un Mes");
	}else{
		if (!wx || wx.closed) { 
			wx = window.open("reportePreConciliacion.pdf.php?idEdoCta="+idEdoCta+"&saldoIniBook="+IniBook+"&saldoFinBook="+FinBook,"winX","width=500,height=500,scrollbars=yes,resizable=yes,status=yes");
			wx.focus()
		} else { 
			wx.focus()
		} 
	}		
	}
	
</script>

<?
$validator->create_message("error_bancos", "bancos", "*");
$validator->create_message("error_nro_cuenta", "nro_cuenta", "*");
$validator->create_message("error_fecha_conc", "fecha_conc", "*");
$validator->create_message("error_num_mes", "num_mes", "*");
$validator->print_script();
?>
<? require ("comun/footer.php"); ?>