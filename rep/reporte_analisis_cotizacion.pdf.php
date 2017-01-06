<?
include("comun/ini.php");
include("Constantes.php");
$id_requisicion = $_REQUEST['id_requisicion'];
$prov_ganador = $_REQUEST['prov_ganador'];
if(empty($id_requisicion))
	header ("location: revision_requisicion.php");
//$oRequisicion = new requisiciones;
$oRequisicion = new requisicion_global;
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
			$this->Image ("images/logoa.jpg",15,4,26);//logo a la izquierda 
			$this->SetXY(42, 20); 
			$textoCabecera = PAIS."\n";
			$textoCabecera.= UBICACION."\n";
			$textoCabecera.= ENTE."\n";
			$this->MultiCell(50,2, $textoCabecera, 0, 'L');

			$oReviRequi = unserialize($_SESSION['pdf']);

			$this->SetXY(150, 20); 
			$textoDerecha = "Fecha: ".date('d/m/Y')."\n";
			$textoDerecha.= "Fecha Generac.:".muestrafecha($oReviRequi->fecha)."\n";
			$textoDerecha.= "Fecha Aprob.:".muestrafecha($oReviRequi->fecha_aprobacion)."\n";
			$textoDerecha.= "Pag: ".$this->PageNo()." de {nb}\n";
			$this->MultiCell(50,2, $textoDerecha, 0, 'L');
			
			$this->SetFont('Courier','b',12);
			$this->Text(40, 40, " ",0,'',R);
			$this->Text(115, 40, " Requisicion Nro.:".$oReviRequi->id."\n");
			//$this->Line(15, 41, 190, 41);
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


//OBTIENE EL ID DEL PROVEEDOR GANADOR POR CADA PRODUCTO CON COSTO MAS BAJO DE LA REQUISICION P
/*$q="SELECT DISTINCT ON (puser.proveedores_requisicion.id_producto) puser.proveedores_requisicion.id_producto AS idprod, ";
$q.="puser.proveedores_requisicion.costo, ";
$q.="Min(puser.proveedores_requisicion.id_proveedor) AS proveedor, ";
$q.="(puser.proveedores_requisicion.costo*puser.relacion_requisiciones.cantidad) AS total, ";
$q.="puser.relacion_requisiciones.cantidad, ";
$q.="puser.proveedores_requisicion.id AS idg ";
$q.="FROM puser.relacion_requisiciones ";
$q.="Inner Join puser.proveedores_requisicion ON puser.relacion_requisiciones.id_producto = puser.proveedores_requisicion.id_producto ";
$q.="AND puser.relacion_requisiciones.id_requisicion = puser.proveedores_requisicion.id_requisicion ";
$q.="WHERE puser.relacion_requisiciones.id_requisicion = '$id_requisicion' ";
$q.="AND puser.proveedores_requisicion.costo > 0 ";
$q.="GROUP BY ";
$q.="puser.proveedores_requisicion.id_producto, ";
$q.="puser.proveedores_requisicion.costo, ";
$q.="puser.relacion_requisiciones.cantidad, ";
$q.="puser.proveedores_requisicion.id";*/
//die($q);
$q = "SELECT pr.id_producto, ((pr.costo * rr.cantidad) + (pr.costo * rr.cantidad * (pr.iva/100))) as total, pr.id as idg FROM puser.proveedores_requisicion pr ";
$q.= "Inner Join puser.relacion_gbl_requisicion rr ON pr.id_requisicion = rr.id_gbl_requisicion AND pr.id_producto = rr.id_producto ";
$q.= "WHERE pr.id_proveedor = '$prov_ganador' AND pr.id_requisicion = '$id_requisicion'";
//die($q);
$oAnalisis= $conn->Execute($q);
$cantidad= $oAnalisis->RecordCount();
//$id_ganadores = $oAnalisis->fields['id_proveedor'];
$id_ganadores = array();
for($i=0;$i<$cantidad;$i++){
	array_push($id_ganadores, $oAnalisis->fields['idg']);
	$oAnalisis->movenext();
}


