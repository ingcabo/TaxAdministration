<? require ("comun/ini.php"); 
$q="SELECT A.int_cod,A.fecha,B.tra_nom,B.tra_ape,A.descripcion FROM rrhh.pago_acumulado AS A INNER JOIN rrhh.trabajador AS B ON A.tra_cod=B.int_cod ORDER BY A.fecha,B.tra_nom";
$r= $conn->Execute($q);
if(!$r->EOF){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>Fecha</td>
<td>Trabajador</td>
<td>Descripcion</td>
<td>&nbsp;</td>
</tr>
<? 
while(!$r->EOF) {  ?> 
<tr class="filas"> 
<td><?=muestrafecha($r->fields['fecha'])?></td>
<td align="center"><?=Cadena($r->fields['tra_nom'])." ".cadena($r->fields['tra_ape'])?></td>
<td align="center"><?=$r->fields['descripcion']?></td>
<td align="center"><?=!empty($r->fields['int_cod']) ? '<a href="pago_acumulado.pdf.php?id='.$r->fields['int_cod'].'" target="_blank" title="Emitir Reporte"><img style="width:16px; height:10px" src="images/reporte.jpg" border="0" /></a>' : "" ?></td>
</tr>
<? 
$r->movenext();
}
?>
</table>
<? }else {
		echo "No hay registros en la bd";
} ?>
