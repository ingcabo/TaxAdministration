<? require ("comun/ini.php"); require ("comun/header.php"); ?>
<span class="titulo_maestro">Suspencion de Conceptos por Trabajador</span><br /><br />
<span class="titulo_maestro">
	Trabajador:&nbsp;<?=helpers::combonominaIII($conn, '','','','','Trabajador','int_cod','tra_nom','tra_ape','Trabajador','','SELECT A.int_cod,A.tra_nom,A.tra_ape FROM (rrhh.trabajador AS A INNER JOIN rrhh.departamento AS B ON A.dep_cod=B.int_cod) INNER JOIN rrhh.division AS C ON B.div_cod=C.int_cod WHERE  C.emp_cod='.$_SESSION['EmpresaL'].' AND A.tra_estatus<>4  ORDER BY A.int_cod','traeConceptos(this.value)','true')?>
</span>
<div id="formulario"> Seleccione un Trabajador del combo</div>
<div style="height:40px;padding-top:10px;"> 
	<p id="cargando" style="display:none;margin-top:0px;">
  		<img alt="Cargando" src="images/loading.gif" /> Cargando...
	</p>
</div>

<script type="text/javascript">
function traeConceptos(TrabajadorAux){
	var url = 'rtra_concI.php';
	var pars = 'Trabajador=' + TrabajadorAux;
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
	if($('Trabajador').options[$('Trabajador').selectedIndex].value==-1){
		alert("Debe Selecionar un Trabajador");
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
			Json={"Trabajador":parseInt($('Trabajador').options[$('Trabajador').selectedIndex].value),"Conceptos":ConceptosAux,"Accion":Accion,"Forma":4};
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
			traeConceptos($('Trabajador').options[$('Trabajador').selectedIndex].value)
		}
	}
}
<?=(!empty($msj)) ? "Effect.Appear('msj',{duration:1.4});\n" : ""?>
</script>
<? require ("comun/footer.php"); ?>
