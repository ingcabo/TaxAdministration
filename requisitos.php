<?
include('adodb/adodb-exceptions.inc.php');
require ("comun/ini.php");
$pagina = $_REQUEST['pagina'];
$tamano_pagina = 10;
if (!$pagina)
{
	$inicio = 0;
	$pagina=1;
}
else 
	$inicio = ($pagina - 1) * tamano_pagina;

// Creando el objeto requisitos
$oRequisitos = new requisitos;
$accion = $_REQUEST['accion'];
$vencido=$_REQUEST['vencido'];
$today=date("Y-m-d");
if($vencido!='TRUE'){$vencido='FALSE';}
if($accion == 'Guardar'){

	$oRequisitos->add($conn, 
										$_REQUEST['nombre'], 
										$_REQUEST['descripcion'],
										$today,
										$vencido);
}elseif($accion == 'Actualizar'){

	$oRequisitos->set($conn, $_REQUEST['id'], 
										$_REQUEST['nombre'], 
										$_REQUEST['descripcion'],
										$vencido, $today);
	
}elseif($accion == 'del'){
	$oRequisitos->del($conn, $_REQUEST['id']);
		
}
//Seccion paginador


//$cRequisitos=$oRequisitos->get_all($conn, $start_record,$page_size);

$cRequisitos = $oRequisitos->buscar($conn, "", $tamano_pagina, $inicio, "nombre");
$total = requisitos::total_registro_busqueda($conn, "");

require ("comun/header.php");
?>
<? if(!empty($oRequisitos->msj)){ ?><div id="msj" style="display:'';"><?= $oRequisitos->msj?></div><br /><? }elseif(empty($oRequisitos->msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<br />
<span class="titulo_maestro">Maestro de requisitos</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />
<fieldset id="buscador">
	<legend>Buscar:</legend>
	<table>
		<td>Nombre:</td>
		<td>
			<input type="text" name="nombre" id="nombre" />
			<input type="hidden" name="hidden_nombre" id="hidden_nombre" />
		</td>
	</table>
</fieldset>
<br />
<div id="busqueda">
<?
if(is_array($cRequisitos) && count($cRequisitos)>0)
{ 
?>
<table id="grid" cellpadding="0" cellspacing="1">
	<tr class="cabecera"> 
		<td width="15%">C&oacute;digo</td>
		<td>Nombre</td>
		<td width="5%">&nbsp;</td>
		<td width="5%">&nbsp;</td>
	</tr>
	<? 
	foreach($cRequisitos as $requisitos) 
	{ 
	?> 
	<tr class="filas"> 
		<td><?=$requisitos->id?></td>
		<td><?=$requisitos->nombre?></td>
		<td align="center"><a href="?accion=del&id=<?=$requisitos->id?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
		<td align="center"><a href="#" onclick="updater('<?=$requisitos->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
	</tr>
	<?
	}

	$total_paginas = ceil($total / $tamano_pagina);
	?>
	<tr class="filas">
		<td colspan="7" align="center">
	<?
	for ($j=1;$j<=$total_paginas;$j++)
	{
		if ($j==1)
			echo '<span class="actual">'.$j.'</span>';
		else
			echo '<span style="cursor:pointer" onclick="busca($(\'hidden_nombre\').value, '.$j.');">'.($j>1 ? ' - ':'').$j.'</span>';
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
else
	echo "No hay registros en la bd";
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
function buscador(nombre, pagina, code)
{
	if ((code>=48 && code<=57) || (code>=96 && code<=105) || (code>=65 && code<=90) || code==8 || code==13 || code==46)
	{
		clearTimeout(t);
		$('hidden_nombre').value = nombre;
		t = setTimeout("busca('"+nombre+"', "+pagina+")", 800);
	}
}
	
function busca(nombre, pagina)
{
	var url = 'updater_busca_requisitos.php';
	var pars = 'nombre=' + nombre + '&pagina=' + pagina + '&ms='+new Date().getTime();
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
	
Event.observe('nombre', "keyup", function (evt) { 
     buscador($F('nombre'), 1, evt.keyCode); 
});
</script>
<? 
//$validator->create_message("error_cod", "id_nuevo", "*");
$validator->create_message("error_nombre", "nombre", "*");
$validator->print_script();
require ("comun/footer.php");?>
