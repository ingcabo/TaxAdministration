<?php
#ESTA 
include ("lib/core.lib.php");
$req=$_REQUEST['id_requisito'];
$prov=$_REQUEST['id_proveedor'];
$i=$_REQUEST['i'];
#REQUISITOS POR PROVEEDOR
	$c="select * from relacion_req_prov where id_proveedores=".$prov." and id_requisitos='$req'";
	$rs = $conn->Execute($c);
	$tt=@$rs->RecordCount();


$tbl='	<table><tr>
	<td width="96" align="center"><input type="text" id="fecha_emi" name="fecha_emi[]" value="'.$rs->fields['fecha_emi'].'" size="12"></td>
	<td width="97" align="center"><input type="text" id="fecha_vcto" name="fecha_vcto[]" value="'.$rs->fields['fecha_vcto'].'" size="12"></td>
	<td width="102" align="center"><input type="text" id="prorroga" name="prorroga[]" value="'.$rs->fields['prorroga'].'" size="12"></td>
	</tr>
	</table>';
//echo $tbl;
//echo $req."...".$prov."..".$i;
//echo "-->".$req."<-- -->".$prov;
 header ("content-type: text/xml");
 ?>
 <requisito><fecha_emi><?=$rs->fields['fecha_emi']?></fecha_emi><fecha_vcto><?=$rs->fields['fecha_vcto']?></fecha_vcto><prorroga><?=$rs->fields['prorroga']?></prorroga></requisito>