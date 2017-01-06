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
		<id_ciudadano><?=$oProveedor->id?></id_ciudadano>
		<rif_ciudadano><?=$oProveedor->rif?></rif_ciudadano>
		<nombre_ciudadano><?=$oProveedor->nombre?></nombre_ciudadano>
		<dir_ciudadano><?=$oProveedor->direccion?></dir_ciudadano>
		<tlf_ciudadano><?=$oProveedor->telefono?></tlf_ciudadano>
	</proveedor>
</xmldoc>

<?
/*
include("comun/ini.php");
header ("content-type: text/xml");
$id = $_GET['id'];
$oCiudadano = new ciudadanos();
$oCiudadano->get($conn, $id);
?>
<xmldoc>
	<ciudadano>
		<id_ciudadano><?=$oCiudadano->id?></id_ciudadano>
		<nombre_ciudadano><?=$oCiudadano->nombre?></nombre_ciudadano>
		<tlf_ciudadano><?=$oCiudadano->tlf?></tlf_ciudadano>
		<dir_ciudadano><?=$oCiudadano->direccion?></dir_ciudadano>
	</ciudadano>
</xmldoc>*/?>