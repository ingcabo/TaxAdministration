<?php 
/*INTENTO MOSTRAR DATOS EN INPUTS SEGUN EL VALOR Q SELECCIONE EN UN SELECT*/
include ("lib/core.lib.php");
$id_proveedores=57;
?>
<html>
<head>
<title>Documento sin t&iacute;tulo</title>
	<script src="js/prototype.js" type="text/javascript"></script>
<script type="text/javascript">
function traefecha(id_requisito, id_proveedor, i){
	var url = 'select_ajax_celdas.php';
	var pars = 'id_requisito=' + id_requisito+'&id_proveedor='+id_proveedor+'&i='+i;
	alert(pars);
	var updater = new Ajax.Updater('formulario_'+i, url,{
		parameters: pars,
		asynchronous:true, 
		evalScripts:true,
		onLoading:function(request){Element.show('cargando')}, 
		onComplete:function(request){Element.hide('cargando')},
		onSuccess:function(){ 
			new Effect.Highlight('formulario', {startcolor:'#fff4f4', endcolor:'#ffffff'});
		} 
	}); 
} 
</script>
</head>
<body>
<?php for($i=1;$i<=3;$i++){ ?>
<table>
  <tr bgcolor="#FFFFFF">
  <!-- " \" onChange=\"MM_jumpMenu('requisitos_proveedor.php?id_proveedores=','self',this,0)\" " -->
    <td width="345"><?=helpers::combo($conn, 'requisitos', $rs->fields['id_requisitos'], '\"  onchange="traefecha(this.value, '.$id_proveedores.', '.$i.') "  ', 'descripcion', 'requisitos[]','requisitos')?></td>
	<td>
	<div id="formulario_<?=$i?>">
	<table><tr>
	<td width="96" align="center"><?=$i?><input type="text" id="fecha_emi" name="fecha_emi[]" value="<?=$rs->fields['fecha_emi']?>" size="12"></td>
	<td width="97" align="center"><input type="text" id="fecha_vcto" name="fecha_vcto[]" value="<?=$rs->fields['fecha_vcto']?>" size="12"></td>
	<td width="102" align="center"><input type="text" id="prorroga" name="prorroga[]" value="<?=$rs->fields['prorroga']?>" size="12"></td>
	</tr>
	</table></div>	</td>
	
  </tr>
</table>
<?php } ?>
</body>
</html>
