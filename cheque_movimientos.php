<?
//set_time_limit(0);
require ("comun/ini.php");

	$q = "SELECT * FROM finanzas.cheques ORDER BY nrodoc";
	$r = $conn->Execute($);
	while(!$r->EOF){
		$q = "SELECT nroref FROM finanzas.relacion_cheque WHERE nrodoc = '".$row->fields['nrodoc']."'";
		$row = $conn->Execute($r);
		$q = "SELECT id_unidad_ejecutora,id_proveedor,nroref FROM finanzas.orden_pago WHERE nrodoc='".$row->fields['nroref']."' ";
		$rM = $conn->Execute($q);
		if($rM->fields['id_unidad_ejecutora']){
			$idUniEje=$rM->fields['id_unidad_ejecutora'];
			$q = "INSERT INTO movimientos_presupuestarios ";
			$q.= "(id_usuario, id_unidad_ejecutora, ano, descripcion, nrodoc, tipdoc, tipref,nroref, status_movimiento,  ";//nroref, 
			$q.= "fechadoc, fecharef, status, id_proveedor) ";
			$q.= "VALUES ";
			$q.= "(22, '$idUniEje', 2008, '".$r->fields['observaciones']."', '".$r->fields['nrodoc']."', '005', '','',1, ";//'$nroref', 
			$q.= " '"$r->fields['fecha']"', '"$r->fields['fecha']"', '3', "$r->fields['id_proveedor']") ";
	}

include('comun/header.php');
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? } ?>
<div id="formulario" style="text-align:center">
	<p style="font-weight:bold; font-size:larger; color:#C82619">Anular movimientos con error</p><br />
	<form name="form1" id="form1" method="post">
		<input type="hidden" name="accion" id="accion" />
		<table align="center">
			<tr>
				<td width="120px">Numero Documento:</td>
				<td width="50px"><input type="text" name="nrodoc" id="nrodoc"/></td>
			</tr>
			<tr>
				<td colspan="2" align="right"><br /><input type="submit" name="accion" value="Anular" /></td>
			</tr>
		</table>
	</form>
</div>

<div id="xxx"></div>
<?
//$validator->print_script();
require ("comun/footer.php");
?>
