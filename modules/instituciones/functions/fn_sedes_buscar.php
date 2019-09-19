<?php
	require_once '../../../db/conexion.php';
	require_once '../../../config.php';

	// Declaración de variables enviadas por AJAX.
	$periodoActual = $_SESSION['periodoActual'];
	$municipio = mysqli_real_escape_string($Link, $_POST["municipio"]);
	$institucion = (isset($_POST["institucion"]) && $_POST["institucion"] != "") ? mysqli_real_escape_string($Link, $_POST["institucion"]) : "";
	$condicionInstitucion = ($institucion != "") ? "AND cod_inst = $institucion" : "";

	$consultaSedes = "SELECT sed.id, sed.nom_sede AS nombreSede, sed.cod_sede, sed.estado AS estadoSede, usu.nombre AS nombreCoordinador, jor.nombre AS nombreJornada, tipo_validacion AS tipoValidacion
						        FROM sedes$periodoActual sed
						        LEFT JOIN usuarios usu ON usu.id = sed.id_coordinador 
						        LEFT JOIN jornada jor ON jor.id = sed.jornada 
						        WHERE cod_mun_sede = '$municipio' $condicionInstitucion
						        ORDER BY nom_sede ASC";
	$resultadoSedes = $Link->query($consultaSedes);
	if($resultadoSedes){
		while($registrosSedes = $resultadoSedes->fetch_assoc()) {
			echo '<tr>
							<td>'. $registrosSedes["nombreSede"] .'</td>
							<td>'. $registrosSedes["nombreCoordinador"] .'</td>
							<td>'. $registrosSedes["nombreJornada"] .'</td>
							<td>'. $registrosSedes["tipoValidacion"] .'</td>
							<td>Acciones</td>
						</tr>';
		}
	} else {
		echo '<td><h4>No existe Sedes registradas para los anteriores filtros de búsqueda. </h4></td>';
	}