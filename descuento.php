<?php 
#INCOMPLETO
require ("comun/ini.php");
$today=date('Y-m-d');
$cod_des=@$_REQUEST['cod_des'];
$dias=$_REQUEST['dias'];
$porcentaje=$_REQUEST['porcentaje'];

	#GUARDO
	if(!empty($_REQUEST['accion']) and $_REQUEST['accion']=='Guardar'){
		$sql="INSERT INTO vehiculo.descuento (dias, fecha, porcentaje) VALUES ('$dias', '$today', $porcentaje)";
		$msj="Registro Guardado con éxito";
		
	}
	#ACTUALIZO
	if(!empty($_REQUEST['accion']) and $_REQUEST['accion']=='Actualizar'){
		//echo $cod_col."....".$descripcion."....".$status;
		$sql="UPDATE vehiculo.descuento SET dias=$dias, porcentaje=$porcentaje WHERE cod_des=".$cod_des;
		$msj="Registro Actualizado con éxito";
	}
		
		$conn->Execute($sql);

$sql="SELECT * FROM vehiculo.descuento ORDER BY cod_des ASC";
$rs = $conn->Execute($sql);
print $conn->ErrorMsg();

require ("comun/header.php");
$div="<div id='formulario'><a href='#' onclick=\"gettpl('')\";>Agregar Nuevo Registro</a></div>";
if(!empty($msj)){ ?><div id="msj" style="display:none;"><?=$msj?></div><? echo "<br>"; }
?>
	<script type="text/javascript">
	function gettpl(id)
	{
		var url = 'descuento.tpl.php';
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
<span class="titulo_maestro">Maestro de Porcentajes de Descuentos por D&iacute;as</span>
<?=$div?>
<br />



<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td width="9%">C&oacute;digo</td>
<td width="63%">D&iacute;as</td>
<td width="14%">Porcetaje</td>
<td width="14%">&nbsp;</td>
</tr>
<?php 	while (!$rs->EOF) { ?>
<tr class="filas"> 
<td><?=$rs->fields['cod_des']?></td>
<td><?=$rs->fields['dias']?></td>
<td align="center"><?=$rs->fields['porcentaje']?>%</td>
<td align="center"><a href="#" onclick="gettpl(<?=$rs->fields['cod_des']?>);"><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
</tr>
<?php 
			$rs->MoveNext();
		}
?>
</table>

<!--begin footer-->
<? require ("comun/footer.php"); ?>