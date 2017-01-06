<?
require ("comun/ini.php");
$nrodoc = $_GET['nrodoc'];
$oMovPre = new movimientos_presupuestarios;
$oMovPre->get($conn, $nrodoc);
// el porcentaje de retencion es 75% si el tipo de contribuyente es ordinario, sino su valor es 100%
$porcret = $oMovPre->tipo_contribuyente == 'ORDINARIO' ? '75': '100';
// Creando el objeto cargos
$oCargos = new cargos();
$accion = $_REQUEST['accion'];
if($accion == 'Guardar'){
	if($oCargos->add($conn, $_POST['id_nuevo'], $_POST['descripcion']))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
}elseif($accion == 'Actualizar'){
	if($oCargos->set($conn, $_POST['id_nuevo'], $_POST['id'], $_POST['descripcion']))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}elseif($accion == 'del'){
	if($oCargos->del($conn, $_POST['id']))
		$msj = REG_DEL_OK;
	else
		$msj = ERROR;
}

$cCargos=$oCargos->get_all($conn);
require ("comun/header_popup.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:none;"><?=$msj?></div><? } ?>
<a id="linkAgrega" href="#" onclick="addTR(); return false;">Agregar una factura [+]</a>
<a id="linkElimina" href="#" onclick="delTR(); return false;">Eliminar la &uacute;ltima factura [ - ]</a>
<br />
<form method="post" name="form1">
  <fieldset><legend>Facturas Relacionadas</legend>
  <table id="tablita" style="text-align: left; width: 100%;" border="0" cellpadding="1" cellspacing="0">
    <tbody>
      <tr>
        <td>N&ordm; Factura</td>
        <td>N&ordm; Control</td>
        <td>Fecha</td>
        <td>IVA</td>
        <td>Monto Documento</td>
        <td>Base Imponible</td>
        <td>Monto Exento</td>
        <td>IVA</td>
        <td>IVA Retenido</td>
      </tr>
      <tr>
        <td><input class="nrofac" id="nrofac_1" name="nrofac[]"></td>
        <td><input class="nrofac" id="nroctrl_1" name="nroctrl[]"></td>
        <td><input class="campoFecha" id="fecha_1" name="fecha[]"></td>
        <td><input class="iva" id="iva_1" value="14" name="iva[]"></td>
        <td><input onkeypress="return(formatoNumero (this,event));" onblur="valorBase($('mntdoc_1'), $('mntex_1'), $('iva_1') ); valorIva($('baseimp_1'), $('iva_1')); valorRetiva($('baseimp_1'), $('iva_1')); sumaTotal();" class="monto suma" id="mntdoc_1" name="mntdoc[]"></td>
        <td><input readonly="readonly" class="monto" id="baseimp_1" name="baseimp[]"></td>
        <td><input onkeypress="return(formatoNumero (this,event));" value="0" class="monto" id="mntex_1" name="mntex[]"></td>
        <td><input readonly="readonly" class="monto" id="ivamnt_1" name="ivamnt[]"></td>
        <td><input readonly="readonly" class="monto" id="ivaret_1" name="ivaret[]"></td>
      </tr>
      <tr>
        <td colspan="7" style="text-align:right">Total a Facturar:</td>
        <td colspan="2" style="text-align:right"><input class="monto" style="width:200px" id="total" name="total"></td>
	  </tr>
    </tbody>
  </table>
  <br>
  </fieldset>
