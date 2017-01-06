<?php 
require('comun/ini.php');
$num = $_REQUEST['num'];

$valor = num2letras($num,false,true);

echo 'El numero es: '.$valor;

?>
