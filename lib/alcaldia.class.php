<?
class alcaldia{

	// Propiedades

	var $id;
	var $descripcion;
	var $razon;
	var $domicilio;
	var $fecha_creacion;
	var $ciudad;
	var $estado;
	var $telefono;
	var $fax;
	var $web_site;
	var $cpostal;
	var $alcalde;
	var $personal;
	var $concejales;

	var $total;

	function get($conn, $id){
		$q = "SELECT * FROM puser.alcaldia WHERE id='$id'";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->descripcion = $r->fields['descripcion'];
			$this->razon = $r->fields['razon'];
			$this->domicilio = $r->fields['domicilio'];
			$this->fecha_creacion = muestrafecha($r->fields['fecha_creacion']);
			$this->ciudad = $r->fields['ciudad'];
			$this->estado = $r->fields['estado'];
			$this->telefono = $r->fields['telefono'];
			$this->fax = $r->fields['fax'];
			$this->web_site = $r->fields['web_site'];
			$this->cpostal = $r->fields['cpostal'];
			$this->alcalde = $r->fields['alcalde'];
			$this->personal = $r->fields['personal'];
			$this->concejales = $r->fields['concejales'];
			return true;
		}else
			return false;
	}

	function get_all($conn, $from=0, $max=0,$orden="id"){
		$q = "SELECT * FROM puser.alcaldia ";
		$q.= "ORDER BY $orden ";
		
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new alcaldia;
			$ue->get($conn, $r->fields[0]);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, 
				$id,
				$descripcion,
				$razon,
				$domicilio,
				$fecha_creacion,
				$ciudad,
				$estado,
				$telefono,
				$fax = '',
				$web_site = '',
				$cpostal,
				$alcalde,
				$personal,
				$concejales){
		$q = "INSERT INTO alcaldia ";
		$q.= "(id, descripcion, razon, domicilio, fecha_creacion, ciudad, estado, fax, telefono, web_site, ";
		$q.= "cpostal, alcalde, personal, concejales) ";
		$q.= "VALUES ('$id', '$descripcion', '$razon', '$domicilio', '".guardafecha($fecha_creacion)."', '$ciudad', '$estado', ";
		$q.= "'$telefono', '$fax', '$web_site', '$cpostal', '$alcalde', '$personal', '$concejales') ";
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function set($conn, $id_nuevo,
				$id, 
				$descripcion,
				$razon,
				$domicilio,
				$fecha_creacion,
				$ciudad,
				$estado,
				$telefono,
				$fax = '',
				$web_site = '',
				$cpostal,
				$alcalde,
				$personal,
				$concejales){
		$q = "UPDATE alcaldia SET id = '$id_nuevo', descripcion='$descripcion', razon = '$razon', domicilio = '$domicilio', ";
		$q.= "fecha_creacion = '".guardafecha($fecha_creacion)."', ciudad = '$ciudad', estado = '$estado', telefono = '$telefono', ";
		$q.= "fax = '$fax', web_site = '$web_site', cpostal = '$cpostal', alcalde = '$alcalde', personal = '$personal', ";
		$q.= "concejales = '$concejales' ";
		$q.= "WHERE id='$id' ";
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $id){
		$q = "DELETE FROM alcaldia WHERE id='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
}
?>
