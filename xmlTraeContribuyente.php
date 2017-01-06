<?
include("comun/ini.php");
header ("content-type: text/xml");
$id = $_GET['id'];
$oContribuyente = new contribuyente;
$oContribuyente->get($conn, $id);
$nombre_completo = $oContribuyente->primer_nombre." ".$oContribuyente->segundo_nombre." ".$oContribuyente->primer_apellido." ".$oContribuyente->segundo_apellido;
?>
<xmldoc>
	<contribuyente>
		<primer_nombre><?=$nombre_completo?></primer_nombre>
		<razons><?=$oContribuyente->razon_social?></razons>		
		<telefono><?=$oContribuyente->telefono?></telefono>
		<domicilio_fiscal><?=$oContribuyente->domicilio_fiscal?></domicilio_fiscal>
		<ciudad><?=$oContribuyente->ciudad_domicilio?></ciudad>
	</contribuyente>
</xmldoc>