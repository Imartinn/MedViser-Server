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
    $last = mysqli_real_escape_string($link, $input->{"last"});

    if($wanted == null || $last == null) {
      http_response_code(400);
      die("NOK; Bad parameters");        
    }

    // create SQL based on HTTP method    
    switch ($wanted) {
      case 'meds':
        $sql = "select * from meds WHERE idUser = ".$userID." AND idMed > ".$last.";";
        break;
      case 'tomas':
        $sql = "select * from tomas WHERE idUser = ".$userID." AND idToma > ".$last.";";
        break;
      case 'registros':
        $sql = "select * from registros WHERE idUser = ".$userID." AND idReg > ".$last.";";
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
        switch ($wanted) {
          case 'meds':
            //while($row = mysqli_fetch_row($result)) {
          while($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {
              $row1['idMed'] = $row['idMed'];
              $row1['nombre'] = $row['nombre'];
              $row1['detalles'] = $row['detalles'];
              $row1['enActivo'] = $row['enActivo'];
              array_push($rows,$row1);
            }
            break;
        case 'tomas':
          while($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {
            $row1['idToma'] = $row['idToma'];
            $row1['idMed'] = $row['idMed'];
            $row1['lunes'] = $row['lunes'];
            $row1['martes'] = $row['martes'];
            $row1['miercoles'] = $row['miercoles'];
            $row1['jueves'] = $row['jueves'];
            $row1['viernes'] = $row['viernes'];
            $row1['sabado'] = $row['sabado'];
            $row1['domingo'] = $row['domingo'];            
            $row1['detalles'] = $row['detalles'];
            $row1['hora'] = $row['hora'];
            $row1['enActivo'] = $row['enActivo'];
            array_push($rows,$row1);
          }
          break;
        case 'registros':
          while($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {
            $row1['idReg'] = $row['idReg'];
            $row1['idMed'] = $row['idMed'];
            $row1['idToma'] = $row['idToma'];
            $row1['horaToma'] = $row['horaToma'];
            $row1['fechaRegistro'] = $row['fechaRegistro'];
            $row1['estadoToma'] = $row['estadoToma'];
            array_push($rows,$row1);
          }
          break;
        default:
          http_response_code(400);
          die("NOK; Bad parameters");
          break;
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
}
?>