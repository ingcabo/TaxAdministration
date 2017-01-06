<?
require ("comun/ini.php");
// Creando el objeto solicitud de pago

$oCheque = new cheque;
$accion = $_REQUEST['accion'];
$hoy = date('Y-m-d');	
#ACCION DE GUARDAR EL CHEQUE#
if($accion == 'Guardar'){
	$oCheque->addLote($conn, $_REQUEST['banco'], $_REQUEST['nro_cuenta'],$_REQUEST['cheque_desde'],$_REQUEST['cheque_hasta'], $_REQUEST['id_proveedor'],guardafecha($_REQUEST['fecha']),$escEnEje,$_REQUEST['observacion']);
}
	
require ("comun/header.php");

$msj =  $oCheque->msg;
?>

<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<br />
<script type="text/javascript">var mygridfac,i=0</script>
<span class="titulo_maestro">Anulaci&oacute;n de Cheques por Lotes</span>
<form name="form1" method="post">
	<table style="text-align: left; width: 100%;" border="0" cellpadding="1" cellspacing="1">
		<tbody>
			<tr>
			<td>
				Fecha: 
			</td>
			<td>
				<input style="width:120px"  type="text" name="fecha" id="fecha" value="<?=muestrafecha($objeto->fecha)?>"/>
				<a href="#" id="boton_fecha" onclick="return false;">
					<img border="0" alt="fecha" src="images/calendarA.png" width="20" height="20" />
				</a>  
			<script type="text/javascript">
				new Zapatec.Calendar.setup({
					firstDay          : 1,
					weekNumbers       : true,
					showOthers        : false,
					showsTime         : false,
					timeFormat        : "24",
					step              : 2,
					range             : [1900.01, 2999.12],
					electric          : false,
					singleClick       : true,
					inputField        : "fecha",
					button            : "boton_fecha",
					ifFormat          : "%d/%m/%Y",
					daFormat          : "%Y/%m/%d",
					align             : "Br"
				 });
			</script>
			</td>
			</tr>
			<tr>
				<td width="130">Banco</td>
				<td width="595">
					
					<?	$bn = new banco;
						$oBancos = $bn->get_all($conn);
						$div = "'divnrocuenta'";
						echo helpers::superComboObj($oBancos, $objeto->id_banco, 'banco', 'banco','width:150px',"traeCuentasBancarias(this.value,$div,'','-1')",'id','descripcion', false, 20,$disabled);
						
					?>
					<? if(!empty($objeto->nrodoc)){ 
							echo "<script type='text/javascript'>traeCuentasBancarias($objeto->id_banco, $div, $objeto->id_nro_cuenta,'$disabled');</script>";
					 } ?>				
				</td>
			</tr>
			<tr>
				<td>Nro. de Cuenta</td>
				<td>
					<table width="100%" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td width="28%">
							<div id="divnrocuenta" style="width:155px">
								<select name="nro_cuenta" id="nro_cuenta" style="width:150px">
									<option value="0">Seleccione</option>
								</select>
							</div>						</td>
						<td width="72%">
							<div id="cargando_cuentas" style="display:none; font-size:11px"><img alt="Cargando" src="images/loading2.gif" />&nbsp;Cargando N&uacute;meros de Cuentas del Banco</div></td>
					</tr>
					</table>				</td>
			</tr>
			<tr>
				<td>Nro. Cheque	Desde</td>
				<td>
					<input type="text" name="cheque_desde" id="cheque_desde" value="<?=$objeto->nro_cheque?>" <?=$readonly?> />
					<span class="errormsg" id="error_nrocheque">*</span>
					<?=$validator->show("error_nrocheque")?>
				</td>
			</tr>
			<tr>
				<td>Nro. Cheque	Hasta</td>
				<td>
					<input type="text" name="cheque_hasta" id="cheque_hasta" value="<?=$objeto->nro_cheque?>" <?=$readonly?> onblur="validadornro($('cheque_desde').value,this.value)" />
					<span class="errormsg" id="error_nrocheque">*</span>
					<?=$validator->show("error_nrocheque")?>
				</td>
			</tr>				
			<tr>
				<td> Motivo de Anulacion</td>
			</tr>
			<tr>
				<td colspan="2">
					<textarea name="observacion" id="observacion" cols="70"  <?=$readonlyObser?> rows="5"><?=$objeto->observacion?></textarea>
					<span class="errormsg" id="error_observacion">*</span>
					<?=$validator->show("error_observacion")?>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="right">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2" align="right">
				<input type="hidden" name="accion" value="<?='Guardar'?>" />
				<input type="button" name="boton" onclick="Guardar();" value="<?='Guardar'?>" /></td>

			</tr>
		</tbody>
	</table>
