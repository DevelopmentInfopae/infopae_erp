<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

// Respuesta en caso de error
$resultadoAJAX = array(
	"steate" => 2,
	"message" => "Error al hacer el registro.",
);
$idFichaTecnica = '';
$idIntercambio = '';
$tipoIntercambio = '';
$codigoIntercambio = '';

if(isset($_POST['idIntercambio']) && $_POST['idIntercambio'] != ''){
	$idIntercambio = mysqli_real_escape_string($Link, $_POST['idIntercambio']);
}

if(isset($_POST['tipoIntercambio']) && $_POST['tipoIntercambio'] != ''){
	$tipoIntercambio = mysqli_real_escape_string($Link, $_POST['tipoIntercambio']);
}

if(isset($_POST['codigoIntercambio']) && $_POST['codigoIntercambio'] != ''){
	$codigoIntercambio = mysqli_real_escape_string($Link, $_POST['codigoIntercambio']);
}

// Cosulta del Id de la ficha técnica
$consultaFichaTecnica = " select * from fichatecnica where Codigo = \"$codigoIntercambio\" ";
//echo $consultaFichaTecnica;
$resultadoFichaTecnica = $Link->query($consultaFichaTecnica);
if ($resultadoFichaTecnica->num_rows > 0) {
	$fichaTecnica = $resultadoFichaTecnica->fetch_assoc();
	$idFichaTecnica = $fichaTecnica['Id'];
}

// var_dump($_POST);
// echo $idFichaTecnica;

if($tipoIntercambio == 1){

	// Borrar de la tabla de fichatecnicadet los productos que corresponden al IdFT
	$query = " delete from fichatecnicadet where idFT = $idFichaTecnica ";
	$result = $Link->query($query) or die ('Delete error'. mysqli_error($Link));


	// Insertando a partir de la información de la tabla de novedades det
	$query = "INSERT INTO fichatecnicadet (codigo, Componente, Cantidad, UnidadMedida, Costo, IdFT, Subtotal, Factor, Estado, Tipo, TipoProducto, PesoBruto, PesoNeto) SELECT nmd.cod_producto AS codigo, p.Descripcion AS Componente, nmd.pesobruto AS Cantidad, nmd.unidadmedida AS UnidadMedida, \"0,00\" AS Costo, \"$idFichaTecnica\" AS IdFT, \"0,00\" AS Subtotal, (1/nmd.pesobruto) AS Factor, \"0\" AS Estado, \"Alimento\" AS Tipo, \"Alimento\" AS TipoProducto, nmd.pesobruto AS PesoBruto, nmd.pesoneto AS PesoNeto FROM novedades_menudet nmd LEFT JOIN productos19 p ON p.Codigo = nmd.cod_producto WHERE nmd.id_novedad = $idIntercambio AND nmd.tipo = 0 ";
	$result = $Link->query($query) or die ('Delete error'. mysqli_error($Link));
	
	// Cambiando el estado de la novedad
	$query = " update novedades_menu set estado = 0 where id = $idIntercambio ";
	$result = $Link->query($query) or die ('Update error'. mysqli_error($Link)); 

	if($result){
		$resultadoAJAX = array(
			"state" => 1,
			"message" => "El registro se ha realizado con éxito.",
		);
	}	

}
	
else if($tipoIntercambio == 2){

	// Borrar de la tabla de fichatecnicadet los productos que corresponden al IdFT
	$query = " delete from fichatecnicadet where idFT = $idFichaTecnica ";
	$result = $Link->query($query) or die ('Delete error'. mysqli_error($Link));


	// Insertando a partir de la información de la tabla de novedades det
	$query = " 	INSERT INTO fichatecnicadet (codigo, Componente, Cantidad, UnidadMedida, Costo, IdFT, Subtotal, Factor, Estado, Tipo, TipoProducto, PesoBruto, PesoNeto) SELECT nmd.cod_producto AS codigo, p.Descripcion AS Componente, nmd.pesobruto AS Cantidad, \"u\" AS UnidadMedida, \"0,00\" AS Costo, \"$idFichaTecnica\" AS IdFT, \"0,00\" AS Subtotal, \"0.00000000\" AS Factor, \"0\" AS Estado, \"Preparacion\" AS Tipo, \"Preparacion\" AS TipoProducto, nmd.pesobruto AS PesoBruto, nmd.pesoneto AS PesoNeto FROM novedades_menudet nmd LEFT JOIN productos19 p ON p.Codigo = nmd.cod_producto WHERE nmd.id_novedad = $idIntercambio AND nmd.tipo = 0 ORDER BY nmd.id asc ";

	$result = $Link->query($query) or die ('Delete error'. mysqli_error($Link));
	
	// Cambiando el estado de la novedad
	$query = " update novedades_menu set estado = 0 where id = $idIntercambio ";
	$result = $Link->query($query) or die ('Update error'. mysqli_error($Link)); 

	if($result){
		$resultadoAJAX = array(
			"state" => 1,
			"message" => "El registro se ha realizado con éxito.",
		);
	}

}

