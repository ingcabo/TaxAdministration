<?
include('adodb/adodb.inc.php');
$conn = &ADONewConnection('postgres');
$conn->Connect($DBserver, $DBuser, $DBpass, $DBname); 
?>
