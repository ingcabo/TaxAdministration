<?
include("comun/ini.php");
header ("content-type: text/xml");

$id = $_REQUEST[['id'];
$q = "SELECT COUNT(*) FROM puser.formulacion WHERE unidad_ejecutora = '$id'";//die($q);
$r = $conn->Execute($q);
$cant = $r + 1;
?>
<xmldoc>
	<contribuyente>
		<cant_metas><?=$cant?></cant_metas>
		<ue><?=$r->fields['descripcion']?></ue>
	</contribuyente>
</xmldoc>