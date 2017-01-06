<?
	require ("comun/ini.php");
	$oaprobacion_metas = new aprobacion_metas;

	$accion = $_REQUEST['accion'];

	if($accion == 'Guardar')
	{
		if($_REQUEST['procesado'] == 1)
		{
			$oaprobacion_metas->set_formulacion($conn,
												$_REQUEST['id_formulacion'],
												$_REQUEST['procesado']
												);
		}
		else 
		{
			$oaprobacion_metas->add($conn, 
									$_REQUEST['id_formulacion'],
									$_REQUEST['id_escenario'],
									$_REQUEST['escenario'],
									$_REQUEST['status'],
									$_REQUEST['id_unidad_ejecutora'], 
									$_REQUEST['unidad_ejecutora'], 
									$_REQUEST['organismo'], 
									$_REQUEST['id_meta'], 
									$_REQUEST['desc_meta'], 
									$_REQUEST['objetivo'],
									$_REQUEST['procesado']
									);
		}
	}
	
	$caprobacion = $oaprobacion_metas->get_all($conn, $start_record, $page_size);
	require ("comun/header.php");

	if(!empty($msj))
	{ 
?>
		<div id="msj" >
			<?=$msj?>
		</div>
		<? echo "<br>"; 
	} 
?>
	<br />
	<span class="titulo_maestro">
		Aprobaci&oacute;n de Metas 
	</span>
	<div id="formulario">
		<a href="#" onclick="updater(0); return false;">
			Aprobar Nuevo Registro
		</a>
	</div>
	<br />
	<? 
		if(is_array($caprobacion))
		{ 
	?>
			<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
				<tr class="cabecera"> 
					<td>C&oacute;digo</td>
					<td>Escenario</td>
					<td>A&ntilde;o</td>
					<td>Unidad Ejecutora</td>
					<td>Status</td>
					<td>&nbsp;</td>
				</tr>
	<? 
				foreach($caprobacion as $aprobacion) 
				{ 
					if(($aprobacion->procesado) == 3)
					{
						$status = "Procesar";
					}
	?> 
				<tr class="filas"> 
					<td><?=$aprobacion->id_aprobacion_meta?></td>
					<td align="left"><?=$aprobacion->escenario?></td>
					<td align="right"><?=$aprobacion->id_escenario?></td>
					<td align="left"><?=$aprobacion->unidad_ejecutora?></td>
					<td align="center"><?=strtoupper($status)?></td>
					<td align="center"><a href="#" onclick="updater('<?=$aprobacion->id_aprobacion_meta?>'); return false;" title="Ver Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
				</tr>
	<? 
				}
	?>
			</table>
	<? 
		}
		else 
		{
			echo "No hay registros en la bd";
		} 
	?>
	<br />
	<div style="height:40px;padding-top:10px;">
		<p id="cargando" style="display:none;margin-top:0px;">
  			<img alt="Cargando" src="images/loading.gif" /> Cargando...
		</p>
	</div>
<?
	$validator->create_message("error_cod_formulacion", "id_formulacion", "*");
	$validator->create_message("error_id_meta", "id_meta", "*");
	$validator->create_message("error_procesado", "procesado", "*");
	$validator->print_script();
?>
<? require ("comun/footer.php"); ?>
<script>
	function traeFormulacionDesdeXML(id_formulacion)
	{
		var url = 'xmlTraeFormulacion.php'; 
		var pars = 'id_formulacion=' + id_formulacion;
		var myAjax = new Ajax.Request(
			url, 
			{
				method: 'post', 
				parameters: pars,
				onComplete: traeFormulacion
			});
	}
	
	function traeFormulacion(originalRequest)
	{
		var xmlDoc = originalRequest.responseXML;
		var x = xmlDoc.getElementsByTagName('formulacion');
		for(j = 0; j < x[0].childNodes.length; j++)
		{ 
			if (x[0].childNodes[j].nodeType != 1) continue;
			var nombre = x[0].childNodes[j].nodeName
			$(nombre).value = x[0].childNodes[j].firstChild.nodeValue;
		}
	}
	
	function traeMetas(id_formulacion)
	{
		var url = 'updater_selects.php';
		var pars = 'combo=metas&id_formulacion=' + id_formulacion;
		var updater = new Ajax.Updater('divcombometa', 
		url,
		{
			method: 'post',
			parameters: pars,
			asynchronous:true, 
			evalScripts:true,
		});
	}
	
	function traeDescripcionMeta(id_meta)
	{
		var url = 'xmlTraeDescripcionMeta.php'; 
		var pars = 'id_meta=' + id_meta;
		var myAjax = new Ajax.Request(
			url, 
			{
				method: 'post', 
				parameters: pars,
				onComplete: traeDescripcion
			});
	}
	
	function traeDescripcion(originalRequest)
	{
		var xmlDoc = originalRequest.responseXML;
		var x = xmlDoc.getElementsByTagName('descripcion');
		for(j = 0; j < x[0].childNodes.length; j++)
		{ 
			if (x[0].childNodes[j].nodeType != 1) continue;
			var nombre = x[0].childNodes[j].nodeName
			$(nombre).value = x[0].childNodes[j].firstChild.nodeValue;
		}
	}
</script>