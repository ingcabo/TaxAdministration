<?
include("comun/ini.php");
$id=$_REQUEST['id'];
$q = "SELECT 
					publicidad.publicidad.*, 
					vehiculo.contribuyente.id AS contribuyente, 
					vehiculo.contribuyente.primer_nombre, 
					vehiculo.contribuyente.segundo_nombre, 
					vehiculo.contribuyente.primer_apellido, 
					vehiculo.contribuyente.segundo_apellido, 
					vehiculo.contribuyente.direccion, 
					vehiculo.contribuyente.razon_social, 
					vehiculo.contribuyente.telefono, 
					vehiculo.contribuyente.ciudad_domicilio, 
					vehiculo.contribuyente.domicilio_fiscal, 
					publicidad.calculo_publicidad.* 
				FROM 
					publicidad.publicidad 
				INNER JOIN 
					vehiculo.contribuyente 
				ON 
					(publicidad.publicidad.id_contribuyente=vehiculo.contribuyente.id) 
				INNER JOIN 
					publicidad.calculo_publicidad 
				ON 
					(publicidad.publicidad.patente=publicidad.calculo_publicidad.patente) 
				AND 
					(publicidad.publicidad.id = publicidad.calculo_publicidad.id_publicidad) 
				WHERE 
					publicidad.publicidad.id = '$id'"; //die($q);
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$id = $r->fields['id'];  
			$fecha = $r->fields['fecha_actual']; 
			$patente = $r->fields['patente']; 
			$id_contribuyente = $r->fields['id_contribuyente'];
			$cod_ins = $r->fields['cod_inspector'];
			$nombre_contribuyente = $r->fields['primer_nombre']." ".$r->fields['segundo_nombre']." ".$r->fields['primer_apellido']." ".$r->fields['segundo_apellido'];
			$razon_contribuyente = $r->fields['razon_social'];
			$ciudad_contribuyente = $r->fields['ciudad_domicilio'];
			$telefono_contribuyente = $r->fields['telefono'];
			$domiciliotrib_contribuyente = $r->fields['domicilio_fiscal'];
			$id_solicitud = $r->fields['id_solicitud'];
			$fec_desde = $r->fields['fec_desde'];
			$fec_hasta = $r->fields['fec_hasta'];
}
if($id_solicitud=='1'){$solicitud="Publicidad Eventual";} else {$solicitud="Publicidad Eventual";}	
class PDF extends FPDF
{
	function Header()
	{
			$this->SetLeftMargin(15);
			$this->SetFont('Courier','',6);
			$this->Ln(1);
			$this->Image ("images/logoa.jpg",15,4,26);//logo a la izquierda 
			$this->SetXY(42, 20); 
			$textoCabecera = "REPUBLICA BOLIVARIANA DE VENEZUELA\n";
			$textoCabecera.= "VALENCIA, ESTADO CARABOBO\n";
			$textoCabecera.= "ALCALDIA DE LIBERTADOR\n";
			$textoCabecera.= "DIRECCION DE HACIENDA";
			$textoCabecera.= $id_publicidad;
			$this->MultiCell(50,2, $textoCabecera, 0, 'L');
			$this->SetXY(150, 20); 
			$textoDerecha = "Fecha Impresion: ".date('d/m/Y')."\n";
			$textoDerecha.= "Fecha Generac.:".muestrafecha($fecha)."\n";
			$textoDerecha.= "Pag: ".$this->PageNo()." de {nb}\n";
			$this->MultiCell(50,2, $textoDerecha, 0, 'L');
			$this->Ln();
			$this->SetFont('Courier','b',12);
			$tipoOrden = "RECIBO DE PAGO";
			$this->Text(85, 40, $tipoOrden);
			$this->Line(15, 41, 190, 41);
			$this->Ln(20);
		
	}

	function Footer()
	{	
		$this->SetFont('Arial','I',8);
	}
}
//Creación del objeto de la clase heredada
$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Courier','',8);
$pdf->SetLeftMargin(15);

