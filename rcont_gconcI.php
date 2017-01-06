<? include ("comun/ini.php"); ?>
<table width="600" border="0" >
	<tr>
		<td colspan="2" align="center"><span class="titulo">Grupos de Conceptos Disponibles</span></td>
		<td colspan="2" align="center"><span class="titulo">Grupos de Conceptos Agregados </span></td>
	</tr>
	<tr>
		<td colspan="2" align="center"><?=helpers::combonomina($conn, '','','width:220px;height:100px','','GConceptos1','int_cod','gconc_nom','GConceptos1','multiple','SELECT int_cod,gconc_nom FROM rrhh.grupoconceptos WHERE int_cod NOT IN (SELECT gconc_cod FROM rrhh.cont_gconc WHERE cont_cod='.$_REQUEST['Contrato'].') ORDER BY int_cod','')?></td>
		<td colspan="2" align="center"><?=helpers::combonomina($conn, '','','width:220px;height:100px','','GConceptos2','int_cod','gconc_nom','GConceptos2','multiple','SELECT A.int_cod as int_cod, A.gconc_nom as gconc_nom FROM rrhh.grupoconceptos as A INNER JOIN rrhh.cont_gconc as B ON A.int_cod=B.gconc_cod WHERE B.cont_cod='.$_REQUEST['Contrato'].' ORDER BY A.int_cod','')?></td>
	</tr>
	<tr>
		<td align="center"><input height=""  type="button" name="Inicio" value=" >> " onClick="Tool(1)" > </td>
		<td align="center"><input  type="button" name="Anterior"  value="  >  " onClick="Tool(2)" ></td>
		<td align="center"><input  type="button" name="Siguiente" value="  <  " onClick="Tool(3)" > </td>
		<td align="center"><input  type="button" name="Final" value=" << " onClick="Tool(4)" ></td>
	</tr>
</table>
