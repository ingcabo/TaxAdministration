<?
//include('adodb/adodb-exceptions.inc.php');
set_time_limit(0);
require ("comun/ini.php");
$sql = "SELECT nrodoc FROM puser.movimientos_presupuestarios WHERE tipdoc = '010' AND nrodoc > '010-0011' ORDER BY nrodoc DESC "; 
$row = $conn->Execute($sql);

while(!$row->EOF){
	$sql2 = "UPDATE puser.movimientos_presupuestarios SET nrodoc = '010-'|| to_char(substring(nrodoc from 5 for 4 )::int + 11,'FM0000')::char(4) WHERE nrodoc = '".$row->fields['nrodoc']."'";
	//die($sql2);
	$row2 = $conn->Execute($sql2);
	$sql3 = "UPDATE puser.relacion_movimientos SET nrodoc = '010-'|| to_char(substring(nrodoc from 5 for 4 )::int + 11,'FM0000')::char(4) WHERE nrodoc = '".$row->fields['nrodoc']."'";
	//die($sql2);
	$row3 = $conn->Execute($sql3);
	
	$sql4 = "UPDATE finanzas.solicitud_pago SET nroref = '010-'|| to_char(substring(nroref from 5 for 4 )::int + 11,'FM0000')::char(4) WHERE nroref = '".$row->fields['nrodoc']."'";
	$row4 = $conn->Execute($sql4);
	
	$sql5 = "UPDATE puser.relacion_solicitud_pago SET nroref = '010-'|| to_char(substring(nroref from 5 for 4 )::int + 11,'FM0000')::char(4) WHERE nroref = '".$row->fields['nrodoc']."'";
	$row5 = $conn->Execute($sql4);
	
	$row->movenext();
}
echo "registros modificados!!!";

?>