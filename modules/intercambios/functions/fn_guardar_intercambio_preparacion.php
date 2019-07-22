<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

//var_dump($_POST);


$codigoMenu = '';
// $codigoMenuOriginal = "";



if(isset($_POST['codigoMenu']) && $_POST['codigoMenu'] != ''){
	$codigoMenu = mysqli_real_escape_string($Link, $_POST['codigoMenu']);
}


// Respuesta en caso de error
$resultadoAJAX = array(
	"estado" => 2,
	"message" => "Error al hacer el registro.",
);

$consultaFichaTecnica = " select * from fichatecnica where Codigo = \"$codigoMenu\" ";

//echo "<br><br>$consultaFichaTecnica<br><br>";

$resultadoFichaTecnica = $Link->query($consultaFichaTecnica);
if ($resultadoFichaTecnica->num_rows > 0) {
	$fichaTecnica = $resultadoFichaTecnica->fetch_assoc();
	$idFichaTecnica = $fichaTecnica['Id'];
    $consultaFichaTecnicaDet = "select * from fichatecnicadet where IdFT = ".$idFichaTecnica;
    
    //echo "<br><br>$consultaFichaTecnicaDet<br><br>";

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
      		$unidad = "u";
      		$pesoBruto = "0,00";
      		$pesoNeto = "0,00";
      		$factor = "0.00000000"; 
      		if($aux > 0){
        		$query .= " , ";
      		}
    		$query .= " ( ";
			$query .= " \"$producto\", \"$productoNombre\", \"$pesoBruto\" , \"$unidad\", \"0,00\", \"$idFichaTecnica\", \"0,00\", \"$factor\", \"0\", \"Preparación\", \"Preparación\", \"$pesoBruto\", \"$pesoNeto\" ";
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