<?
include('adodb/adodb-exceptions.inc.php');
require ("comun/ini.php");
$pagina = $_REQUEST['pagina'];
$num = 20;
if (!$pagina) {
    $inicio = 0;
    $pagina=1;
}
else {
    $inicio = ($pagina - 1) * $num;
}

$otrabajador = new trabajador;
$accion = $_REQUEST['accion'];
$status=$_REQUEST['status'];
if($accion == 'Guardar' and !empty($_REQUEST['codigo'])){
	$msj =$otrabajador->add($conn, 
				$_REQUEST['codigo'], 
				$_REQUEST['nombre'], 
				$_REQUEST['apellido'], 
				$_REQUEST['nacionalidad'], 
				$_REQUEST['cedula'], 
				guardafecha($_REQUEST['fn']), 
				$_REQUEST['genero'], 
				$_REQUEST['telefono'], 
				$_REQUEST['direccion'], 
				guardafecha($_REQUEST['fi']), 
				guardafecha($_REQUEST['fe']),
				$_REQUEST['estatus'], 
				$_REQUEST['ncuenta'], 
				$_REQUEST['tipopago'], 
				$_REQUEST['cargo'],
				$_REQUEST['funcion'],				
				$_REQUEST['banco'], 
				$_REQUEST['departamento'],
				$_REQUEST['familiar'],
				$_REQUEST['tipocta'],
				guardafloat($_REQUEST['sueldo']),
				$_REQUEST['vacante'],
				$_REQUEST['territorios'],
				$_REQUEST['contratado']);
				$otrabajador->guardaContrato($conn, $_REQUEST['Contrato'], $_REQUEST['int_cod']);
}elseif($accion == 'Actualizar' and !empty($_REQUEST['codigo'])){
	$msj =$otrabajador->set($conn, $_REQUEST['int_cod'], $_REQUEST['codigo'], $_REQUEST['nombre'], $_REQUEST['apellido'], $_REQUEST['nacionalidad'], $_REQUEST['cedula'], guardafecha($_REQUEST['fn']), $_REQUEST['genero'], $_REQUEST['telefono'], $_REQUEST['direccion'], guardafecha($_REQUEST['fi']), guardafecha($_REQUEST['fe']), $_REQUEST['estatus'], $_REQUEST['ncuenta'], $_REQUEST['tipopago'], $_REQUEST['cargo'], $_REQUEST['funcion'], $_REQUEST['banco'], $_REQUEST['departamento'],$_REQUEST['familiar'],$_REQUEST['tipocta'],guardafloat($_REQUEST['sueldo']), $_REQUEST['vacante'],$_REQUEST['territorios'],
							$_REQUEST['crearVacante'], $_REQUEST['contratado'], $_REQUEST['crearVacanteAux'], $_REQUEST['Contrato'], $_REQUEST['departamentoAux'], $_REQUEST['sueldoAux']);
		$otrabajador->guardaContrato($conn, $_REQUEST['Contrato'], $_REQUEST['int_cod']);
}elseif($accion == 'del'){
	$msj =$otrabajador->del($conn, $_REQUEST['int_cod']);
}

$ctrabajador=$otrabajador->get_all($conn,$_SESSION['EmpresaL'],'B.dep_ord,A.int_cod', $_POST['TipoB'], $_POST['textAux'],$_POST['TipoBE'],$num,$inicio);
$total = $otrabajador->total_registro_busqueda($conn, $_SESSION['EmpresaL'], $_POST['TipoB'], $_POST['textAux'],$_POST['TipoBE']);
require ("comun/header.php");

