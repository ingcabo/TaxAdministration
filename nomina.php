<?
require ("comun/ini.php");
// Creando el objeto nomina
$oNomina = new nomina;
$accion = $_REQUEST['accion'];
if($accion == 'Guardar'){
	$oNomina->add($conn, 
										$_POST['descripcion'],
										$_POST['observaciones'],
										$_POST['tipdoc'],
										$_POST['nro_ref'],
										$_POST['unidad_ejecutora'],
										$usuario->id,
										guardafecha($_POST['fecha']),
										guardafecha($_POST['fecha_pago']),
										$_POST['proveedores'],
										$_POST['idParCat'],
										$_REQUEST['categorias_programaticas'],
										$_REQUEST['partidas_presupuestarias'],
										$_REQUEST['monto']);
}elseif($accion == 'Aprobar'){
	$oNomina->aprobar($conn, 
									$_POST['id'], 
									$usuario->id,
									$_POST['unidad_ejecutora'],
									date("Y"),
									$_POST['descripcion'],
									$_POST['tipdoc'],
									$_POST['nro_ref'],
									guardafecha($_POST['fecha']),
									'1',
									$_POST['proveedores'],
									$_POST['idParCat'],
									$_POST['categorias_programaticas'],
									$_POST['partidas_presupuestarias'],
									$_POST['monto']);
}elseif($accion == 'Actualizar'){
	$oNomina->set($conn, 
									$_POST['id'], 
									$_POST['descripcion'],
									$_POST['observaciones'],
									$_POST['tipdoc'],
									$_POST['nro_ref'],
									$_POST['unidad_ejecutora'],
									guardafecha($_POST['fecha']),
									guardafecha($_POST['fecha_pago']),
									$_POST['proveedores'],
									$_POST['idParCat'],
									$_REQUEST['categorias_programaticas'],
									$_REQUEST['partidas_presupuestarias'],
									$_REQUEST['monto']);
}elseif($accion == 'del'){
	$oNomina->del($conn, $_POST['id']);
}
$msj = $oNomina->msj; // lleno esta variable con el mensaje de la operacion llevada a cabo
$cNomina=$oNomina->get_all($conn, $escEnEje);
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? } ?>
<br />
<span class="titulo_maestro">N&oacute;mina</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />
<div>
<fieldset id="buscador">
	<legend>Buscar:</legend>
	<table>
		<tr>
			<td colspan="3">Unidad Ejecutora</td>
		</tr>
		<tr>
			<td colspan="3"><?=helpers::combo_ue_cp($conn,'busca_ue','','','','','','',
			"SELECT DISTINCT id, descripcion FROM unidades_ejecutoras")?></td>
		</tr>
		<tr>
			<td>Proveedor</td>
			<td>Descripci&oacute;n</td>
		</tr>
		<tr>
			<td>
			<?=helpers::combo_ue_cp($conn, 'busca_proveedores','','','','','','',
			"SELECT id, rif||' - '||nombre AS descripcion FROM proveedores")?></td>
			<td><input style="width:300px" type="text" name="busca_descripcion" id="busca_descripcion" /></td>
		</tr>
		<tr>
			<td>N&ordm; de Documento</td>
			<td colspan="2">
				<table>
					<tr>
						<td style="width:125px">Desde</td>
						<td>Hasta</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td><input style="width:100px" type="text" name="busca_nrodoc" id="busca_nrodoc" /></td>
			<td colspan="2">
				<table>
					<tr>
						<td>
							<input style="width:100px"  type="text" name="busca_fecha_desde" id="busca_fecha_desde" 
							onchange="validafecha(this);"/>
						</td>
						<td>
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
						</td>
						
						<td>
							<input style="width:100px" type="text" name="busca_fecha_hasta" id="busca_fecha_hasta"
							onchange="validafecha(this); "/>
						</td>
						<td>
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
						</td>
					</tr>
					</table>
			</td>
		</tr>
	</table>
</fieldset>
</div>
<br />
<div style="margin-bottom:10px" id="busqueda"><div>
<br />
<div style="height:40px;padding-top:10px;">
<p id="cargando" style="display:none;margin-top:0px;">
  <img alt="Cargando" src="images/loading.gif" /> Cargando...
