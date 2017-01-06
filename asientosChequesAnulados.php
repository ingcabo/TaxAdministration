<? 
require ("comun/ini.php");
$q= "SELECT nrodoc FROM finanzas.cheques WHERE status = 1 AND fecha >= '2007-07-01'";
$r = $conn->Execute($q);
while(!$r->EOF){
	$sql = "SELECT public.asiento_cheque ('".$r->fields['mrodoc']."'::varchar, 0::int2, 1111::int8)";
	$row = $conn->Execute($sql);
}
echo "Proceso realizado con exito ";
?>
