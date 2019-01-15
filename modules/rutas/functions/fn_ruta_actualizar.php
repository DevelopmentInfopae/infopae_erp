<?php
session_start();
$tablaAnno = $_SESSION['periodoActual'];

$idRuta = 0;
$nombreRuta = "";
$items = array();

$idRuta = $_POST['idRuta'];
$nombreRuta = $_POST['nombreRuta'];
$sedes = $_POST['itemsDespacho'];

require_once '../../../db/conexion.php';
$Link = new mysqli($Hostname, $Username, $Password, $Database);
if ($Link->connect_errno) {
  echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
$Link->set_charset("utf8");

$consulta = " update rutas set nombre = '$nombreRuta' where id = '$idRuta' ";
$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));

$consulta = " delete from rutasedes where IDRUTA = '$idRuta' ";
//echo "<br>Consulta de borrado: ".$consulta;
$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));

$consulta = " insert into rutasedes (idruta, cod_sede) values ";
for ($i=0; $i < count($sedes); $i++) {
  $sede = $sedes[$i];
  if($i > 0){$consulta = $consulta." , "; }
    $consulta = $consulta." ( ";
    $consulta = $consulta." '$idRuta','$sede' ";
    $consulta = $consulta." ) ";
}

//echo "<br><br>".$consulta."<br><br>";

$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
echo '1';
