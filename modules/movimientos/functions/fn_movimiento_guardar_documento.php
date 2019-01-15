<?php
session_start();
//var_dump($_POST);

//Parametros de fecha para saber que tablas afectar
date_default_timezone_set('America/Bogota');
$mes =  date('m');
$anno = date('y');
//echo '<br> Año actual: '.$anno.' Mes actual: '.$mes.'<br>';

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




if (isset($_POST['bodegaOrigen']) && $_POST['bodegaOrigen']!= '') {
  $bodegaOrigen = $_POST['bodegaOrigen'];
}else{
  $bodegaOrigen = 0;
}

if (isset($_POST['bodegaDestino']) && $_POST['bodegaDestino'] != '') {
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

// Se va a buscar el numero de consecutivo en la tabla de documentos,
// dependiendo del tipo de documento, lo tomo e incremento en uno el
// valor de la tabla documentos.
$consulta = " select Consecutivo from documentos where Id = $documento ";
$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));

if($resultado->num_rows >= 1){
  $row = $resultado->fetch_assoc();
  $consecutivo = $row['Consecutivo'];
}

$consecutivoSiguiente = $consecutivo+1;
$consulta = " update documentos set Consecutivo = $consecutivoSiguiente where Id = $documento ";
//echo "<br>".$consulta."<br>";
$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));









// Productos Mov
$consulta = " show tables like 'productosmov$mes$anno' ";
$result = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
$existe = $result->num_rows;
if($existe <= 0){
  $consulta = " CREATE TABLE `productosmov$mes$anno` ( `Documento` varchar(10) DEFAULT '', `Numero` int(10) unsigned DEFAULT '0', `Tipo` varchar(100) DEFAULT '', `FechaDoc` varchar(45) DEFAULT '', `BodegaOrigen` bigint(20) unsigned DEFAULT '0', `BodegaDestino` bigint(20) unsigned DEFAULT '0', `Nombre` varchar(200) DEFAULT '', `Nitcc` varchar(20) DEFAULT '', `Concepto` text, `ValorTotal` decimal(20,2) DEFAULT '0.00', `Aprobado` tinyint(1) DEFAULT '0', `NombreResponsable` varchar(60) DEFAULT '', `LoginResponsable` varchar(30) DEFAULT '', `GeneraCompra` tinyint(1) DEFAULT '0', `DocOrigen` varchar(10) DEFAULT '', `NumDocOrigen` int(10) unsigned DEFAULT '0', `NombreRED` varchar(45) DEFAULT '', `Id` int(10) unsigned NOT NULL AUTO_INCREMENT, `FechaMYSQL` datetime DEFAULT '0000-00-00 00:00:00', `Anulado` tinyint(1) DEFAULT '0', `TipoTransporte` varchar(50) NOT NULL DEFAULT '', `Placa` varchar(10) NOT NULL DEFAULT '', `ResponsableRecibe` varchar(45) NOT NULL DEFAULT '', `NumCompra` int(10) unsigned DEFAULT '0', PRIMARY KEY (`Id`) ) ";
  $result = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
}

// Productos Mov Det
$consulta = " show tables like 'productosmovdet$mes$anno' ";
$result = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
$existe = $result->num_rows;
if($existe <= 0){
  $consulta = " CREATE TABLE `productosmovdet$mes$anno` ( `Documento` varchar(10) DEFAULT '', `Numero` int(10) DEFAULT '0', `Item` int(10) unsigned DEFAULT '0', `CodigoProducto` varchar(20) DEFAULT '', `Descripcion` text NOT NULL, `Cantidad` decimal(28,8) DEFAULT '0.00000000', `CantFacturada` decimal(28,8) DEFAULT '0.00000000', `ValorUnitario` decimal(18,2) DEFAULT '0.00', `CuentaInventario` varchar(30) DEFAULT '', `CuentaContraPartida` varchar(30) DEFAULT '', `Facturado` tinyint(1) DEFAULT '0', `CentroCosto` varchar(10) DEFAULT '', `BodegaOrigen` bigint(20) unsigned DEFAULT '0', `BodegaDestino` bigint(20) unsigned DEFAULT '0', `CantBodOrg` decimal(28,8) DEFAULT '0.00000000', `CantBodDest` decimal(28,8) DEFAULT '0.00000000', `Id` int(10) unsigned NOT NULL AUTO_INCREMENT, `Talla` varchar(5) DEFAULT '', `Color` varchar(45) DEFAULT '', `CostoUnitario` decimal(10,2) DEFAULT '0.00', `NombreRED` varchar(45) DEFAULT '', `Umedida` varchar(255) DEFAULT '', `CantUmedida` decimal(20,4) DEFAULT '0.0000', `Factor` decimal(28,8) DEFAULT '0.00000000', `Id_Usuario` int(10) unsigned DEFAULT '0', `CostoTotal` decimal(20,2) DEFAULT '0.00', `Lote` varchar(45) NOT NULL DEFAULT '', `FechaVencimiento` date DEFAULT NULL, PRIMARY KEY (`Id`) ) ";
  $result = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
}




$consulta = " insert into productosmov$mes$anno (Documento, Numero, Tipo, BodegaOrigen, bodegaDestino, Nombre, Nitcc, Concepto, ValorTotal, Aprobado, FechaMYSQL, TipoTransporte, Placa, ResponsableRecibe, NombreResponsable, LoginResponsable) values ('$tipoDocumento', '$consecutivo', '$tipoMovimiento', '$bodegaOrigen', '$bodegaDestino', '$nombre', '$nitcc', '$concepto', '$valorTotal', '$aprobado', '$fecha', '$tipoTransporte', '$placa', '$conductor', '$NombreResponsable', '$LoginResponsable') ";
//echo "<br><br>".$consulta."<br><br>";
$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
//$ultimoMovimiento = $Link->insert_id;
//echo "<br><br>".$ultimoMovimiento."<br><br>";

$items = $_POST['items'];
$item = 0;















for ($i=1; $i <= $items ; $i++) {
  if ( isset($_POST['codigo'.$i]) && $_POST['codigo'.$i] > 0 ) {
    $item++;
    $codigo = $_POST['codigo'.$i];
    $descripcion = $_POST['descripcion'.$i];




    //$bodegaOrigen = $_POST['bodegaOrigen'.$i];
    //$bodegaDestino = $_POST['bodegaDestino'.$i];


    if (isset($_POST['bodegaOrigen'.$i]) && $_POST['bodegaOrigen'.$i] != '') {
      $bodegaOrigen = $_POST['bodegaOrigen'.$i];
    }else{
      $bodegaOrigen = 0;
    }

    if (isset($_POST['bodegaDestino'.$i]) && $_POST['bodegaDestino'.$i] != '' ) {
      $bodegaDestino = $_POST['bodegaDestino'.$i];
    }else{
      $bodegaDestino = 0;
    }








    $unidades = $_POST['unidades'.$i];
    $unidadesnm = $_POST['unidadesnm'.$i];
    $factor = $_POST['factor'.$i];
    if($factor <= 0){
      $factor = 1;
    }
    $Lote = $_POST['lote'.$i];
    $fechaV = $_POST['fechaV'.$i];
    $fechaV = date('Y-m-d', strtotime(str_replace('/', '-', $fechaV)));
    $costoUnitario = $_POST['costoUnitario'.$i];
    $costoTotal = $_POST['costoTotal'.$i];

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
