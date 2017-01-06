<? include ("comun/ini.php");
$q = "SELECT nrodoc FROM finanzas.cheques WHERE status = '1' ";
$q.= "ORDER BY nrodoc";

/*$q = "SELECT nrodoc FROM finanzas.otros_pagos WHERE status = '1' ";
$q.= "ORDER BY nrodoc";*/
//die($q);
$r = $conn->Execute($q);
//die(var_dump($r));
while(!$r->EOF){
	//para  cheques anulados 
	$sql = "SELECT public.asiento_cheque('".$r->fields['nrodoc']."'::varchar, 0::int2, 1111::int8)";
	//Para otros pagos anulados	
	//$sql = "SELECT public.asiento_cheque('".$r->fields['nrodoc']."'::varchar, 1::int2, 1111::int8)";
	//echo $sql."</br>";
	$conn->Execute($sql);
	$r->movenext();
} 
echo "Realizado con Exito!!!!";
?>