else if($tipoIntercambio == 3){
	// var_dump($_POST);
	// echo $idFichaTecnica;
	$query = " SELECT nmd.cod_producto, nmd.orden_ciclo from novedades_menudet nmd WHERE nmd.tipo = 0 AND nmd.id_novedad = $idIntercambio ORDER BY nmd.id ";
	$result = $Link->query($query);
	if ($result->num_rows > 0) {
		$query = "";
		while($row = $result->fetch_assoc()){
			$codProducto = $row['cod_producto'];
			$ordenCiclo = $row['orden_ciclo'];
			$query .= " update productos19 set Orden_Ciclo = \"$ordenCiclo\" where Codigo = \"$codProducto\"; ";
		}

		$query .= " update novedades_menu set estado = 0 where id = $idIntercambio; ";
		$result = $Link->multi_query($query) or die ('Update error'. mysqli_error($Link));  
		
		if($result){
			$resultadoAJAX = array(
				"state" => 1,
				"message" => "El registro se ha realizado con éxito.",
			);
		}

	}
}


// $fecha = date('Y-m-d H:i:s');
// $carpeta = 'upload/novedades/menu/';
// $carpetaFisica = '../../../upload/novedades/menu/';
// $nuevoId = 0;

// //var_dump($_FILES);
// //var_dump($_SESSION);

// $usuario = $_SESSION['id_usuario'];
// $mes = '';
// $semana = '';
// $dia = '';
// $tipoComplemento = '';
// $grupoEtario = '';
// $codigoMenu = '';
// $menu = '';
// $fechaVencimiento = '';
// $observaciones = '';
// $rutaArchivo = '';



// if(isset($_POST['mes']) && $_POST['mes'] != ''){
// 	$mes = mysqli_real_escape_string($Link, $_POST['mes']);
// }
// if(isset($_POST['semana']) && $_POST['semana'] != ''){
//   $semana = mysqli_real_escape_string($Link, $_POST['semana']);
// }
// if(isset($_POST['dia']) && $_POST['dia'] != ''){
//   $dia = mysqli_real_escape_string($Link, $_POST['dia']);
// }
// if(isset($_POST['tipoComplemento']) && $_POST['tipoComplemento'] != ''){
//   $tipoComplemento = mysqli_real_escape_string($Link, $_POST['tipoComplemento']);
// }
// if(isset($_POST['grupoEtario']) && $_POST['grupoEtario'] != ''){
//   $grupoEtario = mysqli_real_escape_string($Link, $_POST['grupoEtario']);
// }
// if(isset($_POST['codigoMenu']) && $_POST['codigoMenu'] != ''){
//   $codigoMenu = mysqli_real_escape_string($Link, $_POST['codigoMenu']);
// }
// if(isset($_POST['menu']) && $_POST['menu'] != ''){
//   $menu = mysqli_real_escape_string($Link, $_POST['menu']);
// }
// if(isset($_POST['fechaVencimiento']) && $_POST['fechaVencimiento'] != ''){
//   $fechaVencimiento = mysqli_real_escape_string($Link, $_POST['fechaVencimiento']);
//   $fechaVencimiento = str_replace('/', '-', $fechaVencimiento);
//   $fechaVencimiento = date("Y-m-d", strtotime($fechaVencimiento));


// }
// if(isset($_POST['observaciones']) && $_POST['observaciones'] != ''){
//   $observaciones = mysqli_real_escape_string($Link, $_POST['observaciones']);
// }

// $preparacion = '';

// if(isset($_POST['preparacion']) && $_POST['preparacion'] != ''){
// 	$preparacion = mysqli_real_escape_string($Link, $_POST['preparacion']);
// }



// $consultaFichaTecnica = " select * from fichatecnica where Codigo = \"$preparacion\" ";
// //echo $consultaFichaTecnica;
// $resultadoFichaTecnica = $Link->query($consultaFichaTecnica);
// if ($resultadoFichaTecnica->num_rows > 0) {
// 	$fichaTecnica = $resultadoFichaTecnica->fetch_assoc();
// 	$idFichaTecnica = $fichaTecnica['Id'];
// 		$consultaFichaTecnicaDet = "select * from fichatecnicadet where IdFT = ".$idFichaTecnica;
// 		//echo $consultaFichaTecnicaDet;
// 		$resultadoFichaTecnicaDet = $Link->query($consultaFichaTecnicaDet);
// 		if ($resultadoFichaTecnicaDet->num_rows > 0) {

// 			// Registro del encabezdo de la novedad
// 			$query = " insert into novedades_menu (mes, semana, dia, menu, tipo_complem, cod_grupo_etario, cod_producto, observaciones, url_archivo, fecha_registro, estado, fecha_vencimiento, id_usuario, tipo_intercambio) values (\"$mes\", \"$semana\", \"$dia\", \"$menu\", \"$tipoComplemento\", \"$grupoEtario\", \"$preparacion\", \"$observaciones\", \"\", \"$fecha\", \"1\", \"$fechaVencimiento\", \"$usuario\", \"1\") "; 

