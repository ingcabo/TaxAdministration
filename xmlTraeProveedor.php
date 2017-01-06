<?
include("comun/ini.php");
header ("content-type: text/xml");
$id = $_GET['id'];
$rif = $_REQUEST['rif'];
$oProveedor = new proveedores;

if ($id!=""){
	$oProveedor->get($conn, $id);
}elseif($rif!=""){
	$oProveedor->proveedores_rif($conn, $rif);
	}
?>
<xmldoc>
	<proveedor>
		<id_proveedor><?=$oProveedor->id?></id_proveedor>
		<nombre><?=urldecode(urlencode($oProveedor->nombre))?></nombre>
		<direccion><?=urldecode(urlencode($oProveedor->direccion))?></direccion>
		<ciudad><?=$oProveedor->estado?></ciudad>
		<telefono><?=$oProveedor->telefono?></telefono>
	</proveedor>
</xmldoc>