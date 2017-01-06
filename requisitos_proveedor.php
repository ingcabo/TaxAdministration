<?php 
require ("comun/ini.php");
require('comun/header_popup.php');
//include ("lib/core.lib.php");
?>
<!--<style type="text/css" media="screen">@import url("css/estilos.css");</style>
<script src="js/prototype.js"></script>
<script src="js/script_st.js"></script>
<script src="js/scriptaculous.js" type="text/javascript"></script>-->
<span class="titulo_maestro">Requisitos por Proveedor</span>

<?php 
$id_proveedores=@$_REQUEST['id_proveedores'];
$id_grupo= $_REQUEST['grupo'];

#GURADO.
if(!empty($_REQUEST['accion'])){ 


				#BORRO.
				$sql_del="DELETE FROM relacion_req_prov WHERE id_proveedores=$id_proveedores";
			    $rd = $conn->Execute($sql_del);
				//echo $sql_del;
				//si le paso a postgre una consulta con la fecha vacia, no inserta nada, en mysql no pasa lo mismo. quien propuso postgre para este proyecto?
					$JsonRec = new Services_JSON();
					$JsonRec=$JsonRec->decode(str_replace("\\","",$_REQUEST['requisito_prov']));
						if(is_array($JsonRec->requisito)){
							foreach ($JsonRec->requisito as $oRP_Aux){
								if($oRP_Aux[2]==''){
									$q = "INSERT INTO puser.relacion_req_prov ";
									$q.= "( id_proveedores, id_requisitos, prorroga, fecha_emi) ";
									$q.= "VALUES ";
									$q.= "('$id_proveedores', '$oRP_Aux[0]', '$oRP_Aux[3]', '".guardafecha($oRP_Aux[1])."') ";
								} else {
									$q = "INSERT INTO puser.relacion_req_prov ";
									$q.= "( id_proveedores, id_requisitos, prorroga, fecha_emi, fecha_vcto) ";
									$q.= "VALUES ";
									$q.= "('$id_proveedores', '$oRP_Aux[0]', '$oRP_Aux[3]', '".guardafecha($oRP_Aux[1])."', '".guardafecha($oRP_Aux[2])."') ";
									}
								
								
								//die($q);
						$conn->Execute($q);
						
						//echo "<br>".$sql."<br>";
					}
				}
					
/*start validacion*/

			
					
/* end validación */
					
				
		
}

#PROVEEDORES
	$q = "SELECT * FROM proveedores ORDER BY id";
	$r = $conn->Execute($q);
#REQUISITOS POR PROVEEDOR
	
	/*$c="select  requisitos.descripcion AS descripcion, 
				relacion_req_prov.fecha_emi AS fecha_emi, 
				relacion_req_prov.fecha_vcto AS fecha_vcto,
				relacion_req_prov.prorroga AS prorroga,
				requisitos.vencido AS vence,
				relacion_req_prov.id_requisitos AS id_requisitos
		from proveedores,relacion_req_prov,requisitos
		where proveedores.id=relacion_req_prov.id_proveedores
			and relacion_req_prov.id_requisitos=requisitos.id
			and relacion_req_prov.id_proveedores=$id_proveedores";
	$rs = $conn->Execute($c);
	$tt=@$rs->RecordCount();*/
//	echo $conn->ErrorMsg();
#REQUISISTOS POR GRUPO DE PROVEEDRO	
	$rp="SELECT 
	  requisitos.nombre AS nombre,
	  requisitos.vencido AS vence,
	  requisitos.descripcion AS descripcion,
	  relacion_req_gp.fecha_emi AS fecha_emi,
	  relacion_req_gp.fecha_vcto AS fecha_vcto,
	  relacion_req_gp.prorroga AS prorroga
	FROM
	 proveedores
	 INNER JOIN relacion_req_gp ON (proveedores.id_grupo_prove=relacion_req_gp.id_grupo_proveedor)
	 INNER JOIN requisitos ON (relacion_req_gp.id_requisito=requisitos.id)
	WHERE
	  (proveedores.id = $id_proveedores)";
	  //die($rp);
	  $rgp = $conn->Execute($rp);		
		
//require ("comun/header.php");

if(!empty($msj)){
echo "<div id='msj'><p align='left'>".$msj."</p></div>";
}

?>


<div id="formulario_popup">
<form name="form1" action="requisitos_proveedor.php?id_proveedores=<?=$id_proveedores?>" method="post">
  <table width="357" border="0">
  <tr>
    <td width="67">Proveedor:</td>

    <td width="41">
	<select name="proveedores" onChange="MM_jumpMenu('requisitos_proveedor.php','self',this,0)" disabled=disabled>
	<?php 	
	while(!$r->EOF){
	$id = $r->fields['id'];
	$nombre = $r->fields['nombre'];
	?>	
	<option value="?id_proveedores=<?=$id?>" <?php if(@$id==@$id_proveedores) { echo "selected"; } ?>><?=$nombre?></option>
	<?php $r->movenext();
	} ?>
	</select>
	</td>
	<td width="235">&nbsp;&nbsp;&nbsp;&nbsp;
