<?
	require ("comun/ini.php");
	
	$oliquidacion = new liquidacion;
	
	if ($ultimo_pago=$_REQUEST['ultimo_pago']==0)
	{
		$ultimo_pago=0;
	}
	else
	{
		$ultimo_pago=$_REQUEST['ultimo_pago'];
		$ultimo_pago.='-12-31';
	}
		$tipo_vehiculo=$_GET['tipo'];
		
		if($_REQUEST['primera']==1){$primera_vez=true;} else {$primera_vez=false;}
		
	
	//$veh = $_GET['veh'];

	//$ovehiculo->get($conn, $_GET['veh']);

	//$oliquidacion->ImpuestoAnualMasRecargo($conn, $anio_a_liquidar,$tipo_vehiculo);
	
	require ("comun/header.php");
	
	//echo "<h1>Impuesto Anual: $oliquidacion->impuesto</h1>";
	//echo "<h1>Recargo: $oliquidacion->recargo</h1>";
	//echo "<h1>Motivo de recargo: $oliquidacion->art_ref $oliquidacion->desc_tipo superior a $oliquidacion->vig_desde</h1>";
	//echo "<h1>Impuesto Anual + Recargo: $oliquidacion->impuesto_mas_recargo</h1>";
	
	$cimpuestos = $oliquidacion->Cuantifica($conn, $ultimo_pago, $tipo_vehiculo, $primera_vez, $ovehiculo->id_contribuyente,$ovehiculo->id);

	
?>

<? if(is_array($cimpuestos)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>A&ntilde;o</td>
<td>Impuesto</td>
<td>Recargo</td>
<td>Impuesto+Recargo</td>
<td>Descripcion</td>
</tr>
<? 
$i = 0;
foreach($cimpuestos as $impuestos) { 
?> 
<tr class="filas"> 
<td><?=$impuestos->anio?></td>
<td><?=muestrafloat($impuestos->impuesto)?></td>
<td align="center"><?=muestrafloat($impuestos->recargo)?></td>
<td align="center"><?=muestrafloat($impuestos->impuesto_mas_recargo)?></td>
<td align="left"><? echo "$impuestos->art_ref ";  echo "$impuestos->desc_tipo";  
	$aux=muestrafecha($impuestos->vig_desde); 
	echo " a partir de $aux"; ?></td>
</tr>
<? $i++;

	}
?>

<? }
	echo "</table><br><br>
	<table class=\"sortable\"><tr class=\"filas\"><td>Tasa de Inscripcion</td><td align=\"center\"><b>".muestrafloat($oliquidacion->tasa)."</b></td></tr>";

	echo "<tr class=\"filas\"><td>Calcomania</td><td align=\"center\"><b>".muestrafloat($oliquidacion->calcomania)."</b></td></tr>";


	echo "<tr class=\"filas\"><td>Total</td><td align=\"center\"><b>".muestrafloat($oliquidacion->total)."</b></td></tr>";

	require ("comun/footer.php"); 
?>