<input style="float:right" name="boton" type="button" value="Guardar"  />
<input name="porcret" id="porcret" type="hidden" value="<?=$porcret?>"  />
</form>
<script type="text/javascript">
function addTR() {
	var x = $('tablita').insertRow($('tablita').rows.length - 1);
	var i = $('tablita').rows.length - 2;
	var y1=x.insertCell(0);
	var y2=x.insertCell(1);
	var y3=x.insertCell(2);
	var y4=x.insertCell(3);
	var y5=x.insertCell(4);
	var y6=x.insertCell(5);
	var y7=x.insertCell(6);
	var y8=x.insertCell(7);
	var y9=x.insertCell(8);
	var nf = $('nrofac_1').cloneNode(true);
	nf.id = 'nrofac_' + i;
	nf.value = '';
	y1.appendChild(nf);
	var nc = $('nroctrl_1').cloneNode(true);
	nc.id = 'nroctrl_' + i;
	nc.value = '';
	y2.appendChild(nc);
	var fecha = $('fecha_1').cloneNode(true);
	fecha.id = 'fecha_' + i;
	fecha.value = '';
	y3.appendChild(fecha);
	var iva = $('iva_1').cloneNode(true);
	iva.id = 'iva_' + i;
	iva.value = '14';
	y4.appendChild(iva);
	var md = $('mntdoc_1').cloneNode(false)
	md.id = 'mntdoc_' + i;
	md.value = '';
	md.removeAttribute('onchange');
	function operaciones(){ 
		valorBase($('mntdoc_' + i), $('mntex_' + i), $('iva_' + i) ); 
		valorIva($('baseimp_' + i), $('iva_' + i)); 
		valorRetiva($('baseimp_' + i), $('iva_' + i));
		sumaTotal();
	};
	Try.these(
		function(){ md.addEventListener('blur', operaciones, false);},
		function(){ md.attachEvent('onblur', operaciones);}
 	);
	y5.appendChild(md);
	var bi = $('baseimp_1').cloneNode(true);
	bi.id = 'baseimp_' + i;
	bi.value = '';
	y6.appendChild(bi);
	var me = $('mntex_1').cloneNode(true);
	me.id = 'mntex_' + i;
	me.value = '0';
	y7.appendChild(me);
	var im = $('ivamnt_1').cloneNode(true);
	im.id = 'ivamnt_' + i;
	im.value = '';
	y8.appendChild(im);
	var ir = $('ivaret_1').cloneNode(true);
	ir.id = 'ivaret_' + i;
	ir.value = '';
	y9.appendChild(ir);
}

function delTR() {
	if($('tablita').rows.length <= 3){
		$$('#tablita input').each( function(e){ e.value = ''; });
		alert('No puede eliminar mas facturas');
	}else{
		var montoTotal = 0;
		var x = $('tablita').deleteRow($('tablita').rows.length - 2);
		$A(document.getElementsByTagName('mntdoc')).each( function(e){
			alert(e.value);
			montoTotal += parseFloat(usaFloat(e.value));
		});
		$('monto_total').value = muestraFloat(montoTotal);
	}
}

function valorBase(nodoMntdoc, nodoMntexe, nodoIva){
	var fila = getFila(nodoMntdoc.id);
	var mntdoc = parseFloat(usaFloat(nodoMntdoc.value));
	var mntexe = parseFloat(usaFloat(nodoMntexe.value));
	var r = ((mntdoc - mntexe) * 100 ) / (100 + parseInt(nodoIva.value));
	r = isNaN(r) ? '0' : muestraFloat( r );
	$('baseimp_' + fila).value = r;
}

function valorIva(nodoBaseimp, nodoIva){
	var fila = getFila(nodoBaseimp.id);
	var baseimp = parseFloat(usaFloat(nodoBaseimp.value));
	var r = baseimp *  ( parseInt(nodoIva.value) / 100);
	r = isNaN(r) ? '0' : muestraFloat( r );
	$('ivamnt_' + fila).value = r;
}

function valorRetiva(nodoBaseimp, nodoIva){
	var fila = getFila(nodoBaseimp.id);
	var baseimp = parseFloat(usaFloat(nodoBaseimp.value));
	var r = baseimp *  ( parseInt(nodoIva.value) / 100) * parseInt($F('porcret')) / 100;
	r = isNaN(r) ? '0' : muestraFloat( r );
	$('ivaret_' + fila).value = r;
}

function sumaTotal(){
	var total = 0;
	$A(document.getElementsByClassName('suma', 'tablita')).each( function(el){
		total += parseFloat(usaFloat(el.value));
	});
	$('total').value  = muestraFloat(total);
}
</script>
<?
require ("comun/footer_popup.php");
?>