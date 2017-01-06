<? include ("comun/ini.php"); ?>
<table width="600" border="0" >
	<tr>
		<td colspan="2" align="center"><span class="titulo">Conceptos Activos</span></td>
		<td colspan="2" align="center"><span class="titulo">Conceptos Suspendidos </span></td>
	</tr>
	<tr>
		<td colspan="2" align="center"><?=helpers::combonomina($conn, '','','width:220px;height:100px','','Concepto1','int_cod','conc_nom','Concepto1','multiple','SELECT int_cod,conc_nom FROM rrhh.concepto WHERE int_cod NOT IN (SELECT conc_cod FROM rrhh.tra_conc WHERE tra_cod='.$_REQUEST['Trabajador'].') ORDER BY int_cod','')?></td>
		<td colspan="2" align="center"><?=helpers::combonomina($conn, '','','width:220px;height:100px','','Concepto2','int_cod','conc_nom','Concepto2','multiple','SELECT A.int_cod as int_cod, A.conc_nom as conc_nom FROM rrhh.concepto as A INNER JOIN rrhh.tra_conc as B ON A.int_cod=B.conc_cod WHERE B.tra_cod='.$_REQUEST['Trabajador'].' ORDER BY A.int_cod','')?></td>
	</tr>
	<tr>
		<td align="center"><input height=""  type="button" name="Inicio" value=" >> " onClick="Tool(1)" > </td>
		<td align="center"><input  type="button" name="Anterior"  value="  >  " onClick="Tool(2)" ></td>
		<td align="center"><input  type="button" name="Siguiente" value="  <  " onClick="Tool(3)" > </td>
		<td align="center"><input  type="button" name="Final" value=" << " onClick="Tool(4)" ></td>
	</tr>
</table>
