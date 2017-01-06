<?
include ("comun/ini.php");

$descripcion = $_REQUEST['desc'];
$opcion = $_REQUEST['opcion'];
$cDocRef = traFondosTerceros::getAporRetXpagar($conn, $descripcion);
?>

<? if(is_array($cDocRef)){ ?>
<div style="width:600px" align="center">
<? if($opcion!="2"){ ?>
<span style="position:absolute; margin-top:3px; margin-left:370px;cursor:pointer;">
<img onClick="Dialog.okCallback();" src="images/close_div.gif" /></span>
<span class="titulo_maestro">Cuentas Contables</span>
</div>
<table border="0" width="600">
	<tr class="filas">
		<td>Descripcion:</td>
		<td align="left"><input type="text" name="search_descrip" id="search_descrip" size="40" onKeyUp="busca_popup(this.value)" ></td>	
	</tr>
</table>
<? } ?>
<br><br>
<div id="divcuentas">
<span class="titulo_maestro">Seleccione una Cuenta </span>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1" width="600">
<tr class="cabecera"> 
<td>Documentos:</td>
</tr>
<? 
$i = 0;
foreach($cDocRef as $com) { 
	if($com->saldo > 0) {
?> 
		<tr class="filas"> 
		<td><span onClick="selDocumento('<?=$com->idCta?>','<?=$com->descripcion?>','<?=$com->saldo?>');" style="cursor:pointer" ><?= $com->codCta?> - <?=$com->descripcion?> - <?=muestraFloat($com->saldo)?></span></td>

		</tr>
<? 
	}
	$i++;
}
?>
</table>
</div>
<? }else {
		echo "No hay registros en la bd";
} ?>