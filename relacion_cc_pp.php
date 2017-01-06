<?
require ("comun/ini.php");

$oRelacion_cc_pp = new relacion_cc_pp;
//die($_REQUEST['accion']);
$accion = $_REQUEST['accion'];
$tamano_pagina = 20;

if($accion == 'Guardar')
{
	$msj = $oRelacion_cc_pp->add($conn, $_REQUEST['plan_cuenta'], $_REQUEST['partidas_presupuestarias'], $escEnEje);
	if($msj === true)
		$msj = REG_ADD_OK;
	else if ($msj == 'Duplicado')
		$msj = DUPLICATED;
	else
		$msj = ERROR;
}
elseif($accion == 'Actualizar')
{
	if($oRelacion_cc_pp->set($conn,$_REQUEST['id'], $_REQUEST['plan_cuenta'], $_REQUEST['partidas_presupuestarias'], $escEnEje))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}
elseif($accion == 'del')
{

	if($oRelacion_cc_pp->del($conn, $_REQUEST['id']))
		$msj = REG_DEL_OK;
	else
		$msj = 'Imposible Eliminar el Registro. Existe(n) Asiento(s) Contables(s) Pertenenciente(s) a la Relacion';
}



require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<br />
<span class="titulo_maestro">Maestro Relaci&oacute;n Cuentas Contables - Partidas Presupuestarias </span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />
<br />
<div>
<fieldset id="buscador">
	<legend>Buscar:</legend>
	<table width="300px">
		<!--tr>
			<td>Escenario</td>
		</tr>
		<tr>
			<td>
				<?=helpers::superComboSQL($conn, 
												'escenarios',
												0,
												'busca_escenarios',
												'busca_escenarios',
												'',
												'buscador()',
												'id',
												'descripcion')?>
			</td>
		</tr-->
		<tr>
			<td>Cuenta Contable</td>
		</tr>
		<tr>
			<td>
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
				<input type="hidden" name="hidden_cc" id="hidden_cc" />
			</td>
		</tr>
		<tr>
			<td>Partida Presupuestaria</td>
		</tr>
		<tr>
			<td>
				<?=helpers::superComboSQL($conn, 
											'',
											0,
											'busca_pp',
											'busca_pp', 
											'', 
											'buscador()', 
											'id', 
											'descripcion', 
											false,
											'', 
											"SELECT id, (id || ' - ' || descripcion)::varchar AS descripcion FROM partidas_presupuestarias WHERE id_escenario=$escEnEje AND madre = 0 ORDER BY id",
											'80')?>
				<input type="hidden" name="hidden_pp" id="hidden_pp" />
			</td>
		</tr>
	</table>
