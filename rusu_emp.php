<? require ("comun/ini.php"); require ("comun/header.php"); ?>
<span class="titulo_maestro">Relacion Usuario - Empresas</span><br /><br />
<span class="titulo_maestro">
	Empresa:&nbsp;<?=helpers::combonominaIII($conn, 'usuarios', '','','id','Usuario','id','nombre','apellido','Usuario','','','traeEmpresas(this.value)','true');?>
</span>
<div id="formulario"> Seleccione una Empresa del combo</div>
<div style="height:40px;padding-top:10px;"> 
	<p id="cargando" style="display:none;margin-top:0px;">
  		<img alt="Cargando" src="images/loading.gif" /> Cargando...
	</p>
</div>

<script type="text/javascript">
function traeEmpresas(UsuarioAux){
	var url = 'rusu_empI.php';
	var pars = 'Usuario=' + UsuarioAux;
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
var EmpresaAux = new Array(), Json,Bandera=true;
	if($('Usuario').options[$('Usuario').selectedIndex].value==-1){
		alert("Debe Selecionar un Usuario");
	}else{
		var i=0;
		if(Accion==2){
			while($('Empresa1').selectedIndex!=-1){
				Bandera=false;
				EmpresaAux[i]=parseInt($('Empresa1').options[$('Empresa1').selectedIndex].value);
				$('Empresa1').options[$('Empresa1').selectedIndex].selected=false;
				i++;
			}
		}
		if(Accion==3){
			while($('Empresa2').selectedIndex!=-1){
				Bandera=false;
				EmpresaAux[i]=parseInt($('Empresa2').options[$('Empresa2').selectedIndex].value);
				$('Empresa2').options[$('Empresa2').selectedIndex].selected=false;
				i++;
			}
		}
		if(Bandera==true && (Accion==2 || Accion==3)){
			alert("Debe Selecionar al menos una Empresa");
		}else{
			Json={"Usuario":parseInt($('Usuario').options[$('Usuario').selectedIndex].value),"Empresas":EmpresaAux,"Accion":Accion,"Forma":3};
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
			traeEmpresas($('Usuario').options[$('Usuario').selectedIndex].value)
		}
	}
}
<?=(!empty($msj)) ? "Effect.Appear('msj',{duration:1.4});\n" : ""?>
</script>
<? require ("comun/footer.php"); ?>
