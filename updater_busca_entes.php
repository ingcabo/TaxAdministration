<?
include ("comun/ini.php");
$nombre = $_GET['nombre'];
$siglas = $_GET['siglas'];
$pagina = $_REQUEST['pagina'];

$tamano_pagina = 20;
$pagina = $_REQUEST['pagina'];
if (!$pagina)
{
	$pagina = 1;
	$inicio = 0;
}
else
	$inicio = ($pagina - 1) * $tamano_pagina;
$cEntes = entes::buscar($conn, 
						$nombre, 
						$siglas, 
						'id',$inicio,$tamano_pagina);
$total = entes::totalRegistroBusqueda($conn,$nombre,$siglas,'id');
?>
<? if(is_array($cEntes)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Nombre:</td>
<td>Siglas:</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cEntes as $Entes) { 
?> 
<tr class="filas"> 
<td><?=$Entes->id?></td>
<td><?=$Entes->nombre?></td>
<td><?=$Entes->siglas ?></td>
<td align="center"><a href="?accion=del&id=<?=$Entes->id?>" onClick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
<td align="center">
<a href="#" onClick="updater('<?=$Entes->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
</tr>
<? $i++;
	}
	$total_paginas = ceil($total / $tamano_pagina);
?>
	<tr class="filas">
		<td colspan="7" align="center">
		<?
		for ($j=1; $j<=$total_paginas; $j++)
		{
			if ($j==$pagina)
				echo '<span class="actual">'.($j>1 ? ' - ':'').$j.'</span>';
			else
				echo '<span style="cursor:pointer" onclick="busca($F(\'busca_nombre\'), $F(\'busca_siglas\'), '.$j.')">'.($j>1 ? ' - ':'').$j.'</span>';
		}
		?>
		</td>
	</tr>
	<tr class="filas">
		<td colspan="7" align="center"> Pagina <strong><?=$pagina?></strong> de <strong><?=$total_paginas?></strong></td>
	</tr>
</table>
<? }else {
		echo "No hay registros en la bd";
} ?>