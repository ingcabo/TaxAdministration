<?
	include("comun/ini.php");
	include("Constantes.php");

$id_cp = $_GET['id_cp']; 
$escenario = $_REQUEST['id_escenario']; 
$idCatPro = substr($id_cp,0,2);
	
		$oppcp = new categorias_programaticas;
		$rppcp = $oppcp->descObjetivo($conn, $escenario, $idCatPro);
		//var_dump($rppcp);
	
function dividirStr($str, $max)
  {
    $strArray = array();
    do
    {
      if (strlen($str) > $max)
        $posF = strrpos( substr($str, 0, $max), ' ' );
      else
        $posF = -1;
      
      if ($posF===false || $posF==-1)
      {
        $strArray[] = substr($str, 0);
        $str = substr($str, 0);
        $posF = -1;
      }
      else
      {
        $strArray[] = substr($str, 0, $posF);
        $str = substr($str, $posF+1 );
      }
    }while ($posF != -1);
    
    return ($strArray);
  }
  
class PDF extends FPDF
{
  var $leftMargin = 15;
  var $rightMargin = 195;
  var $fontStyle = 'Courier';
  var $fontBodySize = 8;
  var $fontHeaderSize = 6;
  var $fontTableBodySize = 7;
  var $fontHeaderTitleSize = 12;
  var $cellSectWidth = 25;
  var $cellColWidth = 15;
  var $cellDescWidth = 140;
  var $codPrograma;
  var $descPrograma;
  var $escEnEje;
  
  
	function Header()
	{
			$this->SetLeftMargin($this->leftMargin);
			$this->SetFont($this->fontStyle,'',$this->fontHeaderSize);
			$this->Ln(1);
			$this->Rect($this->leftMargin, 4, $this->rightMargin-$this->leftMargin, 45);

			$this->Image ("images/logoa.jpg",16,5,26);//logo a la izquierda 
			$this->SetXY(42, 22); 
			$textoCabecera.= UBICACION."\n";
			//$textoCabecera.= "MUNICIPIO: SANTOS MICHELENA";
			$this->MultiCell(50,2, $textoCabecera, 0, 'L');

			$textoDerecha = "Fecha: ".date('d/m/Y')."\n";
			$textoDerecha.= "Pag: ".$this->PageNo()." de {nb}\n";
			$this->SetXY(170, 22); 
			$this->MultiCell(50,2, $textoDerecha, 0, 'L');
			
			$this->Ln(8);

			$this->SetFont($this->fontStyle, 'B', $this->fontHeaderTitleSize);
			$this->MultiCell(0,2, "OBJETIVOS SECTORIALES", 0, 'C');
			
			$this->SetFont($this->fontStyle, '', $this->fontHeaderSize);
			$this->Text(16, 48, 'Presupuesto: Año ' . $this->anoCurso);
			
			//---- Parte donde va el código y el nombre del progarma.
			$this->Rect($this->leftMargin, 51, $this->rightMargin-$this->leftMargin, 8);
			$this->SetY(51);
			$this->SetFont($this->fontStyle, 'B', $this->fontBodySize);
			$this->Cell($this->cellSectWidth, 4, '', 0, '', 'L');
			$this->Cell($this->cellColWidth, 4, 'Codigo', LBTR, '', 'C');
			$this->Cell($this->cellDescWidth, 4, 'Denominacion', RBT, '', 'C');
			$this->Ln();
			$this->Cell($this->cellSectWidth, 4, 'SECTOR', LTR, '', 'C');
			$this->SetFont($this->fontStyle, '', $this->fontBodySize);
			$this->Cell($this->cellColWidth, 4, $this->codPrograma, LR, '', 'C');
			$this->Cell($this->cellDescWidth, 4, utf8_decode($this->descPrograma), L, '', 'L');
			$this->Ln(6);
			$this->Cell($this->cellDescWidth+$this->cellColWidth+$this->cellSectWidth, 4, utf8_decode(DESCRIPCION), LRBT, '', 'C');
			//---- fin Parte donde va el código y el nombre del progarma.
			
			
	}

	function Footer()
	{	
		//$this->Line($this->leftMargin, $this->GetY(), $this->rightMargin, $this->GetY());
		$this->SetFont($this->fontStyle, '', $this->fontBodySize);
	}
}

//Creación del objeto de la clase heredada
$pdf=new PDF();
$pdf->codPrograma = substr($rppcp->id_cp, 0, 2);
$pdf->descPrograma = $rppcp->desc_cp;
$pdf->escEnEje = $escEnEje;
$pdf->anoCurso = $anoCurso;
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetLeftMargin($pdf->leftMargin);

//var_dump($rppcp);

	$pdf->Line(15, 61, 15, 270);
	$pdf->Line(195, 61, 195, 270);

	$objetivoSectorial = dividirStr($rppcp->os, intval(($pdf->cellDescWidth+$pdf->cellColWidth+$pdf->cellSectWidth)/$pdf->GetStringWidth('M')));
	//$objetivoSectorial = dividirStr($rppcp->os, intval(180/$pdf->GetStringWidth('M')));
	$pdf->Ln(8);
	$pdf->SetFont($pdf->fontStyle, '', $pdf->fontBodySize);
	$pdf->Cell($pdf->cellDescWidth+$pdf->cellColWidth+$pdf->cellSectWidth, 4, utf8_decode($objetivoSectorial[0]), 0, '', 'L' );
	//$pdf->Cell(90,4,utf8_decode($dir_proveedor[0]),0, '','L');
			$linea = 0;
			$hay_os = next($objetivoSectorial);
			for ($i=1; $hay_os!==false; $i++)
			{
				$linea+= 4;
				$pdf->Ln();
				$pdf->Cell($pdf->cellDescWidth+$pdf->cellColWidth+$pdf->cellSectWidth,4, utf8_decode($objetivoSectorial[$i]), 0, '', 'L');
				$hay_os = next($objetivoSectorial);
			}
	//$pdf->Cell($pdf->cellDescWidth+$pdf->cellColWidth+$pdf->cellSectWidth, 4, 'TOTALES', LR, '', 'C' );
	
$pdf->Line(15, 270, 195, 270);



$pdf->Output();
?>
