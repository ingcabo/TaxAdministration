<?
class productos{

	// Propiedades

	var $id;
	var $id_tipo_producto;
	var $descripcion;
	var $tiempo_entrega;
	var $garantia;
	var $forma_pago;
	var $contrib;
	var $unidad_medida;
	var $rop;
	var $roq;
	var $ctd_minimo;
	var $ctd_maximo;
	var $ubic_fisica;
	var $ctd_actual;
	var $id_status_producto;
	var $id_manejo_almacen_prodcuto;
	var $id_activo_inactivo_producto;
	var $costo_std;
	var $costo_prm;
	var $ultimo_costo;
	var $fecha;
	var $total;
	var $desc_completa;
	var $grupo;

	function get($conn, $id){
		$q = "SELECT * FROM puser.productos ";
		$q.= "WHERE id='$id'";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->id_tipo_producto = $r->fields['id_tipo_producto'];
			$this->descripcion = $r->fields['descripcion'];
			$this->tiempo_entrega = $r->fields['tiempo_entrega'];
			$this->garantia = $r->fields['garantia'];
			$this->forma_pago = $r->fields['forma_pago'];
			$this->contrib = $r->fields['contrib'];	
			$this->unidad_medida = $r->fields['unidad_medida'];
			$this->rop = $r->fields['rop'];
			$this->roq = $r->fields['roq'];
			$this->ctd_minimo = $r->fields['ctd_minimo'];
			$this->ctd_maximo = $r->fields['ctd_maximo'];
			$this->ubic_fisica = $r->fields['ubic_fisica'];
			$this->ctd_actual = $r->fields['ctd_actual'];
			$this->id_status_producto = $r->fields['id_status_producto'];
			$this->id_manejo_almacen_prodcuto = $r->fields['id_manejo_almacen_prodcuto'];
			$this->id_activo_inactivo_producto = $r->fields['id_activo_inactivo_producto'];
			$this->costo_std = $r->fields['costo_std'];
			$this->costo_prm = $r->fields['costo_prm'];
			$this->ultimo_costo = $r->fields['ultimo_costo'];
			$this->fecha = $r->fields['fecha'];
			$this->desc_completa = $r->fields['desc_completa'];
			$this->grupo = $r->fields['grupo'];
			
			return true;
		}else
			return false;
	}

	function get_all($conn, $from=0, $max=0,$orden="id"){
		$q = "SELECT * FROM productos ";
		$q.= "ORDER BY $orden ";
		//die($q)
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new productos;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $descripcion, $tipo_producto, $unidad_medida, $rop,$roq,
						 $ctd_minimo, $ctd_maximo, $ubic_fisica, $ctd_actual, 
						 $activo_inactivo_producto, $costo_std,
						 $costo_prm, $ultimo_costo, $today,$desc_completa,$grupo){
		$grupo = ($grupo!=0) ? $grupo : 'null';
		$q = "INSERT INTO puser.productos ";
		$q.= "( id_tipo_producto, descripcion, unidad_medida, rop,roq,
				 ctd_minimo, ctd_maximo, ubic_fisica, ctd_actual, 
				 id_activo_inactivo_producto,costo_std,
				costo_prm, ultimo_costo, fecha,desc_completa,id_grupo) ";
		$q.= " VALUES ";
		$q.= "( $tipo_producto, '$descripcion','$unidad_medida', '$rop','$roq',
				 '$ctd_minimo', '$ctd_maximo', '$ubic_fisica', '$ctd_actual', 
				 $activo_inactivo_producto, '$costo_std',
				'$costo_prm', '$ultimo_costo', '$today', '$desc_completa', $grupo ) ";
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function set($conn, $id,$descripcion, $tipo_producto, $unidad_medida, $rop,$roq,
						 $ctd_minimo, $ctd_maximo, $ubic_fisica, $ctd_actual, 
						 $activo_inactivo_producto, $costo_std,
						 $costo_prm, $ultimo_costo, $today,$desc_completa,$grupo){
		$grupo = ($grupo!=0) ? $grupo : 'null';
		$q = "UPDATE productos SET id_tipo_producto=$tipo_producto,
								descripcion = '$descripcion', unidad_medida='$unidad_medida', rop='$rop',
								roq='$roq', ctd_minimo='$ctd_minimo', ctd_maximo='$ctd_maximo', ubic_fisica='$ubic_fisica', ctd_actual='$ctd_actual', id_activo_inactivo_producto=$activo_inactivo_producto, costo_std='$costo_std', costo_prm='$costo_prm',
								ultimo_costo='$ultimo_costo', fecha='$today', desc_completa = '$desc_completa', id_grupo = $grupo ";
		$q.= "WHERE id=$id";	
		//die($q);
		if($conn->Execute($q))
			return true;
		else
			return false;
	}

	function del($conn, $id){
		$q = "DELETE FROM productos WHERE id='$id'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
	
	function buscar($conn, $tipoProd="", $descripcion="", $max=10, $from=1, $orden="id"){
		try{
			$q = "SELECT * FROM puser.productos WHERE 1 = 1 ";
			$q.= !empty($tipoProd) ? "AND id_tipo_producto = '$tipoProd' " : "";
			$q.= !empty($descripcion) ? "AND descripcion ILIKE '%$descripcion%' " : ""; 
			$q.= "ORDER BY $orden ";
			//die($q);
			$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
			$collection=array();
			while(!$r->EOF){
				$ue = new productos;
				$ue->get($conn, $r->fields['id']);
				$coleccion[] = $ue;
				$r->movenext();
			}
			return $coleccion;
		}
		catch( ADODB_Exception $e ){
			if($e->getCode()==-1)
				return ERROR_CATCH_VFK;
			elseif($e->getCode()==-5)
				return ERROR_CATCH_VUK;
			else
				return ERROR_CATCH_GENERICO;
		}
	}
	
	function total_registro_busqueda($conn, $tipoProd="", $descripcion="", $orden="id"){
		
		$q = "SELECT * FROM puser.productos WHERE 1=1 ";
		$q.= !empty($tipoProd) ? "AND id_tipo_producto = '$tipoProd' " : "";
		$q.= !empty($descripcion) ? "AND descripcion ILIKE '%$descripcion%' " : ""; 
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		$total = $r->RecordCount();

		return $total;
	}
}
?>
