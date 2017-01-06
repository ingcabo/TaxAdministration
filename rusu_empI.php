<? include ("comun/ini.php"); ?>
<table width="600" border="0" >
	<tr>
		<td colspan="2" align="center"><span class="titulo">Empresas Disponibles</span></td>
		<td colspan="2" align="center"><span class="titulo">Empresas Agregadas </span></td>
	</tr>
	<tr>
		<td colspan="2" align="center"><?=helpers::combonomina($conn, '','','width:220px;height:100px','','Empresa1','int_cod','emp_nom','Empresa1','multiple','SELECT int_cod,emp_nom FROM rrhh.empresa WHERE int_cod NOT IN (SELECT emp_cod FROM emp_usu WHERE usu_cod='.$_REQUEST['Usuario'].') ORDER BY int_cod','')?></td>
		<td colspan="2" align="center"><?=helpers::combonomina($conn, '','','width:220px;height:100px','','Empresa2','int_cod','emp_nom','Empresa2','multiple','SELECT A.int_cod, A.emp_nom FROM rrhh.empresa as A INNER JOIN emp_usu as B ON A.int_cod=B.emp_cod WHERE B.usu_cod='.$_REQUEST['Usuario'].' ORDER BY A.int_cod','')?></td>
	</tr>
	<tr>
		<td align="center"><input height=""  type="button" name="Inicio" value=" >> " onClick="Tool(1)" > </td>
		<td align="center"><input  type="button" name="Anterior"  value="  >  " onClick="Tool(2)" ></td>
		<td align="center"><input  type="button" name="Siguiente" value="  <  " onClick="Tool(3)" > </td>
		<td align="center"><input  type="button" name="Final" value=" << " onClick="Tool(4)" ></td>
	</tr>
</table>
