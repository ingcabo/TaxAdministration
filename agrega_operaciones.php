<?
require ("comun/ini.php");
$oUsuarios = new usuarios;
require ("comun/header.php");
?>
<span class="titulo_maestro">Operaciones por usuario</span>
<br /><br />
<span class="titulo_maestro">
	<?=helpers::combo_us($conn,'usuarios', '', 'nombre', 'usuarios','usuarios', 'traePermisos($(\'usuarios\').value)');?>
</span>
<div id="formulario">Seleccione un usuario del combo</div>
<div style="height:40px;padding-top:10px;">
<p id="ok" style="display:none;margin-top:0px;">
  Permiso modificado
</p>
</div>
<script type="text/javascript">
function traePermisos(id_usuario){
	var url = 'updater_operaciones.php';
	var pars = 'id_usuario=' + id_usuario;
	var updater = new Ajax.Updater('formulario', url,{
		parameters: pars,
		asynchronous:true, 
		evalScripts:true,
		onLoading:function(request){Element.show('cargando')}, 
		onComplete:function(request){Element.hide('cargando')},
		onSuccess:function(){ 
			new Effect.Highlight('formulario', {startcolor:'#fff4f4', endcolor:'#ffffff'});
		} 
	}); 
} 
function setPermisos(id_usuario, id_operacion){
	var url = 'updater_operaciones.php';
	var pars = 'id_usuario=' + id_usuario + '&id_operacion=' + id_operacion;
	var setPermiso = new Ajax.Request(
		url,
		{
			method: 'get',
			parameters: pars,
			onComplete:function(request){Element.show('ok')}
		}
	); 
} 
<?=(!empty($msj)) ? "Effect.Appear('msj',{duration:1.2});\n" : ""?>
</script>
<? require ("comun/footer.php"); ?>
