<?php 

require ("comun/ini.php");

$cod_des=@$_REQUEST['cod_des'];
$descripcion=strtoupper(@$_REQUEST['descripcion']);
$status=@$_REQUEST['status'];
$text_status='';

if(empty($status)){
$status=0;
}
	#GUARDO
	if(!empty($_REQUEST['accion']) and $_REQUEST['accion']=='Guardar' and !empty($descripcion)){
		$sql="INSERT INTO vehiculo.mot_desincorporacion (descripcion, status) VALUES ('$descripcion', $status)";
		$msj="Registro Guardado con �xito";
		
	}
	#ACTUALIZO
	if(!empty($_REQUEST['accion']) and $_REQUEST['accion']=='Actualizar' and !empty($descripcion)){
		//echo $cod_col."....".$descripcion."....".$status;
		$sql="UPDATE vehiculo.mot_desincorporacion SET descripcion='$descripcion', status=".$status." WHERE cod_des=".$cod_des;
		$msj="Registro Actualizado con �xito";
	}
		$conn->Execute($sql);

$sql="SELECT * FROM vehiculo.mot_desincorporacion ORDER BY descripcion ASC";
$rs = $conn->Execute($sql);
print $conn->ErrorMsg();

require ("comun/header.php");
$div="<div id='formulario'><a href='#' onclick=\"gettpl('')\";>Agregar Nuevo Registro</a></div>";
if(!empty($msj)){ ?><div id="msj" style="display:none;"><?=$msj?></div><? echo "<br>"; }
?>
	<script type="text/javascript">
	function gettpl(id)
	{
		var url = 'mot_desincorporacion.tpl.php';
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
		
		<!-- end header -->

<br />
<span class="titulo_maestro">Maestro de Motivos de Desincorporaci&oacute;n</span>
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
<td><?=$rs->fields['cod_des']?></td>
<td><?=$rs->fields['descripcion']?></td>
<td align="center"><?=$text_status?></td>
<td align="center"><a href="#" onclick="gettpl(<?=$rs->fields['cod_des']?>);"><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
</tr>
<?php 
			$rs->MoveNext();
		}
?>
</table>

<?
$validator->create_message("error_desc", "descripcion", "*");
$validator->print_script();
?>

<!--begin footer-->
<? require ("comun/footer.php"); ?>
