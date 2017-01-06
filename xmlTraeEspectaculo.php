<?
include("comun/ini.php");
header ("content-type: text/xml");
$id = $_GET['id'];


$sql="SELECT * FROM publicidad.tipo_espectaculo where publicidad.tipo_espectaculo.cod_espectaculo = '$id'" ;//die($sql);
$rs = $conn->Execute($sql);
?>
<xmldoc>
	<espectaculo>
		<ut_1><?=muestrafloat($rs->fields['ut'])?></ut_1>
		<ut_2><?=muestrafloat($rs->fields['ut'])?></ut_2>
		<ut_3><?=muestrafloat($rs->fields['ut'])?></ut_3>
	</espectaculo>
</xmldoc>