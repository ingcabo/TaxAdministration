<?
include("comun/ini.php");
header ("content-type: text/xml");
$id = $_GET['id'];
$fila = $_GET['fila'];
$oRetenciones = new retenciones_adiciones;
$oRetenciones->get($conn, $id);
?>
<xmldoc>
	<retencion>
		<porcra_<?=$fila?>><?=$oRetenciones->porcentaje?></porcra_<?=$fila?>>
	</retencion>
</xmldoc>