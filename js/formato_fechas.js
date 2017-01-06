function formato_fecha(obj){
var var1;

largo = obj.value.length;
reg = /[^0-9:]/g;

if (largo==1 || largo==3 )
{			
	obj.value =  obj.value.replace(reg,"");
}
if (largo==2)
{	  if (obj.value.replace(reg,""))
		obj.value =  obj.value.replace(reg,"")+'/';
	  else
		obj.value =  obj.value.replace(reg,"");	
}
if (largo==4)
{ 	
	mes=obj.value.substr(0,3);
	var1=obj.value.substr(3);
	var1=var1.replace(reg,"");
	obj.value=mes+var1;
}
if (largo==5)
{ 	
	mes=obj.value.substr(0,4);
	var1=obj.value.substr(4);
	var1=var1.replace(reg,"");
	if (var1.replace(reg,""))
		obj.value=mes+var1+'/';
	else
		obj.value=mes+var1;
}
if (largo==7)
{ 	
	mes=obj.value.substr(0,6);
	var1=obj.value.substr(6);
	var1=var1.replace(reg,"");
	obj.value=mes+var1;
}
if (largo==8)
{ 	
	mes=obj.value.substr(0,7);
	var1=obj.value.substr(7);
	var1=var1.replace(reg,"");
	obj.value=mes+var1;
}
if (largo==9)
{ 	
	mes=obj.value.substr(0,8);
	var1=obj.value.substr(8);
	var1=var1.replace(reg,"");
	obj.value=mes+var1;
}
if (largo==10)
{ 	
	mes=obj.value.substr(0,9);
	var1=obj.value.substr(9);
	var1=var1.replace(reg,"");
	obj.value=mes+var1;
}

}
function valida_fecha(campo)
{
	dia_mes=campo.value.substr(0,6);
	ano=campo.value.substr(6);
	if (ano.length <= 2)
		campo.value=dia_mes+'20'+ano;
	else
		campo.value=dia_mes+ano;
dia=parseInt(campo.value.substr(0,2));
mes=parseInt(campo.value.substr(3,2));	
ano=parseInt(campo.value.substr(6));
	
if (dia > 31 || mes >12 )
{	alert("El formato de la fecha es invalido, el correcto es 'DD/MM/AAAA'");
	campo.focus();
}

		
}
