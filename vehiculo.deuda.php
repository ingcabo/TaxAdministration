<?php

	require ("comun/ini.php");

	$ultimo_pago=$_REQUEST['a_ult_pago'];
	$ultimo_pago.='-12-31';

	$tipo_vehiculo=$_REQUEST['tipo_vehiculo'];
	$primera_vez=$_REQUEST['primera_vez'];

	$oliquidacion = new liquidacion;

	$cimpuestos = $oliquidacion->Cuantifica($conn, $ultimo_pago,$tipo_vehiculo,$primera_vez);
	
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
<?		 $i++;
	}
?>

	</table><br>
	
	<table id="grid" cellpadding="0" cellspacing="1">
	<tr class="filas">
	 	<td class="cabecera">Tasa de Inscripcion</td>
		<td align="right"><?=muestrafloat($oliquidacion->tasa)?> Bs.</td>
	</tr>
	<tr class="filas">
		<td class="cabecera">Calcomania</td>
		<td align="right"><?=muestrafloat($oliquidacion->calcomania)?> Bs.</td>
	</tr>
	<tr class="filas">
		<td class="cabecera">Total</td>
		<td align="right"><b><?=muestrafloat($oliquidacion->total)?> Bs.</b></td>
	</tr>
	</table>
<?
	}
?>
