<?
	include("comun/ini.php");
	include("Constantes.php");
	
	$id_formulacion = $_REQUEST['id_formulacion'];
	
	$oformulacion = new formulacion;
	$oformulacion->get($conn, $id_formulacion);
	
	$oformulacion->ContenidoMetas = new Services_JSON();
	$oformulacion->res_metas = $oformulacion->ContenidoMetas->decode($oformulacion->metas);
	
	$oformulacion->ContenidoGastosPersonales = new Services_JSON();
	$oformulacion->res_gastos_personal = $oformulacion->ContenidoGastosPersonales->decode($oformulacion->gastos_personal);
	
	$oformulacion->ContenidoMaterialesSuministros = new Services_JSON();
	$oformulacion->res_material_suministros = $oformulacion->ContenidoMaterialesSuministros->decode($oformulacion->mat_suminis);
	
	$oformulacion->ContenidoServiciosNoPersonales = new Services_JSON();
	$oformulacion->res_serv_no_personales = $oformulacion->ContenidoServiciosNoPersonales->decode($oformulacion->serv_no_personal);
	
	$oformulacion->ContenidoActivosReales = new Services_JSON();
	$oformulacion->res_act_reales = $oformulacion->ContenidoActivosReales->decode($oformulacion->act_reales);
	
	$oformulacion->ContenidoOtros = new Services_JSON();
	$oformulacion->res_otros = $oformulacion->ContenidoOtros->decode($oformulacion->otros);
	
	$oescenario = new escenarios;
	$oescenario->get($conn, $oformulacion->id_escenario);
	
	
//if(empty($oOrden->nrodoc))
//	header ("location: orden_servicio_trabajo.php");

//$_SESSION['pdf'] = serialize($ovehiculo);
//$_SESSION['pdf'] = serialize($oliquidacion);

class PDF extends FPDF
{
	function Header()
	{
			$this->SetLeftMargin(15);
			$this->SetFont('Courier','',6);
			$this->Ln(1);
			$this->Image ("images/logoa.jpg",15,4,26);//logo a la izquierda 
			$this->SetXY(42, 20); 
			$textoCabecera = PAIS."\n";
			$textoCabecera.= UBICACION."\n";
			$textoCabecera.= ENTE."\n";
			$textoCabecera.= DEPARTAMENTO."\n";
			$this->MultiCell(50,2, $textoCabecera, 0, 'L');

			//$ovehiculo = unserialize($_SESSION['pdf']);
			//$tipo = $ovehiculo->id_tipo_documento;

			$this->SetXY(150, 20); 
			$textoDerecha = "Fecha: ".date('d/m/Y')."\n";
			//$textoDerecha.= "Nro.:".$oOrden->nrodoc."\n";
			//$textoDerecha.= "Fecha Generac.:".muestrafecha($oOrden->fecha)."\n";
			//$textoDerecha.= "Fecha Aprob.:".muestrafecha($oOrden->fecha_aprobacion)."\n";
			$textoDerecha.= "Pag: ".$this->PageNo()." de {nb}\n";
			$this->MultiCell(50,2, $textoDerecha, 0, 'L');
			
			$this->Ln();

			$this->SetFont('Courier','b',12);
			//if($tipo == '002')
			//	$tipoOrden = "Orden de Servicio";
			//elseif($tipo == '009')
				$tipoOrden = "RESUMEN UNIDADES EJECUTORAS";
			$this->Text(60, 40, $tipoOrden);
			$this->Line(15, 41, 190, 41);
			$this->Ln(16);
			
	}

	function Footer()
	{	

		//$this->SetY(-15);
		//Arial italic 8
		$this->SetFont('Arial','I',8);
		//Número de página
		//$this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
	}
}
//Creación del objeto de la clase heredada
$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Courier','',8);
$pdf->SetLeftMargin(15);

//$ocontribuyente = new contribuyente;
//$ocontribuyente->get($conn, $ovehiculo->id_contribuyente);
//$pdf->SetFillColor(232 , 232, 232);