$pdf->Ln(3);
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln();
$pdf->SetFont('Courier','B',12);
$pdf->Cell(175,4, 'ANALISIS DE COTIZACIONES',0, '','C');

$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');
//$pdf->SetFillColor(232 , 232, 232);
$pdf->Ln(3);
$pdf->SetFont('Courier','B',8);
$pdf->Cell(30,4, 'Unidad Ejecutora:',0, '','L' );
$pdf->SetFont('Courier','',8);
$pdf->Cell(100,4,$oRequisicion->unidad_ejecutora,0, '','L');

$pdf->Ln();
$pdf->SetFont('Courier','B',8);
$pdf->Cell(14,4, 'Status:',0, '','L');
$pdf->SetFont('Courier','',8);
$pdf->Cell(100,4, $oRequisicion->nom_status,0, '','L');

 $descripcion = dividirStr($oRequisicion->motivo, intval(175/$pdf->GetStringWidth('M')));
 

$pdf->Ln();
$pdf->SetFont('Courier','B',8);
$pdf->Cell(23,4, 'Descripcion:',0, '','L');
$pdf->SetFont('Courier','',8);
$pdf->Cell(175,4, $descripcion[0],0, '','L');

		$hay = next($descripcion);
  		for ($i=1;$hay!==false; $i++)
  		{	
			$pdf->Ln();
			$pdf->Cell(23,4, '',0, '','L');
			$pdf->Cell(175,4, $descripcion[$i],0, '','L');
			$hay = next($descripcion);
		}

$pdf->Ln(4);
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln(2);
$pdf->SetFont('Courier','B',12);
$pdf->Cell(175,4, 'Artículos cotizados por el proveedor',0, '','C');

