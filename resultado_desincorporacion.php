<?php 
require ("comun/ini.php");


$tipo=$_REQUEST['tipo'];
$valor=$_REQUEST['valor'];

//echo $tipo."..".$valor;

	if(empty($tipo) OR empty($valor)){
		echo "Filtro Vacio";
		exit;
	}

	if($tipo=='rif/c'){
		$w= " WHERE contribuyente.rif ILIKE '".$valor."' OR vehiculo.contribuyente.identificacion ILIKE '".$valor."' ";
	}
	if($tipo=='placa'){
		$w= " WHERE vehiculo.placa ILIKE '".strtoupper($valor)."' ";
	}
	if($tipo=='serial'){
		$w= " WHERE vehiculo.serial_motor ILIKE '".$valor."' ";
	}


$q="
SELECT 
  vehiculo.vehiculo.serial_carroceria AS serial_carroceria,
  vehiculo.vehiculo.placa AS placa,
  vehiculo.vehiculo.id AS id_vehiculo,
  vehiculo.desincorporado AS desincorporado,
  vehiculo.vehiculo.id_contribuyente AS id_contribuyente,
  vehiculo.contribuyente.primer_nombre AS primer_nombre,
  vehiculo.contribuyente.primer_apellido AS primer_apellido,
  vehiculo.contribuyente.rif AS rif,
  vehiculo.contribuyente.identificacion AS identificacion
FROM
 vehiculo.vehiculo
 INNER JOIN vehiculo.contribuyente ON (vehiculo.vehiculo.id_contribuyente=vehiculo.contribuyente.id)
";
$q.=$w;



	//die($q);
	$r = $conn->Execute($q);
//echo $r->RecordCount();
if($r->RecordCount()==0){ echo "No hay registros en la bd";} else {
?>

<table align="center" width="635" cellpadding="0" cellspacing="1" class="sortable" id="grid">
<tr class="cabecera"> 
<td width="7%">C&oacute;digo</td>
<td width="17%">Rif/C&eacute;dula</td>
<td width="34%">Nombre</td>
<td width="14%">Placa</td>
<td width="18%">Serial Motor</td>
<td width="5%">&nbsp;</td>
<td width="5%">&nbsp;</td>
</tr>
<?php 	while(!$r->EOF){ 
	if($r->fields['desincorporado']!='' AND $r->fields['desincorporado']!='0')
	{
		$r->movenext();
	}else{
?>
<tr class="filas">
<td><?=$r->fields['id_vehiculo']?></td>
<td><?=$r->fields['rif']?><?=$r->fields['identificacion']?></td>
<td align="center"><?=$r->fields['primer_apellido']." ".$r->fields['primer_nombre']?></td>
<td align="center"><?=$r->fields['placa'];?></td>
<td align="center"><?=$r->fields['serial_carroceria']?></td>
<td align="center">
<a href="#" onClick="updater(<?=$r->fields['id_vehiculo']?>); return false;" title="Modificar �Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
<td align="center">
<?
	$id_vehiculo = $r->fields['id_vehiculo'];

	$q="SELECT cod_vehiculo FROM vehiculo.veh_desincorporado WHERE cod_vehiculo='$id_vehiculo' ORDER BY id DESC Limit 1";

	$w = $conn->Execute($q);

	$nro_recibo = $w->fields['cod_vehiculo'];

	if(!empty($nro_recibo)) {
?>
		<a href="desincorporacion.pdf.php?nro_recibo=<?=$nro_recibo?>" title="Reporte Desincorporaci�" ><img src="images/reporte.jpg" width="16" height="10" border="0"></a>

<? 	}	?>

</td>
</tr>
<?php  	$r->movenext();		}} ?>
</table>

<?php } ?>

