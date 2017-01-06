<?php 
include ("lib/core.lib.php");
$id_proveedores=@$_REQUEST['id_proveedores'];
#PROVEEDORES
	$q = "SELECT * FROM proveedores ORDER BY id";
	$r = $conn->Execute($q);
#PRODUCTOS SEGÚN PROVEEDORES
	$c="select productos.descripcion as descripcion, productos.tiempo_entrega AS tiempo_entrega,
				productos.garantia AS garantia, productos.forma_pago as forma_pago, productos.contrib as contrib
					from proveedores,productos, relacion_producto_prove
					where productos.id=relacion_producto_prove.id
						and proveedores.id=relacion_producto_prove.id_proveedores
						and relacion_producto_prove.id_proveedores=$id_proveedores";
	$rs = $conn->Execute($c);
	
require ("comun/header.php");
?>
<FIELDSET>
Tipos Productos por Proveedor:
<table width="200" border="0">
  <tr >
    <td>Proveedor:</td>
    <td>
	<select name="proveedores" onChange="MM_jumpMenu('productos_proveedor.php?id_proveedores=','self',this,0)">
	<?php 	
	while(!$r->EOF){
	$id = $r->fields['id'];
	$nombre = $r->fields['nombre'];
	?>	
	<option value="<?=$id?>" <?php if(@$id==@$id_proveedores) { echo "selected"; } ?>><?=$nombre?></option>
	<?php $r->movenext();
	} ?>
	</select>
	</td>
  </tr>
</table>

<br><br><br>
<table class="sortable" id="grid"  cellpadding="0" cellspacing="1">
  <tr class="cabecera">
    <td>Producto</td>
    <td>T. Entrega</td>
    <td>Garantía</td>	
    <td>Forma Pago</td>	
    <td>Contrib.</td>	
  </tr>
<?php 	while(!$rs->EOF){ ?>	  
  <tr class="filas">
    <td width="525"><?=$rs->fields['descripcion']?></td>
    <td width="185"><?=$rs->fields['tiempo_entrega']?></td>
    <td width="145"><?=$rs->fields['garantia']?></td>	
    <td width="158"><?=$rs->fields['forma_pago']?></td>	
    <td width="66"><?=$rs->fields['contrib']?></td>	
<?php @$rs->movenext();
		} ?>	
  </tr>
</table>
</FIELDSET>
<? require ("comun/footer.php"); ?>