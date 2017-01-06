<?php 

require ("comun/ini.php");

$cod_mar=@$_REQUEST['cod_mar'];
$descripcion=strtoupper(@$_REQUEST['descripcion']);
$status=@$_REQUEST['status'];
$text_status='';

if(empty($status)){
$status=0;
}
	#GUARDO
	if(!empty($_REQUEST['accion']) and $_REQUEST['accion']=='Guardar'){
		$sql="INSERT INTO vehiculo.marca (descripcion, status) VALUES ('$descripcion', $status)";
		$msj="Registro Guardado con éxito";
		
	}
	#ACTUALIZO
	if(!empty($_REQUEST['accion']) and $_REQUEST['accion']=='Actualizar'){
		//echo $cod_col."....".$descripcion."....".$status;
		$sql="UPDATE vehiculo.marca SET descripcion='$descripcion', status=".$status." WHERE cod_mar=".$cod_mar;
		$msj="Registro Actualizado con éxito";
	}
		$conn->Execute($sql);

$sql="SELECT * FROM vehiculo.marca ORDER BY descripcion ASC";
$rs = $conn->Execute($sql);
print $conn->ErrorMsg();


require ("comun/header.php");

$div="<div id='formulario'><a href='#' onclick=\"gettpl('')\";>Agregar Nuevo Registro</a></div>";
if(!empty($msj)){ ?><div id="msj" style="display:none;"><?=$msj?></div><? echo "<br>"; }


?>

	<script type="text/javascript">
	function gettpl(id)
	{
		var url = 'marca.tpl.php';
		var pars = 'id='+id;
		
		var myAjax = new Ajax.Updater(
			'formulario', 
			url, 
			{
				method: 'get', 
				parameters: pars
			});
		
	}
	
	function close_dv(){
	$('formulario').innerHTML = "<a href='#' onclick=gettpl('') >Agregar Nuevo Registro</a>";
	}
</script>

<br />
<span class="titulo_maestro">Maestro de Marcas</span>
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
<td><?=$rs->fields['cod_mar']?></td>
<td><a href="#" onClick="popup('mod_mar.php?cod_mar=<?=$rs->fields['cod_mar']?>	','','width=725,height=560, resizable,scrollbars')"><?=$rs->fields['descripcion']?></a></td>
<td align="center"><?=$text_status?></td>
<td align="center"><a href="#" onclick="gettpl(<?=$rs->fields['cod_mar']?>);"><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
</tr>
<?php 
			$rs->MoveNext();
		}
?>
</table>

<!--begin footer-->
<? require ("comun/footer.php"); ?>