if(!empty($msj)){ ?><div id="msj" ><?=$msj?></div><? echo "<br>"; } ?>
<br />
<script>var mygrid,i=0</script>
<span class="titulo_maestro">Ficha de Ingreso (Maestro de Trabajadores)</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />
<form name="formAux" method="post">
<table align="center" border="0" style="margin-left:10px" width="800px">
	<tr  >
		<td width="200px"></td>
		<td align="center">
			<table align="center" border="0" style="margin-left:10px" width="400px">
				<tr  >
					<td>Buscar por:</td>
					<td>
						<select name="TipoB" id="TipoB">
							<option value="0" <?=$_POST['TipoB']==0 ? "selected" : ""?>>Cedula</option>
							<option value="1" <?=$_POST['TipoB']==1 ? "selected" : ""?>>Nombre</option>
							<option value="2" <?=$_POST['TipoB']==2 ? "selected" : ""?>>Apellido</option>
							<option value="3" <?=$_POST['TipoB']==3 ? "selected" : ""?>>Departamento</option>
						</select>
					</td>
					<td><input type="text" name="textAux" value="<?=$_POST['textAux']?>" id="textAux"></td>
					<td><input type="submit" value="Buscar"></td>
				</tr>
				<tr  >
					<td>Estatus:</td>
					<td colspan="3">
						<select name="TipoBE" onChange="document.formAux.submit();" id="TipoBE">
							<OPTION <?=$_POST['TipoBE']==0 ? "selected" : "" ?> value='0'>Activo
							<OPTION <?=$_POST['TipoBE']=='1' ? "selected" : "" ?> value='1'>Vacaciones
							<OPTION <?=$_POST['TipoBE']=='2' ? "selected" : "" ?> value='2'>Reposo
							<OPTION <?=$_POST['TipoBE']=='3' ? "selected" : "" ?> value='3'>Por Egresar
							<OPTION <?=$_POST['TipoBE']=='4' ? "selected" : "" ?> value='4'>Egresado
							<OPTION <?=$_POST['TipoBE']=='5' ? "selected" : "" ?> value='5'>Inactivo
						</select>
					</td>
				</tr>
			</table>
		</td>
		<td></td>
	</tr>
</table>
</form>

<div id="busqueda">
<? if(is_array($ctrabajador)){ ?>
<table class="sortable" style="width:700px" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>Nombre</td>
<td>Apellido</td>
<td>Departamento</td>
<td>Cargo/Funci&oacute;n</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
foreach($ctrabajador as $trabajador) { 
?> 
<tr class="filas"> 
	<td align="center"><?=$trabajador->tra_nom?></td>
	<td align="center"><?=$trabajador->tra_ape?></td>
	<td align="center"><?=$trabajador->dep_nom?></td>
	<?
	if($trabajador->tra_tipo){?>
	<td align="center"><?=$trabajador->fun_nom?></td>
	<? }
	else{?>
	<td align="center"><?=$trabajador->car_nom?></td>
	<? }
	?>
	<td align="center"><a href="?accion=del&int_cod=<?=$trabajador->int_cod?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
	<td align="center"><a href="#" onclick="updater('<?=$trabajador->int_cod?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
</tr>
	<?
			}
			
			$total_paginas = ceil($total / $num);
			?>
			<tr class="filas">
				<td colspan="7" align="center">
				<? 
				for ($j=1;$j<=$total_paginas;$j++)
				{
					if ($j==1)
					{
				?>
						<span class="actual"><?=$j?></span>
					<?
					}
					else
					{
					?>
						<a href="" onclick="busca( $('TipoB').options[$('TipoB').selectedIndex].value, $('textAux').value, $('TipoBE').options[$('TipoBE').selectedIndex].value, '<?=$j?>', '<?=$num?>'); return false;"><?=(($j==1) ? '':' - ').$j?></a>
					<?
					}
				}
				?>
				</td>
			</tr>
			<tr class="filas">
				<td colspan="7" align="center"> Pagina <strong><?=$pagina?></strong> de <strong><?=$total_paginas?></strong></td>
			</tr>
		</table>
		<?
		}else {
		echo "No hay registros en la bd";
} ?>
</div>
<br />
<div style="height:40px;padding-top:10px;">
<p id="cargando" style="display:none;margin-top:0px;">
  <img alt="Cargando" src="images/loading.gif" /> Cargando...
