<?php

	require ("comun/ini.php");
	
	$cod_cambio = $_REQUEST['cod_cambio'];
	
	if ($cod_cambio == '1')
	{
		echo helpers::combo($conn, '', $objeto->cod_col, '', 'cod_col_nue', 'cod_col_nue', 'cod_col_nue', '', 'SELECT cod_col as id, descripcion FROM vehiculo.colores WHERE status=1');
	}
	elseif ($cod_cambio  == '2')
	{?>
		<input align="left" type="text" name="serial_motor_nue" id="serial_motor_nue">
	<? }
	elseif ($cod_cambio == '3')
	{?>
		<input align="left" type="text" name="placa_nue" id="placa_nue" size="10">
	<? }
	elseif ($cod_cambio == '4')
	{
		echo helpers::combo($conn, '', $objeto->id_contribuyente, '', 'id_contribuyente_nue', 'id_contribuyente_nue', 'id_contribuyente_nue', '', 'SELECT id as id, id||\'::\'||primer_apellido||\' \'||primer_nombre as descripcion FROM vehiculo.contribuyente');
	}
?>
		