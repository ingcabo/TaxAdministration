<?
	require ("comun/ini.php");
	// Creando el objeto publicidad
	$oespectaculo = new espectaculo;

	
?>
<div id="divTablitaEvento">
	<table align="center" border="0" id="tablitaEvento" width="62%">
		<!--DWLayoutTable-->
		<tr>
			<td height="40" valign="bottom">
				Entradas:			</td>
			<td width="10%" valign="bottom">
				Cantidad Entradas:			</td>
			<td width="18%" valign="bottom">
				Valor<br /> Entradas:			</td>
			<td valign="bottom">
				UT:			</td>
			
		</tr>
		<tr>
			<td height="26" valign="top"><?=helpers::combo_ue_cp($conn, 
															'',
															$objeto->entradas_1,
															'',
															'',
															'entradas_1',
															'entradas_1',
															'',
															"SELECT id AS id, descripcion FROM publicidad.entradas ",
															"enable :",
															$clase = "espectaculo",
															'22')?>			</td>											
			<td valign="top">
	        	<input name="cant_1" type="text" class="espectaculo" id="cant_1" style="text-align:right" size="8" maxlength="8" value="<?=$objeto->cant_1?>">			
			</td>
			<td valign="top">
				<input name="aforo_1" type="text" id="aforo_1"  style="text-align:right" onkeypress = "return(formatoNumero(this, event));" value="<?=$objeto->aforo_1?>" size="10" class="espectaculo" >
			</td>
			<td valign="top">
		    <input style="text-align:right" size="20" id="ut_1" value="<?=$objeto->ut?>" name="ut_1" type="text" readonly="readonly" />				</td>
			
		    
		</tr>
		<tr>
			<td height="26" valign="top"><?=helpers::combo_ue_cp($conn, 
															'',
															$objeto->entradas_2,
															'',
															'',
															'entradas_2',
															'entradas_2',
															'',
															"SELECT id AS id, descripcion FROM publicidad.entradas ",
															"enable :",
															$clase = "espectaculo",
															'22')?>			</td>											
			<td valign="top">
	        	<input name="cant_2" type="text" class="espectaculo" id="cant_2" value="<?=$objeto->cant_2?>" style="text-align:right" size="8" maxlength="8">			
			</td>
			<td valign="top">
				<input name="aforo_2" type="text" id="aforo_2"  style="text-align:right" value="<?=$objeto->aforo_2?>" onkeypress = "return(formatoNumero(this, event));" size="10" class="espectaculo" >
			</td>
			<td valign="top">
				<input style="text-align:right" value="<?=$objeto->ut?>" size="20" id="ut_2" name="ut_2" type="text" readonly="readonly" />				</td>
			
		</tr>
		<tr>
			<td height="26" valign="top"><?=helpers::combo_ue_cp($conn, 
															'',
															$objeto->entradas_3,
															'',
															'',
															'entradas_3',
															'entradas_3',
															'',
															"SELECT id AS id, descripcion FROM publicidad.entradas ",
															"enable :",
															$clase = "espectaculo",
															'22')?>			</td>											
			<td valign="top">
	        	<input name="cant_3" type="text" class="espectaculo" id="cant_3" value="<?=$objeto->cant_3?>" style="text-align:right" size="8" maxlength="8">			
			</td>
			<td valign="top">
				<input name="aforo_3" type="text" id="aforo_3"  style="text-align:right" value="<?=$objeto->aforo_3?>" onkeypress = "return(formatoNumero(this, event));" size="10" class="espectaculo" >
			</td>
			<td valign="top">
				<input style="text-align:right" value="<?=$objeto->ut?>" size="20" id="ut_3" name="ut_3" type="text" readonly="readonly" />				</td>
			
		</tr>
</table>
	
