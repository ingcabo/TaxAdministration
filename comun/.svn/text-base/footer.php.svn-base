<script type="text/javascript">
function updater(id){
	var url = 'updater.php';
	var pars;

	if(id != ''){
		pars = 'form=<?=nombre(self($_SERVER['PHP_SELF']))?>&id=' + id +'&ms='+new Date().getTime();
	}else{
		pars = 'form=<?=nombre(self($_SERVER['PHP_SELF']))?>'+'&ms='+new Date().getTime();
	}
	var updater = new Ajax.Updater('formulario', url,{
		parameters: pars,
		asynchronous:true, 
		evalScripts:true,
		onLoading:function(request){Element.show('cargando')}, 
		onComplete:function(request){Element.hide('cargando')},
		onSuccess:function(){
			$('msj').style.display= "none";  
		//	new Effect.Highlight('formulario', {startcolor:'#ffffff', endcolor:'#FFF4F4'});
		} 
	}); 
}
function close_div(){
	$('formulario').innerHTML = '<a href="#" onclick="updater(0)">Agregar Nuevo Registro<'+'/'+'a>';
	
}
function muestraMenu(elemento){
	var menu = new Effect.toggle(elemento, 'blind');
	var estilo = Element.getStyle('li_' + elemento, 'background');
	if (estilo == 'transparent url(images/vinetas.gif) no-repeat scroll 2px 2px' || estilo == ''){
		Element.setStyle('li_' + elemento, {background: 'url(images/vinetas_min.gif) no-repeat 2px 2px'});
	}else{
		Element.setStyle('li_' + elemento, {background: 'url(images/vinetas.gif) no-repeat 2px 2px'});
	}
}
<? //(!empty($msj)) ? "Effect.Appear('msj',{duration:1.2});\n" : ""?>
</script>

	</div>
	<div id="pie"><?=date('Y')?></div>
</div>

</body>
</html>