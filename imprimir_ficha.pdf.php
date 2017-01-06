<?
	include("comun/ini.php");
	include("Constantes.php");
	
	
	$oproveedor = new proveedores;
	
	$idp = $_REQUEST['id_proveedores'];

	$oproveedor->get($conn, $idp);
	
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

//if(empty($oOrden->nrodoc))
//	header ("location: orden_servicio_trabajo.php");

//$_SESSION['pdf'] = serialize($ovehiculo);
//$_SESSION['pdf'] = serialize($oliquidacion);

class PDF extends FPDF
{
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
			$textoCabecera.= DEPARTAMENTO."\n";
			$this->MultiCell(50,2, $textoCabecera, 0, 'L');

			//$ovehiculo = unserialize($_SESSION['pdf']);
			//$tipo = $ovehiculo->id_tipo_documento;

			$this->SetXY(150, 20); 
			$textoDerecha = "Fecha: ".date('d/m/Y')."\n";
			//$textoDerecha.= "Nro.:".$oOrden->nrodoc."\n";
			//$textoDerecha.= "Fecha Generac.:".muestrafecha($oOrden->fecha)."\n";
			//$textoDerecha.= "Fecha Aprob.:".muestrafecha($oOrden->fecha_aprobacion)."\n";
			$textoDerecha.= "Pag: ".$this->PageNo()." de {nb}\n";
			$this->MultiCell(50,2, $textoDerecha, 0, 'L');
			
			$this->Ln();

