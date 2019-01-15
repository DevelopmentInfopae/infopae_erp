<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

//var_dump($_POST);
//echo "<br><br><br>";
//var_dump($_SESSION);

$consulta = '';
$consecutivos = $_SESSION['consecutivos'];
$complementosCantidades = $_SESSION['complementosCantidades'];
var_dump($complementosCantidades);




for ($i=0; $i < count($consecutivos) ; $i++) {
  $consecutivo = $consecutivos[$i];
  for ($j=0; $j < count($complementosCantidades) ; $j++) {
    $complemento = $complementosCantidades[$j];
    $codigo = $complemento['codigo'];
    $indice = 'fechaVencimiento'.$complemento['codigo'];
    $fecha=$_POST[$indice];
    $fecha = date("Y-m-d",strtotime(str_replace('/','-',$fecha)));




    $indice = 'lote'.$complemento['codigo'];
    $lote=$_POST[$indice];

    $consulta = $consulta." update productosmovdet16 set FechaVencimiento = '$fecha', Lote = $lote where Documento = 'DES' and Numero = $consecutivo and CodigoProducto = $codigo; ";
  }
}

//echo "<br><br><br>".$consulta."<br><br><br>";

/*
$query  = "SELECT CURRENT_USER();";
$query .= "SELECT Name FROM City ORDER BY ID LIMIT 20, 5";
$mysqli->multi_query($query)
*/
require_once 'db/conexion.php';

$Link = new mysqli($Hostname, $Username, $Password, $Database);
if ($Link->connect_errno) {
  echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
$Link->set_charset("utf8");

$Link->multi_query($consulta);



?>
