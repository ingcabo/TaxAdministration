<? include ("comun/ini.php"); ?>
<table width="640" border="0" >
	<tr>
		<td colspan="2" align="center"><span class="titulo">Trabajadores Disponibles</span></td>
		<td colspan="2" align="center"><span class="titulo">Trabajadores Agregados </span></td>
	</tr>
	<tr>
		<td colspan="2" align="center"><?=helpers::combonominaIV($conn, '','','width:370px;height:100px','','Trabajador1','int_cod','tra_nom','tra_ape','cargo_funcion','Trabajador1','multiple','SELECT A.int_cod, A.tra_nom,  A.tra_ape, CASE WHEN A.tra_tipo = 0 THEN 
										(SELECT B.car_nom FROM rrhh.cargo AS B WHERE B.int_cod=A.car_cod)
										ELSE 
										(SELECT B.fun_nom FROM rrhh.funciones AS B WHERE B.int_cod=A.fun_cod)
										END 
										AS cargo_funcion 
										FROM rrhh.trabajador AS A 
										INNER JOIN  rrhh.departamento AS C ON A.dep_cod = C.int_cod
										INNER JOIN rrhh.division AS D ON C.div_cod = D.int_cod  
										WHERE A.int_cod  NOT IN (SELECT tra_cod FROM rrhh.cont_tra WHERE cont_cod='.$_REQUEST['Contrato'].') AND D.emp_cod = '.$_SESSION['EmpresaL'].' AND A.tra_estatus<>4
										ORDER BY A.int_cod ','','','55')?>
		</td>
		<td colspan="2" align="center"><?=helpers::combonominaIV($conn, '','','width:370px;height:100px','','Trabajador2','int_cod','tra_nom','tra_ape','cargo_funcion','Trabajador2','multiple','SELECT A.int_cod, A.tra_nom,  A.tra_ape, CASE WHEN A.tra_tipo = 0 THEN 
										(SELECT B.car_nom FROM rrhh.cargo AS B WHERE B.int_cod=A.car_cod)
										ELSE 
										(SELECT B.fun_nom FROM rrhh.funciones AS B WHERE B.int_cod=A.fun_cod)
										END 
										AS cargo_funcion 
										FROM rrhh.trabajador AS A 
										INNER JOIN  rrhh.cont_tra AS C ON C.tra_cod = A.int_cod
										WHERE C.cont_cod = '.$_REQUEST['Contrato'].' AND A.tra_estatus<>4
										ORDER BY A.int_cod','','','55')?>
		</td>
	</tr>
	<tr>
		<td align="center"><input height=""  type="button" name="Inicio" value=" >> " onClick="Tool(1)" > </td>
		<td align="center"><input  type="button" name="Anterior"  value="  >  " onClick="Tool(2)" ></td>
		<td align="center"><input  type="button" name="Siguiente" value="  <  " onClick="Tool(3)" > </td>
		<td align="center"><input  type="button" name="Final" value=" << " onClick="Tool(4)" ></td>
	</tr>
</table>
