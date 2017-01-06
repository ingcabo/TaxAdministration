<?php 
require ("comun/ini.php");


$tipo=$_REQUEST['tipo'];
$valor=$_REQUEST['valor'];

//echo $tipo."..".$valor;

	if(empty($tipo) OR empty($valor)){
		echo "Filtro Vacio";
		exit;
	}

	if($tipo=='cedula'){
		$w= "where publicidad.inspector.cedula=".$valor;
	}
	if($tipo=='patent'){
		$w= " WHERE resultado_inspeccion.patente ILIKE '".$valor."' AND resultado_inspeccion.status = '1' ";
	}
$q="
select * from publicidad.inspector inner join publicidad.resultado_inspeccion on (publicidad.inspector.cod_ins=publicidad.resultado_inspeccion.cod_inspector)
";
$q.=$w;die();
$r = $conn->Execute($q);
if($r->RecordCount()==0){ echo "No hay registros en la bd";} else {
?>

<table align="center" width="635" cellpadding="0" cellspacing="1" class="sortable" id="grid">
<tr class="cabecera"> 
<td width="7%">C&oacute;digo</td>
<td width="44%">Patente</td>
<td width="40%">Status</td>
<td width="9%">&nbsp;</td>
</tr>
<?php 	while(!$r->EOF){
	
		if ($r->fields['status']==1) 
		{ 
			$text_status='Activo'; 
		}
		else
		{ 
			$text_status='Inactivo'; 
		}
	
?>
<tr class="filas">
<td align="right"><?=$r->fields['cod_asignacion']?></td>
<td align="right"><?=$r->fields['patente']?></td>
<td align="center"><?=$text_status?></td>
<td align="center">
<a href="#" onClick="updater(<?=$r->fields['patente']?>); return false;" title="Modificar &eacute; Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
</tr>
<?php  	$r->movenext();		}  ?>
</table>

<?php } ?>

