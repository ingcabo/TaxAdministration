<? require ("comun/ini.php"); require ("comun/header.php"); 
	$q = "SELECT * FROM rrhh.parametrossistema";
	$r = $conn->Execute($q);
	if(!$r->EOF){
		$Empresa=$r->fields['emp_pred'];
		$MultiEmpresa=$r->fields['multi_empresa'];
		$Enlace=$r->fields['enlace_presupuesto'];
	}
?>
<span class="titulo_maestro">Parametros del Sistema</span><br /><br />
<div id="formulario">
Sistema Multiempresa:&nbsp;<input type="checkbox" name="SM" onChange="Guardar()" id="SM" <? if($MultiEmpresa==1){ echo "checked"; } ?> > <br /><br />
Empresa Predeterminada:&nbsp;<?=helpers::combonomina($conn, 'rrhh.empresa',$Empresa,'','int_cod','Empresa','int_cod','emp_nom','Empresa','','','Guardar()');?><br /><br />
Enlazado Con Presupuesto:&nbsp;<input type="checkbox" name="SM" onChange="Guardar()" id="EP" <? if($Enlace==1){ echo "checked"; } ?> > <br /><br />
</div>
<div style="height:40px;padding-top:10px;"> 
	<p id="cargando" style="display:none;margin-top:0px;">
  		Operacion Realizada... Reinicie la aplicacion para que los cambios sean efectivos...
	</p>
</div>

<script type="text/javascript">
function Guardar(){
var JsonAux;
	if($('Empresa').selectedIndex==-1){
		alert("Debe escojer una Empresa");
	}
	Element.hide('cargando');
	JsonAux={"MultiEmpresa":$('SM').checked,"Empresa":$('Empresa').options[$('Empresa').selectedIndex].value,"Enlace":$('EP').checked,"Forma":2};
	var url = 'OtrosCalculos.php';
	var pars = 'JsonEnv=' + JsonAux.toJSONString();
	var Request = new Ajax.Request(
		url,
		{
			method: 'post',
			parameters: pars,
			asynchronous:true, 
			onComplete:function(request){Element.show('cargando');}
		}
	); 
} 
</script>
<? require ("comun/footer.php"); ?>
