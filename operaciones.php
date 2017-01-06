<?
require ("comun/ini.php");
// Creando el objeto operaciones
$oOperaciones = new operaciones;
$accion = $_REQUEST['accion'];
if($accion == 'Guardar'){
	if($oOperaciones->add($conn, 
											$_POST['descripcion'], 
											$_POST['pagina'], 
											$_POST['modulos'],
											$_POST['padre'],
											$_POST['tipo'],
											$_POST['nivel1'],
											$_POST['orden']
											))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
}elseif($accion == 'Actualizar'){
	if($oOperaciones->set($conn, 
											$_POST['id'],
											$_POST['descripcion'], 
											$_POST['pagina'], 
											$_POST['modulos'],
											$_POST['padre'],
											$_POST['tipo'],
											$_POST['nivel1'],
											$_POST['orden']
											))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}elseif($accion == 'del'){
	if($oOperaciones->del($conn, $_REQUEST['id']))
		$msj = REG_DEL_OK;
	else
		$msj = ERROR;
}
if($accion){
?>	
<script language="JavaScript">window.parent.menu.location = "menu.php";</script>
<?
}
$cModulos = $oOperaciones->getAllMods($conn);
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<br />
<span class="titulo_maestro">Maestro de Operaciones</span>

<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />

<? foreach($cModulos as $modulo){ ?>
<div id="<?=$modulo->descripcion?>" style="margin-bottom:15px;">
<span class="titulo_maestro" >Operaciones m&oacute;dulo <?=$modulo->descripcion?></span><br /><br />
	<?
	$cOperaciones=$oOperaciones->get_all($conn, $modulo->id);
	if(is_array($cOperaciones)){
	?>
	<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
	<tr class="cabecera"> 
	<td width="7%">C&oacute;digo</td>
	<td width="42%">Descripci&oacute;n</td>
	<td  width="42%">Pertenece</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	</tr>
	<? 
	$i = 0;
	foreach($cOperaciones as $operaciones) { 
	?> 
	<tr class="filas"> 
	<td><?=$operaciones->id?></td>
	<td><?=$operaciones->descripcion?></td>
	<td><?=$operaciones->nom_padre?></td>
	<td><a href="?accion=del&id=<?=$operaciones->id?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
	<td align="center">
	<a href="#" onclick="updater('<?=$operaciones->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
	</tr>
	<? $i++;
		}
	?>
	</table>
	<? }else {
			echo "No hay registros en la bd";
	} ?>
</div>
<? } ?>


<script type="text/javascript">
function traeCarpetasDesdeUpdater(id_modulo){
	var url = 'updater_selects.php';
	var pars = 'combo=padreOperaciones&id_modulo=' + id_modulo;
	var updater = new Ajax.Updater('cont_carpetas', 
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
function desactivaNivel1(){
	if($F('padre') == 0 )
		$('nivel1').disabled = false; 
	else
		$('nivel1').disabled = true;
}
</script>
<br />
<div style="height:40px;padding-top:10px;">
<p id="cargando" style="display:none;margin-top:0px;">
  <img alt="Cargando" src="images/loading.gif" /> Cargando...
</p>
</div>
<!-- <a href="#" onclick="alert($('formulario').innerHTML)">AAAA</a> -->
<?
$validator->create_message("error_desc", "descripcion", "*");
$validator->create_message("error_pag", "pagina", "*");
$validator->create_message("error_mod", "modulos", "*");
$validator->create_message("error_orden", "orden", "*");
$validator->print_script();
require ("comun/footer.php");
?>
