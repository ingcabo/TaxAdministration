<?php 
//se7h
require ("comun/ini.php");


$tipo=$_REQUEST['tipo'];
$valor=$_REQUEST['valor'];

//echo $tipo."..".$valor;

	if(empty($tipo) OR empty($valor)){
		echo "Filtro Vacio";
		exit;
	}

	if($tipo=='rif/c'){
		$w= " WHERE contribuyente.rif='".$valor."' OR contribuyente.identificacion='".$valor."' ";
	}
	if($tipo=='nombre'){
		$w= " WHERE contribuyente.primer_nombre='".$valor."' OR contribuyente.primer_apellido='".$valor."'";
	}


$q="
SELECT *
FROM
 vehiculo.contribuyente
";
$q.=$w;

	//die($q);
	$r = $conn->Execute($q);
//echo $r->RecordCount();
if($r->RecordCount()==0){ echo "No hay registros en la bd";} else {

?>
<html>
<head>
<title>Documento sin t&iacute;tulo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<table align="center" width="635" cellpadding="0" cellspacing="1" class="sortable" id="grid">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Nombre</td>
<td>Identificaci&oacute;n</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<?php 	while(!$r->EOF){ ?>
<tr class="filas"> 
<td><?=$r->fields['id']?></td>
<td><?=$r->fields['primer_apellido']?> <?=$r->fields['primer_nombre']?></td>
<td><?=$r->fields['rif']?> <?=$r->fields['identificacion']?></td>
<td><a href="?accion=del&id=<?=$r->fields['id']?>" onClick="if (confirm('Si presiona Aceptar ser&aacute; eliminada esta informaci&oacute;n')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
<td align="center">
<a href="#" onClick="updater('<?=$r->fields['id']?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
</tr>
<?php  	$r->movenext();		} ?>
</table>
</body>
</html>
<?php } ?>