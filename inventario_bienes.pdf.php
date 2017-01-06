<?
	include("comun/ini.php");
	$desde=$_REQUEST['fecha_desde'];
	$hasta= $_REQUEST['fecha_hasta'];
	$idUE = $_REQUEST['id_unidad_ejecutora'];
	//$idUE = '01';
	$cUnidEjecutora = new unidades_ejecutoras;
	$cUnidEjecutora->get($conn,$idUE,$escEnEje);
	
	$_SESSION['pdf'] = serialize($cUnidEjecutora);
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
	function Header()
	{
			$cUnidEjecutora = unserialize($_SESSION['pdf']);
			$this->SetLeftMargin(13);
			$this->SetFont('Courier','',6);
			$this->Ln(1);
			$this->Image ("images/logoa.jpg",13,4,26);//logo a la izquierda 
			$this->SetXY(42, 12); 
			$textoCabecera = "ENTIDAD PROPIETARIA: ALCALDIA DE MARACAIBO\n";
			$textoCabecera.= "UNIDAD DE TRABAJO: ".$cUnidEjecutora->descripcion."\n";
			$textoCabecera.= "DIRECCION O LUGAR: MARACAIBO";
			$this->MultiCell(70,2, $textoCabecera, 0, 'L');

			//$ovehiculo = unserialize($_SESSION['pdf']);
			//$tipo = $ovehiculo->id_tipo_documento;

			$this->SetXY(230, 12); 
			$textoDerecha = "Fecha Impresion: ".date('d/m/Y')."\n";
			//$textoDerecha.= "Nro.:".$nro_recibo."\n";
			$textoDerecha.= "Fecha Generac.:".muestrafecha($fecha)."\n";
			//$textoDerecha.= "Fecha Aprob.:".muestrafecha($oOrden->fecha_aprobacion)."\n";
			$textoDerecha.= "Formulario BM-1\n";
			$textoDerecha.= "Pag: ".$this->PageNo()." de {nb}\n";
			$this->MultiCell(50,2, $textoDerecha, 0, 'L');
			
			$this->Ln();

			$this->SetFont('Courier','b',10);
			//if($tipo == '002')
			//	$tipoOrden = "Orden de Servicio";
			//elseif($tipo == '009')
				$tipoOrden = "INVENTARIO DE BIENES INMUEBLES";
			
			$this->Text(105, 32,$tipoOrden);
			$this->Line(13, 35, 268, 35);
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
$pdf=new PDF('l','mm','letter');
$pdf->AliasNbPages();
//$pdf->AddPage();
$pdf->SetFont('Courier','',8);
$pdf->SetLeftMargin(10);

//$oespectaculo = new espectaculo;
//$oespectaculo_id_contribuyente = $oespectaculo->id_contribuyente;die($oespectaculo_id_contribuyente);
//$oespectaculo->get($conn, $oespectaculo->id_contribuyente);

//$ocontribuyente = new contribuyente;
//$ocontribuyente->get($conn, $oespectaculo->id_contribuyente);
//$pdf->SetFillColor(232 , 232, 232);
//$pdf->Ln();//$pdf->Ln();
//$pdf->Ln(5);


	$pdf->AddPage();
	$pdf->SetFont('Courier','',8);
		
	$pdf->Cell(50,4,'CLASIFICACION (CODIGO)','LRTB','','C');
	$pdf->Cell(65,4,'NOMBRE Y DESCRIPCION','LRT','','C');
	$pdf->Cell(25,4,'ORDEN','LRT','','C');
	$pdf->Cell(25,4,'ORDEN','LRT','','C');
	$pdf->Cell(20,4,'AÑO '.$anoCurso,'LRT','','C');
	$pdf->Cell(30,4,'VALOR','LRT','','C');
	$pdf->Cell(40,4,'VALOR','LRT','','C');
	$pdf->Ln();
	$pdf->Cell(15,4,' ','LR','','C');
	$pdf->Cell(20,4,' ','LR','','C');
	$pdf->Cell(15,4,' ','LR','','C');
	$pdf->Cell(65,4,'DE LOS','LR','','C');
	$pdf->Cell(25,4,'DE','LR','','C');
	$pdf->Cell(25,4,'DE','LR','','C');
	$pdf->Cell(20,4,' ','LR','','C');
	$pdf->Cell(30,4,'UNITARIO','LR','','C');
	$pdf->Cell(40,4,'TOTAL','LR','','C');
	$pdf->Ln();
	$pdf->Cell(15,4,'GRUPO','LRB','','C');
	$pdf->Cell(20,4,'SUB-GRUPO','LRB','','C');
	$pdf->Cell(15,4,'CANTIDAD','LRB','','C');
	$pdf->Cell(65,4,'ELEMENTOS','LRB','','C');
	$pdf->Cell(25,4,'PAGO','LRB','','C');
	$pdf->Cell(25,4,'COMPRA','LRB','','C');
	$pdf->Cell(20,4,'FECHA','LRB','','C');
	$pdf->Cell(30,4,'Bs.','LRB','','C');
	$pdf->Cell(40,4,'BS.','LRB','','C');
	$pdf->Ln();
	
		$sql = "SELECT oc.fecha, op.nrodoc, oc.nrodoc AS nrodoc_oc, p.descripcion, roc.cantidad, roc.precio_base, roc.monto, rcb.codigo AS sub_grupo, cb.codigo AS grupo "; 
		$sql.= "FROM puser.relacion_ordcompra roc ";
		$sql.= "INNER JOIN puser.orden_compra oc ON (roc.id_ord_compra = oc.id) ";
		$sql.= "INNER JOIN puser.productos p ON (roc.id_producto = p.id) ";
		$sql.= "INNER JOIN puser.relacion_clasificacion_bienes rcb ON (p.id_grupo = rcb.id) ";
		$sql.= "INNER JOIN puser.clasificador_bienes cb ON (rcb.id_grupo = cb.id) ";
		$sql.= "INNER JOIN finanzas.solicitud_pago sp ON (oc.nrodoc = sp.nroref) ";
		$sql.= "INNER JOIN finanzas.orden_pago op ON (sp.nrodoc = op.nroref) ";
		$sql.= "WHERE op.status = 2 AND p.id_grupo is not null ";
		$sql.= !empty($idUE) ? "AND oc.id_unidad_ejecutora = '".$idUE."' " : "";
		$sql.= !empty($desde) ? "AND oc.fecha >= '".guardaFecha($desde)."' " : "";
		$sql.= !empty($hasta) ? "AND oc.fecha <= '".guardaFecha($hasta)."' " : "";
		$sql.= "ORDER BY grupo, sub_grupo, op.nrodoc";
		//die($sql);		
		
		$pdf->SetFont('Courier','',6);
			//die($sql);
		$r = $conn->Execute($sql); //or die($q);
		while(!$r->EOF){
			$descProd = dividirStr($r->fields['descripcion'], intval(65/$pdf->GetStringWidth('M')));
			$cDescProd = count($descProd);
			$lineaFin = cDescProd;
			$marco = ($lineaFin>1) ? "'LR'" : "'LRB'";
			$auxPosition = true;
			$pdf->Cell(15,4,$r->fields['grupo'],$marco,0,'C');
			$pdf->Cell(20,4,$r->fields['sub_grupo'],$marco,0,'C');
			$pdf->Cell(15,4,muestraFloat($r->fields['cantidad']),$marco,0,'R');
			$pdf->Cell(65,4,utf8_decode($descProd[0]),$marco,0,'L');
			$pdf->Cell(25,4,$r->fields['nrodoc'],$marco,0,'C');
			$pdf->Cell(25,4,$r->fields['nrodoc_oc'],$marco,0,'C');
			$pdf->Cell(20,4,muestraFecha($r->fields['fecha']),$marco,0,'C');
			$pdf->Cell(30,4,muestrafloat($r->fields['precio_base']),$marco,0,'R');
			$pdf->Cell(40,4,muestrafloat($r->fields['monto']),$marco,0,'R');
				$hay_desc = next($descProd);
				for($j=1; $hay_desc!==false ;$j++)
				{
					$marco2 = ($lineaFin == $j+1) ? 'LRB' : 'LR';
					$pdf->Ln(4);
					$pdf->Cell(15,4,'',$marco2,0,'C');
					$pdf->Cell(20,4,'',$marco2,0,'C');
					$pdf->Cell(15,4,'',$marco2,0,'C');
					$pdf->Cell(65,4,utf8_decode($descProd[$j]),$marco2,0,'L');
					$pdf->Cell(25,4,'',$marco2,0,'C');
					$pdf->Cell(25,4,'',$marco2,0,'C');
					$pdf->Cell(20,4,'',$marco2,0,'C');
					$pdf->Cell(30,4,'',$marco2,0,'R');
					$pdf->Cell(40,4,'',$marco2,0,'R');
					$hay_desc = next($descProd);
				}
			
			$totalMonto+= $r->fields['monto'];
			$pdf->Ln();
			$r->movenext();
		}
		$pdf->Ln();
		$pdf->SetFont('Courier','',8);
		$pdf->Cell(205,4, 'TOTAL GENERAL: ','LBT','','C');
		$pdf->Cell(50,4, muestraFloat($totalMonto),'RBT','','R');	


$pdf->Output();
?>