<?php
  //var_dump($_POST);

  $semana = $_POST['semana'];

  // require_once 'autenticacion.php';
  require_once 'db/conexion.php';

  $Link = new mysqli($Hostname, $Username, $Password, $Database);
  if ($Link->connect_errno) {
    echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }
  $Link->set_charset("utf8");

  $consulta = " select * from planilla_semanas where SEMANA = $semana ";
  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
  if($resultado->num_rows >= 1){
    $aux = 0;
    while($row = $resultado->fetch_assoc()) {
      if($aux == 0){
        $mes = $row['MES'];
      }

      if($row['MES'] != $mes){
        echo ' ';
        mesEnLetras($mes);
        echo ', ';
        $mes = $row['MES'];
      }
      else{
        if($aux != 0){
          echo ", ";
        }
      }

      echo $row['DIA'];


      $aux++;
    }// Termina el while
  }//Termina el if que valida que si existan resultados
  echo ' ';
  mesEnLetras($mes);


  function mesEnLetras($mes){
    switch ($mes) {
      case '01':
        echo 'Enero';
        break;
      case '01':
        echo 'Enero';
        break;
      case '02':
        echo 'Febrero';
        break;
      case '03':
        echo 'Marzo';
        break;
      case '04':
        echo 'Abril';
        break;
      case '05':
        echo 'Mayo';
        break;
      case '06':
        echo 'Junio';
        break;
      case '07':
        echo 'Julio';
        break;
      case '08':
        echo 'Agosto';
        break;
      case '09':
        echo 'Septiembre';
        break;
      case '10':
        echo 'Octubre';
        break;
      case '11':
        echo 'Nomviembre';
        break;
      case '12':
        echo 'Diciembre';
        break;

      default:
        # code...
        break;
    }
  }
