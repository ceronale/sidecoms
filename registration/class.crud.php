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


	public function doRegister($uname, $upass, $firstname, $lastname, $email, $phone, $gender)
	{
		try {
			// Verificar si el nombre de usuario ya existe
			$stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE usuario=:uname");
			$stmt->execute(array(':uname' => $uname));
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);

			if ($stmt->rowCount() == 1) {
				// Nombre de usuario ya existe
				return 1;
			} else {
				// Campos que estan medio cursed hay que preguntar codempresa
				// Insertar los datos en la tabla usuarios
				$stmt2 = $this->conn->prepare("INSERT INTO usuarios (usuario, nombre, apellido, email, password, telefono, genero , codempresa) VALUES (:uname, :firstname, :lastname, :email, :upass, :phone, :gender, codempresa)");
				$stmt2->bindParam(':uname', $uname);
				$stmt2->bindParam(':firstname', $firstname);
				$stmt2->bindParam(':lastname', $lastname);
				$stmt2->bindParam(':email', $email);
				$stmt2->bindParam(':upass', $upass);
				$stmt2->bindParam(':phone', $phone);
				$stmt2->bindParam(':gender', $gender);
				$stmt2->execute();

				return 2; // Registro exitoso
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
			return 0; // Error desconocido
		}
	}
}
