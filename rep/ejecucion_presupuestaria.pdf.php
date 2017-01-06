<? require ("comun/ini.php");
include("constantes.php");
//- Este reporte esta basado en el reporte detallado de ejecucion presupuestaria, mucho codigo no se ha borrado del reporte original -//

$escenario = $_REQUEST['esc'];
$fechaDesde = $_REQUEST['fecha_desde'];
$fechaHasta = $_REQUEST['fecha_hasta'];
$_SESSION['_fechaDesde'] = $_REQUEST['fecha_desde'];
$_SESSION['_fechaHasta'] = $_REQUEST['fecha_hasta'];
$categoria = $_REQUEST['categoria'];
$pinicial = $_REQUEST['pinicial'];
$pfinal = $_REQUEST['pfinal'];
$unidad = $_REQUEST['id_ue'];

$oUnidad = new unidades_ejecutoras();
$oUnidad->get($conn, $unidad, $escenario);
$oCategoria = new categorias_programaticas();
$oCategoria->get($conn, $categoria, $escenario);

class PDF extends FPDF
{
//Cabecera de página
	function Header()
	{
			$this->SetLeftMargin(15);
			$this->SetFont('Courier','',8);
			$this->Ln(1);
			$this->Image ("images/logoa.jpg",15,4,26);//logo a la izquierda 
			$this->SetXY(42, 15); 
			$textoCabecera = PAIS."\n\n";
			$textoCabecera.= ENTE."\n\n";
			$textoCabecera.= ORGANISMO_NOMBRE."\n\n";
			$this->MultiCell(70,2, $textoCabecera, 0, 'L');

			
			//$tipo = $oSolicitud->id_tipo_documento;

			$this->SetXY(240, 20); 
			$textoDerecha = "Fecha: ".date('d/m/Y')."\n\n";
			$textoDerecha.= "Pag: ".$this->PageNo()." de {nb}\n";
			$this->MultiCell(50,2, $textoDerecha, 0, 'L');
			
			$this->SetFont('Courier','b',12);
			$titulo = "EJECUCIÓN GENERAL DEL PRESUPUESTO";
			$desdeHasta = utf8_decode("Desde " . $_SESSION['_fechaDesde'] . " al " . $_SESSION['_fechaHasta']);
			$this->Cell(40,4, '',0, '','C');
			$this->Cell(200,4, $titulo,0, '','C');
			$this->Ln(5);
			$this->Cell(40,4, '',0, '','C');
			$this->Cell(200,4, $desdeHasta,0, '','C');
			$this->SetFont('Courier','',12);
			$this->Line(15, 41, 280, 41);
			$this->Ln(16);
			
	}

	//Pie de página
	function Footer()
	{
		
		//$this->SetY(-15);
		//Arial italic 8
		$this->SetFont('Arial','I',8);
		//Número de página
		//$this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
	}
}

$pdf=new PDF('l');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Courier','B',8);
$pdf->SetLeftMargin(15);

$oParCat = new relacion_pp_cp;
//die($escenario);
// me traigo un array con los id_parcat de relacion_pp_cp
$cParCat = $oParCat->partidasXCategoriasRepDet($conn, $escenario, $categoria, $pinicial, $pfinal);

