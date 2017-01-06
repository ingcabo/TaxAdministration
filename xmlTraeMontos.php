<?
include("comun/ini.php");
header ("content-type: text/xml");
$nrodoc = $_GET['nrodoc'];
$q = "SELECT id_categoria_programatica || id_partida_presupuestaria AS parcat, sum(monto) AS monto ";
$q.= "FROM relacion_movimientos WHERE nrodoc='$nrodoc' ";
$q.= "GROUP BY parcat";
//die($q);
$r = $conn->execute($q);
?>
<xmldoc>
<? while(!$r->EOF){?>
<monto nombre="<?=$r->fields['parcat']?>" valor="<?=$r->fields['monto']?>" />
<? $r->movenext(); } ?>
</xmldoc>