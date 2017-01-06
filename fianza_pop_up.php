<?php 
require("comun/ini.php");
require('comun/header_popup.php');
?>
<script language="javascript" type="text/javascript">
	function Cerrar() {
		window.close();
	}
</script>
<?
$aCobra= new contrato_obras;
if ($_REQUEST['accion']=='Agregar'){
	
	$aCobra->add_fianza($conn, $_REQUEST['idcontrato'], $_REQUEST['tipfina'], $_REQUEST['num_contrato'], $_REQUEST['porc'], guardafecha($_REQUEST['busca_fecha_desde']), guardafecha($_REQUEST['busca_fecha_hasta']));?>
	<script>
		Cerrar();
	</script>
<? } else 	
	if ($_REQUEST['accion']=='Actualizar'){
	$aCobra->set_fianza($conn, $_REQUEST['idcontrato'], $_REQUEST['tipfina'], $_REQUEST['num_contrato'], $_REQUEST['porc'], guardafecha($_REQUEST['busca_fecha_desde']), guardafecha($_REQUEST['busca_fecha_hasta']));?>
	<script>
		Cerrar();
	</script>
<? }

$aCobra->get_fianza($conn, $_REQUEST['idcontrato']); 

$aObra = new obras;
$aObra->get($conn, $_REQUEST['idobra'], $escEnEje);
$desc_obra= $aObra->descripcion;
$oProvee= new proveedores;
$oProvee->get($conn, $_REQUEST['idempre']);
$nomb= $oProvee->nombre." RIF: ".$oProvee->rif


?>
<script language="javascript" type="text/javascript">
	function calcular(evento){
		//alert(evento);
		var porcentaje = 0;
		var monto=0;
		var porc=0;
		monto= usaFloat($('monto').value);
		porc= usaFloat($('porc').value);
		if (!evento) var evento = window.event;
		if (evento.keyCode) code = evento.keyCode;
			else if (evento.which) code = evento.which;
		if (code==13) {
			porcentaje= monto * (porc / 100);
			$('mon_por').value = muestraFloat(porcentaje);	
		}
	}
	
	function calculafecha(){
	var tempini = $('busca_fecha_desde').value;
	var tempfin = $('busca_fecha_hasta').value;
	trozosini=tempini.split("/");
	trozosfin=tempfin.split("/");
		var diaini= trozosini[0];
		var mesini=	trozosini[1];
		var anoini= trozosini[2];
		var diafin= parseInt(trozosfin[0]);
		var mesfin=	trozosfin[1];
		var anofin= parseInt(trozosfin[2]);
		/*alert("este: "+diaini);
		alert("aqui: "+diafin);*/
	var fechaini = new Date(mesini+"/"+diaini+"/"+anoini);//formato mes dia año
	var fechaf = new Date(mesfin+"/"+diafin+"/"+anofin);//formato mes dia año
	var tiempoRestante = fechaf.getTime() - fechaini.getTime(); //tiempo en milisegundos
	var diaspermiso = Math.floor(tiempoRestante / (1000 * 60 * 60 * 24)); //pasamos los milisegundos a dias
diaspermiso = diaspermiso + 1 ;
	$('dias').value= parseFloat(diaspermiso);	
}

	
	
</script>
<form name="form1" method="post" >
<table border="0" width="310px" align="left">
<input type="hidden" name="monto" id="monto" value="<?=$_REQUEST['montotot']?>">
<tr>
	<td colspan="2" align="center"><strong>Contrato de Fianza</strong></td>
</tr>
<tr>
	<td colspan="2"><hr /></td>
</tr>
<tr>
	<td align="left" width="80">Obra:</td>
	<td align="left"><? echo($desc_obra);?></td>
</tr>
<tr>
	<td align="left">Tipo de Fianza:</td>
	<td align="left"><?= helpers::combo_ue_cp($conn, 
								'tipfina', 
								$aCobra->tip_fianza,
								'',
								'',
								'',
								'',
								"",
								"SELECT * FROM tipos_fianzas ")?>
								<span class="errormsg" id="error_tipo">*</span>
								<?=$validator->show("error_tipo")?>
								</td>
