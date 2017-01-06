function MM_jumpMenu(page,targ,selObj,restore){ //Se7h V.1
	var url=page+selObj.options[selObj.selectedIndex].value;
	//alert (url);
	eval(location=url);
}
function popup(theURL,winName,features) {//se7h
  window.open(theURL,winName,features);
}
/*SI SELECCIONO UN REQ DENTRO DE REQUISITOS_PROVEEDOR.PHP, ACTUALISO EL SELF Y MUSTRO*/
/*de momento no la estoy utilizando: se7h: 01/03/2006
function MM_jumpMenu_reqs(page,id,targ,selObj,restore){ //Se7h V.1
	var url=page+selObj.options[selObj.selectedIndex].value;
	eval(location=url);
}
*/
function deleteLastRow(tblName){
	var tbl = document.getElementById(tblName);
	if (tbl.rows.length > 1) tbl.deleteRow(tbl.rows.length - 1);
}

function onlynumbers(evento){ //valida q la entrada sea solo numerica, pero permita la 'coma'
	if (!evento) var evento = window.event;
	if (evento.keyCode) code = evento.keyCode;
	else if (evento.which) code = evento.which;
	if ((code < 48) || (code > 57)) return false;
	if (code == 44) return true;
}

//se7h: esta podria ser pero de momento uso la de arriba

	function noAlpha(obj){
		reg = /[^0-9.]/g;
		obj.value =  obj.value.replace(reg,"");
	}
	
function onlyNumbersCI(evento){ //valida q la entrada sea solo numerica
	if (!evento) var evento = window.event;
	if (evento.keyCode) code = evento.keyCode;
	else if (evento.which) code = evento.which;
//	 alert(code)
	// si la tecla presionada es 
	if (code == 8 || // backspace
		code == 9 || // tab
		code == 35 || // flecha izq
		code == 36 || // flecha izq
		code == 37 || // flecha izq
		code == 39 || // flecha der
		code == 46 || // suprimir
		code == 13) // enter
		return true;
	
	if ((code >= 48 && code <= 57) || (code >= 96 && code <= 105))
		return true
	else
		return false;
}

function formatoNumero(fld,e) {
	var milSep='.';
	var decSep=',';
	var sep = 0;
	var key = '';
	var i = j = 0;
	var len = len2 = 0;
	var strCheck = '0123456789';
	var aux = aux2 = '';
	
	var whichCode = (e.which) ? e.which : e.keyCode;

	// 8 y 46
	if (whichCode == 13) return true;  // Enter
	if (whichCode >= 96 && whichCode <= 105)
		whichCode -= 48;
	key = String.fromCharCode(whichCode);  // Get key value from key code
	
	if (strCheck.indexOf(key) == -1){
		if(whichCode == 8 || whichCode == 9 || whichCode == 46){
			return true;
		}else{
			return false;  // Not a valid key
		}
	}
	
	len = fld.value.length;
	
	for(i = 0; i < len; i++)
		if ((fld.value.charAt(i) != '0') && (fld.value.charAt(i) != decSep)) break;
	aux = '';
	for(; i < len; i++)
		if (strCheck.indexOf(fld.value.charAt(i))!=-1) aux += fld.value.charAt(i);
	aux += key;
	len = aux.length;
	if (len == 0) fld.value = '';
	if (len == 1) fld.value = '0'+ decSep + '0' + aux;
	if (len == 2) fld.value = '0'+ decSep + aux;
	if (len > 2) {
	aux2 = '';
	for (j = 0, i = len - 3; i >= 0; i--) {
		if (j == 3) {
			aux2 += milSep;
			j = 0;
		}
		aux2 += aux.charAt(i);
		j++;
	}
	fld.value = '';
	len2 = aux2.length;
	for (i = len2 - 1; i >= 0; i--)
		fld.value += aux2.charAt(i);
	fld.value += decSep + aux.substr(len - 2, len);
	}
	return false;
}

