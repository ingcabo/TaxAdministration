<?
include("comun/ini.php");
header ("content-type: text/xml");
$fila = $_GET['fila'];
$parpre = relacion_pp_cp::get_all_by_cp_pp_esc($conn, $_GET['cp'], $_GET['pp'], $escEnEje);
?>
<xmldoc>
	<parcat>
		<idParCat><?=$parpre->id?></idParCat>
		<presupuesto_original><?=$parpre->presupuesto_original?></presupuesto_original>
		<compromisos><?=$parpre->compromisos?></compromisos>
		<hCompromisos><?=$parpre->compromisos?></hCompromisos>
		<causados><?=$parpre->causados?></causados>
		<hCausados><?=$parpre->causados?></hCausados>
		<pagados><?=$parpre->pagados?></pagados>
		<hPagados><?=$parpre->pagados?></hPagados>
		<aumentos><?=$parpre->aumentos?></aumentos>
		<hAumentos><?=$parpre->aumentos?></hAumentos>
		<disminuciones><?=$parpre->disminuciones?></disminuciones>
		<hDisminuciones><?=$parpre->disminuciones?></hDisminuciones>
		<disponible><?=$parpre->disponible?></disponible>
		<hDisponible><?=$parpre->disponible?></hDisponible>
		<max_<?=$fila?>><?=$parpre->disponible?></max_<?=$fila?>>
	</parcat>
</xmldoc>