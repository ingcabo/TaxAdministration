<?php //se7h:2006[mar]

include ("comun/ini.php");

$id_proveedores=$_REQUEST['id_proveedores'];
$tipo_contribuyente=$_REQUEST['tipo_contribuyente'];
$ingreso_periodo_fiscal=$_REQUEST['ingreso_periodo_fiscal'];
$cant_unid_tributaria=$_REQUEST['cant_unid_tributaria'];
$today=date('Y-m-d');
$fecha=$_REQUEST['fecha'];

#DATOS DEL PROVEEDOR, RELACIONADOS A LA RETENCION_IVA
$sql="SELECT 
  proveedores.nombre AS nombre,
  retencion_iva.tipo_contribuyente,
  retencion_iva.ingreso_periodo_fiscal,
  retencion_iva.cant_unid_tributaria,
  retencion_iva.fecha
FROM
 proveedores
 LEFT JOIN retencion_iva ON (proveedores.id=retencion_iva.id_proveedor)
WHERE
  (proveedores.id = $id_proveedores)";
$rs = @$conn->Execute($sql);  
#UNIDAD TRIBUTARIA CORRESPONDIENTE A $TODAY
$ut="
	SELECT 
	  ut.ut AS ut
	FROM
	 ut
	WHERE
	  ('$today' BETWEEN ut.fecha_desde AND ut.fecha_hasta)";
$rsut = @$conn->Execute($ut); 
$ut=$rsut->fields['ut'];
	#GUARDO, en vez de hacer un update, un delete y luego insert me va bien ;)
	if(!empty($_REQUEST['btn_guardar'])){
	
		$tc=$ingreso_periodo_fiscal/$ut;  //tipo contribuyente segun operacion: si ingreso/ut>=3000 then tipo_contribuyente=ORDINARIO
		if($tc>=3000){
			$tipo_contribuyente='ORDINARIO';
		}
	
		$del="DELETE FROM retencion_iva WHERE id_proveedor=".$id_proveedores;
		@$conn->Execute($del);

		$ins="INSERT INTO retencion_iva (id_proveedor, tipo_contribuyente, ingreso_periodo_fiscal, cant_unid_tributaria,  fecha) VALUES
			($id_proveedores, '$tipo_contribuyente', '$ingreso_periodo_fiscal', '$cant_unid_tributaria', '$today')";
		@$conn->Execute($ins);
		////echo $ins;
	header ("Location: retencion_iva_proveedor.php?id_proveedores=$id_proveedores");
	
	}
require('comun/header_popup.php');

?>
<html>
<head>
<title>Informaci&oacute;n Fiscalo del Proveedor</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css" media="screen">@import url("css/estilos.css");</style>
<script src="js/script_st.js"></script>
<script>
function setblank(){//blaque el campo: se7h 2006[mar]
var campo=window.document.retencion_iva.ingreso_periodo_fiscal;
		if(campo.value=='0.00'){
		campo.value='';
		}
}
</script>
</head>

<body>
<div id="formulario_popup" style="width:370px">
<form action="<?=$HTTP_SERVER_VARS['PHP_SELF']?>?id_proveedores=<?=$id_proveedores?>" method="post" name="retencion_iva">
<table width="344" border="0">
  <tr>
    <td width="116">Proveedor:</td>
    <td width="218"><?=strtoupper($rs->fields['nombre'])?></td>
  </tr>
  <tr>
    <td>Tipo Contribuyente:</td>
    <td>
	<select name="tipo_contribuyente">
	<option value="">Seleccione</option>
	<option value="FORMAL" <?php if($rs->fields['tipo_contribuyente']=='FORMAL'){ echo "selected"; } ?> >FORMAL</option>
	<option value="ORDINARIO" <?php if($rs->fields['tipo_contribuyente']=='ORDINARIO'){ echo "selected"; } ?>>ORDINARIO</option>
	</select>
	</td>
  </tr>
  <tr>
    <td>Ingreso Periodo Fiscal : </td>
    <td><input name="ingreso_periodo_fiscal" value="<?=number_format($rs->fields['ingreso_periodo_fiscal'], 2, '.', '')?>" onFocus="setblank(this)" onKeyUp="noAlpha(this)"> ej: 9999.5</td>
  </tr>
  <tr>
    <td>Cant. Unid. Tributaria: </td>
    <td><input name="cant_unid_tributaria" value="<?=number_format($ut, 2, '.', '')?>" readonly="readonly"> ej: 9999.5</td>
  </tr>
  <tr>
    <td>Actualizado</td>
    <td><input name="fecha" value="<?=muestrafecha($rs->fields['fecha'])?>" readonly="readonly"></td>
  </tr>
  <tr>
    <td colspan="2" align="right"><input type="submit" name="btn_guardar" value="Guardar"></td>
  </tr>
</table>
</form>

</div>
</body>
</html>
<? require ("comun/footer_popup.php"); ?>
