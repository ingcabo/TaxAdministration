<?php 
#INCOMPLETO
require ("comun/ini.php");
$today=date('Y-m-d');
$cod_tas=@$_REQUEST['cod_tas'];
$mes=$_REQUEST['mes'];
$anio=$_REQUEST['anio'];
$monto=str_replace(',','.',str_replace('.','',$_REQUEST['monto']));



	#GUARDO
	if(!empty($_REQUEST['accion']) and $_REQUEST['accion']=='Guardar'){
		$sql="INSERT INTO vehiculo.tasa_bancaria (mes, anio, monto) VALUES ($mes, $anio, '$monto')";
		$msj="Registro Guardado con éxito";
	
	}
	#ACTUALIZO
	if(!empty($_REQUEST['accion']) and $_REQUEST['accion']=='Actualizar'){
		//echo $cod_col."....".$descripcion."....".$status;
		$sql="UPDATE vehiculo.tasa_bancaria SET mes=$mes, anio=$anio, monto='$monto' WHERE cod_tas=".$cod_tas;
		$msj="Registro Actualizado con éxito";
		//die($sql);
	}
		
		$conn->Execute($sql);

$sql="SELECT * FROM vehiculo.tasa_bancaria ORDER BY cod_tas ASC";
$rs = $conn->Execute($sql);
print $conn->ErrorMsg();

require ("comun/header.php");
$div="<div id='formulario'><a href='#' onclick=\"gettpl('')\";>Agregar Nuevo Registro</a></div>";
if(!empty($msj)){ ?><div id="msj" style="display:none;"><?=$msj?></div><? echo "<br>"; }
?>
	<script type="text/javascript">
	function gettpl(id)
	{
		var url = 'tasa_bancaria.tpl.php';
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
<span class="titulo_maestro">Maestro de Tasas Bancarias</span>
<?=$div?>
<br />



<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td width="5%">C&oacute;digo</td>
<td width="6%">Mes</td>
<td width="8%">A&ntilde;o</td>
<td width="69%">Monto</td>
<td width="12%">&nbsp;</td>
</tr>
<?php 	while (!$rs->EOF) { ?>
<tr class="filas"> 
<td><?=$rs->fields['cod_tas']?></td>
<td><?=$rs->fields['mes']?></td>
<td align="center"><?=$rs->fields['anio']?></td>
<td align="right"><?=number_format($rs->fields['monto'], 2, ',', '.')?></td>
<td align="center"><a href="#" onclick="gettpl(<?=$rs->fields['cod_tas']?>);"><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
</tr>
<?php 
			$rs->MoveNext();
		}
?>
</table>

<!--begin footer-->
<? require ("comun/footer.php"); ?>