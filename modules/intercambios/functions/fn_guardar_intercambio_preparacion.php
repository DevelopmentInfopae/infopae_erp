<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$fecha = date('Y-m-d H:i:s');
$carpeta = 'upload/novedades/menu/';
$carpetaFisica = '../../../upload/novedades/menu/';
$nuevoId = 0;

//var_dump($_POST);

$usuario = $_SESSION['id_usuario'];
$mes = '';
$semana = '';
$dia = '';
$tipoComplemento = '';
$grupoEtario = '';
$fechaVencimiento = '';
$observaciones = '';
$rutaArchivo = '';



if(isset($_POST['mes']) && $_POST['mes'] != ''){
  $mes = mysqli_real_escape_string($Link, $_POST['mes']);
}
if(isset($_POST['semana']) && $_POST['semana'] != ''){
  $semana = mysqli_real_escape_string($Link, $_POST['semana']);
}
if(isset($_POST['dia']) && $_POST['dia'] != ''){
  $dia = mysqli_real_escape_string($Link, $_POST['dia']);
}
if(isset($_POST['tipoComplemento']) && $_POST['tipoComplemento'] != ''){
  $tipoComplemento = mysqli_real_escape_string($Link, $_POST['tipoComplemento']);
}
if(isset($_POST['grupoEtario']) && $_POST['grupoEtario'] != ''){
  $grupoEtario = mysqli_real_escape_string($Link, $_POST['grupoEtario']);
}
if(isset($_POST['fechaVencimiento']) && $_POST['fechaVencimiento'] != ''){
  $fechaVencimiento = mysqli_real_escape_string($Link, $_POST['fechaVencimiento']);
  $fechaVencimiento = str_replace('/', '-', $fechaVencimiento);
  $fechaVencimiento = date("Y-m-d", strtotime($fechaVencimiento));
}
if(isset($_POST['observaciones']) && $_POST['observaciones'] != ''){
  $observaciones = mysqli_real_escape_string($Link, $_POST['observaciones']);
}

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

      // Registro del encabezdo de la novedad
      $query = " insert into novedades_menu (mes, semana, dia, menu, tipo_complem, cod_grupo_etario, cod_producto, observaciones, url_archivo, fecha_registro, estado, fecha_vencimiento, id_usuario, tipo_intercambio) values (\"$mes\", \"$semana\", \"$dia\", \"$codigoMenu\", \"$tipoComplemento\", \"$grupoEtario\", \"$codigoMenu\", \"$observaciones\", \"$carpeta\", \"$fecha\", \"1\", \"$fechaVencimiento\", \"$usuario\", \"2\") "; 

      //echo "<br><br>$query<br><br>";
      $result = $Link->query($query) or die ('Insertando novedad'. mysqli_error($Link)); 
      $nuevoId = $Link->insert_id;
  
      /* Tratamiento del archivo */
      if (!file_exists($carpetaFisica)) {
          mkdir($carpetaFisica, 0777);
      }
      if (isset($_FILES["foto"])) {
          $file = $_FILES["foto"];
          $tipo = $file["type"];
          $size = $file["size"];
          $nombre = $file["name"];
          $ruta_provisional = $file["tmp_name"];
          $ext = pathinfo($nombre, PATHINFO_EXTENSION);
          if($nuevoId > 0){
            $nombre = $nuevoId.'.'.$ext;
            $src = $carpetaFisica.$nombre;
            $srcw = $carpeta.$nombre;
            if(move_uploaded_file($ruta_provisional, $src)){
              $query = " update novedades_menu set url_archivo = \"$srcw\" where id = \"$nuevoId\" ";
              // echo "<br><br> $query <br><br>";
              $Link->query($query) or die ('Error actualizando la URL del archivo de novedad de menú'. mysqli_error($Link));
            }
          } 
        
      }
      /* Termina tratamiento del archivo */

      // Registro del detalle de la novedad
      
      // Inserción de los registros originales
      $query = " INSERT INTO novedades_menudet (tipo, id_novedad, cod_producto) SELECT 0 AS tipo, \"$nuevoId\" as novedad, ftd.codigo from fichatecnicadet ftd where ftd.IdFT = \"$idFichaTecnica\"";
      //echo $query;
      $Link->query($query) or die ('Error inserción de registros originales en el detalle de la novedad'. mysqli_error($Link));
    	



    	// Borrar de la tabla de fichatecnicadet los productos que corresponden al IdFT
    	$query = " delete from fichatecnicadet where idFT = $idFichaTecnica ";
    	$result = $Link->query($query) or die ('Delete error'. mysqli_error($Link));   
    	

    	// Hacer el registro de los productos para el IdFT
    	$productos = $_POST['productos'];

    	$query = " insert into fichatecnicadet ( codigo, Componente, Cantidad, UnidadMedida, Costo, idFT, Subtotal, Factor, Estado, Tipo, TipoProducto, PesoBruto, PesoNeto ) values ";

      $queryNovedadDet = " INSERT INTO novedades_menudet (tipo, id_novedad, cod_producto) values ";


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
            $queryNovedadDet .= " , ";
      		}
    		$query .= " ( ";
			$query .= " \"$producto\", \"$productoNombre\", \"$pesoBruto\" , \"$unidad\", \"0,00\", \"$idFichaTecnica\", \"0,00\", \"$factor\", \"0\", \"Preparación\", \"Preparación\", \"$pesoBruto\", \"$pesoNeto\" ";
			$query .= " ) ";

      $queryNovedadDet .= " (\"1\", \"$nuevoId\", \"$producto\" ) ";


			$aux++;
    	}
    	//echo $query;
	    $result = $Link->query($query) or die ('Insert error'. mysqli_error($Link));

      $resultNovedadDet = $Link->query($queryNovedadDet) or die ('Insert error en novedad det'. mysqli_error($Link)); 

      
	    if($result){
	      $resultadoAJAX = array(
	        "estado" => 1,
	        "message" => "El registro se ha realizado con éxito.",
	      );
	    }  
    }
}
echo json_encode($resultadoAJAX);