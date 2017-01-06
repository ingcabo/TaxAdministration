<?
class usuarios{

	// Propiedades

	var $id;
	var $id_cargo;
	var $id_profesion;
	var $id_unidad_ejecutora;
	var $cargo;
	var $profesion;
	var $unidad_ejecutora;
	var $cedula; 
	var $nombre; 
	var $apellido;
	var $login;
	var $ind;
	var $status;

	var $total;
	
	function get($conn, $id){
		
		$q = "SELECT usuarios.*, rrhh.cargo.int_cod AS cargo, profesiones.descripcion AS profesion, ";
		$q.= "unidades_ejecutoras.descripcion AS unidad_ejecutora ";
		$q.= "FROM usuarios ";
		$q.= "LEFT JOIN rrhh.cargo ON (usuarios.id_cargo = cargo.int_cod) ";
		$q.= "LEFT JOIN profesiones ON (usuarios.id_profesion = profesiones.id) ";
		$q.= "LEFT JOIN unidades_ejecutoras ON (usuarios.id_unidad_ejecutora = unidades_ejecutoras.id) ";
		$q.= "WHERE usuarios.id='$id'";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->id_cargo = $r->fields['id_cargo'];
			$this->id_profesion = $r->fields['id_profesion'];
			$this->id_unidad_ejecutora = $r->fields['id_unidad_ejecutora'];
			$this->cargo = $r->fields['cargo'];
			$this->profesion = $r->fields['profesion'];
			$this->unidad_ejecutora = $r->fields['unidad_ejecutora'];
			$this->cedula = $r->fields['cedula'];
			$this->nombre = $r->fields['nombre'];
			$this->apellido = $r->fields['apellido'];
			$this->login = $r->fields['login'];
			$this->ind = $r->fields['ind'];
			$this->status = $r->fields['status'];
			return true;
		}else
			return false;
			
	}

	function get_all($conn, $from=0, $max=0,$orden="id"){
		$q = "SELECT id FROM usuarios ";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new usuarios;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, 
							$id_cargo, 
							$id_profesion, 
							$id_unidad_ejecutora, 
							$ind, 
							$cedula, 
							$nombre, 
							$apellido,
							$login,
							$password,
							$status){
		try{
			$passMD5 = md5($password);
			$q = "INSERT INTO usuarios ";
			$q.= "(id_cargo, id_profesion, id_unidad_ejecutora, cedula, nombre, apellido, login, password, ind, status) ";
			$q.= "VALUES ";
			$q.= "('$id_cargo', '$id_profesion', '$id_unidad_ejecutora', '$cedula', '$nombre', '$apellido', '$login', '$passMD5', ";
			$q.= " '$ind', '$status') ";
			//die($q);
			$conn->Execute($q);

			return REG_ADD_OK;

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

	function set($conn, 
							$id, 
							$id_cargo, 
							$id_profesion, 
							$id_unidad_ejecutora, 
							$ind, 
							$cedula, 
							$nombre, 
							$apellido,
							$login,
							$password,
							$status){
		try{
			$passMD5 = md5($password);
			$q = "UPDATE usuarios SET id_cargo='$id_cargo', ";
			$q.= "id_profesion = '$id_profesion', id_unidad_ejecutora = '$id_unidad_ejecutora', ";
			$q.= "cedula = '$cedula', nombre = '$nombre', apellido = '$apellido', login = '$login', ind= '$ind', status = '$status', ";
			$q.= "password = '$passMD5' ";
			$q.= "WHERE id='$id' ";
			//die($q);
			$conn->Execute($q);
			if(!empty($password))
				$this->set_password($conn, $id, $password);
			return REG_SET_OK;
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

	function set_password($conn, $id_usuario, $password) {
		if(!empty($password))
			$passMD5 = md5($password);
		else{
			$password = $this->genera_password();
			$passMD5 = md5($password);
		}
		$q = "UPDATE usuarios SET password = '$passMD5' WHERE id = $id_usuario";
		if($conn->Execute($q))
			return $password;
		else
			return false;
	}

	function genera_password(){
		return  "at".mt_rand(0,10000);
	}

	function del($conn, $id){
		try{
			$q = "DELETE FROM usuarios WHERE id='$id'";
			$conn->Execute($q);

			return REG_DEL_OK;

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
	
	//CHECK IF THE USER HAS BEEN LOGGED
	function is_logged($conn) {
	//revisa si el usuario ya esta logeado
		if (count($_SESSION)==0) {
			return false;
		}else{
			$this->get_user_by_session($conn);
			return true;
		}
	}

	function get_user_by_session($conn) {
		$id=$_SESSION['_AL_id']; // obtengo la id de la sesion
		$this->get($conn, $id); // llamo al metodo get para obtener la inforacion del usuario
	}

	//funcion que hace el proceso de loggeo
	function access_login($conn, $login, $password,$MultiEmpresa,$Empresa){
		$passmd5 = md5($password);
		$q="SELECT * FROM usuarios WHERE login='$login'";
		//die($q);
		$r=$conn->Execute($q);
		if(!$r->EOF){
			if ($r->fields['password']==$passmd5) {
				//CEPV.100706.SM VALIDANDO LA EMPRESA EN CASO QUE EL SISTEMA SEA MULTIEMPRESA
				if($MultiEmpresa!=1 || $this->validar_empresa($conn,$r->fields['id'],$Empresa)){
					session_start();
					$_SESSION['_AL_login'] = $r->fields['login'];	// lleno la variable de sesion
					$_SESSION['_AL_id'] = $r->fields['id'];
					return true;
				}else{
					return false;
				}
				//CEPV.100706.EM 
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	function olvido_password($conn, $login) {
	// funcion para validar usuario y enviar datos de su cuenta de acceso al sistema
			$sql = "Select * from user where login = '$login'";
			$consulta = mysql_query($sql);
			$registros = mysql_fetch_array($consulta);
			if(!empty($registros)){
				$usremail = $registros["email"];
				$login = $registros["login"];
				$pass = $registros["password"];
				$firstname = $registros["first_name"];
				$lastname = $registros["last_name"];
				$pagetitle = "Request Account Info";
				$arr_c=file('mail_templates/send_account_info.html');
				for ($i=0;$i<count($arr_c);$i++)
					$contenido.=$arr_c[$i];
				$contenido= str_replace("#PAGETITLE#",$pagetitle,$contenido);
				$contenido= str_replace("#FIRSTNAME#",$firstname,$contenido);
				$contenido= str_replace("#LASTNAME#",$lastname,$contenido);
				$contenido= str_replace("#LOGIN#",$login,$contenido);
				$contenido= str_replace("#PASSWORD#",$pass,$contenido);
				$contenido=str_replace("#SERVERPATH#",$GLOBALS['domain_root'],$contenido);
				$page = new page;
				smail($p->title. " <".$GLOBALS['system_email'].">", $usremail, "Your Account Info", $contenido);
				return(true);
			}
			else{
				return(false);
			}
	}
	
	function obtiene_permisos($conn, $id_usuario){
	//Devuelve una matriz con los permisos para el usuario
		$q = "SELECT operaciones.descripcion, operaciones.pagina ";
		$q.= "FROM relacion_us_op ";
		$q.= "INNER JOIN operaciones ON (operaciones.id = relacion_us_op.id_operacion) ";
		$q.= "INNER JOIN usuarios ON (usuarios.id = relacion_us_op.id_usuario) ";
		$q.= "WHERE usuarios.id = '$id_usuario' ";
		//die($q);
		$r = $conn->Execute($q);
		$i = 0;
		while(!$r->EOF){
			$aPermiso[$i]['descripcion'] = $r->fields['descripcion'];
			$aPermiso[$i]['pagina'] = $r->fields['pagina'];
			$r->movenext();
			$i++;
		}
		return $aPermiso;
	}
	
	function chequea_permiso($conn, $id_usuario, $pagina){
	//Devuelve una matriz con los permisos para el usuario
		$q = "SELECT 'X' ";
		$q.= "FROM relacion_us_op ";
		$q.= "INNER JOIN operaciones ON (operaciones.id = relacion_us_op.id_operacion) ";
		$q.= "INNER JOIN usuarios ON (usuarios.id = relacion_us_op.id_usuario) ";
		$q.= "WHERE operaciones.id = '$id_usuario' ";
		$q.= "AND operaciones.pagina = '$pagina' ";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF)
			return true;
		else
			return false;
	}
	//CEPV.100706.SN VALIDANDO LA EMPRESA EN CASO QUE EL SISTEMA SEA MULTIEMPRESA
	function validar_empresa($conn, $Usuario,$Empresa){
		$q="SELECT * FROM emp_usu WHERE emp_cod=$Empresa and usu_cod=$Usuario";
		//die($q);
		$r=$conn->Execute($q);
		if(!$r->EOF){
			session_start();
			$_SESSION['EmpresaL']=$Empresa;
			return true;
		}else
			return false;
	}
	//CEPV.100706.EN 
}
?>
