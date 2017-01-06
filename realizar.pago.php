<?php

	require ("comun/ini.php");
	
	
	if($ultimo_pago=$_REQUEST['a_ult_pago']==0)
	{
		$ultimo_pago=date("y")-01;
		$ultimo_pago.='-12-31';
	}
	elseif ($ultimo_pago=$_REQUEST['a_ult_pago']>=0)
	{
		$ultimo_pago=$_REQUEST['a_ult_pago'];
		$ultimo_pago.='-12-31';
	}
	
	if($_REQUEST['primera_vez']==1)
	{
		$primera_vez=1;
	} 
	else
	{
		$primera_vez=0;
	}

	
	$exento = $_REQUEST['exento'];
	
	$ovehiculo = new vehiculo;
	
	$z = "SELECT cod_exo FROM vehiculo.exoneracion WHERE cod_exo = $exento";//die($z);
	$resul= $conn->Execute($z);
	$ovehiculo->exentos = $resul->fields['cod_exo'];
	
	
	
	if ( ($exento == $ovehiculo->exentos) && (!empty($exento)) )
	{
		echo "Vehículo Exento de Deudas";
		die();
	}
		
	/*$ultimo_pago=$_REQUEST['a_ult_pago'];
	$ultimo_pago.='-12-31';*/

	$tipo_vehiculo=$_REQUEST['tipo_vehiculo'];
	//$primera_vez=$_REQUEST['primera_vez'];

	$contribuyente=$_REQUEST['id_contribuyente'];

	$declaracion=$_REQUEST['veh'];
	
	$opago = new pago;


	$q = "SELECT num_pago FROM vehiculo.det_pago WHERE nro_declaracion = $declaracion";
	$r = $conn->Execute($q);
	while(!$r->EOF)
	{
		$opago->num_pago = $r->fields['num_pago'];
		$r->movenext();
	}
	
	$w = "SELECT num_pago FROM vehiculo.imp_pago WHERE num_pago = $opago->num_pago";
	$res= $conn->Execute($w);
	$opago->num_pagos = $res->fields['num_pago'];
	
	if(!empty($opago->num_pago))
	{
		if ($opago->num_pago == $opago->num_pagos)
		{
			echo "No Tiene Deudas";
			die();
		}
	}
	
	
	if($ultimo_pago=$_REQUEST['a_ult_pago']==0)
	{
		//$ultimo_pago=$_REQUEST['ultimo_pago'];
		$ultimo_pago = '1999-12-31';
	}
	elseif ($ultimo_pago=$_REQUEST['a_ult_pago']>0)
	{
		$ultimo_pago=$_REQUEST['a_ult_pago'];
		$ultimo_pago.='-12-31';
	}
	elseif( ($ultimo_pago=$_REQUEST['a_ult_pago']>0) && ($ultimo_pago=$_REQUEST['a_ult_pago']==date("Y")) )
	{
		$ultimo_pago=date("y")-01;
		//$ultimo_pago=$_REQUEST['ultimo_pago'];
		$ultimo_pago.='-12-31';
	}
	
	if($_REQUEST['primera_vez']==1)
	{
		$primera_vez=1;
	} 
	else
	{
		$primera_vez=0;
	}



	
	
	$oliquidacion = new liquidacion;

	$cimpuestos = $oliquidacion->Cuantifica($conn, $ultimo_pago,$tipo_vehiculo,$primera_vez,$contribuyente,$declaracion);
	
?>

	


