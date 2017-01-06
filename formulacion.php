<?
	require ("comun/ini.php");

	$oformulacion = new formulacion;

	$accion = $_REQUEST['accion'];

	if($accion == 'Guardar')
	{//die($_REQUEST['gastos_personal']);
		if($oformulacion->add($conn, 
								$_REQUEST['id_ue'], 
								$_REQUEST['status'], 
								$_REQUEST['escenario'], 
								$_REQUEST['anio'], 
								$_REQUEST['organismo'], 
								$_REQUEST['desc_ue'], 
								$_REQUEST['objetivo'],
								$_REQUEST['metas'],
								$_REQUEST['gastos_personal'],
								$_REQUEST['mat_suminis'],
								$_REQUEST['serv_no_personal'],
								$_REQUEST['act_reales'],
								$_REQUEST['otros'],
								$_REQUEST['observacion'],
								$_REQUEST['cant_metas'],
								$_REQUEST['id_tab']
								))
			$msj = REG_ADD_OK;
		else
			$msj = ERROR;
	}
	elseif($accion == 'Actualizar')
	{
		if($oformulacion->set($conn, 
								$_REQUEST['id_formulacion'], 
								$_REQUEST['objetivo'],
								$_REQUEST['status'], 
								$_REQUEST['metas'],
								$_REQUEST['id_tab'],
								$_REQUEST['gastos_personal'],
								$_REQUEST['mat_suminis'],
								$_REQUEST['serv_no_personal'],
								$_REQUEST['act_reales'],
								$_REQUEST['otros'],
								$_REQUEST['observacion']
								))
			$msj = REG_SET_OK;
		else
			$msj = ERROR;
	}
	elseif($accion == 'del')
	{
		if($oformulacion->del($conn, $_REQUEST['id_form']))
			$msj = REG_DEL_OK;
		else
			$msj = ERROR;
	}

	$cformulacion = $oformulacion->get_all($conn, $start_record,$page_size,$_POST['busca_ue']);
	require ("comun/header.php");

	if(!empty($msj))
	{ 
?>
		<div id="msj">
			<?=$msj?>
		</div>
<? 
		echo "<br>"; 
	} 
?>
	<br />
	<script type="text/javascript">
		var mygridMetas,mygridGastosDePersonal,mygridMaterialesSuministros,mygridServiciosNoPersonales,mygridActivosReales,mygridOtros,mygridObservaciones,i=0
	</script>
	<span class="titulo_maestro">
		Formulaci&oacute;n de Metas
	</span>
	<div id="formulario"><? if(!empty( $_REQUEST['id_formulacion']) && $_REQUEST['status'] != '2'){ 
		$oFormulacion = new formulacion;
		$oformulacion->get($conn,$_REQUEST['id_formulacion']);
		if($oformulacion->id_tab != 'tab7'){ ?>
					<script type="text/javascript">
						setTimeout("updater('<?=$_REQUEST['id_formulacion']?>');",100);
					</script>
		<? } else {?>
		<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
		<? }
		}
		else {
		?>
		<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a> 
		<? 
		} ?>
	</div>
	<br />
<div>
<fieldset id="buscador">
	<legend>Buscar:</legend>
<table align="center" border="0" style="margin-left:10px" width="605px">
	<tr>
		<td width="95px"></td>
		<td align="center">
			<table align="center" border="0" style="margin-left:10px" width="605px">
				<tr>
					<td>Formulaci&oacute;n:</td>
					<td>				<?=helpers::combo_ue_cp($conn, 
										'escenarios', 
										'',
										'',
										'',
										'busca_escenario',
										'busca_escenario',
										'',
										"SELECT id, descripcion FROM puser.escenarios WHERE id <> '$escEnEje'",
										'',
										'',
										'')



				?>
					</td>
				</tr>
				<tr>



					<td>Unidad Ejecutora:</td>
					<td><?=helpers::superCombo($conn, "SELECT id, (id || ' - ' || descripcion)::varchar AS descripcion FROM unidades_ejecutoras WHERE  
												id_escenario=$escEnEje ORDER BY id",0,
												'busca_ue','busca_ue', '', '', 'id', 'descripcion',
												'', '', '', 'Seleccione...', true)?>
					</td>


				</tr>
			</table>
























		</td>	







	</tr>




</table>
</fieldset>
</div>








	<br />
	<div style="height:40px;padding-top:10px;">
		<p id="cargando" style="display:none;margin-top:0px;">
  			<img alt="Cargando" src="images/loading.gif" /> Cargando...
		</p>
	</div>
	<br/>
	<div style="margin-bottom:10px" id="busqueda"><div>
<?
	$validator->create_message("error_cod_ue", "id_ue", "*");
	$validator->create_message("error_status", "status", "*");
	$validator->create_message("error_escenario", "escenario", "*");
	$validator->create_message("error_objetivo", "objetivo", "Este campo no puede estar vacio");

	$validator->print_script();
