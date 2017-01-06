<?
include('adodb/adodb-exceptions.inc.php');
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
$ovariable = new variable;
$accion = $_REQUEST['accion'];
if($accion == 'Guardar' and !empty($_REQUEST['codigo'])){
	$msj = $ovariable->add($conn, $_REQUEST['codigo'], $_REQUEST['nombre'], $_REQUEST['tipo'], $_REQUEST['estatus']);
}elseif($accion == 'Actualizar' and !empty($_REQUEST['codigo'])){
	$msj = $ovariable->set($conn, $_REQUEST['int_cod'], $_REQUEST['codigo'], $_REQUEST['nombre'], $_REQUEST['tipo'], $_REQUEST['estatus']);
}elseif($accion == 'del'){
	$msj =$ovariable->del($conn, $_REQUEST['int_cod']);
}

$cvariable=$ovariable->get_all($conn,'int_cod', $_POST['TipoB'], $_POST['textAux'],$_POST['TipoBE'],$num,$inicio);
$total = $ovariable->total_registro_busqueda($conn, $_POST['TipoB'], $_POST['textAux'],$_POST['TipoBE']);
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" ><?=$msj?></div><? echo "<br>"; } ?>
<br />
<span class="titulo_maestro">Maestro de Variables </span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />

<form name="formAux" method="post">
<table align="center" border="0" style="margin-left:10px" width="800px">
	<tr  >
		<td width="200px"></td>
		<td align="center">
			<table align="center" border="0" style="margin-left:10px" width="400px">
				<tr  >
					<td>Buscar por:</td>
					<td>
						<select name="TipoB" id="TipoB">
							<option value="0" <?=$_POST['TipoB']==0 ? "selected" : ""?>>C&oacute;digo</option>
							<option value="1" <?=$_POST['TipoB']==1 ? "selected" : ""?>>Nombre</option>
						</select>
					</td>
					<td><input type="text" name="textAux" value="<?=$_POST['textAux']?>" id="textAux"></td>
					<td><input type="submit" value="Buscar"></td>
				</tr>
				<tr>
					<td>Estatus:</td>
					<td colspan="3">
						<select name="TipoBE" onChange="document.formAux.submit();" id="TipoBE">
							<OPTION <?=$_POST['TipoBE']==0 ? "selected" : "" ?> value='0'>Activo</OPTION>
							<OPTION <?=$_POST['TipoBE']=='1' ? "selected" : "" ?> value='1'>Inactivo</OPTION>
						</select>
					</td>
				</tr>
			</table>
		</td>
		<td></td>
	</tr>
</table>
</form>

<div id="busqueda">
<? if(is_array($cvariable)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Nombre</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cvariable as $variable) {  ?> 
<tr class="filas"> 
<td><?=$variable->var_cod?></td>
<td align="center"><?=$variable->var_nom?></td>
<td align="center"><a href="?accion=del&int_cod=<?=$variable->int_cod?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
<td align="center"><a href="#" onclick="updater('<?=$variable->int_cod?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
</tr>
	<? $i++;
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
						<a href="" onclick="busca( $('TipoB').options[$('TipoB').selectedIndex].value, $('textAux').value, $('TipoBE').options[$('TipoBE').selectedIndex].value, '<?=$j?>', '<?=$num?>'); return false;"><?=(($j==1) ? '':' - ').$j?></a>
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
		}else {
		echo "No hay registros en la bd";
} ?>
</div>
<br />
<div style="height:40px;padding-top:10px;">
<p id="cargando" style="display:none;margin-top:0px;">
  <img alt="Cargando" src="images/loading.gif" /> Cargando...
</p>
</div>

<?
$validator->create_message("error_codigo", "codigo", "Este campo no puede estar vacio");
$validator->create_message("error_desc", "nombre", "Este campo no puede estar vacio");
$validator->print_script();
require ("comun/footer.php"); 
?>
<script language="javascript" >
function busca(busqueda, textAux, estatus, pagina,num)
{
	var url = 'updater_busca_variable.php';
	var pars = 'busqueda=' + busqueda +'&textAux='+textAux + '&estatus='+estatus +'&pagina='+pagina+'&num='+num;
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
</script>