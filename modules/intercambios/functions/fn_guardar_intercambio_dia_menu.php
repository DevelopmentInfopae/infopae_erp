<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

//var_dump($_POST);

// Respuesta en caso de error
$resultadoAJAX = array(
	"estado" => 2,
	"message" => "Error al hacer el registro.",
);

$query = "";
$menus = $_POST['menu'];
foreach ($menus as $menu) {
	$ordenCiclo = mysqli_real_escape_string($Link, $menu['ordenCiclo']);
	$codigo = mysqli_real_escape_string($Link, $menu['codigo']);
	$query .= " update productos19 set Orden_Ciclo = \"$ordenCiclo\" where Codigo = \"$codigo\"; ";
}
//echo $query;
$result = $Link->multi_query($query) or die ('Update error'. mysqli_error($Link));   
if($result){
	$resultadoAJAX = array(
    	"estado" => 1,
    	"message" => "El registro se ha realizado con éxito.",
  	);
} 
 
echo json_encode($resultadoAJAX);