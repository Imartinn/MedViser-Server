<?php
 
  include 'getUserId.php';
  // get the HTTP method, path and body of the request
  $contents = file_get_contents('php://input');
  $contents = utf8_encode($contents);
  $results = json_decode($contents); 
  $input = json_decode($contents);
  $data = json_decode($input->{"data"});  
  //echo $data->{"nombre"};  
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

    $sql = "";
    // create SQL based on HTTP method    
    switch ($wanted) {
      case 'meds':        
        foreach($data as $json) {          
          $sql = $sql."INSERT INTO meds VALUES(".$userID.",".mysqli_real_escape_string($link, $json->{'idMed'}).",'".mysqli_real_escape_string($link, $json->{'nombre'})."','".mysqli_real_escape_string($link, $json->{'detalles'})."',".mysqli_real_escape_string($link, $json->{'enActivo'})."); "; // you can access your key value like this if result 
           //echo $json->key; // you can access your key value like this if result is object
        }        
        break;
      case 'tomas':
        $sql = $sql."INSERT INTO tomas VALUES(".$userID.",".mysqli_real_escape_string($link, $json->{'idToma'}).",".mysqli_real_escape_string($link, $json->{'idMed'}).",".mysqli_real_escape_string($link, $json->{'lunes'}).",".mysqli_real_escape_string($link, $json->{'martes'}).",".mysqli_real_escape_string($link, $json->{'miercoles'}).",".mysqli_real_escape_string($link, $json->{'jueves'}).",".mysqli_real_escape_string($link, $json->{'viernes'}).",".mysqli_real_escape_string($link, $json->{'sabado'}).",".mysqli_real_escape_string($link, $json->{'domingo'}).",'".mysqli_real_escape_string($link, $json->{'detalles'})."','".mysqli_real_escape_string($link, $json->{'hora'})."'); ";
        break;
      case 'regs':
        $sql = $sql."INSERT INTO registros VALUES(default,".$userID.",".mysqli_real_escape_string($link, $json->{'idReg'}).",".mysqli_real_escape_string($link, $json->{'idMed'}).",".mysqli_real_escape_string($link, $json->{'idToma'}).",'".mysqli_real_escape_string($link, $json->{'horaToma'})."',".mysqli_real_escape_string($link, $json->{'fechaRegistro'}).",".mysqli_real_escape_string($link, $json->{'estadoToma'})."); ";
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
  /*{"user":"rootrootrootrootrootrootrootrootrootroot","pass":"rootrootrootrootrootrootrootrootrootroot", "wanted":"meds", "data":{"idMed":4, "nombre":"Valid", "detalles":"Muy bueno pa to", "enActivo":1}}

  {"user":"rootrootrootrootrootrootrootrootrootroot","pass":"rootrootrootrootrootrootrootrootrootroot", "wanted":"meds", "data":{"idMed":3,"nombre":"Lora", "detalles":"Good", "enActivo":1},{"idMed":4,"nombre":"Norepinefrina", "detalles":"WAKE UP", "enActivo":1} }*/
?>
 