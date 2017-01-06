<?
include ("comun/ini.php");
$proveedor=$_REQUEST['provee'];
$cOrdenPago = orden_pago::getOrdenesPagoBy($conn,'2',$proveedor);
if(is_array($cOrdenPago)){ ?>
<span class="titulo_maestro">Seleccione una orden de Pago</span>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1" width="600">
<tr class="cabecera"> 
<td width="120">Nro. Documento</td>
<td align="center">Descripcion</td>
</tr>
<? 
foreach($cOrdenPago as $op) { 
?> 
<tr class="filas" onclick="selOrdenes('<?=$op->nrodoc?>','<?=$op->montodoc?>','<?=$op->montopagado?>','<?=$op->descripcion?>');traeBancos('<?=$op->id_banco?>','divbanco');traeCuentasBancarias2('<?=$op->id_banco?>','divnrocuenta','<?=$op->id_nro_cuenta?>');traeUltimoCheque('<?=$op->id_nro_cuenta?>');" style="cursor:pointer"> 
	<td><span onclick="selOrdenes('<?=$op->nrodoc?>','<?=$op->montodoc?>','<?=$op->montopagado?>','<?=$op->descripcion?>');traeBancos('<?=$op->id_banco?>','divbanco');traeCuentasBancarias2('<?=$op->id_banco?>','divnrocuenta','<?=$op->id_nro_cuenta?>');traeUltimoCheque('<?=$op->id_nro_cuenta?>');" style="cursor:pointer"><?=$op->nrodoc?></span></td>
	<td><span onclick="selOrdenes('<?=$op->nrodoc?>','<?=$op->montodoc?>','<?=$op->montopagado?>','<?=$op->descripcion?>');traeBancos('<?=$op->id_banco?>','divbanco');traeCuentasBancarias2('<?=$op->id_banco?>','divnrocuenta','<?=$op->id_nro_cuenta?>');traeUltimoCheque('<?=$op->id_nro_cuenta?>');" style="cursor:pointer"><?=$op->descripcion?></span></td>
</tr>
<? } ?>
</table>
<? }else {
		echo "No hay registros en la bd";
} ?>