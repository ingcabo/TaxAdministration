<?
require ("comun/ini.php");
require ("comun/header.php");

$fecha = $_POST['fecha'];
$anio = $_POST['anio'];
$mes = $_POST['mes'];
$accion = $_POST['accion'];

if ($accion=='cerrar')
{
	$sql = "SELECT contabilidad.cerrar($anio::int2, $mes::int2, $escEnEje::int8, $usuario->id::int8)";
	$rs = $conn->Execute($sql);
	
	if ($rs === false)
		$msj = ERROR;
	else
		$msj = "Se ha Cerrado el mes con &eacute;xito";
}

$q = "SELECT MIN(ano) AS anio FROM contabilidad.consolidado WHERE status='A'";
$rs = $conn->Execute($q);
$hoy = date('d/m/Y');
if (!empty($rs->fields['anio']))
{
	$anio = $rs->fields['anio'];

	$q = "SELECT MIN(mes) AS mes FROM contabilidad.consolidado WHERE ano = $anio AND status = 'A'";
	$rs = $conn->Execute($q);
	$mes = $rs->fields['mes'];

	switch($mes)
	{
		case 2:
			$ultimoDia = 28;
			if(!($mes%4) && (($mes%100) || !($mes%400)))
				$ultimoDia++;
			break;
			
		case 1:
		case 3:
		case 5:
		case 7:
		case 8:
		case 10:
		case 12:
			$ultimoDia = 31;
			break;
			
		default:
			$ultimoDia = 30;
	}
	
	$q = "SELECT DISTINCT fecha FROM contabilidad.consolidado WHERE ano = $anio AND mes = $mes";
	$r = $conn->Execute($q);
	$fecha = strtotime($r->fields['fecha']);
	$finalMes = strtotime($anio.'-'.$mes.'-'.$ultimoDia);
	
	/*if ($fecha < $finalMes)
		$actualizar = 2;*/
}
else
	$actualizar = 1;

if ($actualizar==1 && empty($accion))
	$msj = "Debe Actualizar antes de cerrar el mes";
else if ($actualizar==2 && empty($accion))
	$msj = "No puede cerrar el mes antes de que finalice";
	
$mes = sprintf("%02d", $mes);


?>
<div id="msj" <?=($actualizar || !empty($accion)) ? '':'style="display:none"'?>><?=$msj?></div><br /><br />
<span class="titulo_maestro">Cierre Mensual</span>
<div id="formulario" style="text-align:center">
	<p style="font-weight:bold; font-size:larger; color:#C82619">Actualiza el saldo de las cuentas y cierra presupuesto en el mes indicado</p><br />
	<form name="form1" id="form1" method="post">
		<input type="hidden" name="accion" id="accion" />
		<table align="center">
			<tr>
				<td width="40px">Fecha:</td>
				<td><input type="text" name="fecha" id="fecha" value=" <?=$hoy?>" readonly <?=($actualizar) ? 'disabled':''?> /></td>
			</tr>
			<tr>
				<td>A&ntilde;o:</td>
				<td><input type="text" name="anio" id="anio" value=" <?=($actualizar) ?'':$anio?>" readonly <?=($actualizar) ? 'disabled':''?> /></td>
			</tr>
			<tr>
				<td>Mes:</td>
				<td><input type="text" name="mes" id="mes" value=" <?=($actualizar) ?'':$mes?>" readonly <?=($actualizar) ? 'disabled':''?> /></td>
			</tr>
			<tr>
				<td>Usuario:</td>
				<td><input type="text" name="usuario" id="usuario" value="<?=$usuario->nombre." ".$usuario->apellido?>" readonly <?=($actualizar) ? 'disabled':''?> /></td>
			</tr>
			<tr>
				<td colspan="2" align="right"><br /><input type="button" name="accion" value="Cerrar" onclick="Cerrar()" <?=($actualizar) ? 'disabled':''?> /></td>
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
	
	function Cerrar()
	{
		$('accion').value = 'cerrar';
		$('form1').submit();
		/*var url = 'json.php';
		var pars = 'op=cerrar&anio='+$('anio').value+'&mes='+$('mes').value+'&fecha='+$('fecha').value+'&ms=' + new Date().getTime();
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