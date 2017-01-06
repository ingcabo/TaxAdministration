<?
include ("lib/core.lib.php");
// Creando el objeto cargos
$oCargos = new cargos;
$accion = $_REQUEST['accion'];
$enviar = $_REQUEST['enviar'];
if($enviar == 'Enviar'){
	$cp = $_REQUEST['cp'];
	$pp = $_REQUEST['pp'];
	$mon = $_REQUEST['mon'];
/*	for($i = 0; $i<count($cp); $i++){
		echo "CP: $cp[$i] - PP: $pp[$i] - Monto: $mon[$i]<br/>";
	} */
	if(obras::add_relacion($conn, $cp, $pp, '1', $mon))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
}
if($accion == 'Guardar'){
	if($oCargos->add($conn, $_REQUEST['descripcion']))
		$msj = "Registro insertado con &eacute;xito";
	else
		$msj = ERROR;
}elseif($accion == 'Actualizar'){
	if($oCargos->set($conn, $_REQUEST['id'], $_REQUEST['descripcion']))
		$msj = "Registro actualizado con &eacute;xito";
	else
		$msj = ERROR;
}elseif($accion == 'del'){
	if($oCargos->del($conn, $_REQUEST['id']))
		$msj = "Registro borrado";
	else
		$msj = ERROR;
}
//Seccion paginador
$page_size = 25;
if ($_GET['pg'])
	$start_record=($_GET['pg'] * $page_size) - $page_size;
else
	$start_record=0;

$cCargos=$oCargos->get_all($conn, $start_record,$page_size);
$pag=new paginator($oCargos->total,$page_size, "lista_cargos.php");
$i=$pag->get_total_pages();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Cargos</title>
<script src="js/prototype.js" type="text/javascript"></script>
<script src="js/scriptaculous.js" type="text/javascript"></script>
<link href="css/estilo.css" type=text/css rel=stylesheet>
</head>
<body>
<?=$msj?>
<div id="contenido">
<form name="form1" method="post">
<table border="0">
<tr>
	<td>Escenario:</td>
	<td><?=helpers::combo($conn, 'escenarios')?></td>
	<td>Unidad Ejecutora:</td>
	<td><?=helpers::combo($conn, 'unidades_ejecutoras')?></td>
	<td>Parroquia:</td>
	<td colspan="3"><?=helpers::combo($conn, 'parroquias')?></td>
</tr>
<tr>
	<td>Financiadora:</td>
	<td><?=helpers::combo($conn, 'financiamiento')?></td>
	<td>A&ntilde;o:</td>
	<td colspan="5"><input name="ano" size="8" type="text"></td>
</tr>
<tr>
	<td>Denominaci&oacute;n:</td>
	<td colspan="7"><textarea name="denominacion" cols="50" rows="8"></textarea></td>
</tr>
<tr>
	<td>Funcionario Responsable:</td>
	<td colspan="7"><input size="50" name="responsable" type="text"></td>
</tr>
<tr>
	<td colspan="8">
	<table  border=0 id="tablita">
		<tr>
			<td>Categoria:</td>
			<td><?=helpers::combo($conn, 'categorias_programaticas', '', '', 'descripcion', 'cp[]')?></td>
			<td>Partida Presupuestaria:</td>
			<td><?=helpers::combo($conn, 'partidas_presupuestarias', '', '', 'descripcion', 'pp[]')?></td>
			<td>Monto:</td>
			<td><input name="mon[]" id="monto" size="8" type="text"></td>
		</tr>
	</table>
	</td>
</tr>
</table>
<input name="enviar" id="enviar" type="submit" value="Enviar" />
</form>
<a href="#" onclick="addTR(); return false;">[+]</a>
<br/>
 <a href="#" onclick="alert($('tablita').innerHTML);">INNER	</a> 
<script type="text/javascript">
var i = 1
function addTR(){
	var x=$('tablita').insertRow($('tablita').rows.length)
	var y1=x.insertCell(0)
	var y2=x.insertCell(1)
	var y3=x.insertCell(2)
	var y4=x.insertCell(3)
	var y5=x.insertCell(4)
	var y6=x.insertCell(5)
	y1.innerHTML= "Categoria:"
	var cp = $('categorias_programaticas').cloneNode(true)
	y2.appendChild(cp)
	y3.innerHTML= "Partida Presupuestaria:"
	var pp = $('partidas_presupuestarias').cloneNode(true)
	y4.appendChild(pp)
	y5.innerHTML= "Monto:"
	var m = $('monto').cloneNode(false)
	m.nodeValue = 'aaa';
	y6.appendChild(m)
	i++
}
function updater(id){
	var sParam;
	if(id != 0){
		sParam = 'updater.php?form=cargos&id=' + id;
	}else{
		sParam = 'updater.php?form=cargos';
	}
	new Ajax.Updater('formulario', sParam,{
		asynchronous:true, 
		onLoading:function(request){Element.show('cargando')}, 
		onComplete:function(request){Element.hide('cargando')},
		onSuccess:function(){ 
			new Effect.Highlight('formulario', {startcolor:'#ffe9b9', endcolor:'#ffffff'});
		} 
	}); 
}
function close_div(){
	$('formulario').innerHTML = '<a href="#" onclick="updater(0)">Agregar Nuevo Registro</a>';
}
//Effect.Appear('msj',{duration:1.2});
</script>
</div>
<!-- <a href="#" onclick="alert($('formulario').innerHTML);">INNER form</a> -->
</body>
</html>
