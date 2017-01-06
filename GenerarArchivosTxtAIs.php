<? 
$enlace =$_REQUEST['archivo']; 
header ("Content-Disposition: attachment; filename=".$enlace); 
header ("Content-Type: application/download ");
header ("Content-Length: ".filesize($enlace));
readfile($enlace);
?> 
