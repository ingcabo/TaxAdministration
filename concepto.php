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
$q = "SELECT * FROM rrhh.parametrossistema";
$r = $conn->Execute($q);
if(!$r->EOF){
	$Enlace= $r->fields['enlace_presupuesto']=='1' ? '1' : '0';
}else{
	$Enlace='0';
}

$oconcepto = new concepto;
$accion = $_REQUEST['accion'];
if($accion == 'Guardar' and !empty($_REQUEST['codigo'])){
	$msj =$oconcepto->add($conn, $_REQUEST['codigo'], $_REQUEST['nombre'], $_REQUEST['tipo'], $_REQUEST['estatus'], $_REQUEST['formula'], $_REQUEST['desc'], $_REQUEST['aporte'], $_REQUEST['Presupuesto'], $_REQUEST['id_plan_cuenta'], $_REQUEST['retencion'], $_REQUEST['idCtaAporte']);
}elseif($accion == 'Actualizar' and !empty($_REQUEST['codigo'])){
	$msj =$oconcepto->set($conn, $_REQUEST['int_cod'], $_REQUEST['codigo'], $_REQUEST['nombre'], $_REQUEST['tipo'], $_REQUEST['estatus'], $_REQUEST['formula'], $_REQUEST['desc'], $_REQUEST['aporte'], $_REQUEST['Presupuesto'], $_REQUEST['id_plan_cuenta'], $_REQUEST['retencion'], $_REQUEST['idCtaAporte']);
}elseif($accion == 'del'){
	$msj =$oconcepto->del($conn, $_REQUEST['int_cod']);
}

