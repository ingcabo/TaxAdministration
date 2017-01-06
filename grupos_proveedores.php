<?
include('adodb/adodb-exceptions.inc.php');
require ("comun/ini.php");
$pagina = $_REQUEST['pagina'];
$tamano_pagina = 20;
if (!$pagina) 
{
	$inicio = 0;
	$pagina=1;
}
else 
	$inicio = ($pagina - 1) * $tamano_pagina;

// Creando el objeto grupos_proveedores
$oGruposProveedores = new grupos_proveedores;
$today=date("Y-m-d");
$accion = $_REQUEST['accion'];
if($accion == 'Guardar'){
	if($oGruposProveedores->add($conn, 
										$_REQUEST['nombre'],  
										$_REQUEST['descripcion'],
										$today,
										$_REQUEST['requisito']))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
}elseif($accion == 'Actualizar'){
	if($oGruposProveedores->set($conn,
										$_REQUEST['id'],
										$_REQUEST['nombre'],  
										$_REQUEST['descripcion'],
										$today,
										$_REQUEST['requisito']))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}elseif($accion == 'del'){
	$msj = $oGruposProveedores->del($conn, $_REQUEST['id']);
	if($msj===true)
		$msj = REG_DEL_OK;
	else if ($msj==ERROR_CATCH_VFK)
		$msj = "ERROR: No puede eliminar un grupo que contiene proveedores";
	else if($msj!=ERROR_CATCH_VUK)
		$msj = ERROR;
		
}
//Seccion paginador


$cGruposProveedores = $oGruposProveedores->buscar($conn, "", $tamano_pagina, $inicio, "nombre");
$total = grupos_proveedores::total_registro_busqueda($conn, "");

require ("comun/header.php");

?>
<div id="msj" <?=!empty($msj) ? '':'style="display:none;"'?>><?=$msj?></div><br />
<script type="text/javascript">var mygrid,i=0, ipp=0</script>
<br />
<span class="titulo_maestro">Maestro de Grupos de Proveedores</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />
<fieldset id="buscador">
	<legend>Buscar:</legend>
	<table>
		<tr>
			<td>Nombre:</td>
			<td>
				<input type="text" name="nombre" id="nombre" onkeyup="buscador(this.value, event)" />
				<input type="hidden" name="hidden_nombre" id="hidden_nombre" />
			</td>
		</tr>
	</table>
</fieldset>
<br />
<div id="busqueda">
<? 
if(is_array($cGruposProveedores) && count($cGruposProveedores) > 0)
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
	foreach($cGruposProveedores as $gruposProveedores)
	{ 
	?> 
	<tr class="filas"> 
		<td><?=$gruposProveedores->id?></td>
		<td><?=$gruposProveedores->nombre?></td>
		<td align="center"><a href="?accion=del&id=<?=$gruposProveedores->id?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
		<td align="center"><a href="#" onclick="updater('<?=$gruposProveedores->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
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
				echo '<span class="actual">'.$j.'</a>';
			else
				echo '<span style="cursor:pointer" onclick="busca($(\'hidden_nombre\').value, '.$j.');">'.($j>1 ? ' - ':'').$j.'</span>';
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
	echo "No hay registros en la bd";
?>
</div>
<br />
<div style="height:40px;padding-top:10px;">
<p id="cargando" style="display:none;margin-top:0px;">
  <img alt="Cargando" src="images/loading.gif" /> Cargando...
</p>
</div>
<? 
$validator->create_message("error_nombre", "nombre", "*");
$validator->print_script();
require ("comun/footer.php");?>
<script language="javascript" type="text/javascript">

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
	var url = 'updater_busca_grupos_proveedores.php';
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

function AgregarRP(){
	//alert($('unidad_ejecutora').value);
	
	if ($('requisitos').value =="0"){
		
		alert("Primero debe Seleccionar un Requisito.");
		return;
	}else{
		
		for(j=0;j<i;j++){
		
			if(mygridrp.getRowIndex(j)!=-1){
				
				if (mygridrp.cells(j,'0').getValue() == $('requisitos').value){
						
					alert('Este requisito ya ha sido seleccionada, por favor seleccione otra');
					return false;

				}
			}
		}
		
		/*mygridco.getCombo(0).put(JsonData[j]['id_categoria_programatica'],JsonData[j]['categoria_programatica']);
		mygridco.getCombo(1).put(JsonData[j]['id_partida_presupuestaria'],JsonData[j]['partida_presupuestaria']);*/
		mygridrp.addRow(i,$('requisitos').value);
		i++;
		
	}
}

	function EliminarRP(){
	mygridrp.deleteRow(mygridrp.getSelectedId());
	i--;
	
}

	function Guardar()
	{
		var JsonAux,requisito=new Array;
			mygridrp.clearSelection()
			if(i > 0){
				for(j=0;j<i;j++)
				{
					if((mygridrp.getRowIndex(j)!= -1))
					{
						requisito[j] = new Array;
						requisito[j][0]= mygridrp.cells(j,0).getValue();	
					}
				}
				JsonAux={"requisito":requisito};
				$("requisito").value=JsonAux.toJSONString();
				validate();
			} else {
				alert('Debe Seleccionar al menos un requisito para este grupo'); 
				}
	}
	
Event.observe('nombre', "keyup", function (evt) { 
     buscador($F('nombre'), 1, evt.keyCode); 
});

</script>