$pdf->Ln();$pdf->Ln();

$pdf->SetFont('Courier','B',8);
$pdf->Cell(30,4, 'UNIDAD EJECUTORA:',0, '','L' );
$pdf->SetFont('Courier','',8);
$pdf->Cell(70,4, utf8_decode(strtoupper($oformulacion->desc_ue)),0, '','L');
$pdf->Ln();
$pdf->SetFont('Courier','B',8);
$pdf->Cell(30,4, 'ESCENARIO:',0, '','L' );
$pdf->SetFont('Courier','',8);
$pdf->Cell(70,4, utf8_decode(strtoupper($oescenario->descripcion)),0, '','L');

$pdf->Ln();
$pdf->Ln(6);
$pdf->Cell(175,0.1, '',1, '','C');
$pdf->Ln(8);
$pdf->SetFont('Courier','B',10);
$pdf->Cell(155,4, 'GASTOS DE PERSONAL',0, '','C' );
$pdf->Ln(6);
$pdf->Cell(175,0.1, '',1, '','C');
$pdf->Ln();
$pdf->Ln(2);
$pdf->SetFont('Courier','B',8);
$pdf->Cell(35,4, 'CODIGO',0, '','L' );
$pdf->Cell(110,4, 'PARTIDA PRESUPUESTARIA',0, '','L' );
$pdf->Cell(40,4, 'MONTO',0, '','L' );
$pdf->Ln(4);
$pdf->Cell(175,0.1, '',1, '','C');
$pdf->Ln(4);
if(!empty($oformulacion->res_gastos_personal))
{
	foreach($oformulacion->res_gastos_personal as $res_gp)
	{
		$id_partida_presupuestaria = $res_gp[1];//die($oformulacion->id_escenario);
		$opartidas_presupuestarias = new partidas_presupuestarias; 
		$opartidas_presupuestarias->get($conn, $id_partida_presupuestaria, $oformulacion->id_escenario);
		$pdf->SetFont('Courier','',8);
		$pdf->Cell(35,4, utf8_decode($id_partida_presupuestaria),0, '','L');
		$pdf->Cell(115,4, utf8_decode($res_gp[2]),0, '','L');
		$pdf->Cell(15,4, muestrafloat($res_gp[3]) .' Bs.',0, '','R');
		$pdf->Ln();
		$total_gastos_personales +=  $res_gp[3];
	}//die("-".$total_gastos_personales);
}
else
{
	$pdf->SetFont('Courier','',8);
	$pdf->Cell(25,4, 'NO HAY REGISTROS',0, '','L');
}
$pdf->Ln();
$pdf->Cell(175,0.1, '',1, '','C');
$pdf->Ln(2);
$pdf->SetFont('Courier','B',8);
$pdf->Cell(120,4, 'TOTAL GASTOS DE PERSONAL:',0, '','R' );
$pdf->Cell(45,4, muestrafloat($total_gastos_personales).' Bs.',0, '','R');

