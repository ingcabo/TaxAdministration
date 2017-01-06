<? require ("comun/ini.php"); require ("comun/header.php"); 
	$q="SELECT * FROM rrhh.mod_conc WHERE modulo=1";
	$r= $conn->Execute($q);
	$C= !$r->EOF ? $r->fields['conc_cod'] : 0;
	$q="SELECT * FROM rrhh.mod_conc WHERE modulo=2";
	$r= $conn->Execute($q);
	$C1= !$r->EOF ? $r->fields['conc_cod'] : 0;
	$q="SELECT * FROM rrhh.mod_conc WHERE modulo=3";
	$r= $conn->Execute($q);
	$C2= !$r->EOF ? $r->fields['conc_cod'] : 0;
?>
<br />
<span class="titulo_maestro">Preparar Vacaciones</span>
<div id="formulario">
	<table width="600" border="0">
		<tr>
			<td width="100" >Fecha de Inicio:</td>
			<td >
				<table>
					<tr>
						<td><input name="FechaIni" id="FechaIni"  type="text" style="width:80px" /></td> 
						<td > 
							<a href="#" id="boton_fecha_ini" onclick="return false;"><img border="0" src="images/calendarA.png" width="20" height="20" /></a>  
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
									inputField        : "FechaIni",
									button            : "boton_fecha_ini",
									ifFormat          : "%d/%m/%Y",
									daFormat          : "%Y/%m/%d"
									//align             : "Br"
								});
							</script>
						</td>
						<td>&nbsp;&nbsp;&nbsp;<input type="button" value="Buscar" onClick="BuscarTrabajadores()"></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2" ><br />Nomina de Vacaciones Preparada:</td>
		</tr>
		<tr>
			<td colspan="2"><div id="gridbox" width="650" height="250" class="gridbox"></div><br /></td>
		</tr>
		<tr>
			<td colspan="2" align="right" ><input type="button" value="Peparar Vacaciones" onClick="Preparar()"></td>
		</tr>
		<tr>
			<td colspan="2" onClick="Visible()" class="titulo_maestro" style="cursor:pointer; font-size:12px" >Parametros</td>
		</tr>
		<tr id="Parametros" >
			<td colspan="2"  >
				<table>
					<tr>
						<td>Contrato Utilizado Para Vacaciones:</td>
						<td  ><?=helpers::combonomina($conn, '',$C,'','cont_nom','Contrato','int_cod','cont_nom','Contrato','','SELECT int_cod,cont_nom FROM rrhh.contrato WHERE emp_cod='.$_SESSION['EmpresaL'],'GuadarParametros()','','')?></td>
					</tr>
					<tr>
						<td  >Constante Utilizada Para los Dias De Difrute:</td>
						<td  ><?=helpers::combonomina($conn, '',$C1 ,'','cons_nom','Constante1','int_cod','cons_nom','Constante1','','SELECT int_cod,cons_nom FROM rrhh.constante','GuadarParametros()','','')?></td>
					</tr>
					<tr>
						<td  >Constante Para los Dias Adicionales Por A&ntilde;os de Antiguedad:</td>
						<td  ><?=helpers::combonomina($conn, '',$C2 ,'','cons_nom','Constante2','int_cod','cons_nom','Constante2','','SELECT int_cod,cons_nom FROM rrhh.constante','GuadarParametros()','','')?></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<hr>
	<br>
	<? 
		$q = "SELECT A.int_cod,A.tra_nom,A.tra_ape,A.tra_fec_ing,D.vac_fec_ini,D.vac_fec_fin,D.vac_dias,D.vac_dias_pendientes FROM ((rrhh.trabajador AS A INNER JOIN rrhh.departamento AS B ON A.dep_cod=B.int_cod) INNER JOIN rrhh.division AS C ON B.div_cod=C.int_cod) LEFT JOIN rrhh.vacaciones AS D ON A.int_cod=D.tra_cod WHERE A.tra_estatus!=4 AND A.tra_estatus!=5 AND C.emp_cod=".$_SESSION['EmpresaL']." ORDER BY A.int_cod";
		$rV = $conn->Execute($q);
		$j=0;
		if(!$rV->EOF){ ?>
		<table class="sortable" id="grid" cellpadding="0" style=" width:650px " cellspacing="1">
			<tr class="cabecera"> 
				<td>Trabajador</td>
				<td>Fecha Ingreso</td>
				<td>Fecha Inicio Vac</td>
				<td>Fecha Fin Vac</td>
				<td>Dias Difrutados</td>
				<td>Dias Pendiente</td>
				<td>Estatus</td>
			</tr>
		<? 
			while(!$rV->EOF){
				$Trabajador=cadena($rV->fields["tra_nom"])." ".cadena($rV->fields["tra_ape"]);
				$Estatus="-";
				if(empty($rV->fields["vac_fec_ini"])){
					$Estatus="No ha difrutado Vacaciones";
				}else{
					if(strtotime($rV->fields["vac_fec_ini"])<=strtotime(date("Y-m-d")) && strtotime(date("Y-m-d"))<=strtotime($rV->fields["vac_fec_fin"])){
						$Estatus="Actualmente en Vacaciones";
					}else{
						$Estatus="Ultimas Vacaciones Difrutadas";
					}
				}
		?> 
				<tr class="filas"> 
					<td align="center"><?=$Trabajador?></td>
					<td align="center"><?=muestrafecha($rV->fields["tra_fec_ing"])?></td>
					<td align="center"><?=!empty($rV->fields["vac_fec_ini"]) ? muestrafecha($rV->fields["vac_fec_ini"]) : "-"?></td>
					<td align="center" ><?=!empty($rV->fields["vac_fec_fin"]) ? muestrafecha($rV->fields["vac_fec_fin"]) : "-"?></td>
					<td align="center" ><?=!empty($rV->fields["vac_dias"]) ? $rV->fields["vac_dias"] : "-"?></td>
					<td align="center" <? if($rV->fields["vac_dias_pendientes"]>0) { ?> bgcolor="#FFD5D5" <? } ?>><?=!empty($rV->fields["vac_dias_pendientes"]) ? $rV->fields["vac_dias_pendientes"] : "-"?></td>
					<td align="center" <? if($Estatus=="Actualmente en Vacaciones") { ?> bgcolor="#FFD5D5" <? } ?>><?=$Estatus?></td>
				</tr>
		<? 	$rV->movenext();
		  }
	?></table><?
	} else {
		echo "No hay registros en la bd";
	}
	?>
