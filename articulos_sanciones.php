<?php 

require ("comun/ini.php");
$cod_articulo = $_REQUEST['cod_articulo'];
$descripcion = strtoupper($_REQUEST['descripcion']);
$monto = guardafloat($_REQUEST['monto']);
$status = $_REQUEST['status'];
//$text_status='';

if(empty($status)){
$status=0;
}
	#GUARDO
	if(!empty($_REQUEST['accion']) and $_REQUEST['accion']=='Guardar'){
		$sql="INSERT INTO publicidad.articulos_sanciones (descripcion, monto, status) VALUES ('$descripcion', '$monto', '$status')";
		$msj="Registro Guardado con éxito";
		
	}
	#ACTUALIZO
	if(!empty($_REQUEST['accion']) and $_REQUEST['accion']=='Actualizar'){
		
		$sql="UPDATE publicidad.articulos_sanciones SET descripcion='$descripcion', monto='$monto', status='$status' WHERE cod_articulo='$cod_articulo'";
		$msj="Registro Actualizado con éxito";
	}
		//die($sql);
		$conn->Execute($sql);

$sql="SELECT * FROM publicidad.articulos_sanciones ORDER BY descripcion ASC";
$rs = $conn->Execute($sql);
print $conn->ErrorMsg();

require ("comun/header.php");
$div="<div id='formulario'><a href='#' onclick=\"gettpl('')\";>Agregar Nuevo Registro</a></div>";
if(!empty($msj)){ ?><div id="msj" style="display:none;"><?=$msj?></div><? echo "<br>"; }
?>

	<script type="text/javascript">
	function gettpl(id)
	{
		var url = 'articulos_sanciones.tpl.php';
		var pars = 'id='+id;
		//$('pars').innerHTML = pars;
		
		var myAjax = new Ajax.Updater(
			'formulario', 
			url, 
			{
				method: 'get', 
				parameters: pars
			});
		
	}
	
	function close_div(){
	$('formulario').innerHTML = "<a href='#' onclick=gettpl('') >Agregar Nuevo Registro</a>";
	}
</script>

		
		<!-- end header -->

<br />
<span class="titulo_maestro">Maestro Art&iacute;culos</span>
<?=$div?>
<br />



<table align="center" class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Descripci&oacute;n</td>
<td>Monto</td>
<td>Status</td>
<td>&nbsp;</td>
</tr>
<?php 	while (!$rs->EOF) { 
if($rs->fields['status']==1) { $text_status='Activo'; }else{ $text_status='Inactivo'; }
?>
<tr class="filas"> 
<td><?=$rs->fields['cod_articulo']?></td>
<td><?=$rs->fields['descripcion']?></td>
<td align="right"><?=muestrafloat($rs->fields['monto'])." UT"?></td>
<td align="center"><?=$text_status?></td>
<td align="center"><a href="#" onclick="gettpl(<?=$rs->fields['cod_articulo']?>);"><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
</tr>
<?php 
			$rs->MoveNext();
		}
?>
</table>

<!--begin footer--><!--<div id="pars"></div>-->
<? require ("comun/footer.php"); ?>