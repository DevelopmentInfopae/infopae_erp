<?php
session_start();
//var_dump($_POST);

//Parametros de fecha para saber que tablas afectar
date_default_timezone_set('America/Bogota');
$mes =  date('m');
$anno = date('y');
//echo '<br> Año actual: '.$anno.' Mes actual: '.$mes.'<br>';


$idMovimiento = $_POST['idMovimiento'];

require_once '../../../db/conexion.php';
$Link = new mysqli($Hostname, $Username, $Password, $Database);
if ($Link->connect_errno) {
  echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
$Link->set_charset("utf8");

$documento = $_POST['documento'];

$consulta = " select Tipo from documentos where Id = $documento ";
$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
  $row = $resultado->fetch_assoc();
  $tipoDocumento = $row['Tipo'];
  //echo "<br><br>".$tipoDocumento."<br><br>";
}

$tipo = $_POST['tipo'];
$consulta = " SELECT Movimiento FROM tipomovimiento where Id = $tipo  ";
$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
  $row = $resultado->fetch_assoc();
  $tipoMovimiento = $row['Movimiento'];
  //echo "<br><br>".$tipoMovimiento."<br><br>";
}

$documento = $_POST['documento'];

if (isset($_POST['bodegaOrigen']) && $_POST['bodegaOrigen'] != '' ) {
  $bodegaOrigen = $_POST['bodegaOrigen'];
}else{
  $bodegaOrigen = 0;
}

if (isset($_POST['bodegaDestino']) && $_POST['bodegaDestino'] != '' ) {
  $bodegaDestino = $_POST['bodegaDestino'];
}else{
  $bodegaDestino = 0;
}

$nombre = $_POST['nombre'];
$nitcc = $_POST['nitcc'];
$concepto = $_POST['concepto'];
$valorTotal = $_POST['valorTotal'];
if(isset($_POST['aprobado']) && $_POST['aprobado'] == '1' ){ $aprobado = 1; }else{ $aprobado = 0; }
$fecha = $_POST['fecha'];
$fecha = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $fecha)));
//echo "<br><br>".$fecha."<br><br>";
$tipoTransporte = $_POST['tipoTransporte'];
$placa = $_POST['placa'];
$conductor = $_POST['conductor'];

$NombreResponsable = $_SESSION['usuario'];
$LoginResponsable = $_SESSION['login'];
$consecutivo= $_POST['numero'];

$consulta = " update productosmov$mes$anno set
Documento = '$tipoDocumento',
Numero = '$consecutivo',
Tipo = '$tipoMovimiento',
BodegaOrigen = '$bodegaOrigen',
bodegaDestino = '$bodegaDestino',
Nombre = '$nombre',
Nitcc = '$nitcc',
Concepto = '$concepto',
ValorTotal = '$valorTotal',
Aprobado = '$aprobado',
FechaMYSQL = '$fecha',
TipoTransporte = '$tipoTransporte',
Placa = '$placa',
ResponsableRecibe = '$conductor',
NombreResponsable = '$NombreResponsable',
LoginResponsable = '$LoginResponsable'
where Id = '$idMovimiento' ";

$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));


//borrando los detalles de movimiento
$consulta = " delete from productosmovdet$mes$anno where Documento = '$tipoDocumento' and Numero = '$consecutivo' ";
$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));

//Insertando de nuevo los items
$items = $_POST['items'];
//echo "<br>Items: ".$items."<br>";


$item = 0;

for ($i=1; $i <= $items ; $i++) {
  if ( isset($_POST['codigo'.$i]) && $_POST['codigo'.$i] > 0 ) {
    //echo "<br>Encontro codigo<br>";
    $item++;
    $codigo = $_POST['codigo'.$i];
    $descripcion = $_POST['descripcion'.$i];




    //$bodegaOrigen = $_POST['bodegaOrigen'.$i];
    //$bodegaDestino = $_POST['bodegaDestino'.$i];


    if (isset($_POST['bodegaOrigen'.$i])  && $_POST['bodegaOrigen'.$i] != '') {
      $bodegaOrigen = $_POST['bodegaOrigen'.$i];
    }else{
      $bodegaOrigen = 0;
    }

    if (isset($_POST['bodegaDestino'.$i]) && $_POST['bodegaDestino'.$i] != '') {
      $bodegaDestino = $_POST['bodegaDestino'.$i];
    }else{
      $bodegaDestino = 0;
    }







    $factor = $_POST['factor'.$i];
    if($factor <= 0){
      $factor = 1;
    }








    $unidades = $_POST['unidades'.$i];
    $unidadesnm = $_POST['unidadesnm'.$i];
    $Lote = $_POST['lote'.$i];
    $fechaV = $_POST['fechaV'.$i];
    $fechaV = date('Y-m-d', strtotime(str_replace('/', '-', $fechaV)));
    $costoUnitario = $_POST['costoUnitario'.$i];
    $costoTotal = $_POST['costoTotal'.$i];

    if($bodegaOrigen == ''){
      $bodegaOrigen = 0;
    }

    if($bodegaDestino == ''){
      $bodegaDestino = 0;
    }



    $costoUnitario = $costoUnitario/$factor;
    $cantunidades = $unidades*$factor;



    $consulta = " insert into productosmovdet$mes$anno (Documento, Numero, Item, CodigoProducto,Descripcion, Cantidad, CostoUnitario, BodegaOrigen, BodegaDestino, Umedida, CantUmedida, CostoTotal, Lote, FechaVencimiento,factor) values ('$tipoDocumento', '$consecutivo', '$item', '$codigo', '$descripcion', '$unidades', '$costoUnitario', '$bodegaOrigen', '$bodegaDestino', '$unidadesnm', '$cantunidades', '$costoTotal', '$Lote', '$fechaV','$factor') ";
    //echo "<br><br>".$consulta."<br><br>";
    $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));

    $consulta = " update productosmensual$mes$anno set CostoUnitario = '$costoUnitario' where CodigoProducto = '$codigo' ";
    $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
  }
}

echo '1';
?>