$pdf->Ln(6);
$pdf->Cell(175,0.2, '',1, '','C');
$pdf->Ln(2);
//$oAnaRequi= new analisis_cotizacion;
$provee = analisis_cotizacion::getProveedoresporRequisicion($conn, $id_requisicion);
foreach($provee as $prov){
	$pdf->Ln();
	$pdf->SetFont('Courier','B',10);
	$pdf->Cell(25,4, 'Proveedor:',0,'','L');
	$pdf->Cell(70,4, $prov->nombre,0,'','L');
	$pdf->Ln();
	$pdf->Cell(175,0.2, '',1, '','C');
	$pdf->Ln();
	
	
	$pdf->Ln();
	$pdf->SetFont('Courier','B',8);
	$pdf->Cell(50,4, '  ',LR,'','L');
	$pdf->Cell(18,4, 'Unidad',LR,'','C');
	$pdf->Cell(17,4, '  ',LR,'','C');
	$pdf->Cell(20,4, 'Precio',LR,'','C');
	$pdf->Cell(25,4, 'Precio',LR, '','C');
	$pdf->Cell(20,4, '  ',LR,'','L');
	$pdf->Cell(25,4, 'Precio',LR,'','C');
	$pdf->Ln();
	
	$pdf->Cell(50,4, 'Articulo',LR,'','L');
	$pdf->Cell(18,4, 'Medida',LR,'','C');
	$pdf->Cell(17,4, 'Cantidad',LR,'','C');
	$pdf->Cell(20,4, 'Unitario',LR,'','C');
	$pdf->Cell(25,4, 'Total',LR, '','C');
	$pdf->Cell(20,4, 'IVA',LR,'','C');
	$pdf->Cell(25,4, 'con IVA',LR,'','C');
	
	$pdf->Ln();
	$pdf->Cell(175,0.2, '',1, '','C');

	$pdf->Ln();

	$tot_precio=0;
	$tot_prec_imp=0;
	$tot_ganado_prov=0;

	$pdf->SetFont('Courier','',8);
	$oArti = analisis_cotizacion::getArticulosporProveedor($conn, $id_requisicion, $prov->id_proveedor);
	foreach($oArti as $arti){
		
		$impuesto= ($arti->costo * $arti->iva/100) * $arti->cantidad;
		$precio= $arti->costo * $arti->cantidad;
		$total = $precio + $impuesto;
		$tot_precio+= $precio;
		$tot_prec_imp+= $total;
		
		$aux = 0;
		$pdf->Ln();
		//die("prov ".var_dump($arti->id_pr)."<br>  idg: ".var_dump($id_ganadores));
		if(in_array($arti->id_pr,$id_ganadores)){
			$pdf->SetTextColor(206,36,8);
			$aux=1;
			$tot_ganado_prov+=$total;
		} else {
			$pdf->SetTextColor(0,0,0);
		}	
		$pdf->SetFont('Courier','',7);
		$pdf->Cell(50,4, $arti->descripcion,LR, '','L');
		$pdf->Cell(18,4, $arti->unidad_medida,LR, '','L');
		$pdf->Cell(17,4, $arti->cantidad,LR, '','C');
		$pdf->Cell(20,4, muestrafloat($arti->costo),LR, '','R');
		$pdf->Cell(25,4, muestrafloat($precio),LR, '','R');
		$pdf->Cell(20,4, muestrafloat($impuesto),LR, '','R');
		$pdf->Cell(25,4, muestrafloat($total),LR, '','R');
		if($aux==1)
			$pdf->Cell(3,4,'**',0,'','C');
	}

	$pdf->SetTextColor(0,0,0);
	$pdf->Ln();
	$pdf->Cell(175,0.2, '',1, '','C');
	$pdf->Ln();
	$pdf->Cell(60,4,'Total:',0,'','L');
	$pdf->Cell(45,4, '  ',0,'','R');
	$pdf->Cell(25,4, muestrafloat($tot_precio),0,'','R');
	//$pdf->Cell(25,4, '(Bs.F.: '.muestrafloat($tot_precio/1000).')',0,'','R');
	$pdf->Ln();
	$pdf->Cell(60,4,'Total Precio + IVA:',0,'','L');
	$pdf->Cell(45,4, '     ',0,'','R');
	$pdf->Cell(70,4, muestrafloat($tot_prec_imp),0,'','R');
	//$pdf->Cell(25,4, '(Bs.F.: '.muestrafloat($tot_prec_imp/1000).')',0,'','R');
	if($anoCurso == 2007){
		$pdf->Ln();
		$pdf->Cell(175,4, 'Bs.F.: '.muestrafloat($tot_prec_imp/1000),0,'','R');
	}
	$pdf->Ln();
	$pdf->SetFont('Courier','B',8);
	$pdf->Cell(60,4,'Total Ganado en Cotizacion:',0,'','L');
	$pdf->Cell(45,4, '     ',0,'','R');
	$pdf->Cell(70,4, muestrafloat($tot_ganado_prov),0,'','R');
	if($anoCurso == 2007){
		$pdf->Ln();
		$pdf->Cell(175,4, 'Bs.F.: '.muestrafloat($tot_ganado_prov/1000),0,'','R');
	}
	$pdf->Ln();
	$pdf->SetFont('Courier','',8);
	$pdf->Cell(175,0.2, '',1, '','C');
	$pdf->Ln(5);
	
}
$pdf->Ln(10);
$pdf->SetTextColor(206,36,8);
$pdf->SetFont('Courier','B',8);
$pdf->Cell(60,4,'** Proveedor Ganador',0,'','L');

$pdf->SetTextColor(0,0,0);
$pdf->Ln(25);
$pdf->Cell(175,4, '_______________________',0,'','C');
$pdf->Ln();
$pdf->Cell(175,2, 'JEFE DE COMPRAS',0, '','C');



//$pdf->AddPage();


$pdf->Output();
?>
