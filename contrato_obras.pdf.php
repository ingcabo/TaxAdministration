<?
include("comun/ini.php");
include("Constantes.php");
$oContratoObra = new contrato_obras();
$oContratoObra->get($conn, $_GET['id']);
$mPresupuestario = new movimientos_presupuestarios;
if(empty($oContratoObra->nrodoc))
	header ("location: contrato_obras.php");
	
// Crea un array donde cada posicion es un string de tamaño 'max' caracteres,
// teniendo en cuenta de no cortar una palabra, busca el espacio en blanco  
// mas cerca del tamaño 'max' y ahi corta el string

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
  
$_SESSION['pdf'] = serialize($oContratoObra);
class PDF extends FPDF
{
//Cabecera de página
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
			$this->MultiCell(50,2, $textoCabecera, 0, 'L');

			$oContratoObra = unserialize($_SESSION['pdf']);

			$this->SetXY(150, 20); 
			$textoDerecha = "Fecha: ".date('d/m/Y')."\n";
			$textoDerecha.= "Fecha Generac.:".muestrafecha($oContratoObra->fecha)."\n";
			$textoDerecha.= "Fecha Aprob.:".muestrafecha($oContratoObra->fecha_aprobacion)."\n";
			$textoDerecha.= "Pag: ".$this->PageNo()." de {nb}\n";
			$this->MultiCell(50,2, $textoDerecha, 0, 'L');
			
			$this->SetFont('Courier','b',12);
			$this->Text(80, 40, "Contrato de Obras");
			$this->Text(153, 40, "Nro.:".$oContratoObra->nrodoc."\n");
			$this->Line(15, 41, 190, 41);
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
//Creación del objeto de la clase heredada
$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Courier','B',8);
$pdf->SetLeftMargin(15);

$oProveedor = new proveedores;
$oProveedor->get($conn, $oContratoObra->id_proveedor);
//$pdf->SetFillColor(232 , 232, 232);
$pdf->Cell(25,4, 'Sres.:',0, '','L' );
$pdf->SetFont('Courier','',8);
$pdf->Cell(100,4, $oProveedor->id." - ".$oProveedor->nombre,0, '','L');

$pdf->Ln();
$pdf->SetFont('Courier','B',8);
$pdf->Cell(7,4, 'Rif:',0, '','L' );
$pdf->SetFont('Courier','',8);
$pdf->Cell(25,4, $oContratoObra->rif,0, '','L' );
$pdf->SetFont('Courier','B',8);
$pdf->Cell(7,4, 'Nit:',0, '','L' );
$pdf->SetFont('Courier','',8);
$pdf->Cell(25,4, $oProveedor->nit,0, '','L' );
$pdf->SetFont('Courier','B',8);
$pdf->Cell(7,4, 'Fax:',0, '','L' );
$pdf->SetFont('Courier','',8);
$pdf->Cell(25,4, $oProveedor->fax,0, '','L' );
$pdf->SetFont('Courier','B',8);
$pdf->Cell(7,4, 'Tlf:',0, '','L' );
$pdf->SetFont('Courier','',8);
$pdf->Cell(25,4, $oProveedor->telefono,0, '','L' );

$pdf->Ln();
$pdf->SetFont('Courier','B',8);
$pdf->Cell(25,4, 'Direccion',0, '','L');
$pdf->SetFont('Courier','',8);
$pdf->MultiCell(100,4, $oProveedor->direccion,0, '','L');

$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');

/*$pdf->Ln(2);
$pdf->SetFont('Courier','B',8);
$pdf->Cell(25,4, 'Descripcion:',0, '','L');
$pdf->SetFont('Courier','',8);
$pdf->MultiCell(150,4, $oContratoObra->descripcion,0, '','L');*/

/*$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');*/

$pdf->Ln();
$pdf->SetFont('Courier','B',12);
$pdf->Cell(175,4, 'Obra',0, '','C');

$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln(2);
$pdf->SetFont('Courier','B',8);
$pdf->Cell(30,4, 'Codigo de Obra:',0, '','L');
$pdf->SetFont('Courier','',8);
$pdf->Cell(20,4, $oContratoObra->id_obra,0, '','L');
$pdf->SetFont('Courier','B',8);
$pdf->Cell(40,4, 'Descripcion de Obra:',0, '','L');
$pdf->SetFont('Courier','',8);
$pdf->Cell(60,4, $oContratoObra->descripcion,0, '','L');

$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln(2);
$pdf->SetFont('Courier','B',8);
$pdf->Cell(35,4, 'Unidad Solicitante:',0, '','L');
$pdf->SetFont('Courier','',8);
$pdf->Cell(90,4, utf8_decode($oContratoObra->unidad_ejecutora),0, '','L');

$pdf->Ln();

$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln();
$pdf->SetFont('Courier','B',12);
$pdf->Cell(175,4, 'IMPUTACION PRESUPUESTARIA',0, '','C');

$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln(2);
$pdf->SetFont('Courier','B',8);
$pdf->Cell(50,4, 'Partida Presupuestaria',0, '','C');
$pdf->Cell(90,4, 'Descripcion',0, '','L');
$pdf->Cell(30,4, 'Monto',0, '','C');


$cPartidas = $mPresupuestario->getImputacionReportes($conn,$oContratoObra->nrodoc,$escEnEje);
//$cPartidas = $oContratoObra->getRelacionPartidas($conn, $oContratoObra->id, $escEnEje);

foreach($cPartidas as $partida){
	$desc_partida = dividirStr(utf8_decode($partida->partida), intval(100/$pdf->GetStringWidth('M')));
	$pdf->Ln();
	$pdf->Cell(50,4, $partida->id_categoria."-".$partida->id_partida,0, '','L');
	$pdf->Cell(90,4, $desc_partida[0],0, '','L');
	$pdf->Cell(30,4, muestrafloat($partida->monto),0, '','R');
	$montoTotal += $partida->monto;
	
	$hay_ue = next($desc_partida);
  		for ($i=1; $hay_ue!==false; $i++)
  		{
    		$pdf->Ln();
			$pdf->Cell(50,4, '',0, '','L');
			$pdf->Cell(90,4, $desc_partida[$i],0, '','L');
			$pdf->Cell(30,4, '',0, '','R');
    		$hay_ue = next($desc_partida);
  		}
}
$pdf->Ln();
$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln();
$pdf->Cell(140,4, 'TOTAL',0, '','R');
$pdf->Cell(35,4, muestrafloat($montoTotal),0, '','R');
$pdf->Ln();

if($anoCurso == 2007){
	$pdf->Cell(140,4, 'TOTAL Bs.F',0, '','R');
	$pdf->Cell(35,4, muestrafloat($montoTotal/1000),0, '','R');
	$pdf->Ln();
}

$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Cell(40,25, '',0, '','R');
$pdf->Cell(45,10, '___________________',0, '','C');
$pdf->Cell(45,10, '___________________',0, '','C');

$pdf->Ln();
$pdf->Cell(40,4, '',0, '','R');
$pdf->Cell(45,4, 'JEFE DE PRESUPUESTO',0, '','C');
$pdf->Cell(45,4, 'DIRECTOR DE',0, '','C');

$pdf->Ln();
$pdf->Cell(85,4, '',0, '','R');
$pdf->Cell(45,4, 'ADMINISTRACION',0, '','C');

$pdf->Output();
?>