</div>

<script>
var mygrid;
buildGrid();
CargarGrid();
function buildGrid(){
	//set grid parameters
	mygrid = new dhtmlXGridObject('gridbox');
	mygrid.selMultiRows = true;
	mygrid.setImagePath("js/grid/imgs/");
	mygrid.setHeader("Trabajador,Fecha de Ingreso,Inicio Vacaciones,Fin Vacaciones,Dias Ultimo Periodo,Dias Pendientes, Seleccionar");
	mygrid.setInitWidths("120,90,90,90,110,80,70")
	mygrid.setColAlign("center,center,center,center,center,center,center")
	mygrid.setColTypes("ro,ro,ed,ed,ed,ed,ch");
	mygrid.setColSorting("str,str,str,str,str,int,int")
	mygrid.setColumnColor("white,white,white,white,white,white,white")
	mygrid.rowsBufferOutSize = 0;
	mygrid.setMultiLine(false);
	mygrid.selmultirows="true";
	mygrid.delim=";";
	mygrid.setOnEditCellHandler(Variaciones);
	mygrid.init();
} 
function BuscarTrabajadores(){
var JsonAux;
	mygrid.clearSelection();
	mygrid.clearAll();
	if($('FechaIni').value!="" ){
		JsonAux={"FechaIni":$('FechaIni').value,"Forma":7,"Accion":0};
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
							mygrid.addRow(JsonRec[i]['CI'],JsonRec[i]['N']+";"+JsonRec[i]['FI']+";"+JsonRec[i]['FIV']+";"+JsonRec[i]['FFV']+";"+JsonRec[i]['DI']+";0;0");
						} 
					}
				}
			}
		); 
	}else{
		alert("Debe colocar una fecha de inicio")
	}
}
function Variaciones(stage,rowId,cellInd){
var Fecha,Dias;
	if(stage==2){
		if(cellInd=='2' || cellInd=='4'){
			Fecha=mygrid.cells(rowId,'2').getValue();
			Dias=mygrid.cells(rowId,'4').getValue();
			if(Fecha && Dias){
				JsonAux={"FechaIni":Fecha,"Dias":Dias,"Forma":11,"Accion":0};
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
								mygrid.cells(rowId,'3').setValue(request.responseText);
							}
						}
					}
				); 
			}
		}
		if(cellInd=='3'){
			FechaIni=mygrid.cells(rowId,'2').getValue();
			FechaFin=mygrid.cells(rowId,'3').getValue();
			if(FechaIni && FechaFin){
				JsonAux={"FechaIni":FechaIni,"FechaFin":FechaFin,"Forma":11,"Accion":1};
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
								mygrid.cells(rowId,'4').setValue(request.responseText);
//								alert(request.responseText);
							}
						}
					}
				); 
			}
		}
	}
} 
function Preparar(){
var Trabajadores= new Array,Bandera=false;
	for(i=0;i<mygrid.getRowsNum();i++){
		if(mygrid.cells(mygrid.getRowId(i),6).isChecked()){
			Bandera=true;
			break;
		}
	}
	if(Bandera){
		for(i=0;i<mygrid.getRowsNum();i++){
			Trabajadores[i] = new Array;
			Trabajadores[i][0]= mygrid.getRowId(i);
			Trabajadores[i][1]= mygrid.cells(mygrid.getRowId(i),2).getValue();
			Trabajadores[i][2]= mygrid.cells(mygrid.getRowId(i),3).getValue();
			Trabajadores[i][3]= mygrid.cells(mygrid.getRowId(i),4).getValue();
			Trabajadores[i][4]= mygrid.cells(mygrid.getRowId(i),5).getValue();
			Trabajadores[i][5]= mygrid.cells(mygrid.getRowId(i),6).isChecked();
		}
		JsonAux={"Trabajadores":Trabajadores,"Forma":11,"Accion":2};
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
						alert("Operacion Realizada Con Exito");
					}
				}
			}
		); 
	}else{
		alert("Debe seleccionar al menos a un trabajador");
	}
}
var EstaVisible=false;
Element.hide('Parametros');
function Visible(){
	if(EstaVisible){
		Element.hide('Parametros');
		EstaVisible=false;
	}else{
		Element.show('Parametros');
		EstaVisible=true;
	}
}
function GuadarParametros(){
var JsonAux;
	JsonAux={"Contrato":$('Contrato').options[$('Contrato').selectedIndex].value,"Constante1":$('Constante1').options[$('Constante1').selectedIndex].value,"Constante2":$('Constante2').options[$('Constante2').selectedIndex].value,"Forma":11,"Accion":3};
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
					alert("Operacion Realizada");
				}else{
					alert("Ha ocurrido un error durante la operacion");
				}
			}
		}
	); 
} 
function CargarGrid(){
var JsonAux,FechaAux,Bandera=true;
	mygrid.clearSelection();
	mygrid.clearAll();
	JsonAux={"Forma":7,"Accion":1};
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
						mygrid.addRow(JsonRec[i]['CI'],JsonRec[i]['N']+";"+JsonRec[i]['FI']+";"+JsonRec[i]['FIV']+";"+JsonRec[i]['FFV']+";"+JsonRec[i]['D']+";"+JsonRec[i]['DP']+";1");
					}  
				}
			}
		}
	);  
} 
</script>
<? require ("comun/footer.php"); ?>


