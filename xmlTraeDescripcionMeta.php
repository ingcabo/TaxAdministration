<?
	include("comun/ini.php");
	header ("content-type: text/xml");

	$id_meta = $_REQUEST['id_meta'];
	
	$q = "SELECT * FROM puser.metas WHERE id_metas = '$id_meta'";
	$rq = $conn->Execute($q);
?>
<xmldoc>
	<descripcion>
		<desc_meta><?=strtoupper($rq->fields['descripcion'])?></desc_meta>
	</descripcion>
</xmldoc>