// convierte una cifra de formato USA a formato VEN
// similar a la funcion php muestrafloat en $appRoot/lib/functiones.php
function muestraFloat(nStr, dec){
	nStr += '';
	dec = (arguments.length > 1) ? dec : 2;
	x = nStr.split('.');
	x1 = x[0];
	x2 = ',' + ((x.length > 1) ? x[1].substr(0, dec) : '00');
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + '.' + '$2');
	}
	return x1 + x2;
}

// recibe un numero con formato VEN, y la convierte en un formato 
// utilizable para ejecutar operaciones matematicas
function usaFloat(nStr){
	return parseFloat(replace_caracter(replace_caracter(nStr, '.', ''), ',', '.'));
}

function replace_caracter(inputString, fromString, toString) {
   var temp = inputString;
   if (fromString == "") {
      return inputString;
   }
   if (toString.indexOf(fromString) == -1) { 
      while (temp.indexOf(fromString) != -1) {
         var toTheLeft = temp.substring(0, temp.indexOf(fromString));
         var toTheRight = temp.substring(temp.indexOf(fromString)+fromString.length, temp.length);
         temp = toTheLeft + toString + toTheRight;
      }
   } else { 
      var midStrings = new Array("~", "`", "_", "^", "#");
      var midStringLen = 1;
      var midString = "";

      while (midString == "") {
         for (var i=0; i < midStrings.length; i++) {
            var tempMidString = "";
            for (var j=0; j < midStringLen; j++) { tempMidString += midStrings[i]; }
            if (fromString.indexOf(tempMidString) == -1) {
               midString = tempMidString;
               i = midStrings.length + 1;
            }
         }
      }
	  
	     while (temp.indexOf(fromString) != -1) {
         var toTheLeft = temp.substring(0, temp.indexOf(fromString));
         var toTheRight = temp.substring(temp.indexOf(fromString)+fromString.length, temp.length);
         temp = toTheLeft + midString + toTheRight;
      }

      while (temp.indexOf(midString) != -1) {
         var toTheLeft = temp.substring(0, temp.indexOf(midString));
         var toTheRight = temp.substring(temp.indexOf(midString)+midString.length, temp.length);
         temp = toTheLeft + toString + toTheRight;
      }
   }
   return temp;
}

function popup(url, ancho, alto, left){
   left = left == undefined ? "300" : left;
   window.open(url,
      "popup1",
      "width="+ancho+", height="+alto+", scrollbars=yes, menubar=no, location=no, resizable=no,left = "+left+",top = 300");
}

// recibe como parametro una cadena que finaliza en un numero y devuelve ese numero
function getFila(nombre){
	var aNombre = nombre.split("_");
	return aNombre[aNombre.length - 1];
}
// CEPV.SN 16-08-2006   
function Cadena(Vector){
	var Palabra;
	Vector=Vector.split(' ');
	if(Vector[1]){
		Palabra=Vector[1];
		Palabra=Palabra.substring(0,1)+".";
		return Vector[0]+" "+Palabra; 
	}
	return Vector[0]; 
}
//CEPV.EN
function mostrarFecha(cadena){
	var Fecha= new String(cadena)	// Crea un string
	
	// Cadena Año
	var Dia= new String(Fecha.substring(Fecha.lastIndexOf("-")+1,Fecha.length))
	// Cadena Mes
	var Mes= new String(Fecha.substring(Fecha.indexOf("-")+1,Fecha.lastIndexOf("-")))
	// Cadena Día
	var Ano= new String(Fecha.substring(0,Fecha.indexOf("-")))
	
	
	var fecha_nueva = Dia + '/' + Mes + '/' + Ano ;
	
	return fecha_nueva;
}
// CEPV EN
function digitosMin(el, tam){
	elem = $(el);
	if(elem.value.length != tam){
		alert("Este campo debe contener al menos " + tam + " digitos");
		elem.value = "";
		elem.focus();
	}
}

// Ismael Depablos 16/11/06
function validarEmail(valor) {
  if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(valor)){
   return (true);
  } else {
   alert("La dirección de email es incorrecta.");
   return (false);
  }
 }