</p>
</div>
<script type="text/javascript">
/***************       Seccion de las Partidas ******************************/
function addTR() {
	var x = $('tablita').insertRow($('tablita').rows.length);
	var i = $('tablita').rows.length;
	var y1=x.insertCell(0);
	var y2=x.insertCell(1);
	var y3=x.insertCell(2);
	var y4=x.insertCell(3);
	var y5=x.insertCell(4);
	var y6=x.insertCell(5);
	var contenedor;
	y1.innerHTML= "Categoria:";
	var cp = $('categorias_programaticas_1').cloneNode(true);
	cp.setAttribute('id', 'categorias_programaticas_' + i);
	cp.setAttribute('onchange', 'traeParPreDesdeUpdater(this.value, ' + i + ')' );
	cp.value = 0;
	contenedor = Builder.node('div', {id:'cont_categorias_programaticas_' + i});
	contenedor.appendChild(cp);
	y2.appendChild(contenedor);
	y3.innerHTML= "Partida Presupuestaria:";
	var pp = $('partidas_presupuestarias_1').cloneNode(true);
	pp.value = 0;
	pp.setAttribute('id', 'partidas_presupuestarias_' + i);
	contenedor = Builder.node('div', {id:'cont_partidas_presupuestarias_' + i});
	contenedor.appendChild(pp);
	y4.appendChild(contenedor);
	y5.innerHTML= "Monto:";
	var cppp = $('cppp_1').cloneNode(false)
	cppp.setAttribute('id', 'cppp_' + i);
	cppp.value = 0;
	y6.appendChild(cppp)
	var idParCat = $('idParCat_1').cloneNode(false)
	idParCat.setAttribute('id', 'idParCat_' + i);
	idParCat.value = 0;
	y6.appendChild(idParCat)
	var nuevoDisponibleParCat = $('nuevoDisponibleParCat_1').cloneNode(false)
	nuevoDisponibleParCat.setAttribute('id', 'nuevoDisponibleParCat_' + i);
	nuevoDisponibleParCat.value = 0;
	y6.appendChild(nuevoDisponibleParCat);
	var m = $('monto_1').cloneNode(false)
	m.setAttribute('id', 'monto_' + i);
	m.removeAttribute('readonly');
	m.value = '';
	m.onblur = function(){ operacion(this.value, $('nuevoDisponibleParCat_' + i).value, this.id); };
	y6.appendChild(m);
	$('total').value = i;
}

function delTR() {
	if($('tablita').rows.length <= 1){
		$('partidas_presupuestarias_1').value = 0;
		$("cppp_1").value = 0;
		$("monto_1").value = "";
		alert('No puede eliminar mas partidas');
	}else{
		var montoTotal = 0;
		var x = $('tablita').deleteRow($('tablita').rows.length - 1);
		$A(document.getElementsByClassName('montos')).each( function(e){
			montoTotal += parseFloat(usaFloat(e.value));
		});
		$('monto_total_partidas').value = muestraFloat(montoTotal);
	}
}

