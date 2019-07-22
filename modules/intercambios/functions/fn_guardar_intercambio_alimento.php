<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$preparacion = '';
// $preparacionOriginal = "";

if(isset($_POST['preparacion']) && $_POST['preparacion'] != ''){
	$preparacion = mysqli_real_escape_string($Link, $_POST['preparacion']);
}

//var_dump($_POST);

// Respuesta en caso de error
$resultadoAJAX = array(
	"estado" => 2,
	"message" => "Error al hacer el registro.",
);

$consultaFichaTecnica = " select * from fichatecnica where Codigo = \"$preparacion\" ";
//echo $consultaFichaTecnica;
$resultadoFichaTecnica = $Link->query($consultaFichaTecnica);
if ($resultadoFichaTecnica->num_rows > 0) {
	$fichaTecnica = $resultadoFichaTecnica->fetch_assoc();
	$idFichaTecnica = $fichaTecnica['Id'];
    $consultaFichaTecnicaDet = "select * from fichatecnicadet where IdFT = ".$idFichaTecnica;
    //echo $consultaFichaTecnicaDet;
    $resultadoFichaTecnicaDet = $Link->query($consultaFichaTecnicaDet);
    if ($resultadoFichaTecnicaDet->num_rows > 0) {
    	

    	// Borrar de la tabla de fichatecnicadet los productos que corresponden al IdFT
    	$query = " delete from fichatecnicadet where idFT = $idFichaTecnica ";
    	$result = $Link->query($query) or die ('Delete error'. mysqli_error($Link));   
    	

    	// Hacer el registro de los productos para el IdFT
    	$productos = $_POST['productos'];
    	$query = " insert into fichatecnicadet ( codigo, Componente, Cantidad, UnidadMedida, Costo, idFT, Subtotal, Factor, Estado, Tipo, TipoProducto, PesoBruto, PesoNeto ) values ";
    	$aux = 0;
    	foreach ($productos as $productoElement) {
    		//var_dump($productoElement);
      		$producto = mysqli_real_escape_string($Link, $productoElement['producto']);
      		$productoNombre = mysqli_real_escape_string($Link, $productoElement['productoNombre']);
      		$unidad = mysqli_real_escape_string($Link, $productoElement['unidad']);
      		$pesoBruto = mysqli_real_escape_string($Link, $productoElement['pesoBruto']);
      		$pesoNeto = mysqli_real_escape_string($Link, $productoElement['pesoNeto']);
      		$factor = 1 / floatval($pesoBruto); 
      		if($aux > 0){
        		$query .= " , ";
      		}
    		$query .= " ( ";
			$query .= " \"$producto\", \"$productoNombre\", \"$pesoBruto\" , \"$unidad\", \"0,00\", \"$idFichaTecnica\", \"0,00\", \"$factor\", \"0\", \"Alimento\", \"Alimento\", \"$pesoBruto\", \"$pesoNeto\" ";
			$query .= " ) ";
			$aux++;
    	}
    	//echo $query;
	    $result = $Link->query($query) or die ('Insert error'. mysqli_error($Link));   
	    if($result){
	      $resultadoAJAX = array(
	        "estado" => 1,
	        "message" => "El registro se ha realizado con éxito.",
	      );
	    }  
    }
}
echo json_encode($resultadoAJAX);