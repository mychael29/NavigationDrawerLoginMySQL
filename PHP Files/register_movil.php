<?php

require_once("conectar.php");
	
	class SignupUser {
		
		private $db;
        private $conexion;
		
		function __construct() {
			$this -> db = new Conectar();
			$this -> conexion = $this->db->conexion();
        }
        
		public function does_user_exist($email,$password,$url_image,$nombres)
		{
            $query = "Select * from usuarios where email='$email'";
            $result = $this -> conexion->prepare($query);
            $result->execute();
            
			if($result->rowCount() == 1){
              
				$json['error'] = 'Ya existe un usuario con '.$email;
				echo json_encode($json);
			}else{
				//registro
                $query = "insert into usuarios (email, password, photo, nombres) values (?,?,?,?)";
				$inserted = $this->conexion->prepare($query);
				
				$inserted->bindParam(1, $email, PDO::PARAM_STR); 
				$inserted->bindParam(2, $password, PDO::PARAM_STR);
				$inserted->bindParam(3, $url_image, PDO::PARAM_STR);
				$inserted->bindParam(4, $nombres, PDO::PARAM_STR);
			
				if($inserted->execute()){
					$json['success'] = 'Cuenta creada';

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
						// Aquí puedes clasificar el error dependiendo de la excepción
						// para presentarlo en la respuesta Json
						return -1;
					}

				}else{
					$json['error'] = 'Se produjo un error';
				}
				echo json_encode($json);
			}    
			
		}
		
	}
	
	$signupUser = new SignupUser();
	if(isset($_POST['email'],$_POST['password'],$_POST['nombres'])) {
		$email = $_POST['email'];
		$password = $_POST['password'];
		$nombres = $_POST['nombres'];
		$image = $_POST['photo'];
		
		if($image!="no imagen"){
			$path  = "profile_image/$nombres.jpg"; 
			$url_image = "profile_image/".$nombres.".jpg";// para no presentar errores, agregarle el id al nombre de la imagen
			file_put_contents($path,base64_decode($image));
			//$bytesArchivo=file_get_contents($path);//para guardar la imagen en la tabla de la bbdd
		}else{
			$url_image = "sin imagen";
		}
        
		if(!empty($email) && !empty($password) && !empty($nombres)){
			
			$encrypted_password = md5($password);
			$signupUser-> does_user_exist($email,$password,$url_image,$nombres);
			
		}else{
			echo json_encode("debe escribir ambas entradas");
		}
		
	}
?>