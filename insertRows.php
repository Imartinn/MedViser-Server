<?php
 
  include 'getUserId.php';
  // get the HTTP method, path and body of the request
  $contents = file_get_contents('php://input');
  $contents = utf8_encode($contents);
  $results = json_decode($contents); 
  $input = json_decode($contents);
  $data = $input->{"data"};  
  echo $data->{"nombre"};  
  //$data = json_decode($contents, true, 1, 0);

  $userID = comprobarCredenciales($input);

  if($userID > 0) {
    
    // connect to the mysql database
    $link = mysqli_connect('localhost', 'root', '', 'dbMedViserData');
    mysqli_set_charset($link,'utf8');

    /* comprobar la conexión */
    if (mysqli_connect_errno()) {
        printf("Falló la conexión: %s\n", mysqli_connect_error());
        exit();
    }

    $wanted = mysqli_real_escape_string($link, $input->{"wanted"});    

    if($wanted == null) {
      http_response_code(400);
      die("NOK; Bad parameters");        
    }

    // create SQL based on HTTP method    
    switch ($wanted) {
      case 'meds':
        $sql = "";
        foreach($data as $json) {
           $sql += "INSERT INTO meds VALUES(default,".$userID.",".mysqli_real_escape_string($link, $json->{'idMed'}).",".mysqli_real_escape_string($link, $json->{'nombre'}).",".mysqli_real_escape_string($link, $json->{'detalles'}).",".mysqli_real_escape_string($link, $json->{'enActivo'})."); "; // you can access your key value like this if result is array
           //echo $json->key; // you can access your key value like this if result is object
        }        
        break;
      case 'tomas':
        $sql = "SELECT idToma FROM tomas ORDER BY idToma DESC LIMIT 1";
        break;
      case 'regs':
        $sql = "SELECT idReg FROM registros ORDER BY idReg DESC LIMIT 1";
        break;
      default:
        http_response_code(400);
        die("NOK; Bad parameters");
        break;
    }
     
    // excecute SQL statement
    if (mysqli_query($link, $sql)) {
      echo "New record created successfully";
    } else {
      http_response_code(500);
      echo "NOK; Error: " . $sql . "<br>" . mysqli_error($link);
    } 
  }else if($userID == -1) {
    http_response_code(403);
    die("NOK; Unauthorized");
  } else if($userID == -2) {
    http_response_code(500);
    die("NOK; Server error");
  }
  /*{"user":"rootrootrootrootrootrootrootrootrootroot","pass":"rootrootrootrootrootrootrootrootrootroot", "wanted":"meds", "data":{"idMed":4, "nombre":"Valid", "detalles":"Muy bueno pa to", "enActivo":1}}*/
?>
 