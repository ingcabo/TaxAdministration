<?php //SE7H[ENE][2006]
session_start();
set_time_limit(0);
include ("lib/core.lib.php");
require('lib/fpdf.php');

$id_proveedores=$_REQUEST['id_proveedores'];

$sql="SELECT 
  proveedores.fecha AS fecha,
  proveedores.nombre,
  proveedores.rif_letra AS l,
  proveedores.rif_numero AS n,
  proveedores.rif_control AS c,
  proveedores.nit AS nit,
  proveedores.id
FROM
 proveedores
WHERE id=".$id_proveedores;
 
$rs = @$conn->Execute($sql);  
$nombre=strtoupper($rs->fields['nombre']);
$rif=$rs->fields['l']."-".$rs->fields['n']."-".$rs->fields['c'];
$nit=$rs->fields['nit'];
$fecha=$rs->fields['fecha'];

class PDF extends FPDF
{
//Cabecera de página
	function Header()
	{
			$this->Ln(15);
	}

//Pie de página
	function Footer()
	{
		//Posición: a 1,5 cm del final
		$this->SetY(-15);
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
$pdf->SetFont('Times','',9);
$pdf->SetLeftMargin(10);
	
			
			$pdf->SetFont('Times','BI',20);
			$pdf->Text(50, 100, $nombre);
			
			$pdf->SetFont('Courier','I',12);
			$pdf->Text(40, 115, 'R.I.F.: '.$rif);
			$pdf->Text(140, 115, 'NIT: '.$nit);
			
			#CODIGO ID DEL PROVEEDOR
			$pdf->SetFont('Courier','BI',12);
			$pdf->Text(120, 165, $id_proveedores);
			$pdf->Text(120, 175, $fecha);
#	

$pdf->Output();
?> 