</form>
<br />


<div style="height:40px;padding-top:10px;">
<p id="cargando" style="display:none;margin-top:0px;">
  <img alt="Cargando" src="images/loading.gif" /> Cargando...
</p>
</div>
<?
	$validator->create_message("error_observacion", "observacion", "*");
	$validator->create_message("error_nrocheque", "nrocheque", "*");
	$validator->create_message("error_nombenef", "nomBenef", "*");
	$validator->create_message("error_concepto", "concepto", "*");
	$validator->print_script();
?>
<script type="text/javascript"> 
//FUNCION QUE TRAE LAS CUENTAS BANCARIAS AL MOMENTO DE SELECCIONAR UN BANCOS//
function traeCuentasBancarias(id_banco, div, id_cuenta,onchange){
	var url = 'updater_selects.php';
	var pars = 'combo=cuentas_bancarias&id_banco=' + id_banco + '&id_cuenta=' + id_cuenta + '&onchange=' + onchange +'&ms='+new Date().getTime();
	var updater = new Ajax.Updater(div, 
		url,
		{
			method: 'get',
			parameters: pars,
			asynchronous:true, 
			evalScripts:true,
			onLoading:function(request){Element.show('cargando_cuentas')}, 
			onComplete:function(request){Element.hide('cargando_cuentas')}
		}); 
}

function Guardar(){
var text ;
	if($("cheque_desde").value=="" || $("cheque_hasta").value=="" || $("observacion").value==""|| $("nro_cuenta").value==0){
		alert("Hay campos vacios");
	}else{
	var url = 'json.php';
	var pars = 'op=revisa_lote&banco=' + $("banco").value +'&cuenta='+ $("nro_cuenta").value +'&desde=' + $("cheque_desde").value + '&hasta=' + $("cheque_hasta").value +'&ms='+new Date().getTime();
	var myAjax = new Ajax.Request(
		url, 
		{
			method: 'get', 
			parameters: pars,
			onLoading:function(peticion){Element.show('cargando')},			
			onComplete: function(peticion){
				Element.hide('cargando');
				var jsonData = eval('(' + peticion.responseText + ')');
				if (jsonData == "") { 
					document.form1.submit();
				}
				else{
				text = 'El lote posee los siguientes cheques ya existentes en el sistema\n ';
				for(var i=0 ; i<jsonData.length;i++){
				text+= 'Cheque No ' + jsonData[i] + ', ';
				}
				text+= '\n No se han realizado cambios';
				alert(text);
				}
			}
		});
	}
}	

function comboNroCuentas(){
	busca($F('busca_bancos'),
		  $F('busca_nro_cuenta'), 
		  $F('busca_proveedores'), 
		  $F('busca_fecha_desde'), 
		  $F('busca_fecha_hasta'),
		  $F('busca_nrodoc'), 1);  
}

function selDocumento(id, nombre){

	$('nombrepro').value = nombre;
	$('id_proveedor').value = id;
	//$('nomBenef').value = nombre;
	Dialog.okCallback();

}
function validadornro(desde,hasta){

	if (desde>hasta){
		alert("El Numero de Cheque de inicial es mayor que el final");
		$("cheque_desde").value="";
		$("cheque_hasta").value=""
		return false;
	}else{
		return true;
	}
}
</script>


<? require ("comun/footer.php");?>