<?php #Se7h _-·[mar][2006]
require ("comun/ini.php");
$cod_col=@$_REQUEST['cod_col'];
$descripcion=strtoupper(@$_REQUEST['descripcion']);
$status=@$_REQUEST['status'];
$text_status='';

if(empty($status)){
$status=0;
}
	#GUARDO
	if(!empty($_REQUEST['accion']) and $_REQUEST['accion']=='Guardar'){
		$sql="INSERT INTO vehiculo.colores (descripcion, status) VALUES ('$descripcion', $status)";
		$msj="Registro Guardado con éxito";
		
	}
	#ACTUALIZO
	if(!empty($_REQUEST['accion']) and $_REQUEST['accion']=='Actualizar'){
		//echo $cod_col."....".$descripcion."....".$status;
		$sql="UPDATE vehiculo.colores SET descripcion='$descripcion', status=".$status." WHERE cod_col=".$cod_col;
		$msj="Registro Actualizado con éxito";
	}
		$conn->Execute($sql);

$sql_col="SELECT * FROM vehiculo.colores ORDER BY descripcion ASC";
$rs = $conn->Execute($sql_col);
print $conn->ErrorMsg();



require ("comun/header.php");

$div="<div id='formulario'><a href='#' onclick=\"gettpl('')\";>Agregar Nuevo Registro</a></div>";


if(!empty($msj)){ ?><div id="msj" style="display:none;"><?=$msj?></div><? echo "<br>"; }
?>

	<script type="text/javascript">
	function gettpl(id)
	{
		var url = 'color.tpl.php';
		var pars = 'id='+id;
		
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

<span class="titulo_maestro">Maestro de Color</span>
<?=$div?>
<br />



<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td width="9%">C&oacute;digo</td>
<td width="63%">Descripci&oacute;n</td>
<td width="14%">Status</td>
<td width="14%">&nbsp;</td>
</tr>
<?php 	while (!$rs->EOF) { 
if($rs->fields['status']==1) { $text_status='Activo'; }else{ $text_status='Inactivo'; }
?>
<tr class="filas"> 
<td><?=$rs->fields['cod_col']?></td>
<td><?=$rs->fields['descripcion']?></td>
<td align="center"><?=$text_status?></td>
<td align="center"><a href="#" onclick="gettpl(<?=$rs->fields['cod_col']?>);"><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
</tr>
<?php 
			$rs->MoveNext();
		}
?>
</table>

<!--begin footer-->
<? require ("comun/footer.php"); ?>