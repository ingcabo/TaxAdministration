<?
include('adodb/adodb-exceptions.inc.php');
require ("comun/ini.php");
$q="SELECT conc_cod FROM rrhh.mod_conc WHERE modulo=0";
$rC= $conn->Execute($q);
$ConceptoA= !$rC->EOF ? $rC->fields['conc_cod'] : -1 ;
$txt= $ConceptoA==-1 ? "(No se ha escojido ningun concepto)" : "";
$oprestamo = new prestamo;
$accion = $_REQUEST['accion'];
if($accion == 'Guardar'){
	$msj =$oprestamo->add($conn, $_REQUEST['Trabajador'], $_REQUEST['Contrato'], $_REQUEST['Fecha'],guardafloat($_REQUEST['Monto']), $_REQUEST['Cuotas'], $_REQUEST['Estatus'], $_REQUEST['Descripcion'],$_REQUEST['CuotasDet'],$_REQUEST['NroPrestamo']);
}elseif($accion == 'Actualizar'){
	$msj =$oprestamo->set($conn, $_REQUEST['int_cod'], $_REQUEST['Trabajador'], $_REQUEST['Contrato'], $_REQUEST['Fecha'],guardafloat($_REQUEST['Monto']), $_REQUEST['Cuotas'], $_REQUEST['Estatus'], $_REQUEST['Descripcion'],$_REQUEST['CuotasDet'],$_REQUEST['NroPrestamo']);
}elseif($accion == 'del'){
	$msj =$oprestamo->del($conn, $_REQUEST['int_cod']);
}

$cprestamo=$oprestamo->get_all($conn,'pres_estatus',$_POST['TipoB'],$_POST['textAux']);
require ("comun/header.php");

if(!empty($msj)){ ?><div id="msj" ><?=$msj?></div><? echo "<br>"; } ?>
<br />
<span class="titulo_maestro">Prestamos</span>
<script>var mygrid;</script>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />
<table style="margin-left:10px">
	<tr >
		<td onClick="DivPres_Conc()" style="cursor:pointer; font-size; font-weight:bold" class="titulo_maestro">Parametros</td>
		<td id="text"><?=$txt?></td>
	</tr>
	<tr>
		<td  colspan="2">
			<div id="Pres_Conc" width="700" height="100" style="display:none;">
			<!--
			<table>
				<tr>
					<td>
						<input type="text" id="ConceptoCodigo" name="ConceptoCodigo" readonly="true" style="width:35">
						<input type="text" id="ConceptoNombre" name="ConceptoNombre"  onKeyUp="traeCodigo(this.value)" onMouseOut="traeCodigo(this.value)" style="width:230">
						<div id="autocomplete_choices" class="autocomplete"></div>
						<input type="button" value="Agregar" onClick="Agregar()">
					</td>
					<td align="right" ><input type="button" value="Eliminar" onClick="Eliminar()"></td>
				</tr>
				<tr>
					<td colspan="2"><div id="gridbox" width="700" height="100" class="gridbox"></div></td>
				</tr>			
			</table> -->
				Concepto Asociado:
				<?=helpers::combonomina($conn, 'rrhh.concepto', $ConceptoA,'','int_cod','Concepto','int_cod','conc_nom','','','','CambiarConcepto()','true')?>
			</div>
		</td>
	</tr>
</table>
<br>
<form name="formAux" method="post">
<table border="0" style="margin-left:10px" width="800">
	<tr align="center" >
		<td>
			Buscar por:&nbsp;&nbsp;&nbsp;
			<select name="TipoB" >
				<option value="0" <?=$_POST['TipoB']==0 ? "selected" : ""?>>Cedula</option>
				<option value="1" <?=$_POST['TipoB']==1 ? "selected" : ""?>>Nombre</option>
				<option value="2" <?=$_POST['TipoB']==2 ? "selected" : ""?>>Apellido</option>
			</select>
			<input type="text" name="textAux" value="<?=$_POST['textAux']?>">
			&nbsp;&nbsp;&nbsp;<input type="submit" value="Buscar">
		</td>
	</tr>
