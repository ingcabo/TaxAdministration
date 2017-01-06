<?
	include("comun/ini.php");
	include("Constantes.php");
	$mes=$_REQUEST['mes'];
	
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
			$this->SetLeftMargin(13);
			$this->SetFont('Courier','',6);
			$this->Ln(1);
			$this->Image ("images/logoa.jpg",13,4,26);//logo a la izquierda 
			$this->SetXY(42, 12); 
			$textoCabecera = PAIS."\n";
			$textoCabecera.= UBICACION."\n";
			$textoCabecera.= ENTE."\n";
			$textoCabecera.= DEPARTAMENTO."\n";
			$this->MultiCell(70,2, $textoCabecera, 0, 'L');

			//$ovehiculo = unserialize($_SESSION['pdf']);
			//$tipo = $ovehiculo->id_tipo_documento;

			$this->SetXY(300, 12); 
			$textoDerecha = "Fecha Impresion: ".date('d/m/Y')."\n";
			//$textoDerecha.= "Nro.:".$nro_recibo."\n";
			$textoDerecha.= "Fecha Generac.:".muestrafecha($fecha)."\n";
			//$textoDerecha.= "Fecha Aprob.:".muestrafecha($oOrden->fecha_aprobacion)."\n";
			$textoDerecha.= "Pag: ".$this->PageNo()." de {nb}\n";
			$this->MultiCell(50,2, $textoDerecha, 0, 'L');
			
			$this->Ln();

			$this->SetFont('Courier','b',10);
			//if($tipo == '002')
			//	$tipoOrden = "Orden de Servicio";
			//elseif($tipo == '009')
				$tipoOrden = "CUADRO DE BIENES ADQUIRIDOS AÑO ".date('Y');
			
			$this->Text(105, 32,$tipoOrden);
			$this->Line(13, 35, 265, 35);
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
		
	$pdf->Cell(17,4,'FECHA','LRT','','C');
	$pdf->Cell(20,4,'ORDEN DE','LRT','','C');
	$pdf->Cell(35,4,'NOMBRE DE','LRT','','C');
	$pdf->Cell(20,4,'ORDEN DE','LRT','','C');
	$pdf->Cell(45,4,'DESCRIPCION','LRT','','C');
	$pdf->Cell(20,4,'CANTIDAD','LRT','','C');
	$pdf->Cell(25,4,'PRECIO','LRT','','C');
	$pdf->Cell(30,4,'PRECIO','LRT','','C');
	$pdf->Cell(40,4,'CODIGO','LRT','','C');
	$pdf->Ln();
	$pdf->Cell(17,4,'','LRB','','C');
	$pdf->Cell(20,4,'DE PAGO','LRB','','C');
	$pdf->Cell(35,4,'BENEFICIARIO','LRB','','C');
	$pdf->Cell(20,4,'COMPRA','LRB','','C');
	$pdf->Cell(45,4,'','LRB','','C');
	$pdf->Cell(20,4,'','LRB','','C');
	$pdf->Cell(25,4,'UNITARIO','LRB','','C');
	$pdf->Cell(30,4,'TOTAL','LRB','','C');
	$pdf->Cell(40,4,'PRESUPUESTARIO','LRB','','C');
	$pdf->Ln();
	
		$sql = "SELECT oc.fecha, op.nrodoc, pv.nombre, oc.nrodoc AS nrodoc_oc, p.descripcion, roc.cantidad, roc.precio_base, roc.monto, roc.id_categoria_programatica||' - '||roc.id_partida_presupuestaria AS presupuesto ";
		$sql.= "FROM puser.relacion_ordcompra roc ";
		$sql.= "INNER JOIN puser.orden_compra oc ON (roc.id_ord_compra = oc.id) ";
		$sql.= "INNER JOIN puser.proveedores pv ON (oc.rif = pv.id) ";			
		$sql.= "INNER JOIN puser.productos p ON (roc.id_producto = p.id) ";
		$sql.= "INNER JOIN finanzas.solicitud_pago sp ON (oc.nrodoc = sp.nroref) ";
		$sql.= "INNER JOIN finanzas.orden_pago  op ON (sp.nrodoc = op.nroref) ";
		$sql.= "WHERE op.status = 2 AND p.id_grupo is not null ";
		$sql.= "ORDER BY oc.fecha, op.nrodoc, presupuesto";
				
		
		$pdf->SetFont('Courier','',6);
			//die($sql);
		$r = $conn->Execute($sql); //or die($q);
		while(!$r->EOF){
			$Control = true;
			$contPresup = true;
			if(($antNroOC == $r->fields['nrodoc_oc']) && ($antNroOP == $r->fields['nrodoc'])){
				$Control = false; 
				if($antPresupuesto == $r->fields['presupuesto'])
					$contPresup = false;
			}
			$nombProv = dividirStr($r->fields['nombre'], intval(35/$pdf->GetStringWidth('M')));
			$descProd = dividirStr($r->fields['descripcion'], intval(45/$pdf->GetStringWidth('M')));
			$cNombProv = count($nombProv);
			$cDescProd = count($descProd);
			$antNroOP = $r->fields['nrodoc'];
			$antNroOC = $r->fields['nrodoc_oc'];
			$antPresupuesto = $r->fields['presupuesto'];
			$r->movenext();
			$auxNroOP = $r->fields['nrodoc'];
			$auxNroOC = $r->fields['nrodoc_oc'];
			//$auxPresupuesto = $r->fields['presupuesto'];
			$r->Move($r->AbsolutePosition()-1);
			/*$pdf->Cell(50,4,$auxNroOC,'',0,'C');
			$pdf->Cell(50,4,$r->fields['nrodoc_oc'],'',0,'C');
			$pdf->Cell(50,4,$auxPresupuesto,'',0,'C');
			$pdf->Cell(50,4,$r->fields['presupuesto'],'',0,'C');
			$pdf->Ln();*/
			$lineaFin = ($cNombProv >= $cDescProd) ? $cNombProv : $cDescProd;
			if($Control==false)
				$lineaFin = cDescProd;
			$siguiente = ($lineaFin>1) ? true : false; 
			$marco = ($lineaFin>1) ? "'LR'" : "'LRB'";
			$auxPosition = true;
			if(($auxNroOC == $r->fields['nrodoc_oc']) && ($auxNroOP == $r->fields['nrodoc'])){
				//$pdf->Cell(50,4,'Entro','',0,'C');
				//$pdf->Ln();
				$marco = "'LR'";
				$auxPosition = false;
			}
			
			//die("$pdf->Cell(12,4,'01-REG',$marco,0,'C');");	
			
			if($Control){
				$pdf->Cell(17,4,muestraFecha($r->fields['fecha']),$marco,0,'C');
				$pdf->Cell(20,4,$r->fields['nrodoc'],$marco,0,'C');
				$pdf->Cell(35,4,utf8_decode($nombProv[0]),$marco,0,'C');
				$pdf->Cell(20,4,$r->fields['nrodoc_oc'],$marco,0,'C');
			}else{
				$pdf->Cell(17,4,'',$marco,0,'C');
				$pdf->Cell(20,4,'',$marco,0,'C');
				$pdf->Cell(35,4,'',$marco,0,'C');
				$pdf->Cell(20,4,'',$marco,0,'C');
			}
			$pdf->Cell(45,4,utf8_decode($descProd[0]),$marco,0,'C');
			$pdf->Cell(20,4,muestrafloat($r->fields['cantidad']),$marco,0,'C');
			$pdf->Cell(25,4,muestrafloat($r->fields['precio_base']),$marco,0,'R');
			$pdf->Cell(30,4,muestrafloat($r->fields['monto']),$marco,0,'R');
			if($contPresup)
				$pdf->Cell(40,4,$r->fields['presupuesto'],$marco,0,'C');
			else
				$pdf->Cell(40,4,'',$marco,0,'C');
			
			
				/*$hay_prov = next($nombProv);
				$hay_desc = next($descProd)*/
				//for ($j=1; $hay_prov!==false; $i++)
				for($j=1; $siguiente!=false ;$j++)
				{
					$marco2 = ($lineaFin == $j+1 && $auxPosition!=false) ? 'LRB' : 'LR';
					$pdf->Ln(4);
					$pdf->Cell(17,4,'',$marco2,0,'C');
					$pdf->Cell(20,4,'',$marco2,0,'C');
					$pdf->Cell(35,4,utf8_decode($nombProv[$j]),$marco2,0,'C');
					$pdf->Cell(20,4,'',$marco2,0,'C');
					$pdf->Cell(45,4,utf8_decode($descProd[$j]),$marco2,0,'C');
					$pdf->Cell(20,4,'',$marco2,0,'C');
					$pdf->Cell(25,4,'',$marco2,0,'C');
					$pdf->Cell(30,4,'',$marco2,0,'C');
					$pdf->Cell(40,4,'',$marco2,0,'C');
					$siguiente = ($lineaFin == $j+1) ? false : true;
					/*$hay_prov = next($nombProv);
					$hay_desc = next($descPrd)*/
				}
			
			$totalMonto+= $r->fields['monto'];
			$pdf->Ln();
			$r->movenext();
		}
		$pdf->Ln();
		$pdf->SetFont('Courier','',8);
		$pdf->Cell(180,4, 'Total Bienes: ',0,'','L');
		$pdf->Cell(30,4, muestraFloat($totalMonto),0,'','R');	


$pdf->Output();
?>