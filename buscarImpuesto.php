<?
include('adodb/adodb-exceptions.inc.php'); 
require ('lib/config.php'); 
if(isset($_REQUEST['id'])){//die($_REQUEST['id']);
	$q='SELECT monto FROM publicidad.tipo_publicidad WHERE cod_publicidad='.$_REQUEST['id'];
	$r=$conn->Execute($q);
	echo $r->fields['monto'];
} 
?>
