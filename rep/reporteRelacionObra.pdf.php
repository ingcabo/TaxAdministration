<?
include("comun/ini.php");
include("Constantes.php");

$escenario = $_REQUEST['id_escenario']; 	
	
		//$oobra = new obras;
		//$obras = $oobra->get_all_escenario($conn, $escenario);
		/*$ocategoria = new categorias_programaticas;
		$categoria_sector = $ocategoria->getReporteCategoriaSector($conn, $id_cp, $escenario);
		$categoria_programa= $ocategoria->getReporteCategoriaActividad($conn, $id_cp, $escenario);
		$categoria_unidad = $ocategoria->getReporteCategoriaUnidadEjecutora($conn, $id_cp, $escenario);*/

		
		
		//var_dump($obras[0]);


class PDF extends FPDF
{
  var $leftMargin = 15;
  var $rightMargin = 285;
  var $fontStyle = 'Courier';
  var $fontBodySize = 6;
  var $fontHeaderSize = 6;
  var $fontHeaderTitleSize = 12;
  var $cellColWidth = 5;
  var $cellDescWidth = 114;
  //var $cellTotalWidth = 35;
  
  var $cellCodWith = 20;
  var $cellDenoWith = 36;
  var $cellFuncWith = 25;
  var $cellComWith = 19;
  var $cellTotalWidth = 16;
  var $cellCostoWidth = 14;
   
