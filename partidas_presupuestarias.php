<?
include('adodb/adodb-exceptions.inc.php');
require ("comun/ini.php");

// Creando el objeto partidas_presupuestarias
$oPartidasPresupuestarias = new partidas_presupuestarias;
$accion = $_REQUEST['accion'];
//echo $accion;
if($_REQUEST['check_ing'] == '')
{
	$check_ing = 0;
}
else
{
	$check_ing = $_REQUEST['check_ing'];
}

if($accion == 'Guardar'){

	
	// Revisa si la partida presupuestaria es madre o no, revisa que los ultimos 10 caracteres sean cero
	$cod_partida = $_REQUEST['cod1'].$_REQUEST['cod2'].$_REQUEST['cod3'].$_REQUEST['cod4'].$_REQUEST['cod5'].$_REQUEST['cod6'];
	$madre = (substr($cod_partida,3)=="0000000000" ? 1:'0');
	$msj = $oPartidasPresupuestarias->add($conn, $cod_partida, 
									$_REQUEST['escenarios'], 
									$_REQUEST['descripcion'], 
									$_REQUEST['detalle'], 
									$_REQUEST['gastos_inv'], 
									$_REQUEST['id_contraloria'], 
									escenarios::get_ano($conn, $_REQUEST['escenarios']),
									$madre,
									$check_ing,
									guardafloat($_REQUEST['ingreso']),
									guardafloat($_POST['estAnt']),
									guardafloat($_POST['estAjusAnt']),
									$_POST['baseCalc'],
									guardafloat($_POST['porcMaxi']));
}elseif($accion == 'Actualizar'){
	$cod_partida = $_REQUEST['cod1'].$_REQUEST['cod2'].$_REQUEST['cod3'].$_REQUEST['cod4'].$_REQUEST['cod5'].$_REQUEST['cod6'];
	
	$msj = $oPartidasPresupuestarias->set($conn, $cod_partida, 
									$_REQUEST['id'],
									$_REQUEST['escenarios'], 
									$_REQUEST['descripcion'], 
									$_REQUEST['detalle'], 
									$_REQUEST['gastos_inv'], 
									$_REQUEST['id_contraloria'],
									escenarios::get_ano($conn, $_REQUEST['escenarios']),
									$_REQUEST['madre'],
									$check_ing,
									guardafloat($_REQUEST['ingreso']),
									guardafloat($_POST['estAnt']),
									guardafloat($_POST['estAjusAnt']),
									$_POST['baseCalc'],
									guardaFloat($_POST['porcMaxi']));
}elseif($accion == 'del'){
	$msj = $oPartidasPresupuestarias->del($conn, $_REQUEST['id'], $_REQUEST['id_escenario']);
}

require ("comun/header.php");
if(!empty($msj)){ ?><div id="msj"><?=$msj?></div><? } 
?>
<br />
<span class="titulo_maestro">Maestro de Partidas Presupuestarias</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />
<div>
<fieldset id="buscador">
	<legend>Buscar:</legend>
	<table>
		<tr>
			<td>C&oacute;digo</td>
			<td>Escenario</td>
			<td>Descripci&oacute;n</td>
		</tr>
		<tr>
			<td><input style="width:80px" type="text" name="busca_id" id="busca_id" maxlength="13" /></td>
			<td><?=helpers::combo_ue_cp($conn, 'escenarios','','','id','busca_escenarios','busca_escenarios')?></td>
			<td><input type="text" style="width:300px" name="busca_descripcion" id="busca_descripcion" /></td>
		</tr>
	</table>
</fieldset>
</div>
<br />
<div style="margin-bottom:10px" id="busqueda"></div>

<br />
<div style="height:40px;padding-top:10px;">
<p id="cargando" style="display:none;margin-top:0px;">
  <img alt="Cargando" src="images/loading.gif" /> Cargando...
</p>
</div>
<script type="text/javascript">
var t;
var opn = 0; 
var opn2 = 0;

function busca(id, id_escenario, descripcion, pagina)
{
	var url = 'updater_busca_pp.php';
	var pars = '&id=' + id + '&id_escenario=' + id_escenario + '&descripcion=' + descripcion + '&pagina=' + pagina;
	var updater = new Ajax.Updater('busqueda', 
		url,
		{
			method: 'get',
			parameters: pars,
			asynchronous:true, 
			evalScripts:true,
			onLoading:function(request){Element.show('cargando')}, 
			onComplete:function(request){Element.hide('cargando')}
		}); 
} 

