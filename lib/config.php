<?
session_cache_limiter('private'); // CEPV.070906 permite abrir los reportes pdf en el IE... 
session_start();
/*
 * VARIABLES DE CONFIGURACION:
 * appRoot: 		Ubicacion fisica de la aplicacion
 * webRoot: 	Ubicacion HTTP de la aplicacion
 * DBuser:			Nombre de usuario de la base de datos
 * DBpass:			Password del usuario de la base de datos
 * DBname:			Nombre de la base de datos
 * DBserver:		Nombre del servidor de bases de datos
 */

// este archivo es necesario ya que contiene las funciones appRoot() y webRoot()
require("lib/ini.func.php");
$appRoot = appRoot();
$webRoot = webRoot();


// Parametros necesarios para conectarse a la BD
$DBuser = "puser";
$DBpass = "1234";
$DBserver="127.0.0.1:5432";
$DBname = "SCG_BD";

//Para conectar a la BD local
//$DBuser = "puser";
//$DBpass = "123";
//$DBserver="127.0.0.1";
//$DBname = "libertador";
//$DBserver="127.0.0.1";
//$DBname = "tocu";

// Establecemos el encoding por defecto del sistema, debe ser el mismo de la BD
header("Content-Type: text/html; charset=UTF-8");

// hago la conex a la BD
include($appRoot . "/lib/conn.php");

// Incluyo las librerias
include($appRoot . "/lib/core.lib.php");

?>
