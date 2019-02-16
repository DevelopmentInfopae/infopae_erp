<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../../../db/conexion.php';
$Link = new mysqli($Hostname, $Username, $Password, $Database);
if ($Link->connect_errno) {
  echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
$Link->set_charset("utf8");

//var_dump($_POST);

$semana = $_POST['semana'];

// Se van a buscar el mes y el año a partir de la tabla de planilla semana
$consulta = " select ano, mes, semana from planilla_semanas where semana = '$semana' limit 1 ";
$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
  while($row = $resultado->fetch_assoc()){
    $semanaMes = $row['mes'];
    $semanaAnno = $row['ano'];
  }
}
$semanaAnno = substr($semanaAnno, -2);
$annoMes = $semanaMes.$semanaAnno;












//echo "<br><br><br>";

// Se va a construir el array de alimentos

$despacho = $_POST['despacho'];
$alimentos = Array();

foreach ($_POST as $key => $value) {
  $pos = strpos($key, 'alimento');
  if ($pos === false) {}
  else{
    $alimentos[] = $_POST[$key];
  }
}
//echo "<br>Despacho: ".$despacho."<br>";
//echo "<br>Array de alimentos:<br>";
//var_dump($alimentos);















$consulta = '';







  for ($j=0; $j < count($alimentos) ; $j++) {

    $codigo = $alimentos[$j];
    $indice = 'fechaVencimiento'.$codigo;
    $fecha=$_POST[$indice];
    $fecha = date("Y-m-d",strtotime(str_replace('/','-',$fecha)));




    $indice = 'lote'.$codigo;
    $lote=$_POST[$indice];

    $consulta = $consulta." update productosmovdet$annoMes set FechaVencimiento = '$fecha', Lote = $lote where Documento = 'DES' and Numero = $despacho and CodigoProducto = $codigo; ";
  }


//echo "<br><br><br>".$consulta."<br><br><br>";


//$query  = "SELECT CURRENT_USER();";
//$query .= "SELECT Name FROM City ORDER BY ID LIMIT 20, 5";
//$mysqli->multi_query($query)






// Actualizando el estado, despues de esto no se podra ingresar a modificar.
$consulta = $consulta." update despachos_enc$annoMes set estado = 1 where Tipo_Doc = 'DES' and Num_Doc = $despacho; ";


//echo "<br>".$consulta."<br>";

$Link->multi_query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));



echo "1";
