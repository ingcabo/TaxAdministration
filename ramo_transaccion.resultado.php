<?php 
require ("comun/ini.php");

$tipo=$_REQUEST['tipo'];
$valor=$_REQUEST['valor'];

//echo $tipo."..".$valor;

	if(empty($tipo) OR empty($valor)){
		echo "Filtro Vacio";
		exit;
	}

	if($tipo==1){
		$w= "WHERE ramo_imp.id=".$valor;
	}
	if($tipo==2){
		$w= " WHERE tipo_transaccion.id=".$valor;
	}
	if($tipo==3){
		$w= " WHERE ramo_transaccion.anio=".$valor;
	}


$q="SELECT
  vehiculo.ramo_imp.id AS ramo_id,
  vehiculo.ramo_transaccion.anio AS anio,
  vehiculo.ramo_transaccion.id AS id_trans,
  vehiculo.ramo_imp.ramo AS ramo,
  vehiculo.ramo_imp.descripcion AS ramo_des,
  vehiculo.tipo_transaccion.descripcion AS des_trans
FROM
 vehiculo.ramo_transaccion
 INNER JOIN vehiculo.ramo_imp ON (vehiculo.ramo_transaccion.id_ramo_imp=vehiculo.ramo_imp.id)
 INNER JOIN vehiculo.tipo_transaccion ON (vehiculo.ramo_transaccion.id_tipo_transaccion=vehiculo.tipo_transaccion.id)";
$q.=$w;
 
$r=$conn->Execute($q); 
if($r->RecordCount()>=1){
?>
<table width="635" cellpadding="0" cellspacing="1" class="sortable" id="grid">
  <tr class="cabecera">
    <td>C&oacute;digo</td>
    <td>Ramo</td>
    <td>Transacci&oacute;n</td>
    <td>A&ntilde;o</td>
    <td>&nbsp;</td>
  </tr>
<?php while(!$r->EOF){ ?>  
  <tr class="filas">
    <td><?=$r->fields['id_trans']?></td>
    <td><?=$r->fields['ramo_des']?></td>
    <td><?=$r->fields['des_trans']?></td>
    <td><?=$r->fields['anio']?></td>
<td align="center">
<a href="#" onclick="updater(<?=$r->fields['id_trans']?>); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
  </tr>
<?php $r->MoveNext(); } ?>  
</table>
<?php }else{
echo "No Hay Registros en la db.";
} ?>
