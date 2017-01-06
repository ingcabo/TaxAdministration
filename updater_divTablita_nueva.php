<?
include ("comun/ini.php");
$nrodoc = $_REQUEST['id'];
$mp = $_REQUEST['mp'];
$cRelaciones = movimientos_presupuestarios::get_relaciones($conn, $nrodoc, $escEnEje);
$montos = 0;
$i = 0;
switch($mp){
case 2:
	$comprometido = movimientos_presupuestarios::get_suma_monto($conn, $nrodoc);
	break;
case 3:
	$nrorefCausa = movimientos_presupuestarios::get_nroref($conn, $nrodoc);
	$causado = movimientos_presupuestarios::get_suma_monto($conn, $nrodoc);
	$comprometido = movimientos_presupuestarios::get_suma_monto($conn, $nrorefCausa);
	break;
}
if($mp == 2){
?>
	<!--sumatoria de lo comprometido para ese doc -->
	<input type="hidden" id="totalComprometido" value="<?=$comprometido?>" />
<? }
if($mp == 3){
?>
	<!--sumatoria de lo comprometido para ese doc -->
	<input type="hidden" id="totalComprometido" value="<?=$comprometido?>" />
	<!--sumatoria de lo causado para ese doc -->
	<input type="hidden" id="totalCausado" value="<?=$causado?>" />
<? } ?>
<table align="center" border=0 id="tablita">
<?
if(is_array($cRelaciones)){
foreach($cRelaciones as $relacion){
	//si el documento es referenciado, obtengo un array con los nrodoc de quien hace referencia a el
	
	/*$cNrodocReferencia = movimientos_presupuestarios::get_nrodoc($conn, $relacion->nrodoc);
	
	$montoReferencia = 0;
	if(is_array($cNrodocReferencia)){
	
		foreach($cNrodocReferencia as $nrodocRef){
			print_r($nrodocRef);
			die("o");
			$montoReferencia += movimientos_presupuestarios::get_monto($conn, $nrodocRef,
																		$relacion->id_categoria_programatica,
																		$relacion->id_partida_presupuestaria);
			
		}
		
	}*/
	
	echo("=".$relacion->montoporcausar);
	// le resto al monto del documento el monto de los que lo referencian
	
	$nuevoMonto = $relacion->monto - $montoReferencia;
?>
	<tr>
		<td>Categoria:</td>
		<td>
			<input type="text" readonly value="<?=$relacion->categoria_programatica?>" />
			<input type="hidden" id="categorias_programaticas_<?=$i?>" name="categorias_programaticas[]" value="<?=$relacion->id_categoria_programatica?>" />
		</td>
		<td>Partida Presupuestaria:</td>
		<td>
			<input type="text" readonly value="<?=$relacion->partida_presupuestaria?>" />
			<input type="hidden" id="partidas_presupuestarias_<?=$i?>" name="partidas_presupuestarias[]" value="<?=$relacion->id_partida_presupuestaria?>" />
		</td>
		<td>Monto:</td>
		<td><input value ="0" title="Comprometido para esta partida: <?=muestrafloat($nuevoMonto)?>" name="monto[]" id="monto_<?=$i?>" class="monto" onkeypress="return(formatoNumero (this,event));" onblur="operacion(this, $F('categorias_programaticas_<?=$i?>'), $F('partidas_presupuestarias_<?=$i?>'));" onclick="traeParCatDesdeXML($('categorias_programaticas_<?=$i?>').value, $('partidas_presupuestarias_<?=$i?>').value, this.id);" type="text" />
		<input type="hidden" id="max_comp_<?=$i?>" value="<?=$nuevoMonto?>" /><!--comprometido para la partida -->
		<input type="hidden" name="idParCat[]" id="idParCat_<?=$i?>" value="0" />
		<input type="hidden" id="nuevoMontoParCat_<?=$i?>" name="nuevoMontoParCat[]" value="0" />
		</td>
	</tr>
<? $i++; } } else{ ?>
<p style="">No hay partidas</p>
<? } ?>
</table>