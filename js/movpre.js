/*************************************************
***       Utilizado en la pagina:              ***
***       movimientos_presupuestarios.php      ***
*************************************************/
/**********
   NOTA
***********
Todos los valores numericos que se guarden en algun input tienen el formato 1.000.000,22, 
esto se lleva a cabo utilizando la funcion muestraFloat, ej:
var x = 1000000.22;
debe guardarse en el input 'xx' de esta manera
$('xx').value = muestraFloat(x); <-- el valor que tendra el input es 1.000.000,22
indistintamente si el input es tipo 'text' o 'hidden', se guarda con formato visible para usuario para mantener
una consistencia en la manera de trabajo y minimizar errores.
si necesito obtener nuevamente ese valor y manipularlo matematicamente debo utilizar la funcion usaFloat() 
de la siguiente manera...
--
var y = $('xx').value; <-- y tendra el valor 1.000.000,22
var z = 1;
y = usaFloat(y); <- ahora y vale 1000000.00 y puede ser manupulado matematicamente
z += y; // z vale ahora 1000001.00
--
*************************************************/

function addTR(){ 
	if($('unidad_ejecutora').value == 0){
		alert('Debe seleccionar una Unidad Ejecutora antes de agregar Partidas');
	}else{
		var contenedor;
		var x=$('tablita').insertRow($('tablita').rows.length);
		var i = $('tablita').rows.length;
		var y1=x.insertCell(0)
		var y2=x.insertCell(1)
		var y3=x.insertCell(2)
		var y4=x.insertCell(3)
		var y5=x.insertCell(4)
		var y6=x.insertCell(5)
		y1.innerHTML= "Categoria:"
		var cp = $('categorias_programaticas_0').cloneNode(true);
		cp.setAttribute('id', 'categorias_programaticas_' + i);
		cp.onchange = function(){ traeParPreDesdeUpdater(this.value, i); };
		contenedor = Builder.node('div', {id:'cont_categorias_programaticas_' + i});
		contenedor.appendChild(cp);
		y2.appendChild(contenedor);
		y3.innerHTML= "Partida Presupuestaria:"
		var pp = $('partidas_presupuestarias_0').cloneNode(true)
		pp.setAttribute('id', 'partidas_presupuestarias_' + i);
		contenedor = Builder.node('div', {id:'cont_partidas_presupuestarias_' + i});
		contenedor.appendChild(pp);
		y4.appendChild(contenedor)
		y5.innerHTML= "Monto:"
		var m = $('monto_0').cloneNode(false)
		m.setAttribute('id', 'monto_' + i);
		m.removeAttribute('readonly');
		m.value = '';
		m.onblur = function(){ operacion(this.value, $('momentos_presupuestarios').value, $('categorias_programaticas_'+i).value, $('partidas_presupuestarias_'+i).value, this.id, this.className) };
		m.onkeypress = function() { return formatoNumero(this,event); } ;
		y6.appendChild(m)
		var cppp = $('cppp_0').cloneNode(false)
		cppp.setAttribute('id', 'cppp_' + i);
		cppp.value = 0;
		y6.appendChild(cppp)
		var max = $('max_0').cloneNode(false)
		max.setAttribute('id', 'max_' + i);
		y6.appendChild(max)
		// este hidden es utilizado para almacenar los aumentos hechos, de manera de no permitir hacer disminuciones
		// con un numero mayor a dichos aumentos
		var aumentos = $('aumentos_0').cloneNode(false)
		aumentos.setAttribute('id', 'aumentos_' + i);
		y6.appendChild(aumentos)
		// necesito estos hidden para actualizar los campos disponible y el otro dependiendo del tipo de movimiento
		// (comprometido, causado, etc) en la tabla relacion_pp_cp
		var nmpc = $('nuevoMontoParCat_0').cloneNode(false)
		nmpc.setAttribute('id', 'nuevoMontoParCat_' + i);
		y6.appendChild(nmpc)
		var ndpc = $('nuevoDisponibleParCat_0').cloneNode(false)
		ndpc.setAttribute('id', 'nuevoDisponibleParCat_' + i);
		y6.appendChild(ndpc)
		var idParCat = $('idParCat_0').cloneNode(false)
		idParCat.setAttribute('id', 'idParCat_' + i);
		y6.appendChild(idParCat)
	}
}
function delTR(){
	if($('tablita').rows.length > 1)
		$('tablita').deleteRow($('tablita').rows.length - 1);
	else{
		$('partidas_presupuestarias_0').value = 0;
		var m = $('monto_0').cloneNode(false)
		m.removeAttribute('readonly');
		m.value = '';
		m.onblur = function(){ operacion(this.value, $('momentos_presupuestarios').value, $('categorias_programaticas_0').value, $('partidas_presupuestarias_0').value, this.id, this.className) };
		m.onkeypress = function() { return(formatoNumero (this, event)); } ;
		$('monto_0').parentNode.replaceChild(m, $('monto_0'));
		alert('Ud no puede eliminar mas partidas');
	}
}

