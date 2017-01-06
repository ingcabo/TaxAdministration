<?
include("comun/ini.php");
header ("content-type: text/xml");
$cedula = $_GET['cedula']; 
$q = "select primer_nombre, primer_apellido, razon_social, domicilio_fiscal, fec_registro, telefono, ciudad_domicilio from vehiculo.contribuyente where identificacion=$cedula";die($q);
die($q);
$r = $conn->execute($q);

$primer_nombre= $r->fields['primer_nombre'];
$primer_apellido= $r->fields['primer_apellido'];
$razon_social= $r->fields['razon_social'];
$telefono= $r->fields['telefono'];
$domicilio_fiscal= $r->fields['domicilio_fiscal'];
$ciudad_domicilio= $r->fields['ciudad_domicilio'];
?>

<xmldoc>
	<personas>
		<primer_nombre><?=$primer_nombre?></primer_nombre>
		<primer_apellido><?=$primer_apellido?></primer_apellido>
		<razons><?=$razon_social?></razons>		
		<telefono><?=$telefono?></telefono>
		<domicilio_fiscal><?=$domicilio_fiscal?></domicilio_fiscal>
		<ciudad><?=ciudad_domicilio?></ciudad>
</personas>
</xmldoc>