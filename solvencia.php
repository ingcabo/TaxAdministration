<?php 
require ("comun/ini.php");
$id=@$_REQUEST['id'];
$anio=$_REQUEST['anio'];
$fecha_desde= $_REQUEST['fecha_desde'];
$fecha_hasta=$_REQUEST['fecha_hasta'];
$monto_normal=str_replace(',','.',str_replace('.','',$_REQUEST['monto_normal']));
$monto_habilitado=str_replace(',','.',str_replace('.','',$_REQUEST['monto_habilitado']));

	#GUARDO
	if(!empty($_REQUEST['accion']) and $_REQUEST['accion']=='Guardar'){
		$sql="INSERT INTO vehiculo.solvencia (anio, fecha_desde, fecha_hasta, monto_normal, monto_habilitado) 
			  VALUES ($anio, '".guardafecha($fecha_desde)."', '".guardafecha($fecha_hasta)."', '$monto_normal', '$monto_habilitado')";
		$msj="Registro Guardado con éxito";
				
	}
	#ACTUALIZO
	if(!empty($_REQUEST['accion']) and $_REQUEST['accion']=='Actualizar'){
		//echo $cod_col."....".$descripcion."....".$status;
		$sql="UPDATE vehiculo.solvencia SET anio=$anio, fecha_desde='".guardafecha($fecha_desde)."', fecha_hasta='".guardafecha($fecha_hasta)."',
			monto_normal='$monto_normal', monto_habilitado='$monto_habilitado' WHERE id=".$id;
		$msj="Registro Actualizado con éxito";

	}
		
		$conn->Execute($sql);

$sql="SELECT * FROM vehiculo.solvencia ORDER BY id ASC";
$rs = $conn->Execute($sql);
print $conn->ErrorMsg();

require ("comun/header.php");
$div="<div id='formulario'><a href='#' onclick=\"gettpl('')\";>Agregar Nuevo Registro</a></div>";
if(!empty($msj)){ ?><div id="msj" style="display:none;"><?=$msj?></div><? echo "<br>"; }
?>
	<script type="text/javascript">
	function gettpl(id)
	{
		var url = 'solvencia.tpl.php';
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
<span class="titulo_maestro">Maestro de Solvencias</span>
<?=$div?>
<br />



<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td width="5%">C&oacute;digo</td>
<td width="7%">A&ntilde;o</td>
<td width="13%">Desde</td>
<td width="13%">Hasta</td>
<td width="27%">Monto Normal</td>
<td width="24%">Monto Habilitado</td>
<td width="11%">&nbsp;</td>
</tr>
<?php 	while (!$rs->EOF) { 
if($rs->fields['status']==1) { $text_status='Activo'; }else{ $text_status='Inactivo'; }
?>
<tr class="filas"> 
<td><?=$rs->fields['id']?></td>
<td><?=$rs->fields['anio']?></td>
<td align="center"><?=muestrafecha($rs->fields['fecha_desde'])?></td>
<td align="center"><?=muestrafecha($rs->fields['fecha_hasta'])?></td>
<td align="center"><?=number_format($rs->fields['monto_normal'], 2, ',', '.')?></td>
<td align="center"><?=number_format($rs->fields['monto_habilitado'], 2, ',', '.')?></td>
<td align="center"><a href="#" onclick="gettpl(<?=$rs->fields['id']?>);"><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
</tr>
<?php 
			$rs->MoveNext();
		}
?>
</table>

<!--begin footer-->
<? require ("comun/footer.php"); ?>