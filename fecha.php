<?
include("comun/ini.php");
$fecha = $_GET['fecha'];
$fecha = empty($fecha) ? "null" : "'".$fecha."'";
$q = "insert into x (date) values ($fecha) ";
die($q);
$r = $conn->execute($q);
?>
