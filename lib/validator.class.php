<? 
/*
	EN ESTE EJEMPLO asumimos QUE EL FORM SE LLAMA form1 <form name="form1">
	asumimos tambien que hay un input text box que  se llama txttitle <input type="text" name="txttitle">
	DENTRO DEL <HEAD> COLOCAR ALGO COMO ESTO
	 	$validator=new validator("form1"); //CREA EL OBJETO VALIDATOR E INDICA EL NOMBRE DEL FORM
		$validator->create_message("id_title", "txttitle", "(Field required)"); //CREO EL MENSAJE DE VALIDACION Y EL OBJETO QUE VOY A CONTROLAR
		$validator->print_script();  //IMPRIMO EL SCRIPT
	DONDE QUEREMOS COLOCAR EL MENSAJE DEL VALIDADOR, COLOCAMOS ESTO
		$validator->show("id_title"); 
	AL FINAL, EN EL BOTON DE ENVIO COLOCAMOS ESTO
	<a href="#" < ? $validator->validate() ? > >enviar</a>
	
*/
class validator {
	var $form_name=""; //indica el formulario que sera validado
	var $forms=array(); //lleva un arreglo de todos los controlos html que seran validados
	var $id_client=array(); //controla un identificador del lado del cliente que permite saber donde se va a mostrar el error
	var $ar_messaje=array(); //colecciona los mensajes de error
	var $arr_type=array();
//	var $type_validation=array();

	//contructor: recibe opcionalmente el formulario que va a validar
	function validator($form="") {
		$this->form_name=$form;
	}	
	//crea el mensaje indicando como parametro el id que permitira asociarlo al momento de mostrar el error, el objeto que se va a validar y el mensaje que se va a enviar
	function create_message($idclient, $formobject, $message,$type_validation=0) {
		$this->id_client[]=$idclient;
		$this->forms[]=$formobject;
		$this->ar_messaje[]=$message;
		$this->arr_type[]=$type_validation;
	}

