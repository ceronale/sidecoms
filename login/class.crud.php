<?php

require_once('../layouts/dbconexion_pdo.php');
define('METHOD', 'AES-256-CBC');
define('SECRET_KEY', 'SideComs*');
define('SECRET_IV', '202020');

class crud
{
	private $conn;

	public function __construct()
	{
		$database = new Database();
		$db = $database->dbConnection();
		$this->conn = $db;
	}

	public function runQuery($sql)
	{
		$stmt = $this->conn->prepare($sql);
		return $stmt;
	}

	public static function encryption($string)
	{
		$output = FALSE;
		$key = hash('sha256', SECRET_KEY);
		$iv = substr(hash('sha256', SECRET_IV), 0, 16);
		$output = openssl_encrypt($string, METHOD, $key, 0, $iv);
		$output = base64_encode($output);
		return $output;
	}

	public static function decryption($string)
	{
		$key = hash('sha256', SECRET_KEY);
		$iv = substr(hash('sha256', SECRET_IV), 0, 16);
		$output = openssl_decrypt(base64_decode($string), METHOD, $key, 0, $iv);
		return $output;
	}



	public function doLogin($uname, $upass)
	{
		try {
			$stmt2 = $this->conn->prepare("SELECT * FROM usuarios WHERE usuario=:uname");
			$stmt2->execute(array(':uname' => $uname));
			$userRow = $stmt2->fetch(PDO::FETCH_ASSOC);

			if ($stmt2->rowCount() == 1) {
				$stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE usuario=:uname and password=:upass");
				$stmt->execute(array(':uname' => $uname, ':upass' => $upass));
				//$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
				if ($stmt->rowCount() == 1) {
					// Usuario y contraseña válidos
					return 1;
				} else {
					// Contraseña incorrecta
					return 2;
				}

				// if ($stmt->rowCount() == 1) {
				// 	$_SESSION['user_session'] = $userRow['id_usuario'];
				// 	$_SESSION['user_type'] = $userRow['id_tipo_usuario'];
				// 	$operador = $_SESSION['user_session'];
				// 	extract($this->getID($operador));
				// 	$operacion = "Inicio de sesión del usuario: " . $operador;
				// 	$ip = $_SERVER['REMOTE_ADDR'];
				// 	$this->log_operaciones($operador, $operacion, $usuario, $ip);

				// 	return $_SESSION['user_type'];
				// } else {
				// 	return 0;
				// }


			} else {
				// Nombre de usuario no encontrado
				return 3;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
			return 0;
		}
	}












	public function is_loggedin()
	{
		if (isset($_SESSION['user_session'])) {
			return true;
		}
	}

	public function redirect($url)
	{
		header("Location: $url");
	}

	public function doLogout()
	{
		$operador = $_SESSION['user_session'];
		extract($this->getID($operador));
		$operacion = "Cierre de sesión del usuario: " . $operador;
		$ip = $_SERVER['REMOTE_ADDR'];
		$this->log_operaciones($operador, $operacion, $usuario, $ip);
		session_destroy();
		unset($_SESSION['user_session']);
		return true;
	}

	public function log_operaciones($operador, $operacion, $id_usuario, $ip)
	{
		try {
			$created_at = date("Y-m-d H:i:s", strtotime('now'));
			$updated_at = date("Y-m-d H:i:s", strtotime('now'));
			$stmt2 = $this->conn->prepare("INSERT INTO  log_operaciones(operador, operacion, usuario_afectado, ip)
		VALUES ('" . $operador . "', '" . $operacion . "', '" . $id_usuario . "', '" . $ip . "', '" . $created_at . "', '" . $updated_at . "')");
			$stmt2->execute();
			return true;
		} catch (PDOException $e) {
			echo $e->getMessage();
			return false;
		}
	}

	public function getID($id)
	{
		$stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE id_usuario=:id");
		$stmt->execute(array(":id" => $id));
		$editRow = $stmt->fetch(PDO::FETCH_ASSOC);
		return $editRow;
	}

	public function getUsuario($usuario)
	{
		$stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE usuario=:usuario");
		$stmt->execute(array(":usuario" => $usuario));
		$editRow = $stmt->fetch(PDO::FETCH_ASSOC);
		return $editRow;
	}

	public function gettoken($token)
	{
		$stmt = $this->conn->prepare("SELECT * FROM token WHERE token=:token");
		$stmt->execute(array(":token" => $token));
		$editRow = $stmt->fetch(PDO::FETCH_ASSOC);
		return $editRow;
	}

	public function existe_usuario($usuario)
	{
		$stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE usuario=:usuario OR correo1=:usuario");
		$stmt->execute(array(":usuario" => $usuario));
		$editRow = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($editRow == "") {
			return 0;
		} else {
			return $editRow;
		}
	}

	public function token($length = 20)
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		$this->insert_token($randomString);
		return $randomString;
	}

	public function insert_token($token)
	{
		try {

			$stmt2 = $this->conn->prepare("INSERT INTO  token(token, status)
		VALUES ('" . $token . "', 0)");
			$stmt2->execute();
			return true;
		} catch (PDOException $e) {
			echo $e->getMessage();
			return false;
		}
	}

	public function update_contrasena($id, $contrasena, $usuario)
	{
		try {

			$stmt = $this->conn->prepare("UPDATE usuarios SET 
				contrasena=:contrasena 
				WHERE id=:id");
			$stmt->bindparam(":contrasena", $contrasena);
			$stmt->bindparam(":id", $id);
			$stmt->execute();

			$operador = $_SESSION['user_session'];
			$operacion = "Se Cambio la contraseña del usuario: " . $id;
			$ip = $_SERVER['REMOTE_ADDR'];
			$this->log_operaciones($operador, $operacion, $usuario, $ip);

			return true;
		} catch (PDOException $e) {
			echo $e->getMessage();
			return false;
		}
	}
}