  var $codSector;
  var $descSector;
  var $codPrograma;
  var $descPrograma;
  var $codActividad;
  var $descActividad;
  var $descUnidad;
  var $escEnEje;
  var $unidad_ejecutora;
  
  
	function Header()
	{
			$this->SetLeftMargin($this->leftMargin);
			$this->SetFont($this->fontStyle,'',$this->fontHeaderSize);
			$this->Ln(1);
			$this->Rect($this->leftMargin, 4, $this->rightMargin-$this->leftMargin, 40);

			$this->Image ("images/logoa.jpg",16,5,26);//logo a la izquierda 
			$this->SetXY(42, 10); 
			$textoCabecera = PAIS."\n";
			$textoCabecera.= UBICACION."\n";
			$textoCabecera.= ENTE."\n";
			$textoCabecera.= DEPARTAMENTO."\n";
			$this->MultiCell(60,2, $textoCabecera, 0, 'L');

			$textoDerecha = "Fecha: ".date('d/m/Y')."\n";
			$textoDerecha.= "Pag: ".$this->PageNo()." de {nb}\n";
			$this->SetXY(250, 10); 
			$this->MultiCell(90,2, $textoDerecha, 0, 'L');
			
			$this->Ln(1);

			$this->SetFont($this->fontStyle, 'B', $this->fontHeaderTitleSize);
			$this->MultiCell(0,2, "RELACION DE OBRAS", 0, 'C');
			
			$this->SetFont($this->fontStyle, '', $this->fontHeaderSize);
			$this->Text(16, 38, 'Presupuesto: Año ' . date ('Y');	
			//$this->Text(16, 38, 'Presupuesto: Año ' . $this->escEnEje);
			$this->Ln(30);
			

	}

	function Footer()
	{	
		$this->Line($this->leftMargin, $this->GetY(), $this->rightMargin, $this->GetY());
		$this->SetFont($this->fontStyle, '', $this->fontBodySize);
	}
}

//Creación del objeto de la clase heredada
$pdf=new PDF('L','mm','A4');
$pdf->escEnEje = $escenario;
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetLeftMargin($pdf->leftMargin);
//session_unset();
//session_destroy();

$q = "SELECT id FROM puser.unidades_ejecutoras WHERE id_escenario = '$escenario'";
$Runidades = $conn->Execute($q);
while(!$Runidades->EOF){
$imprimir = 0;
$imprimir_encabezado = 1;
$Totalcosto = 0;
$Totalcaa = 0;
$Totalcav = 0;
$Totalcomprometido = 0;
$Totaleaa = 0;
$Totaleav = 0;
$Totalejecutado = 0;
$Totalepre = 0;
$Totalepos = 0;
$unidad =$Runidades->fields['id'];
$q = "SELECT id FROM puser.obras WHERE id_escenario = '2009' AND id_unidad_ejecutora = '$unidad'";
$Robras = $conn->Execute($q);
	while(!$Robras->EOF){
		$oobra = new obras;
		$oobra->get($conn, $Robras->fields['id'], $escenario);
		//echo strpos($oobra->relacion, 'id_categoria_programatica');
		$categoria = substr(substr($oobra->relacion, strpos($oobra->relacion, 'id_categoria_programatica'), 39), 28); 
		$pdf->unidad_ejecutora = $oobra->unidad_ejecutora;
		$imprimir = 1;		
		if($imprimir_encabezado&&$imprimir){
		
			$pdf->Cell($pdf->cellCodWith+$pdf->cellDenoWith+$pdf->cellFuncWith+($pdf->cellComWith*3) , 4, 'CODIGO: '.substr($categoria, 0, 2).' SECTOR: '.substr($categoria, 2, 2).' PROGRAMA: '.substr($categoria, 4, 2).' SUB-PROGRAMA: '.substr($categoria, 6, 2).' PROYECTO: '.substr($categoria, 8, 2),  TLR);
			$pdf->Cell(($pdf->cellTotalWidth*3)+($pdf->cellCostoWidth*6), 4, 'UNIDAD EJECUTORA: '.utf8_decode($pdf->unidad_ejecutora),  TLR);
			$pdf->Ln();
			$pdf->Cell($pdf->cellCodWith+$pdf->cellDenoWith+$pdf->cellFuncWith , 6, 'OBRAS',  TLR);
			$pdf->Cell(($pdf->cellComWith*2), 6, 'MES Y AÑO DE',  TLR,0, 'C');
			$pdf->Cell($pdf->cellComWith, 6, '',  TLR);
			$pdf->Cell($pdf->cellTotalWidth, 6, '', TR);
			$pdf->Cell(($pdf->cellTotalWidth*2)+($pdf->cellCostoWidth*6), 6, 'ASIGNACIONES (miles de bolivares)', TR,0, 'C');					
			$pdf->Ln();
			
			$pdf->Cell($pdf->cellCodWith, 4, '',  TLR);
			$pdf->Cell($pdf->cellDenoWith, 4, '',  TLR);
			$pdf->Cell($pdf->cellFuncWith, 4, '',  TLR);
			$pdf->Cell($pdf->cellComWith, 4, '',  TLR);
			$pdf->Cell($pdf->cellComWith, 4, '',  TLR);
			$pdf->Cell($pdf->cellComWith, 4, '',  LR);
			$pdf->Cell($pdf->cellTotalWidth, 4, 'COSTO', R,0, 'C');
			$pdf->Cell($pdf->cellTotalWidth+($pdf->cellCostoWidth*2), 4, 'COMPROMETIDAS', TR,0, 'C');
			$pdf->Cell($pdf->cellTotalWidth+($pdf->cellCostoWidth*2), 4, 'EJECUTADAS', TR,0, 'C');
			$pdf->Cell(($pdf->cellCostoWidth*2), 4, 'ESTIMADAS', TR,0, 'C');			
			$pdf->Ln();

			$pdf->Cell($pdf->cellCodWith, 4, '',  LR);
			$pdf->Cell($pdf->cellDenoWith, 4, '',  LR);
			$pdf->Cell($pdf->cellFuncWith, 4, 'FUNCIONARIO',  LR,0, 'C');
			$pdf->Cell($pdf->cellComWith, 4, '',  LR);
			$pdf->Cell($pdf->cellComWith, 4, 'CONCLUSION',  LR,0, 'C');
			$pdf->Cell($pdf->cellComWith, 4, 'SITUACION',  LR,0, 'C');
			$pdf->Cell($pdf->cellTotalWidth, 4, 'TOTAL', R,0, 'C');
			$pdf->Cell($pdf->cellCostoWidth, 4, 'AÑOS', TR,0, 'C');
			$pdf->Cell($pdf->cellCostoWidth, 4, 'AÑOS', TR,0, 'C');
			$pdf->Cell($pdf->cellTotalWidth, 4, 'TOTAL', TR,0, 'C');
			$pdf->Cell($pdf->cellCostoWidth, 4, 'AÑOS', TR,0, 'C');
			$pdf->Cell($pdf->cellCostoWidth, 4, 'AÑOS', TR,0, 'C');
			$pdf->Cell($pdf->cellTotalWidth, 4, 'TOTAL', TR,0, 'C');
			$pdf->Cell($pdf->cellCostoWidth, 4, 'PRESUP', TR,0, 'C');
			$pdf->Cell($pdf->cellCostoWidth, 4, 'AÑOS', TR,0, 'C');				
			$pdf->Ln();			
	
			$pdf->Cell($pdf->cellCodWith, 4, 'CODIGO',  LR,0, 'C');
			$pdf->Cell($pdf->cellDenoWith, 4, 'DENOMINACION',  LR,0, 'C');
			$pdf->Cell($pdf->cellFuncWith, 4, 'RESPONSABLE',  LR,0, 'C');
			$pdf->Cell($pdf->cellComWith, 4, 'INICIO',  LR,0, 'C');
			$pdf->Cell($pdf->cellComWith, 4, '',  LR);
			$pdf->Cell($pdf->cellComWith, 4, '',  LR);
			$pdf->Cell($pdf->cellTotalWidth, 4, '', R);
			$pdf->Cell($pdf->cellCostoWidth, 4, 'ANTERIORES', R,0, 'C');
			$pdf->Cell($pdf->cellCostoWidth, 4, 'VIGENTE', R,0, 'C');
			$pdf->Cell($pdf->cellTotalWidth, 4, '', R);
			$pdf->Cell($pdf->cellCostoWidth, 4, 'ANTERIORES', R,0, 'C');
			$pdf->Cell($pdf->cellCostoWidth, 4, 'VIGENTES', R,0, 'C');
			$pdf->Cell($pdf->cellTotalWidth, 4, '', R);
			$pdf->Cell($pdf->cellCostoWidth, 4, '', R);
			$pdf->Cell($pdf->cellCostoWidth, 4, 'POSTERIORES', R,0, 'C');				
			$pdf->Ln();
			
			$pdf->Cell($pdf->cellCodWith, 4, '',  LRB);
			$pdf->Cell($pdf->cellDenoWith, 4, '',  LRB);
			$pdf->Cell($pdf->cellFuncWith, 4, '',  LRB);
			$pdf->Cell($pdf->cellComWith, 4, '',  LRB);
			$pdf->Cell($pdf->cellComWith, 4, '',  LRB);
			$pdf->Cell($pdf->cellComWith, 4, '',  LRB);
			$pdf->Cell($pdf->cellTotalWidth, 4, '', RB);
			$pdf->Cell($pdf->cellCostoWidth, 4, '', RB);
			$pdf->Cell($pdf->cellCostoWidth, 4, '', RB);
			$pdf->Cell($pdf->cellTotalWidth, 4, '', RB);
			$pdf->Cell($pdf->cellCostoWidth, 4, '', RB);
			$pdf->Cell($pdf->cellCostoWidth, 4, '', RB);
			$pdf->Cell($pdf->cellTotalWidth, 4, '', RB);
			$pdf->Cell($pdf->cellCostoWidth, 4, '', RB);
			$pdf->Cell($pdf->cellCostoWidth, 4, '', RB);				
			$pdf->Ln();
			
			//---- fin Cabeceras de partidas y subpartidas, denominación y monto

		}
		$pdf->Cell($pdf->cellCodWith, 4, $oobra->obra_cod,  LR);
		$pdf->Cell($pdf->cellDenoWith, 4, $oobra->denominacion,  LR);
		$pdf->Cell($pdf->cellFuncWith, 4, $oobra->responsable,  LR);
		$pdf->Cell($pdf->cellComWith, 4, $oobra->inicio,  LR);
		$pdf->Cell($pdf->cellComWith, 4, $oobra->culminacion,  LR);
		switch($oobra->id_situacion){
			case 1: $situacion = 'A Iniciar';
					break;
			case 2: $situacion = 'Paralizada';
					break;
			case 3: $situacion = 'En ejecucion';
					break;
			case 4: $situacion = 'Terminada';
					break;
		}
		$pdf->Cell($pdf->cellComWith, 4, $situacion,  LR);
		$pdf->Cell($pdf->cellTotalWidth, 4, muestraFloat($oobra->ctotal), R);
		$Totalcosto+= $oobra->ctotal;
		$pdf->Cell($pdf->cellCostoWidth, 4, muestraFloat($oobra->caa), R);
		$Totalcaa+= $oobra->caa;
		$pdf->Cell($pdf->cellCostoWidth, 4, muestraFloat($oobra->cav), R);
		$Totalcav+= $oobra->cav;
		$Comprometido = $oobra->caa + $oobra->cav;
		$pdf->Cell($pdf->cellTotalWidth, 4, muestraFloat($Comprometido), R);
		$Totalcomprometido+= $Comprometido;
		$pdf->Cell($pdf->cellCostoWidth, 4, muestraFloat($oobra->eaa), R);
		$Totaleaa+= $oobra->eaa;
		$pdf->Cell($pdf->cellCostoWidth, 4, muestraFloat($oobra->eav), R);
		$Totaleav+= $oobra->eav;
		$Ejecutado = $oobra->eaa + $oobra->eav;
		$pdf->Cell($pdf->cellTotalWidth, 4, muestraFloat($Ejecutado), R);
		$Totalejecutado+= $Ejecutado;
		$pdf->Cell($pdf->cellCostoWidth, 4, muestraFloat($oobra->epre), R);
		$Totalepre+= $oobra->epre;
		$pdf->Cell($pdf->cellCostoWidth, 4, muestraFloat($oobra->epos), R);
		$Totalepos+= $oobra->epos;		
		$pdf->Ln();
		//die();
		$imprimir_encabezado = 0;
	$Robras->movenext();
	}
	if($imprimir){
		$pdf->Cell($pdf->cellCodWith+$pdf->cellDenoWith+$pdf->cellFuncWith + ($pdf->cellComWith*3), 4, 'TOTALES',1, 0,'C');
		$pdf->Cell($pdf->cellTotalWidth, 4, muestraFloat($Totalcosto), 1);
		$pdf->Cell($pdf->cellCostoWidth, 4, muestraFloat($Totalcaa), 1);
		$pdf->Cell($pdf->cellCostoWidth, 4, muestraFloat($Totalcav), 1);
		$pdf->Cell($pdf->cellTotalWidth, 4, muestraFloat($Totalcomprometido), 1);
		$pdf->Cell($pdf->cellCostoWidth, 4, muestraFloat($Totaleaa), 1);
		$pdf->Cell($pdf->cellCostoWidth, 4, muestraFloat($Totaleav), 1);
		$pdf->Cell($pdf->cellTotalWidth, 4, muestraFloat($Totalejecutado), 1);
		$pdf->Cell($pdf->cellCostoWidth, 4, muestraFloat($Totalepre), 1);
		$pdf->Cell($pdf->cellCostoWidth, 4, muestraFloat($Totalepos), 1);				
		$pdf->Ln();
	}
$Runidades->movenext();
}
$pdf->SetFont($pdf->fontStyle, '', $pdf->fontBodySize);


if (is_array($obras))
{
	foreach ($obras as $robras) 
	{
			//---- Cabeceras de partidas y subpartidas, denominación y monto

			//$pdf->AddPage();
    }
/*
    $maxPal = intval($pdf->cellDescWidth/$pdf->GetStringWidth('0'));
    if (strlen($report->desc_pp) >= $maxPal)
    {
      $strArray = array();
      do
      {
        if (strlen($report->desc_pp) >= $maxPal)
          $posF = strrpos( substr( $report->desc_pp, 0, $maxPal ), ' ' );
        else
          $posF = -1;
        
        if ($posF===false || $posF==-1)
        {
          $strArray[] = substr( $report->desc_pp, 0 );
          $report->desc_pp = substr( $report->desc_pp, 0 );
          $posF = -1;
        }
        else
        {
          $strArray[] = substr( $report->desc_pp, 0, $posF );
          $report->desc_pp = substr( $report->desc_pp, $posF );
        }
      }while ($posF != -1);
      
      $pdf->Cell($pdf->cellColWidth+1, 4, substr($report->id_pp, 0, 3), L, '','C' );
  		$pdf->Cell($pdf->cellColWidth, 4, substr($report->id_pp, 3, 2), L, '','C' );
  		$pdf->Cell($pdf->cellColWidth, 4, substr($report->id_pp, 5, 2), L, '','C' );
  		$pdf->Cell($pdf->cellColWidth, 4, substr($report->id_pp, 7, 2), L, '','C' );
  		$pdf->Cell($pdf->cellColWidth, 4, substr($report->id_pp, 9, 2), L, '','C' );
  		$pdf->Cell($pdf->cellColWidth, 4, substr($report->id_pp, 11, 2), L, '','C' );
  		$pdf->Cell($pdf->cellDescWidth, 4, utf8_decode($strArray[0]), L, '','L' );
  	  $pdf->Cell($pdf->cellTotalWidth, 4, muestrafloat($report->monto), LR, '','R' );
  	  
  	  for ($i=1,$lon=count($strArray); next($strArray); $i++)
  	  {
    	  $pdf->Ln(4);
        $pdf->Cell($pdf->cellColWidth+1, 4, '', L, '','C' );
    		$pdf->Cell($pdf->cellColWidth, 4, '', L, '','C' );
    		$pdf->Cell($pdf->cellColWidth, 4, '', L, '','C' );
    		$pdf->Cell($pdf->cellColWidth, 4, '', L, '','C' );
    		$pdf->Cell($pdf->cellColWidth, 4, '', L, '','C' );
    		$pdf->Cell($pdf->cellColWidth, 4, '', L, '','C' );
    		$pdf->Cell($pdf->cellDescWidth, 4, utf8_decode($strArray[$i]), L, '','L' );
    	  $pdf->Cell($pdf->cellTotalWidth, 4, '', LR, '','R' );
      }
    }
    else
    {
      $pdf->Cell($pdf->cellColWidth+1,4, substr($report->id_pp, 0, 3), L, '','C' );
  		$pdf->Cell($pdf->cellColWidth,4, substr($report->id_pp, 3, 2), L, '','C' );
  		$pdf->Cell($pdf->cellColWidth,4, substr($report->id_pp, 5, 2), L, '','C' );
  		$pdf->Cell($pdf->cellColWidth,4, substr($report->id_pp, 7, 2), L, '','C' );
  		$pdf->Cell($pdf->cellColWidth,4, substr($report->id_pp, 9, 2), L, '','C' );
  		$pdf->Cell($pdf->cellColWidth,4, substr($report->id_pp, 11, 2), L, '','C' );
  		$pdf->Cell($pdf->cellDescWidth,4, utf8_decode($report->desc_pp), L, '','L' );
  	  $pdf->Cell($pdf->cellTotalWidth,4, muestrafloat($report->monto), LR, '','R' );
	}
    $total_presupuesto += $report->monto;
		$pdf->Ln(4);*/
		
}

$pdf->Output();
?>