$pdf->SetFont('Courier','B',10);
$pdf->Cell(52,4, 'Datos del Contribuyente:',0, '','C' );
$pdf->Ln(5);
$pdf->Cell(175,0.1, '',1, '','C');
$pdf->Ln(6);

$pdf->SetFont('Courier','',8);
$pdf->Cell(13,4, 'Sr(a):',0, '','C' );
$pdf->Cell(52,4, $nombre_contribuyente,0, '','C');

$pdf->Cell(46,4, 'Patente Nro.:',0, '','C' );
$pdf->Cell(5,4, $patente,0, '','R');

$pdf->Ln();
$pdf->Cell(16,4, 'C.I/RIF:',0, '','C' );
$pdf->Cell(40.5,4, $identificacion ." / ".$rif,0, '','C');

$pdf->Cell(63.5,4, 'Razon Social:',0, '','C' );
$pdf->Cell(8,4, $razon_contribuyente,0, '','C');

$pdf->Ln();
$pdf->Cell(20,4, 'Direccion:',0, '','C');
$pdf->Cell(34,4, $ciudad_contribuyente ." - ". $domiciliotrib_contribuyente,0, '','C');

$pdf->Cell(62,4, 'Telefono:',0, '','C' );
$pdf->Cell(1,4, $telefono_contribuyente,0, '','C');

$pdf->Ln(4);
$pdf->Cell(175,0.1, '',1, '','C');

$pdf->Ln(8);
$pdf->SetFont('Courier','B',10);
$pdf->Cell(48,4, 'Datos de la Publicidad:',0, '','C' );
$pdf->Ln(5);
$pdf->Cell(175,0.1, '',1, '','C');
$pdf->Ln(6);

$pdf->SetFont('Courier','',8);
$pdf->Cell(35,4, 'Tipo de Solicitud:',0, '','C' );

$pdf->SetFont('Courier','',8);
$pdf->Cell(30,4, $solicitud,0, '','C' ); 

$pdf->Ln(6);
$pdf->SetFont('Courier','',8);
$pdf->Cell(28,4, 'Periodo Desde:',0, '','C' );
$pdf->Cell(20,4, muestrafecha($fec_desde),0, '','C' );

$pdf->Ln(6);
$pdf->SetFont('Courier','',8);
$pdf->Cell(28,4, 'Periodo Hasta:',0, '','C' );
$pdf->Cell(20,4, muestrafecha($fec_hasta),0, '','C' );

$pdf->Ln(7.5);
$pdf->Cell(175,0.1, '',1, '','C');

$pdf->Ln(8); 

$pdf->Ln();    
$pdf->Ln();
$pdf->Ln();

$pdf->SetFont('Courier','B',10);
$pdf->Cell(15,4, 'Detalle del Pago:',0, '','L' );
$pdf->Ln(7);
$pdf->Cell(175,0.1, '',1, '','C');
$pdf->Ln();
$pdf->Ln();

$pdf->SetFont('Courier','',8);
$pdf->Cell(25,4, 'Tipo Publicidad',0, '','C' );
$pdf->Cell(70,4, 'Cantidad Publicidad',0, '','R' );
$pdf->Cell(30,4, 'Aforo',0, '','R');
$pdf->Cell(50,4, 'Impuesto',0, '','R');
$pdf->Ln(5);
$pdf->Cell(175,0.1, '',1, '','C');
$pdf->Ln(4);
$pdf->SetFont('Courier','',8);
$pdf->Ln();

