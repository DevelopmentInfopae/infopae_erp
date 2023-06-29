<?php
	
require_once '../../../db/conexion.php';
// 11 Proveedor
// 12 Empleados
echo "<option value=''>Seleccione uno</option>";
$subtipo = '';
if(isset($_POST['subtipo'])){
	$subtipo = $_POST['subtipo'];
}

if($subtipo == 11 || $subtipo == 12){
	$consulta="";
	if($subtipo == 11){
		$consulta = " select Nitcc as documento, Nombrecomercial as nombre from proveedores ";
	}else if($subtipo == 12){
		$consulta = " select Nitcc as documento, Nombre as nombre from empleados ";
	}

  $Link = new mysqli($Hostname, $Username, $Password, $Database);
  if ($Link->connect_errno) {
    echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }
  $Link->set_charset("utf8");

  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
  if($resultado->num_rows >= 1){
  	while($row = $resultado->fetch_assoc()){
  			$documento = $row['documento'];
  			$nombre = $row['nombre'];
  			echo "<option value='$documento'>$nombre</option>";
  	}
  }
}