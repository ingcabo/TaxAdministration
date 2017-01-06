<?
	include("comun/ini.php");
	
	$id = $_REQUEST['id'];
	$id_escenario = $_REQUEST['id_escenario'];
	$cp = $_REQUEST['id_categoria'];
	
	
	if($_REQUEST['tab'] == 'tab2')
	{
		$tab = 401;
	}
	elseif($_REQUEST['tab'] == 'tab3')
	{
		$tab = 402;
	}
	elseif($_REQUEST['tab']== 'tab4')
	{
		$tab = 403;
	}
	elseif($_REQUEST['tab'] == 'tab5')
	{
		$tab = 404;
	}
	elseif($_REQUEST['tab'] == 'tab6')
	{
		$tab = '';
	}
	$pp = new formulacion;
	if($tab == 401)
		$objpp = $pp->get_pp($conn, $id, $id_escenario, $tab, $cp);
	else
		$objpp = $pp->get_pp($conn, $id, $id_escenario, $tab);
	
	$pp->partidas_presupuestarias = new Services_JSON();
	$pp->partidas_presupuestarias = $pp->partidas_presupuestarias->encode($objpp);	
	echo $pp->partidas_presupuestarias;
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