$pdf->Ln(10);
$pdf->SetFont('Courier','B',10);
$pdf->Cell(155,4, 'MATERIALES Y SUMINISTROS',0, '','C' );
$pdf->Ln(6);
$pdf->Cell(175,0.1, '',1, '','C');
$pdf->Ln();
$pdf->Ln(2);
$pdf->SetFont('Courier','B',8);
$pdf->Cell(35,4, 'CODIGO',0, '','L' );
$pdf->Cell(110,4, 'PARTIDA PRESUPUESTARIA',0, '','L' );
$pdf->Cell(40,4, 'MONTO',0, '','L' );
$pdf->Ln(4);
$pdf->Cell(175,0.1, '',1, '','C');
$pdf->Ln(4);
if(!empty($oformulacion->res_material_suministros))
{
	foreach($oformulacion->res_material_suministros as $res_ms)
	{
		$id_partida_presupuestaria = $res_ms[1];//die($oformulacion->id_escenario);
		$opartidas_presupuestarias = new partidas_presupuestarias; 
		$opartidas_presupuestarias->get($conn, $id_partida_presupuestaria, $oformulacion->id_escenario);
		$pdf->SetFont('Courier','',8);
		$pdf->Cell(35,4, $id_partida_presupuestaria,0, '','L');
		$pdf->Cell(115,4, utf8_decode($res_ms[2]),0, '','L');
		$pdf->Cell(15,4, muestrafloat($res_ms[3]).' Bs.',0, '','R');
		$pdf->Ln();
		$total_material_suministros +=  $res_ms[3];//echo $total_material_suministros;
	}
}
else
{
	$pdf->SetFont('Courier','',8);
	$pdf->Cell(25,4, 'NO HAY REGISTROS',0, '','L');
}
$pdf->Ln();
$pdf->Cell(175,0.1, '',1, '','C');
$pdf->Ln(2);
$pdf->SetFont('Courier','B',8);
$pdf->Cell(120,4, 'TOTAL MATERIALES Y SUMINISTROS:',0, '','R' );
$pdf->Cell(45,4, muestrafloat($total_material_suministros).' Bs.',0, '','R');

$pdf->Ln(10);
$pdf->SetFont('Courier','B',10);
$pdf->Cell(155,4, 'SERVICIOS NO PERSONALES',0, '','C' );
$pdf->Ln(6);
$pdf->Cell(175,0.1, '',1, '','C');
$pdf->Ln();
$pdf->Ln(2);
$pdf->SetFont('Courier','B',8);
$pdf->Cell(35,4, 'CODIGO',0, '','L' );
$pdf->Cell(110,4, 'PARTIDA PRESUPUESTARIA',0, '','L' );
$pdf->Cell(40,4, 'MONTO',0, '','L' );
$pdf->Ln(4);
$pdf->Cell(175,0.1, '',1, '','C');
$pdf->Ln(4);
if(!empty($oformulacion->res_serv_no_personales))
{
	foreach($oformulacion->res_serv_no_personales as $res_snp)
	{
		$id_partida_presupuestaria = $res_snp[1];//die($oformulacion->id_escenario);
		$opartidas_presupuestarias = new partidas_presupuestarias; 
		$opartidas_presupuestarias->get($conn, $id_partida_presupuestaria, $oformulacion->id_escenario);
		$pdf->SetFont('Courier','',8);
		$pdf->Cell(35,4, $id_partida_presupuestaria,0, '','L');
		$pdf->Cell(115,4, utf8_decode($res_snp[2]),0, '','L');
		$pdf->Cell(15,4, muestrafloat($res_snp[3]).' Bs.',0, '','R');
		$pdf->Ln();
		$total_serv_no_personales +=  $res_snp[3];
	}
}
else
{
	$pdf->SetFont('Courier','',8);
	$pdf->Cell(25,4, 'NO HAY REGISTROS',0, '','L');
}
$pdf->Ln();
$pdf->Cell(175,0.1, '',1, '','C');
$pdf->Ln(2);
$pdf->SetFont('Courier','B',8);
$pdf->Cell(120,4, 'TOTAL SERVICIOS NO PERSONALES:',0, '','R' );
$pdf->Cell(45,4, muestrafloat($total_serv_no_personales).' Bs.',0, '','R');

