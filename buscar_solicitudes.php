<?
include ("comun/ini.php");
$id_ue = $_REQUEST['id_ue'];
$id_prov = $_REQUEST['id_prov'];
$nrodoc = $_REQUEST['nrodoc'];
$opcion = $_REQUEST['opcion'];
$cDocRef = orden_pago::getSolicitudes($conn, '2', $id_ue, $id_prov, $nrodoc);
?>

<? if(is_array($cDocRef)){ ?>
<div style="width:600px" align="center">
<? if($opcion!="2"){ ?>
<span style="position:absolute; margin-top:3px; margin-left:370px;cursor:pointer;">
<img onclick="Dialog.okCallback();" src="images/close_div.gif" /></span>
<span class="titulo_maestro">Solicitudes de Pago</span>

</div>
<table border="0" width="600">
	<tr class="filas">
		<td>Unidad Ejecutora:</td>
		<td align="left"><?=helpers::superCombo($conn, "SELECT id,id ||' - '|| descripcion as descripcion FROM puser.unidades_ejecutoras WHERE id_escenario = '$escEnEje'", '','search_ue','search_ue', 'width=150px', "busca_popup(this.value);")?></td>	
	</tr>
	<tr class="filas">
		<td>Nro Documento:</td>
		<td align="left"><input type="text" name="search_nrodoc" id="search_nrodoc" size="40" onKeyUp="busca_popup(this.value)" ></td>	
	</tr>
	<tr class="filas">
		<td>Proveedor:</td>
		<td align="left"><?=helpers::superCombo($conn, "SELECT id, rif || '-' || nombre AS descripcion FROM puser.proveedores ", '','search_prov','search_prov', 'width=150px', "busca_popup(this.value);")?></td>	
	</tr>
</table>
<br><br>
<? } ?>
<div id="divsolicitudes">
<span class="titulo_maestro">Seleccione un Documento </span>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1" width="600">
<tr class="cabecera"> 
<td>Documentos:</td>
</tr>
<? 
$i = 0;
foreach($cDocRef as $com) { 
?> 
<tr class="filas"> 
<td><span onclick="selDocumento('<?=$com->id_sp?>'); getInfo('<?=$com->id_sp?>');CargarGridPP('<?=$com->id_sp?>','<?=$com->nroref?>');" style="cursor:pointer"><?= $com->id_sp." - ".$com->observacion?></span></td>

</tr>
<? $i++;
	}
?>
</table>
</div>
<? }else {
		echo "No hay registros en la bd";
} ?>