Event.observe('busca_id', "keyup", function () { 
  if ($F('busca_id').length == 13 || $F('busca_id') == '')
	   busca($F('busca_id'), $F('busca_escenarios'), $F('busca_descripcion'), '1'); 
});
Event.observe('busca_escenarios', "change", function () { 
	busca($F('busca_id'), $F('busca_escenarios'), $F('busca_descripcion'), '1'); 
});
Event.observe('busca_descripcion', "keyup", function () {
  clearTimeout(t); 
	t = setTimeout("busca($F('busca_id'), $F('busca_escenarios'), $F('busca_descripcion'), '1')", 1500); 
});

function auto_focus(campo,next,num){
		
		if(campo.value.length == num){
			next.focus();
		}
	}

function cambio(check_ing) 
	{
		if(document.form1.check_ing.checked == true)  
		{
			document.form1.ingreso.disabled = false;	 
		}
		else
		{
			document.form1.ingreso.disabled = true;
		}
	}

// Llama a la funcion que valida que el codigo sea de 13 digitos y revisa el primer digito
// si es 3 (ingreso) activa la casilla del monto
function validar_codigo(el, tam, id)
	{
		
		digitosMin(el, tam);
		if ($(el).value != '')
		{
			cad = String($(el).value);
			if (id!=undefined){
				if (cad.charAt(0) == '3' && id!='oculto'){
					$(id).disabled = false;
					$('gasto1').disabled = true;
					$('gasto2').disabled = true;
					$('gasto2').checked = true;
					if(opn == 0){
						Effect.toggle('estimaIng', 'blind');
						opn = 1;
					}
					if(opn2 == 1){
						Effect.toggle('porcMaxi', 'blind');
						opn2 = 0;
					}
				}else{
					if(id!='oculto'){
						$(id).disabled = true;
						$('gasto1').disabled = false;
						$('gasto2').disabled = false;
						if(opn == 1){
							Effect.toggle('estimaIng', 'blind');
							opn = 0;
						}
						if(cad == '498' && opn2 == 0){
							Effect.toggle('porcMaxi', 'blind');
							opn2 = 1;
						}
						if(cad != '498' && opn2 != 0){
							Effect.toggle('porcMaxi', 'blind');
							opn2 = 0;
						}
					}
				}
			}
			//alert('entro1');
			if(el.id == 'cod6')
				validaFormato(el);
		}
		//alert('entro2');
	}
	
	function habilitar(){
		$('gasto1').disabled = false;
		$('gasto2').disabled = false;
	}
	
	function validaFormato(campo){
	
		var i = 0;
		var j = 0;
		var flag = true;
		if(campo.value.length == 2){
			//alert(document.form1.cod1.value);
			//alert($("cod1").value);
			var codigo = $('cod1').value + $('cod2').value + $('cod3').value + $('cod4').value + $('cod5').value+$('cod6').value;
			if(codigo.length == 13){
			//alert(codigo);
				for(i=3; i<=11; i++){
					j = i+2;
					if(codigo.substr(i,j-i) == creaCadena('0',2)){
						if(codigo.substr(j,13-j) != creaCadena('0',13-j)){
							flag = false;
						}
					}
					i++;	
				}
				if(!flag){
					alert('Debe llevar la secuencia de codificacion de las partidas');
					$('cod2').value = '';
					$('cod3').value = '';
					$('cod4').value = '';
					$('cod5').value = '';
					$('cod6').value = '';
					$('cod2').focus();
				}
			}	
		}
	}
	
	
	function creaCadena(str,cant){
		var cad = '';
		var i = 0;
		for(i=0;i<cant;i++){
			cad = cad + str;
		}
		return cad;
	}
</script>
<?
$validator->create_message("error_cod", "cod1", "*");
$validator->create_message("error_cod", "cod2", "*");
$validator->create_message("error_cod", "cod3", "*");
$validator->create_message("error_cod", "cod4", "*");
$validator->create_message("error_cod", "cod5", "*");
$validator->create_message("error_cod", "cod6", "*");
$validator->create_message("error_esc", "escenarios", "*");
$validator->create_message("error_desc", "descripcion", "*");
//$validator->create_message("error_ing", "ingreso", "*", 16);
$validator->create_message("error_ing", "ingreso", "*");
$validator->print_script();
require ("comun/footer.php");
?>
