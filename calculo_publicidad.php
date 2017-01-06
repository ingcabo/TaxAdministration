<?
	require ("comun/ini.php");
	// Creando el objeto publicidad
	$oPublicidad = new publicidad;
	$cRelacionPublicidad = new publicidad;
	
	
	$patente = $_REQUEST['patente'];
	$id_solicitud = $_REQUEST['id_usuario'];
	$sql_id = "SELECT id FROM publicidad.publicidad WHERE patente = '4278'";
	$rs_id = $conn->Execute($sql_id);
	
	$id = $rs_id->fields['id'];
	
	$fecha_actual = date("Y-m");
	$sql_tb ="SELECT * FROM publicidad.tasa_bancaria_publicidad WHERE status = '1' AND fecha_desde <= '$fecha_actual-01'AND fecha_hasta >= '$fecha_actual-01'";
	$res_tb = $conn->Execute($sql_tb);
	$TB = $res_tb->fields['monto']; 
	
	$año_actual = date("Y");
	$sql_ut ="SELECT * FROM publicidad.unidad_tributaria WHERE status = '1' AND fecha_desde <= '$año_actual-01-01'AND fecha_hasta >= '$año_actual-12-01'";
	$res_ut = $conn->Execute($sql_ut);
	$UT = $res_ut->fields['monto'];
							
					
	
	$oPublicidad->get($conn, $id);
	
	
?>

<table width="100%"rules="none" frame="void">
<tr>
  <td width="803" height="18" valign="top"><div align="center" class="Estilo9">
          <div align="left">Publicidad</div>
		  <div align="right">Periodo Desde:<span class="Estilo8">
		    <input name="fec_ini" type="text" id="fec_ini" value="<?=muestrafecha($objeto->fec_ini)?>" size="12" readonly="readonly" />
          </span> <a href="#" id="boton_fechainicio" onclick="return false;"> <img  border="0" src="images/calendarA.png" width="14" height="14" /> </a>
          <script type="text/javascript">
					new Zapatec.Calendar.setup({
						firstDay          : 1,
						weekNumbers       : true,
						showOthers        : false,
						showsTime         : false,
						timeFormat        : "24",
						step              : 2,
						range             : [1900.01, 2999.12],
						electric          : false,
						singleClick       : true,
						inputField        : "fec_ini",
						button            : "boton_fechainicio",
						ifFormat          : "%d/%m/%Y",
						daFormat          : "%Y/%m/%d",
						align             : "Br"
					 });
				</script>
		  </div>
		  <div align="right">Periodo Hasta:<span class="Estilo8">
		    <input  name="fec_fin" type="text" id="fec_fin" value="<?=muestrafecha($objeto->fec_fin)?>" size="12" readonly="readonly" />
            <a href="#" id="boton_fechafin" onclick="return false;"><img  border="0" src="images/calendarA.png" width="14" height="14" /> </a>
            <script type="text/javascript">
					new Zapatec.Calendar.setup({
						firstDay          : 1,
						weekNumbers       : true,
						showOthers        : false,
						showsTime         : false,
						timeFormat        : "24",
						step              : 2,
						range             : [1900.01, 2999.12],
						electric          : false,
						singleClick       : true,
						inputField        : "fec_fin",
						button            : "boton_fechafin",
						ifFormat          : "%d/%m/%Y",
						daFormat          : "%Y/%m/%d",
						align             : "Br"
					 });
		       	</script>
		  </span></div>
      </div></td>
<td width="147"></td>
</tr>
</table>

