<?
include('adodb/adodb-exceptions.inc.php');
require ("comun/ini.php");
$pagina = $_REQUEST['pagina'];
if (!$pagina) {
    $inicio = 0;
    $pagina=1;
}
else {
    $inicio = ($pagina - 1) * 20;
} 
// Creando el objeto Proveedores
$oProveedores = new proveedores;
$accion = $_REQUEST['accion'];
$today=date("d/m/Y");
$rif=$_REQUEST['rif_letra']."-".$_REQUEST['rif_numero']."-".$_REQUEST['rif_cntrl'];
if($accion == 'Guardar'){
	$oProveedores->add($conn, 
							$_REQUEST['nombre'], 
							$today,
							$_REQUEST['rif_letra'], 
							$_REQUEST['rif_numero'], 
							$_REQUEST['rif_cntrl'], 
							$_REQUEST['nit'], 
							$_REQUEST['n_trabajadores'], 
							$_REQUEST['direccion'], 
							$_REQUEST['estado'], 
							$_REQUEST['municipios'], 
							$_REQUEST['parroquias'], 
							$_REQUEST['provee_contrib_munic'], 
							$_REQUEST['provee_contrat'],
							'P', 
							$_REQUEST['datos_reg'], 
							$_REQUEST['registro_const'],
							$_REQUEST['ci_representante'],
							$_REQUEST['nombre_representante'],
							$_REQUEST['contacto'], 
							$_REQUEST['accionistas'], 
							$_REQUEST['junta_directiva'],
							$_REQUEST['telefono'], 
							$_REQUEST['fax'], 
							$_REQUEST['email'], 
							$_REQUEST['ci_comisario'],
							$_REQUEST['nombre_comisario'],
							$_REQUEST['ci_responsable'],
							$_REQUEST['nombre_responsable'],
							guardaFloat($_REQUEST['cap_suscrito']),
							guardaFloat($_REQUEST['cap_pagado']), 
							$rif, 
							$_REQUEST['obj_empresa'], 
							$_REQUEST['relacion_gp'],	// Cuando este hecho el modulo decontabilidad hay que quitar este comentario
							$_REQUEST['cta_contable']);
		
}elseif($accion == 'Actualizar'){
	$oProveedores->set($conn, 
							$_REQUEST['id'], 
							$_REQUEST['nombre'],
							$_REQUEST['rif_letra'], 
							$_REQUEST['rif_numero'], 
							$_REQUEST['rif_cntrl'], 
							$_REQUEST['nit'], 
							$_REQUEST['n_trabajadores'], 
							$_REQUEST['direccion'], 
							$_REQUEST['estado'], 
							$_REQUEST['municipios'], 
							$_REQUEST['parroquias'], 
							$_REQUEST['provee_contrib_munic'], 
							$_REQUEST['provee_contrat'],
							$_REQUEST['status_proveedor'], 
							$_REQUEST['datos_reg'], 
							$_REQUEST['registro_const'],
							$_REQUEST['ci_representante'],
							$_REQUEST['nombre_representante'],
							$_REQUEST['contacto'], 
							$_REQUEST['accionistas'], 
							$_REQUEST['junta_directiva'],
							$_REQUEST['telefono'], 
							$_REQUEST['fax'], 
							$_REQUEST['email'], 
							$_REQUEST['ci_comisario'],
							$_REQUEST['nombre_comisario'],
							$_REQUEST['ci_responsable'],
							$_REQUEST['nombre_responsable'],
							guardafloat($_REQUEST['cap_suscrito']),
							guardafloat($_REQUEST['cap_pagado']),
							$rif, 
							$_REQUEST['obj_empresa'], 
							$_REQUEST['relacion_gp'],		// Cuando este hecho el modulo decontabilidad hay que quitar este comentario
							$_REQUEST['cta_contable']);
	
}elseif($accion == 'del'){
	$oProveedores->del($conn, $_REQUEST['id']);
		
}
//Seccion paginador
//echo "aqui ".$oProveedores->msj;
$cProveedores = $oProveedores->buscarProveedoresContrato($conn, '', '', '', '', $inicio, 20, "nombre");
$total = proveedores::totalRegistroContrato($conn, '', '', '', '');