function traeCatProDesdeUpdater(ue){
	var url = 'updater_selects.php';
	var pars = 'combo=catpro&ue=' + ue;
	var updater = new Ajax.Updater('cont_categorias_programaticas_0', 
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

function traeParPreDesdeUpdater(cp, cont){
	var url = 'updater_selects.php';
	var pars = 'combo=parpre&cp=' + cp + '&cont=' + cont;
	var updater = new Ajax.Updater('cont_partidas_presupuestarias_' + cont, 
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
	var pars = 'cp=' + cp + '&pp=' + pp + '&fila=' + fila;
	//alert(pars)
	var salir = false;
	var listaNodos = document.getElementsByClassName('cppps');
	var tieneCppp = true;
	$A(listaNodos).each( function(e){
		if(cppp == e.value){
			alert('Esta partida ya ha sido seleccionada, por favor seleccione otra partida');
			$('partidas_presupuestarias_' + fila).value = 0;
			salir = true;
		}else{
			tieneCppp = false;
		}
	} );
	if(!tieneCppp)
		$('cppp_' +fila).value = cppp;
	if($F('momentos_presupuestarios') == 5){
		var listaNodos = document.getElementsByClassName('arrAumentos');
		$A(listaNodos).each( function(nodo){
			if (nodo.value == cppp){
				alert('No puede hacer una disminucion de una partida a la que se le realizó el aumento')
				$(nombre).value = 0;
				salir = true;
			}
		} );
	}
	if(!salir){
		var myAjax = new Ajax.Request(
			url, 
			{
				method: 'get', 
				parameters: pars,
				onComplete: function(originalRequest){
					var xmlDoc = originalRequest.responseXML;
					var x = xmlDoc.getElementsByTagName('parcat');
					for(j=0;j<x[0].childNodes.length;j++){
						if (x[0].childNodes[j].nodeType != 1) continue;
						var nombre = x[0].childNodes[j].nodeName;
						$(nombre).value = muestraFloat(x[0].childNodes[j].firstChild.nodeValue);
					}
				}
			});
	}
}

function traeMpDesdeXML(mp, nombre){
	var url = 'updater_selects.php';
	var pars = 'combo=tipdoc&mp=' + mp + '&nombre=' + nombre +'&ms='+new Date().getTime();
	var updater = new Ajax.Updater('cont_' + nombre, 
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

function traeNroRefDesdeXML(ue, id_proveedor, tipref){
	var url = 'xmlTraeTipRef.php';
	var momento = parseInt($('momentos_presupuestarios').value)
	switch(momento){
		case 1:
		// nada
		break
		case 2:
			var url = 'updater_selects.php';
			var pars = 'combo=tipref&ue=' + ue + '&status=1&id_proveedor=' + id_proveedor + '&tipref=' + tipref +'&ms='+new Date().getTime();
			var updater = new Ajax.Updater('cont_nroref', 
				url,
				{
					method: 'get',
					parameters: pars,
					asynchronous:true, 
					evalScripts:true,
					onLoading:function(request){Element.show('cargando')}, 
					onComplete:function(request){Element.hide('cargando')}
				}); 
		break
		case 3:
			var url = 'updater_selects.php';
			var pars = 'combo=tipref&ue=' + ue + '&status=2&id_proveedor=' + id_proveedor + '&tipref=' + tipref +'&ms='+new Date().getTime();
			var updater = new Ajax.Updater('cont_nroref', 
				url,
				{
					method: 'get',
					parameters: pars,
					asynchronous:true, 
					evalScripts:true,
					onLoading:function(request){Element.show('cargando')}, 
					onComplete:function(request){Element.hide('cargando')}
				}); 
		break
		case 5:
			var url = 'updater_selects.php';
			var pars = 'combo=tipref&status=5';
			var updater = new Ajax.Updater('cont_nroref', 
				url,
				{
					method: 'get',
					parameters: pars,
					asynchronous:true, 
					evalScripts:true,
					onLoading:function(request){Element.show('cargando')}, 
					onComplete:function(request){Element.hide('cargando')}
				}); 
		break
		default:
			//alert('x')
		break
	}
} 

function setNroDoc(){ // utilizada para setear el numero del tipo de documento
	if($('momentos_presupuestarios').value == 3)
		$('nrodoc').removeAttribute('readonly')
	else
		traeNroDoc();
}

function campoNroDoc(){
	
	if($('momentos_presupuestarios').value == 3 && $('nrodoc').readonly==true )
		$('nrodoc').readonly = false;
//		$('nrodoc').removeAttribute('readonly');
	else if ($('nrodoc').readonly!=true){
		$('nrodoc').value = '';
		$('nrodoc').readonly = false;
	}
	
}

function tipmovpre(id){
	
	//LIMPIO EL CAMPO DEL NRO DE REFERENCIA//
	$('cont_nroref').innerHTML = '<input type="text" style="width: 80px;" name="nroref" id="nroref" value="00000000"/>';
	
	//LIMPIAMOS LOS CAMPOS DE LA PARTIDA PRESUPUESTARIA//
	$('compromisos').value 			= muestraFloat(0);
	$('causados').value 			= muestraFloat(0);
	$('pagados').value 				= muestraFloat(0);
	$('aumentos').value 			= muestraFloat(0);
	$('disminuciones').value		= muestraFloat(0);
	$('presupuesto_original').value	= muestraFloat(0);
	$('disponible').value			= muestraFloat(0);

	//LIMPIAMOS LOS CAMPOS DEL DOCUMENTO//
	$('ppCompromiso').value 		= muestraFloat(0);
	$('ppCausado').value 			= muestraFloat(0);
	$('ppPagado').value 			= muestraFloat(0);
	$('ppAumentos').value 			= muestraFloat(0);
	$('ppDisminuciones').value		= muestraFloat(0);
	
	if (id=='2'){
		
		buildGridPPC();
		$('divagregarpp').style.display='none';
		$('divagregarpp2').style.display='none';
		$('divagregarpp3').style.display='none';
	}else if(id=='3'){
		buildGridPPCO();
		$('divagregarpp').style.display='none';
		$('divagregarpp2').style.display='none';
		$('divagregarpp3').style.display='none';		
	}else{
		buildGridPPCO();
		$('divagregarpp').style.display='inline';
		$('divagregarpp2').style.display='inline';
		$('divagregarpp3').style.display='inline';
		}
	// si el tipo es un aumento o una disminucion desactivo el campo proveedor
	if(id == 4 || id == 5){
		$('proveedores').disabled = true;
	}else{
		$('proveedores').disabled = false;
	}
	
		
}

function traeNroDoc(){
	var url = 'traeNroDoc.php';
	var tipdoc = $F('tipdoc');
	var pars = 'tipdoc=' + tipdoc +'&ms='+new Date().getTime();
	var myAjax = new Ajax.Updater('divNrodoc',
		url, 
		{
			asynchronous:false,
			method: 'get', 
			parameters: pars
		});
}

function actualizaTablita(nroref){
	var momento = parseInt($('momentos_presupuestarios').value);
	switch(momento){
		case 1:
		// nada
		break
		case 2:
			var url = 'updater_divTablita.php';
			var pars = 'id=' + nroref + '&mp=' + momento;
			var compromisoTotal = 0;
			var updater = new Ajax.Updater('divTablita', url,{
				parameters: pars,
				asynchronous:true, 
				evalScripts:true,
				onLoading:function(request){Element.show('cargando')}, 
				onComplete:function(request){Element.hide('cargando')}
			}); 
		break
		case 3:
			var url = 'updater_divTablita.php';
			var pars = 'id=' + nroref + '&mp=' + momento;
			var compromisoTotal = 0;
			var updater = new Ajax.Updater('divTablita', url,{
				parameters: pars,
				asynchronous:true, 
				evalScripts:true,
				onLoading:function(request){Element.show('cargando')}, 
				onComplete:function(request){Element.hide('cargando')}
			}); 
		break
		case 5:
		break
		default:
			alert("Usted debe seleccionar un tipo de movimiento");
		break
	}
}

function operacion(monto, tipo, cp, pp, nombre, nombreClase){
	tipo = parseInt(tipo);
	monto = usaFloat(monto)
	if(isNaN(monto)) monto = 0;
	var nodosMonto = document.getElementsByClassName('montos');
	var montoTotal = 0;
	var fila = getFila(nombre);
	switch(tipo){
		case 1:
			var disponible = usaFloat($('max_' + fila).value);
			if(monto > disponible){
				alert("El monto no puede ser superior al disponible para esta partida")
				$(nombre).value = "";
				$(nombre).focus();
			}else{
				$('disponible').value = muestraFloat(disponible - monto); //cambio el valor del disponible 
				$('nuevoDisponibleParCat_' + fila).value = $('disponible').value;
				$('idParCat_' + fila).value = $('idParCat').value;
				var compromisos = usaFloat($('hCompromisos').value);
				compromisos += monto;
				$('compromisos').value = muestraFloat(compromisos);
				$('nuevoMontoParCat_' + fila).value = $('compromisos').value;
				$A(nodosMonto).each(function(monto){ montoTotal +=  usaFloat(monto.value); });
				$('ppCompromiso').value = muestraFloat(montoTotal);
				$('monto_' + fila).removeAttribute('onblur');
				$('monto_' + fila).setAttribute('readonly', '');
			}
		break
		case 2:
			var comprometido = usaFloat($('max_comp_' + fila).value);
			if(monto > comprometido){
				alert("El monto no puede ser superior al comprometido para esta partida")
				$(nombre).value = '';
				$(nombre).focus();
			}else{
				$('idParCat_' + fila).value = $('idParCat').value; // guardo en un array hidden el id de la tabla relacion_pp_cp
				var causados = usaFloat($('hCausados').value)
				causados += monto;
				$('causados').value = muestraFloat(causados);
				$('nuevoMontoParCat_' + fila).value = $('causados').value // y en otro array hidden lo causado para ese id
				for(i = 0; i < nodosMonto.length; i++) {
					montoTotal += usaFloat(nodosMonto[i].value);
				}
				$('ppCausado').value = muestraFloat(montoTotal);
			}
		break
		case 3:
			var causado = usaFloat($('max_comp_' + fila).value);
			if(monto > causado){
				alert("El monto no puede ser superior al causado para esta partida")
				$(nombre).value = '';
				$(nombre).focus();
			}else{
				$('idParCat_' + fila).value = $('idParCat').value; // guardo en un array hidden el id de la tabla relacion_pp_cp
				var pagados = usaFloat($('hPagados').value)
				pagados += monto;
				$('pagados').value = muestraFloat(pagados);
				$('nuevoMontoParCat_' + fila).value = $('pagados').value // y en otro array hidden lo causado para ese id
				for(i = 0; i < nodosMonto.length; i++) {
					montoTotal += usaFloat(nodosMonto[i].value);
				}
				$('ppPagado').value = muestraFloat(montoTotal);
			}
		break
		case 4:
			var disponible = usaFloat($('hDisponible').value);
			var aumentos = monto;
			disponible += monto; //aumento el valor del disponible 
			$('disponible').value = muestraFloat(disponible); //asigno el nuevo disponible al input
			$('nuevoDisponibleParCat_' + fila).value = $('disponible').value; // guardo el nuevo disponible de la partida en un array
			$('idParCat_' + fila).value = $('idParCat').value;
			$('aumentos').value = muestraFloat(aumentos);
			$('nuevoMontoParCat_' + fila).value = $('aumentos').value
			for(i = 0; i < nodosMonto.length; i++) {
				montoTotal += usaFloat(nodosMonto[i].value);
			}
			$('ppAumentos').value = muestraFloat(montoTotal);
			$('monto_' + fila).removeAttribute('onblur');
			$('monto_' + fila).setAttribute('readonly', '');
			break
		case 5:
			var aumento = usaFloat($('aumentos_0').value);
			var disponible = usaFloat($('hDisponible').value);
			if(isNaN(disponible))
				disponible = 0;
			if(monto > aumento){
				alert("El monto no puede ser superior al aumentado")
				$(nombre).value = '';
				$(nombre).focus();
			}else if(monto > disponible){
				alert("El monto no puede ser superior al disponible")
				$(nombre).value = '';
				$(nombre).focus();
			}else{
				$('idParCat_' + fila).value = $('idParCat').value; // guardo en un array hidden el id de la tabla relacion_pp_cp
				var disminucion = usaFloat($('hDisminuciones').value);
				disminucion += monto;
				aumento -= disminucion;
				$('aumentos_0').value = muestraFloat(aumento);
				$('disminuciones').value = muestraFloat(disminucion);
				disponible -= monto;
				$('disponible').value = muestraFloat(disponible);
				$('nuevoDisponibleParCat_' + fila).value = $('disponible').value; // guardo el nuevo disponible de la partida en un array
				$('nuevoMontoParCat_' + fila).value = $('disminuciones').value // y en otro array hidden lo causado para ese id
				for(i = 0; i < nodosMonto.length; i++) {
					montoTotal += usaFloat(nodosMonto[i].value);
				}
				if(isNaN(montoTotal))
					montoTotal = 0;
				$('ppDisminuciones').value = muestraFloat(montoTotal);
				if(monto != ''){
					$('monto_' + fila).removeAttribute('onblur');
					$('monto_' + fila).setAttribute('readonly', '');
				}
			}
		break
		default:
			alert("Usted debe seleccionar un tipo de movimiento");
		break
	}
}

function getFila(nombre){
	var aNombre = nombre.split("_");
	return aNombre[aNombre.length - 1];
}

function suma(monto){
	var momento = parseInt($F('momentos_presupuestarios'))
	switch(momento){
		case 1:
		break
		case 2:
			$('ppCompromiso').value = muestraFloat($F("totalComprometido"));
		break
		case 3:
			$('ppCompromiso').value = muestraFloat($F("totalComprometido"));
			$('ppCausado').value = muestraFloat($F("totalCausado"));
		break
	}
}

// el doc de aumentos seleccionado en la referencia tiene montos en una categoria y partida especifica
// esta funcion trae de la bd el id de la categoria y el id de la partida concatenados y los guarda en un array 
// de manera de poder chequear que no se haga una disminucion de una partida ya utilizada para hacer el aumento
function chequeaMontos(nrodoc){
	var url = 'json.php';
	var pars = 'op=buscaAumentos&nrodoc=' + nrodoc;
	var myAjax = new Ajax.Request(
		url, 
		{
			method: 'get', 
			parameters: pars,
			onComplete: function(respuesta){
				var jsonData = eval('(' + respuesta.responseText + ')');
				if (jsonData == undefined) { return }
				$('aumentos_0').value = jsonData;
			
			}
		});
}
// validamos el form
function valida(){
	var url = 'traeNroDoc.php';
	var tipdoc = $F('tipdoc');
	var pars = 'tipdoc=' + tipdoc +'&ms='+new Date().getTime();
	var myAjax = new Ajax.Updater('divNrodoc',
		url, 
		{
			method: 'get', 
			parameters: pars,
			onComplete:function(respuesta){
				
				var momento = parseInt($F('momentos_presupuestarios'));
				var submit = false;
				switch(momento){
					case 1:
						submit = true;
					break
					case 2:
						if($F('ppCompromiso') != '0')
							submit = true;
						else
							alert('Debe causar la partida presupuestaria');
					break
					case 3:
						if($F('ppCompromiso') != '0')
							submit = true;
						else
							alert('Debe pagar la partida presupuestaria');
					break
					case 4:
						submit = true;
					break
					case 5:
						if(parseInt($('aumentos_0').value) !== parseInt($('total_grid').value)){
							alert('Debe disminuir la misma cantidad aumentada en el documento referencia');
						}else if($('nroref').value != 0)
							submit = true;
						else
							alert('No puede hacer una disminución si no tiene Número de Referencia');
					break
					default:
						alert('Debe seleccionar un tipo de movimiento');
					break
				}
				
				var nrodoc = respuesta.responseText;
				if($F('tipdoc') == 0){
					alert('Debe seleccionar un tipo de documento');
					submit = false;
				}
				if(submit){
					var ok = confirm('Usted esta a punto de guardar el Documento Número ' + nrodoc + ', ¿desea continuar?');
					if (ok){
						$('nrodoc').value = nrodoc;
						document.form1.submit();
					}
				}	
			}
		});
	}

function updater_consulta(fecha_desde, fecha_hasta, tipdoc, tipmov, nrodoc, descripcion, pagina){
	var url = 'updater_busca_movpre.php';
	var pars = 'fecha_desde=' + fecha_desde + '&fecha_hasta=' + fecha_hasta + '&tipdoc=' + tipdoc;
	pars += '&tipmov=' + tipmov + '&nrodoc=' + nrodoc + '&descripcion=' + descripcion + '&pagina=' + pagina;
	pars += '&ms='+new Date().getTime();
	var updater = new Ajax.Updater('consulta', url,{
		parameters: pars,
		asynchronous:true, 
		evalScripts:true,
		onLoading:function(request){Element.show('cargando')}, 
		onComplete:function(request){Element.hide('cargando')}
	}); 
}

function traeProveedores(){
	
	var url = 'buscar_proveedores.php';
	var pars = 'ms='+new Date().getTime();
		
	var Request = new Ajax.Request(
		url,
		{
			method: 'get',
			parameters: pars,
			onLoading:function(request){}, 
			onComplete:function(request){
				
				Dialog.alert(request.responseText, {windowParameters: {width:600, height:400, 
								showEffect:Element.show,hideEffect:Element.hide,
								showEffectOptions: { duration: 1}, hideEffectOptions: { duration:1 }
								}});
			}
		}
	);   
}

var t;

function busca_popup()
{
	clearTimeout(t);
	setTimeout('buscaProveedores()', 800);
}

function buscaProveedores()
{
	var url = 'buscar_proveedores.php';
	var pars = 'opcion=2&rif=' + $('search_rif_prov').value +'&nombre='+$('search_nombre_prov').value+'&ms='+new Date().getTime();
		
	var updater = new Ajax.Updater('divProveedores', 
		url,
		{
			method: 'post',
			parameters: pars,
			asynchronous:true, 
			evalScripts:true,
			onLoading:function(request){Element.show('cargando')}, 
			onComplete:function(request){Element.hide('cargando')}
		});
}

function selDocumento(id, rif){
	$('txtProveedores').value = id;
	$('proveedores').value = rif;
	Dialog.okCallback();
}