<? // if(is_array($cRelacionPublicidad->getRelacionPublicidad($conn, $id))){

		if ($id_solicitud == 1){
	$i = 1;
		//foreach($cRelacionPublicidad->getRelacionPublicidad($conn, $id) as $relacionPublicidad){	
?>
<div id="divTablitaPubFija">
	<div>
<?
//$cRelacionPublicidad->getRelacionPublicidad($conn, $id); 
?>
	<a id="linkAgrega" href="#" onClick="addPRE(); return false;">Agregar una Publicidad [+]</a>
	<a id="linkElimina" href="#" onClick="delPRE(); return false;">Eliminar la &uacute;ltima Publicidad [ - ]</a>
</div>
		<table align="center" border="0" id="tablitaPubFija" width="70%">
<tr>
			<td width="162" height="40" valign="bottom">Tipo de Publicidad :</td>
		  <td width="123" align="center" valign="bottom">Cantidad:</td>
				<td width="122" align="center" valign="bottom">Unidad:</td>
		<td colspan="4" align="center" valign="bottom"><div align="center">Sanci&oacute;n:</div></td>
		<td width="126" align="center" valign="bottom">Aforo:</td>
				<td width="82" align="center" valign="bottom">Total:</td>

				<td width="86" align="center" valign="bottom">&nbsp;</td>
</tr>

			<tr>
			<td height="26" valign="top"><?=helpers::combo_ue_cp($conn, 
															'', 
															'',
															'',
															'',
															'cod_publicidad_'.$i,
															'cod_publicidad_'.$i,											
															"traePublicidadEventualDesdeXML(this,".guardafloat($UT).",'0','0')",
															"SELECT cod_publicidad AS id, descripcion FROM publicidad.tipo_publicidad WHERE status = '1' AND tip_publicidad = '1' ",
															"enabled :",
															$clase = "comun",
															'22')?>			  </td>
			  <td valign="top"><select name="cant[]" id="cant1_<?=$i?>" class="comun" onchange="traePublicidadEventualDesdeXML($('cod_publicidad_<?=$i?>'),<?=guardafloat($UT)?>,this.value,'0')">
			  						<option value="0">Seleccione . . .</option>
			  						<option value="1">1</option>
									<option value="2">2</option>
									<option value="3">3</option>
									<option value="4">4</option>
									<option value="5">5</option>
									<option value="6">6</option>
									<option value="7">7</option>
									<option value="8">8</option>
									<option value="9">9</option>
									<option value="10">10</option>
			  					</select> </td>
				<td valign="top"><select id="unid1_<?=$i?>" name="unid1" class="comun">
									<option value="0">Seleccione . . .</option>
									<option value="unidad">Unidad</option>
									<option value="metros">Metros</option>
								</select>				</td>
				<td width="7" valign="top">&nbsp;</td>
				<td width="7" valign="top"><?=helpers::combo_ue_cp($conn, 
															'', 
															'',
															'',
															'',
															'cod_articulo_'.$i,
															'cod_articulo_'.$i,											
															"traePublicidadEventualDesdeXML($('cod_publicidad_$i'),".guardafloat($UT).",$('cant1_".$i."').value,this.value)",
															"SELECT cod_articulo AS id, descripcion FROM publicidad.articulos_sanciones WHERE status = '1'",
															"enabled :",
															$clase = "comun",
															'22')?>	</td>
				<td width="7" valign="top">&nbsp;</td>
				<td width="8" valign="top">&nbsp;</td>
				<td align="center" valign="top"><input name="aforo[]2" type="text" id="aforo_<?=$i?>"  style="text-align:right" onkeypress= "return(formatoNumero(this, event));" size="10" class="comun" readonly="readonly" />
				
			    <input name="hidden" type="hidden" class="pubSeleccionada" id="pubSeleccionada_<?=$i?>" /></td>
			  <td valign="top"><input name="precioTotalPu[]" type="text" class="comun" id="total_pr_<?=$i?>" style="text-align:right" size="14" readonly="readonly" /></td>
				<td valign="top">&nbsp;</td>
			</tr>
<? 
$i++;
$totalBase += $relacionPublicidad->precioTotalPub;
$totalGeneral = $totalBase;
}?>	</table>
</div>
<? 
if ($id_solicitud == 2){ ?> 
		<div id="divTablitaPubFija">
		<div>
<?
//$cRelacionPublicidad->getRelacionPublicidad($conn, $id); 
?>
	<a id="linkAgrega" href="#" onClick="addPR(); return false;">Agregar una Publicidad [+]</a>
	<a id="linkElimina" href="#" onClick="delPR(); return false;">Eliminar la &uacute;ltima Publicidad [ - ]</a>
</div>
		<table align="center" border="0" id="tablitaPubFija" width="70%">
			<tr>
				<td width="73" height="40" valign="bottom">Tipo de Publicidad :</td>
				<td width="125" align="center" valign="bottom">Cantidad:</td>
				<td width="122" align="center" valign="bottom">Unidad:</td>
				<td colspan="4" align="center" valign="bottom"><div align="center"></div></td>
				<td width="83" align="center" valign="bottom">Aforo:</td>
				<td width="89" align="center" valign="bottom">Interes (%):</td>
				<td width="189" align="center" valign="bottom">Total:</td>
			</tr>
	
		<? $i = 1; ?>
			<tr>
			<td height="26" valign="top"><?=helpers::combo_ue_cp($conn, 
															'', 
															'',
															'',
															'',
															'cod_publicidad_'.$i,															
															'cod_publicidad_'.$i,
															"traePublicidadDesdeXML(this,".guardafloat($UT).",".guardafloat($TB).", '0', '0')",
															"SELECT cod_publicidad AS id, descripcion FROM publicidad.tipo_publicidad WHERE status = '1' AND tip_publicidad = '2' ",
															"enabled :",
															$clase = "comun",
															'22')?></td>
			  <td valign="top"><select name="cant[]" id="cant1_<?=$i?>" class="comun" onchange="traePublicidadDesdeXML($('cod_publicidad_<?=$i?>'),<?=guardafloat($UT)?>,<?=guardafloat($TB)?>,this.value,'0')">
			  						<option value="0">Seleccione . . .</option>
			  						<option value="1">1</option>
									<option value="2">2</option>
									<option value="3">3</option>
									<option value="4">4</option>
									<option value="5">5</option>
									<option value="6">6</option>
									<option value="7">7</option>
									<option value="8">8</option>
									<option value="9">9</option>
									<option value="10">10</option>
			  					</select>
	          <!--<input name="cant1[]" type="text" class="fija" id="cant1_<?=$i?>"  style="text-align:right" size="4" />--></td>
			  <td valign="top"><select id="unid1_1" name="unid1" class="fija">
			      	<option value="0">Seleccione . . .</option>
					<option value="Unidad">Unidad</option>
				</select></td>
			  <td width="3" valign="top">&nbsp;</td>
			  <td width="19" valign="top">			    <!--<select name="unid2" class="eventual" id="unid2_1" onchange="traePublicidadDesdeXML($('cod_publicidad_<?=$i?>'),<?=guardafloat($UT)?>,<?=guardafloat($TB)?>,$('cant1_<?=$i?>').value,$('unid1_1').value, this)">
			      <option value="0">Seleccione . . .</option>
					<option value="1">Menor o Igual a 2m&sup2;</option>
				    <option value="2">Mayor a 2m&sup2;</option>
		        </select>--></td>
			  <td width="7" valign="top">&nbsp;</td>
			  <td width="9" valign="top">&nbsp;</td>
			  <td align="center" valign="top"><input name="aforo[]" type="text" id="aforo[]"  style="text-align:right" onkeypress= "return(formatoNumero(this, event));" size="10"class="comun" readonly />
		      <input name="hidden2" type="hidden" class="pubSeleccionada"  id="hidden2" /></td>
				<td valign="top"><input name="interes[]" type="text" id="intereses_<?=$i?>"  style="text-align:right"  size="10"class="comun" readonly="readonly" />
			    <input name="hidden2_<?=$i?>" type="hidden" class="pubSeleccionada"  id="pubSeleccionada2_<?=$i?>" /></td>
				<td valign="top"><input name="precioTotalPu[]2" type="text" class="comun" id="precioTotalPu[]" style="text-align:right" size="14"  readonly /></td>

<? $i++;}  ?>
	</table>
</div>
  <table>
		<tr>
		<td width="107" height="39"></td>
		<td width="51"></td>
		<td width="21"></td>
		<td width="71"></td>
		<td width="22"></td>
		<td width="69"></td>
		<td width="91">&nbsp;</td>
		<td width="1">&nbsp;</td>
		<td width="65">&nbsp;</td>
		<td colspan="5" align="right" valign="top">TOTAL GENERAL Bs.</td>
		<td colspan="2" align="center" valign="top">
          <input value="<?=muestrafloat($totalGeneral)?>" name="total_general_pub" align="right" id="total_general_pub" size="14" type="text" readonly /></td>
      <td width="16" valign="top"><span class="errormsg" id="error_totg">*</span>		  <?=$validator->show("error_totg")?></td>
		</tr>
</table>



