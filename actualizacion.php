<?
require ("comun/ini.php");
require ("comun/header.php");

$fecha = $_POST['fecha'];
$anio = $_POST['anio'];
$mes = $_POST['mes'];
$accion = $_POST['accion'];

if ($accion=='actualizar')
{
	$sql = "SELECT contabilidad.actualizar($anio::int2, $mes::int2, $escEnEje::int8)";
	$rs = $conn->Execute($sql);
	
	if ($rs ===false)
		$msj = $conn->ErrorMsg();//ERROR;
	else
		$msj = "Se ha Actualizado con &eacute;xito";
}

$q = "SELECT MAX(ano) AS anio FROM contabilidad.consolidado WHERE status = 'C'";
$rs = $conn->Execute($q);
$anio = $rs->fields['anio'];
if (empty($rs->fields['anio']))
{
	$op = 'MIN(mes)';
	$q = "SELECT MIN(ano) AS anio FROM contabilidad.consolidado WHERE status = 'R'";
	$status = 'R';
	$rs = $conn->Execute($q);
	$anio = $rs->fields['anio'];

	if (empty($rs->fields['anio']))
	{
		$q = "SELECT MIN(ano) AS anio FROM contabilidad.consolidado WHERE status = 'A'";
		$status = 'A';
		$rs = $conn->Execute($q);
	}
	else
	{
		$q = "SELECT MIN(mes) AS mes FROM contabilidad.consolidado WHERE status = 'R' AND ano = ano <= $anio";
		$rs = $conn->Execute($q);
		$mes = $rs->fields['mes'];
		
		$q = "SELECT MIN(ano) AS anio FROM contabilidad.consolidado WHERE status = 'A' AND ano <= $anio AND mes < $mes";
		$rs = $conn->Execute($q);
		if (!empty($rs->fields['anio']))
		{
			$anio = $rs->fields['anio'];
			$status = 'A';
		}
	}
}
else
{
	$status = 'C';
	$op = 'MAX(mes)';
}

if (empty($anio))
	$anio = date('Y');
	
$q = "SELECT ".$op." AS mes FROM contabilidad.consolidado WHERE ano=$anio AND status='$status'";

$rs = $conn->Execute($q);
$mes = empty($rs->fields['mes']) ? 1:$rs->fields['mes'];

if ($status=='C' && $mes==12)
{
	$anio++;
	$mes = 1;
}
else if ($status=='C')
	$mes++;
	
$mes = sprintf("%02d", $mes);
?>
<div id="msj" <?=($accion=='actualizar') ? '':'style="display:none"'?>><?=$msj?></div><br /><br />
<span class="titulo_maestro">Actualizaci&oacute;n de Cuentas</span>
<div id="formulario" style="text-align:center">
	<p style="font-weight:bold; font-size:larger; color:#C82619">Actualiza los mayores de cuentas de acuerdo a los movimientos contables del per&iacute;odo indicado</p><br />
	<form name="form1" id="form1" method="post">
		<input type="hidden" name="accion" id="accion" />
		<table align="center">
			<tr>
				<td width="40px">Fecha:</td>
				<td><input type="text" name="fecha" id="fecha" value=" <?=date('d/m/Y')?>" readonly /></td>
			</tr>
			<tr>
				<td>A&ntilde;o:</td>
				<td><input type="text" name="anio" id="anio" value=" <?=$anio?>" readonly /></td>
			</tr>
			<tr>
				<td>Mes:</td>
				<td><input type="text" name="mes" id="mes" value=" <?=$mes?>" readonly /></td>
			</tr>
			<tr>
				<td colspan="2" align="right"><br /><input type="button" name="accion" value="Actualizar" onclick="Actualizar()" /></td>
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
	
	function Actualizar()
	{
		$('accion').value = 'actualizar';
		$('form1').submit();
		
		/*var url = 'json.php';
		var pars = 'op=actualizar&anio='+$('anio').value+'&mes='+$('mes').value+'&fecha='+$('fecha').value+'&ms=' + new Date().getTime();
		var Request = new Ajax.Request
		(
			url,
			{
				method: 'get',
				parameters: pars,
				onLoading:function(request){Element.show("cargando");}, 
				onComplete:function(request)
				{
					$('msj').innerHTML = request.responseText;
					Element.hide("cargando");
					Element.show('msj');
				}
			}
		);*/
	}
	
</script>

<? require ("comun/footer.php"); ?>