$control = 0;
if (is_array($cParCat)){

$pdf->Cell(0,0.2, 'CATEGORIA PROGRAMATICA:   ' . $categoria . ' ' . $oCategoria->descripcion,0, '','L');
$pdf->Ln(5);
$pdf->Cell(0,0.2, 'UNIDAD EJECUTORA:         ' . $unidad . ' ' . $oUnidad->descripcion,0, '','L');
//$pdf->Ln(5);
$pdf->Ln(2);
$pdf->Cell(265,0.2, '',1, '','C');
$pdf->Ln(2);

$pdf->SetAligns(array('R','R','R','R','R'/*,'R'*/,'R'));
$pdf->SetWidths(array(37,37,37,44,47,47/*,37*/,47));
$pdf->SetFont('Courier','B',8);
$pdf->RowNL(array('PRESUP. INICIAL', 'AUMENTOS', 'DISMINUCIONES', 'COMPROMISO', 'CAUSADO'/*, 'PAGADO'*/,'DISPONIBLE'));

$totalPptoOrigPP = 0;
$totalAumentoPP = 0;
$totalDisminucionPP = 0;
$totalCompromisoPP = 0;
$totalCausadoPP = 0;
$totalPagadoPP = 0;
$totalDisponiblePP = 0;
//Esta Variable se crea para levar control de la pocicion en que esta el recorriendo el arreglo $parcat
$contPP=0;
$ctrCausado = array();
$gpant = '400';
foreach($cParCat as $ii => $parcat){
	
	$pdf->SetAligns(array('L','L'));
	$pdf->SetWidths(array(30,100));
	$pdf->RowNL(array($parcat->id_pp, strtoupper(utf8_decode($parcat->desc_pp))));
	$pdf->Ln(1);

	// grupo de partida actual
	$gpa = substr($parcat->id_pp, 0, 3);
	
	// traigo los documentos relacionados con la partida x categoria actual 
	$cDocs = $oParCat->docsRepDet($conn,$parcat->id,$fechaDesde, $fechaHasta);
	//die(var_dump($c))
	//TRAIGO LOS MONTOS A LA FECHA DE AUMENTOS Y DISMINUCIONES PARA OBTENER LA DSPONIBILIDAD REAL A LA FECHA
	//LA FORMULA QUEDARIA DE LA SIGUIENTE MANERA: DISPONIBILIDAD = PRESUPUESTO INICIAL + AUMENTOS - DISMINUCIONES - COMPROMISOS
	$tMontos = $oParCat->docsMontoGen($conn,$parcat->id,$fechaDesde, $fechaHasta);
	#AQUI CONTAMOS LOS DOCUMENTOS E INICIALIZAMOS i EN 0 PARA SABER AL FINAL CUAL ES EL ULTIMO REGISTRO#
	$count = count($cDocs);	
	//die("aqui ".$count);
	//$i=0;
	
	//- ESTE SEGUNDO FOREACH ES PARA BUSCAR EL MONTO DE LOS DOCUMENTOS COMPROMETIDOS, CAUSADOS Y PAGADOS POR FECHA -//

	/*
		Recorro la coleccion de objetos, los que tengan momento = 1, con cada uno de los registros obtenidos   
		vuelvo a recorrer el array en busca del nroref igual al nrodoc del primer ciclo for para conseguir el causado de ese 
		compromiso, se repite el proceso para el pagado aunque puede haber varios pagados para un causado
	*/
	
	$ctrCompromiso = array();
	
	$totalCompromiso = 0;
	$totalCausado = 0;
	$totalPagado = 0;
	$causado = 0;
	$aPagado = 0;
	//echo 'actual: '.$gpa.'  anterior: '.$gpant;
	if($gpa != $gpant){
		$totalPptoOrigPP = 0;
		$totalAumentoPP = 0;
		$totalDisminucionPP = 0;
		$totalCompromisoPP = 0;
		$totalCausadoPP = 0;
		$totalPagadoPP = 0;
		$totalDisponiblePP = 0;
	}
	$gpant = $gpa;
	for($i = 0; $i < count($cDocs); $i++){
	
		// busco en todos los compromisos
		
		if($cDocs[$i]->id_momento == 1){
		
			// busco en el array
			$nroCompromiso = $cDocs[$i]->nrodoc;
			$totalCausado = 0;
			$totalPagado = 0;
				for($j = 0; $j < count($cDocs); $j++){
					
					// guardo la posicion del causados donde se referencie el compromiso
					if($cDocs[$j]->id_momento == 2 && $cDocs[$j]->nroref == $cDocs[$i]->nrodoc){
						$nroCausado = $cDocs[$j]->nrodoc;
						//echo "entro <br>";
						
						//if(!in_array($nroCausado,$ctrCausado)){
							$ctrCausado[] = $nroCausado;
							 $causado += $cDocs[$j]->monto;
							//RECORREMOS LOS PAGADOS QUE HAGAN REFERENCIA AL CAUSADO
							
							for($k = 0; $k < count($cDocs); $k++){
								// para todos los pagados donde se referencie un causado
								if($cDocs[$k]->id_momento == 3 && $cDocs[$k]->nroref == $nroCausado){
									
									$aPagado += $cDocs[$k]->monto;
								}
							}
						//}
						//echo var_dump($ctrCausado)."<br>";
						//echo "causado: ".$causado."<br>";
					}//else{ echo "no entro <br>";}
				}
			
			$totalCompromiso += $cDocs[$i]->monto;
			$totalCausado = $causado;
			$totalPagado = $aPagado;
		}
		
	}
	
	$mCausado = 0;
	$mAumentos = 0;
	$mDisminuciones = 0;
	if(is_array($tMontos)){
		foreach($tMontos as $montos){
			if($montos->id_momento == 1)
				$mCausado = $montos->monto;
			else if($montos->id_momento == 4)
				$mAumentos = $montos->monto;
			else if ($montos->id_momento == 5)
				$mDisminuciones = $montos->monto;	
		}
	}
	$mDisponible = $parcat->ppo + $mAumentos - $mDisminuciones - $mCausado;
	if(substr($cParCat[$ii+1]->id_pp, 0, 3) == $gpa){
		$pdf->SetAligns(array('R','R','R','R','R'/*,'R'*/,'R'));
		$pdf->SetWidths(array(37,37,37,44,47,47/*,37*/,47));
		$pdf->SetFont('Courier','B',8);
		$pdf->RowNL(array(muestrafloat($parcat->ppo),
							muestrafloat($mAumentos),
							muestrafloat($mDisminuciones), 
							muestrafloat($totalCompromiso),
							muestrafloat($totalCausado),
							//muestrafloat($totalPagado),
							//muestrafloat($parcat->disponible)));
							muestrafloat($mDisponible)));
		$totalPptoOrigPP += $parcat->ppo;
		$totalAumentoPP += $mAumentos;
		$totalDisminucionPP += $mDisminuciones;
		$totalCompromisoPP += $totalCompromiso;
		$totalCausadoPP += $totalCausado;
		$totalPagadoPP += $totalPagado;
		$totalDisponiblePP += $mDisponible;
	}else{
		$pdf->SetAligns(array('R','R','R','R','R'/*,'R'*/,'R'));
		$pdf->SetWidths(array(37,37,37,44,47,47/*,37*/,47));
		$pdf->SetFont('Courier','B',8);
		$pdf->RowNL(array(muestrafloat($parcat->ppo),
							muestrafloat($mAumentos),
							muestrafloat($mDisminuciones), 
							muestrafloat($totalCompromiso),
							muestrafloat($totalCausado),
							//muestrafloat($totalPagado),
							//muestrafloat($parcat->disponible)));
							muestrafloat($mDisponible)));


		$totalPptoOrigPP += $parcat->ppo;
		$totalAumentoPP += $mAumentos;
		$totalDisminucionPP += $mDisminuciones;
		$totalCompromisoPP += $totalCompromiso;
		$totalCausadoPP += $totalCausado;
		$totalPagadoPP += $totalPagado;
		$totalDisponiblePP += $mDisponible;
		
		//- TOTAL GENERAL -//
		$totalPptoOrigGen += $totalPptoOrigPP;
		$totalAumentoGen += $totalAumentoPP;
		$totalDisminucionGen += $totalDisminucionPP;
		$totalCompromisoGen += $totalCompromisoPP;
		$totalCausadoGen += $totalCausadoPP;
		$totalPagadoGen += $totalPagadoPP;
		$totalDisponibleGen += $totalDisponiblePP;

		$pdf->Ln(1);
		$pdf->SetFont('Courier','B',9);
		$pdf->SetAligns(array('L','L'));
		$pdf->SetWidths(array(50,50));
		$pdf->RowNL(array('TOTAL PARTIDA:', $gpa));

		$pdf->Cell(265,0.1, '',1, '','C');
		$pdf->Ln(2);

		$pdf->SetAligns(array('R','R','R','R','R','R','R'));
		$pdf->SetWidths(array(37,37,37,44,47,47/*,37*/,47));
		$pdf->RowNL(array(muestrafloat($totalPptoOrigPP),
							muestrafloat($totalAumentoPP),
							muestrafloat($totalDisminucionPP), 
							muestrafloat($totalCompromisoPP),
							muestrafloat($totalCausadoPP),
							//muestrafloat($totalPagadoPP),
							//muestrafloat($totalDisponiblePP)
							muestrafloat($totalPptoOrigPP+$totalAumentoPP-$totalDisminucionPP-$totalCompromisoPP)));
		$pdf->Cell(265,0.1, '',1, '','C');
		$pdf->Ln(2);
	}


//	if( ($ii+1) < count($cParCat) )
//		$pdf->AddPage();

}
//echo var_dump($ctrCausado)."<br>";


		$pdf->Ln(1);
		$pdf->SetFont('Courier','B',9);
		$pdf->SetAligns(array('L'));
		$pdf->SetWidths(array(50));
		$pdf->RowNL(array('TOTAL GENERAL:'));

		$pdf->Cell(265,0.1, '',1, '','C');
		$pdf->Ln(2);

		$pdf->SetAligns(array('R','R','R','R','R','R','R'));
		$pdf->SetWidths(array(37,37,37,44,47,47/*,37*/,47));
		$pdf->RowNL(array(muestrafloat($totalPptoOrigGen),
							muestrafloat($totalAumentoGen),
							muestrafloat($totalDisminucionGen), 
							muestrafloat($totalCompromisoGen),
							muestrafloat($totalCausadoGen),
							//muestrafloat($totalPagadoGen),
							//muestrafloat($totalDisponibleGen)
							muestrafloat($totalPptoOrigGen+$totalAumentoGen-$totalDisminucionGen-$totalCompromisoGen)));
		$pdf->Cell(265,0.1, '',1, '','C');

}else{
	$pdf->ln(10);
	$pdf->SetFont('Courier','B',15);
	$pdf->Cell(200,4, 'El Reporte no tiene Datos para mostrar',0, '','R' );


}
//die();	
$pdf->Output();
?>