</table>
</form>
<br>
<? if(is_array($cprestamo)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>Fecha</td>
<td>Trabajador</td>
<td>Estatus</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cprestamo as $prestamo) { 
?> 
<tr class="filas"> 
<td><?=date("d/m/Y",strtotime($prestamo->pres_fecha))?></td>
<td align="center"><?=$prestamo->tra_nom?></td>
<td align="center"><? if($prestamo->pres_estatus==0){ echo "Por Aprobar"; } elseif($prestamo->pres_estatus==1){ echo "Aprobado"; } elseif($prestamo->pres_estatus==2){ echo "Rechazado"; } elseif($prestamo->pres_estatus==3){ echo "Cancelado"; }?></td>
<td align="center"><?=!empty($prestamo->int_cod) ? '<a href="prestamo.pdf.php?id='.$prestamo->int_cod.'" target="_blank" title="Emitir Reporte"><img style="width:16px; height:10px" src="images/reporte.jpg" border="0" /></a>' : "" ?></td>
<td align="center"><a href="?accion=del&int_cod=<?=$prestamo->int_cod?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
<td align="center"><a href="#" onclick="updater('<?=$prestamo->int_cod?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
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
<?
require ("comun/footer.php"); ?>
<script>

//new Ajax.Autocompleter("ConceptoNombre", "autocomplete_choices", "AutoCompleteConceptos.php", {});
var presGrid;
var Indice=1;
//CargarGrid();
function ComboTrabajador(){
var JsonAux;
	$('Trabajador').length=0;
	JsonAux={"Contrato":parseInt($('Contrato').options[$('Contrato').selectedIndex].value),"Departamento":parseInt($('Departamento').options[$('Departamento').selectedIndex].value),"Forma":4};
	var url = 'CargarCombo.php';
	var pars = 'JsonEnv=' + JsonAux.toJSONString();
	var Request = new Ajax.Request(
		url,
		{
			method: 'post',
			parameters: pars,
	//		asynchronous:true, 
			onComplete:function(request){
				var JsonRec = eval( '(' + request.responseText + ')');
				if(JsonRec){
					$('Trabajador').options[0]=new Option("Seleccione",-1);
					for(var i=1;i<=JsonRec.length;i++){
						$('Trabajador').options[i]= new Option(Cadena(JsonRec[i]['N'])+" "+Cadena(JsonRec[i]['A']),JsonRec[i]['CI']);
					}
				}
			}
		}
	); 
	BuscarTipoContrato();
	ClearAll();
} 
function BuscarTipoContrato(){
	JsonAux={"Contrato":$('Contrato').options[$('Contrato').selectedIndex].value,"Forma":6,"Accion":1};
	var url = 'OtrosCalculos.php';
	var pars = 'JsonEnv=' + JsonAux.toJSONString();
	var Request = new Ajax.Request(
		url,
		{
			method: 'post',
			parameters: pars,
			//asynchronous:false, 
			onComplete:function(request){
				var JsonRec = eval( '(' + request.responseText + ')');
				if(JsonRec){
					$('cont_tipo').value=JsonRec;
				}
			}
		}
	); 
}
function BuscarPrestamo(){
	//Validacion de Numero Prestamo  start
	if($('Trabajador').options[$('Trabajador').selectedIndex].value!=-1){
		JsonAux={"Trabajador":$('Trabajador').options[$('Trabajador').selectedIndex].value,"Forma":6,"Accion":4};
		var url = 'OtrosCalculos.php';
		var pars = 'JsonEnv=' + JsonAux.toJSONString();
		var Request = new Ajax.Request(
			url,
			{
				method: 'post',
				parameters: pars,
				asynchronous:true, 
				onComplete:function(request){
					var JsonRec = eval( '(' + request.responseText + ')');
					$('NroPrestamo').value = JsonRec;
				}
			}
		);
	}
	//Validacion de Numero  prestamo end
}
function CambiarConcepto(){
	if($('Concepto').options[$('Concepto').selectedIndex].value!=-1){
		JsonAux={"Concepto":$('Concepto').options[$('Concepto').selectedIndex].value,"Forma":6,"Accion":5};
		var url = 'OtrosCalculos.php';
		var pars = 'JsonEnv=' + JsonAux.toJSONString();
		var Request = new Ajax.Request(
			url,
			{
				method: 'post',
				parameters: pars,
				//asynchronous:false, 
				onComplete:function(request){
					var JsonRec = eval( '(' + request.responseText + ')');
					if(JsonRec){
						$('text').innerHTML="";
						alert("Operacion realiza con exito");
					}else{
						alert("Fallo la Operacion");
					}
				}
			}
		); 
	}else{
		alert("Debe Escojer un Concepto");
	}
}
function ClearAll(){
	$('Monto').value="";
	$('Cuotas').value=""; 
	$('Fecha').value="";
	$('msjMonto').innerHTML="";
	mygrid.clearSelection();
	mygrid.clearAll();
}
function Guardar(){
var JsonAux,CuotasDetAux=new Array;
	mygrid.clearSelection()
	$('Trabajador').disabled=false;
	$('Contrato').disabled=false;
	$('Estatus').disabled=false;
	if($('Trabajador').options[$('Trabajador').selectedIndex].value==-1){
		alert("Debe Escojer un Trabajador");
	}else if($('Monto').value=="" && $('Cuotas').value=="" && $('Fecha').value==""){
		alert("Informacion No Valida");
	}else{
		for(j=0;j<mygrid.getRowsNum();j++){
			CuotasDetAux[j] = new Array;
			CuotasDetAux[j][0]= mygrid.cells(j,0).getValue();
			CuotasDetAux[j][1]= mygrid.cells(j,1).getValue();
			CuotasDetAux[j][2]= mygrid.cells(j,2).getValue();
			CuotasDetAux[j][3]= parseFloat(usaFloat(mygrid.cells(j,3).getValue()));
			CuotasDetAux[j][4]= parseFloat(usaFloat(mygrid.cells(j,4).getValue()));
			CuotasDetAux[j][5]= mygrid.cells(j,5).getValue();
		}
		JsonAux={"CuotasDet":CuotasDetAux};
		$("CuotasDet").value=JsonAux.toJSONString();
		document.form1.submit();
	}
} 
var EstaVisible=false;
function BuscarDatos(){
var Valor;
	if(!EstaVisible){
		if($('Trabajador').options[$('Trabajador').selectedIndex].value!=-1){
			Element.show('DatosTrabajador');
			JsonAux={"Trabajador":$('Trabajador').options[$('Trabajador').selectedIndex].value,"Forma":6,"Accion":0};
			var url = 'OtrosCalculos.php';
			var pars = 'JsonEnv=' + JsonAux.toJSONString();
			var Request = new Ajax.Request(
				url,
				{
					method: 'post',
					parameters: pars,
					//asynchronous:false, 
					onComplete:function(request){
						var JsonRec = eval( '(' + request.responseText + ')');
						if(JsonRec){
							$('tra_fec_ing').value=JsonRec[0];
							$('tra_sueldo').value=JsonRec[1];
						}
					}
				}
			); 
			EstaVisible=true;
		}
	}else{
		Element.hide('DatosTrabajador');
		EstaVisible=false;
	}
}
function CargarGrid(){
var JsonAux,FechaAux,Bandera=true;
	mygrid.clearSelection();
	mygrid.clearAll();
	$('msjMonto').innerHTML="";
	$('msjTrabajador').innerHTML="";
	if($('Contrato').options[$('Contrato').selectedIndex].value!=-1 && $('Monto').value!="" && $('Cuotas').value!="" && $('Fecha').value!=""){
		//Validacion de monto acumulado
		JsonAux={"Trabajador":$('Trabajador').options[$('Trabajador').selectedIndex].value,"Forma":6,"Accion":3};
		var url = 'OtrosCalculos.php';
		var pars = 'JsonEnv=' + JsonAux.toJSONString();
		var Request = new Ajax.Request(
			url,
			{
				method: 'post',
				parameters: pars,
				asynchronous:true, 
				onComplete:function(request){
					var JsonRec = eval( '(' + request.responseText + ')');
					if(parseFloat(usaFloat($('Monto').value))>(JsonRec*0.5)){
						$('msjMonto').innerHTML="* El monto del prestamo excede el 50% de lo acumulado por el trabajador";
					}
				}
			}
		); 
		//Validacion de monto acumulado end
		//Validacion de Inicio de Nomina start
		FechaAux=$('Fecha').value;
		FechaAux=FechaAux.split("/");
		if($('cont_tipo').value=='1' && FechaAux[0]!='01' && FechaAux[0]!='16'){
			alert("Para contratos quicenales los dias de inicio de nomina son 01 y 16");
			Bandera=false;
		}
		if($('cont_tipo').value=='2' && FechaAux[0]!='01'){
			alert("Para contratos mensuales el dias de inicio de nomina es 01");
			Bandera=false;
		}
		if($('cont_tipo').value=='3'){
			alert("No puede usar este contratos porque no es ni 'semanal', ni 'quincenal' ni 'mensual'");
			Bandera=false;
		}
		//Validacion de Inicio de Nomina end
		//Valicacion de Fin de Nomina start
		JsonAux={"Fecha":$('Fecha').value,"Contrato":$('Contrato').options[$('Contrato').selectedIndex].value,"Cuotas":parseInt($('Cuotas').value),"Forma":6,"Accion":6};
		var url = 'OtrosCalculos.php';
		var pars = 'JsonEnv=' + JsonAux.toJSONString();
		var Request = new Ajax.Request(
			url,
			{
				method: 'post',
				parameters: pars,
				asynchronous:true, 
				onComplete:function(request){
					var JsonRec = eval( '(' + request.responseText + ')');
					if(JsonRec){
						alert("El numero de cuotas exede el año actual");
						ClearAll();
					}	
				}
			}
		); 
		//Valicacion de Fin de Nomina end
		if(Bandera){
			JsonAux={"Contrato":parseInt($('Contrato').options[$('Contrato').selectedIndex].value),"Trabajador":parseInt($('Trabajador').options[$('Trabajador').selectedIndex].value),"Monto":parseFloat($('Monto').value),"Cuotas":parseInt($('Cuotas').value),"Fecha":$('Fecha').value,"Forma":6,"Accion":0};
			var url = 'OperarGrid.php';
			var pars = 'JsonEnv=' + JsonAux.toJSONString();
			var Request = new Ajax.Request(
				url,
				{
					method: 'post',
					parameters: pars,
			//		asynchronous:true, 
					onComplete:function(request){
						var JsonRec = eval( '(' + request.responseText + ')');
						if(JsonRec){
							for(var i=0;i<JsonRec.length;i++){
								mygrid.addRow(i,JsonRec[i]['Nro']+";"+JsonRec[i]['FechaIni']+";"+JsonRec[i]['FechaFin']+";"+muestraFloat(JsonRec[i]['Porc'],2)+";"+muestraFloat(JsonRec[i]['Monto'],2)+";Por Cobrar",i);
							}  
						}
					}
				}
			);  
		}
	} 
} 
function CargarGrid3(Cuota,Fecha){
var JsonAux,CuotasDetAux=new Array;
	mygrid.clearSelection();
	for(j=0;j<mygrid.getRowsNum();j++){
		CuotasDetAux[j] = new Array;
		CuotasDetAux[j][0]= mygrid.cells(j,0).getValue();
		CuotasDetAux[j][1]= mygrid.cells(j,1).getValue();
		CuotasDetAux[j][2]= mygrid.cells(j,2).getValue();
		CuotasDetAux[j][3]= mygrid.cells(j,3).getValue();
		CuotasDetAux[j][4]= parseFloat(usaFloat(mygrid.cells(j,4).getValue()));
		CuotasDetAux[j][5]= mygrid.cells(j,5).getValue();
	}
	mygrid.clearAll();
	JsonAux={"CuotasDet":CuotasDetAux,"Cuota":Cuota,"Fecha":Fecha,"TipoContrato":$('cont_tipo').value,"Forma":6,"Accion":2};
	var url = 'OperarGrid.php';
	var pars = 'JsonEnv=' + JsonAux.toJSONString();
	var Request = new Ajax.Request(
		url,
		{
			method: 'post',
			parameters: pars,
	//		asynchronous:true, 
			onComplete:function(request){
				var JsonRec = eval( '(' + request.responseText + ')');
				if(JsonRec){
					for(var i=0;i<JsonRec.length;i++){
						mygrid.addRow(i,JsonRec[i]['Nro']+";"+JsonRec[i]['FechaIni']+";"+JsonRec[i]['FechaFin']+";"+muestraFloat(JsonRec[i]['Porc'],2)+";"+muestraFloat(JsonRec[i]['Monto'],2)+";"+JsonRec[i]['Estatus'],i);
					}  
				}
			}
		}
	);  
} 
var EstaVisible2=false;
function DivPres_Conc(){
var Valor;
	if(!EstaVisible){
		Element.show('Pres_Conc');
		EstaVisible=true;
		//buildGrid2();
	}else{
		Element.hide('Pres_Conc');
		EstaVisible=false;
	}
}
/*
function buildGrid2(){
	//set grid parameters
	presGrid = new dhtmlXGridObject('gridbox');
	presGrid.selMultiRows = true;
	presGrid.setImagePath("js/Grid/imgs/");
	presGrid.setHeader("Codigo,Descripcion");
	presGrid.setInitWidths("100,600");
	presGrid.setColAlign("center,center");
	presGrid.setColTypes("ro,ro");
	presGrid.setColSorting("int,str");
	presGrid.setColumnColor("white,white");
	presGrid.rowsBufferOutSize = 0;
	presGrid.setMultiLine(false);
	presGrid.selmultirows="true";
	//presGrid.setOnEditCellHandler(ValidarFecha);
	
	//start grid
	presGrid.init();

}
function traeCodigo(Concepto){
	JsonAux={"Concepto":Concepto,"Forma":5,"Accion":0};
	var url = 'OtrosCalculos.php';
	var pars = 'JsonEnv=' + JsonAux.toJSONString();
	var Request = new Ajax.Request(
		url,
		{
			method: 'post',
			parameters: pars,
	//		asynchronous:true, 
			onComplete:function(request){
				if(request.responseText){
					$('ConceptoCodigo').value=request.responseText;
				}else{
					$('ConceptoCodigo').value="";
				}
			}
		}
	); 
} 
function Agregar(){
var Bandera=true,i,ConceptoValor;
		if($('ConceptoCodigo').value){
			if(!presGrid.isItemExists($('ConceptoCodigo').value)){
				//CalcularValorConcepto($('Trabajador').options[$('Trabajador').selectedIndex].value,$('ConceptoCodigo').value);
				presGrid.addRow(Indice,$('ConceptoCodigo').value+","+$('ConceptoNombre').value,Indice);
				Indice++;
			}else{
				alert("El concepto ya fue agregado");
			}
		}else{
			alert("Concepto no existe");
		}
}
function Eliminar(){
	presGrid.deleteRow(presGrid.getSelectedId());
	//CalcularMontoTotalGrid2();
}*/
</script>

