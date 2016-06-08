<?php
 
function comprobarCredenciales($input) {
	 
	 // get the HTTP method, path and body of the request
	//$method = $_SERVER['REQUEST_METHOD'];
	//$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
	// $contents = file_get_contents('php://input');
	// $contents = utf8_encode($contents);
	// $results = json_decode($contents); 
	// $input = json_decode($contents);
	
	// connect to the mysql database
	$link = mysqli_connect('localhost', 'root', '', 'dbMedViserData');
	mysqli_set_charset($link,'utf8');

	/* comprobar la conexión */
	if (mysqli_connect_errno()) {
	    printf("Falló la conexión: %s\n", mysqli_connect_error());
	    exit();
	}
	
	$user = mysqli_real_escape_string($link, $input->{"user"}); $pass = mysqli_real_escape_string($link, $input->{"pass"});
	 
	$sql = "select idUser from users WHERE mail = '".$user."' AND pass = '".$pass."';";
	//echo($sql);
	// excecute SQL statement
	if($result = mysqli_query($link,$sql)) {

		$userID = -1;		

		if (mysqli_num_rows($result) > 0) {
		    // output data of each row
		    while($row = mysqli_fetch_row($result)) {
				$userID = $row[0];
		    }
		} 
		mysqli_close($link);

		//echo $userID;
		return $userID;

	} else {		
		return -2;
		die("Error");
	}
}
//CODIGOS: -1:No encontrado -2:Error en la consulta
?>