?>
<script type="text/javascript">

	function busca(id_unidad, id_escenario, pagina){
		var url = 'updater_busca_formulacion.php';
		var pars = '&id='+ id_unidad + '&id_escenario=' + id_escenario +'&pagina=' + pagina;
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
	
	//Event.observe(window, "keypress", function (e) { if(e.keyCode == Event.KEY_RETURN){ validate(); } });
	
	Event.observe('busca_ue', "change", function () {
	  if ($F('busca_ue').length == 2 || $F('busca_ue') == '')  
		   busca($F('busca_ue'),$F('busca_escenario'),'1'); 
	});
	
	Event.observe('busca_escenario', "change", function () {
	  if ($F('busca_escenario').length == 4 || $F('busca_escenario') == '0')  
		   busca($F('busca_ue'),$F('busca_escenario'),'1'); 
	});

	var k = 0;
	var monto_old = 0;
	function AgregarMetas(cod, cant, id_escenario)
	{
	if(cod != 0 || id_escenario != 0){
		var url = 'xmlTraeCategoriasProgramaticas.php'; 
		var pars = 'id=' + cod + '&id_escenario=' + id_escenario;
		var myAjax = new Ajax.Request(
			url, 
			{
				method: 'post', 
				parameters: pars,
				evalScripts: true,
				onComplete: function(request)
				{ 
					json = eval('('+ request.responseText + ')' );
					var fila = mygridMetas.getRowsNum() + 1;
					var cod_meta = cod+'-'+cant+'-'+fila;
					k = fila - 1;
					mygridMetas.getCombo(1).put('0','Seleccione...');
					for(var j = 0; j < json.length ; j++)
					{
						mygridMetas.getCombo(1).put(json[j]['id'],json[j]['id']);
					}
					mygridMetas.addRow(k,cod_meta+',0,,,');
					i = fila;






				}
			});
			}
		else{
		alert("Debe seleccionar un escenario y unidad ejecutora");
		}
	}

	function EliminarMetas()
	{
		mygridMetas.deleteRow(mygridMetas.getSelectedId());
	}
	


	function AgregarGastosDePersonal(cod, cant, id_escenario, tab)
	{
		if(mygridMetas.getSelectedId()){
		var url = 'xmlTraePartidasPresupuestarias.php';
		var categoria =  mygridMetas.cells(mygridMetas.getRowId(mygridMetas.getRowIndex(mygridMetas.getSelectedId())),'1').getValue();
		var pars = 'id=' + cod + '&id_escenario=' + id_escenario + '&tab=' + tab + '&id_categoria=' + categoria;
		var myAjax = new Ajax.Request(
			url, 
			{
				method: 'post', 
				parameters: pars,
				evalScripts: true,
				onComplete: function(request){ 
					json = eval('('+ request.responseText + ')' );

					var cod_gdp = mygridMetas.cells(mygridMetas.getRowId(mygridMetas.getRowIndex(mygridMetas.getSelectedId())),'0').getValue();
					var filas = mygridGastosDePersonal.getRowsNum() + 1;


					//mygridGastosDePersonal.getCombo(1).put('0','Seleccione...');
					for(var j = 0; j < json.length; j++)
					{
						mygridGastosDePersonal.addRow(filas,cod_gdp+'-'+filas+';'+json[j]['id']+';'+json[j]['descripcion']+';'+muestraFloat(json[j]['estimacion']));
						filas = filas +1;
						//mygridGastosDePersonal.getCombo(1).put(json[j]['id'],json[j]['id']);
					}

					//mygridGastosDePersonal.addRow(filas,cod_gdp+'-'+filas+";0;;0");				


				}
			});
		}
		else{
			alert("Debes seleccionar una meta");	
		}
	}

	function EliminarGastosDePersonal()
	{	
		mygridGastosDePersonal.deleteRow(mygridGastosDePersonal.getSelectedId());
	}
	
	function AgregarMaterialesSuministros(cod, cant, id_escenario, tab)
	{
		if(mygridMetas.getSelectedId()){
		var url = 'xmlTraePartidasPresupuestarias.php'; 
		var categoria =  mygridMetas.cells(mygridMetas.getRowId(mygridMetas.getRowIndex(mygridMetas.getSelectedId())),'1').getValue();
		var pars = 'id=' + cod + '&id_escenario=' + id_escenario + '&tab=' + tab + '&id_categoria=' + categoria;
		var myAjax = new Ajax.Request(
			url, 
			{
				method: 'post', 
				parameters: pars,
				evalScripts: true,
				onComplete: function(request){ 
					json = eval('('+ request.responseText + ')' );		

					var cod_gdp = mygridMetas.cells(mygridMetas.getRowId(mygridMetas.getRowIndex(mygridMetas.getSelectedId())),'0').getValue();
					var filas = mygridMaterialesSuministros.getRowsNum() + 1;

					mygridMaterialesSuministros.getCombo(1).put('0','Seleccione...');
					for(var j = 0; j < json.length; j++)
					{
						mygridMaterialesSuministros.getCombo(1).put(json[j]['id'],json[j]['id']);
					}

					mygridMaterialesSuministros.addRow(filas,cod_gdp+'-'+filas+";0;;0");


				}
			});
		}
		else{
		alert("Debes seleccionar una meta");
		}
	}

	function EliminarMaterialesSuministros()
	{
		mygridMaterialesSuministros.deleteRow(mygridMaterialesSuministros.getSelectedId());
	}
	
	function AgregarServiciosNoPersonales(cod, cant, id_escenario, tab)
	{
		if(mygridMetas.getSelectedId()){
		alert();
		var url = 'xmlTraePartidasPresupuestarias.php'; 
		var categoria =  mygridMetas.cells(mygridMetas.getRowId(mygridMetas.getRowIndex(mygridMetas.getSelectedId())),'1').getValue();
		var pars = 'id=' + cod + '&id_escenario=' + id_escenario + '&tab=' + tab + '&id_categoria=' + categoria;
		var myAjax = new Ajax.Request(
			url, 
			{
				method: 'post', 
				parameters: pars,
				evalScripts: true,
				onComplete: function(request){ 
					json = eval('('+ request.responseText + ')' );		

					var cod_gdp = mygridMetas.cells(mygridMetas.getRowId(mygridMetas.getRowIndex(mygridMetas.getSelectedId())),'0').getValue();
					var filas = mygridServiciosNoPersonales.getRowsNum() + 1;

					mygridServiciosNoPersonales.getCombo(1).put('0','Seleccione...');
					for(var j = 0; j < json.length; j++)
					{
						mygridServiciosNoPersonales.getCombo(1).put(json[j]['id'],json[j]['id']);
					}

					mygridServiciosNoPersonales.addRow(filas,cod_gdp+'-'+filas+";0;;0");


				}
			});
		}
		else{
		alert("Debes seleccionar una meta");
		}
	}

	function EliminarServiciosNoPersonales()
	{
		mygridServiciosNoPersonales.deleteRow(mygridServiciosNoPersonales.getSelectedId());
	}
	
	function AgregarActivosReales(cod, cant, id_escenario, tab)
	{
		if(mygridMetas.getSelectedId()){
		var url = 'xmlTraePartidasPresupuestarias.php'; 
		var categoria =  mygridMetas.cells(mygridMetas.getRowId(mygridMetas.getRowIndex(mygridMetas.getSelectedId())),'1').getValue();
		var pars = 'id=' + cod + '&id_escenario=' + id_escenario + '&tab=' + tab + '&id_categoria=' + categoria;
		var myAjax = new Ajax.Request(
			url, 
			{
				method: 'post', 
				parameters: pars,
				evalScripts: true,
				onComplete: function(request){ 
					json = eval('('+ request.responseText + ')' );		

					var cod_gdp = mygridMetas.cells(mygridMetas.getRowId(mygridMetas.getRowIndex(mygridMetas.getSelectedId())),'0').getValue();
					var filas = mygridActivosReales.getRowsNum() + 1;

					mygridActivosReales.getCombo(1).put('0','Seleccione...');
					for(var j = 0; j < json.length; j++)
					{
						mygridActivosReales.getCombo(1).put(json[j]['id'],json[j]['id']);
					}

					mygridActivosReales.addRow(filas,cod_gdp+'-'+filas+";0;;0");


				}
			});
		}
		else{
		alert("Debes seleccionar una meta");
		}
	}

	function EliminarActivosReales()
	{
		mygridActivosReales.deleteRow(mygridActivosReales.getSelectedId());
	}
	
	function AgregarOtros(cod, cant, id_escenario, tab)
	{
		if(mygridMetas.getSelectedId()){
		var url = 'xmlTraePartidasPresupuestarias.php'; 
		var categoria =  mygridMetas.cells(mygridMetas.getRowId(mygridMetas.getRowIndex(mygridMetas.getSelectedId())),'1').getValue();
		var pars = 'id=' + cod + '&id_escenario=' + id_escenario + '&tab=' + tab + '&id_categoria=' + categoria;
		var myAjax = new Ajax.Request(
			url, 
			{
				method: 'post', 
				parameters: pars,
				evalScripts: true,
				onComplete: function(request){ 
					json = eval('('+ request.responseText + ')' );		

					var cod_gdp = mygridMetas.cells(mygridMetas.getRowId(mygridMetas.getRowIndex(mygridMetas.getSelectedId())),'0').getValue();
					var filas = mygridOtros.getRowsNum() + 1;

					mygridOtros.getCombo(1).put('0','Seleccione...');
					for(var j = 0; j < json.length; j++)
					{
						mygridOtros.getCombo(1).put(json[j]['id'],json[j]['id']);
					}

					mygridOtros.addRow(filas,cod_gdp+'-'+filas+";0;;0");


				}
			});
		}
		else{
		alert("Debes seleccionar una meta");
		}
	}

	function EliminarOtros()
	{
		mygridOtros.deleteRow(mygridOtros.getSelectedId());
	}
	
	function AgregarObservaciones(cod, cant)
	{
		if(mygridMetas.getSelectedId()){
			var cod_gdp = mygridMetas.cells(mygridMetas.getRowId(mygridMetas.getRowIndex(mygridMetas.getSelectedId())),'0').getValue();
			var filas = mygridOtros.getRowsNum() + 1;

			mygridObservaciones.addRow(filas,cod_gdp+'-'+filas+"");
		}
		else{
		alert("Debes seleccionar una meta");
		}


	}

	function EliminarObservaciones()
	{
		mygridObservaciones.deleteRow(mygridObservaciones.getSelectedId());
	}
	
	function GuardarMetas()
	{
		var JsonAux,metas=new Array;
		mygridMetas.clearSelection();
		$('id_tab').value = tabbar.getActiveTab();

		for(j=0;j<mygridMetas.getRowsNum();j++)
		{
			if(!isNaN(mygridMetas.getRowId(j)))
			{
				metas[j] = new Array;
				metas[j][0]= mygridMetas.cells(mygridMetas.getRowId(j),0).getValue();
				metas[j][1]= mygridMetas.cells(mygridMetas.getRowId(j),1).getValue();
				metas[j][2]= mygridMetas.cells(mygridMetas.getRowId(j),2).getValue();			
				metas[j][3]= mygridMetas.cells(mygridMetas.getRowId(j),3).getValue();
				metas[j][4]= mygridMetas.cells(mygridMetas.getRowId(j),4).getValue();			
			}
		}//alert(entradas);
		JsonAux={"metas":metas};
		$("metas").value=JsonAux.toJSONString();
	}
	
	function GuardarGastosDePersonal()
	{
		var JsonAux,gastos_personal=new Array;
		mygridGastosDePersonal.clearSelection()
		
		for(j=0;j<mygridGastosDePersonal.getRowsNum();j++)

		{
			if(!isNaN(mygridGastosDePersonal.getRowId(j)))
			{
				gastos_personal[j] = new Array;
				gastos_personal[j][0]= mygridGastosDePersonal.cells(mygridGastosDePersonal.getRowId(j),0).getValue();
				gastos_personal[j][1]= mygridGastosDePersonal.cells(mygridGastosDePersonal.getRowId(j),1).getValue();
				gastos_personal[j][2]= mygridGastosDePersonal.cells(mygridGastosDePersonal.getRowId(j),2).getValue();			
				gastos_personal[j][3]= mygridGastosDePersonal.cells(mygridGastosDePersonal.getRowId(j),3).getValue();
			}
		}//alert(entradas);
		JsonAux={"gastos_personal":gastos_personal};
		$("gastos_personal").value=JsonAux.toJSONString(); 
	}
	
	function GuardarMaterialesSuministros()
	{
		var JsonAux,mat_suminis=new Array;
		mygridMaterialesSuministros.clearSelection()
	
		for(j=0;j<mygridMaterialesSuministros.getRowsNum();j++)

		{
			if(!isNaN(mygridMaterialesSuministros.getRowId(j)))
			{
				mat_suminis[j] = new Array;
				mat_suminis[j][0]= mygridMaterialesSuministros.cells(mygridMaterialesSuministros.getRowId(j),0).getValue();
				mat_suminis[j][1]= mygridMaterialesSuministros.cells(mygridMaterialesSuministros.getRowId(j),1).getValue();
				mat_suminis[j][2]= mygridMaterialesSuministros.cells(mygridMaterialesSuministros.getRowId(j),2).getValue();			
				mat_suminis[j][3]= mygridMaterialesSuministros.cells(mygridMaterialesSuministros.getRowId(j),3).getValue();
			}
		}//alert(entradas);
		JsonAux={"mat_suminis":mat_suminis};
		$("mat_suminis").value=JsonAux.toJSONString();
	}
	
	function GuardarServiciosNoPersonales()
	{
		var JsonAux,serv_no_personal=new Array;
		mygridServiciosNoPersonales.clearSelection()	
	
		for(j=0;j<mygridServiciosNoPersonales.getRowsNum();j++)

		{
			if(!isNaN(mygridServiciosNoPersonales.getRowId(j)))
			{
				serv_no_personal[j] = new Array;
				serv_no_personal[j][0]= mygridServiciosNoPersonales.cells(mygridServiciosNoPersonales.getRowId(j),0).getValue();
				serv_no_personal[j][1]= mygridServiciosNoPersonales.cells(mygridServiciosNoPersonales.getRowId(j),1).getValue();
				serv_no_personal[j][2]= mygridServiciosNoPersonales.cells(mygridServiciosNoPersonales.getRowId(j),2).getValue();			
				serv_no_personal[j][3]= mygridServiciosNoPersonales.cells(mygridServiciosNoPersonales.getRowId(j),3).getValue();
			}
		}//alert(entradas);
		JsonAux={"serv_no_personal":serv_no_personal};
		$("serv_no_personal").value=JsonAux.toJSONString(); 
	}
	
	function GuardarActivosReales()
	{
		var JsonAux,act_reales=new Array;
		mygridActivosReales.clearSelection()
		
		for(j=0;j<mygridActivosReales.getRowsNum();j++)

		{
			if(!isNaN(mygridActivosReales.getRowId(j)))
			{
				act_reales[j] = new Array;
				act_reales[j][0]= mygridActivosReales.cells(mygridActivosReales.getRowId(j),0).getValue();
				act_reales[j][1]= mygridActivosReales.cells(mygridActivosReales.getRowId(j),1).getValue();
				act_reales[j][2]= mygridActivosReales.cells(mygridActivosReales.getRowId(j),2).getValue();			
				act_reales[j][3]= mygridActivosReales.cells(mygridActivosReales.getRowId(j),3).getValue();
			}
		}//alert(entradas);
		JsonAux={"act_reales":act_reales};
		$("act_reales").value=JsonAux.toJSONString();
	}
	
	function GuardarOtros()
	{
		var JsonAux,otros=new Array;
		mygridOtros.clearSelection()
		
		for(j=0;j<mygridOtros.getRowsNum();j++)
		{
			if(!isNaN(mygridOtros.getRowId(j)))
			{
				otros[j] = new Array;
				otros[j][0]= mygridOtros.cells(mygridOtros.getRowId(j),0).getValue();
				otros[j][1]= mygridOtros.cells(mygridOtros.getRowId(j),1).getValue();
				otros[j][2]= mygridOtros.cells(mygridOtros.getRowId(j),2).getValue();			
				otros[j][3]= mygridOtros.cells(mygridOtros.getRowId(j),3).getValue();
			}
		}//alert(entradas);
		JsonAux={"otros":otros};
		$("otros").value=JsonAux.toJSONString();
	}
	
	function GuardarObservaciones()
	{
		var JsonAux,observacion=new Array;
		mygridObservaciones.clearSelection()
		
		for(j=0;j<mygridObservaciones.getRowsNum();j++)

		{
			if(!isNaN(mygridObservaciones.getRowId(j)))
			{
				observacion[j] = new Array;
				observacion[j][0]= mygridObservaciones.cells(mygridObservaciones.getRowId(j),0).getValue();
				observacion[j][1]= mygridObservaciones.cells(mygridObservaciones.getRowId(j),1).getValue();
			}
		}//alert(entradas);
		JsonAux={"observacion":observacion};
		$("observacion").value=JsonAux.toJSONString();
	}
	
	function traeCodigoDesdeXML(id_ue)
	{
		var url = 'xmlTraeCodigo.php'; 
		var pars = 'id=' + id_ue;
		var myAjax = new Ajax.Request(
			url, 
			{
				method: 'post', 
				parameters: pars,
				onComplete: traeCodigo
			});
	}
	
	function traeCodigo(originalRequest)
	{
		var xmlDoc = originalRequest.responseXML;
		var x = xmlDoc.getElementsByTagName('codigo');
		for(j=0;j<x[0].childNodes.length;j++)
		{ 
			if (x[0].childNodes[j].nodeType != 1) continue;
			var nombre = x[0].childNodes[j].nodeName
			$(nombre).value = x[0].childNodes[j].firstChild.nodeValue;
		}
	}
	
	function traeAnioEscenarioDesdeXML(id_escenario)
	{
		var url = 'xmlTraeAnioEscenario.php'; 
		var pars = 'id_escenario=' + id_escenario;
		var myAjax = new Ajax.Request(
			url, 
			{
				method: 'post', 
				parameters: pars,
				onComplete: traeAnioEscenario
			});
	}
	
	function traeAnioEscenario(originalRequest)
	{
		var xmlDoc = originalRequest.responseXML;
		var x = xmlDoc.getElementsByTagName('anio_escenario');
		for(j=0;j<x[0].childNodes.length;j++)
		{ 
			if (x[0].childNodes[j].nodeType != 1) continue;
			var nombre = x[0].childNodes[j].nodeName
			$(nombre).value = x[0].childNodes[j].firstChild.nodeValue;
		}
	}
	
	function ValidarGastosDePersonalPP(stage,rowId,cellInd)
	{
		if(stage == 0 && cellInd == 3){
			 if(isNaN(mygridGastosDePersonal.cells(rowId,3).getValue()))
			 	mygridGastosDePersonal.cells(rowId,3).setValue('0');
			 //alert(mygridGastosDePersonal.cells(rowId,3).getValue());
			 monto_old = parseFloat(mygridGastosDePersonal.cells(rowId,3).getValue());
		}
		if(stage == 2)
		{
			if(cellInd == '1')
			{
				var url = 'BuscarDescripcion.php';
				var pars = 'id=' + mygridGastosDePersonal.cells(rowId,'1').getValue();
				for(j = 0; j < mygridGastosDePersonal.getRowsNum(); j++)

				{
					if (mygridGastosDePersonal.getSelectedId() != j)
					{
						if(mygridGastosDePersonal.getRowIndex(j) != -1)
						{
							if (mygridGastosDePersonal.cells(rowId,'1').getValue() == mygridGastosDePersonal.cells(j,1).getValue())
							{
								alert("La Partida Presupuestaria Ya Se ha Seleccionado");
								mygridGastosDePersonal.cells(rowId,'1').setValue('0');
								return false;
							}
						}
					}
				}

				var Request = new Ajax.Request(
					url,
					{
						method: 'post',
						parameters: pars,
						//asynchronous:false, 	
						onComplete:function(request)
						{
							if(request.responseText)
							{
								mygridGastosDePersonal.cells(rowId,'2').setValue(request.responseText);
							}
						}
					}
				);
				var subcadena;
				var encontrado = false;
				var j;
				var i;
				i = 0;
				j = mygridMetas.getRowsNum();
				subcadena = mygridGastosDePersonal.cells(rowId,'0').getValue();
				while(!encontrado || i < j){
					if( mygridMetas.cells(i,'0').getValue() == subcadena.substring(0,6)){
						encontrado = true;	
					}
					i++;
				}
				var url = 'BuscarValor.php';
				var pars = 'id=' + mygridGastosDePersonal.cells(rowId,'1').getValue() + '&cat=' + mygridMetas.cells((i-1),'1').getValue() + '&escenario=' + $('escenario').value;
				var Request = new Ajax.Request(
					url,
					{
						method: 'post',
						parameters: pars,
						//asynchronous:false, 	
						onComplete:function(request)
						{
							if(request.responseText)
							{
								mygridGastosDePersonal.cells(rowId,'3').setValue(request.responseText);
							}
						}
					}
				);
			}
			if(cellInd == 3){
				if((mygridGastosDePersonal.cells(rowId,3).getValue()=='') || (isNaN(mygridGastosDePersonal.cells(rowId,3).getValue()))){
					alert("Debe introducir la cantidad ");
					mygridGastosDePersonal.cells(rowId,3).setValue(monto_old.toString());
					return false;
				} else {	
					var monto_new = parseFloat(mygridGastosDePersonal.cells(rowId,3).getValue());
					if(isNaN(monto_new))
						monto_new = 0;
					monto_new = muestraFloat(monto_new);
					mygridGastosDePersonal.cells(rowId,3).setValue(monto_new);	
				}
			}
		}
	}
	
	function ValidarMaterialesSuministrosPP(stage,rowId,cellInd)
	{
		if(stage == 0 && cellInd == 3){
			 if(isNaN(mygridMaterialesSuministros.cells(rowId,3).getValue()))
			 	mygridMaterialesSuministros.cells(rowId,3).setValue('0');
			 //alert(mygridGastosDePersonal.cells(rowId,3).getValue());
			 monto_old = parseFloat(mygridMaterialesSuministros.cells(rowId,3).getValue());
		}
		
		if(stage == 2)
		{
			if(cellInd == '1')
			{
				var url = 'BuscarDescripcion.php';
				var pars = 'id=' + mygridMaterialesSuministros.cells(rowId,'1').getValue();
				for(j = 0; j < mygridMaterialesSuministros.getRowsNum(); j++)

				{
					if (mygridMaterialesSuministros.getSelectedId() != j)
					{
						if(mygridMaterialesSuministros.getRowIndex(j) != -1)
						{
							if (mygridMaterialesSuministros.cells(rowId,'1').getValue() == mygridMaterialesSuministros.cells(j,1).getValue())
							{
								alert("La Partida Presupuestaria Ya Se ha Seleccionado");
								mygridMaterialesSuministros.cells(rowId,'1').setValue('0');
								return false;
							}
						}
					}
				}

				var Request = new Ajax.Request(
					url,
					{
						method: 'post',
						parameters: pars,
						//asynchronous:false, 	
						onComplete:function(request)
						{
							if(request.responseText)
							{
								mygridMaterialesSuministros.cells(rowId,'2').setValue(request.responseText);
							}
						}
					}
				);
			}
			if(cellInd == 3){
				if((mygridMaterialesSuministros.cells(rowId,3).getValue()=='') || (isNaN(mygridMaterialesSuministros.cells(rowId,3).getValue()))){
					alert("Debe introducir la cantidad ");
					mygridMaterialesSuministros.cells(rowId,3).setValue(monto_old.toString());
					return false;
				} else {	
					var monto_new = parseFloat(mygridMaterialesSuministros.cells(rowId,3).getValue());
					if(isNaN(monto_new))
						monto_new = 0;
					monto_new = muestraFloat(monto_new);
					mygridMaterialesSuministros.cells(rowId,3).setValue(monto_new);	
				}
			}
		}
	}
	
	function ValidarServiciosNoPersonalesPP(stage,rowId,cellInd)
	{
		if(stage == 0 && cellInd == 3){
			 if(isNaN(mygridServiciosNoPersonales.cells(rowId,3).getValue()))
			 	mygridServiciosNoPersonales.cells(rowId,3).setValue('0');
			 //alert(mygridGastosDePersonal.cells(rowId,3).getValue());
			 monto_old = parseFloat(mygridServiciosNoPersonales.cells(rowId,3).getValue());
		}
		
		if(stage == 2)
		{
			if(cellInd == '1')
			{
				var url = 'BuscarDescripcion.php';
				var pars = 'id=' + mygridServiciosNoPersonales.cells(rowId,'1').getValue();
				for(j = 0; j < mygridServiciosNoPersonales.getRowsNum(); j++)

				{
					if (mygridServiciosNoPersonales.getSelectedId() != j)
					{
						if(mygridServiciosNoPersonales.getRowIndex(j) != -1)
						{
							if (mygridServiciosNoPersonales.cells(rowId,'1').getValue() == mygridServiciosNoPersonales.cells(j,1).getValue())
							{
								alert("La Partida Presupuestaria Ya Se ha Seleccionado");
								mygridServiciosNoPersonales.cells(rowId,'1').setValue('0');
								return false;
							}
						}
					}
				}

				var Request = new Ajax.Request(
					url,
					{
						method: 'post',
						parameters: pars,
						//asynchronous:false, 	
						onComplete:function(request)
						{
							if(request.responseText)
							{
								mygridServiciosNoPersonales.cells(rowId,'2').setValue(request.responseText);
							}
						}
					}
				);
			}
			if(cellInd == 3){
				if((mygridServiciosNoPersonales.cells(rowId,3).getValue()=='') || (isNaN(mygridServiciosNoPersonales.cells(rowId,3).getValue()))){
					alert("Debe introducir la cantidad ");
					mygridServiciosNoPersonales.cells(rowId,3).setValue(monto_old.toString());
					return false;
				} else {	
					var monto_new = parseFloat(mygridServiciosNoPersonales.cells(rowId,3).getValue());
					if(isNaN(monto_new))
						monto_new = 0;
					monto_new = muestraFloat(monto_new);
					mygridServiciosNoPersonales.cells(rowId,3).setValue(monto_new);	
				}
			}
		}
	}
	
	function ValidarActivosRealesPP(stage,rowId,cellInd)
	{
		if(stage == 0 && cellInd == 3){
			 if(isNaN(mygridActivosReales.cells(rowId,3).getValue()))
			 	mygridActivosReales.cells(rowId,3).setValue('0');
			 //alert(mygridGastosDePersonal.cells(rowId,3).getValue());
			 monto_old = parseFloat(mygridActivosReales.cells(rowId,3).getValue());
		}
		
		if(stage == 2)
		{
			if(cellInd == '1')
			{
				var url = 'BuscarDescripcion.php';
				var pars = 'id=' + mygridActivosReales.cells(rowId,'1').getValue();
				for(j = 0; j < mygridActivosReales.getRowsNum(); j++)

				{
					if (mygridActivosReales.getSelectedId() != j)
					{
						if(mygridActivosReales.getRowIndex(j) != -1)
						{
							if (mygridActivosReales.cells(rowId,'1').getValue() == mygridActivosReales.cells(j,1).getValue())
							{
								alert("La Partida Presupuestaria Ya Se ha Seleccionado");
								mygridActivosReales.cells(rowId,'1').setValue('0');
								return false;
							}
						}
					}
				}

				var Request = new Ajax.Request(
					url,
					{
						method: 'post',
						parameters: pars,
						//asynchronous:false, 	
						onComplete:function(request)
						{
							if(request.responseText)
							{
								mygridActivosReales.cells(rowId,'2').setValue(request.responseText);
							}
						}
					}
				);
			}
			if(cellInd == 3){
				if((mygridActivosReales.cells(rowId,3).getValue()=='') || (isNaN(mygridActivosReales.cells(rowId,3).getValue()))){
					alert("Debe introducir la cantidad ");
					mygridActivosReales.cells(rowId,3).setValue(monto_old.toString());
					return false;
				} else {	
					var monto_new = parseFloat(mygridActivosReales.cells(rowId,3).getValue());
					if(isNaN(monto_new))
						monto_new = 0;
					monto_new = muestraFloat(monto_new);
					mygridActivosReales.cells(rowId,3).setValue(monto_new);	
				}
			}
		}
	}
	
	function ValidarOtrosPP(stage,rowId,cellInd)
	{
		if(stage == 0 && cellInd == 3){
			 if(isNaN(mygridOtros.cells(rowId,3).getValue()))
			 	mygridOtros.cells(rowId,3).setValue('0');
			 //alert(mygridGastosDePersonal.cells(rowId,3).getValue());
			 monto_old = parseFloat(mygridOtros.cells(rowId,3).getValue());
		}
		
		if(stage == 2)
		{
			if(cellInd == '1')
			{
				var url = 'BuscarDescripcion.php';
				var pars = 'id=' + mygridOtros.cells(rowId,'1').getValue();
				for(j = 0; j < mygridOtros.getRowsNum(); j++)

				{
					if (mygridOtros.getSelectedId() != j)
					{
						if(mygridOtros.getRowIndex(j) != -1)
						{
							if (mygridOtros.cells(rowId,'1').getValue() == mygridOtros.cells(j,1).getValue())
							{
								alert("La Partida Presupuestaria Ya Se ha Seleccionado");
								mygridOtros.cells(rowId,'1').setValue('0');
								return false;
							}
						}
					}
				}

				var Request = new Ajax.Request(
					url,
					{
						method: 'post',
						parameters: pars,
						//asynchronous:false, 	
						onComplete:function(request)
						{
							if(request.responseText)
							{
								mygridOtros.cells(rowId,'2').setValue(request.responseText);
							}
						}
					}
				);
			}
			if(cellInd == 3){
				if((mygridOtros.cells(rowId,3).getValue()=='') || (isNaN(mygridOtros.cells(rowId,3).getValue()))){
					alert("Debe introducir la cantidad ");
					mygridOtros.cells(rowId,3).setValue(monto_old.toString());
					return false;
				} else {	
					var monto_new = parseFloat(mygridOtros.cells(rowId,3).getValue());
					if(isNaN(monto_new))
						monto_new = 0;
					monto_new = muestraFloat(monto_new);
					mygridOtros.cells(rowId,3).setValue(monto_new);	
				}
			}
		}
	}
	function ValidarCategoriasProgramaticas(stage,rowId,cellInd)
	{
		if(stage == 2)
		{
			if(cellInd == '1')
			{
				for(j = 0; j < k; j++)
				{
					if (mygridMetas.getSelectedId() != j)
					{
						if(mygridMetas.getRowIndex(j) != -1)
						{
							if (mygridMetas.cells(rowId,'1').getValue() == mygridMetas.cells(j,1).getValue())
							{
								alert("La Categoria Programatica Ya Se ha Seleccionado");
								mygridMetas.cells(rowId,'1').setValue('0');
								return false;
							}
						}
					}
				}
			}
			if(cellInd == '3'){
				if((mygridMetas.cells(rowId,3).getValue()=='') || (isNaN(mygridMetas.cells(rowId,3).getValue()))){
					alert("Debe introducir la cantidad ");
					mygridMetas.cells(rowId,3).setValue('0');
					return false;
				} else {	
					var monto_new = parseFloat(mygridMetas.cells(rowId,3).getValue());
					if(isNaN(monto_new))
						monto_new = 0;
					monto_new = muestraFloat(monto_new);
					mygridMetas.cells(rowId,3).setValue(monto_new);	
				}
			}
			if(cellInd == '4'){
				if((mygridMetas.cells(rowId,4).getValue()=='') || (!isNaN(mygridMetas.cells(rowId,4).getValue()))){
					alert("No Debe introducir una cantidad");
					mygridMetas.cells(rowId,4).setValue('valor por defecto');
					return false;
					}
			}
		}
	}
	
	function ValidarGuardar()
	{
		if(stage == 2)
		{
			if(cellInd == '1')
			{
				for(j = 0; j < k; j++)
				{
					if (mygridMetas.getSelectedId() != j)
					{
						if(mygridMetas.getRowIndex(j) != -1)
						{
							if (mygridMetas.cells(rowId,'1').getValue() == mygridMetas.cells(j,1).getValue())
							{
								alert("La Categoria Programatica Ya Se ha Seleccionado");
								mygridMetas.cells(rowId,'1').setValue('0');
								return false;
							}
						}
					}
				}
			}
		}
	}
	
	function traeUnidadesEjecutoras(id_escenario){
		var url = 'updater_selects.php';
		var pars = 'combo=id_unidades&escenario=' + id_escenario;
		var updater = new Ajax.Updater('divcomboescenario', 
		url,
		{
			method: 'post',
			parameters: pars,
			asynchronous:true, 
			evalScripts:true,
			onComplete:function(request){}
		}
		);
	}
	
	function traerCategoriaSeleccionada(rowId){
	var cp = mygridMetas.cells(rowId,1).getValue();
	var url = 'json.php';
	var pars = 'op=categoria_programatica&cp=' + cp +'&escenario='+ $('escenario').value;
			
	var myAjax = new Ajax.Request(
		url,
		{
			method: 'get',
			parameters: pars,
			onComplete: function(peticion){
				var jsonData = eval('(' + peticion.responseText + ')');
				if (jsonData == undefined) { return }
				$('nom_cat_pro').value 	= jsonData.descripcion;
			}
		}
	);
}
</script>
<? require ("comun/footer.php"); ?>