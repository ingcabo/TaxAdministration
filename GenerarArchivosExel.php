<?
$enlace =$_REQUEST['archivo'];
header("Pragma:  no-cache");
header('Content-type:application/x-msdownload');
header("Expires: 0");  
header("Content-Length: " .(string)(filesize($enlace)) );
header("Content-Disposition: attachment; filename=".$enlace."\n\n");
header("Content-Transfer-Encoding: binary\n");
readfile($enlace);
?>