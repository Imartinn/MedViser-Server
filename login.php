<?php
 include 'getUserId.php';
  // get the HTTP method, path and body of the request
  $contents = file_get_contents('php://input');
  $contents = utf8_encode($contents);
  $results = json_decode($contents); 
  $input = json_decode($contents);

  $userID = comprobarCredenciales($input);    

  if($userID > 0) {

  	echo 'Login successful';

  } else {
    
    // connect to the mysql database
    $link = mysqli_connect('localhost', 'root', '', 'dbMedViserData');
    mysqli_set_charset($link,'utf8');

    /* comprobar la conexión */
    if (mysqli_connect_errno()) {
        printf("Falló la conexión: %s\n", mysqli_connect_error());
        exit();
    }

    $user = mysqli_real_escape_string($link, $input->{"user"}); $pass = mysqli_real_escape_string($link, $input->{"pass"});

    if(strlen($user) != 40 || strlen($pass) != 40) {
	  	http_response_code(400);
		die("NOK; Bad parameters");    
	}

    $sql = "INSERT INTO users VALUES(default,'".$user."','".$pass."');";

    if(mysqli_query($link,$sql)) {
    	echo "Register successful";
    } else {
    	if(mysqli_errno($link) == 1062) {
    		http_response_code(400);
    		die("NOK; Mail already in use");
    	}
    	http_response_code(500);
      	die("NOK; Internal server error");        
    }
}
?>