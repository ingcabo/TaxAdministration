<?
require ("comun/ini.php");



$idCta = $_POST['idCta'];
$accion = $_POST['accion'];

if ($accion == 'Guardar')
{
	$sql = "INSERT INTO contabilidad.fondos_terceros (id_cta) VALUES ($idCta)";
	$rs = $conn->Execute($sql);
	
	if ($rs === false)
		$msj = ERROR;
	else
		$msj = "Se registro la cuenta con exito";
} elseif ($accion == 'Actualizar') {
	$sql = "UPDATE contabilidad.fondos_terceros SET id_cta = $idCta";
	//die($sql);
	$rs = $conn->Execute($sql);
	if ($rs === false)
		$msj = ERROR;
	else
		$msj = "Se registro la cuenta con exito";
}

$q = "SELECT id_cta FROM contabilidad.fondos_terceros";
$rs = $conn->Execute($q);
if (($rs->fields['id_cta'])!== null)
{
	$boton = 'Actualizar';
	$idCuenta = $rs->fields['id_cta'];
} else
	$boton = 'Guardar';	

require ("comun/header.php");
?>
<div id="msj" <?=($cerrar || !empty($accion)) ? '':'style="display:none"'?>><?=$msj?></div><br /><br />
<span class="titulo_maestro">Cuenta de Fondos a Terceros</span>
<div id="formulario" style="text-align:center">
	<p style="font-weight:bold; font-size:larger; color:#C82619">Parametrizacion de la cuenta de Fondos a Terceros</p><br />
	<form name="form1" id="form1" method="post">
		<table align="center">
			<tr>
				<td>Cuenta Contable:</td>
				<td><? 
                	$q = "SELECT id, (codcta || ' - ' || descripcion)::varchar AS descripcion FROM contabilidad.plan_cuenta WHERE id_escenario ='$escEnEje' AND movim='S'";
					$q.= "AND id NOT IN (SELECT COALESCE(id_cuenta_contable::int8, 0) FROM contabilidad.relacion_cc_pp WHERE id_escenario = '$escEnEje' ".(!empty($objeto->id) ? "AND id_cuenta_contable <> ".$objeto->id_cuenta_contable:"").") ";
					//$q.= "AND id NOT IN (SELECT COALESCE(id_plan_cuenta::int8, 0) FROM finanzas.cuentas_bancarias) ";
					$q.= "AND id NOT IN (SELECT COALESCE(cta_contable::int8, 0) FROM puser.proveedores) ";
					$q.= "AND id NOT IN (SELECT COALESCE(cuenta_contable::int8, 0) FROM finanzas.tipos_solicitud_sin_imp) ";
					$q.= "AND id NOT IN (SELECT COALESCE(cuenta_contable::int8, 0) FROM rrhh.concepto) ";
					$q.= "AND id NOT IN (SELECT COALESCE(id_cta::int8, 0) FROM finanzas.retenciones_adiciones) ";
					$q.= "ORDER BY codcta::text ";
					echo helpers::superComboSQL($conn, 
												'',
												$idCuenta,
												'idCta',
												'idCta',
												'',
												'',
												'id',
												'descripcion',
												false,
												'',
												$q, 
												80)?>
                                                <span class="errormsg" id="error_idCta">*</span>
												<?=$validator->show("error_idCta")?>
                                                </td>
			</tr>
			<tr>
				<td colspan="2" align="right"><br /><input type="button" name="boton" id="boton" value="<?=$boton?>" onclick="guarda(this.value)" /></td>
                <input type="hidden" name="accion" id="accion" />
			</tr>
		</table>
	</form>
</div>
<div style="height:40px;padding-top:10px;">
<p id="cargando" style="display:none;margin-top:0px;">
  <img alt="Cargando" src="images/loading.gif" /> Cargando...
</p>
</div>

<script type="text/javascript">
	
	function guarda(valor)
	{
		if($('idCta').value != 0){
			$('accion').value = valor;
			form1.submit();
		} else {
			alert('Debe seleccionar una cuenta contable');
			return false;
		}
	
	}
	
</script>

<? 
$validator->create_message("error_idCta", "idCta", "*");
$validator->print_script();
require ("comun/footer.php"); ?>