<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$periodoActual = $_SESSION['periodoActual'];
$bandera = 0;
$fila = 0;
$date = new DateTime();
$anno = $date->format('Y');
$mes = $date->format('m');
$dia = $date->format('d');

$registrosMySQL = array();
$consulta = " SELECT b.usr_dispositivo_id from biometria_reg b where YEAR(b.fecha) = $anno and MONTH(b.fecha) = $mes and day(b.fecha) = $dia  ";
$resultado = $Link->query($consulta) or die ('No se pudieron cargar los muunicipios. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	while($row = $resultado->fetch_assoc()){
		$registrosMySQL[] = intval($row["usr_dispositivo_id"]);
	}
}
$registrosMySQL = implode(",",$registrosMySQL);

$serverName = "179.12.72.70";
$databasename = "infopae_lectores";
$user = 'sa';
$pass = 'Sylopez18';

$connection_string = "DRIVER={SQL Server};SERVER=$serverName;DATABASE=$databasename";
$connection = odbc_connect( $connection_string, $user, $pass );
if ($connection) {
	echo "conexion exitosa";
}else {
	echo "Error en conexion n";
	die(print_r(sqlsvr_errors(), true));
}
$sql = " select c.Sensorid, c.Userid, CONVERT(varchar, c.CheckTime,120) from Checkinout c where YEAR(c.CheckTime) = $anno and MONTH(c.CheckTime) = $mes and day(c.CheckTime) = $dia ";
if($registrosMySQL != ''){
	$sql .= " and c.Userid in ($registrosMySQL) ";
}
// exit(var_dump($sql));
$result=odbc_exec($connection,$sql);
// exit(var_dump($result));
$indice = 0;
$registrosSQLServer = array();
while(odbc_fetch_row($result)){
	$row = odbc_fetch_array ( $result , $indice  ) ;
	$values = '("';
	$values .= implode('","', $row);
	$values .= '")';
	$registrosSQLServer[] = $values;
	$indice++;
}
odbc_close($connection);
// exit(var_dump($registrosSQLServer));
function valores($n){
	return($n);
}
$columns = "dispositivo_id, usr_dispositivo_id, fecha";
$values  = implode(",", $registrosSQLServer);
// exit(var_dump($values));
if($values != ''){
	$consulta = 'INSERT INTO biometria_reg ('.$columns.') VALUES '.$values;
	$resultado = $Link->query($consulta) or die ('Error en la inserción en MySQL'. mysqli_error($Link));
}

if($bandera == 0){
	$resultadoAJAX = array(
		"estado" => 1,
		"mensaje" => "Se ha cargado con exito.",
		"fila" => $fila
	);
}else{
	$resultadoAJAX = array(
		"estado" => 0,
		"mensaje" => "Se ha cargado con exito."
	);
}
echo json_encode($resultadoAJAX);