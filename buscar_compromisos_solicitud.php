<?
set_time_limit(0);
include ("comun/ini.php");
$id_ue = $_REQUEST['id_ue'];
$tipdoc = $_REQUEST['tipdoc'];
$nrodoc = $_REQUEST['nrodoc'];
$opcion = $_REQUEST['opcion'];
$cDocRef = movimientos_presupuestarios::getMovimientosStatus($conn, 2, '1', $nrodoc, $id_ue, $tipdoc);
?>
<? if(is_array($cDocRef)){ ?>
<div style="width:600px" align="center">
<? if($opcion!="2"){ ?>
<span style="position:absolute; margin-top:3px; margin-left:370px;cursor:pointer;">
<img onclick="Dialog.okCallback();" src="images/close_div.gif" /></span>
<span class="titulo_maestro">Busqueda del Compromiso</span>

</div>
<table border="0" width="600">
	<tr class="filas">
		<td>Tipo Documento:</td>
		<td align="left"><?=helpers::superCombo($conn, "SELECT * FROM puser.tipos_documentos WHERE id_momento_presupuestario = '1'", '','search_tip_doc','search_tip_doc', 'width=150px', "busca_popup(this.value);")?></td>	
	</tr>
	<tr class="filas">
		<td>Unidad Ejecutora:</td>
		<td align="left"><?=helpers::superCombo($conn, "SELECT * FROM puser.unidades_ejecutoras WHERE id_escenario = '$escEnEje'", '','search_ue','search_ue', 'width=150px', "busca_popup(this.value);")?></td>	
	</tr>
	<tr class="filas">
		<td>Nro Documento:</td>
		<td align="left"><input type="text" name="search_nrodoc" id="search_nrodoc" size="40" onkeyup="busca_popup(this.value)" ></td>	
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
<td><span onclick="selDocumento('<?=$com->id2?>'); getInfo('<?=$com->id?>');CargarGridPP('<?=$com->id?>');" style="cursor:pointer"><?=$com->descripcion?></span></td>

</tr>
<? $i++;
	}
?>
</table>
</div>
<? }else {
		echo "No hay registros en la bd";
} ?>