//$cconcepto=$oconcepto->get_all($conn, 'int_cod', $_POST['TipoB'], $_POST['textAux'], $_POST['TipoBE']);
$cconcepto=$oconcepto->get_all($conn,'int_cod', $_POST['TipoB'], $_POST['textAux'],$_POST['TipoBE'],$num,$inicio);
$total = $oconcepto->total_registro_busqueda($conn, $_POST['TipoB'], $_POST['textAux'],$_POST['TipoBE']);
require ("comun/header.php");
if(!empty($msj)){ ?><div id="msj" ><?=$msj?></div><? echo "<br>"; } ?>
<br />
<script>var mygrid,i=0</script>
<span class="titulo_maestro">Maestro de Conceptos </span>
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
							<option value="0" <?=$_POST['TipoB']==0 ? "selected" : ""?>>C&oacute;digo</option>
							<option value="1" <?=$_POST['TipoB']==1 ? "selected" : ""?>>Nombre</option>
						</select>
					</td>
					<td><input type="text" name="textAux" value="<?=$_POST['textAux']?>" id="textAux"></td>
					<td><input type="submit" value="Buscar"></td>
				</tr>
				<tr>
					<td>Estatus:</td>
					<td colspan="3">
						<select name="TipoBE" onChange="document.formAux.submit();" id="TipoBE">
							<OPTION <?=$_POST['TipoBE']==0 ? "selected" : "" ?> value='0'>Activo</OPTION>
							<OPTION <?=$_POST['TipoBE']=='1' ? "selected" : "" ?> value='1'>Inactivo</OPTION>
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
<? if(is_array($cconcepto)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Nombre</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cconcepto as $concepto) { 
?> 
<tr class="filas"> 
<td><?=$concepto->conc_cod?></td>
<td align="center"><?=$concepto->conc_nom?></td>
<td align="center"><a href="?accion=del&int_cod=<?=$concepto->int_cod?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
<td align="center"><a href="#" onclick="updater('<?=$concepto->int_cod?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
</tr>
	<? $i++;
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
$validator->create_message("error_codigo", "codigo", "Este campo no puede estar vacio");
$validator->create_message("error_desc", "nombre", "Este campo no puede estar vacio");
$validator->print_script();
?>
<script>
var BanderaAuxA,CadenaAux;
function Validar(){
var TipoVar,Codigo,Contador;
var JsonAux,i;

	cadena=document.form1.formula.value;
	try {	
		Texto="";
		for(i=0;i<cadena.length;i++){
			char_actual=cadena.substring(i,i+1);
			CadenaAux=char_actual;
			if(char_actual=="["){
				i=i+1;
				TipoVar=cadena.substring(i,i+4);
//				alert(TipoVar);
				i=i+5;
				char_actual=cadena.substring(i,i+1);
				Codigo="";Contador=0;
				while(char_actual!="_"){
					Codigo+=char_actual;
					i=i+1;
					char_actual=cadena.substring(i,i+1);
					Contador++;
					if(Contador>=200){
						alert ("FORMATO DE VARIABLE MUY LARGO");
						exit(0);
					}
				}; 
		//		alert(Codigo);
				Contador=0;
				while(char_actual!="]"){
					i=i+1;
					char_actual=cadena.substring(i,i+1);
					Contador++;
					if(Contador>=200){
						alert ("FORMATO DE VARIABLE MUY LARGO");
						exit(0);
					}
				}; 
				if(TipoVar=='Vari' || TipoVar=='Cons' || TipoVar=='Gvar'){
					CadenaAux=0;
				}else{
					alert ("FORMATO DE VARIABLE ERRONEO");
				} 
			}
			Texto+=CadenaAux;
		}
	//	alert(Texto)
		eval(Texto);
		alert("Sintaxis Valida");
	} 
	catch(e) {alert("Error:" + " - "+e.message);}
}
function Borrar(){
	document.form1.formula.value="";
}
function Borrar2(){
	document.form1.desc.value="";
}
function Borrar3(){
	document.form1.aporte.value="";
}
function insertAtCursor(myField, myValue) {
	//CEPV NICE CODE
	//IE support
	if (document.selection) {
		myField.focus();
		sel = document.selection.createRange();
		sel.text = myValue;
	}else if (myField.selectionStart || myField.selectionStart == '0') {
		var startPos = myField.selectionStart;
		var endPos = myField.selectionEnd;
		myField.value = myField.value.substring(0, startPos)
		+ myValue
		+ myField.value.substring(endPos, myField.value.length);
	} else {
		myField.value += myValue;
	}
	document.form1.variable.selectedIndex=-1;
	document.form1.constante.selectedIndex=-1;
	document.form1.variableg.selectedIndex=-1;
	document.form1.variableD.selectedIndex=-1;
	document.form1.constanteD.selectedIndex=-1;
	document.form1.variablegD.selectedIndex=-1;
	document.form1.variableA.selectedIndex=-1;
	document.form1.constanteA.selectedIndex=-1;
	document.form1.variablegA.selectedIndex=-1;
} 
var EstaVisibleI=false;
function MostrarI(){
	if(!EstaVisibleI){
		Element.show('TablaI');
		EstaVisibleI=true;
	}else{
		Element.hide('TablaI');
		EstaVisibleI=false;
	}
} 
var EstaVisibleII=false;
function MostrarII(){
	if(!EstaVisibleII){
		Element.show('TablaII');
		EstaVisibleII=true;
	}else{
		Element.hide('TablaII');
		EstaVisibleII=false;
	}
} 
function MostrarIII(Comp){
	switch(Comp){
		case "FI":{
			Element.show('variable');
			Element.hide('constante');
			Element.hide('variableg');
			Element.hide('concepto');
			$('variable').style.width='600px';
			break;
		}
		case "FII":{
			Element.hide('variable');
			Element.show('constante');
			Element.hide('variableg');
			Element.hide('concepto');
			$('constante').style.width='600px';
			break;
		}
		case "FIII":{
			Element.hide('variable');
			Element.hide('constante');
			Element.show('variableg');
			Element.hide('concepto');
			$('variableg').style.width='600px';
			break;
		}
		case "FIV":{
			Element.hide('variable');
			Element.hide('constante');
			Element.hide('variableg');
			Element.show('concepto');
			break;
		}
		case "TI":{
			$('variable').style.width='200px';
			$('constante').style.width='200px';
			$('variableg').style.width='200px';
			Element.show('variable');
			Element.show('constante');
			Element.show('variableg');
			Element.hide('concepto');
			break;
		}
		case "DI":{
			Element.show('variableD');
			Element.hide('constanteD');
			Element.hide('variablegD');
			Element.hide('conceptoD');
			$('variableD').style.width='600px';
			break;
		}
		case "DII":{
			Element.hide('variableD');
			Element.show('constanteD');
			Element.hide('variablegD');
			Element.hide('conceptoD');
			$('constanteD').style.width='600px';
			break;
		}
		case "DIII":{
			Element.hide('variableD');
			Element.hide('constanteD');
			Element.show('variablegD');
			Element.hide('conceptoD');
			$('variablegD').style.width='600px';
			break;
		}
		case "DIV":{
			Element.hide('variableD');
			Element.hide('constanteD');
			Element.hide('variablegD');
			Element.show('conceptoD');
			break;
		}
		case "TII":{
			$('variableD').style.width='200px';
			$('constanteD').style.width='200px';
			$('variablegD').style.width='200px';
			Element.show('variableD');
			Element.show('constanteD');
			Element.show('variablegD');
			Element.hide('conceptoD');
			break;
		}
		case "AI":{
			Element.show('variableA');
			Element.hide('constanteA');
			Element.hide('variablegA');
			Element.hide('conceptoA');
			$('variableA').style.width='600px';
			break;
		}
		case "AII":{
			Element.hide('variableA');
			Element.show('constanteA');
			Element.hide('variablegA');
			Element.hide('conceptoA');
			$('constanteA').style.width='600px';
			break;
		}
		case "AIII":{
			Element.hide('variableA');
			Element.hide('constanteA');
			Element.show('variablegA');
			Element.hide('conceptoA');
			$('variablegA').style.width='600px';
			break;
		}
		case "AIV":{
			Element.hide('variableA');
			Element.hide('constanteA');
			Element.hide('variablegA');
			Element.show('conceptoA');
			break;
		}
		case "TIII":{
			$('variableA').style.width='200px';
			$('constanteA').style.width='200px';
			$('variablegA').style.width='200px';
			Element.show('variableA');
			Element.show('constanteA');
			Element.show('variablegA');
			Element.hide('conceptoA');
			break;
		}
	}
} 
var EstaVisibleIV=false;
function MostrarIV(){
	if(!EstaVisibleIV){
		Element.show('TablaIV');
		EstaVisibleIV=true;
	}else{
		Element.hide('TablaIV');
		EstaVisibleIV=false;
	}
}
var EstaVisibleV=false;
function MostrarV(){
	if(!EstaVisibleV){
		Element.show('TablaV');
		EstaVisibleV=true;
	}else{
		Element.hide('TablaV');
		EstaVisibleV=false;
	}
} 
var EstaVisibleVI=false;
function MostrarVI(){
	if(!EstaVisibleVI){
		Element.show('TablaVI');
		EstaVisibleVI=true;
	}else{
		Element.hide('TablaVI');
		EstaVisibleVI=false;
	}
} 
function Eliminar(){
	mygrid.deleteRow(mygrid.getSelectedId());
} 
function Agregar(){
	if($('categorias_programaticas').options[$('categorias_programaticas').selectedIndex].value==0){
		alert('Debe Escojer una Categoria Programatica');
	}else if($('partidas_presupuestarias').options[$('partidas_presupuestarias').selectedIndex].value==0){
		alert('Debe Escojer una Partida Presupuestaria');
	}else{
		mygrid.addRow(i,$('categorias_programaticas').options[$('categorias_programaticas').selectedIndex].value+','+$('partidas_presupuestarias').options[$('partidas_presupuestarias').selectedIndex].value)
		i++;
	}
} 
function Guardar(){
<? if($Enlace=='1') { ?>
var JsonAux,PresupuestoAux=new Array;
	mygrid.clearSelection()
	for(j=0;j<mygrid.getRowsNum();j++){
			PresupuestoAux[j] = new Array;
			PresupuestoAux[j][0]= mygrid.cells(mygrid.getRowId(j),0).getValue();
			PresupuestoAux[j][1]= mygrid.cells(mygrid.getRowId(j),1).getValue();
	}
	JsonAux={"Presupuesto":PresupuestoAux};
	$("Presupuesto").value=JsonAux.toJSONString();
<? } ?>	
} 
function traePartidasPresupuestarias(cp){
	
	var url = 'updater_selects.php';
	var pars = 'combo=partidas_presupuestarias2&cp=' + cp +'&ms='+new Date().getTime();
	var updater = new Ajax.Updater('divcombopp', 
		url,
		{
			method: 'get',
			parameters: pars,
			asynchronous:true,
			evalScripts:true,
			onLoading:function(request){Element.show('cargador_partidas')},
			onComplete:function(request){Element.hide('cargador_partidas')}
		});
}
	function busca(busqueda, textAux, estatus, pagina,num)
	{
		var url = 'updater_busca_concepto.php';
		var pars = 'busqueda=' + busqueda +'&textAux='+textAux + '&estatus='+estatus +'&pagina='+pagina+'&num='+num;
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
</script>
<? require ("comun/footer.php"); ?>
