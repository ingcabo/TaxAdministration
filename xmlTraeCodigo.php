<?
include("comun/ini.php");
header ("content-type: text/xml");

$id = $_REQUEST['id'];//die($id);

$q = "SELECT COUNT(*) AS cant_met FROM puser.formulacion WHERE id_ue = '$id'";//die($q);
$r = $conn->Execute($q);
$cant_met = $r->fields['cant_met'];
$cant = $cant_met + 1;

$z = "SELECT * FROM puser.unidades_ejecutoras WHERE id = '$id'";
$rz = $conn->Execute($z);

?>
<xmldoc>
	<codigo>
		<cant_metas><?=$cant?></cant_metas>
		<desc_ue><?=$rz->fields['descripcion']?></desc_ue>
	</codigo>
</xmldoc>