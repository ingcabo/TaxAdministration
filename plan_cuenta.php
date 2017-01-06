<?
require ("comun/ini.php");
// Creando el objeto Proveedores
$oPlan_cuenta = new plan_cuenta;
//die($_REQUEST['accion']);
$accion = $_REQUEST['accion'];
$tamano_pagina = 20;

if($accion == 'Guardar')
{
	$msj = $oPlan_cuenta->add($conn, str_replace('.','',$_REQUEST['codcta']), $_REQUEST['descripcion'], $_REQUEST['naturaleza'], $_REQUEST['movim'], $_REQUEST['nominal'], guardafloat($_REQUEST['saldo_inicial']), $escEnEje, (($_REQUEST['cuentas_acum']==='0') ? 'null':$_REQUEST['cuentas_acum']));
	if($msj===true)
		$msj = REG_ADD_OK;
	else if (strpos($msj, 'NO EXISTE NIVEL INMEDIATAMENTE SUPERIOR') !== false)
		$msj = 'Debe crear la cuenta acumuladora de nivel superior';
	else if (strpos($msj, 'CODIGO CONTABLE INVALIDO') !== false)
		$msj = 'C&oacute;digo Contable Inv&aacute;lido';
	else if (strpos($msj, 'ACUMULAR EN CUENTA DE MOVIMIENTO') !== false)
		$msj = 'No puede acumular en una cuenta de movimiento';
	else if ($msj == 'Duplicado')
		$msj = DUPLICATED;
	else
		$msj = ERROR;
}
elseif($accion == 'Actualizar')
{
	$msj = $oPlan_cuenta->set($conn,$_REQUEST['id'], str_replace('.','',$_REQUEST['codcta']), $_REQUEST['descripcion'], $_REQUEST['naturaleza'], $_REQUEST['movim'], $_REQUEST['nominal'], guardafloat($_REQUEST['saldo_inicial']), $escEnEje, $_REQUEST['cuentas_acum']);
	if ($msj === true)
		$msj = REG_SET_OK;
	else if (strpos($msj, 'NO EXISTE NIVEL INMEDIATAMENTE SUPERIOR') !== false)
		$msj = 'Debe crear la cuenta acumuladora de nivel superior';
	else if (strpos($msj, 'CODIGO CONTABLE INVALIDO') !== false)
		$msj = 'C&oacute;digo Contable Inv&aacute;lido';
	else if ($msj == 'Duplicado')
		$msj = DUPLICATED;
	else
		$msj = ERROR;
}
elseif($accion == 'del')
{
	$msj = $oPlan_cuenta->del($conn, $_REQUEST['id']);
	if($msj===true)
		$msj = REG_DEL_OK;
	else if ($msj = 'Relacionado')
		$msj = "Ha Intentado eliminar una cuenta relacionada con una partida presupuestaria";
	else
		$msj = ERROR;
}



require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<br />
<span class="titulo_maestro">Maestro de Plan de Cuenta </span>
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
			<td>Codigo Cuenta</td>
			<td>Descripci&oacute;n</td>
		</tr>
		
		<tr>
			<td>
				<input type="text" name="codigo_cuenta" id="codigo_cuenta" maxlength="16" />
				<input type="hidden" name="cod_cta" id="cod_cta" />
			</td>
			<td>
				<input type="text" name="descrip_cuenta" id="descrip_cuenta"  size="30" />
				<input type="hidden" name="desc_cta" id="desc_cta" />
			</td>
		</tr>
	</table>
</fieldset>
</div>
<br />
<div style="margin-bottom:10px" id="busqueda">
	<?
	$cPlanCuenta = $oPlan_cuenta->buscar($conn, '', '', '', 0, $tamano_pagina, "codcta::text");
	$total = $oPlan_cuenta->total_registro_busqueda($conn, '', '', '');

	if (is_array($cPlanCuenta))
	{
	?>
	<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
		<tr class="cabecera"> 
			<td width="20%">Codigo Cuenta</td>
			<td width="53%">Descripci&oacute;n</td>
			<td width="5%">&nbsp;</td>
			<td width="5%">&nbsp;</td>
		</tr>
	<?
		foreach($cPlanCuenta as $plan_cuenta)
		{
		?>
		<tr class="filas"> 
			<td><?=$plan_cuenta->codcta?></td>
			<td><?=$plan_cuenta->descripcion?></td>
			<td align="center"><a href="?accion=del&id=<?=$plan_cuenta->id?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
			<td align="center"><a href="#" onclick="updater(<?=$plan_cuenta->codcta?>); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
		</tr>
		<?
		}

		$total_paginas = ceil($total / $tamano_pagina);
		?>
		<tr class="filas">
			<td colspan="4" align="center">
		<?
		for ($j=1; $j<=$total_paginas; $j++)
		{
			if ($j==1)
				echo '<span class="actual">'.$j.'</span>';
			else
				echo '<span style="cursor:pointer" onclick="busca($(\'cod_cta\').value,$(\'desc_cta\').value, '.$j.');"> - '.$j.'</span>';
		}
		?>
			</td>
		</tr>
		<tr class="filas">
			<td colspan="4" align="center"> Pagina <strong>1</strong> de <strong><?=$total_paginas?></strong></td>
		</tr>
	</table>
	<?
	}
	?>
<div>
<br />
<br />
<div style="height:40px;padding-top:10px;">
<p id="cargando" style="display:none;margin-top:0px;">
  <img alt="Cargando" src="images/loading.gif" /> Cargando...
</p>
</div>

<script type="text/javascript">
	
	var t;
	
	function buscador(codcta, descripcion, pagina, keyCode)
	{
		if ((keyCode>=65 && keyCode<=90) || (keyCode>=48 && keyCode<=57) || (keyCode>=96 && keyCode<=105) || keyCode==8 || keyCode==46)
		{
			clearTimeout(t);
			$('cod_cta').value = codcta;
			$('desc_cta').value = descripcion;
			t = setTimeout("busca('"+codcta+"','"+descripcion+"','"+pagina+"')", 800);
		}
	}
	
	function busca(codcta, descripcion, pagina){
	var url = 'updater_busca_plan_cuenta.php';
	var pars = '&codigo_cuenta=' + codcta + '&descrip_cuenta=' + descripcion + '&ms='+new Date().getTime()+ '&pagina='+pagina;
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

Event.observe('codigo_cuenta', "keyup", function (evt) { 
	buscador($F('codigo_cuenta'), $F('descrip_cuenta'), 1, evt.keyCode); 
});
Event.observe('descrip_cuenta', "keyup", function (evt) { 
     buscador($F('codigo_cuenta'), $F('descrip_cuenta'), 1, evt.keyCode); 
});

</script>

<?
$validator->create_message("error_codcta", "codcta", "*");
$validator->create_message("error_desc", "descripcion", "*");
//$validator->create_message("error_esc", "escenarios", "*");
$validator->create_message("error_saldo_inicial", "saldo_inicial", "*");
$validator->create_message("error_nominal", "nominal", "*", 2);
$validator->create_message("error_movim", "movim", "*", 2);
$validator->create_message("error_natu", "naturaleza", "*", 2);
$validator->print_script();
?>
<? require ("comun/footer.php"); ?>