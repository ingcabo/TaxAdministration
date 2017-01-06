<?
include("comun/ini.php");
header ("content-type: text/xml");
$id = $_GET['id'];
$fila = $_GET['fila'];
$oProducto = new productos;
$oProducto->get($conn, $id);
?>
<xmldoc>
	<producto>
		<unidad_medida_<?=$fila?>><?=$oProducto->unidad_medida?></unidad_medida_<?=$fila?>>
		<costo_<?=$fila?>><?=$oProducto->ultimo_costo?></costo_<?=$fila?>>
	</producto>
</xmldoc>