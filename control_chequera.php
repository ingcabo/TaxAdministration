<? require ("comun/ini.php");
// Creando el objeto motivo
$ocontrol_chequera = new control_chequera;
$accion = $_REQUEST['accion'];

#SECCION DE GUARDAR#
if($accion == 'Guardar' and !empty($_REQUEST['nro_chequera'])){
	if($ocontrol_chequera->add($conn, $_REQUEST['nro_chequera'], $_REQUEST['nro_cuenta'], $_REQUEST['fecha'], $_REQUEST['cheque_desde'], $_REQUEST['cheque_hasta'], 
				 $_REQUEST['ultimo_cheque'], $_REQUEST['activa']))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;

#SECCION DE ACTULIZAR#
}elseif($accion == 'Actualizar' and !empty($_REQUEST['nro_chequera'])){
	if($ocontrol_chequera->set($conn, $_REQUEST['id'], $_REQUEST['nro_chequera'], $_REQUEST['nro_cuenta'], $_REQUEST['fecha'], $_REQUEST['cheque_desde'], $_REQUEST['cheque_hasta'], 
				 $_REQUEST['ultimo_cheque']))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;

#SECCION DE ELIMINAR#
}elseif($accion == 'del'){
	if($ocontrol_chequera->del($conn, $_REQUEST['id']))
		$msj = REG_DEL_OK;
	else
		$msj = ERROR;
}

//Seccion paginador
$page_size = 25;
if ($_GET['pg'])
	$start_record=($_GET['pg'] * $page_size) - $page_size;
else
	$start_record=0;

$ccontrol_chequera=$ocontrol_chequera->get_all($conn, $start_record,$page_size);
$pag=new paginator($ocontrol_chequera->total,$page_size, self($_SERVER['SCRIPT_NAME']));
$i=$pag->get_total_pages();
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<br />
<span class="titulo_maestro">Maestro de Control de Chequeras </span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />

<? if(is_array($ccontrol_chequera)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>Nro. de Chequera</td>
<td>Cuenta Bancaria</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($ccontrol_chequera as $cc) { 
?> 
<tr class="filas"> 
<td><?=$cc->nro_chequera?></td>
<td><?=$cc->nro_cuenta->banco->descripcion?> - <?=$cc->nro_cuenta->tipo_cuenta->descripcion?> Nro: <?=$cc->nro_cuenta->nro_cuenta?></td>
<td align="center">
<a href="control_chequera.php?accion=del&id=<?=$cc->id?>" title="Modificar ó Actualizar Registro" ><img src="images/eliminar.gif" width="16" height="10" border="0"></a></td>
<td align="center">
<a href="#" onclick="updater('<?=$cc->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
</tr>
<? $i++;
	}
?>
</table>
<? }else {
		echo "No hay registros en la bd";
} ?>

<br />
<div style="height:40px;padding-top:10px;">
<p id="cargando" style="display:none;margin-top:0px;">
  <img alt="Cargando" src="images/loading.gif" /> Cargando...
</p>
</div>
<script>
	
	function validadornro(desde,hasta){

		if (desde>=hasta){
			alert("El Numero de Cheque de inicial es mayor que el final");
			return false;
		}else{
			return true;
		}
	}
	
	//FUNCION QUE TRAE LAS CUENTAS BANCARIAS AL MOMENTO DE SELECCIONAR UN BANCOS//
function traeCuentasBancarias(id_banco, div, id_cuenta){
	var url = 'updater_selects.php';
	var pars = 'combo=cuentas_bancarias&id_banco=' + id_banco + '&id_cuenta=' + id_cuenta +'&style=width:150px&ms='+new Date().getTime();
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

function traeUltimoCheque(id_chequera,id_cuenta){
	var aux_nan = 0; 
}
function activarChequera(id_chequera,id_cuenta){
	var url = 'json.php';
	var pars = 'op=activar_chequera&id_cuenta=' + id_cuenta + '&id_chequera=' + id_chequera;
	var myAjax = new Ajax.Request(
		url, 
		{
			method: 'get', 
			parameters: pars,
			onComplete: function(peticion){
				var jsonData = eval('(' + peticion.responseText + ')');
				if (jsonData == undefined) { return }
				alert('Proceso Realizado con Exito')
			}
		});
}
	
</script>
<?
$validator->create_message("error_nro_chequera", "nro_chequera", "*");
$validator->create_message("error_nro_cuenta", "nro_cuenta", "*");
$validator->create_message("error_fecha", "fecha", "*");
$validator->create_message("error_cheque_desde", "cheque_desde", "*");
$validator->create_message("error_cheque_hasta", "cheque_hasta", "*");
$validator->create_message("error_ultimo_cheque", "ultimo_cheque", "*");
$validator->print_script();
?>
<? require ("comun/footer.php"); ?>
