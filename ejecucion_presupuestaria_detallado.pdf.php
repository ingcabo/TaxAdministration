<? require ("comun/ini.php");
  include("Constantes.php");

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

function array_count_values_multidim($a,$index,$out=false) {
  if ($out===false) $out=array();
  if (is_array($a)) {
    foreach($a as $e)
      $out=array_count_values_multidim($e[$index],$index,$out);
  }
  else {
    if (array_key_exists($a,$out))
      $out[$a]++;
    else
      $out[$a]=1;
  }
  return $out;
} 

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
			$textoCabecera.= UBICACION."\n\n";
			$textoCabecera.= ENTE."\n\n";
			$this->MultiCell(70,2, $textoCabecera, 0, 'L');

			
			//$tipo = $oSolicitud->id_tipo_documento;

			$this->SetXY(240, 20); 
			$textoDerecha = "Fecha: ".date('d/m/Y')."\n\n";
			$textoDerecha.= "Pag: ".$this->PageNo()." de {nb}\n";
			$this->MultiCell(50,2, $textoDerecha, 0, 'L');
			
			$this->SetFont('Courier','b',12);
			$titulo = "EJECUCIÓN DETALLADA DEL PRESUPUESTO";
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
foreach($cParCat as $ii => $parcat){

	$pdf->Cell(0,0.2, 'CATEGORIA PROGRAMATICA:   ' . $categoria . ' ' . $oCategoria->descripcion,0, '','L');
	$pdf->Ln(5);
	$pdf->Cell(0,0.2, 'UNIDAD EJECUTORA:         ' . $unidad . ' ' . $oUnidad->descripcion,0, '','L');
	$pdf->Ln(5);
	$pdf->SetAligns(array('R','R','R','R','R','R','R','R','R','R'));
	$pdf->SetWidths(array(26,26,26,26,26,26,26,26,26,26));
	$pdf->SetFont('Courier','B',8);
	$pdf->RowNL(array('Partida Presup.', '', 'Compromiso', '', '', 'Causado', '', '', 'Pagado',''));
	$pdf->RowNL(array('','Documento','Fecha', 'Monto','Documento','Fecha', 'Monto','Documento','Fecha', 'Monto'));

$pdf->Cell(265,0.2, '',1, '','C');
$pdf->Ln(2);

	$pdf->SetAligns(array('L','L'));
	$pdf->SetWidths(array(30,100));
	$pdf->RowNL(array($parcat->id_pp, strtoupper(utf8_decode($parcat->desc_pp))));
	$pdf->Ln(2);

	// traigo los documentos relacionados con la partida x categoria actual 
	$cDocs = $oParCat->docsRepDet($conn,$parcat->id,$fechaDesde, $fechaHasta);
	//TRAIGO LOS MONTOS A LA FECHA DE AUMENTOS Y DISMINUCIONES PARA OBTENER LA DSPONIBILIDAD REAL A LA FECHA
	//LA FORMULA QUEDARIA DE LA SIGUIENTE MANERA: DISPONIBILIDAD = PRESUPUESTO INICIAL + AUMENTOS - DISMINUCIONES - COMPROMISOS
	$tMontos = $oParCat->docsMontoGen($conn,$parcat->id,$fechaDesde, $fechaHasta);
	#AQUI CONTAMOS LOS DOCUMENTOS E INICIALIZAMOS i EN 0 PARA SABER AL FINAL CUAL ES EL ULTIMO REGISTRO#
	$count = count($cDocs);	
	//$i=0;
	
	$pdf->SetAligns(array('R','R','R','R','R','R','R','R','R','R'));
	$pdf->SetWidths(array(26,26,26,26,26,26,26,26,26,26));

	#ESTE SEGUNDO FOREACH ES PARA MOSTRAR EL MONTO, FECHA Y NRO DEL DOCUMENTO#
	/*
		Recorro la coleccion de objetos, los que tengan momento = 1, con cada uno de los registros obtenidos   
		vuelvo a recorrer el array en busca del nroref igual al nrodoc del primer ciclo for para conseguir el causado de ese 
		compromiso, se repite el proceso para el pagado aunque puede haber varios pagados para un causado
	*/
	$totalCompromiso = 0;
	$totalCausado = 0;
	$totalPagado = 0;
	
	$controlCompromiso = 0;
	$aCompromiso = array();
	$aCompromiso[$controlCompromiso] = array();
	
	
	$controlCausado = 0;
	$causado = array();
	$causado[$controlCausado] = array();
	
	$controlPagado = 0;
	$aPagado = array();
	$aPagado[$controlPagado] = array();
	
	for($i = 0; $i < count($cDocs); $i++){
		// busco en todos los compromisos
		$strNroRef = explode('-',$cDocs[$i]->nroref);
		$auxNroRef = $strNroRef[2]; 
		if($cDocs[$i]->id_momento == 1 && $auxNroRef != 'ANULADO'){
			$aCompromiso[$controlCompromiso][0] = $cDocs[$i]->nrodoc;
			$aCompromiso[$controlCompromiso][1] = $cDocs[$i]->fechadoc;
			$aCompromiso[$controlCompromiso][2] = $cDocs[$i]->monto;
			$aCompromiso[$controlCompromiso][3] = $cDocs[$i]->nroref;
			$aCompromiso[$controlCompromiso][4] = $cDocs[$i]->status;
			$aCompromiso[$controlCompromiso][5] = $cDocs[$i]->proveedor;
			$controlCompromiso++;
		}elseif($cDocs[$i]->id_momento == 2){
			$causado[$controlCausado][0] = $cDocs[$i]->nrodoc;
			$causado[$controlCausado][1] = $cDocs[$i]->fechadoc;
			$causado[$controlCausado][2] = $cDocs[$i]->monto;
			$causado[$controlCausado][3] = $cDocs[$i]->nroref;
			$causado[$controlCausado][4] = $cDocs[$i]->status;
			$controlCausado++;
		}elseif($cDocs[$i]->id_momento == 3){	
			$aPagado[$controlPagado][0] = $cDocs[$i]->nrodoc;
			$aPagado[$controlPagado][1] = $cDocs[$i]->fechadoc;
			$aPagado[$controlPagado][2] = $cDocs[$i]->monto;
			$aPagado[$controlPagado][3] = $cDocs[$i]->nroref;
			$aPagado[$controlPagado][4] = $cDocs[$i]->status;
			$controlPagado++;
		}
	}			
		
		//SE BUSCA CUANTAS VECES SE REPITE UN NUMERO DE DOCUMENTO EN CADA VECTOR	
		//$arCompDoc = array_count_values_multidim($aCompromiso,0);
		//echo(print_r($arCompDoc))."<br>";
		//$arCauDoc = array_count_values_multidim($causado,0);
		//echo(print_r($arCauDoc))."<br>";
		$arCauRef = array_count_values_multidim($causado,3);
		//echo(print_r($arCauRef))."<br>";
		//$arPagDoc = array_count_values_multidim($aPagado,0);
		//echo(print_r($arPagDoc))."<br>";
		$arPagRef = array_count_values_multidim($aPagado,3);
		//echo(print_r($arPagRef))."<br>";
		//SE RECORRE EL CICLO DE LOS COMPROMISOS 
		$antComp = '0';
		for($i=0;$i<count($aCompromiso);$i++){
			if(!empty($aCompromiso[$i][0])){
				$indCom = 0;
				//echo "comp: ".$aCompromiso[$i][0]."<br>";
				//echo "ref cau: ".$antComp."<br>";
				//&& $antComp != $aCompromiso[$i][0]
				//echo "arCauRef ".$arCauRef[$aCompromiso[$i][0]]."<br>";
				$auxCau = $arCauRef[$aCompromiso[$i][0]];
				if($auxCau>0 && $antComp != $aCompromiso[$i][0]){
					//$antCau = $aCompromiso[$i][]
					$indCau = 0;
					for($j=0;$j<count($causado);$j++){
						
						if($causado[$j][3]==$aCompromiso[$i][0]){
							$antComp = $causado[$j][3]; 
							$auxPag = $arPagRef[$causado[$j][0]];
							if($auxPag>0){
								$indPag = 0;
								for($l=0;$l<count($aPagado);$l++){
									if($aPagado[$l][3]==$causado[$j][0]){
										if($indPag==0){
											$ar = array($aCompromiso[$i][5],
												$aCompromiso[$i][0], 
												muestrafecha($aCompromiso[$i][1]), 
												muestrafloat($aCompromiso[$i][2]),
												$causado[$j][0],
												muestrafecha($causado[$j][1]),
												muestrafloat($causado[$j][2]),
												$aPagado[$l][0], 
												muestrafecha($aPagado[$l][1]), 
												muestrafloat($aPagado[$l][2]));
												$pdf->RowNL($ar);
												$pdf->Ln(2);
												$totalCompromiso += $aCompromiso[$i][2];
												$totalCausado += $causado[$j][2];
												$totalPagado += $aPagado[$l][2];
											}else{
												$ar = array('',
													'', 
													'', 
													'', 
													'', 
													'', 
													'', 
													$aPagado[$l][0], 
													muestrafecha($aPagado[$l][1]), 
													muestrafloat($aPagado[$l][2]));
													$pdf->RowNL($ar);
													$pdf->Ln(2);
													$totalPagado += $aPagado[$l][2];
										}
										$indPag++;	
										//$totalCompromiso += $aCompromiso[$i][2];
										//$totalCausado += $causado[$j][2];
										//$totalPagado += $aPagado[$l][2];
									}
								}
							}else{
								if($indCau==0){
									$ar = array($aCompromiso[$i][5],
										$aCompromiso[$i][0], 
										muestrafecha($aCompromiso[$i][1]), 
										muestrafloat($aCompromiso[$i][2]),
										$causado[$j][0],
										muestrafecha($causado[$j][1]),
										muestrafloat($causado[$j][2]),
										'', 
										'', 
										'');
										$pdf->RowNL($ar);
										$pdf->Ln(2);
										$totalCompromiso += $aCompromiso[$i][2];
										$totalCausado += $causado[$j][2];
									}else{
										$ar = array('',
											'', 
											'', 
											'',
											$causado[$j][0],
											muestrafecha($causado[$j][1]),
											muestrafloat($causado[$j][2]),
											'', 
											'', 
											'');
											$pdf->RowNL($ar);
											$pdf->Ln(2);
											//$totalCompromiso += $aCompromiso[$i][2];
											$totalCausado += $causado[$j][2];
								}	
								$indCau++;	
								//$totalCompromiso += $aCompromiso[$i][2];
								//$totalCausado += $causado[$j][2];
							}
						}
						}
					}else{
						$ar = array($aCompromiso[$i][5],
							$aCompromiso[$i][0], 
							muestrafecha($aCompromiso[$i][1]), 
							muestrafloat($aCompromiso[$i][2]),
							'',
							'',
							'',
							'', 
							'', 
							'');
							$pdf->RowNL($ar);
							$pdf->Ln(2);
							$totalCompromiso += $aCompromiso[$i][2];
					}
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
			//echo "arreglo[".$i."] :".print_r($ar)."<br>";
			$pdf->ln(10);
			$pdf->SetAligns(array('R','R','R','R','R','R','R','R'));
			$pdf->SetWidths(array(33,33,33,33,33,33,33,33));
			$pdf->SetFont('Courier','B',8);
			$pdf->RowNL(array('PRESUPUESTO','AUMENTOS','DISMINUCIONES', 'COMPROMISOS','CAUSADOS','PAGADOS', 'DEUDA','DISPONIBLE'));
			$pdf->RowNL(array(muestrafloat($parcat->ppo),
									muestrafloat(mAumentos),
									muestrafloat($mDisminuciones), 
									muestrafloat($totalCompromiso),
									muestrafloat($totalCausado),
									muestrafloat($totalPagado),
									'0,00',
									muestrafloat($mDisponible)));
			if( ($ii+1) < count($cParCat) )
				$pdf->AddPage();
			}
	//die();
	
}
if(count($cDocs)<1){
	$pdf->ln(10);
	$pdf->SetFont('Courier','B',15);
	$pdf->Cell(200,4, 'El Reporte no tiene Datos para mostrar',0, '','R' );


}	
//die();
$pdf->Output();
?>
