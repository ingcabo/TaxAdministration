<?
require ('lib/config.php');
$usuario = new usuarios; // creo un nuevo objeto usuario
$validator=new validator("form1");
if (!($usuario->is_logged($conn))){ // chequeo si el usuario está loggeado, si lo está obtengo su data
	header("Location:".$webRoot."/salir.php"); // si no lo está lo envio al index
	exit;
}
//$page = new page;
if (empty($usuario->status)){ // si su estatus es 0 o vacio
	header("Location:".$webRoot."/index.php?st=n"); // sale del sistema
}

$escEnEje = 1111; // escenario en ejecucion, normalmente 1111
$q = "SELECT ano FROM puser.escenarios WHERE id = '$escEnEje'";
$row = $conn->Execute($q);
$anoCurso = $row->fields['ano'];

// Esta variable verifica si deja cargar el numero de documento por el usuario o si va a ser autogenerado
//false: va a ser cargado por el usuario  true: va a ser generado por el sistema
$auxNrodoc = true;


## ESTO ERA EN EL CASO DE QUE SE IMPUTARA EL IVA POR UNA UNICA RELACION_PP_CP
#ESTE QUERY TRAE EL ID_PARCAT DE LA PARTIDA POR LA QUE SE IMPUTA EL IVA
/*$q = "SELECT relacion_pp_cp.id AS idparcat FROM puser.relacion_pp_cp WHERE id_categoria_programatica = '0105000051' ";
$q.= "AND id_partida_presupuestaria = '4031801000000' ";
$q.= "AND id_escenario = '$escEnEje'";
$row = $conn->Execute($q);
$idpc_iva = $row->fields['idparcat'];*/
$desdeUT = $anoCurso.'-01-02';
$hastaUT = $anoCurso+1 .'-01-01';
$sql = "SELECT ut FROM puser.ut WHERE fecha_desde = '$desdeUT' AND fecha_hasta = '$hastaUT'";
//die($sql);
$row = $conn->Execute($sql);
if($row){
	$ut_vigente = $row->fields['ut'];
} else {
	$ut_vigente = 0;
}

$sql = "SELECT id_cta FROM contabilidad.fondos_terceros LIMIT 1";
$row = $conn->Execute($sql);
if($row)
	$ctaFondos = $row->fields['id_cta'];
else
	$ctaFondos = '';

?>
