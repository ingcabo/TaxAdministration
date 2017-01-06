<? #by Se7h 2006[mar]
require ("comun/ini.php");
//include ("lib/core.lib.php");
$today=date("Y-m-d");

#LISTA DE PROVEEDORES
/*$prov="SELECT * FROM proveedores ORDER BY status ASC ";
$r_prov=$conn->Execute($prov);*/
$tamano_pagina = 20;
$pagina = $_REQUEST['pagina'];
if (!$pagina)
{
	$inicio = 0;
	$pagina = 1;
}
else
	$inicio = ($pagina -1) * $tamano_pagina;
	
$oProveedor = new proveedores;
$cProveedores = $oProveedor->get_all($conn);
$total = $oProveedor->total;
$cProveedores = $oProveedor->get_all($conn, $inicio, $tamano_pagina, "nombre");

#para el ciclo
$prov_c="SELECT * FROM proveedores ORDER BY id ASC ";
$r_prov_c=$conn->Execute($prov_c);


if(!empty($_REQUEST['btn_actualizar'])){
//echo "proceso";
#PRIMERO HAGO EL PROCESO PARA RELACION_REQ_PROV
	$up="SELECT 
	  proveedores.nombre,
	  proveedores.status,
	  proveedores.id as id_proveedor,
	  (relacion_req_prov.fecha_vcto)+prorroga AS t_prorroga,
	  relacion_req_prov.fecha_emi as fecha_emi,
	  relacion_req_prov.fecha_vcto AS fecha_vcto,
	  relacion_req_prov.prorroga,
	  relacion_req_prov.id_requisitos AS id_requisitos
	FROM
	 proveedores
	 INNER JOIN relacion_req_prov ON (proveedores.id=relacion_req_prov.id_proveedores)
	ORDER BY  relacion_req_prov.id ASC";
	//echo "principal: ".$up."<br><br>";
	 //die($up);
	$r_up=$conn->Execute($up); 
	//print $conn->ErrorMsg();


//$s='A';
	while (!$r_prov_c->EOF) { 
	$id= $r_prov_c->fields['id'];
			
			$sw='0';
			//Busca la cantidad de requisitos que tiene el grupo de proveedor
			$q_cant="SELECT COUNT( DISTINCT puser.relacion_req_gp.id_requisito) AS cantidad ";
			$q_cant.="FROM puser.relacion_provee_grupo_provee ";
			$q_cant.="Inner Join puser.relacion_req_gp ON puser.relacion_provee_grupo_provee.id_grupo_provee = puser.relacion_req_gp.id_grupo_proveedor ";
			$q_cant.="WHERE puser.relacion_provee_grupo_provee.id_provee = '$id'";
			//echo "requisitos por grupos proveedor: ".$q_cant."<br><br>";
			//die($q_cant);
			
			$r_cant= $conn->Execute($q_cant);
				$rCount= $r_cant->fields['cantidad'];
				
			//Busca la cantidad de requisitos que ha presentado el proveedor
			$q_c_prov="SELECT Count(puser.relacion_req_prov.id) AS Cant ";
			$q_c_prov.="FROM puser.relacion_req_prov ";
			$q_c_prov.="WHERE puser.relacion_req_prov.id_proveedores =  '$id'";
			//echo "cantidad requisitos proveedor ".$q_c_prov."<br><br>";
			//die($q_c_prov);
			$r_c_prov= $conn->Execute($q_c_prov);
				$rpCount= $r_c_prov->fields['cant'];
			if ($rpCount < $rCount) $sw='1';
			//die($sw);
		
			$up="SELECT proveedores.nombre, proveedores.status,  proveedores.id as id_proveedor, (relacion_req_prov.fecha_vcto)+prorroga AS t_prorroga,
			relacion_req_prov.fecha_emi as fecha_emi,  relacion_req_prov.fecha_vcto AS fecha_vcto,  relacion_req_prov.prorroga,
			relacion_req_prov.id_requisitos AS id_requisitos
			FROM proveedores
			INNER JOIN relacion_req_prov ON (proveedores.id=relacion_req_prov.id_proveedores)
			WHERE relacion_req_prov.id_proveedores=$id and (relacion_req_prov.fecha_vcto)+prorroga < '$today'
			ORDER BY  relacion_req_prov.id ASC";
			//echo "compara las fechas ".$up."<br><br>";
	 		$r_up=$conn->Execute($up); //
			
			
			$aa=$r_up->RecordCount();		
			if(($aa>=1) || ($sw=='1')){  $s='P'; }else{ $s='A'; }
				$upp="UPDATE puser.proveedores SET status='$s' WHERE id=".$id;//update status proveedor
				$conn->Execute($upp);

	$r_prov_c->MoveNext();
	}
	header("Location: lista_proveedores.php");	
}

