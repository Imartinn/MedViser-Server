<?php
 
  include 'getUserId.php';
  // get the HTTP method, path and body of the request
  $contents = file_get_contents('php://input');
  $contents = utf8_encode($contents);
  $results = json_decode($contents); 
  $input = json_decode($contents);

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
        $sql = "SELECT idMed FROM meds ORDER BY idMed DESC LIMIT 1";
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
    if($result = mysqli_query($link,$sql)) {
      $rows = array();
      if (mysqli_num_rows($result) > 0) {
        // output data of each row
        while($row = mysqli_fetch_row($result)) {
          $rows[] = $row;
        }
      } 
      mysqli_close($link);
      echo json_encode($rows);
    } else {
      http_response_code(500);
      die("NOK; Database error");  
    }
  }else if($userID == -1) {
    http_response_code(403);
    die("NOK; Unauthorized");
  } else if($userID == -2) {
    http_response_code(500);
    die("NOK; Server error");
  }
?>