function traeParPreDesdeUpdater(cp, fila){
	var url = 'updater_selects.php';
	var pars = 'combo=parpreNomina&cp=' + cp + '&fila=' + fila;
	var updater = new Ajax.Updater('cont_partidas_presupuestarias_' + fila, 
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

function traeParCatDesdeXML(cp, pp, nombre){
	var cppp = cp+pp;
	var url = 'xmlTraeParCat.php';
	var fila = getFila(nombre);
	var pars = 'cp=' + cp + '&pp=' + pp + '&pagina=nomina&fila=' + fila;
	//$('xxx').innerHTML = pars;
	var salir = false;
	var listaNodos = document.getElementsByClassName('cppps');
	var seleccionado = false;
	$A(listaNodos).each( function(e){
		if(cppp == e.value){
			alert('Esta partida ya ha sido seleccionada, por favor seleccione otra partida');
			$('partidas_presupuestarias_' + fila).value = 0;
			seleccionado = true;
			salir = true;
		}
	} );
	if(!seleccionado)
		$('cppp_' + fila).value = cppp;
	if(!salir){
		var myAjax = new Ajax.Request(
			url, 
			{
				method: 'get', 
				parameters: pars,
				onComplete: function(peticion){
					var doc = new XMLDoc(peticion.responseXML.documentElement);
					var hash = doc.asHash();
					hash.parcat.each ( function(e) { 
						$('nuevoDisponibleParCat_' + fila).value = e.disponible; 
						$('idParCat_' + fila).value = e.idParCat; 
					});
				}
			}
		);
	}
}

function operacion(monto, disponible, id_monto){
	//alert('m:' +monto+'d:' +disponible+'id:' +id_monto)
	var montoTotal = 0;
	monto = parseFloat(usaFloat(monto));
	if(monto <= disponible){
		$A(document.getElementsByClassName('montos')).each( function(e){
			montoTotal += parseFloat(usaFloat(e.value));
		});
		$('monto_total_partidas').value = muestraFloat(montoTotal);
	}else{
		alert('El monto es mayor que el disponible en la partida');
		$(id_monto).value = "0,00";
		$(id_monto).focus();
	}
}

function getFila(nombre){
	var aNombre = nombre.split("_");
	return aNombre[aNombre.length - 1];
}


function actapr(elemento){
	if(elemento.value == 'Actualizar')
		$('accion').value = 'Actualizar';
	else
		$('accion').value = 'Aprobar'; 
	 validate(); 
}

function traeProveedorDesdeXML(id){
	var url = 'xmlTraeProveedor.php';
	var pars = 'id=' + id;
	var myAjax = new Ajax.Request(
		url, 
		{
			method: 'get', 
			parameters: pars,
			onComplete: function(peticion){
				var doc = new XMLDoc(peticion.responseXML.documentElement);
				var hash = doc.asHash();
				hash.proveedor.each ( function(e) { 
					$('id_proveedor').value = id;
					$('nombre').value = e.nombre; 
					$('direccion').value = e.direccion; 
				});
			}
		});
}

/* Metodos utilizados en el buscador */
function busca(id_ue, id_proveedor, descripcion, fecha_desde, fecha_hasta, nrodoc){
	var url = 'updater_busca_nomina.php';
	var pars = '&id_ue=' + id_ue + '&id_proveedor=' + id_proveedor+ '&descripcion=' + descripcion;
	pars += '&nrodoc=' + nrodoc + '&fecha_desde=' + fecha_desde+ '&fecha_hasta=' + fecha_hasta;
	//alert(pars);
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

Event.observe('busca_ue', "change", function () { 
	busca($F('busca_ue'), 
	$F('busca_proveedores'), 
	$F('busca_descripcion'), 
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta'),
	$F('busca_nrodoc')); 
});
Event.observe('busca_proveedores', "change", function () { 
	busca($F('busca_ue'), 
	$F('busca_proveedores'), 
	$F('busca_descripcion'), 
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta'),
	$F('busca_nrodoc')); 
});
Event.observe('busca_descripcion', "keyup", function () { 
	busca($F('busca_ue'), 
	$F('busca_proveedores'), 
	$F('busca_descripcion'), 
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta'),
	$F('busca_nrodoc')); 
});
Event.observe('busca_nrodoc', "keyup", function () { 
	busca($F('busca_ue'), 
	$F('busca_proveedores'), 
	$F('busca_descripcion'), 
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta'),
	$F('busca_nrodoc')); 
});

function validafecha(fecha){
	var upper = 31;
	if(/^(\d{2})\/(\d{2})\/(\d{4})$/.test(fecha.value)) { // dd/mm/yyyy
		if(RegExp.$2 == '02') upper = 29;
		if((RegExp.$1 <= upper) && (RegExp.$2 <= 12)) {
			busca($F('busca_ue'), 
			$F('busca_proveedores'), 
			$F('busca_descripcion'), 
			$F('busca_fecha_desde'), 
			$F('busca_fecha_hasta'),
			$F('busca_nrodoc')); 
		} else {
			alert("Fecha incorrecta");
			fecha.value = "";
		}
	}else if(fecha.value != '') {
		alert("Fecha incorrecta");
		fecha.value = "";
	}
}
</script>
<div id="xxx"></div>
<?
$validator->create_message("error_tipo_doc", "tipdoc", "*");
$validator->create_message("error_fecha_pago", "fecha_pago", "*");
$validator->create_message("error_desc", "descripcion", "*");
$validator->create_message("error_nro_ref", "nro_ref", "*");
$validator->create_message("error_prov", "proveedores", "*");
$validator->create_message("error_ue", "unidad_ejecutora", "*");
/*
$validator->create_message("error_prov", "proveedores", "(*)");
$validator->create_message("error_reg_prov", "id_proveedor", "(*)");
$validator->create_message("error_cod_contraloria", "cod_contraloria", "(*)");
$validator->create_message("error_nro_cotizacion", "nro_cotizacion", "(*)");
*/
$validator->print_script();
require ("comun/footer.php");
?>
