<?
include("comun/ini.php");
header ("content-type: text/xml");

$id_escenario = $_REQUEST['id_escenario'];//die($id);

$q = "SELECT * FROM puser.escenarios WHERE id = '$id_escenario'";//die($q);
$r = $conn->Execute($q);


?>
<xmldoc>
	<anio_escenario>
		<anio><?=$r->fields['ano']?></anio>
	</anio_escenario>
</xmldoc>