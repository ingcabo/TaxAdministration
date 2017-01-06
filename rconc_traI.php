<? include ("comun/ini.php"); ?>
<table width="600" border="0" >
	<tr>
		<td colspan="2" align="center"><span class="titulo">Trabajadores Disponibles</span></td>
		<td colspan="2" align="center"><span class="titulo">Trabajadores Agregados </span></td>
	</tr>
	<tr>
		<td colspan="2" align="center"><?=helpers::combonominaIII($conn, '','','width:220px;height:100px','','Trabajador1','int_cod','tra_nom','tra_ape','Trabajador1','multiple','SELECT A.int_cod,A.tra_nom,A.tra_ape FROM (rrhh.trabajador AS A INNER JOIN rrhh.departamento AS B ON A.dep_cod=B.int_cod) INNER JOIN rrhh.division AS C ON B.div_cod=C.int_cod WHERE A.int_cod NOT IN (SELECT tra_cod FROM rrhh.tra_conc WHERE conc_cod='.$_REQUEST['Concepto'].') AND C.emp_cod='.$_SESSION['EmpresaL'].' AND A.tra_estatus<>4  ORDER BY A.int_cod','')?></td>
		<td colspan="2" align="center"><?=helpers::combonominaIII($conn, '','','width:220px;height:100px','','Trabajador2','int_cod','tra_nom','tra_ape','Trabajador2','multiple','SELECT A.int_cod,A.tra_nom,A.tra_ape FROM rrhh.trabajador as A INNER JOIN rrhh.tra_conc as B ON A.int_cod=B.tra_cod WHERE B.conc_cod='.$_REQUEST['Concepto'].' AND A.tra_estatus<>4 ORDER BY A.int_cod','')?></td>
	</tr>
	<tr>
		<td align="center"><input height=""  type="button" name="Inicio" value=" >> " onClick="Tool(1)" > </td>
		<td align="center"><input  type="button" name="Anterior"  value="  >  " onClick="Tool(2)" ></td>
		<td align="center"><input  type="button" name="Siguiente" value="  <  " onClick="Tool(3)" > </td>
		<td align="center"><input  type="button" name="Final" value=" << " onClick="Tool(4)" ></td>
	</tr>
</table>