</p>
</div>

<?
$validator->create_message("error_codigo", "codigo", "No puede estar vacio");
$validator->create_message("error_nombre", "nombre", "No puede estar vacio");
$validator->create_message("error_apellido", "apellido", "No puede estar vacio");
$validator->create_message("error_cedula", "cedula", "No puede estar vacio",14);
$validator->create_message("error_sueldo", "sueldo", "No puede estar vacio",15);
$validator->print_script();
require ("comun/footer.php"); ?>
<script>
function Agregar(){
	mygrid.addRow(i,"0,,,,0",i);
	i++;
}
function Eliminar(){
	mygrid.deleteRow(mygrid.getSelectedId());
}
function Guardar(estatusAntesEdit, cargoAntesEdit, departamentoAntesEdit, sueldoAntesEdit){
var JsonAux,FamiliarAux=new Array;
	cambio();
	
	mygrid.clearSelection()
	for(j=0;j<i;j++){
		if(!isNaN(mygrid.getRowId(j))){
			FamiliarAux[j] = new Array;
			FamiliarAux[j][0]= mygrid.cells(mygrid.getRowId(j),0).getValue();
			FamiliarAux[j][1]= mygrid.cells(mygrid.getRowId(j),1).getValue();
			FamiliarAux[j][2]= mygrid.cells(mygrid.getRowId(j),2).getValue();
			FamiliarAux[j][3]= mygrid.cells(mygrid.getRowId(j),3).getValue();
			FamiliarAux[j][4]= mygrid.cells(mygrid.getRowId(j),4).getValue();
		}
	}
	JsonAux={"familiar":FamiliarAux};
	$("familiar").value=JsonAux.toJSONString(); 
	$("fe").disabled=false; 
	var estatusDespuesEdit=$("estatus").value;
	var cargoDespuesEdit=$("cargo").value;
	if($("vacante").checked){
		var url = 'json.php';
		var pars =  'op=disponibilidad_vacantes&car_cod=' + cargoDespuesEdit;
		var Request = new Ajax.Request(
			url,
			{
				method: 'get',
				parameters: pars,
//				asynchronous:false, 
				onComplete:function(request){
						if(request.responseText > 0 || estatusDespuesEdit==5)
							document.form1.submit();
						else
							alert('Ha exedido la cantidad de trabajadores permitido para este cargo');
				}
			}
		); 
	}else{
		if(estatusDespuesEdit==4 && estatusAntesEdit!=estatusDespuesEdit && !$("contratado").checked){
			$("crearVacante").value = confirm('Desea crear la vacante?');
			validate();
		}
		else if(cargoAntesEdit!= -1  && cargoAntesEdit!=cargoDespuesEdit && !$("contratado").checked){
			var url = 'json.php';
			var pars =  'op=disponibilidad_vacantes&car_cod=' + cargoDespuesEdit;
			var Request = new Ajax.Request(
				url,
				{
					method: 'get',
					parameters: pars,
//					asynchronous:false, 
					onComplete:function(request){
							if(request.responseText > 0){
								$("crearVacanteAux").value = cargoAntesEdit;
								$("departamentoAux").value = departamentoAntesEdit;
								$("sueldoAux").value = muestraFloat(sueldoAntesEdit);
								$("crearVacante").value = confirm('Desea crear la vacante?');
								validate();
								}
							else
								alert('Ha exedido la cantidad de trabajadores permitido para este cargo');
					}
				}
			);	
		}
		else if(cargoAntesEdit == -1&& cargoAntesEdit!=cargoDespuesEdit && !$("contratado").checked){
			var url = 'json.php';
			var pars =  'op=disponibilidad_vacantes&car_cod=' + cargoDespuesEdit;
			var Request = new Ajax.Request(
				url,
				{
					method: 'get',
					parameters: pars,
//					asynchronous:false, 
					onComplete:function(request){
							if(request.responseText > 0){
								validate();
								}
							else
								alert('Ha exedido la cantidad de trabajadores permitido para este cargo');
					}
				}
			);	
		}
		else{
			validate();
		}
	}
} 
function esDigito(sChr){  
var sCod = sChr.charCodeAt(0);  
    return ((sCod > 47) && (sCod < 58));  
}  
function valSep(oTxt){  
    var bOk = false;  
    var sep1 = oTxt.charAt(2);  
    var sep2 = oTxt.charAt(5);  
    bOk = bOk || ((sep1 == "-") && (sep2 == "-"));  
    bOk = bOk || ((sep1 == "/") && (sep2 == "/"));  
    return bOk;  
}  
function finMes(oTxt){  
var nMes = parseInt(oTxt.substr(3, 2), 10);  
var nAno = parseInt(oTxt.substr(6), 10);  
var nRes = 0;  
	switch (nMes){  
		case 1: nRes = 31; break;  
		case 2: nRes = 28; break;  
		case 3: nRes = 31; break;  
		case 4: nRes = 30; break;  
		case 5: nRes = 31; break;  
		case 6: nRes = 30; break;  
		case 7: nRes = 31; break;  
		case 8: nRes = 31; break;  
		case 9: nRes = 30; break;  
		case 10: nRes = 31; break;  
		case 11: nRes = 30; break;  
		case 12: nRes = 31; break;  
	}  
    return nRes + (((nMes == 2) && (nAno % 4) == 0)? 1: 0);  
}  
function valDia(oTxt){  
var bOk = false;  
var nDia = parseInt(oTxt.substr(0, 2), 10);  
	bOk = bOk || ((nDia >= 1) && (nDia <= finMes(oTxt)));  
    return bOk;  
}  
function valMes(oTxt){  
var bOk = false;  
var nMes = parseInt(oTxt.substr(3, 2), 10);  
    bOk = bOk || ((nMes >= 1) && (nMes <= 12));  
    return bOk;  
}  
function valAno(oTxt){  
var bOk = true;  
var nAno = oTxt.substr(6);  
    bOk = bOk && ((nAno.length == 2) || (nAno.length == 4));  
	if (bOk){  
    	for (var i = 0; i < nAno.length; i++){  
      		bOk = bOk && esDigito(nAno.charAt(i));  
    	}  
    }  
    return bOk;  
}  
function validarFecha(oTxt){  
var bOk = true;  
	if (oTxt != ""){  
    	bOk = bOk && (valAno(oTxt));  
	    bOk = bOk && (valMes(oTxt));  
     	bOk = bOk && (valDia(oTxt));  
     	bOk = bOk && (valSep(oTxt));  
		return bOk
    }else{
		return false;
	}  
}  
function ActivarFE(Indice)
{
	if(Indice==3){
		$('fe').disabled=false;
		Element.show('boton_fecha_egreso');
	}else{
		if(Indice!=4){
			$('fe').value="";
		}
		$('fe').disabled=true;
		Element.hide('boton_fecha_egreso');
	}
}
function TraeSueldo(Cargo,Boton,Cargando,Opcion){
var Bandera=false;
	if(Boton=='Actualizar'){
		if(!Cargando){
			if(confirm("Desea Actualizar el Sueldo?")){
				Bandera=true;
			}
		}
	}else{
		Bandera=true;
	}
	if(Bandera){
		JsonAux={"Cargo":parseInt(Cargo),"Forma":9,"Accion":Opcion};
		var url = 'OtrosCalculos.php';
		var pars = 'JsonEnv=' + JsonAux.toJSONString();
		var Request = new Ajax.Request(
			url,
			{
				method: 'post',
				parameters: pars,
		//			asynchronous:false, 
				onComplete:function(request){
					if(request.responseText){
						if(Opcion==0)
							$('sueldo').value=request.responseText;
						else
							$('sueldox').value=request.responseText;
					}
				}
			}
		); 
	}
}
function ComboDepartamento(Division){
var JsonAux;
	$('departamento').length=0;
	JsonAux={"Division":parseInt(Division),"Forma":3};
	var url = 'CargarCombo.php';
	var pars = 'JsonEnv=' + JsonAux.toJSONString();
	var Request = new Ajax.Request(
		url,
		{
			method: 'post',
			parameters: pars,
//			asynchronous:false, 
			onComplete:function(request){
				var JsonRec = eval( '(' + request.responseText + ')');
				if(JsonRec){
					for(var i=0;i<JsonRec.length;i++){
						$('departamento').options[i]= new Option(JsonRec[i]['N'],JsonRec[i]['CI']);
					}
				}
			}
		}
	); 
} 
	function traeMunicipios(ide, idm){
		var url = 'updater_selects.php';
		var pars = 'combo=municipios&ide=' + ide +'&idm=' + idm + '&ms='+new Date().getTime();
		var updater = new Ajax.Updater('divcombomunicipios', 
			url,
			{
				method: 'get',
				parameters: pars,
				asynchronous:true, 
				evalScripts:true,
				onLoading:function(request){}, 
				onComplete:function(request){}
			});
		if($('parroquias')){ $('parroquias').length=1; }; 
		if($('territorios')){ $('territorios').length=1; }; 
	}
	
	function traeParroquias(idm, idp){
		var url = 'updater_selects.php';
		var pars = 'combo=parroquias&idm=' + idm +'&idp=' + idp + '&ms='+new Date().getTime();
			
		var updater = new Ajax.Updater('divcomboparroquia', 
			url,
			{
				method: 'get',
				parameters: pars,
				asynchronous:true, 
				evalScripts:true,
				onLoading:function(request){}, 
				onComplete:function(request){}
			});
		if($('territorios')){ $('territorios').length=1; }; 
	}
	function traeTerritorios(idp, idt){
		alert();
		var url = 'updater_selects.php';
		var pars = 'combo=territorios&idp=' + idp +'&idt=' + idt + '&ms='+new Date().getTime();
			
		var updater = new Ajax.Updater('divcomboterritorio', 
			url,
			{
				method: 'get',
				parameters: pars,
				asynchronous:true, 
				evalScripts:true,
				onLoading:function(request){}, 
				onComplete:function(request){}
			});
	} /*
	function crearVacante(vacante){
		if(vacante){
			if(confirm("Desea crear el cargo vacante para este trabjador")){
				
			}
		}
	} */
	function oculta(){
	if(document.getElementById("contratado").checked==true){
		$("cargox").style.display = "none";
		$("cargos").style.display = "none";
		$("salario").style.display = "none";
		$("sueldo").style.display = "none";
		$("imput").style.display = "none";
		$("funcionx").style.display = "inline";
		$("funcions").style.display = "inline";
		$("salariox").style.display = "inline";
		$("sueldox").style.display = "inline";
		$("imputx").style.display = "inline";
	}
	else{
	$("cargox").style.display = "inline";
	$("cargos").style.display = "inline";
	$("salario").style.display = "inline";
	$("sueldo").style.display = "inline";
	$("imput").style.display = "inline";
	$("funcionx").style.display = "none";
	$("funcions").style.display = "none";
	$("salariox").style.display = "none";
	$("sueldox").style.display = "none";
	$("imputx").style.display = "none";
	}
	}
	function cambio(){
	if(document.getElementById("contratado").checked==true){
		$("sueldo").value = $("sueldox").value;
		}
	}

	function busca(busqueda, textAux, estatus, pagina,num)
	{
		var url = 'updater_busca_trabajador.php';
		var pars = 'busqueda=' + busqueda +'&textAux='+textAux + '&estatus='+estatus +'&pagina='+pagina+'&num='+num;
		var updater = new Ajax.Updater('busqueda', 
			url,
			{
				method: 'get',
				parameters: pars,
				asynchronous:true, 
				evalScripts:true,
				/*onLoading:function(request){Element.show('cargando')}, 
				onComplete:function(request){Element.hide('cargando')}*/
			}); 
	}	
</script>