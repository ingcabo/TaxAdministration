<?php
require ("comun/ini.php");
$id=$_REQUEST['id'];
?>
		<h3>Informacion de pago</h3>

			<table align="left" cellpadding="0" cellspacing="1" width="59%">
				<tr class="cabecera"> 
					<td  align="left"><b>Tipo Publicidad </b></td>
					<td align="right"><b>Cantidad Publicidad </b></td>
					<td  align="right"><b>Unidad </b></td>
					<td  align="right"><b>Aforo </b></td>
					<td  align="right"><b>Impuesto </b></td>
				</tr>
				
<? $w = "SELECT publicidad.calculo_publicidad.*, publicidad.tipo_publicidad.descripcion  FROM publicidad.calculo_publicidad INNER JOIN publicidad.tipo_publicidad ON (publicidad.calculo_publicidad.id_tipopub=publicidad.tipo_publicidad.cod_publicidad)  WHERE id_publicidad='$id'";//die($w);
					$r = $conn->Execute($w);
					$i=0; 
while(!$r->EOF){ ?>
				<tr class="filas"> 
					<td align="left"><?=$r->fields['descripcion'];?></td>
					<td align="right"><?=$r->fields['cantidad'];?></td>
					<td align="right"><?=$r->fields['unidad'];?></td>
					<td align="right"><?=$r->fields['aforo'];?></td>
					<td align="right"><?=$r->fields['cantidad']*$r->fields['aforo'];?></td>
				</tr>
<? 	$i++;
	$r->movenext();
}

	$q = "SELECT sum(publicidad.calculo_publicidad.cantidad*publicidad.calculo_publicidad.aforo) as total FROM publicidad.calculo_publicidad where id_publicidad='$id'";
	$r = $conn->Execute($q);
	$total= $r->fields['total'];  
?>
</table>				
<br><p>
<br>
<br>
<br>
<br>
<p>
<p>
<p>
<p>
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
              <tr><td align="right"><input style="text-align:right" type="text" name="monto_total" id="monto_total" size="25" readonly="readonly" value="<?=muestrafloat($total);?>" align="right">
                  Bs.</td>
                <td align="left"><input name="btn_Pagar" type="button" id="btn_Pagar"  onclick="Pagar()" value="Pagar" />
                    <input name="accion3" type="hidden" id="accion3" /> </td>
              </tr>
         </table>