<?
include('adodb/adodb-exceptions.inc.php'); 
require ("comun/ini.php");
if($_REQUEST['ConceptoNombre']){
	$string=strtoupper($_REQUEST['ConceptoNombre']);
	$q="SELECT int_cod,conc_nom FROM rrhh.concepto WHERE conc_estatus='0' AND conc_tipo<>'2' AND UPPER(conc_nom) LIKE '$string%'";
	$r= $conn->Execute($q);
	echo "<ul>";
	while(!$r->EOF){
		echo "<li >".$r->fields['conc_nom']."</li>";
		$r->movenext();
	}
	echo "</ul>";
}
?>
