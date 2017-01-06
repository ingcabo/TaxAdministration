<?
include ("comun/ini.php");
$id_cp = $_REQUEST['id_cp'];
$descripcion = $_REQUEST['descripcion'];
$opcion = $_REQUEST['opcion'];
$cProductos = requisiciones::buscaProductoporCategoria($conn,$id_cp, $descripcion);
?>
<input type="hidden" name="id_cp" id="id_cp" value="<?=$id_cp?>">
<span style="position:absolute; margin-top:3px; margin-left:370px;cursor:pointer;">
<img onclick="Dialog.okCallback();" src="images/close_div.gif" /></span>
<? if(is_array($cProductos)){ ?>
<div style="width:600px" align="center">
<? if($opcion!="2"){ ?>
<span class="titulo_maestro">Ingrese la descripcion</span>

</div>
<table border="0" width="600">
	<tr class="filas">
		<td align="center"><input type="text" name="descrip" id="descrip" size="40" onKeyUp="xxxxx(this.value)" ></td>	
	</tr>
</table>
<br><br>
<? } ?>
<div id="divproductos">
<span class="titulo_maestro">Seleccione Un Producto</span>

<table class="sortable" id="grid" cellpadding="0" cellspacing="1" width="600">
<tr class="cabecera"> 
<td>Productos:</td>
</tr>
<? 
$i = 0;
foreach($cProductos as $pro) { 
?> 
<tr class="filas"> 
<td><span onclick="traeProductos('<?=$pro->id?>', '<?=$pro->descripcion?>')" style="cursor:pointer"><?=$pro->descripcion?></span></td>

</tr>
<? $i++;
	}
?>
</table>
</div>
<? }else {
		echo "No hay registros en la bd";
} ?>
<script language="javascript" type="text/javascript">



</script>