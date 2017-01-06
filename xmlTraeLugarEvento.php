<?
include("comun/ini.php");
$id = $_REQUEST['id'];
$fec_inicio = $_REQUEST['fec_ini'];
$fec_final = $_REQUEST['fec_fin'];

$fec_ini = guardafecha($fec_inicio); 
$fec_fin = guardafecha($fec_final);

$sql="SELECT * FROM publicidad.espectaculo WHERE publicidad.espectaculo.cod_lugar_evento = '$id' " ;
$sql.="AND ( ('$fec_ini' between fecha_inicio AND fecha_fin) ";
$sql.="OR ('$fec_fin' between fecha_inicio AND fecha_fin) )";
//die($sql);
$rs = $conn->Execute($sql); 
if(!$rs->EOF)
	echo "El Lugar esta Reservado Para este Periodo: \n $fec_inicio - $fec_final";	
else
	echo "El Lugar esta Disponible";
?>