<?php

require_once("conectar.php");
	
	class LoginUser {
		
		private $db;
		private $conexion;
		
		function __construct() {
			$this -> db = new Conectar();
			$this -> conexion = $this->db->conexion();
		}
		
		public function does_user_exist($email,$password)
		{
			$query = "Select * from usuarios where email='$email' and password = '$password'";
			$result = $this -> conexion->prepare($query);
			$result->execute();
			
			if($result->rowCount() == 1){
				$json['success'] = ' Bienvenido '.$email;
				// Al igual como en register_movil, con este json enviamos los datos al MainActivity
				$query = "SELECT iduser_,email,photo,nombres FROM usuarios WHERE email = ?";
				
				try {
					// Preparar sentencia
					$comando = $this->conexion->prepare($query);
					// Ejecutar sentencia preparada
					$comando->execute(array($email));
					// Capturar primera fila del resultado
					$row = $comando->fetch(PDO::FETCH_ASSOC);
					$json['usuario'][]=$row;
									
				} catch (PDOException $e) {
					$json['error'] = 'exception';
					// Aquí puedes clasificar el error dependiendo de la excepción
					// para presentarlo en la respuesta Json
					return -1;
				}

				echo json_encode($json);
			}else{
				$json['error'] = 'Las credenciales de inicio de sesión son incorrectas';
				echo json_encode($json);
			}
			
		}
		
	}
	
	$loginUser = new LoginUser();
	if(isset($_POST['email'],$_POST['password'])) {
		$email = $_POST['email'];
		$password = $_POST['password'];
		
		if(!empty($email) && !empty($password)){
			
			$encrypted_password = md5($password);
			$loginUser-> does_user_exist($email,$password);
			
		}else{
			echo json_encode("debe escribir ambas entradas");
		}
		
	}
?>