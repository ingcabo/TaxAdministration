<?php 

require ("comun/ini.php");
$cod_ins = $_REQUEST['cod_ins'];
$nombre = strtoupper($_REQUEST['nombre']);
$apellido = $_REQUEST['apellido'];
$cedula = $_REQUEST['cedula'];
$status = $_REQUEST['status'];
//$text_status='';

if(empty($status)){
$status=0;
}
	#GUARDO
	if(!empty($_REQUEST['accion']) and $_REQUEST['accion']=='Guardar'){
		$sql="INSERT INTO publicidad.inspector (nombre, apellido, cedula, status) VALUES ('$nombre', '$apellido', '$cedula', '$status')";
		$msj="Registro Guardado con éxito";
		
	}
	#ACTUALIZO
	if(!empty($_REQUEST['accion']) and $_REQUEST['accion']=='Actualizar'){
		
		$sql="UPDATE publicidad.inspector SET nombre='$nombre', apellido='$apellido', cedula='$cedula', status='$status' WHERE cod_ins='$cod_ins'";
		$msj="Registro Actualizado con éxito";
	}
		//die($sql);
		$conn->Execute($sql);

$sql="SELECT * FROM publicidad.inspector ORDER BY cod_ins ASC";
$rs = $conn->Execute($sql);
print $conn->ErrorMsg();

require ("comun/header.php");
$div="<div id='formulario'><a href='#' onclick=\"gettpl('')\";>Agregar Nuevo Registro</a></div>";
if(!empty($msj)){ ?><div id="msj" style="display:none;"><?=$msj?></div><? echo "<br>"; }
?>

	<script type="text/javascript">
	function gettpl(id)
	{
		var url = 'inspector.tpl.php';
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
<span class="titulo_maestro">Maestro Inspector</span>
<?=$div?>
<br />



<table align="center" class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>C&eacute;dula</td>
<td>Inspector</td>
<td>Status</td>
<td>&nbsp;</td>
</tr>
<?php 	while (!$rs->EOF) { 
if($rs->fields['status']==1) { $text_status='Activo'; }else{ $text_status='Inactivo'; }
?>
<tr class="filas"> 
<td><?=$rs->fields['cod_ins']?></td>
<td><?=$rs->fields['cedula']?></td>
<td align="left"><?=$rs->fields['nombre']." ".$rs->fields['apellido']?></td>
<td align="center"><?=$text_status?></td>
<td align="center"><a href="#" onclick="gettpl(<?=$rs->fields['cod_ins']?>);"><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
</tr>
<?php 
			$rs->MoveNext();
		}
?>
</table>

<!--begin footer--><!--<div id="pars"></div>-->
<? require ("comun/footer.php"); ?>