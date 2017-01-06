<?
	include("comun/ini.php");
	$id = $_REQUEST['id'];
	$id_escenario = $_REQUEST['id_escenario'];
	$cp = new formulacion;
	$objcp = $cp->get_cp($conn, $id, $id_escenario);
	
	$cp->categorias_programaticas = new Services_JSON();
	$cp->categorias_programaticas = $cp->categorias_programaticas->encode($objcp);	
	echo $cp->categorias_programaticas;
/*
	header ("content-type: text/xml");
	
	$id = $_REQUEST['id'];
	$cant_metas = $_REQUEST['cant_metas'];
	
	$q = "SELECT COUNT(*) AS cant_met FROM puser.formulacion WHERE id_ue = '$id'";//die($q);
	$r = $conn->Execute($q);
	$cant_met = $r->fields['cant_met'];
	$cant = $cant_met + 1;
	
	$cp = new formulacion;
	$objcp = $cp->get_all_cp($conn, $id);
?>

<rows>
	<?
		foreach($objcp as $key => $value) 
		{
	?>
			<row id='<?=$key?>'>
				<cell><?=$id."-".$cant_metas."-".$cant?></cell>
				<cell><?=helpers::combogrid($objcp, 1, 'id' , 'id', 'Seleccione...', 'mygrid1' )?></cell>
				<cell></cell>
				<cell></cell>
				<cell></cell>
			</row>
	<?	
		}
	?>
</rows>
*/
?>