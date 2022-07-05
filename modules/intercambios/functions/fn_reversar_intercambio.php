<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';
$periodoActual = $_SESSION['periodoActual'];

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

// validacion para que no se pueda devolver un intercambio si ya existe uno creado
$consultaValidacion = " SELECT id, mes, semana, fecha_registro FROM novedades_menu WHERE id = '".$idIntercambio."' ";
$respuestaValidacion = $Link->query($consultaValidacion) or die ('Error al consultar la novedad'. mysqli_error($Link));
if ($respuestaValidacion->num_rows > 0) {
	$dataValidacion = $respuestaValidacion->fetch_assoc();
	$fecha_registro = $dataValidacion['fecha_registro'];
	$id_registro = $dataValidacion['id'];
	$consultaSegundaValidacion = " SELECT id, mes, semana, fecha_registro FROM novedades_menu WHERE cod_producto = '".$codigoIntercambio."' AND id != '".$id_registro."' AND estado = '1' AND fecha_registro > '".$fecha_registro."' ORDER BY fecha_registro DESC  LIMIT 1 "; 
	$respuestaSegundaValidacion = $Link->query($consultaSegundaValidacion) or die ('Error en la validacion ln 34' . mysqli_error($Link));
	if ($respuestaSegundaValidacion->num_rows > 0) {
		$dataSegundaValidacion = $respuestaSegundaValidacion->fetch_assoc();
		$numeroExistente = $dataSegundaValidacion['id'];
		$mesExistente = $dataSegundaValidacion['mes'];
		$semanaExistente = $dataSegundaValidacion['semana'];
		$resultadoAJAX = array(
			"steate" => 2,
			"message" => "Existe una novedad de orden superior en el mes : $mesExistente, semana : $semanaExistente, número : $numeroExistente ",
		);
		echo json_encode($resultadoAJAX);
		exit();
	}
}

// Cosulta del Id de la ficha técnica
$consultaFichaTecnica = " select * from fichatecnica where Codigo = \"$codigoIntercambio\" ";
$resultadoFichaTecnica = $Link->query($consultaFichaTecnica);
if ($resultadoFichaTecnica->num_rows > 0) {
	$fichaTecnica = $resultadoFichaTecnica->fetch_assoc();
	$idFichaTecnica = $fichaTecnica['Id'];
}

if($tipoIntercambio == 1){
	// Borrar de la tabla de fichatecnicadet los productos que corresponden al IdFT
	$query = " delete from fichatecnicadet where idFT = $idFichaTecnica ";
	$result = $Link->query($query) or die ('Delete error'. mysqli_error($Link));


	// Insertando a partir de la información de la tabla de novedades det
	$query = "INSERT INTO fichatecnicadet (codigo, Componente, Cantidad, UnidadMedida, Costo, IdFT, Subtotal, Factor, Estado, Tipo, TipoProducto, PesoBruto, PesoNeto) SELECT nmd.cod_producto AS codigo, p.Descripcion AS Componente, nmd.pesobruto AS Cantidad, nmd.unidadmedida AS UnidadMedida, \"0,00\" AS Costo, \"$idFichaTecnica\" AS IdFT, \"0,00\" AS Subtotal, (1/nmd.pesobruto) AS Factor, \"0\" AS Estado, \"Alimento\" AS Tipo, \"Alimento\" AS TipoProducto, nmd.pesobruto AS PesoBruto, nmd.pesoneto AS PesoNeto FROM novedades_menudet nmd LEFT JOIN productos$periodoActual p ON p.Codigo = nmd.cod_producto WHERE nmd.id_novedad = $idIntercambio AND nmd.tipo = 0 ";
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
	$query = " 	INSERT INTO fichatecnicadet (codigo, Componente, Cantidad, UnidadMedida, Costo, IdFT, Subtotal, Factor, Estado, Tipo, TipoProducto, PesoBruto, PesoNeto) SELECT nmd.cod_producto AS codigo, p.Descripcion AS Componente, nmd.pesobruto AS Cantidad, \"u\" AS UnidadMedida, \"0,00\" AS Costo, \"$idFichaTecnica\" AS IdFT, \"0,00\" AS Subtotal, \"0.00000000\" AS Factor, \"0\" AS Estado, \"Preparacion\" AS Tipo, \"Preparacion\" AS TipoProducto, nmd.pesobruto AS PesoBruto, nmd.pesoneto AS PesoNeto FROM novedades_menudet nmd LEFT JOIN productos$periodoActual p ON p.Codigo = nmd.cod_producto WHERE nmd.id_novedad = $idIntercambio AND nmd.tipo = 0 ORDER BY nmd.id asc ";

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
			$query .= " update productos$periodoActual set Orden_Ciclo = \"$ordenCiclo\" where Codigo = \"$codProducto\"; ";
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

echo json_encode($resultadoAJAX);