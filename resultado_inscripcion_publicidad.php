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
		$w= " WHERE inscripcion_publicidad.patente ILIKE '".$valor."' ";
	}
	


$q="
SELECT
	publicidad.inscripcion_publicidad.id_inscripcion_publicidad AS id,
	publicidad.inscripcion_publicidad.patente AS patente
FROM
 publicidad.inscripcion_publicidad";
$q.=$w;
//die($q);

	$r = $conn->Execute($q);
//echo $r->RecordCount();
if($r->RecordCount()==0){ echo "No hay registros en la bd";} else {
?>

<table align="center" cellpadding="0" cellspacing="1" class="sortable" id="grid">
<tr class="cabecera"> 
<td width="17%">C&oacute;digo</td>
<td width="70%">Patente</td>
<td width="13%"></td>

</tr>
<tr class="filas">
<td align="right"><?=$r->fields['id']?></td>
<td align="right"><?=$r->fields['patente']?></td>
<td align="center">
<a href="#" onClick="updater(<?=$r->fields['id']?>); return false;" title="Modificar &eacute; Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>

</tr>
<?php  	$r->movenext();		}  ?>
</table>



