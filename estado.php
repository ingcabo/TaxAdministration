<?
require ("comun/ini.php");
$pagina = $_REQUEST['pagina'];
$num = 20;
if (!$pagina) {
    $inicio = 0;
    $pagina=1;
}
else {
    $inicio = ($pagina - 1) * $num;
} 
// Creando el objeto Estados
$oEstados = new estado;
$accion = $_REQUEST['accion'];
if($accion == 'Guardar'){
	$msj = $oEstados->add($conn, $_REQUEST['descripcion']);
		
}elseif($accion == 'Actualizar'){
	$msj = $oEstados->set($conn, $_REQUEST['id'], $_REQUEST['descripcion']);
		
}elseif($accion == 'del'){
	if($oEstados->del($conn, $_REQUEST['id']))
		$msj = REG_DEL_OK;
	else
		$msj = ERROR;
}


$cEstados=$oEstados->buscar($conn, $num,$inicio);
$total_E = estado::total_registro_busqueda($conn);
$total = $total_E;
//echo "aqui ".$total;
require ("comun/header.php");

?>

<div id="msj" <?=(empty($msj)) ? 'style="display:none;"':'style="display:block;"'?>><?=$msj?></div><br />
<br />
<span class="titulo_maestro">Maestro de Estados</span>
<div id="formulario">
	<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />

<fieldset id="buscador">
	<legend>Buscar:</legend>
	<table>
		<td>Descripci&oacute;n:</td>
		<td>
			<input type="text" name="busca_desc" id="busca_desc" />
			<input type="hidden" name="hid_desc" id="hid_desc" />
		</td>
	</table>
</fieldset>
<br />

<div id="busqueda">
	<?
	if(is_array($cEstados))
	{
	?>
	<table class="sortable" id="grid" cellpadding="0" cellspacing="1" align="center">
		<tr class="cabecera"> 
			<td width="10%">C&oacute;digo</td>
			<td>Descripci&oacute;n</td>
			<td width="5%">&nbsp;</td>
			<td width="5%">&nbsp;</td>
		</tr>
		<? 
		foreach($cEstados as $estados)
		{ 
		?> 
		<tr class="filas"> 
			<td><?=$estados->id?></td>
			<td><?=$estados->descripcion?></td>
			<td align="center"><a href="estado.php?accion=del&id=<?=$estados->id?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
			<td align="center"><a href="#" onclick="updater('<?=$estados->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
		</tr>
		<?
		}
		
		$total_paginas = ceil($total / $num);
		?>
		<tr class="filas">
			<td colspan="7" align="center">
			<? 
			for ($j=1;$j<=$total_paginas;$j++)
			{
				if ($j==1)
				{
			?>
					<span class="actual"><?=$j?></span>
				<?
				}
				else
				{
				?>
					<a href="" onclick="busca($('hid_desc').value,'<?=$j?>'); return false;"><?=(($j==1) ? '':' - ').$j?></a>
				<?
				}
			}
			?>
			</td>
		</tr>
		<tr class="filas">
			<td colspan="7" align="center"> Pagina <strong><?=$pagina?></strong> de <strong><?=$total_paginas?></strong></td>
		</tr>
	</table>
	<?
	}
	else
	{
		echo "No hay registros en la bd";
	}
	?>
</div>
<br />
<div style="height:40px;padding-top:10px;">
<p id="cargando" style="display:none;margin-top:0px;">
  <img alt="Cargando" src="images/loading.gif" /> Cargando...
</p>
</div>

<script type="text/javascript">
	
	var t;
	
	function buscador(descripcion, pagina, keyCode)
	{
		if ((keyCode>=65 && keyCode<=90) || (keyCode>=48 && keyCode<=57) || (keyCode>=96 && keyCode<=105) || keyCode==8 || keyCode==46)
		{
			clearTimeout(t);
			$('hid_desc').value = descripcion;
			t = setTimeout("busca('"+descripcion+"','"+pagina+"')", 800);
		}
	}
	
	function busca(descripcion, pagina)
	{
		var url = 'updater_busca_estado.php';
		var pars = 'descripcion=' + descripcion + '&ms='+new Date().getTime()+ '&pagina='+pagina;
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

	Event.observe('busca_desc', "keyup", function (evt) 
	{	buscador($F('busca_desc'), '1', evt.keyCode);	});
</script>

<?
$validator->create_message("error_desc", "descripcion", "*");
$validator->print_script();
require ("comun/footer.php");
?>