<!--	<a href="proveedores.php">Volver</a>-->
	<input name="accion" type="submit" value="Guardar" onclick="Guardar();" readonly />
	<input name="id" type="hidden" value="<?=$id_proveedores?>" /></td>
  </tr>
</table>
<br>
<table width="500"   cellpadding="0" cellspacing="0">
  <tr>
	<td width="167">Requisitos:</td>
	<td colspan="2"><?=helpers::combo_ue_cp($conn, 
														'requisitos', 
														$objeto->id_requisito,
														'width:160px',
														'',
														'',
														'',
														"traeVencido(this.value);",
														"SELECT DISTINCT puser.relacion_req_gp.id_requisito AS id, puser.requisitos.nombre AS descripcion FROM puser.grupos_proveedores Inner Join puser.relacion_req_gp ON puser.grupos_proveedores.id = puser.relacion_req_gp.id_grupo_proveedor Inner Join puser.relacion_provee_grupo_provee ON puser.relacion_provee_grupo_provee.id_grupo_provee = puser.grupos_proveedores.id Inner Join puser.requisitos ON puser.relacion_req_gp.id_requisito = puser.requisitos.id WHERE puser.relacion_provee_grupo_provee.id_provee = '$id_proveedores'")?> 
	<input type="hidden" name="vencido" id="vencido" />													
	</td>
</tr>
<tr>
	<td>Fecha Entrega:</td>
	<td colspan="2"><div align="left" style="width:190px"><input style="width:100px"  type="text" name="busca_fecha_desde" id="busca_fecha_desde" readonly/>
							<a href="#" id="boton_busca_fecha_desde" onclick="return false;">
								<img border="0" src="images/calendarA.png" width="20" height="20" />							</a>  
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
								inputField        : "busca_fecha_desde",
								button            : "boton_busca_fecha_desde",
								ifFormat          : "%d/%m/%Y",
								daFormat          : "%Y/%m/%d",
								align             : "Br"
							 });
							 
						</script>
						</div></td>
</tr>
<tr>
	<td>Fecha Expiracion:</td>
	<td colspan="2" height="29px"><div align="left" style="width:190px"><input style="width:100px"  type="text" name="busca_fecha_hasta" id="busca_fecha_hasta" readonly />
	<div id="muestra_calendario" style="display:inline">
							<a href="#" id="boton_busca_fecha_hasta" onclick="return false;" >
								<img border="0" src="images/calendarA.png" width="20" height="20" />							</a>  
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
								inputField        : "busca_fecha_hasta",
								button            : "boton_busca_fecha_hasta",
								ifFormat          : "%d/%m/%Y",
								daFormat          : "%Y/%m/%d",
								align             : "Br"
							 });
						</script>
		</div>
						</div></td>
</tr>
<tr height="30px">
	<td>Prorroga:</td>
	<td colspan="2"><div align="left"><input type="text" name="prorroga" id="prorroga" style="width:75px; text-align:right"  /></div><input type="hidden" name="requisito_prov" id="requisito_prov" /> </td>
</tr>
<tr>
	<td align="left"><input type="button" name="b_agregar_co" onclick="AgregarRP();" value="Agregar"></td>
	<td width="113">&nbsp;</td>
	<td width="220" align="right"><input type="button" name="b_eliminar_co" onclick="EliminarRP()" value="Eliminar"></td>	
</tr>
<tr>
	<td colspan="3"><hr /></td>
</tr>
<tr>
	<td colspan="3" align="center">
	<div id="gridboxrp" style=" width:500px; height:150px; z-index:0; display:block" class="gridbox" align="center"></div></td>
</tr>
</table>
</form>
</div>
<? require ("comun/footer.php"); ?>
<script language="javascript" type="text/javascript">
i=0;
buildGridRP();
CargarGrid('<?=$id_proveedores?>');

