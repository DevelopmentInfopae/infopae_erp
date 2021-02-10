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

// $anno = '2020';
// $mes = '3';
// $dia = '2';

$registrosMySQL = array();
$consulta = " SELECT b.Logid from biometria_reg b where YEAR(b.fecha) = $anno and MONTH(b.fecha) = $mes and day(b.fecha) = $dia  ";
$resultado = $Link->query($consulta) or die ('No se pudieron cargar los muunicipios. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	while($row = $resultado->fetch_assoc()){
		$registrosMySQL[] = intval($row["Logid"]);
	}
}
$registrosMySQL = implode(",",$registrosMySQL);
//var_dump($registrosMySQL);


$serverName = "179.12.227.88";
$databasename = "infopae_lectores";
$user = 'sa';
$pass = 'Sylopez18';


$connection_string = "DRIVER={SQL Server};SERVER=$serverName;DATABASE=$databasename";
$connection = odbc_connect( $connection_string, $user, $pass );
if($connection){
	//echo "conexión exitosa";
}else{
	//echo "fallo en la conexión";
}

//var_dump($registrosMySQL);
$sql = " select c.Sensorid, c.Userid, CONVERT(varchar, c.CheckTime,120), c.Logid from Checkinout c where YEAR(c.CheckTime) = $anno and MONTH(c.CheckTime) = $mes and day(c.CheckTime) = $dia ";
if($registrosMySQL != ''){
	$sql .= " and c.Logid not in ($registrosMySQL) ";
}
//echo "<br><br>$sql<br><br>";


$result=odbc_exec($connection,$sql);
$indice = 0;
$registrosSQLServer = array();
while(odbc_fetch_row($result)){
	$row = odbc_fetch_array ( $result , $indice  ) ;
	$values = '("';
	$values .= implode('","', $row);
	$values .= '")';

	//$values = "($values)";
	//var_dump($values);
	//$values .= ")";
	$registrosSQLServer[] = $values;
	$indice++;
}
odbc_close($connection);
//var_dump($registrosSQLServer);


function valores($n){
	return($n);
}
$columns = "dispositivo_id, usr_dispositivo_id, fecha, Logid";
// $escaped_values = array_values($registrosSQLServer);
// $escaped_values = array_map("valores", $escaped_values);


$values  = implode(",", $registrosSQLServer);
//var_dump($values);


if($values != ''){
	$consulta = 'INSERT INTO biometria_reg ('.$columns.') VALUES '.$values;
	//echo "<br><br>$consulta<br><br>";
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