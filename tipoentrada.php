<?
require ("comun/ini.php");
// Creando el objeto Tipo de Entrada
$oTipoentrada = new tipoentrada;

$accion = $_REQUEST['accion'];

$precio=$_REQUEST['precio'];
	if(empty($precio)){ $precio=0; }
$imp=$_REQUEST['imp'];
if(empty($imp)){ $imp=0; }

if($accion == 'Guardar'){
	if($oTipoentrada->add($conn, $_REQUEST['descripcion'] ,$_REQUEST['mpersona'], $precio, $imp, guardafloat($_REQUEST['aforo'])))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
}elseif($accion == 'Actualizar'){
	if($oTipoentrada->set($conn, $_REQUEST['id'], $_REQUEST['descripcion'] ,$_REQUEST['mpersona'], $precio, $imp, guardafloat($_REQUEST['aforo'])))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}elseif($accion == 'del'){
	if($oTipoentrada->del($conn, $_REQUEST['id']))
		$msj = REG_DEL_OK;
	else
		$msj = ERROR;
}
$cTipoentrada=$oTipoentrada->get_all($conn);
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:none;"><?=$msj?></div><? echo "<br>"; } ?>
<br />
<span class="titulo_maestro">Maestro de Tipo de Entrada</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />

<? if(is_array($cTipoentrada)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1" align="center">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Descripcion</td>
<td>Maximo de Personas</td>
<td>Tiene Precio</td>
<td>Exento de Impuesto</td>
<td>Aforo</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cTipoentrada as $tipoentrada) { 
?> 
<tr class="filas"> 
<td><?=$tipoentrada->id?></td>
<td><?=$tipoentrada->descripcion?></td>
<td><?=$tipoentrada->mpersona?></td>
<td align="center"><?php if($tipoentrada->precio==1) { echo "Con Precio"; }else{ echo "Sin Precio"; } ?></td>
<td align="center"><?php if($tipoentrada->imp==1) { echo "No Excento"; }else{ echo "Esta Excento"; } ?></td>
<td><?=muestrafloat($tipoentrada->aforo)?></td>
<td align="center">
<a href="#" onclick="updater('<?=$tipoentrada->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
<td align="center">
<a href="tipoentrada.php?accion=del&id=<?=$tipoentrada->id?>" title="Modificar ó Actualizar Registro" ><img src="images/eliminar.gif" width="16" height="10" border="0"></a></td>
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

<?
$validator->create_message("error_mpersona", "mpersona", "*");
$validator->create_message("error_descripcion", "descripcion", "*");
$validator->print_script();
require ("comun/footer.php"); ?>