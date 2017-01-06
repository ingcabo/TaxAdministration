<?
include ("comun/ini.php");
require('comun/header_popup.php');
$cProveeCoti= new revision_requisicion;

//$idp = $_REQUEST['id_productos'];
$id_requisicion= $_REQUEST['id_requisicion'];?>
<input type="hidden" id="idr" name="idr" value="<?=$id_requisicion?>" />
<?
if ($_REQUEST['accion']=='Guardar'){
	
		$cProveeCoti->guardaProveedoresParaCotizacion($conn,$id_requisicion,$_REQUEST['ch_id']);
	
		
?>
	<script language="javascript" type="text/javascript">
	function cierraVentana(){
	Dialog.alert("El registro se actualizo con exito", {windowParameters: {width:300, height:100}, okLabel: "close", ok:function(win) {debug("validate alert panel"); return true;}});
	setTimeout(window.close, 2000)
	//window.close();
	}
	
	function muestraReporte(idr){
		//alert(idr);
		window.open('solicitud_cotizacion.pdf.php?id_requisicion='+idr,'', ' menubar=no, height=900, width=1200, top=1, left=1, scrollbars=no, resizable=no ');
	}
	
</script>
	<script type="text/javascript">
		muestraReporte($('idr').value);
		cierraVentana();
	</script>
	
<?	
exit;	
}
	$idp = $cProveeCoti->buscaProductos($conn,$id_requisicion);
	$cCotizaciones= $cProveeCoti->busca_proveedores_cotizacion($conn, $idp);
	
?>

<? if(is_array($cCotizaciones)){ ?>
<form name="form2" id="form2" action="?accion=Guardar" method="post">
<input type="hidden" name="id_requisicion" id="id_requisicion" value="<?= $id_requisicion?>" />
<span class="titulo_maestro">Seleccione Proveedores para Generar Solicitudes de Cotizaciones </span>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1" width="600">
<tr class="cabecera"> 
<td colspan="2">Proveedores:</td>
</tr>
<? 
$i = 0;
foreach($cCotizaciones as $pro) { 
?> 
<tr class="filas"> 
<td width="75" align="center"><input type="checkbox" name="ch_id[]" id="<?= $i ?>" value="<?= $pro->id ?>"></td>
<td><? echo $pro->nombre_proveedor?></td>

</tr>
<? $i++;
	}
?>
<tr class="filas">
	<td colspan="2">&nbsp;</td>
</tr>
<tr class="filas">
	<td colspan="2" align="center"><input type="button" name="boton" id="boton" value="Aceptar" onclick="prov_min();"></td>
</tr>
</table>
</form>
<? }else {
		echo "No hay registros en la bd";
} ?>

<script language="javascript" type="text/javascript">
	function prov_min(){
		var aux = document.getElementsByName("ch_id[]");
		var cont = 0;
		for(i=0;i<aux.length;i++){
			if(aux[i].checked)
				cont++ 
		}
		if(cont<1){
			alert('Debe seleccionar un minimo de 3 Proveeores');
		} else {
			$("form2").submit();
		}
	}

		
</script>