<? if(is_array($cimpuestos)){ ?>

	<h3>Informacion de pago</h3>

	<table cellpadding="0" cellspacing="1" width="100%">
		<tr class="cabecera"> 
			<td><b>A&ntilde;o</b></td>
			<td><b>Impuesto</b></td>
			<td><b>Recargo</b></td>
			<td><b>Total anual</b></td>
			<td><b>Descripcion</b></td>
		</tr>
<? 
	$i = 0;
	foreach($cimpuestos as $impuestos) { 
?> 
		<tr class="filas"> 
			<td align="left"><?=$impuestos->anio?></td>
			<td align="left"><?=muestrafloat($impuestos->impuesto)?></td>
			<td align="left"><?=muestrafloat($impuestos->recargo)?></td>
			<td align="left"><?=muestrafloat($impuestos->impuesto_mas_recargo)?></td>
			<td align="left"><? echo "$impuestos->art_ref ";  echo "$impuestos->desc_tipo";  
				$aux=muestrafecha($impuestos->vig_desde); 
				echo " a partir de $aux"; ?></td>
		</tr>
<?		 $i++;
	}
?>	
	</table>
<br>

<? 
//calculo sancion

	$fec_compra = $_REQUEST['fec_compra'];
	$veh = $_REQUEST['veh'];	
	
	$fe_compra = strtotime($fec_compra);
	$fec_sancion = strtotime("+90 day", $fe_compra);
	
	$z = "SELECT fec_crea FROM vehiculo.vehiculo WHERE id = $veh";
	$resul= $conn->Execute($z);
	$ovehiculo->fec_crea = $resul->fields['fec_crea'];
	
	$fe_crea = strtotime($ovehiculo->fec_crea);




if (  ( (date("Y", $fe_crea)) > (date("Y", strtotime($fec_compra))) ) && ($_REQUEST['primera_vez'] == 1) )
{	
	echo "Sancion:  500,00 Bs.";
	$total_pagar = muestrafloat($oliquidacion->total+500);
		
}
elseif ( ($fe_crea > $fec_sancion) && ($_REQUEST['primera_vez'] == 1) )
{
	echo "Sancion:  500,00 Bs.";
	$total_pagar = muestrafloat($oliquidacion->total+500);
}
else
{
	$total_pagar = muestrafloat($oliquidacion->total);
}
?>
<br />
<br />
	
	

<?
	}
?>


<?
	if ($oliquidacion->total>0){
?>

<a href="#" OnClick="agrega_forma_pago(); return false;">[+] Agregar otra forma de pago</a> 
<a href="#" Onclick="quita_forma_pago(); return false;">[-] Eliminar ultima forma de pago</a>

<table id="tabla_pagos">
	<tr>
		<td>Tipo de pago</td>
		<td>Banco</td>
		<td>Numero de Documento</td>
		<td>Monto</td>
	</tr>

	<tr>
		<td>
		<div id="tipo_de_pago">
		
		

		<?=helpers::combo_ue_cp($conn, 
					$tabla='vehiculo.forma_pago', 
					$id_selected='1', 
					$style='', 
					$orden='id', 
					$nombre='tipo_de_pago[]', 
					$id='tipo_de_pago', 
					$onchange='',
					$sql="SELECT * FROM vehiculo.forma_pago WHERE status='1'",
					$disabled='',
					$class='',
					$caracteresDesc='');
		?>

		</div>
		</td>

		<td>
		<div id="banco">
	
		

		<?=helpers::combo_ue_cp($conn, 
					$tabla='vehiculo.banco', 
					$id_selected='', 
					$style='', 
					$orden='id', 
					$nombre='banco[]', 
					$id='banco', 
					$onchange='',
					$sql="SELECT * FROM vehiculo.banco WHERE status='1'",
					$disabled='',
					$class='',
					$caracteresDesc='');
		?>

		
		
		</div>
		</td>

		<td>
		<div id="nro_documento">
 		<input type="text" name="nro_documento[]" id="nro_documento" size="20">
		</div>
		</td>

		<td>
		<div id="monto">
		<input type="text" name="monto[]" id="monto" class="monto_fila" size="12" align="right" onkeypress="return(formatoNumero(this, event));">
		</div>
		</td>
</table>

<br>
<table width="420">
	<tr><td>Numero de Calcomania</td><td>Monto Total</td><td></td> </tr>

	<tr><td><input type="text" name="nro_calcomania" id="nro_calcomania" value=""></td> 

	<td><input type="text" name="monto_total" id="monto_total" size="12" readonly="readonly" value="<? echo $total_pagar; ?>" align="right" >Bs.</td>

	
	<td><input type="button" name="accion2" value="Pagar" onclick="Pagar()">
		<input name="accion2" type="hidden" value="Pagar" />
</td>

</tr>
</table>
<? 
	} else { echo '<p>No tiene deudas</p>';}

?>
