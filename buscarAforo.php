<?
include('adodb/adodb-exceptions.inc.php'); 
require ('lib/config.php'); 
if(isset($_REQUEST['id'])){
	$q='SELECT aforo FROM publicidad.entradas WHERE id='.$_REQUEST['id'];
	$r=$conn->Execute($q);
	echo $r->fields['aforo'];
} 
?>