$pdf->Ln(10);
$pdf->SetFont('Courier','B',10);
$pdf->Cell(155,4, 'ACTIVOS REALES',0, '','C' );
$pdf->Ln(6);
$pdf->Cell(175,0.1, '',1, '','C');
$pdf->Ln();
$pdf->Ln(2);
$pdf->SetFont('Courier','B',8);
$pdf->Cell(35,4, 'CODIGO',0, '','L' );
$pdf->Cell(110,4, 'PARTIDA PRESUPUESTARIA',0, '','L' );
$pdf->Cell(40,4, 'MONTO',0, '','L' );
$pdf->Ln(4);
$pdf->Cell(175,0.1, '',1, '','C');
$pdf->Ln(4);
if(!empty($oformulacion->res_act_reales))
{
	foreach($oformulacion->res_act_reales as $res_ar)
	{
		$id_partida_presupuestaria = $res_ar[1];//die($oformulacion->id_escenario);
		$opartidas_presupuestarias = new partidas_presupuestarias; 
		$opartidas_presupuestarias->get($conn, $id_partida_presupuestaria, $oformulacion->id_escenario);
		$pdf->SetFont('Courier','',8);
		$pdf->Cell(35,4, $id_partida_presupuestaria,0, '','L');
		$pdf->Cell(115,4, utf8_decode($res_ar[2]),0, '','L');
		$pdf->Cell(15,4, muestrafloat($res_ar[3]).' Bs.',0, '','R');
		$pdf->Ln();
		$total_activos_reales +=  $res_ar[3];
	}
}
else
{
	$pdf->SetFont('Courier','',8);
	$pdf->Cell(25,4, 'NO HAY REGISTROS',0, '','L');
}
$pdf->Ln();
$pdf->Cell(175,0.1, '',1, '','C');
$pdf->Ln(2);
$pdf->SetFont('Courier','B',8);
$pdf->Cell(120,4, 'TOTAL ACTIVOS REALES:',0, '','R' );
$pdf->Cell(45,4, muestrafloat($total_activos_reales).' Bs.',0, '','R');

$pdf->Ln(10);
$pdf->SetFont('Courier','B',10);
$pdf->Cell(155,4, 'OTROS',0, '','C' );
$pdf->Ln(6);
$pdf->Cell(175,0.1, '',1, '','C');
$pdf->Ln();
$pdf->Ln(2);
$pdf->SetFont('Courier','B',8);
$pdf->Cell(35,4, 'CODIGO',0, '','L' );
$pdf->Cell(110,4, 'PARTIDA PRESUPUESTARIA',0, '','L' );
$pdf->Cell(40,4, 'MONTO',0, '','L' );
$pdf->Ln(4);
$pdf->Cell(175,0.1, '',1, '','C');
$pdf->Ln(4);
if(!empty($oformulacion->res_otros))
{
	foreach($oformulacion->res_otros as $res_otros)
	{
		$id_partida_presupuestaria = $res_otros[1];//die($oformulacion->id_escenario);
		$opartidas_presupuestarias = new partidas_presupuestarias; 
		$opartidas_presupuestarias->get($conn, $id_partida_presupuestaria, $oformulacion->id_escenario);
		
		$pdf->SetFont('Courier','',8);
		$pdf->Cell(35,4, $id_partida_presupuestaria,0, '','L');
		$pdf->Cell(115,4, utf8_decode($res_otros[2]),0, '','L');
		$pdf->Cell(15,4, muestrafloat($res_otros[3]).' Bs.',0, '','R');
		$pdf->Ln();
		$total_otros +=  $res_otros[3];
	}
}
else
{
	$pdf->SetFont('Courier','',8);
	$pdf->Cell(25,4, 'NO HAY REGISTROS',0, '','L');
}
$total_general = $total_gastos_personales + $total_material_suministros + $total_serv_no_personales + $total_activos_reales + $total_otros;
$pdf->Ln();
$pdf->Cell(175,0.1, '',1, '','C');
$pdf->Ln(2);
$pdf->SetFont('Courier','B',8);
$pdf->Cell(120,4, 'TOTAL OTROS:',0, '','R' );
$pdf->Cell(45,4, muestrafloat($total_otros).' Bs.',0, '','R');

$pdf->Ln(10);
$pdf->SetFont('Courier','B',8);
$pdf->Cell(120,4, 'TOTAL GENERAL:',0, '','R' );
$pdf->Cell(45,4, muestrafloat($total_general).' Bs.',0, '','R');
$pdf->Output();
?>