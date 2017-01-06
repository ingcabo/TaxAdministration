<?
class com_det{
	public $id; //id autonumerico

	public $id_com;
	public $id_cta;
	public $cod_cta;
	public $desc_cta;
	public $debe;
	public $haber;
	public $descrip;    
	
	public $cuenta;
	public $lista; //lista de grupos
	public $lista_count; //cantidad de regs en lista
	private $db_esquema = "contabilidad";
	private $conn; //Conexion a base de datos
	private $prep_ins; //Sentencia Prepare para el Insert

   function __construct($conn){
        $this->conn = $conn;
        $this->prep_ins = $this->conn->Prepare("INSERT INTO contabilidad.com_det(id_com, id_cta, cod_cta, desc_cta, debe, haber, descrip) VALUES (?, ?, ?, ?, ?, ?, ?);");
    }
    
    function __destruct(){
       }
    
	public function get($id)
	{ 
		$sql = "SELECT * FROM contabilidad.com_det WHERE id = $id";
		$rs = $this->conn->Execute($sql);
		$this->cuenta = new plan_cuenta();
		
		$res = false;
		if(!$rs->EOF)
		{
			$this->id = $id;
			
			$this->cuenta->get_by_id($this->conn, $rs->fields["id_cta"]);
			
			$this->id_com = $rs->fields["id_com"];
			$this->cod_cta = $this->cuenta->codcta;
			$this->id_cta = $rs->fields["id_cta"];			
			$this->desc_cta = $this->cuenta->descripcion;
			$this->debe = $rs->fields["debe"];
			$this->haber = $rs->fields["haber"];
			$this->descrip = $rs->fields["descrip"];
			
			$res = true;
		}
		//$rs->Close();
		return $res;
	}


    public function create($id_com, $id_cta, $debe,  $haber, $descrip){

        $res = false;
        $array = array(
                        $id_com, 
						$id_cta, 
						$debe,  
						$haber, 
						$descrip
                       );

        $res = $this->conn->Execute($this->prep_ins,$array);

        return $res;
    }

    public function delete($id){
        
        $res = false;
        $sql = "DELETE FROM contabilidad.com_det WHERE id = $id";
        $res = $this->conn->Execute($sql);
        return $res;
    }

    
    public function get_all($from=0, $max=0, $orden="id", $codcom=""){
        $sql = "SELECT id FROM contabilidad.com_det";
        if (trim($codsuc!="")) $sql.= " WHERE id_com= $codcom";
        if (trim($orden) != "") $sql.= " ORDER BY $orden";
        $rs = ($max!=0) ? $this->conn->SelectLimit($sql, $max, $from) : $this->conn->Execute($sql);
        $grs = array();
        while(!$rs->EOF){
            $gr = new com_det($this->conn);
            $gr->get($rs->fields['id']);
            $grs[] = $gr;
            $rs->moveNext();
        }
        $this->lista_count = $rs->RecordCount();
        $rs->Close();
        $this->lista = $grs;
        return $grs;
    }

}
?>