</fieldset>
</div>
<br />
<div style="margin-bottom:10px" id="busqueda">
	<?
	$cRelacion_cc_pp = $oRelacion_cc_pp->buscar($conn, $cc, $pp,$escenario, $tamano_pagina, 1);
	$total = $oRelacion_cc_pp->total_registro_busqueda($conn, $cc, $pp,$escenario);
	
	if(is_array($cRelacion_cc_pp) && count($cRelacion_cc_pp)>0)
	{
	?>
	<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
		<tr class="cabecera"> 
			<td width="45%">Cuentas Contables</td>
			<td width="45%">Partidas Presupuestarias</td>
			<td width="5%">&nbsp;</td>
			<td width="5%">&nbsp;</td>
	</tr>
	<?
		foreach($cRelacion_cc_pp as $relacion_cc_pp) 
		{ 
		?> 
		<tr class="filas"> 
			<td><?=$relacion_cc_pp->cuenta_contable->codcta.' - '.$relacion_cc_pp->cuenta_contable->descripcion?></td>
			<td><?=$relacion_cc_pp->partida_presupuestaria->id.' - '.$relacion_cc_pp->partida_presupuestaria->descripcion?></td>
			<td align="center"><a href="?accion=del&id=<?=$relacion_cc_pp->id?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
			<td align="center"><a href="#" onclick="updater('<?=$relacion_cc_pp->id?>&id_escenario=<?=$relacion_cc_pp->id_escenario?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
		</tr>
		<?
		}
		$total_paginas = ceil($total / $tamano_pagina);
		?>
		<tr class="filas">
			<td colspan="7" align="center">
		<? 
		for ($j=1; $j<=$total_paginas; $j++)
		{
			if ($j==1)
				echo '<span class="actual">'.($j>1 ? ' - ':'').$j.'</span>';
			else
				echo "<span style=\"cursor:pointer\" onclick=\"busca($('hidden_cc').value , $('hidden_pp').value, ".$j.");\"> - ".$j."</span>";
		}
		?>
			</td>
		</tr>
		<tr class="filas">
			<td colspan="7" align="center"> Pagina <strong>1</strong> de <strong><?=$total_paginas?></strong></td>
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
	
	function buscador(value)
	{
		$('hidden_cc').value = $('busca_cc').value
		$('hidden_pp').value = $('busca_pp').value
		busca($('busca_cc').value, $('busca_pp').value, '1'); 
	}
	
	function busca(cc, pp, pagina){
	var url = 'updater_busca_relacion_cc_pp.php';
	var pars = '&cc=' + cc + '&pp=' + pp + '&escenario=' + <?=$escEnEje?> +'&ms='+new Date().getTime()+ '&pagina='+pagina;
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

function traeParPreDesdeUpdater(escenario){
	var url = 'updater_selects.php';
	var pars = 'combo=parprePorEsc&escenario=' + escenario + '&madre=1&relacion=1';
	var updater = new Ajax.Updater('cont_partidas' , 
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

function traeCCDesdeUpdater(escenario){
	var url = 'updater_selects.php';
	var pars = 'combo=plan_cuentas&id_escenario=' + escenario+'&movim=S&name=plan_cuenta&id=plan_cuenta&relacion=1';
	var updater = new Ajax.Updater('cont_plan_cuenta' , 
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

function traeCuentasContables(){
	
	var url = 'buscar_cuentas.php';
	var pars = 'id_cuenta='+$('plan_cuenta').value+'&ms='+new Date().getTime();
		
	var Request = new Ajax.Request(
		url,
		{
			method: 'get',
			parameters: pars,
			onLoading:function(request){}, 
			onComplete:function(request){
				
				Dialog.closeInfo();
				Dialog.alert(request.responseText, {windowParameters: {width:600, height:400, 
								showEffect:Element.show,hideEffect:Element.hide,
								showEffectOptions: { duration: 1}, hideEffectOptions: { duration:1 }
								
								}});
				
				}
			}
	);     	   
}

function traeCuentasContablesDesc(){
	
	var url = 'buscar_cuentas.php';
	var pars = 'descripcion='+$('search_descrip').value+'&id_cuenta='+$('plan_cuenta').value+'&ms='+new Date().getTime();
		
	var Request = new Ajax.Request(
		url,
		{
			method: 'get',
			parameters: pars,
			onLoading:function(request){}, 
			onComplete:function(request){
				
				Dialog.closeInfo();
				Dialog.alert(request.responseText, {windowParameters: {width:600, height:400, 
								showEffect:Element.show,hideEffect:Element.hide,
								showEffectOptions: { duration: 1}, hideEffectOptions: { duration:1 }
								
								}});
				
				}
			}
	);     	   
}

	function selDocumento(id, nombre){
	
	$('txtCuentaContable').value = nombre;
	$('plan_cuenta').value = id;
	Dialog.okCallback();

}

var t;

	function busca_popup()
{
	clearTimeout(t);
	t = setTimeout('traeCuentasContablesDesc()', 800);
}

	function traePartidasPresupuestarias(){
	
	var url = 'buscar_partidas.php';
	var pars = 'ms='+new Date().getTime();
		
	var Request = new Ajax.Request(
		url,
		{
			method: 'get',
			parameters: pars,
			onLoading:function(request){}, 
			onComplete:function(request){
				
				Dialog.closeInfo();
				Dialog.alert(request.responseText, {windowParameters: {width:600, height:400, 
								showEffect:Element.show,hideEffect:Element.hide,
								showEffectOptions: { duration: 1}, hideEffectOptions: { duration:1 }
								
								}});
				
				}

			}
	);     	   
}

function busca_popup_pp()
{
	clearTimeout(t);
	t = setTimeout('buscaPartidasPresupuestarias()', 800);
}

function buscaPartidasPresupuestarias()
{
	var url = 'buscar_partidas.php';
	var pars = 'nombre='+$('search_nombre_pp').value+'&codigo='+$('search_cod_pp').value+'&opcion=2&ms='+new Date().getTime();
		
	var updater = new Ajax.Updater('divPartidas', 
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

	function selPartidas(id, nombre){
	
	$('txtpartidas_presupuestarias').value = nombre;
	$('partidas_presupuestarias').value = id;
	Dialog.okCallback();

}

	function traerDisponiblePartidas(a,b){
	 var x = a;
	}


</script>

<?
//$validator->create_message("error_esc", "escenarios", "*");
$validator->create_message("error_plan_cuenta", "plan_cuenta", "*");
$validator->create_message("error_parpre", "partidas_presupuestarias", "*");
$validator->print_script();
?>
<? require ("comun/footer.php"); ?>