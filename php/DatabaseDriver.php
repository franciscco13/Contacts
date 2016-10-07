<?php	
	class DatabaseDriver 
	{
		private static $instance = null; 
		private $conn00 = null;
		private $conn01 = null;
		private $conn02 = null;
		private $conn_vars = [
			"diets" => [
				"host"=> "localhost",
				"user"=> "root",
				"pass"=> "",
				"db"=> "dietas"
			],
			"dentist" => [
				"host"=> "localhost",
				"user"=> "root",
				"pass"=> "",
				"db"=> "dentistas"
			],
			"contacts" => [
				"host"=> "localhost",
				"user"=> "root",
				"pass"=> "",
				"db"=> "contacts"
			]
		]; 

		/////////////////////////////////////////////////////////////////
		// Singleton functions
		private function __construct() {}
		public static function getInstance( ) {
    		if (is_null(self::$instance))
      			self::$instance = new DatabaseDriver( );
    		return self::$instance;
  		}

  		/////////////////////////////////////////////////////////////////
  		// Connection methods
		private function startConnection(){  
			$this->conn01  = mysqli_connect(
				$this->conn_vars['diets']['host'], 
				$this->conn_vars['diets']['user'], 
				$this->conn_vars['diets']['pass'], 
				$this->conn_vars['diets']['db'])
			or die("Connection failed: " . $this->conn01->connect_error); 
			$this->conn02 = mysqli_connect(
				$this->conn_vars['dentist']['host'], 
				$this->conn_vars['dentist']['user'], 
				$this->conn_vars['dentist']['pass'], 
				$this->conn_vars['dentist']['db'])
			or die("Connection failed: " . $this->conn02->connect_error); 
		}

		/////////////////////////////////////////////////////////////////
		private function closeConnection(){
			$this->conn01->close();
			$this->conn02->close();
		}

		/////////////////////////////////////////////////////////////////
		// Get all data 
		function getData()
		{
			$this->startConnection();			
			$array = [];

			// Get dietician data
			$query = "SELECT idNutriologo, name, apellidoMat, apellidoPat, tel from nutriologo";
			$results = $this->conn01->query($query);			
			while($row = $results->fetch_assoc()){				
				$this->codifyUTF($row); 
				$aux = [
					"id" => "diets::".$row['idNutriologo'],
					"name" => $row['name'],
					"apellidoPat" => $row['apellidoPat'],
					"apellidoMat" => $row['apellidoMat'],
					"tel" => $row['tel'],
					"job" => "Nutriologo"
				];
				$array[] = $aux;
			}  
			// Get dentists data
			$query2 = "SELECT cedula, nombre, telefono from dentista";
			$results2 = $this->conn02->query($query2);			
			while($row = $results2->fetch_assoc()){				
				$this->codifyUTF($row);
				$partials = explode(" ", $row['nombre']);
				$length = count($partials);
 
				$aux = [
					"id" => "dentists::".$row['cedula'],
					"name" => $partials[0],
					"apellidoPat" => $partials[$length - 2],
					"apellidoMat" => $partials[$length - 1],
					"tel" => $row['telefono'],
					"job" => "Dentista"
				]; 
				$array[] = $aux;
			}  
			$this->closeConnection();
			return json_encode($array);
		}

		/////////////////////////////////////////////////////////////////
		// Modify data
		function modifyData($db_id, $apP, $apM, $name, $tel)
		{			
			$this->startConnection();	
			$db = explode("::", $db_id)[0];
			$id = explode("::", $db_id)[1];

			switch($db){		
				case "diets":
					$query =   "UPDATE nutriologo 
								SET name = '".$name."', 
									apellidoMat = '".$apM."', 
									apellidoPat = '".$apP."', 
									tel = ".$tel."
								WHERE idNutriologo = ".$id;
					$this->conn01->query($query);
					break;

				case "dentists":
					$query =   "UPDATE dentista 
								SET nombre = '".$name." ".$apP." ".$apM."', telefono = '".$tel."' WHERE cedula = ".$id;
					if($this->conn02->query($query) === FALSE){
						echo "Error updating record: " . $this->conn02->error;
					}
					break;
			}
			$this->closeConnection();	

		}

		/////////////////////////////////////////////////////////////////
		function deleteData($db_id)
		{
			$this->startConnection();	
			$db = explode("::", $db_id)[0];
			$id = explode("::", $db_id)[1];

			switch($db){		
				case "diets":
					$query =   "DELETE from nutriologo WHERE idNutriologo = ".$id;
					$this->conn01->query($query);
					break;

				case "dentists":
					$query =   "DELETE from dentista WHERE cedula = ".$id;
					$this->conn02->query($query); 
					break;
			}
			$this->closeConnection();
		}
		/////////////////////////////////////////////////////////////////
		function addData($apP, $apM, $name, $tel, $job)
		{
			$this->startConnection();	 
			switch($job){		
				case "diets":
					$query =   "INSERT INTO nutriologo (name, apellidoPat, apellidoMat, tel) 
								VALUES('".$name."','".$apP."','".$apM."',".$tel.")";
					if($this->conn01->query($query) === FALSE)
						echo "Error: " . $query . "<br>" . $this->conn01->error;
					else echo $this->conn01->insert_id;;					
					break;

				case "dentists":

					$sql = "select max(cedula) as cedula from dentista";
					$result = $this->conn02->query($sql);
					$cedula = null;
					if($result->num_rows > 0){
						$row = $result->fetch_assoc();
						$cedula = $row['cedula']*1 + 1;
					}
					$query =   "INSERT INTO dentista (cedula, id_usuario, nombre, telefono, hora_entrada, hora_salida) 
								VALUES (".$cedula.", 2, '".$name." ".$apP." ".$apM."', '".$tel."', '10:00:00', '19:30:00')";
					if($this->conn02->query($query) === FALSE)
						echo "Error: " . $query . "<br>" . $this->conn02->error;					
					else echo $cedula;
					break;
			}
			$this->closeConnection();
		}

		/////////////////////////////////////////////////////////////////
		function login($email,$pass)
		{
			$this->conn00  = mysqli_connect(
				$this->conn_vars['contacts']['host'], 
				$this->conn_vars['contacts']['user'], 
				$this->conn_vars['contacts']['pass'], 
				$this->conn_vars['contacts']['db'])
			or die("Connection failed: " . $this->conn00->connect_error); 
 

			$sql = "SELECT idAdmin,nombre,email, pass FROM admin WHERE email = '".$email."' AND pass = '".$pass."'";
			$result = $this->conn00->query($sql) or die("no se pudo ");
		
			if ($result === FALSE) 
				echo "0";
			else if ($result->num_rows  > 0) {
				// output data of each row
				$row = $result->fetch_assoc();
				if(!isset($_SESSION))
					session_start();
				$_SESSION['perfil'] = [
					"id" 	 => $row["idAdmin"],
					"nombre" => $row["nombre"]
				];
				$this->conn00->close();
				return 1;
			}
			$this->conn00->close();
			return 0; 
		}


		/////////////////////////////////////////////////////////////////
		// Parse to UTF valid strings
		private function codifyUTF(&$array){
			foreach($array as &$value){
				$value = utf8_encode($value);
			}
		}
	}
?>
