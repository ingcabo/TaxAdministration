<? 
include ("comun/ini.php");
$nroref = $_REQUEST['id'];
$sp = new solicitud_pago;
?>
<table width="80%" align="center" border="0" cellpadding="1" cellspacing="0" id="tablitaAR" style="text-align: left;">
  	<tr>
		<td width="26%">Descripcion</td>
		<td width="18%">(%) Porcentaje</td>
		<td width="26%">Monto Base</td>
		<td width="30%">Monto Ret / Adic.</td>
	</tr>
	<? $cretenciones = $sp->getretenciones($conn,$nroref);
	  	$count = count($cretenciones);
	  ?>
	  <? if(is_array($cretenciones) && $count>0 ){
	  $i = 1;
	  	 foreach($cretenciones as $retenciones){
	  ?>
	<tr>
		<td>
			<? $bn = new retenciones_adiciones;
				$arr = $bn->getAll($conn);
			?>
			
			<?=helpers::superComboObj($arr, $retenciones->codigoretencion,'descrirt[]', "descrirt_".$i."",'',"",'id','descripcion','','','disabled');?>
			
			<input type="hidden" name="retencionselec[]" id="retencionselec_1" class="retencionselec">	
		</td>
		<td>
			<input readonly="" type="text" id="porcra_<?=$i?>" name="porcra[]" value="<?=$retenciones->porcentaje?>" class="iva" style="width:100px" onBlur="montoretenciones(this.value,$('montobase_1').value, this.id);sumaTotalRetenciones();" />
		</td>
		<td>
		    <input type="text" name="montobase[]" class="monto" id="montobase_<?=$i?>" value="<?=muestrafloat($retenciones->montobase)?>" readonly="" />
		</td>
		<td>
			<input type="text" readonly="" name="montora[]" id="montora_<?=$i?>" value="<?=muestrafloat($retenciones->montoretencion)?>" class="total" style="text-align:right" />
		</td>
 	</tr>
	<? $totalre += $retenciones->montoretencion;
	  $i++;
	  ?> 
	  <? } } else{ ?>
	<tr>
		<td>
			<? $bn = new retenciones_adiciones;
				$arr = $bn->getAll($conn);
			?>
			<?=helpers::superComboObj($arr, '','descrirt[]', 'descrirt_1','',"");?>
			<input type="hidden" name="retencionselec[]" id="retencionselec_1" class="retencionselec">	
		</td>
		<td>
			<input type="text" id="porcra_1" name="porcra[]" class="iva" style="width:100px" onBlur="montoretenciones(this.value,$('montobase_1').value, this.id);sumaTotalRetenciones();" />
		</td>
		<td>
		    <input type="text" name="montobase[]" id="montobase_1" value="" readonly="" />
		</td>
		<td>
			<input type="text" name="montora[]" id="montora_1" value="" class="total" style="text-align:right" />
		</td>
 	</tr>
	<? } ?>
	<tr>
		<td colspan="3" align="right">
		Total Adiciones / Retenciones : &nbsp;
		</td>
		<td>
			<input type="text" readonly="" name="totalra" id="totalra" style="text-align:right" value="<?=muestrafloat($totalre)?>"  /> 
		</td>
	</tr>
  </table>