	/*Imprime el javascript que hace todas las acciones. antes de esto tiene que haberse 
	creado TODOS los mensajes que seran mostrados. 
	Preferiblemente, colocar esto dentro de la etiqueta <head>...</head>
	*/
	function print_script() {
		echo "<script type=\"text/javascript\">
		function validate () {
		sw=0; ";
		for ($i=0;$i<count($this->forms);$i++) {
			switch ($this->arr_type[$i]) {
			case 1:
				// Integer
				echo ("
				valor = document.".$this->form_name.".".$this->forms[$i].".value;
				if (valor > 0) { 	
					cad = valor.toString();
					for (var i=0; i<cad.length; i++) {
						var caracter = cad.charAt(i);
						if (caracter < '0' || caracter > '9') {
							document.getElementById('" . $this->id_client[$i] . "').innerHTML = '" . $this->ar_messaje[$i] . "';
							sw=1; 
						} 
					}
				} else { //Si es campo requerido tambien
					sw=1;
				}
				if (sw==1) {
					document.getElementById('" . $this->id_client[$i] . "').innerHTML = '" . $this->ar_messaje[$i] . "';
				} else { 
					document.getElementById('" . $this->id_client[$i] . "').innerHTML = '';
				}
				");
				break;
			case 2:
				//Radio Button
				echo ("
				blnRes = false;
				for(i=0; i < document." . $this->form_name . "." . $this->forms[$i].".length; i++){
					if(document." . $this->form_name . "." . $this->forms[$i]."[i].checked == true){
						blnRes = true;
						break;
					}
				}
				if (blnRes == true)
					document.getElementById('" . $this->id_client[$i] . "').innerHTML = '';
				else{
					document.getElementById('" . $this->id_client[$i] . "').innerHTML = '" . $this->ar_messaje[$i] . "';
					sw=1; 
				} 
				");
				break;
			case 3:
				//Campo requerido y comprueba la direccion de Email
				echo ("
				msj = 'E-mail invalido';
				apos=eval(document.".$this->form_name.".".$this->forms[$i].".value.indexOf('@'));
				dotpos=eval(document.".$this->form_name.".".$this->forms[$i].".value.lastIndexOf('.'));
				lastpos=eval(document.".$this->form_name.".".$this->forms[$i].".value.length-1);
				if (document." . $this->form_name . "." . $this->forms[$i] . ".value=='') { 
					document.getElementById('" . $this->id_client[$i] . "').innerHTML = '" . $this->ar_messaje[$i] . "';
					sw=1; 
				}else {
					if (apos < 1 || dotpos-apos < 2 || lastpos-dotpos > 3 || lastpos-dotpos < 2) { 
						document.getElementById('" . $this->id_client[$i] . "').innerHTML = msj;
						sw=1; 
					} else {
						document.getElementById('" . $this->id_client[$i] . "').innerHTML = '';
					}
				}
				");
				break;
			case 4:
				// Texto Requerido y no acepta numeros
				echo ("
				valor = document.".$this->form_name.".".$this->forms[$i].".value;
				msj = 'No debe contener numeros';
				if (valor == '' ) { 
					document.getElementById('" . $this->id_client[$i] . "').innerHTML = '" . $this->ar_messaje[$i] . "';
					sw=1;
				}
				else {
					if(valor != ''){
						var numdigits = 0;
						for (var j=0; j<valor.length; j++)
							if (valor.charAt(j)>='0' && valor.charAt(j)<='9') numdigits++;
	
						if(numdigits>0){
							document.getElementById('" . $this->id_client[$i] . "').innerHTML = msj;
							sw=1; 
						}else { 
							document.getElementById('" . $this->id_client[$i] . "').innerHTML = '';
						}
					}
				}
				");

				break;
			case 5:
				// Texto Requerido, no acepta letras y la el tamaño debe estar entre 7 y 8 digitos
				echo ("
				valor = document.".$this->form_name.".".$this->forms[$i].".value;
				msj = 'No debe contener letras';
				msj2 = 'Debe tener entre 7 y 8 digitos';
				if (valor == '' ) { 
					document.getElementById('" . $this->id_client[$i] . "').innerHTML = '" . $this->ar_messaje[$i] . "';
					sw=1;
				}
				else {
					if(valor != ''){
						var numletras = 0;
						for (var j=0; j<valor.length; j++)
							if (!(valor.charAt(j)>='0' && valor.charAt(j)<='9')) numletras++;
	
						if(numletras>0){
							document.getElementById('" . $this->id_client[$i] . "').innerHTML = msj;
							sw=1; 
						}else{
							if(valor.length >= 7 && valor.length <= 8 ){
								document.getElementById('" . $this->id_client[$i] . "').innerHTML = '';
							}else{
								document.getElementById('" . $this->id_client[$i] . "').innerHTML = msj2;
								sw=1; 
							}
						}
					}
				}
				");
				break;
			case 6:
				// Texto Requerido, no acepta numeros y la el tamaño debe ser mayor de 25 caracteres
				echo ("
				valor = document.".$this->form_name.".".$this->forms[$i].".value;
				msj = 'No debe contener numeros';
				msj2 = 'Debe tener mas de 25 caracteres';
				if (valor == '' ) { 
					document.getElementById('" . $this->id_client[$i] . "').innerHTML = '" . $this->ar_messaje[$i] . "';
					sw=1;
				}
				else {
					if(valor != ''){
						var numdigitos = 0;
						for (var j=0; j<valor.length; j++)
							if (valor.charAt(j)>='0' && valor.charAt(j)<='9') numdigitos++;
	
						if(numdigitos>0){
							document.getElementById('" . $this->id_client[$i] . "').innerHTML = msj;
							sw=1; 
						}else{
							if(valor.length > 25){
								document.getElementById('" . $this->id_client[$i] . "').innerHTML = '';
							}else{
								document.getElementById('" . $this->id_client[$i] . "').innerHTML = msj2;
								sw=1;
							}
						}
					}
				}
				");
				break;
			case 7:
				// Texto Requerido, no acepta numeros y la el tamaño debe ser mayor de 5 caracteres
				echo ("
				valor = document.".$this->form_name.".".$this->forms[$i].".value;
				msj = 'No debe contener numeros';
				msj2 = 'Debe tener mas de 5 caracteres';
				if (valor == '' ) { 
					document.getElementById('" . $this->id_client[$i] . "').innerHTML = '" . $this->ar_messaje[$i] . "';
					sw=1;
				}
				else {
					if(valor != ''){
						var numdigitos = 0;
						for (var j=0; j<valor.length; j++)
							if (valor.charAt(j)>='0' && valor.charAt(j)<='9') numdigitos++;
	
						if(numdigitos>0){
							document.getElementById('" . $this->id_client[$i] . "').innerHTML = msj;
							sw=1; 
						}else{
							if(valor.length > 5){
								document.getElementById('" . $this->id_client[$i] . "').innerHTML = '';
							}else{
								document.getElementById('" . $this->id_client[$i] . "').innerHTML = msj2;
								sw=1;
							}
						}
					}
				}
				");
				break;
			case 8:
				// No puede estar vacio, solo acepta numeros y caracteres especiales de tlf y el tamaño debe ser entre 9 y 13 digitos
				echo ("
				valor = document.".$this->form_name.".".$this->forms[$i].".value;
				msj = 'Solo debe contener numeros, (), [] o +';
				msj2 = 'Debe tener entre 9 y 13 digitos';
				if (valor == '' ) { 
					document.getElementById('" . $this->id_client[$i] . "').innerHTML = '" . $this->ar_messaje[$i] . "';
					sw=1;
				}
				else {
					if(valor != ''){
						var telnr = /^\+?[0-9 ()-]+[0-9]$/
						if (!telnr.test(valor)) {
							document.getElementById('" . $this->id_client[$i] . "').innerHTML = msj;
							sw=1; 
						}else{
							if(valor.length >= 9 && valor.length <= 13){
								document.getElementById('" . $this->id_client[$i] . "').innerHTML = '';
							}else{
								document.getElementById('" . $this->id_client[$i] . "').innerHTML = msj2;
								sw=1;
							}
						}
					}
				}
				");
				break;
			case 9:
				// No puede estar vacio, no acepta letras y el tamaño debe ser entre 9 y 13 digitos
				echo ("
				valor = document.".$this->form_name.".".$this->forms[$i].".value;
				msj = 'No debe contener letras';
				msj2 = 'Debe tener por lo menos 8 caracteres';
				if (valor == '' ) { 
					document.getElementById('" . $this->id_client[$i] . "').innerHTML = '" . $this->ar_messaje[$i] . "';
					sw=1;
				}
				else {
					if(valor != ''){
						var numletras = 0;
						for (var j=0; j<valor.length; j++)
							if (!(valor.charAt(j)>='0' && valor.charAt(j)<='9' || valor.charAt(j) == '/' || valor.charAt(j) == '-')) numletras++;
	
						if(numletras>0){
							document.getElementById('" . $this->id_client[$i] . "').innerHTML = msj;
							sw=1; 
						}else{
							if(valor.length >= 8 ){
								document.getElementById('" . $this->id_client[$i] . "').innerHTML = '';
							}else{
								document.getElementById('" . $this->id_client[$i] . "').innerHTML = msj2;
								sw=1; 
							}
						}
					}
				}
				");
				break;
			case 10:
				// No puede estar vacio y el tamaño debe ser entre 6 y 10 digitos
				echo ("
				valor = document.".$this->form_name.".".$this->forms[$i].".value;
				msj = 'Debe tener entre 6 y 10 caracteres';
				if (valor == '' ) { 
					document.getElementById('" . $this->id_client[$i] . "').innerHTML = '" . $this->ar_messaje[$i] . "';
					sw=1;
				}
				else {
					if(valor != ''){
						if(valor.length >= 6 && valor.length <= 10 ){
							document.getElementById('" . $this->id_client[$i] . "').innerHTML = '';
						}else{
							document.getElementById('" . $this->id_client[$i] . "').innerHTML = msj;
							sw=1; 
						}
					}
				}
				");
				break;
			case 11:
				// Comprueba que las contraseñas coincidan
				echo ("
				valor = document.".$this->form_name.".".$this->forms[$i].".value;
				if (valor != document.".$this->form_name.".password.value) { 
					document.getElementById('" . $this->id_client[$i] . "').innerHTML = '" . $this->ar_messaje[$i] . "';
					sw=1;
				}
				else {
					document.getElementById('" . $this->id_client[$i] . "').innerHTML = '';
				}
				");
				break;
			case 12:
				// puede estar vacio, solo acepta numeros y caracteres especiales de tlf y el tamaño debe ser entre 9 y 13 digitos
				echo ("
				valor = document.".$this->form_name.".".$this->forms[$i].".value;
				msj = 'Solo debe contener numeros, (), [] o +';
				msj2 = 'Debe tener entre 9 y 13 digitos';
				if (valor == '' ) { 
					document.getElementById('" . $this->id_client[$i] . "').innerHTML = '';
				}
				else {
					if(valor != ''){
						var telnr = /^\+?[0-9 ()-]+[0-9]$/
						if (!telnr.test(valor)) {
							document.getElementById('" . $this->id_client[$i] . "').innerHTML = msj;
							sw=1; 
						}else{
							if(valor.length >= 9 && valor.length <= 13){
								document.getElementById('" . $this->id_client[$i] . "').innerHTML = '';
							}else{
								document.getElementById('" . $this->id_client[$i] . "').innerHTML = msj2;
								sw=1;
							}
						}
					}
				}
				");
				break;
			case 13:
				//Check Button
				echo ("
				msj = 'Usted debe aceptar los terminos y condiciones del contrato para registrarse';
				blnRes = false;
				if(document." . $this->form_name . "." . $this->forms[$i].".checked == true){
					blnRes = true;
				}
				if (blnRes == true)
					document.getElementById('" . $this->id_client[$i] . "').innerHTML = '';
				else{
					alert(msj);
					sw=1; 
				} 
				");
				break;
			// CEPV.210906.SN
			case 14:
				// No puede estar vacio, no acepta letras, solo numeros y el caracter "."  Utilizado para validar Cedula  
				echo ("
				valor = document.".$this->form_name.".".$this->forms[$i].".value;
				msj = 'No debe contener letras';
				if (valor == '' ) { 
					document.getElementById('" . $this->id_client[$i] . "').innerHTML = '" . $this->ar_messaje[$i] . "';
					sw=1;
				}
				else {
					var numletras = 0;
					for (var j=0; j<valor.length; j++)
						if (!(valor.charAt(j)>='0' && valor.charAt(j)<='9' || valor.charAt(j) == '.')) numletras++;

					if(numletras>0){
						document.getElementById('" . $this->id_client[$i] . "').innerHTML = msj;
						sw=1; 
					}
				}
				");
				break;
			case 15: 
				// No puede estar vacio, no acepta letras, solo numeros y los caracteres "." y ","  Utilizado para float  
				echo ("
				valor = document.".$this->form_name.".".$this->forms[$i].".value;
				msj = 'No debe contener letras';
				if (valor == '' ) { 
					document.getElementById('" . $this->id_client[$i] . "').innerHTML = '" . $this->ar_messaje[$i] . "';
					sw=1;
				}
				else {
					var numletras = 0;
					for (var j=0; j<valor.length; j++)
						if (!(valor.charAt(j)>='0' && valor.charAt(j)<='9' || valor.charAt(j) == '.' || valor.charAt(j) == ',')) numletras++;

					if(numletras>0){
						document.getElementById('" . $this->id_client[$i] . "').innerHTML = msj;
						sw=1; 
					}
				}
				");
				break;
			case 16:
				// El monto debe ser mayor que cero
				echo ("
				num = muestraFloat(parseFloat(document.".$this->form_name.".".$this->forms[$i].".value, 10));
				cad = String(document.".$this->form_name.".".$this->forms[0].".value);
				valor = document.".$this->form_name.".".$this->forms[$i].".value;
				msj = 'El monto debe ser mayor que cero';
				
				if (valor == '')
				{
					document.getElementById('" . $this->id_client[$i] . "').innerHTML = '" . $this->ar_messaje[$i] . "';
					sw = 1;
				}
				else if (num == 0 && cad.charAt(0)=='3')
				{
					document.getElementById('" . $this->id_client[$i] . "').innerHTML = msj;
					sw = 1;
				}
				else
				{
					document.getElementById('" . $this->id_client[$i] . "').innerHTML = '';
				}
				");
				break;	
			// CEPV.210906.EN	
			default:
				// Text Requiered
				echo ("
				if (document." . $this->form_name . "." . $this->forms[$i] . ".value=='' ||
					document." . $this->form_name . "." . $this->forms[$i] . ".value=='0') { 
					document.getElementById('" . $this->id_client[$i] . "').innerHTML = '" . $this->ar_messaje[$i] . "';
					sw=1; 
				} else { 
					document.getElementById('" . $this->id_client[$i] . "').innerHTML = '';
				}
				");

				break;
			} //end Switch
		} //end for

		echo "if (sw==0) {
			document." .  $this->form_name . ".submit();
		}else { 
			document.".$this->form_name.".".$this->forms[0].".focus();	
				}
		}
	</script>";
	}
	//funcion que indica donde sera mostrado el mensaje de error, el parametro es el id del mensaje que se coloco en create_message()
	function show($id_client) {
		return "<span id='$id_client' class='errormsg'></span>";	
	}
	//controla la validacion, se debe colocar dentro de la etqueta <input ...> o <a ...>
	function validate(){
		return "validate()";
	}
	function msj($id_client='campo_requerido') {
		return "<span class='errormsg' id='$id_client' class='errormsg'>(*) Campo Requerido</span>";	
	}

}
?>
