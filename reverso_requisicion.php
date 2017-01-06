<?
include('adodb/adodb-exceptions.inc.php');
require ("comun/ini.php");
require ("comun/header.php");


$id_requisicion = $_REQUEST['requisicion'];
$estActual = $_REQUEST['edoAct'];
$idReqGbl = $_REQUEST['gblrequisicion'];
$proxEstado = $_REQUEST['proxEstado'];
$accion = $_POST['accion'];

$cReverso = new requisiciones;

if ($accion == 'reversar')
{
	$msj = $cReverso->reversoRequisicion($conn, $estActual, $proxEstado, $id_requisicion, $idReqGbl);
}


?>
<div id="msj" <?=($cerrar || !empty($accion)) ? '':'style="display:none"'?>><?=$msj?></div><br /><br />
<span class="titulo_maestro">Reverso de Requisicion</span>
<div id="formulario" style="text-align:center">
	<p style="font-weight:bold; font-size:larger; color:#C82619">Devuelve una requisicion a un estado anterior</p><br />
	<form name="form1" id="form1" method="post">
		<input type="hidden" name="accion" id="accion" />
		<table align="center">
			<tr>
				<td width="120px">N&ordm; Requisicion:</td>
				<td width="50px"><input type="text" name="requisicion" id="requisicion" onblur="validaFormato1(this.value)" /></td>
			</tr>
			<tr>
				<td width="120px">N&ordm; Requisicion Global:</td>
				<td><input type="text" name="gblrequisicion" id="gblrequisicion" onblur="validaFormato2(this.value)" /></td>
			</tr>
			<tr>
				<td width="120px">Estado Actual: </td>
				<td><input type="text" name="edoActual" id="edoActual" disabled="disabled" />
					<input type="hidden" name="edoAct" id="edoAct" />
				</td>
			</tr>
			<tr>
				<td>Estado a Reversar:</td>
				<td><select name="proxEstado" id="proxEstado" disabled="disabled" style="width:110px">
						<option id="0" selected="selected">Seleccione...</option>
					</select>
				</td>	
			</tr>
			<tr>
				<td>Usuario:</td>
				<td><input type="text" name="usuario" id="usuario" value="<?=$usuario->nombre." ".$usuario->apellido?>" readonly <?=($cerrar) ? 'disabled':''?> /></td>
			</tr>
			<tr>
				<td colspan="2" align="right"><br /><input type="button" name="accion" value="Reversar" onClick="Reversar()" <?=($cerrar) ? 'disabled':''?> /></td>
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

var req01 = [{desc:'Requisicion',valor:'01'}];
var req02 = [{desc:'Seleccione',valor:'00'},{desc:'Requisicion',valor:'01'},{desc:'Requisicion Global',valor:'05'}];
var req03 = [{desc:'Seleccione',valor:'00'},{desc:'Requisicion',valor:'01'},{desc:'Requisicion Global',valor:'05'},{desc:'Analisis Cotizacion',valor:'06'}];
var none = [{desc:'No se puede reversar',valor:'00'}];
	
	function Buscar(requi, requiGbl)
	{
		
		if(requi!='' && requiGbl!=''){
			
			return false;
		}else{
			var url = 'json.php';
			var pars = 'op=buscaRequisicio&nroReq='+requi+'&nroReqGbl='+requiGbl+'&ms='+new Date().getTime();
			var Request = new Ajax.Request
			(
				url,
				{
					method: 'get',
					parameters: pars,
					onLoading:function(request){Element.show("cargando");}, 
					onComplete:function(request)
					{
						var jsonData = eval('(' + request.responseText + ')');
						if (jsonData == undefined) { 
							$('msj').innerHTML = request.responseText;
							Element.hide("cargando");
							Element.show('msj');
							return;
					   } else {
							$('edoActual').value = jsonData.nom_status;
							$('edoAct').value = jsonData.status;
							$('gblrequisicion').value = jsonData.nroreqgbl;
							cargaCombo(jsonData.status);
							Element.hide("cargando");
					   }
							
					}
				}
			);
		}
	}
	
	function cargaCombo(valor){
		if (valor == '02' || valor == '04'){
			for(var i=0; i<req01.length;i++){
				$('proxEstado').options[i] = new Option(req01[i].desc,req01[i].valor);
			}
		}else if (valor == '06'){
			for(var i=0; i<req02.length;i++){
				$('proxEstado').options[i] = new Option(req02[i].desc,req02[i].valor);
			}
		}else if (valor == '07'){
			for(var i=0; i<req03.length;i++){
				$('proxEstado').options[i] = new Option(req03[i].desc,req03[i].valor);
			}
		}else{
			for(var i=0; i<none.length;i++){
				$('proxEstado').options[i] = new Option(none[i].desc,none[i].valor);
			}
		}
		$('proxEstado').disabled=false;	
	}
	
	function Reversar()
	{
		$('accion').value = 'reversar';
		confirm('Realmente desea reversar la requisicion al estado: ' + $('proxEstado').options[document.getElementById('proxEstado').selectedIndex].text)
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
	
	function validaFormato1(texto)
	{	
		var regEx = /(^[0-9]{2}[-]{1}[0-9]{4}$)/;
		if(texto == ''){
			return false;
		} else if(regEx.exec(texto) != null){
			Buscar(texto,$('gblrequisicion').value);
			return true;
		} else {
			alert('El formato no coincide con el de la requisicion');
			$('requisicion').value = '';
			$('requisicion').focus()
			return false;
		}
	}
	
	function validaFormato2(texto)
	{
		var regEx = /(^[0-9]{4}[-]{1}[0-9]{4}$)/;
		if(texto == ''){
			return false;
		} else if(regEx.exec(texto) != null){
			Buscar($('requisicion').value,texto);
			return true;
		} else {
			alert('El formato no coincide con el de la requisicion global');
			$('gblrequisicion').value = '';
			$('gblrequisicion').focus() 
			return false;
		}
	}
</script>
	
</script>

<? require ("comun/footer.php"); ?>