<?
include("comun/ini.php");
header ("content-type: text/xml");
$id = $_GET['id'];
$fila = $_GET['fila'];


$sql="SELECT aforo FROM publicidad.entradas where publicidad.entradas.id = '$id'" ;
$rs = $conn->Execute($sql);


?>
<xmldoc>
	<entradas>
		<aforo1_<?=$fila?>><?=$rs->fields['aforo']?></aforo1_<?=$fila?>>
	</entradas>
</xmldoc>