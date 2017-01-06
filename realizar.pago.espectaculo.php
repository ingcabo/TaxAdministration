<?php
require ("comun/ini.php");
$id=$_REQUEST['id'];
?>
		<h3>Informacion de pago</h3>

			<table align="left" cellpadding="0" cellspacing="1" width="65%">
				<tr class="cabecera"> 
					<td  align="left"><b>Tipo Espectaculo </b></td>
					<td align="right"><b>Cantidad Entardas </b></td>
					<td  align="right"><b>Valor de la Entrada </b></td>
					<td  align="right"><b>Aforo </b></td>
					<td  align="right"><b>Impuesto a Pagar </b></td>
					
				</tr>
				
<? $w ="SELECT publicidad.calculo_espectaculo.*, publicidad.entradas.descripcion FROM publicidad.calculo_espectaculo 
inner join publicidad.entradas on (publicidad.calculo_espectaculo.id_entradas=publicidad.entradas.id) 
WHERE publicidad.calculo_espectaculo.id_espectaculo ='$id'"; //die($w);
					$r = $conn->Execute($w);
					$i=0; 
while(!$r->EOF){ 
?>
				<tr class="filas"> 
					<td align="left"><?=$r->fields['descripcion'];?></td>
					<td align="right"><?=muestrafloat($r->fields['cant_entradas']);?></td>
					<td align="right"><?=muestrafloat($r->fields['costo_entrada']);?></td>
					<td align="right"><?=$r->fields['ut_espectaculo']." ".'%';?></td>
					<td align="right"><?=muestrafloat(($r->fields['cant_entradas'])*($r->fields['costo_entrada'])*($r->fields['ut_espectaculo']/100));?></td>
				</tr>
<? 
$i++;
$r->movenext();
}

$q = "select sum((publicidad.calculo_espectaculo.cant_entradas * publicidad.calculo_espectaculo.costo_entrada)*(publicidad.calculo_espectaculo.ut_espectaculo/100)) as total 
from publicidad.calculo_espectaculo where id_espectaculo='$id'";
	$r = $conn->Execute($q);
	$total= $r->fields['total'];
?>
</table>				
<br><p>
<br><p>
<br>
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
								$nombre='tipo_de_pago', 
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
								$nombre='banco', 
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
							<input type="text" name="nro_documento" id="nro_documento" size="20">
						</div>
					</td>
					<td>
<div id="monto">
<input type="text" name="monto" style="text-align:right" id="monto" class="monto_fila" size="25" align="right" onKeyPress="return(formatoNumero(this, event));">
					  </div>
					</td>
</table>
            <table width="443" align="center">
				<tr>
					<td width="243" align="right">Monto Total del Impuesto </td>
					<td width="188"></td> 
				</tr>
				<tr> 
<td align="right"><input style="text-align:right" type="text" name="monto_total" id="monto_total" size="25" readonly="readonly" value="<?=muestrafloat($total);?>" align="right" >Bs.</td>
<td align="left"><input name="btn_Pagar" type="button" id="btn_Pagar"  onclick="Pagar()" value="Pagar">
						<input name="accion2" id="accion2" type="hidden" />
				  </td>
				</tr>
</table>