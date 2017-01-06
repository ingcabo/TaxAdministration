<?php 
require ("comun/ini.php");


$tipo=$_REQUEST['tipo'];
$valor=$_REQUEST['valor'];

//echo $tipo."..".$valor;

	if(empty($tipo) OR empty($valor)){
		echo "Filtro Vacio";
		exit;
	}

	if($tipo=='patent'){
		$w= " WHERE espectaculo.patente ILIKE '".$valor."' ";
	}
	


$q="
SELECT
	publicidad.espectaculo.id_espectaculo AS id,
	publicidad.espectaculo.patente AS patente
FROM
	publicidad.espectaculo";
$q.=$w;
//die($q);

$r = $conn->Execute($q);
//echo $r->RecordCount();
if($r->RecordCount()==0){ echo "No hay registros en la bd";} else {

?>

<table align="center" width="236" cellpadding="0" cellspacing="1" class="sortable" id="grid">
<tr class="cabecera"> 
<td width="20%">C&oacute;digo</td>
<td width="42%">Patente</td>
<td width="13%">&nbsp;</td>
<td width="25%">&nbsp;</td>
</tr>
<? $i=0; 
while(!$r->EOF){?>
<tr class="filas">
<td align="right"><?=$r->fields['id']?></td>
<td align="right"><?=$r->fields['patente']?></td>
<td align="center">
<a href="#" onClick="updater(<?=$r->fields['id']?>); return false;" title="Modificar &oacute; Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
<td align="center">
<?
	$patente = $r->fields['patente'];
	$z="SELECT * FROM publicidad.espectaculo WHERE id_espectaculo=".$r->fields['id']. " and monto is not NULL"; //die($z);
	$z = $conn->Execute($z);
	$nro_recibo = $z->fields['monto'];
	if(!empty($nro_recibo)) {
?>
		<a href="recibo.pago.espectaculo.pdf.php?id=<?=$r->fields['id'];?>" title="Recibo Pago Impuesto Espectaculo" ><img src="images/reporte.jpg" width="16" height="10" border="0"></a>

<? 							}	?>

</td>

<?php  $i++;
$r->movenext();	
	}
}  ?>
</tr>
</table>