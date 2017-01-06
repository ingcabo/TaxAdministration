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
$ocargo = new cargo;
$accion = $_REQUEST['accion'];
if($accion == 'Guardar' and !empty($_REQUEST['codigo'])){
	$msj = $ocargo->add($conn, $_REQUEST['codigo'], $_REQUEST['nombre'], guardafloat($_REQUEST['sueldo']), $_REQUEST['cantidad'], $_REQUEST['estatus'], $_REQUEST['departamentos']);
}elseif($accion == 'Actualizar' and !empty($_REQUEST['codigo'])){
	$msj = $ocargo->set($conn, $_REQUEST['int_cod'], $_REQUEST['codigo'], $_REQUEST['nombre'], guardafloat($_REQUEST['sueldo']), $_REQUEST['cantidad'], $_REQUEST['estatus'], $_REQUEST['departamentos']);
}elseif($accion == 'del'){
	$msj = $ocargo->del($conn, $_REQUEST['int_cod']);
}

$ccargo=$ocargo->get_all($conn,'int_cod', $_POST['TipoB'], $_POST['textAux'],$_POST['TipoBE'],$num,$inicio);
$total = $ocargo->total_registro_busqueda($conn, $_POST['TipoB'], $_POST['textAux'],$_POST['TipoBE']);
require ("comun/header.php");

if(!empty($msj)){ ?><div id="msj" ><?=$msj?></div><? echo "<br>"; } ?>
<br />
<script>var mygrid,i=0</script>
<span class="titulo_maestro">Maestro de Cargos </span>
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
							<option value="1" <?=$_POST['TipoB']==1 ? "selected" : ""?>>Nombre</option>
							<option value="0" <?=$_POST['TipoB']==0 ? "selected" : ""?>>Codigo</option>
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
<? if(is_array($ccargo)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>Codigo</td>
<td>Nombre</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($ccargo as $cargo) { 
?> 
<tr class="filas"> 
<td><?=$cargo->car_cod?></td>
<td align="center"><?=$cargo->car_nom?></td>
<td align="center"><a href="?accion=del&int_cod=<?=$cargo->int_cod?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
<td align="center"><a href="#" onclick="updater('<?=$cargo->int_cod?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
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
$validator->create_message("error_sueldo", "sueldo", "Este campo no puede estar vacio");
$validator->create_message("error_cantidad", "cantidad", "Este campo no puede estar vacio");
$validator->print_script();
require ("comun/footer.php"); ?>
<script language="javascript" >
function CambiarSueldoTrabajador(SueldoAnterio,SueldoActual,Boton,Cargo){
	if(SueldoAnterio!=SueldoActual && Boton=='Actualizar'){
		if(confirm("Desea Actualizar los sueldos de los trabajadores asociados al cargo?")){
			JsonAux={"Cargo":parseInt(Cargo),"Sueldo":usaFloat(SueldoActual),"Forma":8,"Accion":0};
			var url = 'OtrosCalculos.php';
			var pars = 'JsonEnv=' + JsonAux.toJSONString();
			var Request = new Ajax.Request(
				url,
				{
					method: 'post',
					parameters: pars,
					//asynchronous:false, 
					onComplete:function(request){
						var JsonRec = eval( '(' + request.responseText + ')');
						if(!JsonRec)
							alert("A OCURRIDO UN ERROR")
					}
				}
			); 
		}
	}
}

function busca(busqueda, textAux, estatus, pagina,num)
{
	var url = 'updater_busca_cargo.php';
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
function Guardar(){
	var JsonAux;
	var Departamentos=new Array;

	mygrid.clearSelection()
	for(i=0;i<mygrid.getRowsNum();i++){
		Departamentos[i] = new Array;
		Departamentos[i][0]=mygrid.getRowId(i);
		Departamentos[i][1]=mygrid.cells(mygrid.getRowId(i),2).getValue();
	}
 	JsonAux={"departamentos":Departamentos};
	$("departamentos").value=JsonAux.toJSONString(); 
	validate();
}
</script>
