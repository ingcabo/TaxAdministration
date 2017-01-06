<?
include ("comun/ini.php");
$form = $_REQUEST['form'];
$id = $_REQUEST['id'];
$id_momento = $_REQUEST['id_momento'];
//die('entro');

$id_escenario = $_REQUEST['id_escenario'];
// funciones de manejo de html
function radio($bool, $checked){ return ($bool==$checked)? "checked=checked":""; }
function checkbox($campo){ return ($campo=="t")?"checked=\"checked\"":""; }
function boton($id){ return (empty($id))?"Guardar":"Actualizar"; }
function desincorporo($id){ return (empty($id))?"":"Desincorporar";}
function reincorporo($id){ return (empty($id))?"":"Reincorporar";}

//die($form);

if($form == 'aprobar_escenarios')
	$objeto = new escenarios;
elseif($form == 'gp_req')
	$objeto = new grupos_proveedores;
elseif($form == 'visualizar_anular_cheque')
	$objeto = new cheque;
elseif ($form== 'comprobante')
	$objeto = new comprobante($conn);
elseif ($form== 'estado_cuenta')
	$objeto = new estadoCuenta;
else // todos los maestros
	$objeto = new $form;

if($form == 'partidas_presupuestarias' 
	or $form == 'categorias_programaticas' 
	or $form == 'unidades_ejecutoras'
	or $form == 'relacion_pp_cp'
	or $form == 'relacion_ue_cp'
	or $form == 'relacion_cc_pp')
	$objeto->get($conn, $id, $id_escenario);
elseif($form == 'orden_servicio_trabajo' 
			or $form == 'nomina' 
			or $form == 'contrato_servicio' 
			or $form == 'obras' 
			or $form == 'caja_chica' 
			or $form == 'contrato_obras' 
			or $form == 'requisiciones' 
			or $form == 'revision_requisicion' 
			or $form == 'actualiza_cotizacion'
			or $form == 'analisis_cotizacion'  
			or $form == 'ordcompra'
			or $form == 'orden_pago'
			or $form == 'ayudas'
			or $form == 'documentos_generales'		
			) // si es una orden, o nomina que sea del escenario en ejecucion
	$objeto->get($conn, $id, $escEnEje);
#cMx 051006 SE HIZO PQ SE CAMBIO LA FORMA DE BUSCAR LOS MOV PRESUPUESTARIOS#
elseif ($form=='movimientos_presupuestarios')
	$objeto->get($conn,$id,$id_momento);
// CEPV.230606.SM Para que solo haga la consulta cuando sea modificar....
elseif($form=='comprobante')
	$objeto->get($id);
elseif($id)
// CEPV.230606.E
	$objeto->get($conn, $id);
$boton = boton($id);
$desincorporados = desincorporo($id);
$reincorporados = reincorporo($id);
include("tpl/$form.tpl.html");
?>

