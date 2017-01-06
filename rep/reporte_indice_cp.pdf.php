<?
	include("comun/ini.php");
	include("Constantes.php");
	set_time_limit(0);
	
	$escSolicitud = $_REQUEST['id_escenario'];
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
    var $leftMargin = 15;
    var $rightMargin = 195;
    var $fontStyle = 'Courier';
    var $fontBodySize = 8;
    var $fontHeaderSize = 6;
    var $fontHeaderTitleSize = 12;
    var $cellColWidth = 5;
    var $cellDescCatWidth = 85;
    var $cellUniEjeWidth = 70;
    var $codPrograma;
    var $descPrograma;
    var $escEnEje;

		function Header()
		{
				$this->SetLeftMargin($this->leftMargin);
				$this->SetFont($this->fontStyle, '', $this->fontHeaderSize);
				$this->Rect($this->leftMargin, 10, $this->rightMargin-$this->leftMargin, 30);
				
        $this->Ln(1);
				$this->SetXY(16, 12); 
				$this->MultiCell(0, 2, UBICACION."\n", 0, 'L');
				
        $this->SetXY(150, 12); 
				$this->MultiCell(45, 2, DEPARTAMENTO."\n", 0, 'R');
				$this->Ln();

				$this->SetFont($this->fontStyle, 'B', $this->fontHeaderTitleSize);
				$this->MultiCell(0, 28, "INDICE DE CATEGORIAS PROGRAMATICAS", 0, 'C');
				$this->Ln();

				$this->SetFont($this->fontStyle, '', $this->fontHeaderSize);
				$this->Text(17, 38, 'PRESUPUESTO: AÑO ' . date('Y'));
				$this->Line(15, 44, 191, 44);

        $this->SetY(44);
      	$this->SetLeftMargin($this->leftMargin);
				$this->SetFont($this->fontStyle, 'B', $this->fontHeaderSize);
      	$this->Cell($this->cellColWidth, 3, 'S', LRT, '', 'C');
      	$this->Cell($this->cellColWidth, 3, 'P', LRT, '', 'C');
      	$this->Cell($this->cellColWidth, 3, 'S', LRT, '', 'C');
      	$this->Cell($this->cellColWidth, 3, 'P', LRT, '', 'C');
      	$this->Cell($this->cellColWidth, 3, 'A', LRT, '', 'C');
      	$this->Cell($this->cellDescCatWidth, 3, '', LRT, '', 'C');
      	$this->Cell($this->cellUniEjeWidth, 3, '', LRT, '', 'C');
      	$this->Ln(2);
      	
        $this->Cell($this->cellColWidth, 3, 'E', LR, '', 'C');
      	$this->Cell($this->cellColWidth, 3, 'R', LR, '', 'C');
      	$this->Cell($this->cellColWidth, 3, 'U', LR, '', 'C');
      	$this->Cell($this->cellColWidth, 3, 'R', LR, '', 'C');
      	$this->Cell($this->cellColWidth, 3, 'C', LR, '', 'C');
      	$this->Cell($this->cellDescCatWidth, 3, '', LR, '', 'C');
      	$this->Cell($this->cellUniEjeWidth, 3, '', LR, '', 'C');
      	$this->Ln(2);
      	
        $this->Cell($this->cellColWidth, 3, 'C', LR, '', 'C');
      	$this->Cell($this->cellColWidth, 3, 'O', LR, '', 'C');
      	$this->Cell($this->cellColWidth, 3, 'B', LR, '', 'C');
      	$this->Cell($this->cellColWidth, 3, 'O', LR, '', 'C');
      	$this->Cell($this->cellColWidth, 3, 'T', LR, '', 'C');
      	$this->Cell($this->cellDescCatWidth, 3, '', LR, '', 'C');
      	$this->Cell($this->cellUniEjeWidth, 3, '', LR, '', 'C');
      	$this->Ln(2);
      	
        $this->Cell($this->cellColWidth, 3, 'T', LR, '', 'C');
      	$this->Cell($this->cellColWidth, 3, 'G', LR, '', 'C');
      	$this->Cell($this->cellColWidth, 3, 'P', LR, '', 'C');
      	$this->Cell($this->cellColWidth, 3, 'Y', LR, '', 'C');
      	$this->Cell($this->cellColWidth, 3, 'I', LR, '', 'C');
      	$this->Cell($this->cellDescCatWidth, 3, '', LR, '', 'C');
      	$this->Cell($this->cellUniEjeWidth, 3, '', LR, '', 'C');
      	$this->Ln(2);
      	
        $this->Cell($this->cellColWidth, 3, 'O', LR, '', 'C');
      	$this->Cell($this->cellColWidth, 3, 'R', LR, '', 'C');
      	$this->Cell($this->cellColWidth, 3, 'R', LR, '', 'C');
      	$this->Cell($this->cellColWidth, 3, 'E', LR, '', 'C');
      	$this->Cell($this->cellColWidth, 3, 'V', LR, '', 'C');
      	$this->Cell($this->cellDescCatWidth, 3, '', LR, '', 'C');
      	$this->Cell($this->cellUniEjeWidth, 3, '', LR, '', 'C');
      	$this->Ln(2);
      	
        $this->Cell($this->cellColWidth, 3, 'R', LR, '', 'C');
      	$this->Cell($this->cellColWidth, 3, 'A', LR, '', 'C');
      	$this->Cell($this->cellColWidth, 3, 'O', LR, '', 'C');
      	$this->Cell($this->cellColWidth, 3, 'C', LR, '', 'C');
      	$this->Cell($this->cellColWidth, 3, 'I', LR, '', 'C');
				$this->SetFont($this->fontStyle, 'B', $this->fontBodySize);
      	$this->Cell($this->cellDescCatWidth, 3, 'DENOMINACIÓN', LR, '', 'C');
      	$this->Cell($this->cellUniEjeWidth, 3, 'UNIDAD EJECUTORA', LR, '', 'C'); 
      	$this->Ln(2);
      	
				$this->SetFont($this->fontStyle, '', $this->fontHeaderSize);
      	$this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
      	$this->Cell($this->cellColWidth, 3, 'M', LR, '', 'C');
      	$this->Cell($this->cellColWidth, 3, 'G', LR, '', 'C');
      	$this->Cell($this->cellColWidth, 3, 'T', LR, '', 'C');
      	$this->Cell($this->cellColWidth, 3, 'D', LR, '', 'C');
      	$this->Cell($this->cellDescCatWidth, 3, '', LR, '', 'C');
      	$this->Cell($this->cellUniEjeWidth, 3, '', LR, '', 'C');
      	$this->Ln(2);
      	
        $this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
      	$this->Cell($this->cellColWidth, 3, 'A', LR, '', 'C');
      	$this->Cell($this->cellColWidth, 3, 'R', LR, '', 'C');
      	$this->Cell($this->cellColWidth, 3, 'O', LR, '', 'C');
      	$this->Cell($this->cellColWidth, 3, 'A', LR, '', 'C');
      	$this->Cell($this->cellDescCatWidth, 3, '', LR, '', 'C');
      	$this->Cell($this->cellUniEjeWidth, 3, '', LR, '', 'C');
      	$this->Ln(2);
      	
        $this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
      	$this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
      	$this->Cell($this->cellColWidth, 3, 'A', LR, '', 'C');
      	$this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
      	$this->Cell($this->cellColWidth, 3, 'D', LR, '', 'C');
      	$this->Cell($this->cellDescCatWidth, 3, '', LR, '', 'C');
      	$this->Cell($this->cellUniEjeWidth, 3, '', LR, '', 'C');
      	$this->Ln(2);
      	
        $this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
      	$this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
      	$this->Cell($this->cellColWidth, 3, 'M', LR, '', 'C');
      	$this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
      	$this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
      	$this->Cell($this->cellDescCatWidth, 3, '', LR, '', 'C');
      	$this->Cell($this->cellUniEjeWidth, 3, '', LR, '', 'C');
      	$this->Ln(2);
      	
        $this->Cell($this->cellColWidth, 3, '', LRB, '', 'C');
      	$this->Cell($this->cellColWidth, 3, '', LRB, '', 'C');
      	$this->Cell($this->cellColWidth, 3, 'A', LRB, '', 'C');
      	$this->Cell($this->cellColWidth, 3, '', LRB, '', 'C');
      	$this->Cell($this->cellColWidth, 3, '', LRB, '', 'C');
      	$this->Cell($this->cellDescCatWidth, 3, '', LRB, '', 'C');
      	$this->Cell($this->cellUniEjeWidth, 3, '', LRB, '', 'C');
      	$this->Ln(3);
    }
	
		function Footer()
		{	
			$this->SetFont('Arial', 'I', 8);
			$this->Line($this->leftMargin, $this->GetY(), $this->rightMargin, $this->GetY()); 
		}
  }
	//Creación del objeto de la clase heredada
	$pdf=new PDF();
	$pdf->AliasNbPages();
	$pdf->AddPage();

  // Busca todas las categorias programaticas que esten ligadas a una una unidad ejecutora.
	$query = "SELECT ";
	$query .= "puser.categorias_programaticas.id as id_cp, ";
	$query .= "puser.categorias_programaticas.descripcion AS categoria_programatica, ";
	$query .= "puser.unidades_ejecutoras.descripcion AS unidad_ejecutora ";
	$query .= "FROM ";
	$query .= "puser.relacion_ue_cp ";
	$query .= "INNER JOIN ";
	$query .= "puser.categorias_programaticas "; 
	$query .= "ON ";
	$query .= "puser.relacion_ue_cp.id_categoria_programatica = puser.categorias_programaticas.id ";
	$query .= "AND ";
	$query .= "puser.relacion_ue_cp.id_escenario = puser.categorias_programaticas.id_escenario ";
	$query .= "INNER JOIN ";
	$query .= "puser.unidades_ejecutoras ";
	$query .= "ON ";
	$query .= "puser.relacion_ue_cp.id_unidad_ejecutora = puser.unidades_ejecutoras.id ";
	$query .= "AND ";
	$query .= "puser.relacion_ue_cp.id_escenario = puser.unidades_ejecutoras.id_escenario ";
    $query .= "WHERE ";
	$query .= "puser.relacion_ue_cp.id_escenario =  '$escSolicitud' ";
	$query .= "ORDER BY ";
	$query .= "puser.categorias_programaticas.id";
  //die($query);
  //echo $query."<br>";
  try
  {
			$cps = $conn->Execute($query);
	}
	catch( ADODB_Exception $e )
  {
    if($e->getCode()==-1)
		  return ERROR_CATCH_VFK;
		elseif($e->getCode()==-5)
		  return ERROR_CATCH_VUK;
		else
		  return ERROR_CATCH_GENERICO;
	}
  
  $pdf->SetFont($pdf->fontStyle, '', $pdf->fontBodySize);
  $cod_sector = '00';
  $cod_programa = '00';
  $cod_sub_programa = '00';
  $cod_proyecto = '00';
  $cod_actividad = '00';
  
  // Mientras hayan categorias, las va imprimiendo 
  while (!$cps->EOF)
  {
     $ue = "";	
    // Compara el codigo del "nuevo" sector con el "actual", si son diferentes
    // imprime el "nuevo" sector en una linea completa y actualiza el sector,
    // ahora el nuevo sector pasa a ser el actual 
    if (substr($cps->fields['id_cp'],0,2) != $cod_sector)
    {
      // Busca la descripcion del sector en la BD
      $cod_sector = substr($cps->fields['id_cp'],0,2);
      $query = "SELECT ";
      $query .= "puser.categorias_programaticas.id AS id_cp, ";
      $query .= "puser.categorias_programaticas.descripcion AS desc_cp ";
      $query .= "FROM ";
      $query .= "puser.categorias_programaticas ";
      $query .= "WHERE ";
      $query .= "puser.categorias_programaticas.id = '" . $cod_sector . "00000000' AND id_escenario = '$escSolicitud'";
      //echo $query."<br>";
      $sector = $conn->Execute($query);
      if (is_array($sector->fields))
        $descripcion = utf8_decode(strtoupper($sector->fields['desc_cp']));
      else
        $descripcion = "Sector " . $cod_sector;

		  // Divide la descripcion del sector para que quepa en una celda
	  $desc_sector = dividirStr($descripcion, intval($pdf->cellDescCatWidth/$pdf->GetStringWidth('M')));
		$pdf->Cell($pdf->cellColWidth, 4, $cod_sector, 1, '','C');
		$pdf->Cell($pdf->cellColWidth, 4, '', 1, '','C');
		$pdf->Cell($pdf->cellColWidth, 4, '', 1, '','C');
		$pdf->Cell($pdf->cellColWidth, 4, '', 1, '','C');
		$pdf->Cell($pdf->cellColWidth, 4, '', 1, '','C');
	  $pdf->SetFont($pdf->fontStyle, 'B', $pdf->fontBodySize);
		$pdf->Cell($pdf->cellDescCatWidth, 4, $desc_sector[0], 1, '', 'C');
	  $pdf->SetFont($pdf->fontStyle, '', $pdf->fontBodySize);
		$pdf->Cell($pdf->cellUniEjeWidth, 4, '', 1, '', 'C');
		  $pdf->Ln(4);
  		
  		for ($i=1; next($desc_sector); $i++)
  		{
    		$pdf->Cell($pdf->cellColWidth, 4, '', 1, '','C');
    		$pdf->Cell($pdf->cellColWidth, 4, '', 1, '','C');
    		$pdf->Cell($pdf->cellColWidth, 4, '', 1, '','C');
    		$pdf->Cell($pdf->cellColWidth, 4, '', 1, '','C');
    		$pdf->Cell($pdf->cellColWidth, 4, '', 1, '','C');
    	  $pdf->SetFont($pdf->fontStyle, 'B', $pdf->fontBodySize);
    		$pdf->Cell($pdf->cellDescCatWidth, 4, $desc_sector[$i], 1, '', 'C');
    	  $pdf->SetFont($pdf->fontStyle, '', $pdf->fontBodySize);
    		$pdf->Cell($pdf->cellUniEjeWidth, 4, '', 1, '', 'C');
   		  $pdf->Ln(4);
      }

      $cod_programa = '00';
      $cod_sub_programa = '00';
      $cod_proyecto = '00';
      $cod_actividad = '00';
    }
    
    // Compara el codigo del "nuevo" programa con el programa "actual", si son diferentes
    // imprime el "nuevo" programa en una linea completa y actualiza el programa
    // ahora el nuevo programa pasa a ser el actual 
    if (substr($cps->fields['id_cp'],2,2) != $cod_programa)
    {
      $cod_programa = substr($cps->fields['id_cp'],2,2);
      
      // Si el codigo de la categoria programatica corresponde al codigo de un
      // programa, se utiliza su descripcion y su unidad ejecutora, y se avanza al siguiente registro
      if (substr($cps->fields['id_cp'],4,6) == '000000')
      {
	  	
        $descripcion = $cps->fields['categoria_programatica'];
		//$descripcion= "Sirve";
        $ue = $cps->fields['unidad_ejecutora'];
	
        $cps->MoveNext();
      }
      else
      {
        // Busca en la base de datos la descripcion del programa.
        $query = "SELECT ";
        $query .= "puser.categorias_programaticas.id AS id_cp, ";
        $query .= "puser.categorias_programaticas.descripcion AS categoria_programatica ";
        $query .= "FROM ";
        $query .= "puser.categorias_programaticas ";
        $query .= "WHERE ";
        $query .= "puser.categorias_programaticas.id = '" . $cod_sector . $cod_programa . "000000' AND id_escenario = '$escSolicitud'";
		//echo $query.'<br>';
        $programa = $conn->Execute($query);
        // Si hubo algun resultado se utiliza, sino se coloca como descripcion el codigo del programa.
        //die(print_r($programa->fields['categoria_programatica']));
		if (is_array($programa->fields)){
          $descripcion =utf8_decode($programa->fields['categoria_programatica']);
		 // die("aqui: ".$descripcion."<br>");
        }else{
          $descripcion = "Programa " . $cod_programa;
		  $ue = " ";
		  }
      } 
      

      // Divide la descripcion  y la unidad ejecutora
      $desc_programa = dividirStr($descripcion, intval($pdf->cellDescCatWidth/$pdf->GetStringWidth('M')));
     
      $ue = dividirStr($ue, intval($pdf->cellUniEjeWidth/$pdf->GetStringWidth('M')));
	  //if($ue[0]=="") $ue='Pendiente';
		
  		$pdf->Cell($pdf->cellColWidth, 4, '', LR, '','L');
  		$pdf->Cell($pdf->cellColWidth, 4, $cod_programa, LR, '','L');
  		$pdf->Cell($pdf->cellColWidth, 4, '', LR, '','L');
  		$pdf->Cell($pdf->cellColWidth, 4, '', LR, '','L');
  		$pdf->Cell($pdf->cellColWidth, 4, '', LR, '','L');
  		$pdf->Cell($pdf->cellDescCatWidth, 4, $desc_programa[0], LR, '', 'L');
  		$pdf->Cell($pdf->cellUniEjeWidth, 4, !empty($ue[0]) ? $ue[0]:'', LR, '', 'L');
 		  $pdf->Ln(4);
  		
  		$hay_programa = next($desc_programa);
  		$hay_ue = next($ue);
  		for ($i=1,$j=1;  $hay_programa!==false || $hay_ue!==false; $i++, $j++)
  		{
    		$pdf->Cell($pdf->cellColWidth, 4, '', LR, '','L');
    		$pdf->Cell($pdf->cellColWidth, 4, '', LR, '','L');
    		$pdf->Cell($pdf->cellColWidth, 4, '', LR, '','L');
    		$pdf->Cell($pdf->cellColWidth, 4, '', LR, '','L');
    		$pdf->Cell($pdf->cellColWidth, 4, '', LR, '','L');
    		if (current($desc_programa))
    		  $pdf->Cell($pdf->cellDescCatWidth, 4, $desc_programa[$i], LR, '', 'L');
    		else
    		  $pdf->Cell($pdf->cellDescCatWidth, 4, '', LR, '', 'L');

        if (current($ue))
				  $pdf->Cell($pdf->cellUniEjeWidth, 4, !empty($ue[$i]) ?$ue[$i]:'', LR, '', 'L');
    		else
    		  $pdf->Cell($pdf->cellUniEjeWidth, 4, '', LR, '', 'L');

   		  $pdf->Ln(4);
    		$hay_programa = next($desc_programa);
    		$hay_ue = next($ue);
  		}

      $cod_sub_programa = '00';
      $cod_proyecto = '00';
      $cod_actividad = '00';
    }
    
    // Compara el codigo del "nuevo" sub-programa con el sub-programa "actual", si son diferentes
    // imprime el "nuevo" sub-programa en una linea completa y actualiza el sub-programa
    // ahora el nuevo sub-programa pasa a ser el actual 
	
	//die(substr($cps->fields['id_cp'],4,2));
    if (substr($cps->fields['id_cp'],4,2) != $cod_sub_programa)
    {
      $cod_sub_programa = substr($cps->fields['id_cp'],4,2);
      
      // Si el codigo de la categoria programatica corresponde al codigo de un
      // sub-programa, se utiliza su descripcion y su unidad ejecutora, y se avanza al siguiente registro
      if ($cod_sub_programa != '00' && substr($cps->fields['id_cp'],6,4) == '0000')
      {
        $descripcion = $cps->fields['categoria_programatica'];
        $ue = $cps->fields['unidad_ejecutora'];
        $cps->MoveNext();
      }
      else if ($cod_sub_programa != '00')
      {
        // Busca en la base de datos la descripcion del sub-programa
        $query = "SELECT ";
        $query .= "puser.categorias_programaticas.id AS id_cp, ";
        $query .= "puser.categorias_programaticas.descripcion AS categoria_programatica ";
        $query .= "FROM ";
        $query .= "puser.categorias_programaticas ";
        $query .= "WHERE ";
        $query .= "puser.categorias_programaticas.id = '" . $cod_sector . $cod_programa . $cod_sub_programa . "'0000 AND id_escenario = '$escSolicitud'";
        //die($query);
        $sub_programa = $conn->Execute($query);
        // Si hubo algun resultado se utiliza, sino se coloca como descripcion el codigo del sub-programa.
        if (is_array($sub_programa->fields))
          $descripcion = $sub_programa->fields['categoria_programatica'];
        else
          $descripcion = "Sub-Programa " . $cod_sub_programa;
      } 
	
      // Si el codigo del sub-programa es distinto de '00' es decir que existe un sub-programa y se imprime
      // su codigo descripcion y unidad ejecutora
      if ($cod_sub_programa != '00')
      {
        $desc_sub_programa = dividirStr($descripcion, intval($pdf->cellDescCatWidth/$pdf->GetStringWidth('M')));
        $ue = dividirStr($cps->fields['unidad_ejecutora'], intval($pdf->cellUniEjeWidth/$pdf->GetStringWidth('M')));
    		$pdf->Cell($pdf->cellColWidth, 4, '', LR, '','L');
    		$pdf->Cell($pdf->cellColWidth, 4, '', LR, '','L');
    		$pdf->Cell($pdf->cellColWidth, 4, $cod_sub_programa, LR, '','L');
    		$pdf->Cell($pdf->cellColWidth, 4, '', LR, '','L');
    		$pdf->Cell($pdf->cellColWidth, 4, '', LR, '','L');
    		$pdf->Cell($pdf->cellDescCatWidth, 4, utf8_decode($desc_sub_programa[0]), LR, '', 'L');
    		$pdf->Cell($pdf->cellUniEjeWidth, 4, !empty($ue[0]) ? utf8_decode($ue[0]) : '', LR, '', 'L');
   		  $pdf->Ln(4);
    		
    		$hay_sub_programa = next($desc_sub_programa);
    		$hay_ue = next($ue);
    		for ($i=1,$j=1; $hay_sub_programa!==false || $hay_ue!==false; $i++,$j++)
    		{
				##############aqui esta el peo#################
				$pdf->Cell($pdf->cellColWidth, 4, '', LR, '','L');
				$pdf->Cell($pdf->cellColWidth, 4, '', LR, '','L');
				$pdf->Cell($pdf->cellColWidth, 4, '', LR, '','L');
				$pdf->Cell($pdf->cellColWidth, 4, '', LR, '','L');
				$pdf->Cell($pdf->cellColWidth, 4, '', LR, '','L');
				
				if (current($desc_sub_programa))
				  $pdf->Cell($pdf->cellDescCatWidth, 4, $desc_sub_programa[$i], LR, '', 'L');
				else
				  $pdf->Cell($pdf->cellDescCatWidth, 4, '', LR, '', 'L');

				if (current($ue))
				  $pdf->Cell($pdf->cellUniEjeWidth, 4, !empty($ue[$i]) ? utf8_decode($ue[$i]) : '', LR, '', 'L');
				else
				  $pdf->Cell($pdf->cellUniEjeWidth, 4, '', LR, '', 'L');
				
				  $pdf->Ln(4);
				$hay_sub_programa = next($desc_sub_programa);
				$hay_ue = next($ue);
        	}
        
        $cod_proyecto = '00';
        $cod_actividad = '00';
    	}
    }    
	//die('entro');
    // Compara el codigo del "nuevo" proyecto con el proyecto "actual", si son diferentes
    // imprime el "nuevo" proyecto en una linea completa y actualiza el proyecto 
    // ahora el nuevo proyecto pasa a ser el actual 
    if (substr($cps->fields['id_cp'],6,2) != $cod_proyecto)
    {
      $cod_proyecto = substr($cps->fields['id_cp'],6,2);
      
      // Si el codigo de la categoria programatica corresponde al codigo de un
      // sub-programa, se utiliza su descripcion y su unidad ejecutora, y se avanza al siguiente registro
      if ($cod_proyecto != '00' && substr($cps->fields['id_cp'],8,2) == '00')
      {
        $descripcion = $cps->fields['categoria_programatica'];
        $ue = $cps->fields['unidad_ejecutora'];
        $cps->MoveNext();
      }
      else if ($cod_proyecto != '00')
      { 
        // Busca en la base de datos la descripcion del proyecto.
        $query = "SELECT ";
        $query .= "puser.categorias_programaticas.id AS id_cp, ";
        $query .= "puser.categorias_programaticas.descricpcion AS categoria_programatica ";
        $query .= "FROM ";
        $query .= "puser.categorias_programaticas ";
        $query .= "WHERE ";
        $query .= "puser.categorias_programaticas.id = '" . $cod_sector . $cod_programa . $cod_sub_programa . "00' AND id_escenario = '$escSolicitud'";
        //die($query);
        $proyecto = $conn->Execute($query);
        // Si hubo algun resultado se utiliza, sino se coloca como descripcion el codigo del sub-programa.
        if (is_array($proyecto->fields))
          $descripcion = $proyecto->fields['categoria_programatica'];
        else
          $descripcion = "Proyecto " . $cod_proyecto;
      } 

      // Si el codigo del proyecto es distinto de '00' es decir que existe un proyecto y se imprime
      // su codigo descripcion y unidad ejecutora
      if ($cod_proyecto != '00')
      {
        $desc_proyecto = dividirStr($descripcion, intval($pdf->cellDescCatWidth/$pdf->GetStringWidth('M')));
        $ue = dividirStr($cps->fields['unidad_ejecutora'], intval($pdf->cellUniEjeWidth/$pdf->GetStringWidth('M')));
    		$pdf->Cell($pdf->cellColWidth, 4, '', LR, '','L');
    		$pdf->Cell($pdf->cellColWidth, 4, '', LR, '','L');
    		$pdf->Cell($pdf->cellColWidth, 4, '', LR, '','L');
    		$pdf->Cell($pdf->cellColWidth, 4, $cod_proyecto, LR, '','L');
    		$pdf->Cell($pdf->cellColWidth, 4, '', LR, '','L');
    		$pdf->Cell($pdf->cellDescCatWidth, 4, utf8_decode($desc_proyecto[0]), LR, '', 'L');
    		$pdf->Cell($pdf->cellUniEjeWidth, 4, !empty($ue[0]) ? utf8_decode($ue[0]) : '', LR, '', 'L');
   		  $pdf->Ln(4);
    		
    		$hay_proyecto = next($desc_proyecto);
    		$hay_ue = next($ue);
    		for ($i=1,$j=1;  $hay_proyecto!==false || $hay_ue!==false; $i++,$j++)
    		{
      		$pdf->Cell($pdf->cellColWidth, 4, '', LR, '','L');
      		$pdf->Cell($pdf->cellColWidth, 4, '', LR, '','L');
      		$pdf->Cell($pdf->cellColWidth, 4, '', LR, '','L');
      		$pdf->Cell($pdf->cellColWidth, 4, '', LR, '','L');
      		$pdf->Cell($pdf->cellColWidth, 4, '', LR, '','L');
      		
          if (current($desc_proyecto))
      		  $pdf->Cell($pdf->cellDescCatWidth, 4, $desc_proyecto[$i], LR, '', 'L');
      		else
      		  $pdf->Cell($pdf->cellDescCatWidth, 4, '', LR, '', 'L');

          if (current($ue))
        		$pdf->Cell($pdf->cellUniEjeWidth, 4, !empty($ue[$i]) ? utf8_decode($ue[$i]) : '', LR, '', 'L');
        	else
        		$pdf->Cell($pdf->cellUniEjeWidth, 4, '', LR, '', 'L');

     		  $pdf->Ln(4);
      		$hay_proyecto = next($desc_proyecto);
      		$hay_ue = next($ue);
    		}
    		
        $cod_actividad = '00';
    	}
    }    
    
    // Imprime el codigo descripcion y unidad ejecutora de una actividad y avanza al siguiente registro
    $desc_actividad = dividirStr($cps->fields['categoria_programatica'], intval($pdf->cellDescCatWidth/$pdf->GetStringWidth('M')));
    $ue = dividirStr($cps->fields['unidad_ejecutora'], intval($pdf->cellUniEjeWidth/$pdf->GetStringWidth('M')));
    $cod_actividad = substr($cps->fields['id_cp'],8,2);
		$pdf->Cell($pdf->cellColWidth, 4, '', LR, '','L');
		$pdf->Cell($pdf->cellColWidth, 4, '', LR, '','L');
		$pdf->Cell($pdf->cellColWidth, 4, '', LR, '','L');
		$pdf->Cell($pdf->cellColWidth, 4, '', LR, '','L');
		$pdf->Cell($pdf->cellColWidth, 4, $cod_actividad, LR, '','L');
		$pdf->Cell($pdf->cellDescCatWidth, 4, utf8_decode($desc_actividad[0]), LR, '', 'L');
		$pdf->Cell($pdf->cellUniEjeWidth, 4, utf8_decode($ue[0]), LR, '', 'L');
	  $pdf->Ln(4);
		
		$hay_actividad = next($desc_actividad);
		$hay_ue = next($ue);
		for ($i=1,$j=1;  $hay_actividad!==false || $hay_ue!==false; $i++,$j++)
		{
  		$pdf->Cell($pdf->cellColWidth, 4, '', LR, '','L');
  		$pdf->Cell($pdf->cellColWidth, 4, '', LR, '','L');
  		$pdf->Cell($pdf->cellColWidth, 4, '', LR, '','L');
  		$pdf->Cell($pdf->cellColWidth, 4, '', LR, '','L');
  		$pdf->Cell($pdf->cellColWidth, 4, '', LR, '','L');
  		
  		if (current($desc_actividad))
  		  $pdf->Cell($pdf->cellDescCatWidth, 4, utf8_decode($desc_actividad[$i]), LR, '', 'L');
  		else
  		  $pdf->Cell($pdf->cellDescCatWidth, 4, '', LR, '', 'L');

      if (current($ue))
  		  $pdf->Cell($pdf->cellUniEjeWidth, 4, !empty($ue[$i]) ? utf8_decode($ue[$i]) : '', LR, '', 'L');
  		else
  		  $pdf->Cell($pdf->cellUniEjeWidth, 4, '', LR, '', 'L');

		  $pdf->Ln(4);
  		$hay_actividad = next($desc_actividad);
  		$hay_ue = next($ue);
		}
		
		$cps->MoveNext();
  }
	
	$pdf->Output();
?>
