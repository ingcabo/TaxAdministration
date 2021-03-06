<?
class comprobante{
    public $id; //id autonumerico
    
	public $numcom;
	public $descrip;
	public $fecha;
	public $ano;
	public $mes;
	public $origen;
	public $num_doc;
 	public $aux;
	public $status;
	public $status_nombre;
	public $transferido;

	public $beneficiario;
	public $lista; //lista de grupos
	public $det_count;
	public $det;
	public $lista_count; //cantidad de regs en lista

	private $db_esquema = "contabilidad";
	private $conn; //Conexion a base de datos
	private $prep_ins; //Sentencia Prepare para el Insert
	private $prep_ins_det;
	private $prep_upd; //Sentencia Prepare para el Update
       
    function __construct($conn)
	 {
        $this->conn = $conn;
        $this->prep_ins = $this->conn->Prepare("INSERT INTO contabilidad.com_enc (id_escenario, ano, mes, numcom, descrip, fecha, origen, status, num_doc, num_doc2) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?);");

        $this->prep_upd=$this->conn->Prepare("UPDATE contabilidad.com_enc SET id_escenario = ?, ano = ? , mes = ?, numcom = ?, descrip = ?, fecha = ?, origen = ?, status = ?, num_doc = ?, num_doc2 = ? WHERE id = ?;");
    }
    
    function __destruct(){
       }
    
    public function get($id){ 
        $sql = "SELECT * FROM contabilidad.com_enc WHERE id = $id";
		$rs = $this->conn->Execute($sql);
        $res = false;
        if(!$rs->EOF){
            $this->id = $id;
			$this->cod_suc = $rs->fields["id_suc"];
			$this->id_escenario = $rs->fields["id_escenario"];
			$this->numcom = $rs->fields["numcom"];
			$this->descrip=$rs->fields["descrip"];
			$this->fecha = muestraFecha($rs->fields["fecha"]);
			$this->ano=$rs->fields["ano"];
			
			switch($rs->fields["mes"]){
				case 1:
					$this->mes='Enero';
					break;
				case 2:
					$this->mes='Febrero';
					break;
				case 3:
					$this->mes='Marzo';
					break;
				case 4:
					$this->mes='Abril';
					break;
				case 5:
					$this->mes='Mayo';
					break;
				case 6:
					$this->mes='Junio';
					break;
				case 7:
					$this->mes='Julio';
					break;
				case 8:
					$this->mes='Agosto';
					break;
				case 9:
					$this->mes='Septiembre';
					break;
				case 10:
					$this->mes='Octubre';
					break;
				case 11:
					$this->mes='Noviembre';
					break;
				case 12:
					$this->mes='Diciembre';
					break;

			}
			
			$this->origen = trim($rs->fields["origen"]);
			
			if ($this->origen == "OP")
			{
				$this->aux = 'Orden de Pago';
				$tabla = 'finanzas.orden_pago';
			}
			else if ($this->origen == "CHQ")
			{
				$this->aux = 'Cheque';
				$tabla = 'finanzas.cheques';
			}
			else if ($this->origen == "TRA")
			{	
				$this->aux = 'Transferencia';
				$tabla = 'finanzas.otros_pagos';
			}
			else if ($this->origen == "TRM")
			{	
				$this->aux = 'Transferencia Manual';
				//$tabla = 'contabilidad.com_enc';
			}
			else if ($this->origen == "CHM")
				$this->aux = 'Cheque Manual';
			else if ($this->origen == "DEP")
				$this->aux = utf8_encode('Dep�sito');
			else if (strpos($this->origen, "TR") !== false)
				$this->aux = 'Transferencia';
			else if ($this->origen == "NC")
				$this->aux = utf8_encode('Nota de Cr�dito');
			else if ($this->origen == "ND")
				$this->aux = utf8_encode('Nota de D�bito');
				
			$this->num_doc = $rs->fields['num_doc'];
			if (!empty($tabla))
			{
				$q = "SELECT id_proveedor FROM $tabla WHERE nrodoc = '".$this->num_doc."'";
				$r = $this->conn->Execute($q);
				if (!$r->EOF)
				{
					$this->beneficiario = new proveedores;
					$this->beneficiario->get($this->conn, $r->fields['id_proveedor']);
				}
			}
			
			$this->status = $rs->fields["status"];
			if ($this->status == 'R')
				$this->status_nombre = 'Registrado';
			else if ($this->status == 'A')
				$this->status_nombre = 'Anulado';
			else if ($this->status == 'T')
				$this->status_nombre = 'Transitorio';
			else if ($this->status == 'C')
				$this->status_nombre = 'Conciliado';
			

			$this->transferido=$rs->fields["transferido"];
			if (!empty($id)){
		    	$this->get_det($id);
			}
			$res = true;
        }
        //$rs->Close();
        return $res;
    }

