<?php //se7h:2006[mar]

include ("lib/core.lib.php");

$id_proveedores=$_REQUEST['id_proveedores'];
$objeto_empresa=@$_REQUEST['objeto_empresa'];

#MUESTRO EL NOMBRE DEL PROVEEDOR
$sql="SELECT * FROM proveedores WHERE id=".$id_proveedores;
$rs = @$conn->Execute($sql);

#MUESTRO EL OBJETO DE LA EMPRESA
$slc="SELECT * FROM objeto_empresa WHERE id_proveedor=".$id_proveedores;
$oe = @$conn->Execute($slc);

					/*nota: pude haber hecho una relación, pero asi va bien ;)*/

	#GUARDO, en vez de hacer un update, un delete y luego insert me va bien ;)
	if(!empty($_REQUEST['btn_guardar'])){

		$del="DELETE FROM objeto_empresa WHERE id_proveedor=".$id_proveedores;
		@$conn->Execute($del);

		$ins="INSERT INTO objeto_empresa (id_proveedor, objeto_empresa) VALUES ($id_proveedores, '$objeto_empresa')";
		@$conn->Execute($ins);
	header ("Location: objeto_empresa_proveedor.php?id_proveedores=$id_proveedores");
	
	}

?>
<html>
<head>
<title>Objeto de la Empresa</title>
<style type="text/css" media="screen">@import url("css/estilos.css");</style>
</head>

<body>
<span class="titulo_maestro">Objeto de la Empresa</span>
<div id="formulario">
<form action="<?=$HTTP_SERVER_VARS['PHP_SELF']?>?id_proveedores=<?=$id_proveedores?>" method="post" name="objeto_empresa">
<table width="623" border="0">
  <tr>
    <td width="102">Proveedor:</td>
    <td width="505"><?=strtoupper($rs->fields['nombre'])?></td>
  </tr>
  <tr>
    <td valign="top">Objeto Empresa:</td>
    <td><textarea name="objeto_empresa" cols="90" rows="10"><?=$oe->fields['objeto_empresa']?></textarea></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="right"><input type="submit" name="btn_guardar" value="Guardar"></td>
  </tr>
</table>
</form>
</div>
</body>
</html>
