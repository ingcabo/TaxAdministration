<?
	include('adodb/adodb-exceptions.inc.php'); 
	require ('lib/config.php'); 

		$id = $_REQUEST['id'];
		$cat= $_REQUEST['cat'];
		$escenario= $_REQUEST['escenario'];
		$q = "SELECT estimacion
			FROM rrhh.conc_part
			WHERE
				par_cod = '$id' AND cat_cod = '$cat' AND escenario = $escenario" ;
		$r = $conn->Execute($q);
		echo muestrafloat($r->fields['estimacion'],2);
	 
?>