function buildGridRP(){
	//set grid parameters
	mygridrp = new dhtmlXGridObject('gridboxrp');
	mygridrp.selMultiRows = true;
	mygridrp.setImagePath("js/Grid/imgs/");
	mygridrp.setHeader("Requisito,Fecha Entrega,Fecha Exp,Prorroga");
	mygridrp.setInitWidths("250,100,100,50");
	mygridrp.setColAlign("left,left,left,right");
	mygridrp.setColTypes("coro,ed,ed,ed");
	mygridrp.setColSorting("str,str,str,int");
	mygridrp.setColumnColor("white,white,white,white");
	mygridrp.rowsBufferOutSize = 0;
	//mygridco.setEditable('fl');
	mygridrp.setMultiLine(false);
	mygridrp.selmultirows="true";
	//mygridpp.setOnEnterPressedHandler(calcularMontoCausado);
	//mygridpp.setOnEditCellHandler(validarMontoPP);
	<? $cp = new requisitos;
		$objcp = $cp->get_all($conn,0,100);
	?>
	<?=helpers::combogrid($objcp, 0, 'id' , 'nombre', 'Seleccione', 'mygridrp')?>
	
	//INICIA GRID//
	mygridrp.init();
	
}
var oculto="none";
var visible="inline";
var aux = 0;
function CargarGrid(id){
var JsonAux;

	var url = 'json.php';
	var pars = 'op=req_prov_busca&id=' + id;
		mygridrp.clearSelection();
		mygridrp.clearAll();
		
	var myAjax = new Ajax.Request(
		url, 
		{
			method: 'get', 
			parameters: pars,
			onComplete: function(peticion){
				var fecha;
				var jsonData = eval('(' + peticion.responseText + ')');
				i=0;
				while (i<jsonData.length){
					//alert(jsonData[i]['fecha_vcto']);
					if(!isNaN(jsonData[i]['fecha_vcto'])){
						//alert("entro");
						fecha = '';
						} else {
							fecha = mostrarFecha(jsonData[i]['fecha_vcto']);
							}
					mygridrp.addRow(i,jsonData[i]['id_requisitos']+","+mostrarFecha(jsonData[i]['fecha_emi'])+","+fecha+","+jsonData[i]['prorroga']);
					i++;
				}
				
			}
		}	
	)
}

function AgregarRP(){

	if ($('requisitos').value =="0"){
		
		alert("Primero debe Seleccionar un Requisito.");
		return false;
	}else if ($('busca_fecha_desde').value==''){
			
		alert("Primero debe Seleccionar una Fecha de Entrega");
		return false;
			
	}else if ($('busca_fecha_hasta').value=='' && aux==0){
			
		alert("Primero debe seleccionar una fecha de Expiracion");
		return false;
			
	}else if ($('prorroga').value==''){
				
		alert("Primero debe ingresar el periodo de la prorroga");
		return false;
				
	} else{
	
				
		for(j=0;j<i;j++){
		
			if(mygridrp.getRowIndex(j)!=-1){
				
				if (mygridrp.cells(j,'0').getValue() == $('requisitos').value){
						
					alert('Este requisito ya ha sido seleccionado, por favor seleccione otro');
					return false;

				}
			}
		}	
			
		
		
		mygridrp.addRow(i,$('requisitos').value+","+$('busca_fecha_desde').value+","+$('busca_fecha_hasta').value+","+parseInt($('prorroga').value));
		i++;
		
	}
}

	function EliminarRP(){
	mygridrp.deleteRow(mygridrp.getSelectedId());
	
}

	function Guardar()
	{
		var JsonAux,requisito=new Array;
			mygridrp.clearSelection()
			
			for(j=0;j<i;j++)
			{
				if((mygridrp.getRowIndex(j)!= -1))
				{
					requisito[j] = new Array;
					requisito[j][0]= mygridrp.cells(j,0).getValue();
					requisito[j][1]= mygridrp.cells(j,1).getValue();
					requisito[j][2]= mygridrp.cells(j,2).getValue();
					requisito[j][3]= mygridrp.cells(j,3).getValue();	
				}
			}
			JsonAux={"requisito":requisito};
			$("requisito_prov").value=JsonAux.toJSONString(); 
	}
	
	function traeVencido(id) {
		var url = 'json.php';
		var pars = 'op=busca_vencido&id=' + id;
		
		var myAjax = new Ajax.Request(
					url, 
					{
					method: 'get', 
					parameters: pars,
					onComplete: function(peticion){
						var jsonData = eval('(' + peticion.responseText + ')');
						if (jsonData == undefined) { return }
						$('vencido').value = jsonData['vencido'];
						if($('vencido').value=='f'){
							aux = 1;
							$('prorroga').value = '0';
							$('busca_fecha_hasta').value = '';
							$('busca_fecha_desde').value = '';
							document.getElementById('muestra_calendario').style.display=oculto;
						} else {
							document.getElementById('muestra_calendario').style.display=visible;
							$('prorroga').value = '';
							$('busca_fecha_hasta').value = '';
							$('busca_fecha_desde').value = '';
							aux = 0;
							}
						
				}
			}
		);
		}
</script>