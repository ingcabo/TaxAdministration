<?
	include('adodb/adodb-exceptions.inc.php'); 
	require ('lib/config.php'); 

		$id = $_REQUEST['id'];
		$q = "SELECT
				DISTINCT ON (puser.partidas_presupuestarias.descripcion)
				puser.partidas_presupuestarias.descripcion
			FROM
				puser.partidas_presupuestarias		
			INNER JOIN 
				puser.relacion_pp_cp	
			ON 
				puser.relacion_pp_cp.id_partida_presupuestaria = puser.partidas_presupuestarias.id
			WHERE
				puser.relacion_pp_cp.id_partida_presupuestaria = '$id'";
		$r = $conn->Execute($q);
		echo strtoupper($r->fields['descripcion']);
	 
?>
