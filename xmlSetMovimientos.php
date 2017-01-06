<?
include("comun/ini.php");
$pp = $_GET['pp'];
$cp = $_GET['cp'];
header ("content-type: text/xml");
$q.= "SELECT * FROM relacion_movimientos WHERE ";
$q.= "id_partida_presupuestaria = '$pp' AND id_categoria_programatica = '$cp' ";
$r = $conn->Execute($q);
if (!isset($r->fields['id']))
	$id = 1;
else
	$id = $r->fields['id'] + 1;
?>
<xmldoc>
	<nextval>
		<valor><?=str_pad($id, 4, 0, STR_PAD_LEFT)?></valor>
	</nextval>
</xmldoc>