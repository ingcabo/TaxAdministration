<?
	include("comun/ini.php");
	include("Constantes.php");
	$vector=$_REQUEST['vector'];//die($nro_recibo);
	$oOrdenPago = new orden_pago();
	$oOrdenPago->get($conn, $id, $escEnEje);
	//die($vector);
	
	function imprime_recibo($cambiados,$pdf,$conn){
		$pdf->Ln(2);
		
		$aux = 0;
		//die(var_dump($conn));
		for($i=0;$i<count($cambiados);){
			$pdf->SetFont('Arial','',10);
			$actual = $cambiados[$i][0];
			$pdf->AddPage();
			$pdf->Ln(2);
			$pdf->SetFont('Arial','B',12);
			$sql = "SELECT p.descripcion, tp.id_tipo_producto_clasif AS id_clasif FROM puser.productos p ";
			$sql.= "INNER JOIN puser.tipo_producto tp ON (p.id_tipo_producto = tp.id) ";
			$sql.= "WHERE p.id = '".$cambiados[$i][1]."' ";
			//die($sql);
			$row = $conn->Execute($sql);
			$pdf->Ln();
			if($row->fields['id_clasif']=='1')
				$tipo = 'Bienes Muebles y Semovientes';
				else
					$tipo = 'Materiales y Suministros';
			$pdf->Cell(255,6,$tipo,'LRBT','','C');
			$pdf->Ln(12);
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(30,6,utf8_decode('Requisicion Nº'),'LTB','','C');
			$pdf->Cell(35,6, 'Cantidad Despachada ', 'LRTB','','C');
			$pdf->Cell(30,6, 'Cantidad Entregada','RTB','','C' );
			$pdf->Cell(160,6, 'Descripcion', 'RTB','','C');
			$pdf->SetFont('Arial','',8);
			while($actual==$cambiados[$i][0] and $i<count($cambiados)){
				$sql2 = "SELECT p.descripcion FROM puser.productos p ";
				$sql2.= "WHERE p.id = '".$cambiados[$i][1]."' ";
				$row2 = $conn->Execute($sql2);
				$pdf->Ln();
				$pdf->Cell(30,6,$cambiados[$i][3],'LR','','C');
				$pdf->Cell(35,6,$cambiados[$i][2],'LR','','C');
				$pdf->Cell(30,6,$cambiados[$i][5]." / ".$cambiados[$i][4],'LR','','C');
				$pdf->Cell(160,6,$row2->fields['descripcion'],'LR','','L');
				$i++;
			}
			$pdf->Ln();
			$pdf->SetFont('Arial','B',10);
			$pdf->Cell(255,0.2, '',1, '','C');
			$pdf->Ln(16);
			$sql = "SELECT descripcion FROM puser.unidades_ejecutoras WHERE id = '".$cambiados[$i-1][0]."' AND id_escenario = '1111'";
			//die($sql);
			$row = $conn->Execute($sql);
			$pdf->Cell(125,4,'',0,'','C');
			$pdf->Cell(125,4,$row->fields['descripcion'],0,'','C');
			$pdf->Ln(1);
			$pdf->Cell(125,4,'_____________________________',0,'','C');
			$pdf->Cell(125,4,'_____________________________',0,'','C');
			$pdf->Ln(6);
			$pdf->Cell(125,4,'Jefe de Compras',0,'','C');
			$pdf->Cell(125,4,'Unidad Receptora',0,'','C');
			
	}
}

class PDF extends FPDF
{
	function Header()
	{
			$this->SetLeftMargin(18);
			$this->SetFont('Arial','',8);
			$this->Ln(1);
			$this->Image ("images/logoa.jpg",15,4,26);//logo a la izquierda 
			$this->SetXY(42, 20); 
			$textoCabecera = PAIS."\n";
			$textoCabecera.= UBICACION."\n";
			$textoCabecera.= ENTE."\n";
			$textoCabecera.= "DIVISION DE COMPRAS";
			$textoCabecera.= $id_espectaculo;
			$this->MultiCell(60,3, $textoCabecera, 0, 'L');

			//$ovehiculo = unserialize($_SESSION['pdf']);
			//$tipo = $ovehiculo->id_tipo_documento;

			$this->SetXY(230, 20); 
			$textoDerecha = "Fecha Impresion: ".date('d/m/Y')."\n";
			//$textoDerecha.= "Nro.:".$nro_recibo."\n";
			$textoDerecha.= "Fecha Generac.:".muestrafecha($fecha)."\n";
			//$textoDerecha.= "Fecha Aprob.:".muestrafecha($oOrden->fecha_aprobacion)."\n";
			$textoDerecha.= "Pag: ".$this->PageNo()." de {nb}\n";
			$this->MultiCell(50,3, $textoDerecha, 0, 'L');
			
			$this->Ln();

			$this->SetFont('Arial','b',12);
			//if($tipo == '002')
			//	$tipoOrden = "Orden de Servicio";
			//elseif($tipo == '009')
				$tipoOrden = "COMPROBANTE DE ENTREGA";
			
			$this->Text(120, 40, $tipoOrden);
			$this->Line(18, 41, 270, 41);
			$this->Ln(20);
			//$this->Text(160, 40, '#');
			//$this->Text(175, 40, $id);
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
$JsonRec = new Services_JSON();
$JsonRec=$JsonRec->decode(stripslashes($vector));
$pdf=new PDF('l','mm','letter');
$pdf->AliasNbPages();
//$pdf->AddPage();
$pdf->SetFont('Arial','',10);
$pdf->SetLeftMargin(15);
if(is_array($JsonRec)){
	for($i=0;$i<count($JsonRec);$i++){
		if($JsonRec[$i][2] != 0){
			$cambiados[] = $JsonRec[$i]; 
		}
	}
	
}else{
	//die("no entro primer arreglo");
}	
//die(var_dump($cambiados));
//for($i=0;$i<count($cambiados);$i++){
	imprime_recibo($cambiados,$pdf,$conn);
//}


	


$pdf->Output();
?>
