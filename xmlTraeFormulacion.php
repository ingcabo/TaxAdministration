<?
	include("comun/ini.php");
	header ("content-type: text/xml");
	$bformulacion = new formulacion;
	$bescenario = new escenarios;
	 
	$id_formulacion = $_REQUEST['id_formulacion'];//die($id);
	$bformulacion->get($conn, $id_formulacion);
	
	$bescenario->get($conn, $bformulacion->id_escenario);
	if ( ($bformulacion->status) == 1 )
	{
		$status = "REGISTRADO";
	}
	elseif( ($bformulacion->status) == 2 )
	{
		$status = "APROBADO";
	}
	else
	{
		$status = "PROCESADO";
	}
?>
<xmldoc>
	<formulacion>
		<escenario><?=strtoupper($bescenario->descripcion)?></escenario>
		<id_escenario><?=$bformulacion->id_escenario?></id_escenario>
		<status><?=$status?></status>
		<id_unidad_ejecutora><?=$bformulacion->id_ue?></id_unidad_ejecutora>
		<unidad_ejecutora><?=strtoupper($bformulacion->desc_ue)?></unidad_ejecutora>
		<organismo><?=strtoupper($bformulacion->organismo)?></organismo>
		<objetivo><?=strtoupper($bformulacion->objetivo_gral)?></objetivo>
	</formulacion>
</xmldoc>