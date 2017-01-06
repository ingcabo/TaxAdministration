<?
include("comun/ini.php");
die(movimientos_presupuestarios::getNroDoc($conn, $_REQUEST['tipdoc']));
?>