<?php

require_once('../../layouts/dbconexion_pdo.php');
define('METHOD','AES-256-CBC');
define('SECRET_KEY','LogiVzla2023*');
define('SECRET_IV','202020');

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

	//FUNCION PARA ENCRIPTAR LAS CONTRASEÑAS
	public static function encryption($string){
			$output=FALSE;
			$key=hash('sha256', SECRET_KEY);
			$iv=substr(hash('sha256', SECRET_IV), 0, 16);
			$output=openssl_encrypt($string, METHOD, $key, 0, $iv);
			$output=base64_encode($output);
			return $output;
	}
	//FIN FUNCION PARA ENCRIPTAR LAS CONTRASEÑAS

	//FUNCION PARA DESENCRIPTAR LAS CONTRASEÑAS
	public static function decryption($string){
		$key=hash('sha256', SECRET_KEY);
		$iv=substr(hash('sha256', SECRET_IV), 0, 16);
		$output=openssl_decrypt(base64_decode($string), METHOD, $key, 0, $iv);
		return $output;
	}
	//FIN FUNCION PARA DESENCRIPTAR LAS CONTRASEÑAS

	//FUNCION PARA MOSTRAR LISTADO DE EMPLEADOS
    public function dataview_empleados($query)
    {
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        if($stmt->rowCount()>0)
        {
            while($row=$stmt->fetch(PDO::FETCH_ASSOC))
                {
                    ?>
<tr>
    <td><?php print($row['usuario_u']); ?></td>
    <td>
        <span name='contrasena[<?php echo $row['id_u']?>]' id='contrasena[<?php echo $row['id_u']?>]'
            style='width: 90%; display:none;'><?php echo crud::decryption($row['pass_u']); ?></span>
        <span name='contrasena2[<?php echo $row['id_u']?>]' id='contrasena2[<?php echo $row['id_u']?>]'
            style='width: 90%; display:block;'>&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;</span>
        <a href="#" onclick="ocultarContrasena(<?php echo $row['id_u']?>); event.preventDefault();"
            style="float: left"><img src="../../assets/img/icons/mostrar.png" height="20" width="20" alt="pass"><i
                class="fa fa-eye" id="ojo2[<?php echo $row['id_u']?>]" aria-hidden="true">
    </td>
    <td><?php print($row['nombres_e'].' '.$row['apellidos_e']); ?>
    </td>
    <td><?php print($row['documento_e']); ?></td>
    <td><?php print($row['email_e']); ?></td>
    <td><?php print($row['telefono_e']); ?></td>

    <td style="text-align: center">
        <a title="Editar Usuario" href="editar?id=<?php print($row['id_u']); ?>"><img
                src="../../assets/img/icons/editar.png" height="20" width="20" alt="Editar Usuario"></a> 
    
				&nbsp;&nbsp;&nbsp;&nbsp;
			
        <a title="Eliminar Usuario" onclick="eliminarempleado(<?php print($row['id_u']) ?>,'<?php print($row['usuario_u']) ?>')"><img
                src="../../assets/img/icons/eliminar.png" height="20" width="20" alt="Eliminar Usuario"></a>
    </td>
</tr>
<?php             
                } 
        }
        else { ?>
<tr>
    <td>No hay registros</td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
</tr>
<?php
        }
    }
    //FIN FUNCION PARA MOSTRAR LISTADO DE EMPLEADOS  

	//FUNCION PARA REGISTRAR UN EMPLEADO EN LA BD
    public function crear_empleado($nombres,$apellidos,$nacionalidad,$documento,$sexo,$dob,$usuario,$pass,$email,$telefono)
	{
		try
		{
            $id_tipo_usuario = "3";
            $created_at= date("Y-m-d H:i:s", strtotime('now'));
            $updated_at = date("Y-m-d H:i:s", strtotime('now'));
       
			$stmt = $this->conn->prepare("INSERT INTO usuarios(usuario,pass,id_tipo_usuario,created_at,updated_at) 
				VALUES(:usuario,:pass,:id_tipo_usuario,:created_at,:updated_at)");
			$stmt->bindparam(":usuario",$usuario);
			$stmt->bindparam(":pass",$pass);
			$stmt->bindparam(":id_tipo_usuario",$id_tipo_usuario);
			$stmt->bindparam(":created_at",$created_at);
			$stmt->bindparam(":updated_at",$updated_at);
			$stmt->execute();

            $last_id = $this->conn->lastInsertId();
            $id_usuario = $this->conn->lastInsertId();

            $stmt2 = $this->conn->prepare("INSERT INTO empleados(id_usuario,documento,nombres,apellidos,dob,nacionalidad,sexo,email,telefono,created_at,updated_at) 
				VALUES(:id_usuario,:documento,:nombres,:apellidos,:dob,:nacionalidad,:sexo,:email,:telefono,:created_at,:updated_at)");
			$stmt2->bindparam(":id_usuario",$id_usuario);
			$stmt2->bindparam(":documento",$documento);
			$stmt2->bindparam(":nombres",$nombres);
			$stmt2->bindparam(":apellidos",$apellidos);
            $stmt2->bindparam(":dob",$dob);
			$stmt2->bindparam(":nacionalidad",$nacionalidad);
			$stmt2->bindparam(":sexo",$sexo);
			$stmt2->bindparam(":email",$email);
            $stmt2->bindparam(":telefono",$telefono);
			$stmt2->bindparam(":created_at",$created_at);
			$stmt2->bindparam(":updated_at",$updated_at);
			$stmt2->execute();
			
			$operador = $_SESSION['user_session'];
			$operacion = "Se registro el usuario (empleado): " . $last_id;
			$ip = $_SERVER['REMOTE_ADDR'];
			$this->log_operaciones($operador,$operacion,$usuario,$ip);
			return true;
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();	
			return false;
		}
	}
	//FIN FUNCION PARA REGISTRAR UN EMPLEADO EN LA BD

	//FUNCION PARA EDITAR UN EMPLEADO EN LA BD
    public function editar_empleado($nombres,$apellidos,$nacionalidad,$documento,$sexo,$dob,$usuario,$pass,$email,$telefono,$id_usuario)
	{
		try
		{
            $updated_at = date("Y-m-d H:i:s", strtotime('now'));
			$stmt=$this->conn->prepare("UPDATE usuarios SET 
				usuario=:usuario, 
				pass=:pass,  
				updated_at=:updated_at
				WHERE id_usuario=:id_usuario");
			$stmt->bindparam(":usuario",$usuario);
			$stmt->bindparam(":pass",$pass);
            $stmt->bindparam(":updated_at",$updated_at);
			$stmt->bindparam(":id_usuario",$id_usuario);
			$stmt->execute();

			$stmt2=$this->conn->prepare("UPDATE empleados SET 
				documento=:documento, 
				nombres=:nombres,  
                apellidos=:apellidos, 
				dob=:dob, 
                nacionalidad=:nacionalidad,
				sexo=:sexo,
				email=:email,  
				telefono=:telefono,
                updated_at=:updated_at
				WHERE id_usuario=:id_usuario");
			$stmt2->bindparam(":documento",$documento);
			$stmt2->bindparam(":nombres",$nombres);
			$stmt2->bindparam(":apellidos",$apellidos);
			$stmt2->bindparam(":dob",$dob);
            $stmt2->bindparam(":nacionalidad",$nacionalidad);
			$stmt2->bindparam(":sexo",$sexo);
            $stmt2->bindparam(":email",$email);
			$stmt2->bindparam(":telefono",$telefono);
            $stmt2->bindparam(":updated_at",$updated_at);
            $stmt2->bindparam(":id_usuario",$id_usuario);
			$stmt2->execute();
			
			$operador = $_SESSION['user_session'];
			$operacion = "Se modificaron los datos del usuario(empleado): " . $id_usuario;
			$ip = $_SERVER['REMOTE_ADDR'];
			$this->log_operaciones($operador,$operacion,$usuario,$ip);

			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();	
			return false;
		}
	}
	//FIN FUNCION PARA EDITAR UN EMPLEADO EN LA BD

	//FUNCION PARA ELIMINAR UN EMPLEADO DE LA BD Y SE REGISTRE EN LA TABLA DE RESPALDO
    public function eliminar_empleado($id_usuario)
	{  
            extract($this->get_usuarioID($id_usuario));	
            extract($this->get_empleadoidusuario($id_usuario));	
            $fecha_eliminado= date("Y-m-d H:i:s", strtotime('now'));
            $stmt2 = $this->conn->prepare("INSERT INTO empleados_eliminados(id_usuario,documento,nombres,apellidos,dob,nacionalidad,sexo,email,telefono,usuario,pass,tipo_usuario,created_at,updated_at,fecha_eliminado) 
				VALUES(:id_usuario,:documento,:nombres,:apellidos,:dob,:nacionalidad,:sexo,:email,:telefono,:usuario,:pass,:tipo_usuario,:created_at,:updated_at,:fecha_eliminado)");
			$stmt2->bindparam(":id_usuario",$id_usuario);
			$stmt2->bindparam(":documento",$documento);
			$stmt2->bindparam(":nombres",$nombres);
			$stmt2->bindparam(":apellidos",$apellidos);
            $stmt2->bindparam(":dob",$dob);
			$stmt2->bindparam(":nacionalidad",$nacionalidad);
			$stmt2->bindparam(":sexo",$sexo);
			$stmt2->bindparam(":email",$email);
            $stmt2->bindparam(":telefono",$telefono);
            $stmt2->bindparam(":usuario",$usuario);
            $stmt2->bindparam(":pass",$pass);
            $stmt2->bindparam(":tipo_usuario",$tipo_usuario);
			$stmt2->bindparam(":created_at",$created_at);
			$stmt2->bindparam(":updated_at",$updated_at);
			$stmt2->bindparam(":fecha_eliminado",$fecha_eliminado);
			$stmt2->execute();

			$stmt = $this->conn->prepare("DELETE FROM usuarios WHERE id_usuario=:id_usuario");
			$stmt->bindparam(":id_usuario",$id_usuario);
			$stmt->execute();

            $stmt3 = $this->conn->prepare("DELETE FROM empleados WHERE id_usuario=:id_usuario");
			$stmt3->bindparam(":id_usuario",$id_usuario);
			$stmt3->execute();

			$operador = $_SESSION['user_session'];
			$operacion = "Se eliminó el usuario(empleado): " . $id_usuario;
			$ip = $_SERVER['REMOTE_ADDR'];
			$this->log_operaciones($operador,$operacion,$usuario,$ip);
			
			return true;		
	}
	//FIN FUNCION PARA ELIMINAR UN EMPLEADO DE LA BD Y SE REGISTRE EN LA TABLA DE RESPALDO

	//BUSCAR USUARIOS POR ID
    public function get_usuarioID($id_usuario)
	{
		$stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE id_usuario=:id_usuario");
		$stmt->execute(array(":id_usuario"=>$id_usuario));
		$editRow=$stmt->fetch(PDO::FETCH_ASSOC);
		return $editRow;
	}
	//FIN BUSCAR USUARIOS POR ID

	//BUSCAR EMPLEADO POR ID DE USUARIO
    public function get_empleadoidusuario($id_usuario)
	{
		$stmt = $this->conn->prepare("SELECT * FROM empleados WHERE id_usuario=:id_usuario");
		$stmt->execute(array(":id_usuario"=>$id_usuario));
		$editRow=$stmt->fetch(PDO::FETCH_ASSOC);
		return $editRow;
	}
	//FIN BUSCAR EMPLEADO POR ID DE USUARIO

	//BUSCAR CLIENTE POR ID DE USUARIO
    public function get_clienteidusuario($id_usuario)
	{
		$stmt = $this->conn->prepare("SELECT * FROM clientes WHERE id_usuario=:id_usuario");
		$stmt->execute(array(":id_usuario"=>$id_usuario));
		$editRow=$stmt->fetch(PDO::FETCH_ASSOC);
		return $editRow;
	}
	//FIN BUSCAR CLIENTE POR ID DE USUARIO

	//BUSCAR PRODUCTO POR ID
    public function get_prodID($id_prod)
	{
		$stmt = $this->conn->prepare("SELECT * FROM productos WHERE id_prod=:id_prod");
		$stmt->execute(array(":id_prod"=>$id_prod));
		$editRow=$stmt->fetch(PDO::FETCH_ASSOC);
		return $editRow;
	}
	//FIN BUSCAR PRODUCTO POR ID

	//BUSCAR PRODUCTO POR ID
    public function get_prod_invID($id_prod)
	{
		$stmt = $this->conn->prepare("SELECT * FROM inventario WHERE id_prod=:id_prod");
		$stmt->execute(array(":id_prod"=>$id_prod));
		$editRow=$stmt->fetch(PDO::FETCH_ASSOC);
		return $editRow;
	}
	//FIN BUSCAR PRODUCTO POR ID
	
	//BUSCAR ALMACEN POR ID
    public function get_almID($id_alm)
	{
		$stmt = $this->conn->prepare("SELECT * FROM almacenes WHERE id_alm=:id_alm");
		$stmt->execute(array(":id_alm"=>$id_alm));
		$editRow=$stmt->fetch(PDO::FETCH_ASSOC);
		return $editRow;
	}
	//FIN BUSCAR ALMACEN POR ID

	//BUSCAR PRODUCTO POR CODIGO
	public function get_cod_prod($cod_prod)
	{
		$stmt = $this->conn->prepare("SELECT * FROM productos WHERE cod_prod=:cod_prod");
		$stmt->execute(array(":cod_prod"=>$cod_prod));
		$editRow=$stmt->fetch(PDO::FETCH_ASSOC);
		return $editRow;
	}
	//FIN BUSCAR PRODUCTO POR CODIGO

	//SELECT DE PRODUCTOS
	public function getproductos()
	{

		$query = "SELECT * FROM productos";
		$stmt = $this->conn->prepare($query);
		$stmt->execute();
		
		$total_no_of_records = $stmt->rowCount();
		
		if($total_no_of_records > 0)
		{
			?>
			<option value="">Seleccione Producto...</option>
		<?php	
					while($row=$stmt->fetch(PDO::FETCH_ASSOC))
				{
					?>
					<option value="<?php echo $row['cod_prod']?>">
						<?php echo $row['producto'];?>
					</option>
					<?php
				}?>
	
			<?php
		}
	}
	//FIN SELECT DE PRODUCTOS

	//SELECT DE PRODUCTOS AGREGANDO CON BOTON
	public function getproductosadd()
	{
		$query = "SELECT * FROM productos";
		$stmt = $this->conn->prepare($query);
		$stmt->execute();
		
		$total_no_of_records = $stmt->rowCount();
		
		if($total_no_of_records > 0)
		{
			?>
			'<option value="">Seleccione Producto...</option>' +
		<?php	
					while($row=$stmt->fetch(PDO::FETCH_ASSOC))
				{
					?>
					'<option value="<?php echo $row['cod_prod']?>"><?php echo $row['producto'];?></option>' +	
					<?php
				}?>
			<?php
		}
	}
	//FIN SELECT DE PRODUCTOS AGREGANDO CON BOTON

	//FUNCION PARA REGISTRAR EN EL LOG DE OPERACIONES
    public function log_operaciones($operador,$operacion,$id_usuario,$ip) 
	{	
		try
		{
		$created_at= date("Y-m-d H:i:s", strtotime('now'));
		$updated_at = date("Y-m-d H:i:s", strtotime('now'));
		$stmt2 = $this->conn->prepare("INSERT INTO log_operaciones(operador, operacion, usuario_afectado, ip, created_at, updated_at)
		VALUES ('".$operador."', '".$operacion."', '".$id_usuario."', '".$ip."', '".$created_at."', '".$updated_at."')");
		$stmt2->execute();
		return true;
	}
	catch(PDOException $e)
	{
		echo $e->getMessage();	
		return false;
	}
		
	}
	//FIN FUNCION PARA REGISTRAR EN EL LOG DE OPERACIONES
}
?>