			$this->SetFont('Courier','b',12);
			//if($tipo == '002')
			//	$tipoOrden = "Orden de Servicio";
			//elseif($tipo == '009')
				$tipoOrden = "FICHA DE DATOS DEL PROVEEDOR";
			$this->Text(60, 40, $tipoOrden);
			$this->Line(15, 41, 190, 41);
			$this->Ln(16);
			
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
$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Courier','',8);
$pdf->SetLeftMargin(15);

//$ocontribuyente = new contribuyente;
//$ocontribuyente->get($conn, $ovehiculo->id_contribuyente);
//$pdf->SetFillColor(232 , 232, 232);

$pdf->Ln();$pdf->Ln();
$desc_prov = dividirStr($oproveedor->nombre, intval(70/$pdf->GetStringWidth('M')));
$pdf->SetFont('Courier','',8);
$pdf->Cell(40,4, 'Nombre o Razón Social:',0, '','L' );
$pdf->Cell(70,4, $desc_prov[0],0, '','L');
$hay_ue = next($desc_prov);
  		for ($i=1; $hay_ue!==false; $i++)
  		{
    		$pdf->Ln();
			$pdf->Cell(40,4, '',0, '','L');
    		$pdf->Cell(70, 4, $desc_prov[$i], 0, '', 'L');
    		$hay_ue = next($desc_prov);
  		}

$pdf->Cell(25,4, 'Numero de RIF:',0, '','L' );
$pdf->Cell(40,4, $oproveedor->rif,0, '','L');

$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Courier','B',10);
$pdf->Cell(15,4, 'Representantes Legales:',0, '','L' );
$pdf->Ln();
$pdf->Ln();
$desc_rep_leg = dividirStr($oproveedor->nombre_representante, intval(60/$pdf->GetStringWidth('M')));
$pdf->SetFont('Courier','',8);
$pdf->Cell(40,4, 'Nombres y Apellidos:',0, '','L');
$pdf->Cell(60,4, $desc_rep_leg[0],0, '','L');
$pdf->Cell(40,4, 'Cedula de Identidad:',0, '','L');
$pdf->Cell(30,4, $oproveedor->ci_representante,0, '','L');
$hay_ue = next($desc_rep_leg);
  		for ($i=1; $hay_ue!==false; $i++)
  		{
    		$pdf->Ln();
			$pdf->Cell(40,4, '',0, '','L');
			$pdf->Cell(60,4, $desc_rep_leg[$i],0, '','L');
			$pdf->Cell(40,4, '',0, '','L');
			$pdf->Cell(30,4, '',0, '','L');
    		$hay_ue = next($desc_rep_leg);
  		}

$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Courier','B',10);
$pdf->Cell(15,4, 'Objeto de la Empresa:',0, '','L' );
$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Courier','',8);
$pdf->Cell(60,4, $oproveedor->obj_empresa,0, '','L');


$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Courier','B',10);
$pdf->Cell(15,4, 'Registro Mercantil:',0, '','L' );
$pdf->SetFont('Courier','',8);
$pdf->Ln();
$pdf->Cell(20,4, 'Fecha:',0, '','L');
$pdf->Cell(40,4, $oproveedor->registro_const,0, '','L');
$pdf->Cell(20,4, 'Numero:',0, '','L');
$pdf->Cell(40,4, $oproveedor->datos_reg,0, '','L');

$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Courier','B',10);
$pdf->Cell(30,4, 'Direccion de la Empresa:',0, '','L' );
$pdf->Ln();

$pdf->Ln();
$pdf->SetFont('Courier','',8);
$pdf->Cell(29,4, 'Entidad Federal:',0, '','L' );
$r = $conn->Execute("SELECT descripcion FROM puser.estado WHERE id='$oproveedor->id_estado'");
$pdf->SetFont('Courier','',8);
$pdf->Cell(30,4, $r->fields['descripcion'],0, '','L' ); 

$pdf->Cell(18,4, 'Municipio:',0, '','L' );

$r = $conn->Execute("SELECT descripcion FROM puser.municipios WHERE id='$oproveedor->id_municipio'");
$pdf->SetFont('Courier','',8);
$pdf->Cell(30,4, $r->fields['descripcion'],0, '','L' );

$pdf->Cell(18,4, 'Parroquia:',0, '','L' );

$r = $conn->Execute("SELECT descripcion FROM puser.parroquias WHERE id='$oproveedor->id_parroquia'");
$pdf->Cell(50,4, $r->fields['descripcion'],0, '','L' );

$pdf->Ln();
$pdf->Cell(17,4, 'Telefono:',0, '','L' );
$pdf->Cell(42,4, $oproveedor->telefono,0, '','L' );
$pdf->Cell(10,4, 'Fax:',0, '','L' );
$pdf->Cell(38,4, $oproveedor->fax,0, '','L' );
$pdf->Cell(11,4, 'Email:',0, '','L' );
$pdf->Cell(50,4, $oproveedor->email,0, '','L' );

$pdf->Ln();
$pdf->SetFont('Courier','',8);
$pdf->Cell(20,4, 'Direccion:',0, '','L' );
$pdf->Cell(100,4, $oproveedor->direccion,0, '','L' );

$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Courier','B',10);
$pdf->Cell(30,4, 'Capital de la Empresa:',0, '','L' );
$pdf->Ln();

$pdf->Ln();
$pdf->SetFont('Courier','',8);
$pdf->Cell(35,4, 'Capital Suscrito:',0, '','L' );
$pdf->Cell(8,4, 'Bs.',0, '','L' );
$pdf->Cell(25,4, muestrafloat($oproveedor->cap_suscrito),0, '','L' );


$pdf->Ln();
$pdf->SetFont('Courier','',8);
$pdf->Cell(35,4, 'Capital Pagado:',0, '','L' );
$pdf->Cell(8,4, 'Bs.',0, '','L' );
$pdf->Cell(25,4, muestrafloat($oproveedor->cap_pagado),0, '','L' );

$pdf->Ln();
$pdf->Ln();

$pdf->SetFont('Courier','B',10);
$pdf->Cell(15,4, 'Solvencia de Recaudos:',0, '','L' );

$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Courier','B',8);
$pdf->Cell(107,4, '     ',0, '','L');
$pdf->Cell(25,4, 'Fecha de',0, '','L');
$pdf->Cell(25,4, 'Fecha de',0, '','L');

$pdf->Ln();
$pdf->SetFont('Courier','B',8);
$pdf->Cell(105,4, 'Recaudos',0, '','L');
$pdf->Cell(25,4, 'Expedicion',0, '','L');
$pdf->Cell(26,4, 'Vencimiento',0, '','L');
$pdf->Cell(30,4, 'Prorroga',0, '','L');

$crequisitos=$oproveedor->busca_req_grupo($conn,$idp,$oproveedor->id_grupo_prove);

$pdf->Ln();
$pdf->Cell(175,0.1, '',1, '','C');
$pdf->Ln();
$pdf->Ln();


$pdf->SetFont('Courier','',8);



if(is_array($crequisitos)){

foreach($crequisitos as $requisitos){
	$pdf->Ln();
	if ($requisitos->fecha_emi==''){
		$pdf->Cell(105,4, $requisitos->requisito."  (PENDIENTE)",0, '','L');
	} else {
		$pdf->Cell(105,4, $requisitos->requisito,0, '','L');
		$pdf->Cell(21,4,muestraFecha($requisitos->fecha_emi),0, '','L');
		$pdf->Cell(26,4,muestraFecha($requisitos->fecha_vcto),0, '','R');
		$pdf->Cell(20,4, $requisitos->prorroga.' Dias',0, '','R');
	}
	
	
	

}

$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Courier','B',10);
$pdf->Cell(175,0.1, '',1, '','C');
$pdf->Ln();
$pdf->Cell(15,4, 'Observaciones:',0, '','L' );
$pdf->Ln();
$pdf->Cell(15,4, '     ',0, '','L' );
$pdf->Ln();
$pdf->Cell(15,4, '     ',0, '','L' );
$pdf->Ln();
$pdf->Cell(15,4, '     ',0, '','L' );
$pdf->Ln();
$pdf->Cell(15,4, '     ',0, '','L' );
$pdf->Ln();
$pdf->Cell(15,4, '     ',0, '','L' );
$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Courier','B',10);
$pdf->Cell(175,0.1, '',1, '','C');
$pdf->Ln();

}

$pdf->Output();
?>