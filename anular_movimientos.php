<?
//set_time_limit(0);
require ("comun/ini.php");

$nrodoc = $_REQUEST['nrodoc'];
$accion = $_REQUEST['accion'];
if ($accion=='Anular'){
	$sql = "SELECT * FROM puser.movimientos_presupuestarios WHERE nrodoc = '$nrodoc'";
	$r = $conn->Execute($sql);
	if($r){
		$q = "INSERT INTO puser.movimientos_presupuestarios ";
		$q.= "(id_usuario, id_unidad_ejecutora, ano, descripcion, nrodoc, tipdoc, nroref, ";//nroref, 
		$q.= "fechadoc, status, id_proveedor, status_movimiento) ";
		$q.= "VALUES ";
		$q.= "('".$r->fields['id_usuario']."', '".$r->fields['id_unidad_ejecutora']."', '".$r->fields['ano']."', '".$r->fields['descripcion']."', '".$r->fields['nrodoc']."', '".$r->fields['tipdoc']."',  '".$r->fields['nrodoc']."-ANULADO', ";//'$nroref', 
		$q.= " '".$r->fields['fechadoc']."', '".$r->fields['status']."', '".$r->fields['id_proveedor']."', '2') ";
		$row = $conn->Execute($q);
		$q2 = "SELECT * FROM puser.relacion_movimientos WHERE nrodoc = '$nrodoc'";
		//die($q2);
		$row2 = $conn->Execute($q2);
		while(!$row2->EOF){
			$q3 = "INSERT INTO puser.relacion_movimientos (nrodoc, id_categoria_programatica, id_partida_presupuestaria, monto, id_parcat) ";
			$q3.= "VALUES ('".$row2->fields['nrodoc']."', '".$row2->fields['id_categoria_programatica']."', '".$row2->fields['id_partida_presupuestaria']."', ".$row2->fields['monto']*(-1).", ".$row2->fields['id_parcat'].")";
			//die($q3);
			$row3 = $conn->Execute($q3);
			$row2->movenext();
		}
		$msj = 'ANULADO CON EXITO';
	}else{
		$msj = 'ERROR AL ANULAR';
	}
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