// 			//echo "<br><br>$query<br><br>";
// 			$result = $Link->query($query) or die ('Insertando novedad'. mysqli_error($Link)); 
// 			$nuevoId = $Link->insert_id;
	
// 			/* Tratamiento del archivo */
// 			if (!file_exists($carpetaFisica)) {
// 			    mkdir($carpetaFisica, 0777);
// 			}
// 			if (isset($_FILES["foto"])) {
// 					$file = $_FILES["foto"];
// 					$tipo = $file["type"];
// 					$size = $file["size"];
// 					$nombre = $file["name"];
// 					$ruta_provisional = $file["tmp_name"];
// 					$ext = pathinfo($nombre, PATHINFO_EXTENSION);
// 					if($nuevoId > 0){
// 						$nombre = $nuevoId.'.'.$ext;
// 						$src = $carpetaFisica.$nombre;
// 						$srcw = $carpeta.$nombre;
// 						if(move_uploaded_file($ruta_provisional, $src)){
// 							$query = " update novedades_menu set url_archivo = \"$srcw\" where id = \"$nuevoId\" ";
// 							// echo "<br><br> $query <br><br>";
// 							$Link->query($query) or die ('Error actualizando la URL del archivo de novedad de menú'. mysqli_error($Link));
// 						}
// 					}	
				
// 			}
// 			/* Termina tratamiento del archivo */


// 			// Registro del detalle de la novedad
			
// 			// Inserción de los registros originales
// 			$query = " INSERT INTO novedades_menudet (tipo, id_novedad, cod_producto, unidadmedida, pesoneto, pesobruto) SELECT 0 AS tipo, \"$nuevoId\" as novedad, ftd.codigo, ftd.UnidadMedida, ftd.PesoNeto, ftd.PesoBruto from fichatecnicadet ftd where ftd.IdFT = \"$idFichaTecnica\"";

	

// 			$Link->query($query) or die ('Error inserción de registros originales en el detalle de la novedad'. mysqli_error($Link));



			
















// 			// Borrar de la tabla de fichatecnicadet los productos que corresponden al IdFT
// 			$query = " delete from fichatecnicadet where idFT = $idFichaTecnica ";
// 			$result = $Link->query($query) or die ('Delete error'. mysqli_error($Link));   
			

// 			// Hacer el registro de los productos para el IdFT
// 			$productos = $_POST['productos'];
// 			$query = " insert into fichatecnicadet ( codigo, Componente, Cantidad, UnidadMedida, Costo, idFT, Subtotal, Factor, Estado, Tipo, TipoProducto, PesoBruto, PesoNeto ) values ";

// 			$queryNovedadDet = " INSERT INTO novedades_menudet (tipo, id_novedad, cod_producto, unidadmedida, pesoneto, pesobruto) values";




// 			$aux = 0;
// 			foreach ($productos as $productoElement) {
// 				//var_dump($productoElement);
// 					$producto = mysqli_real_escape_string($Link, $productoElement['producto']);
// 					$productoNombre = mysqli_real_escape_string($Link, $productoElement['productoNombre']);
// 					$unidad = mysqli_real_escape_string($Link, $productoElement['unidad']);
// 					$pesoBruto = mysqli_real_escape_string($Link, $productoElement['pesoBruto']);
// 					$pesoNeto = mysqli_real_escape_string($Link, $productoElement['pesoNeto']);
// 					$factor = 1 / floatval($pesoBruto); 
// 					if($aux > 0){
// 						$query .= " , ";
// 						$queryNovedadDet .= " , ";
// 					}
					
// 					$query .= " ( ";
// 					$query .= " \"$producto\", \"$productoNombre\", \"$pesoBruto\" , \"$unidad\", \"0,00\", \"$idFichaTecnica\", \"0,00\", \"$factor\", \"0\", \"Alimento\", \"Alimento\", \"$pesoBruto\", \"$pesoNeto\" ";
// 					$query .= " ) ";

// 					$queryNovedadDet .= " (\"1\", \"$nuevoId\", \"$producto\", \"$unidad\", \"$pesoNeto\", \"$pesoBruto\") ";

// 			$aux++;
// 			}
// 			//echo $query;
// 			$result = $Link->query($query) or die ('Insert error'. mysqli_error($Link));   
// 			$resultNovedadDet = $Link->query($queryNovedadDet) or die ('Insert error en novedad det'. mysqli_error($Link)); 

// 			if($result){
// 				$resultadoAJAX = array(
// 					"estado" => 1,
// 					"message" => "El registro se ha realizado con éxito.",
// 				);
// 			}  
// 		}
// }
echo json_encode($resultadoAJAX);