<?
include ("comun/ini.php");
$cp = new categorias_programaticas;
$escenario = !empty($_REQUEST['escenario']) ? $_REQUEST['escenario'] : $escEnEje;
//$escenario = $_GET['escenario'];
//die("aqui".$escenario);
$cCategorias = $cp->get_all_by_ue($conn, $_GET['ue'], $escenario);//die(print_r ($cCategorias));
?>
<span style="position:absolute; margin-top:3px; margin-left:370px;cursor:pointer;">
<img onclick="Dialog.okCallback();" src="images/close_div.gif" /></span>
<? if(is_array($cCategorias)){ ?>
<span class="titulo_maestro">Seleccione Un Categoria</span>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1" width="600">
<tr class="cabecera"> 
<td>Categorias Program&aacute;ticas:</td>
</tr>
<? 
$i = 0;
foreach($cCategorias as $cat) { 
?> 
<tr class="filas"> 
<td><span onclick="selCategorias('<?=$cat->id?>', '<?=$cat->descripcion?>');" style="cursor:pointer"><?=$cat->id?> - <?=$cat->descripcion?></span></td>

</tr>
<? $i++;
	}
?>
</table>
<? }else {
		echo "No hay registros en la bd";
} ?>