<?php 
//include ("lib/core.lib.php");
require ("comun/ini.php");
$id_grupo_proveedores=@$_REQUEST['id_grupo_proveedores'];

$id = $_REQUEST['id'];
$requisitos=$_REQUEST['requisitos'];

$rqs="SELECT * FROM relacion_req_gp WHERE id_grupo_proveedor='$id'";
$rslt=$conn->Execute($rqs);
$tt=$rslt->RecordCount();
if(!empty($_REQUEST['accion']) AND  $_REQUEST['accion']=='Guardar'){ 
	#BORRO.
	$sql_del="DELETE FROM relacion_req_gp WHERE id_grupo_proveedor='$id'";
	$rd = $conn->Execute($sql_del);
		for($i=0;$i<count($requisitos);$i++){
			$sql="INSERT INTO relacion_req_gp (id_grupo_proveedor, id_requisito) VALUES ('$id','$requisitos[$i]')";
			$r = $conn->Execute($sql);
		}
	header ("Location: grupo_proveedor.php?id=$id");	
}

#REQUISITO POR GRUPO
/*
	$c="select requisitos.descripcion AS descripcion,  requisitos.fecha AS fecha
	from relacion_req_gp, requisitos, grupos_proveedores 
	where requisitos.id=relacion_req_gp.id_requisito
	  and grupos_proveedores.id=relacion_req_gp.id_grupo_proveedor
	  and relacion_req_gp.id_grupo_proveedor='$id_grupo_proveedores'";
	$e=	$conn->Execute($c);
*/	
//require ("comun/header.php");
?>
<style type="text/css" media="screen">@import url("css/estilos.css");</style>
<script src="js/prototype.js"></script>
<script src="js/script_st.js"></script>
<span class="titulo_maestro">Requisitos por Grupo Proveedor</span>
<div id="formulario">
<form name="form1" action="grupo_proveedor.php" method="post">
<table width="404" border="0" align="left">
  <tr>
    <td width="157">Grupo Proveedor:</td>
    <td width="237"><?=helpers::combo($conn, 'grupos_proveedores', $id, " \" onChange=\"MM_jumpMenu('grupo_proveedor.php?id=','self',this,0)\" disabled ")?></td>
  </tr>
	<tr><td colspan="2">
	<a href="#" onclick="addTR(); return false;">[+]</a>
	<a href="#" onclick="deleteLastRow('tablita');">[-]</a>Requisitos
		<table align="left" border=0 id="tablita">
<?php if($tt==0){ //muestro al menos uno en caso de q no tenga reqs asociados a el?>
				<tr>
				<td width="230"><?=helpers::combo($conn, 'requisitos', '', '', 'descripcion', 'requisitos[]','requisitos')?></td>
				</tr>
<?php } ?>				
				<?php				 
				if(!empty($id)){
					while(!$rslt->EOF){
						$id_requisito=$rslt->fields['id_requisito'];
				?>
				<tr>
				<td width="230"><?=helpers::combo($conn, 'requisitos', $id_requisito, '', 'descripcion', 'requisitos[]','requisitos')?></td>
				</tr>
				<?php		
						$rslt->movenext();
					}	
				}
				?>
		</table>
	</td></tr>
</table>
<input name="accion" type="submit" value="Guardar" />
<input name="id" type="hidden" value="<?=$id?>" />
</form>
</div>
<script type="text/javascript">
var i = 1
function addTR(){
	var x=$('tablita').insertRow($('tablita').rows.length)
	var y1=x.insertCell(0)
	var cp = $('requisitos').cloneNode(true)
	y1.appendChild(cp)

	i++
}
</script>
<? require ("comun/footer.php"); ?>
