<? require ("comun/ini.php"); require ("comun/header.php"); ?>
<span class="titulo_maestro">Suspencion de Conceptos por Conceptos</span><br /><br />
<span class="titulo_maestro">
	Concepto:&nbsp;<?=helpers::combonomina($conn, '', '','','','Concepto','int_cod','conc_nom','Concepto','','SELECT * FROM rrhh.Concepto ','traeTrabajadores(this.value)','true');?>
</span>
<div id="formulario"> Seleccione un Concepto del combo</div>
<div style="height:40px;padding-top:10px;"> 
	<p id="cargando" style="display:none;margin-top:0px;">
  		<img alt="Cargando" src="images/loading.gif" /> Cargando...
	</p>
</div>

<script type="text/javascript">
function traeTrabajadores(ConceptoAux){
	var url = 'rconc_traI.php';
	var pars = 'Concepto=' + ConceptoAux;
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
var TrabajadoresAux = new Array(), Json,Bandera=true;
	if($('Concepto').options[$('Concepto').selectedIndex].value==-1){
		alert("Debe Selecionar un Concepto");
	}else{
		var i=0;
		if(Accion==2){
			while($('Trabajador1').selectedIndex!=-1){
				Bandera=false;
				TrabajadoresAux[i]=parseInt($('Trabajador1').options[$('Trabajador1').selectedIndex].value);
				$('Trabajador1').options[$('Trabajador1').selectedIndex].selected=false;
				i++;
			}
		}
		if(Accion==3){
			while($('Trabajador2').selectedIndex!=-1){
				Bandera=false;
				TrabajadoresAux[i]=parseInt($('Trabajador2').options[$('Trabajador2').selectedIndex].value);
				$('Trabajador2').options[$('Trabajador2').selectedIndex].selected=false;
				i++;
			}
		}
		if(Bandera==true && (Accion==2 || Accion==3)){
			alert("Debe Selecionar al menos un Trabajador");
		}else{
			Json={"Concepto":parseInt($('Concepto').options[$('Concepto').selectedIndex].value),"Trabajadores":TrabajadoresAux,"Accion":Accion,"Forma":5};
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
			traeTrabajadores($('Concepto').options[$('Concepto').selectedIndex].value)
		}
	}
}
<?=(!empty($msj)) ? "Effect.Appear('msj',{duration:1.4});\n" : ""?>
</script>
<? require ("comun/footer.php"); ?>
