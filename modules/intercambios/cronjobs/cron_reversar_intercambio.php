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
$bigQuery = '';

$query = " SELECT nm.id, nm.tipo_intercambio, nm.cod_producto FROM novedades_menu nm WHERE nm.fecha_vencimiento < CURRENT_DATE AND nm.estado = 1 ";
$result = $Link->query($query) or die ('Buscando novedades para reversar'. mysqli_error($Link));

if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()){
		$idFichaTecnica = '';
		$idIntercambio = $row['id'];
		$tipoIntercambio = $row['tipo_intercambio'];
		$codigoIntercambio = $row['cod_producto'];
		
		// Cosulta del Id de la ficha técnica
		$consultaFichaTecnica = " select * from fichatecnica where Codigo = \"$codigoIntercambio\" ";
		//echo $consultaFichaTecnica;
		$resultadoFichaTecnica = $Link->query($consultaFichaTecnica);
		if ($resultadoFichaTecnica->num_rows > 0) {
			$fichaTecnica = $resultadoFichaTecnica->fetch_assoc();
			$idFichaTecnica = $fichaTecnica['Id'];
		}

		if($tipoIntercambio == 1){

			// Borrar de la tabla de fichatecnicadet los productos que corresponden al IdFT
			$bigQuery .= " delete from fichatecnicadet where idFT = $idFichaTecnica; ";

			// Insertando a partir de la información de la tabla de novedades det
			$bigQuery .= "INSERT INTO fichatecnicadet (codigo, Componente, Cantidad, UnidadMedida, Costo, IdFT, Subtotal, Factor, Estado, Tipo, TipoProducto, PesoBruto, PesoNeto) SELECT nmd.cod_producto AS codigo, p.Descripcion AS Componente, nmd.pesobruto AS Cantidad, nmd.unidadmedida AS UnidadMedida, \"0,00\" AS Costo, \"$idFichaTecnica\" AS IdFT, \"0,00\" AS Subtotal, (1/nmd.pesobruto) AS Factor, \"0\" AS Estado, \"Alimento\" AS Tipo, \"Alimento\" AS TipoProducto, nmd.pesobruto AS PesoBruto, nmd.pesoneto AS PesoNeto FROM novedades_menudet nmd LEFT JOIN productos$periodoActual p ON p.Codigo = nmd.cod_producto WHERE nmd.id_novedad = $idIntercambio AND nmd.tipo = 0; ";
			
			// Cambiando el estado de la novedad
			$bigQuery .= " update novedades_menu set estado = 0 where id = $idIntercambio; ";
		}

		else if($tipoIntercambio == 2){

			// Borrar de la tabla de fichatecnicadet los productos que corresponden al IdFT
			$bigQuery .= " delete from fichatecnicadet where idFT = $idFichaTecnica; ";

			// Insertando a partir de la información de la tabla de novedades det
			$bigQuery .= " 	INSERT INTO fichatecnicadet (codigo, Componente, Cantidad, UnidadMedida, Costo, IdFT, Subtotal, Factor, Estado, Tipo, TipoProducto, PesoBruto, PesoNeto) SELECT nmd.cod_producto AS codigo, p.Descripcion AS Componente, nmd.pesobruto AS Cantidad, \"u\" AS UnidadMedida, \"0,00\" AS Costo, \"$idFichaTecnica\" AS IdFT, \"0,00\" AS Subtotal, \"0.00000000\" AS Factor, \"0\" AS Estado, \"Preparacion\" AS Tipo, \"Preparacion\" AS TipoProducto, nmd.pesobruto AS PesoBruto, nmd.pesoneto AS PesoNeto FROM novedades_menudet nmd LEFT JOIN productos$periodoActual p ON p.Codigo = nmd.cod_producto WHERE nmd.id_novedad = $idIntercambio AND nmd.tipo = 0 ORDER BY nmd.id asc; ";
			
			// Cambiando el estado de la novedad
			$bigQuery .= " update novedades_menu set estado = 0 where id = $idIntercambio; ";
		}
		
		else if($tipoIntercambio == 3){

			$query3 = " SELECT nmd.cod_producto, nmd.orden_ciclo from novedades_menudet nmd WHERE nmd.tipo = 0 AND nmd.id_novedad = $idIntercambio ORDER BY nmd.id ";
			$result3 = $Link->query($query3);
			if ($result3->num_rows > 0) {

				while($row3 = $result3->fetch_assoc()){
					$codProducto3 = $row3['cod_producto'];
					$ordenCiclo3 = $row3['orden_ciclo'];
					$bigQuery .= " update productos$periodoActual set Orden_Ciclo = \"$ordenCiclo3\" where Codigo = \"$codProducto3\"; ";
				}

				$bigQuery .= " update novedades_menu set estado = 0 where id = $idIntercambio; ";

			}
		}

	}
}

if($bigQuery != ""){
	$result = $Link->multi_query($bigQuery) or die ('Big Update error'. mysqli_error($Link)); 
}