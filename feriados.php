<? require ("comun/ini.php"); require ("comun/header.php"); ?>
<br />
<span class="titulo_maestro">Dias Feriados</span>
<div id="formulario">
	<table width="600"  >
		<tr >
			<td width="100" >Escoja un Ano</td>
			<td >
				<select id="Ano" name="Ano" onChange="Create()">
					<? 	for($i=date('Y')-5;$i<=date('Y')+10;$i++){
							echo $i!=date('Y') ? "<option value=$i>$i</option>" : "<option value=$i selected>$i</option>";
						}
					?>
				</select>
			</td>
			<td style="cursor:pointer" onClick="SyD()" >Marcar Sabados y Domingos</td>
			<td style="cursor:pointer" onClick="Eliminar()" >Desmarcar Todos</td>
		</tr>
	</table >
	<br>
	<div id="Calendario"></div>
</div>
<div style="height:40px;padding-top:10px;">
	<p id="Procesando" style="display:none;margin-top:0px;">
		<img alt="Cargando" src="images/loading.gif" /> Cargando...
	</p>
</div>

<script language="javascript"  type="text/javascript"> 
Create();
function Create(){
	var url = 'Calendario.php';
	var pars = 'Ano=' + $('Ano').options[$("Ano").selectedIndex].value;
	var updater = new Ajax.Updater("Calendario", url,{
		parameters: pars,
		asynchronous:true, 
		evalScripts:true,
		onLoading:function(request){Element.show('Procesando');}, 
		onComplete:function(request){},
		onSuccess:function(){  
			Element.hide('Procesando');
		} 
	}); 
}
function Guardar(Fecha,id){
	JsonAux={"Forma":10,"Accion":0,"Fecha":Fecha};
	var url = 'OtrosCalculos.php';
	var pars = 'JsonEnv=' + JsonAux.toJSONString();
	var Request = new Ajax.Request(
		url,
		{
			method: 'post',
			parameters: pars,
	//		asynchronous:true, 
			onComplete:function(request){
				var JsonRec = eval( '(' + request.responseText + ')');
				if(JsonRec==1){
					$(id).style.backgroundColor="#FFD5D5";
				}
				if(JsonRec==2){
					$(id).style.backgroundColor="transparent";
				}
			}
		}
	); 
}
function Eliminar(){
	Element.show('Procesando');
	JsonAux={"Forma":10,"Accion":1,"Ano":$('Ano').options[$("Ano").selectedIndex].value};
	var url = 'OtrosCalculos.php';
	var pars = 'JsonEnv=' + JsonAux.toJSONString();
	var Request = new Ajax.Request(
		url,
		{
			method: 'post',
			parameters: pars,
	//		asynchronous:true, 
			onComplete:function(request){
				var JsonRec = eval( '(' + request.responseText + ')');
				if(JsonRec){
					Create();
					Element.hide('Procesando');
				}
			}
		}
	); 
}
function SyD(){
	Element.show('Procesando');
	JsonAux={"Forma":10,"Accion":2,"Ano":$('Ano').options[$("Ano").selectedIndex].value};
	var url = 'OtrosCalculos.php';
	var pars = 'JsonEnv=' + JsonAux.toJSONString();
	var Request = new Ajax.Request(
		url,
		{
			method: 'post',
			parameters: pars,
	//		asynchronous:true, 
			onComplete:function(request){
				var JsonRec = eval( '(' + request.responseText + ')');
				if(JsonRec){
					Create();
					Element.hide('Procesando');
				}
			}
		}
	); 
}
</script>
<? require ("comun/footer.php"); ?>