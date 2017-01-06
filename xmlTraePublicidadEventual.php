<?
include("comun/ini.php");
header ("content-type: text/xml");
$id = $_GET['id'];
$fila = $_GET['fila'];
$UT = $_GET['ut'];
$cant = $_GET['cant'];
$sancion = $_REQUEST['sancion'];



$sql="SELECT monto FROM publicidad.tipo_publicidad where publicidad.tipo_publicidad.cod_publicidad = '$id'" ;
$rs = $conn->Execute($sql);

$sql_sancion = "SELECT monto FROM publicidad.articulos_sanciones WHERE publicidad.articulos_sanciones.cod_articulo = '$sancion'";// die($sql_sancion);
$rs_sancion = $conn->Execute($sql_sancion);	

$sql_monto_sancion = "SELECT fecha_sancion FROM publicidad.publicidad WHERE publicidad.publicidad.id = '$id'";//die($sql_monto_sancion);
$rs_monto_sancion = $conn->Execute($sql_monto_sancion);

$impuesto_anual = $rs->fields['monto'] * $UT;
$monto_sancion = $rs_sancion->fields['monto'] * $UT;

if (!empty($rs_monto_sancion->fileds['fecha_sancion']))
{
	$meses_sancion = date("Y-m-d") - $rs_monto_sancion->fileds['fecha_sancion'];
	$interes_sancion = $monto_sancion * 0.01 * $meses_sancion;
}
 
if (!empty($monto_sancion))
{
	$total_impuesto = ($impuesto_anual * $cant) + $monto_sancion + $interes_sancion;
}
else
{
	$total_impuesto = $impuesto_anual * $cant;  
}
	

?>
<xmldoc>
	<publicidad>
		<aforo_<?=$fila?>><?=muestrafloat($rs->fields['monto'])?></aforo_<?=$fila?>>
		<total_pr_<?=$fila?>><?=muestrafloat($total_impuesto)?></total_pr_<?=$fila?>>
	</publicidad>
</xmldoc>