require ("comun/header.php");
//		$validator->create_message("id_title", "email", "(Inv&iacute;lido)", 3); //CREO EL MENSAJE DE VALIDACION Y EL OBJETO QUE VOY A CONTROLAR
//		$validator->print_script();  //IMPRIMO EL SCRIPT
//echo "aqui ".$oProveedores->msj;
?>
<script language="javascript" type="text/javascript"> var i=0;</script>
<? if(!empty($oProveedores->msj)){ ?><div id="msj" style="display:'';"><?= $oProveedores->msj?></div><br /><? }elseif(empty($oProveedores->msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<br />
<span class="titulo_maestro">Maestro de Proveedores</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />

<fieldset id="buscador">
	<legend>Buscar:</legend>
	<table>
		<tr>
			<td>RIF:</td>
			<td width="130"><input type="text" name="busca_rif" id="busca_rif" onkeyup="buscador()" /></td>
			<td>Nombre:</td>
			<td><input type="text" name="busca_nombre" id="busca_nombre" onkeyup="buscador()" /></td>
		</tr>
	</table>
</fieldset>
<br />

<div style="margin-bottom:10px" id="busqueda">
<?
if(is_array($cProveedores))
{
?>
	<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
		<tr class="cabecera"> 
			<td width="20%">RIF</td>
			<td width="65%">Nombre</td>
			<td width="5%">&nbsp;</td>
			<td width="5%">&nbsp;</td>
			<td width="5%">&nbsp;</td>
		</tr>
<? 
	foreach($cProveedores as $Proveedores) 
	{ 
?> 
		<tr class="filas"> 
			<td><?=$Proveedores->rif?></td>
			<td><?=$Proveedores->nombre?></td>
			<td align="center"><?='<a href="imprimir_ficha.pdf.php?id_proveedores='.$Proveedores->id.'" target="_blank" title="Emitir Reporte"><img style="width:16px; height:10px" src="images/reporte.jpg" border="0" /></a>'  ?></td>
			<td align="center"><a href="?accion=del&id=<?=$Proveedores->id?>" onclick="if (confirm('Si presiona Aceptar ser&aacute; eliminada esta informaci&oacute;n')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
			<td align="center"><a href="#" onclick="updater('<?=$Proveedores->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
		</tr>
<?
	}

	$total_paginas = ceil($total / 20);
?>
		<tr class="filas">
			<td colspan="5" align="center">
		<?
			for ($j=1;$j<=$total_paginas;$j++)
			//for ($j=1;$j<=10;$j++)
			{
				if ($j==$pagina)
				{
					if ($j==1)
						echo '<span class="actual">'.$j.'</span>';
					else
						echo '<span>-'.$j.'</span>';
				}
				else
				{
					if ($j==1)
						echo '<a style="width:150px" href="proveedores.php?pagina='.$j.'">'.$j.'</a>';
					else
						echo '<a style="width:150px" href="proveedores.php?pagina='.$j.'">-'.$j.'</a>';
				}
			}
	 	?>
			</td>
		</tr>
        
		<tr class="filas">
			<td colspan="5" align="center"> Pagina <strong><?=$pagina?></strong> de <strong><?=$total_paginas?></strong></td>
		</tr>
	</table>
<? 
}
else 
	echo "No hay registros en la bd";
?>
</div>
<br />

<div style="height:40px;padding-top:10px;">
<p id="cargando" style="display:none;margin-top:0px;">
  <img alt="Cargando" src="images/loading.gif" /> Cargando...
</p>
</div>

<!-- <a href="#" onclick="alert($('formulario').innerHTML)">AAAA</a> -->
<script type="text/javascript">

var t;
var ordinario = [{ desc: 'Seleccione',valor: '0'  },{ desc: 'Proveedor',valor: 'P'  }, { desc: 'Proveedor Servicios',valor: 'S'  },{ desc: 'Contratista',valor: 'C'  },{ desc: 'Ambos', valor: 'A'},{ desc: 'Gobiernos Comunitarios', valor: 'G'}];
var formal = [{ desc: 'Ciudadano',valor: 'B'  }];

function buscador()
{
	clearTimeout(t);
	t = setTimeout("busca('"+$('busca_rif').value+"', '"+$('busca_nombre').value+"',1)", 800);
}

function busca(rif, nombre, pagina)
{
	var url  = 'updater_busca_proveedores.php';
	var pars = 'rif='+rif+'&nombre='+nombre+'&pagina='+pagina+'&ms='+new Date().getTime();
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

function agregarGP(){

		for(j=0;j<i;j++){
			
			if (mygridgp.getRowId(j)!=undefined){
				if (mygridgp.cells(mygridgp.getRowId(j),'0').getValue() == $('grupos_proveedores').value){
						
					alert('Ya se registro este grupo de proveedores');
					return false;
				}
			}
		}
		
		mygridgp.addRow(i,$('grupos_proveedores').value);
		i++;
		
	}

function eliminarGP(){
	mygridgp.deleteRow(mygridgp.getSelectedId());
	
}

function Guardar()
	{
		var JsonAux,relacion_gp=new Array;
			mygridgp.clearSelection()
			for(j=0;j<i;j++)
			{
				if(!isNaN(mygridgp.getRowId(j)))
				{
					relacion_gp[j] = new Array;
					relacion_gp[j][0]= mygridgp.cells(mygridgp.getRowId(j),0).getValue();
					relacion_gp[j][1]= mygridgp.getRowId(j);			
				}
			}
			JsonAux={"relacion_gp":relacion_gp};
			$('relacion_gp').value=JsonAux.toJSONString(); 
	}
	
	function traeMunicipios(ide, obj_idm){
		var url = 'updater_selects.php';
		var pars = 'combo=municipios&ide=' + ide + '&idm=' + obj_idm + '&ms='+new Date().getTime();
			
		var updater = new Ajax.Updater('divcombomunicipios', 
			url,
			{
				method: 'get',
				parameters: pars,
				asynchronous:true, 
				evalScripts:true,
				onLoading:function(request){Element.show('cargador_categorias')}, 
				onComplete:function(request){Element.hide('cargador_categorias')}
			});
		
	}
	
	function traeParroquias(idm, obj_idp){
		var url = 'updater_selects.php';
		var pars = 'combo=parroquias&idm=' + idm + '&idp=' + obj_idp + '&ms='+new Date().getTime();
			
		var updater = new Ajax.Updater('divcomboparroquias', 
			url,
			{
				method: 'get',
				parameters: pars,
				asynchronous:true, 
				evalScripts:true,
				onLoading:function(request){Element.show('cargador_categorias')}, 
				onComplete:function(request){Element.hide('cargador_categorias')}
			});
		
	}
	
	function selecciona_cont(tipo, seleccionado) {
		$('provee_contrat').length = 0;
		var indice;
		/*var seleccionado2 = "'"+seleccionado+"'";
		alert('selec: '+ seleccionado2);*/
		switch (seleccionado) { 
			case 'P' : indice = 1;
				break;
			case 'S' : indice = 2;
				break;
			case 'C' : indice = 3;
				break;
			case 'A' : indice = 4;
				break;
			case 'B' : indice = 0;
				break;
					}
		//alert('indice: '+indice);	
		if (tipo == 'N'){
			for(var i=0; i<ordinario.length;i++){
				$('provee_contrat').options[i] = new Option(ordinario[i].desc,ordinario[i].valor);
			}
			if(seleccionado != '0'){
					//$('provee_contrat').selectIndex = 1;
					$('provee_contrat').options[indice].selected = true   /*"'"+seleccionado+"'";*/
				}
			//$('rif_cntrl').value = '';
			$('rif_cntrl').readOnly = false;
			//$('datos_reg').value = '';
			$('datos_reg').readOnly = false;	
			
		}else{
			for(var i=0; i<formal.length;i++){
				$('provee_contrat').options[i] = new Option(formal[i].desc,formal[i].valor);
			}
			
			$('rif_cntrl').value = '0';
			$('rif_cntrl').readOnly = true;
			$('datos_reg').value = 'No Aplica';
			$('datos_reg').readOnly = true;
			
		}
		
	}
	
	function defineCuenta(tipo){
		if(tipo!='0'){
			var url = 'updater_selects.php';
			var pars = 'combo=cuentasProveedores&tipo=' + tipo + '&ctaContable=' + $('ctaContable').value + '&ms='+new Date().getTime();
					
			var updater = new Ajax.Updater('divcombocuentas', 
				url,
				{
					method: 'get',
					parameters: pars,
					asynchronous:true, 
					evalScripts:true,
					onLoading:function(request){Element.show('cargador_categorias')}, 
					onComplete:function(request){Element.hide('cargador_categorias')}
				});
		}
	}
	
	function mostrarBuscarCta(){
	//mygridcs.clearAll();
	$('bcuentas').style.display = 'inline';

}

	function traeCuentasContables(){
	var tipo;
	if($('provee_contrib_munic').value=='S')
		tipo = 3;
	var url = 'buscar_cuentas.php';
	var pars = 'id_cuenta='+$('cta_contable').value+'&tipo='+tipo+'&ms='+new Date().getTime();
		
	var Request = new Ajax.Request(
		url,
		{
			method: 'get',
			parameters: pars,
			onLoading:function(request){}, 
			onComplete:function(request){
				
				Dialog.closeInfo();
				Dialog.alert(request.responseText, {windowParameters: {width:600, height:400, 
								showEffect:Element.show,hideEffect:Element.hide,
								showEffectOptions: { duration: 1}, hideEffectOptions: { duration:1 }
								
								}});
				
				}
			}
	);     	   
}

function traeCuentasContablesDesc(){
	
	var tipo;
	if($('provee_contrib_munic').value=='S')
		tipo = 3;
	var url = 'buscar_cuentas.php';
	var pars = 'descripcion='+$('search_descrip').value+'&id_cuenta='+$('cta_contable').value+'&tipo='+tipo+'&ms='+new Date().getTime();
		
	var Request = new Ajax.Request(
		url,
		{
			method: 'get',
			parameters: pars,
			onLoading:function(request){}, 
			onComplete:function(request){
				
				Dialog.closeInfo();
				Dialog.alert(request.responseText, {windowParameters: {width:600, height:400, 
								showEffect:Element.show,hideEffect:Element.hide,
								showEffectOptions: { duration: 1}, hideEffectOptions: { duration:1 }
								
								}});
				
				}
			}
	);     	   
}

	function selDocumento(id, nombre){
	
	$('txtCuentaContable').value = nombre;
	$('cta_contable').value = id;
	Dialog.okCallback();

}

var t;

	function busca_popup()
{
	clearTimeout(t);
	t = setTimeout('traeCuentasContablesDesc()', 800);
}
</script>
<? 
$validator->create_message("error_nombre", "nombre", "*");
$validator->create_message("error_rif_letra", "rif_letra", "*");
$validator->create_message("error_rif_numero", "rif_numero", "*");
$validator->create_message("error_rif_cntrl", "rif_cntrl", "*",14);
$validator->create_message("error_cc", "cta_contable", "*");
$validator->create_message("error_direccion", "direccion", "*");
$validator->create_message("error_estado", "estado", "*");
$validator->create_message("error_municipios", "municipios", "*");
$validator->create_message("error_parroquias", "parroquias", "*");
$validator->create_message("error_provee_contrib_munic", "provee_contrib_munic", "*");
$validator->create_message("error_provee_contrat", "provee_contrat", "*");
$validator->create_message("error_datos_reg", "datos_reg", "*");
$validator->create_message("error_ci_representante", "ci_representante", "*");
$validator->create_message("error_nombre_representante", "nombre_representante", "*");
$validator->create_message("error_telefono", "telefono", "*");
//$validator->create_message("error_cap_suscrito", "cap_suscrito", "*");
//$validator->create_message("error_cap_pagado", "cap_pagado", "*");
$validator->print_script();
require ("comun/footer.php"); ?>
