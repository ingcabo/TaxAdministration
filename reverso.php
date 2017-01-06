<?
require ("comun/ini.php");
require ("comun/header.php");

$fecha = $_POST['fecha'];
$anio = $_POST['anio'];
$mes = $_POST['mes'];
$accion = $_POST['accion'];

if ($accion == 'reversar')
{
	$sql = "SELECT contabilidad.reversar($anio::int2, $mes::int2, $escEnEje::int8, $usuario->id::int8)";
	$rs = $conn->Execute($sql);
	
	if ($rs === false)
		$msj = ERROR;
	else
		$msj = "Se ha Reversado el mes con &eacute;xito";
}

$q = "SELECT MAX(ano) AS anio FROM contabilidad.consolidado WHERE status='C'";
$rs = $conn->Execute($q);
$hoy = date('d/m/Y');
if (!empty($rs->fields['anio']))
{
	$anio = $rs->fields['anio'];

	$q = "SELECT MAX(mes) AS mes FROM contabilidad.consolidado WHERE ano = $anio AND status = 'C'";
	$rs = $conn->Execute($q);
	$mes = sprintf("%02d", $rs->fields['mes']);
}
else
	$cerrar = true;

if ($cerrar && empty($accion))
	$msj = "No existe ning&uacute;n mes cerrado";

?>
<div id="msj" <?=($cerrar || !empty($accion)) ? '':'style="display:none"'?>><?=$msj?></div><br /><br />
<span class="titulo_maestro">Reverso</span>
<div id="formulario" style="text-align:center">
	<p style="font-weight:bold; font-size:larger; color:#C82619">Devuelve el saldo de las cuentas contables a su estado anterior</p><br />
	<form name="form1" id="form1" method="post">
		<input type="hidden" name="accion" id="accion" />
		<table align="center">
			<tr>
				<td width="40px">Fecha:</td>
				<td><input type="text" name="fecha" id="fecha" value=" <?=$hoy?>" readonly <?=($cerrar) ? 'disabled':''?> /></td>
			</tr>
			<tr>
				<td>A&ntilde;o:</td>
				<td><input type="text" name="anio" id="anio" value=" <?=($cerrar) ?'':$anio?>" readonly <?=($cerrar) ? 'disabled':''?> /></td>
			</tr>
			<tr>
				<td>Mes:</td>
				<td><input type="text" name="mes" id="mes" value=" <?=($cerrar) ?'':$mes?>" readonly <?=($cerrar) ? 'disabled':''?> /></td>
			</tr>
			<tr>
				<td>Usuario:</td>
				<td><input type="text" name="usuario" id="usuario" value="<?=$usuario->nombre." ".$usuario->apellido?>" readonly <?=($cerrar) ? 'disabled':''?> /></td>
			</tr>
			<tr>
				<td colspan="2" align="right"><br /><input type="button" name="accion" value="Reversar" onclick="Reversar()" <?=($cerrar) ? 'disabled':''?> /></td>
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
	
	function Reversar()
	{
		$('accion').value = 'reversar';
		$('form1').submit();
		/*var url = 'json.php';
		var pars = 'op=reversar&anio='+$('anio').value+'&mes='+$('mes').value+'&fecha='+$('fecha').value+'&ms=' + new Date().getTime();
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