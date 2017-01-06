<? require ("comun/ini.php"); require ("comun/header.php"); ?>
<span class="titulo_maestro">Relacion Grupo de Conceptos - Conceptos</span><br /><br />
<span class="titulo_maestro">
	Grupo de Conceptos:&nbsp;<?=helpers::combonomina($conn, 'rrhh.grupoconceptos', '','','int_cod','GConceptos','int_cod','gconc_nom','','','','traeConceptos(this.value)','true');?>
</span>
<div id="formulario"> Seleccione un Grupo de Conceptos del combo</div>
<div style="height:40px;padding-top:10px;"> 
	<p id="cargando" style="display:none;margin-top:0px;">
  		<img alt="Cargando" src="images/loading.gif" /> Cargando...
	</p>
</div>

<script type="text/javascript">
function traeConceptos(GConceptosAux){
	var url = 'rgconc_concI.php';
	var pars = 'GConceptos=' + GConceptosAux;
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
var ConceptosAux = new Array(), Json,Bandera=true;
	if($('GConceptos').options[$('GConceptos').selectedIndex].value==-1){
		alert("Debe Selecionar un Grupo de Conceptos");
	}else{
		var i=0;
		if(Accion==2){
			while($('Concepto1').selectedIndex!=-1){
				Bandera=false;
				ConceptosAux[i]=parseInt($('Concepto1').options[$('Concepto1').selectedIndex].value);
				$('Concepto1').options[$('Concepto1').selectedIndex].selected=false;
				i++;
			}
		}
		if(Accion==3){
			while($('Concepto2').selectedIndex!=-1){
				Bandera=false;
				ConceptosAux[i]=parseInt($('Concepto2').options[$('Concepto2').selectedIndex].value);
				$('Concepto2').options[$('Concepto2').selectedIndex].selected=false;
				i++;
			}
		}
		if(Bandera==true && (Accion==2 || Accion==3)){
			alert("Debe Selecionar al menos un Concepto");
		}else{
			Json={"GConceptos":parseInt($('GConceptos').options[$('GConceptos').selectedIndex].value),"Conceptos":ConceptosAux,"Accion":Accion,"Forma":1};
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
			traeConceptos($('GConceptos').options[$('GConceptos').selectedIndex].value)
		}
	}
}
<?=(!empty($msj)) ? "Effect.Appear('msj',{duration:1.4});\n" : ""?>
</script>
<? require ("comun/footer.php"); ?>