	public function create($id_escenario, $ano, $mes, $numcom, $descrip, $fecha, $origen, $status, $num_doc, $json_det, $tipo='0')
	{
		$res = false;
		$array = array($id_escenario, $ano, $mes, $numcom, $descrip, $fecha, $origen, $status, $num_doc, $num_doc);
		//die(var_dump($array));
		$res = $this->conn->Execute($this->prep_ins,$array);
		//$res = true;
		if($json_det != "" && $res!==false) 
		{
			$sql = "SELECT max(id) as max FROM contabilidad.com_enc";
			
			$rs = $this->conn->Execute($sql);
//			var_dump($rs->fields);
			if(!$rs->EOF)
			{
				$id_com = $rs->fields["max"];
				if ($id_com != "") 
					$res = $this->set_det($id_com, $json_det, $tipo);
			}
			
			if ($res!==false)
				$res = true;
		}
		else if ($res!==false)
			$res = true;

		return $res;
	}

	public function set($id_escenario, $ano, $mes, $numcom, $descrip, $fecha, $origen, $status, $num_doc, $id, $json_det)
	{
		$res = false;
		$array = array($id_escenario, $ano, $mes, $numcom, $descrip, $fecha, $origen, $status, $num_doc, $id);
		//die(var_dump($array));
		
		$res = $this->conn->Execute($this->prep_upd,$array);

		if ($json_det!="" && $res!==false)
		{
			$res = $this->set_det($id, $json_det);
			
			if ($res!==false)
				$res = true;
		}
		else if ($res!==false)
			$res = true;

		return $res;
	}

	public function delete($id)
	{
		$res = false;
		$sql = "DELETE FROM contabilidad.com_enc WHERE id = $id";
		$res = $this->conn->Execute($sql);

		return $res;
	}
    
	public function get_all($id_escenario="", $id_cta_contable="", $origen="", $fecha_desde="", $fecha_hasta="", $from=0, $max=0, $orden="fecha")
	{
		if (empty($id_escenario) && empty($id_cta_contable) && empty($origen) && empty($fecha_hasta) && empty($fecha_desde))
			return false;
			
		$sql = "SELECT DISTINCT com_enc.id, fecha FROM contabilidad.com_enc ";
		if ($id_cta_contable != "" && $id_cta_contable != '0')
			$sql.= " INNER JOIN contabilidad.com_det ON (com_enc.id = com_det.id_com) ";
			
		$sql.= "WHERE 1=1 ";
		if ($id_escenario != "" && $id_escenario != '0')
		{
//			$q = "SELECT ano FROM puser.escenarios WHERE id = '$id_escenario'";
//			$r = $this->conn->Execute($q);
//			$sql.= " AND ano = ".$r->fields['ano'];
			$sql.= " AND id_escenario = $id_escenario ";
		}

		if ($id_cta_contable != "" && $id_cta_contable != '0')
			$sql.= "AND com_det.id_cta = '$id_cta_contable' ";
			
		if ($origen != "" && $origen != '0')
			$sql.= " AND origen = '$origen' ";
		
		if ($fecha_desde != "" && $fecha_desde != '0')
			$sql.= " AND fecha >= '".guardafecha($fecha_desde)."' ";

		if ($fecha_hasta != "" && $fecha_hasta != '0')
			$sql.= " AND fecha <= '".guardafecha($fecha_hasta)."' ";

		if (trim($orden) != "")
			$sql.= " ORDER BY $orden";
		//die($sql);
		$rs = ($max!=0) ? $this->conn->SelectLimit($sql, $max, $from) : $this->conn->Execute($sql);
		$grs = array();
		while(!$rs->EOF)
		{
			$gr = new comprobante($this->conn);
			$gr->get($rs->fields['id']);
			$grs[] = $gr;
			$rs->moveNext();
		}

		$this->lista = $grs;
		$this->lista_count = count($grs);
		return $grs;
	}
	
