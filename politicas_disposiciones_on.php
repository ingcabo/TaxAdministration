<?
require ("comun/ini.php");
// Creando el oPoliticasDisposiciones politicas_disposiciones
$oPoliticasDisposiciones = new politicas_disposiciones;
$accion = $_REQUEST['accion'];
$id = $_REQUEST['id'];

if(!empty($id)){
	$boton="Actualizar";
	$oPoliticasDisposiciones->get($conn, $id);
}else
	$boton = "Guardar";

//Seccion paginador
$page_size = 25;
if ($_GET['pg'])
	$start_record=($_GET['pg'] * $page_size) - $page_size;
else
	$start_record=0;

$cPoliticasDisposiciones=$oPoliticasDisposiciones->get_all($conn, $start_record,$page_size);
$pag=new paginator($oPoliticasDisposiciones->total,$page_size, self($_SERVER['SCRIPT_NAME']));
$i=$pag->get_total_pages();
require ("comun/header.php");
?>
<script src="js/tabpanel.js" type="text/javascript"></script>
<? if(!empty($msj)){ ?><div id="msj" style="display:none;"><?=$msj?></div><? } ?>
<br />
<span class="titulo_maestro">Maestro de Politicas / Disposiciones</span>
<div id="formulario">

<form name="form1" action="politicas_disposiciones.php" method="post">
<table>
<tr>
	<td>Escenario:</td>
	<td>
		<?=helpers::combo($conn, 'escenarios', $oPoliticasDisposiciones->id_escenario)?>
		<span class="errormsg" id="error_esc">*</span>
		<?=$validator->show("error_esc")?>
	</td>
</tr>
<tr>
	<td>A&ntilde;o:</td>
	<td>
		<input name="ano" onkeypress="return onlyNumbersCI(event)" maxlength="4" size="10" type="text" value="<?=$oPoliticasDisposiciones->ano?>">
		<span class="errormsg" id="error_ano">*</span>
		<?=$validator->show("error_ano")?>
	</td>
</tr>
<tr>
	<td>Tipo de Gaceta:</td>
	<td>
		<?=helpers::combo($conn, 'gacetas', $oPoliticasDisposiciones->id_tipo_gaceta)?>
		<span class="errormsg" id="error_gac">*</span>
		<?=$validator->show("error_gac")?>
	</td>
</tr>
</table>
<table>
<tr>
<td>
<div class="tab-page" id="modules-cpanel" style="width:600px"> 
	<script type="text/javascript">
	  var tabPane1 = new WebFXTabPane($("modules-cpanel"), 0)
	</script>
	<div class="tab-page" id="texto1" >
	  <h2 class="tab">TEXTO</h2>
	  <script type="text/javascript">
	    tabPane1.addTabPage($("texto1"))
	  </script>
		<textarea name="texto1" style="width:100%" rows="10"><?=$oPoliticasDisposiciones->texto1?></textarea>
	</div>
	<div class="tab-page" id="texto2">
	  <h2 class="tab">TEXTO</h2>
	  <script type="text/javascript">
	    tabPane1.addTabPage($("texto2"))
	  </script>
		<textarea name="texto2" style="width:100%" rows="10"><?=$oPoliticasDisposiciones->texto2?></textarea>
	</div>
	<div class="tab-page" id="texto3">
	  <h2 class="tab">TEXTO</h2>
	  <script type="text/javascript">
	    tabPane1.addTabPage($("texto3"))
	  </script>
	  <textarea name="texto3" style="width:100%" rows="10"><?=$oPoliticasDisposiciones->texto3?></textarea>
	</div>
	<div class="tab-page" id="texto4">
	  <h2 class="tab">TEXTO</h2>
	  <script type="text/javascript">
	    tabPane1.addTabPage($("texto4"))
	  </script>
	  <textarea name="texto4" style="width:100%" rows="10"><?=$oPoliticasDisposiciones->texto4?></textarea>
	</div>

</td>
</tr>

<tr>
<td>

<input style="float:right" name="boton" type="button" value="<?=$boton?>" onclick="<?=$validator->validate() ?>" />
<input name="id" type="hidden" value="<?=$oPoliticasDisposiciones->id?>" />
<input name="accion" type="hidden" value="<?=$boton?>" />
<span style="position:absolute;top:4px;right:5px;cursor:pointer;">
<img onclick="close_div_pd();" src="images/close_div.gif" /></span>
</form>
<p class="errormsg">(*) Campo requerido</p>
</td>
</tr>

</table>

</div>
<br /><br />
<? if(is_array($cPoliticasDisposiciones)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Escenario</td>
<td>A&ntilde;o</td>
<td>Tipo de Gaceta</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cPoliticasDisposiciones as $politicasDisposiciones) { 
?> 
<tr class="filas"> 
<td><?=$politicasDisposiciones->id?></td>
<td><?=$politicasDisposiciones->escenario?></td>
<td><?=$politicasDisposiciones->ano?></td>
<td><?=$politicasDisposiciones->tipo_gaceta?></td>
<td><a href="politicas_disposiciones.php?accion=del&id=<?=$politicasDisposiciones->id?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
<td align="center">
<a href="?accion=detalle&id=<?=$politicasDisposiciones->id?>" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
</tr>
<? $i++;
	}
?>
</table>
<? }else {
		echo "No hay registros en la bd";
} ?>
<br />
<div style="height:40px;padding-top:10px;">
<p id="cargando" style="display:none;margin-top:0px;">
  <img alt="Cargando" src="images/loading.gif" /> Cargando...
</p>
</div>
<table width="762" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td><span class="paginator"><? $pag->print_page_counter()?></span></td>
		<td align="right"><span class="paginator"><? $pag->print_paginator("pulldown")?> </span></td>
	</tr>
</table>
<script type="text/javascript">
function close_div_pd(){
	$('formulario').innerHTML = '<a href="politicas_disposiciones_on.php">Agregar Nuevo Registro</a>';
}
</script>
<?
$validator->create_message("error_esc", "escenarios", "*");
$validator->create_message("error_ano", "ano", "*");
$validator->create_message("error_gac", "gacetas", "*");
$validator->print_script();
require ("comun/footer.php");
?>
