<?
include ("comun/ini.php");
require('comun/header_popup.php');
$campo = $_REQUEST['campo'];
switch($campo){
	case 'dep_cod':
		$q='SELECT int_cod AS id ,dep_nom AS descripcion FROM rrhh.departamento ';
		$r = $conn->Execute($q);
		$titulo = 'Departamentos';
		break;
	case 'car_cod':
		$q='SELECT int_cod AS id ,car_nom AS descripcion FROM rrhh.cargo ';
		$r = $conn->Execute($q);
		$titulo = 'Cargos';
		break;
	case 'ban_cod':
		$q='SELECT id ,descripcion FROM public.banco ';
		$r = $conn->Execute($q);
		$titulo = 'Bancos';
		break;
	case 'cont_cod':
		$q='SELECT int_cod AS id ,cont_nom AS descripcion FROM rrhh.contrato ';
		$r = $conn->Execute($q);
		$titulo = 'Contratos';
		break;
	case 'tra_estatus':
		$q="SELECT 0 AS id, 'Activo' AS descripcion UNION SELECT 1 AS id, 'Vacaciones' AS descripcion ";
		$q.=" UNION SELECT 2 AS id, 'Reposo' AS descripcion UNION SELECT 3 AS id, 'Por Egresar' AS descripcion ";
		$q.=" UNION SELECT 4 AS id, 'Egresado' AS descripcion UNION SELECT 5 AS id, 'Inactivo' AS descripcion ";
		$r = $conn->Execute($q);
		$titulo = 'Estatus de Trabajadores';
		break;
	case 'tra_sex':
		$q="SELECT 0 AS id, 'Masculino' AS descripcion UNION SELECT 1 AS id, 'Femenino' AS descripcion ";
		$r = $conn->Execute($q);
		$titulo = 'Sexo del Trabajador';
		break;
	case 'tra_nac':
		$q="SELECT 0 AS id, 'Venezolano' AS descripcion UNION SELECT 1 AS id, 'Extranjero' AS descripcion ";
		$r = $conn->Execute($q);
		$titulo = 'Sexo del Trabajador';
		break;
	case 'tra_tip_pag':
		$q="SELECT 0 AS id, 'Efectivo' AS descripcion UNION SELECT 1 AS id, 'Cheque' AS descripcion ";
		$q.=" UNION SELECT 2 AS id, 'Deposito' AS descripcion ";
		$r = $conn->Execute($q);
		$titulo = 'Tipos de Pagos';
		break;
	case 'tra_tipo_cta':
		$q="SELECT 0 AS id, 'Corriente' AS descripcion UNION SELECT 1 AS id, 'De Ahorro' AS descripcion ";
		$r = $conn->Execute($q);
		$titulo = 'Tipos de Cuentas';
		break;
	case 'conc_tipo':
		$q="SELECT 0 AS id, 'Asignacion' AS descripcion UNION SELECT 1 AS id, 'Deduccion' AS descripcion ";
		$q.=" UNION SELECT 2 AS id, 'Acumulado' AS descripcion  ";
		$r = $conn->Execute($q);
		$titulo = 'Tipos de Conceptos';
		break;
	case 'conc_estatus':
		$q="SELECT 0 AS id, 'Activo' AS descripcion UNION SELECT 1 AS id, 'Inactivo' AS descripcion ";
		$r = $conn->Execute($q);
		$titulo = 'Estatus de Conceptos';
		break;
	case 'var_tipo':
		$q="SELECT 0 AS id, 'Fija' AS descripcion UNION SELECT 1 AS id, 'No Fija' AS descripcion ";
		$r = $conn->Execute($q);
		$titulo = 'Tipos de Variables';
		break;
	case 'cont_tipo':
		$q="SELECT 0 AS id, 'Semanal' AS descripcion UNION SELECT 1 AS id, 'Quincenal' AS descripcion ";
		$q.=" UNION SELECT 2 AS id, 'Mensual' AS descripcion UNION SELECT 3 AS id, 'Otros' AS descripcion ";
		$r = $conn->Execute($q);
		$titulo = 'Tipos de Contratos';
		break;
	case 'dep_estatus':
		$q="SELECT 0 AS id, 'Activo' AS descripcion UNION SELECT 1 AS id, 'Inactivo' AS descripcion ";
		$r = $conn->Execute($q);
		$titulo = 'Estatus de Departamentos';
		break;
	case 'div_cod':
		$q='SELECT int_cod AS id ,div_nom AS descripcion FROM rrhh.division ';
		$r = $conn->Execute($q);
		$titulo = 'Divisiones';
		break;
	case 'uni_cod':
		$q='SELECT id ,descripcion FROM puser.unidades_ejecutoras ';
		$r = $conn->Execute($q);
		$titulo = 'Unidades Ejecutoras';
		break;
}
if(!$r->EOF){ ?>
	<form name="form1" id="form1" method="post">
	<table class="sortable" id="grid" cellpadding="0" cellspacing="1" width="600">
		<tr class="cabecera" align="center"> 
			<td  colspan="2" ><span class="titulo_maestro"><?=$titulo?></span><br><br></td>
		</tr>
		<tr class="cabecera" align="center"> 
			<td width="50px">Selecionar</td>
			<td>Descripcion</td>
		</tr>
		<? while(!$r->EOF){ ?> 
			<tr class="filas" align="center">
				<td><input type="checkbox" name="id" value="<?=$r->fields['id']?>" />	</td>
				<td ><?=$r->fields['descripcion']?></td>
			</tr>
		<? $r->movenext();
			} ?>
		<tr class="cabecera" align="center" > 
			<td  colspan="2"><br><br></td>
		</tr>
		<tr class="cabecera" align="center"> 
			<td colspan="2" ><input type="button" onClick="cerrar_ventana();" value="Ok" /></td>
		</tr>
	</table>
	</form>
	<br><br>
<? } else {
	echo "No se encontraron registros";
} ?>
<script>
function cerrar_ventana() {  
var values=new Array(),value=-1; 
	var ids = document.getElementsByName("id");
	var cont = 0;
	for(i=0;i<ids.length;i++){
		if(ids[i].checked){
			values[cont]=ids[i].value;
			cont++
		}
	} 
	if(cont!=0){
		value=values.join(); 
	}
	window.opener.document.getElementById('ids').value=value;
	window.opener.document.getElementById('copia_ids').click();
	window.close();
} 
</script>