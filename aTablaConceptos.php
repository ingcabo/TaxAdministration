<? require ("comun/ini.php");?>
<table border="1" width="600" bordercolor="#EAEAEA"  >
  <tr align="center" > 
    <td width="100" >Codigo</td>
    <td >Descripcion</td>
  </tr>
  <? 
	$q = "SELECT * FROM rrhh.concepto WHERE conc_estatus='0' ORDER BY int_cod";
	$RConceptos = $conn->Execute($q);
	while (!$RConceptos->EOF) { ?>
		<tr align="center"> 
    		<td onClick="Agregado(<?=$RConceptos->fields['int_cod']; ?>,<?="'".$RConceptos->fields['conc_nom']."'"; ?>)" style="cursor:pointer" ><? echo $RConceptos->fields['conc_cod']; ?></td>
    		<td onClick="Agregado(<?=$RConceptos->fields['int_cod']; ?>,<?="'".$RConceptos->fields['conc_nom']."'"; ?>)" style="cursor:pointer"><? echo $RConceptos->fields['conc_nom']; ?></td>
		</tr>
  <?
  		$RConceptos->movenext(); 
  	}  ?>
</table>