	public function get_det($id)
	{
		$res = false;
		$sql = "SELECT id FROM contabilidad.com_det WHERE id_com = $id";
		//echo($sql);
		$rs = $this->conn->Execute($sql);
		
		if(!$rs->EOF)
		{
			$grs = array();
			while(!$rs->EOF)
			{
				$gr = new com_det($this->conn);
				$gr->get($rs->fields['id']);
				$grs[] = $gr;
				$rs->movenext();
			}
			
			$this->det_count = $rs->RecordCount();
			$this->det = $grs;
		}
		
		$rs->Close();
		return $grs;
	}

	public function set_det($id_com, $json_det, $tipo)
	{
		$prep_det_ins = $this->conn->Prepare("INSERT INTO contabilidad.com_det (id_com, id_cta, debe, haber, descrip) VALUES (?, ?, ?, ?, ?);");
		
		$sql = "DELETE FROM contabilidad.com_det WHERE id_com=$id_com";
		
		$this->conn->Execute($sql);
		$JsonRec = new Services_JSON();
		$JsonRec = $JsonRec->decode(str_replace("\\","",$json_det));
		
		//Se modifica esta funcion para buscar el id si se pasa el codigo de la cuenta o si se pasa ya el id de la cuenta
		
		$res = false;
		if(is_array($JsonRec))
		{
			foreach($JsonRec as $det)
			{
				if($tipo=='0'){
					$sql = "SELECT id FROM contabilidad.plan_cuenta WHERE codcta = '".$det->id_cta."' AND id_escenario = ".$det->id_esc;
					$rs = $this->conn->Execute($sql);
					$idCta = $rs->fields['id'];
				}else{
					$idCta = $det->id_cta;
				}
				$array = array($id_com, $idCta, $det->debe,  $det->haber, $det->descrip);
				$res = $this->conn->Execute($prep_det_ins,$array);
			}
		}

		return $res;
	}
	
	function total_registro_busqueda($id_escenario="", $id_cta_contable="", $origen="", $fecha_desde="", $fecha_hasta="")
	{
		if (empty($id_escenario) && empty($id_cta_contable) && empty($origen) && empty($fecha_hasta) && empty($fecha_desde))
			return false;
			
		$sql = "SELECT DISTINCT com_enc.id FROM contabilidad.com_enc ";
		if ($id_cta_contable != "" && $id_cta_contable != '0')
			$sql.= " INNER JOIN contabilidad.com_det ON (com_enc.id = com_det.id_com) ";
			
		$sql.= "WHERE 1=1 ";
		if ($id_cta_contable != "" && $id_cta_contable != "0")
			$sql.= "AND com_det.id_cta = '$id_cta_contable' ";
			
		if ($id_escenario != "" && $id_escenario != '0')
		{
//			$q = "SELECT ano FROM puser.escenarios WHERE id = '$id_escenario'";
//			$r = $this->conn->Execute($q);
//			$sql.= " AND ano = ".$r->fields['ano'];
			$sql.= " AND id_escenario = $id_escenario ";
		}

		if ($origen != "" && $origen != "0")
			$sql.= " AND origen = '$origen' ";
		
		if ($fecha_desde != "" && $fecha_desde != "0")
			$sql.= " AND fecha >= '".guardaFecha($fecha_desde)."' ";

		if ($fecha_hasta != "" && $fecha_hasta != "0")
			$sql.= " AND fecha <= '".guardaFecha($fecha_hasta)."' ";
		
		$r = $this->conn->Execute($sql);
		return $r->RecordCount();
	}
	
	function anular($id)
	{
		$res = false;
		$sql = "UPDATE contabilidad.com_enc SET status = 'A' WHERE id = $id";

		$rs = $this->conn->Execute($sql);
		if ($rs !== false)
			$res = true;
		else
			$res = false;
			
		return $res;
	}
	
	function setDatosConc($id_conc, $fconciliacion, $status, $id)
	{
		$q = "UPDATE contabilidad.com_enc SET id_conciliacion = $id_conc, fecha_conciliacion = '$fconciliacion', status = '$status' WHERE id = $id ";
		$r = $this->conn->Execute($q);
		if ($r !== false)
			return true;
		else
			return $this->conn->ErrorMsg();		
	}
}
?>