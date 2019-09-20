<?php
  session_start();
  require_once '../../../db/conexion.php';
  $Link = new mysqli($Hostname, $Username, $Password, $Database);
  if ($Link->connect_errno) { echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error; }
  $Link->set_charset("utf8");

  $unidad = $_POST['unidad'];
  $codigo = $_POST['codigo'];
  $periodoActual = $_SESSION['periodoActual'];
  $consulta = " SELECT
    NombreUnidad$unidad, CantidadUnd$unidad
FROM
    productos$periodoActual
WHERE
    Codigo = $codigo  ";
  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
  if($resultado->num_rows >= 1){
  $row = $resultado->fetch_assoc();
  $nombre = $row['NombreUnidad'.$unidad];
  $cantidad = $row['CantidadUnd'.$unidad];

  }
















echo json_encode(array("cantidad"=>$cantidad, "nombre"=>$nombre));



 ?>
