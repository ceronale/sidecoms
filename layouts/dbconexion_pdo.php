<?php
class Database
{   
    private $servername = "localhost";
    private $dbname = "c1422206_sidecoms";
    private $username = "root";
    private $password = "";
    
    public $conn;

    //FUNCION PARA CONECTAR A LA BD
    public function dbConnection()
	{
     
	    $this->conn = null;    
        try
		{
            $this->conn = new PDO("mysql:host=" . $this->servername . ";dbname=" . $this->dbname, $this->username, $this->password);
            $this->conn->exec("set names utf8");
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);	
        }
		catch(PDOException $exception)
		{
            echo "Connection error: " . $exception->getMessage();
        }
         
        return $this->conn;
    }
     //FIN FUNCION PARA CONECTAR A LA BD
}
?>