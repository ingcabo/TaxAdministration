<?php
require('fpdf.php');

class PDF extends FPDF
{
//Una tabla más completa
function ImprovedTable($header,$data)
{
    //Anchuras de las columnas
    $w=array(40,35,40,45);
    //Cabeceras
    for($i=0;$i<count($header);$i++)
        $this->Cell($w[$i],7,$header[$i],1,0,'C');
    $this->Ln();
    //Datos
    foreach($data as $row)
    {
		//Texto centrado en una celda con cuadro 20*10 mm y salto de línea
         //$pdf->Cell(20,10,'Title',1,1,'C'); 
        $this->Cell(20,10,'Identificaci&oacute;n del Proveedor', ,);
        $this->Cell($w[1],6,$row[1],'LR');
        $this->Cell($w[2],6,number_format($row[2]),'LR',0,'R');
        $this->Cell($w[3],6,number_format($row[3]),'LR',0,'R');
        $this->Ln();
    }
    //Línea de cierre
    $this->Cell(array_sum($w),0,'','T');
}
}

$pdf=new PDF();
//Títulos de las columnas
$this->Image ("../images/escudo.jpg",15,4,26);//logo a la izquierda 
$header=array('País','Capital','Superficie (km2)','Pobl. (en miles)');
//Carga de datos
$pdf->SetFont('Arial','',14);
$pdf->AddPage();
$pdf->ImprovedTable($header,$data);
$pdf->Output();
?>