$q = "SELECT publicidad.calculo_publicidad.*, publicidad.tipo_publicidad.descripcion  FROM publicidad.calculo_publicidad INNER JOIN publicidad.tipo_publicidad ON (publicidad.calculo_publicidad.id_tipopub=publicidad.tipo_publicidad.cod_publicidad)  WHERE publicidad.calculo_publicidad.id_publicidad='$id'"; die($q);
		$r = $conn->Execute($q);
		$i=0;
		while(!$r->EOF){
			$pdf->Cell(50,4, $r->fields['descripcion'],0, '','L');
			$pdf->Cell(28,4, $r->fields['cantidad']." ".$r->fields['unidad'],0, '','C');
			$pdf->Cell(63,4, muestrafloat($r->fields['aforo']).' ',0, '','C');
			$pdf->Cell(35,4, muestrafloat(($r->fields['cantidad'])*($r->fields['aforo'])).' Bs.',0, '','C');
			$i++;
			$r->movenext();
		}	
		
$pdf->Ln();$pdf->Ln();
$pdf->Ln(1);
$pdf->Cell(175,0.1, '',1, '','C');
$pdf->Ln(1);
$q = "SELECT sum(publicidad.calculo_publicidad.cantidad*publicidad.calculo_publicidad.aforo) as total FROM publicidad.calculo_publicidad where publicidad.calculo_publicidad.id_publicidad='$id'";
$r = $conn->Execute($q);
if(!$r->EOF){
			$total= $r->fields['total'];  
	}
$pdf->SetFont('Courier','B',8);
$pdf->Cell(125,4, 'Total:',0, '','R');
$pdf->Cell(43.5,4, muestrafloat($total).' Bs.',0, '','R');
$pdf->Ln(4);
$pdf->Cell(175,0.1, '',1, '','C');
$i="select * from publicidad.publicidad where id='$id'";// die($i);
$k = $conn->Execute($i);
if(!$k->EOF){
$nro_documento= $k->fields['nro_documento'];
$total_impuesto = $k->fields['monto'];
}//die($total_impuesto);
$n="select public.banco.descripcion from publicidad.publicidad inner join public.banco on (publicidad.publicidad.banco=public.banco.id) where publicidad.publicidad.id='$id'";
$k = $conn->Execute($n);
if(!$k->EOF){
$banco = $k->fields['descripcion'];
}
$m="select vehiculo.forma_pago.descripcion from publicidad.publicidad inner join vehiculo.forma_pago on (publicidad.publicidad.pago=vehiculo.forma_pago.id) where publicidad.publicidad.id='$id'";
$k = $conn->Execute($m);
if(!$k->EOF){
$tipo_pago = $k->fields['descripcion'];
}
$pdf->Ln();
$pdf->Ln(8);
$pdf->SetFont('Courier','B',10);
$pdf->Cell(15,4, 'Forma de Pago:',0, '','L' );
$pdf->Ln();
$pdf->Ln();
$pdf->Cell(175,0.1, '',1, '','C');
$pdf->Ln(1.5);
$pdf->SetFont('Courier','B',8);
$pdf->Cell(20,4, 'Tipo',0, '','L');
$pdf->Cell(28,4, 'Banco',0, '','R');
$pdf->Cell(50,4, 'Nro Documento',0, '','R');
$pdf->Cell(50,4, 'Monto',0, '','R');
$pdf->Ln();
$pdf->Cell(175,0.1, '',1, '','C');
$pdf->Ln();
$pdf->SetFont('Courier','',8);
$pdf->Ln(2);
$pdf->Cell(15,4, $tipo_pago,0, '','C');
$pdf->Cell(60,4, $banco,0, '','C');
$pdf->Cell(22,4, $nro_documento,0, '','C');
$pdf->Cell(96,4, muestrafloat($total_impuesto).' Bs.',0, '','C');
$pdf->Ln(6);
$pdf->Cell(175,0.1, '',1, '','C');
$pdf->Ln();
$pdf->SetFont('Courier','B',8);
$pdf->Cell(125,4, 'Total:',0, '','R');
$pdf->Cell(40,4, muestrafloat($total_impuesto).' Bs.',0, '','C');

$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();

$pdf->SetFont('Courier','B',8);
$pdf->Cell(25,4, 'Cajero:_______________________________');
$pdf->Output();
?>
