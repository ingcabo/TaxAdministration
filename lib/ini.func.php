<?
/********************************************************
********** Funciones para el manejo de rutas ************
********************************************************/
function appRoot(){
	return substr($_SERVER['SCRIPT_FILENAME'], 0, strlen($_SERVER['SCRIPT_FILENAME']) - (strlen(self($_SERVER['PHP_SELF']))+1));
}
function webRoot(){
	return substr($_SERVER['SCRIPT_NAME'], 0, strlen($_SERVER['SCRIPT_NAME']) - (strlen(self($_SERVER['PHP_SELF']))+1));
}

function self($self){
	$a = explode("/", $self);
	return $a[count($a)-1];	
}

function nombre($file){
	$a = explode(".", $file);
	return $a[0];
}

?>