</tr>
<tr>
	<td align="left">Empresa Afianzadora:</td>
	<td align="left"><? echo($oProvee->nombre);?></td>
</tr>
<tr>
	<td align="left">Rif:</td>
	<td align="left"><? echo($oProvee->rif);?></td>
</tr>
<tr>
	<td align="left"># Contrato de Fianza:</td>
	<td align="left"><input type="text" name="num_contrato" id="num_contrato" style="width:100px" value="<?=$aCobra->num_contrato?>">
	<span class="errormsg" id="error_contr">*</span>
	<?=$validator->show("error_contr")?>
	</td>
</tr>
<tr>
	<td align="left">% Fianza:</td>
	<td align="left"><input type="text" name="porc" id="porc" style="width:30px" onKeyPress="calcular(event);" value="<?=$aCobra->porc_fianza?>" >
	<span class="errormsg" id="error_porc">*</span>
	<?=$validator->show("error_porc")?>
	</td>
</tr>
<tr>
	<td align="left">Total Fianza:</td>
	<td align="left"><input type="text" name="mon_por" id="mon_por" style="width:70px" readonly ></td>
</tr>
<tr>
	<td align="left">Fecha Inicio Contrato:
							
						</td>
	  <td><div align="left" style="width:190px"><input style="width:100px"  type="text" name="busca_fecha_desde" id="busca_fecha_desde" value="<?=muestrafecha($aCobra->fecha_ini)?>" readonly/>
							<a href="#" id="boton_busca_fecha_desde" onclick="return false;">
								<img border="0" src="images/calendarA.png" width="20" height="20" />
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
								inputField        : "busca_fecha_desde",
								button            : "boton_busca_fecha_desde",
								ifFormat          : "%d/%m/%Y",
								daFormat          : "%Y/%m/%d",
								align             : "Br"
							 });
							 
						</script>
						<span class="errormsg" id="error_fini">*</span>
						<?=$validator->show("error_fini")?>
						</div>
						</td>
</tr>
<tr>
	<td align="left">Fecha Termino Contrato:
							
						</td>
	  <td><div align="left" style="width:190px"><input style="width:100px"  type="text" name="busca_fecha_hasta" id="busca_fecha_hasta" value="<?=muestrafecha($aCobra->fecha_fin)?>" onchange="calculafecha();" />
							<a href="#" id="boton_busca_fecha_hasta" onclick="return false;">
								<img border="0" src="images/calendarA.png" width="20" height="20" />
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
								inputField        : "busca_fecha_hasta",
								button            : "boton_busca_fecha_hasta",
								ifFormat          : "%d/%m/%Y",
								daFormat          : "%Y/%m/%d",
								align             : "Br"
							 });
						</script>
						<span class="errormsg" id="error_ffin">*</span>
						<?=$validator->show("error_ffin")?>
						</div>
						</td>
</tr>
<tr>
	<td align="left">Dias Continuos</td>
	<td align="left"><input type="text" name="dias" id="dias" readonly style="width:40" />&nbsp;Dias</td>
</tr>
<tr>
	<td colspan="2"><hr /></td>
</tr>
<? if ($_REQUEST['status']!=3){ ?>
<tr>
	<td colspan="2" align="center"><input type="button" name="aceptar" value="<?= $_REQUEST['hacer']?>" onclick="<?= $validator->validate() ?>; "/>
	<input name="accion" id="accion" type="hidden" value="<?= $_REQUEST['hacer']?>" /></td>
</tr>
<? } ?>
</table>
</form>
<?
$validator->create_message("error_tipo", "tipfina", "*");
$validator->create_message("error_contr", "num_contrato", "*");
$validator->create_message("error_porc", "porc", "*");
$validator->create_message("error_fini", "busca_fecha_desde", "*");
$validator->create_message("error_ffin", "busca_fecha_hasta", "*");
$validator->print_script();
 require('comun/footer_popup.php');?>