require ("comun/header.php");
?>
<span class="titulo_maestro">Listado de Provedores por Status</span>
<br><br>
<fieldset id="buscador">
	<legend>Buscar:</legend>
	<table>
		<tr>
			<td>Nombre:</td>
			<td>
				<input type="text" name="nombre" id="nombre" />
				<input type="hidden" name="hidden_nombre" id="hidden_nombre" />
			</td>
		</tr>
	</table>
</fieldset>
<br>
<div id="busqueda">
<?
if (is_array($cProveedores) && count($cProveedores)>0)
{
?>
<table class="sortable" id="grid"  cellpadding="0" cellspacing="1">
	<tr class="cabecera">
		<td width="23">Id</td>
		<td width="328">Proveedor</td>
		<td width="37">Status</td>
	</tr>
 
	<?
	foreach($cProveedores as $proveedor)
	{
	/*while (!$r_prov->EOF)
	{*/
	?>
	<tr class="filas">
		<td><?=$proveedor->id?></td>
		<td><?=$proveedor->nombre?></td>
		<td align="center"><?=$proveedor->status?></td>
		<!--td><?=$r_prov->fields['id']?></td-->
		<!--td><?=$r_prov->fields['nombre']?></td-->
		<!--td align="center"><?=$r_prov->fields['status']?></td-->
	</tr>
	<?
		//$r_prov->MoveNext();
	}
		
	$total_paginas = ceil($total / $tamano_pagina);
	?>
	<tr class="filas">
		<td colspan="7" align="center">
	<?
	for ($j=1; $j<=$total_paginas; $j++)
		{
			if ($j==1)
				echo '<span class="actual">'.$j.'</span>';
			else
				echo '<span style="cursor:pointer" onclick="busca($(\'hidden_nombre\').value, '.$j.');"> - '.$j.'</span>';
		}
	?>
		</td>
	</tr>
	<tr class="filas">
		<td colspan="7" align="center"> Pagina <strong>1</strong> de <strong><?=$total_paginas?></strong></td>
	</tr>
</table>
<?
}
?>
</div>
<br><br>
<div style="width:647px" align="right">
<form action="lista_proveedores.php" method="post">
	<input type="submit" name="btn_actualizar" onClick="" value="Actualizar">
</form>

</div>
<div style="height:40px;padding-top:10px;">
<p id="cargando" style="display:none;margin-top:0px;">
  <img alt="Cargando" src="images/loading.gif" /> Cargando...
</p>
</div>


<script type="text/javascript">
var t;
function buscador(nombre, pagina, code)
{
	if ((code>=48 && code<=57) || (code>=96 && code<=105) || (code>=65 && code<=90) || code==8 || code==13 || code==46)
	{
		clearTimeout(t);
		$('hidden_nombre').value = nombre;
		t = setTimeout("busca('"+nombre+"', "+pagina+")", 800);
		//busca(nombre, pagina);
	}
}
	
function busca(nombre, pagina)
{
	var url = 'updater_busca_lista_proveedores.php';
	var pars = 'nombre=' + nombre + '&pagina=' + pagina + '&ms='+new Date().getTime();
	var updater = new Ajax.Updater('busqueda', 
		url,
		{
			method: 'get',
			parameters: pars,
			asynchronous:true, 
			evalScripts:true,
			onLoading:function(request){Element.show('cargando')}, 
			onComplete:function(request){Element.hide('cargando')}
		}); 
}
	
Event.observe('nombre', "keyup", function (evt) { 
     buscador($F('nombre'), 1, evt.keyCode); 
});
</script>

<? require ("comun/footer.php");?>