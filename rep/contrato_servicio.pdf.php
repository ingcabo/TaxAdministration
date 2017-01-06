<?
include("comun/ini.php");
include("Constantes.php");
$oContratoServicio = new contrato_servicio;
$mPresupuestario = new movimientos_presupuestarios;
$oContratoServicio->get($conn, $_GET['id']);
if(empty($oContratoServicio->nrodoc))
	header ("location: contrato_servicio.php");
$_SESSION['pdf'] = serialize($oContratoServicio);

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

			$oContratoServicio = unserialize($_SESSION['pdf']);

			$this->SetXY(150, 20); 
			$textoDerecha = "Fecha: ".date('d/m/Y')."\n";
			$textoDerecha.= "Fecha Generac.:".muestrafecha($oContratoServicio->fecha)."\n";
			$textoDerecha.= "Fecha Aprob.:".muestrafecha($oContratoServicio->fecha_aprobacion)."\n";
			$textoDerecha.= "Pag: ".$this->PageNo()." de {nb}\n";
			$this->MultiCell(50,2, $textoDerecha, 0, 'L');
			
			$this->SetFont('Courier','b',12);
			$this->Text(80, 40, "Contrato de Servicio");
			$this->Text(153, 40, "Nro.:".$oContratoServicio->nrodoc."\n");
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
$oProveedor->get($conn, $oContratoServicio->id_proveedor);
//$pdf->SetFillColor(232 , 232, 232);
$pdf->Cell(25,4, 'Ciudadano:',0, '','L' );
$pdf->SetFont('Courier','',8);
$pdf->Cell(100,4, $oProveedor->nombre,0, '','L');

$pdf->Ln();
$pdf->SetFont('Courier','B',8);
$pdf->Cell(7,4, 'Rif:',0, '','L' );
$pdf->SetFont('Courier','',8);
$pdf->Cell(25,4, $oContratoServicio->rif,0, '','L' );
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

$pdf->Ln();
$pdf->SetFont('Courier','B',12);
$pdf->Cell(175,4, 'Servicio',0, '','C');

$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln(2);
$pdf->SetFont('Courier','B',8);
$pdf->Cell(35,4, 'Codigo de Servicio:',0, '','L');
$pdf->SetFont('Courier','',8);
$pdf->Cell(20,4, $oContratoServicio->id_servicio,0, '','L');
$pdf->SetFont('Courier','B',8);
$pdf->Cell(25,4, 'Descripcion:',0, '','L');
$pdf->SetFont('Courier','',8);
$desc_servicio = dividirStr(utf8_decode($oContratoServicio->descripcion), intval(95/$pdf->GetStringWidth('M')));
$pdf->Cell(95,4, $desc_servicio[0],0, '','L');
	$hay_desc = next($desc_servicio);
  		for ($i=1; $hay_desc!==false; $i++)
  		{
    		$pdf->Ln();
			$pdf->Cell(35,4, '',0, '','L');
			$pdf->Cell(20,4, '',0, '','L');
			$pdf->Cell(25,4, '',0, '','L');
			$pdf->Cell(95,4, utf8_decode($desc_servicio[$i]),0, '','L');
    		$hay_desc = next($desc_servicio);
  		}

$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln(2);
$pdf->SetFont('Courier','B',8);
$pdf->Cell(35,4, 'Unidad Solicitante:',0, '','L');
$pdf->SetFont('Courier','',8);
$pdf->Cell(90,4, utf8_decode($oContratoServicio->unidad_ejecutora),0, '','L');
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

//$cPartidas = $oContratoServicio->getRelacionPartidas($conn, $oContratoServicio->id, $escEnEje);

$cPartidas = $mPresupuestario->getImputacionReportes($conn,$oContratoServicio->nrodoc,$escEnEje);

foreach($cPartidas as $partida){
	$pdf->Ln();
	$desc_partida = dividirStr(utf8_decode($partida->partida), intval(90/$pdf->GetStringWidth('M')));
	$pdf->Cell(50,4, $partida->id_categoria."-".$partida->id_partida,0, '','L');
	$pdf->Cell(90,4, $desc_partida[0],0, '','L');
	$pdf->Cell(30,4, muestraFloat($partida->monto),0, '','R');
	//die('aqui '.$partida->monto);
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
	$pdf->Cell(140,4, 'TOTAL Bs.F.',0, '','R');
	$pdf->Cell(35,4, muestrafloat($montoTotal/1000),0, '','R');
	$pdf->Ln();
}

$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Cell(30,25, '',0, '','R');
$pdf->Cell(60,10, '___________________',0, '','C');
$pdf->Cell(55,10, '___________________',0, '','C');

$pdf->Ln();
$pdf->Cell(30,4, '',0, '','R');
$pdf->Cell(60,4, 'JEFE DE PRESUPUESTO ',0, '','C');
$pdf->Cell(55,4, 'DIRECTOR DE',0, '','C');

$pdf->Ln();
$pdf->Cell(90,4, '',0, '','R');
$pdf->Cell(55,4, 'ADMINISTRACION',0, '','C');

$pdf->Output();
?>
