<? 
include ("comun/ini.php");
$nroref = $_REQUEST['id'];
$sp = new solicitud_pago;
?>
<table width="90%" align="center" border="0" cellpadding="1" cellspacing="0" id="tablitaFR" style="text-align: left;">
    <tbody>
      <tr>
        <td width="12%" >N&ordm; Factura</td>
        <td width="12%" >N&ordm; Control</td>
        <td width="10%" >Fecha</td>
        <td width="4%" >IVA</td>
        <td width="13%" >Monto Documento</td>
        <td width="13%" >Base Imponible</td>
        <td width="12%" >Monto Exento</td>
        <td width="12%" >IVA</td>
        <td width="13%" >IVA Retenido</td>
      </tr>
      <? $cfacturas = $sp->getfacturas($conn,$nroref);
	  	$count = count($cfacturas);
	  ?>
	  <? if(is_array($cfacturas) && $count>0 ){
	  $i = 1;
	  	 foreach($cfacturas as $facturas){
	  ?>
	  <tr>
			<td><input readonly="" class="nrofac" id="nrofac_<?=$i?>" name="nrofac[]" style="width:80px" value="<?=$facturas->nrofac?>"></td>
        <td><input  readonly="" class="nrofac" id="nroctrl_<?=$i?>" name="nroctrl[]" style="width:80px" value="<?=$facturas->nrocontrol?>"></td>
        <td>
			<input readonly="" class="campoFecha" id="fecha_<?=$i?>" name="fechafac[]" style="width:70px" value="<?=muestrafecha($facturas->fechafac)?>">
		</td>
        <td><input readonly="" class="iva" id="iva_<?=$i?>" value="<?=$facturas->iva?>" name="iva[]" style="width:30px"></td>
        <td><input readonly="" onKeyPress="return(formatoNumero (this,event));" value="<?=muestrafloat($facturas->montofac)?>" onBlur="valorBase($('mntdoc_<?=$i?>'), $('mntex_<?=$i?>'), $('iva_<?=$i?>') ); valorIva($('baseimp_<?=$i?>'), $('iva_<?=$i?>')); valorRetiva($('baseimp_<?=$i?>'), $('iva_<?=$i?>')); sumaTotal();" class="monto suma" id="mntdoc_<?=$i?>" name="mntdoc[]" style="width:95px"></td>
        <td><input readonly="readonly" class="monto" id="baseimp_1" value="<?=muestrafloat($facturas->base_imponible)?>" name="baseimp[]" style="width:90px"></td>
        <td><input onKeyPress="return(formatoNumero (this,event));" value="<?=muestrafloat($facturas->monto_excento)?>" class="monto" id="mntex_<?=$i?>" name="mntex[]" style="width:80px" readonly=""></td>
        <td><input readonly="readonly" class="monto" id="ivamnt_<?=$i?>" value="<?=muestrafloat($facturas->monto_iva)?>" name="ivamnt[]" style="width:80px"></td>
        <td align="right"><input readonly="readonly" class="monto" value="<?=muestrafloat($facturas->iva_retenido)?>" id="ivaret_<?=$i?>" name="ivaret[]" style="width:80px"></td>
      </tr>
	  <? $total += $facturas->montofac;
	  $i++;
	  ?> 
	  <? } } else{ ?>
	  <tr>
        <td><input class="nrofac" id="nrofac_1" name="nrofac[]" style="width:80px"></td>
        <td><input class="nrofac" id="nroctrl_1" name="nroctrl[]" style="width:80px"></td>
        <td>
			<input class="campoFecha" id="fecha_1" name="fechafac[]" style="width:70px">
		</td>
        <td><input class="iva" id="iva_1" value="14" name="iva[]" style="width:30px"></td>
        <td><input onKeyPress="return(formatoNumero (this,event));" onBlur="valorBase($('mntdoc_1'), $('mntex_1'), $('iva_1') ); valorIva($('baseimp_1'), $('iva_1')); valorRetiva($('baseimp_1'), $('iva_1')); sumaTotal();" class="monto suma" id="mntdoc_1" name="mntdoc[]" style="width:95px"></td>
        <td><input readonly="readonly" class="monto" id="baseimp_1" name="baseimp[]" style="width:90px"></td>
        <td><input onKeyPress="return(formatoNumero (this,event));" value="0" class="monto" id="mntex_1" name="mntex[]" style="width:85px"></td>
        <td><input readonly="readonly" class="monto" id="ivamnt_1" name="ivamnt[]" style="width:80px"></td>
        <td align="right"><input readonly="readonly" class="monto" id="ivaret_1" name="ivaret[]" style="width:80px"></td>
      </tr>
	  <? }?>
      <tr>
        <td colspan="7" style="text-align:right">Total a Facturar:</td>
        <td colspan="2" style="text-align:right"><span style="text-align:right">
          <input class="monto" readonly="" style="width:150px" id="total" name="total" value="<?=muestrafloat($total)?>" />
        </span></td>
      </tr>
    </tbody>
  </table>