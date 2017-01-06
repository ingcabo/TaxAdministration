<?
include("comun/ini.php");
header ("content-type: text/xml");
$cedula = $_GET['id_espectaculo']; 
$q = "select valor as id_espectaculo where cod_espectaculo=$cedula";die($q);
$r = $conn->execute($q);

$aforo= $r->fields['valor'];

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