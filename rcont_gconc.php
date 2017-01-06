<? require ("comun/ini.php"); require ("comun/header.php"); ?>
<span class="titulo_maestro">Relacion Nominas - Grupo de Conceptos</span><br /><br />
<span class="titulo_maestro">
	Nomina:&nbsp;<?=helpers::combonomina($conn, '', '','','','Contrato','int_cod','cont_nom','Contrato','','SELECT * FROM rrhh.contrato WHERE emp_cod='.$_SESSION['EmpresaL'].' AND cont_estatus = 0 ORDER BY int_cod','traeGConceptos(this.value)','true');?>
</span>
<div id="formulario"> Seleccione una Nomina del combo</div>
<div style="height:40px;padding-top:10px;"> 
	<p id="cargando" style="display:none;margin-top:0px;">
  		<img alt="Cargando" src="images/loading.gif" /> Cargando...
	</p>
</div>

<script type="text/javascript">
function traeGConceptos(ContratoAux){
	var url = 'rcont_gconcI.php';
	var pars = 'Contrato=' + ContratoAux;
	var updater = new Ajax.Updater('formulario', url,{
		parameters: pars,
		asynchronous:true, 
		evalScripts:true,
		onLoading:function(request){Element.show('cargando')}, 
		onComplete:function(request){Element.hide('cargando')},
		onSuccess:function(){ 
			//new Effect.Highlight('formulario', {startcolor:'#fff4f4', endcolor:'#ffffff'});
		} 
	}); 
} 
function Tool(Accion){
var GConceptosAux = new Array(), Json,Bandera=true;
	if($('Contrato').options[$('Contrato').selectedIndex].value==-1){
		alert("Debe Selecionar un Contrato");
	}else{
		var i=0;
		if(Accion==2){
			while($('GConceptos1').selectedIndex!=-1){
				Bandera=false;
				GConceptosAux[i]=parseInt($('GConceptos1').options[$('GConceptos1').selectedIndex].value);
				$('GConceptos1').options[$('GConceptos1').selectedIndex].selected=false;
				i++;
			}
		}
		if(Accion==3){
			while($('GConceptos2').selectedIndex!=-1){
				Bandera=false;
				GConceptosAux[i]=parseInt($('GConceptos2').options[$('GConceptos2').selectedIndex].value);
				$('GConceptos2').options[$('GConceptos2').selectedIndex].selected=false;
				i++;
			}
		}
		if(Bandera==true && (Accion==2 || Accion==3)){
			alert("Debe Selecionar al menos un Grupo de Conceptos");
		}else{
			Json={"Contrato":parseInt($('Contrato').options[$('Contrato').selectedIndex].value),"GConceptos":GConceptosAux,"Accion":Accion,"Forma":0};
			var url = 'GuardarRelaciones.php';
			var pars = 'JsonEnv=' + Json.toJSONString();
			var Request = new Ajax.Request(
				url,
				{
					method: 'post',
					parameters: pars,
					asynchronous:false, 
					onComplete:function(request){Element.hide('cargando')}
				}
			); 
			traeGConceptos($('Contrato').options[$('Contrato').selectedIndex].value)
		}
	}
}
<?=(!empty($msj)) ? "Effect.Appear('msj',{duration:1.4});\n" : ""?>
</script>
<? require ("comun/footer.php"); ?>
