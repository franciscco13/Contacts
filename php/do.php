<?php	  
	if(isset($_GET['do'])){
		switch($_GET['do']){

			case 'getAllData':
				require_once('DatabaseDriver.php');		
				$dd = DatabaseDriver::getInstance(); 
				echo $dd -> getData(); 
				break;

			case 'modifyData': 
				require_once('DatabaseDriver.php');	
				$dd = DatabaseDriver::getInstance();  
				echo $dd -> modifyData(
					$_GET['db_id'], 
					$_GET['apP'], 
					$_GET['apM'], 
					$_GET['name'], 
					$_GET['tel']
				); 
				break;

			case 'addData': 
				require_once('DatabaseDriver.php');	
				$dd = DatabaseDriver::getInstance();  
				echo $dd -> addData(					
					$_GET['apP'], 
					$_GET['apM'], 
					$_GET['name'], 
					$_GET['tel'],
					$_GET['job']
				); 
				break;

			case 'deleteData': 
				require_once('DatabaseDriver.php');	
				$dd = DatabaseDriver::getInstance();  
				echo $dd -> deleteData(
					$_GET['db_id']
				); 
				break;

			case 'login':
				require_once('DatabaseDriver.php');	
				$dd = DatabaseDriver::getInstance(); 
				echo $dd -> login(
					$_POST['email'],
					$_POST['password']
				); 
				break;

			case 'getSession':
				if(!isset($_SESSION))
					session_start(); 
				if(isset($_SESSION['perfil']))
					echo json_encode($_SESSION['perfil']);				
				break;

			case 'closeSession':
				if(!isset($_SESSION))
	    			session_start();
				session_destroy();
				$_SESSION = [];
				print_r($_SESSION);
				header("location: /");
				break;
		}
	}
?>