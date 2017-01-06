<?
include("comun/ini.php");
$id_requisicion = $_REQUEST['id_requisicion'];
if(!empty($_REQUEST['preRequisicion']))
	$preRequisicion = true;
else
	$preRequisicion = false;
if(empty($id_requisicion))
	header ("location: revision_requisicion.php");
$oRequisicion = new requisiciones;
$oRequisicion->get($conn,$id_requisicion);
$_SESSION['pdf'] = serialize($oRequisicion);

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
			$this->Image ("images/logoa.jpg",15,4,55,20);//logo a la izquierda 
			$this->SetXY(15, 22); 
			$textoCabecera = "INTENDENCIA MUNICIPAL\n";
			$this->MultiCell(50,2, $textoCabecera, 0, 'L');

			$this->SetXY(80, 22); 
			$this->SetFont('Courier','b',12);
			$this->MultiCell(60, 2, "REQUISICION DE COMPRA",0,'C');
			$oReviRequi = unserialize($_SESSION['pdf']);

			$this->SetXY(170, 22); 
			$this->SetFont('Courier','',6);
			//$textoDerecha = "Fecha: ".date('d/m/Y')."\n";
			//$textoDerecha.= "Fecha Generac.:".muestrafecha($oReviRequi->fecha)."\n";
			$textoDerecha.= "Fecha:".muestrafecha($oReviRequi->fecha_aprobacion)."\n";
			//$textoDerecha.= "Pag: ".$this->PageNo()." de {nb}\n";
			$this->MultiCell(50,2, $textoDerecha, 0, 'L');
			
			$this->SetXY(80, 26); 
			$this->SetFont('Courier','b',12);
			$this->MultiCell(60, 2, "  Nro. ".$oReviRequi->id,0,'C');
			//$this->Line(15, 41, 190, 41);
			$this->Ln(4);
			
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


//OBTIENE LOS PRODUCTOS DE LA REQUISICION
$q = "SELECT DISTINCT puser.productos.descripcion, puser.relacion_requisiciones.cantidad, puser.relacion_requisiciones.id_partida, puser.relacion_requisiciones.id_categoria, puser.productos.unidad_medida FROM puser.relacion_requisiciones Inner Join puser.productos ON puser.relacion_requisiciones.id_producto = puser.productos.id WHERE puser.relacion_requisiciones.id_requisicion =  '$id_requisicion'";
//die($q);
$cArticulo= $conn->Execute($q);
$cantidad= $cArticulo->RecordCount();

//$pdf->Ln();
//$pdf->SetFont('Courier','B',12);
//if(!$preRequisicion)
//	$titulo = 'REQUISICION';
//else
//	$titulo = 'PRE-REQUISICION';	
//$pdf->Cell(175,4, $titulo,0, '','C');

//$pdf->SetFillColor(232 , 232, 232);

$pdf->Ln();
$pdf->SetFont('Courier','B',8);
$pdf->Cell(23,4, 'Descripcion:',0, '','L');
$pdf->Line(37,36,190,36);
$pdf->Ln();
$pdf->SetFont('Courier','',8);
//$pdf->MultiCell(190,4, $oRequisicion->motivo,0, '','L');
$descripcion = dividirStr($oRequisicion->motivo, intval(190/$pdf->GetStringWidth('M')));
$pdf->Cell(190,4, $descripcion[0],0,'','L');
$hay_desc = next($descripcion);
//for($i=1;$hay_desc!=false;$i++){
for($i=1;$i<2;$i++){
	$pdf->Ln();
	$pdf->Cell(190,4,$descripcion[$i],0,'','L');
}

//$pdf->Ln(3);
//$pdf->Cell(175,0.2, '',1, '','C');**
$pdf->Line(15,50,190,50);
//$pdf->Ln(2);
$pdf->SetXY(15,51);
$pdf->SetFont('Courier','B',12);
$pdf->Cell(175,4, 'ARTICULOS INCLUIDOS EN LA REQUISICION',0, '','C');
$pdf->Line(15,56,190,56);
$pdf->Ln(1);
//$pdf->Cell(175,0.2, '',1, '','C');**

$pdf->Ln();
$pdf->SetFont('Courier','B',8);
//$pdf->Cell(25,4, 'Categoria',LR,'','L');
//$pdf->Cell(25,4, 'Partida',LR,'','L');
$pdf->Cell(140,4, '  ',LR,'','C');
$pdf->Cell(20,4, 'Unidad',LR,'','C');
$pdf->Cell(15,4, '  ',LR, '','C');
$pdf->Ln();
//$pdf->Cell(25,4, 'Programatica',LR,'','L');
//$pdf->Cell(25,4, 'Presupuestaria',LR,'','L');
$pdf->Cell(140,4, 'Descripcion',LRB,'','C');
$pdf->Cell(20,4, 'Medida',LRB,'','C');
$pdf->Cell(15,4, 'Cantidad',LRB, '','C');
$pdf->Ln();
//$pdf->Cell(175,0.2, '',1, '','C');**


$pdf->Line(15,50,15,230);
$pdf->Line(190,50,190,230);
$pdf->Line(15,230,190,230);

//$pdf->Ln();

$pdf->SetFont('Courier','',8);
for ($j=0;$j<$cantidad;$j++){
	
	$pdf->Ln();
	//$pdf->Cell(25,4, $cArticulo->fields['id_categoria'],LR, '','L');
	//$pdf->Cell(25,4, $cArticulo->fields['id_partida'],LR, '','L');
	$pdf->Cell(140,4, $cArticulo->fields['descripcion'],0, '','L');
	$pdf->Cell(20,4, $cArticulo->fields['unidad_medida'],0, '','C');
	$pdf->Cell(15,4, $cArticulo->fields['cantidad'],0, '','R');
	$cArticulo->movenext();
}

/*$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');*/

/*$pdf->Ln(25);

if(!$preRequisicion){
	$pdf->Cell(175,4, '_______________________',0,'','C');
	$pdf->Ln();
	$pdf->Cell(175,4, 'JEFE DE COMPRAS',0,'','C');
}*/

//$pdf->Cell(175,0.2, '',1, '','C');
$pdf->Line(15,235,90,235);
$pdf->Line(15,235,15,260,15);
$pdf->Line(90,235,90,260);
$pdf->Line(15,260,90,260);
$pdf->SetXY(15,235);
$pdf->Cell(15,4,'Elaborado Por:',0,'','L');
$pdf->Ln();
$pdf->Cell(40,4,$usuario->nombre.' '.$usuario->apellido,0,'','L');

$pdf->Line(115,235, 190,235);
$pdf->Line(115,235,115,260);
$pdf->Line(190,235,190,260);
$pdf->Line(115,260,190,260);
$pdf->SetXY(115,235);
$pdf->Cell(15,4,'Gerencia:',0,'','L');


//$pdf->AddPage();
$pdf->Output();

?>

