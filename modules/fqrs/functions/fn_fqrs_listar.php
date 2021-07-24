<?php
	require_once '../../../db/conexion.php';
  	require_once '../../../config.php';
  	require_once '../../../permisos.php';

  	$periodoActual = $_SESSION['periodoActual'];

  	$condicionRector = '';
  	if ($_SESSION['perfil'] == "6" && $_SESSION['num_doc'] != "7") {
  		$codigoInstitucion = '';
  		$documentoRector = $_SESSION['num_doc'];
  		$consultaInstitucion = "SELECT codigo_inst FROM instituciones WHERE cc_rector = $documentoRector LIMIT 1;";
  		$respuestaInstitucion = $Link->query($consultaInstitucion) or die ('Error al consultar el código de la institucion. ' . mysqli_error($Link));
  		if ($respuestaInstitucion->num_rows > 0) {
  			$dataInstitucion = $respuestaInstitucion->fetch_assoc();
  			$codigoInstitucion = $dataInstitucion['codigo_inst'];
  		}
  		$condicionRector .= " WHERE s.cod_inst = $codigoInstitucion ";
  	}

  	$condicionCoordinador = '';
  	if ($_SESSION['perfil'] == "7" && $_SESSION['num_doc'] != '') {
  		$codigoSedes = "";
  		$documentoCoordinador = $_SESSION['num_doc'];
  		$consultaCodigoSedes = "SELECT cod_sede FROM sedes$periodoActual WHERE id_coordinador = $documentoCoordinador;";
		$respuestaCodigoSedes = $Link->query($consultaCodigoSedes) or die('Error al consultar el código de la sede ' . mysqli_error($Link));
		if ($respuestaCodigoSedes->num_rows > 0) {
			$codigoInstitucion = '';
			while ($dataCodigoSedes = $respuestaCodigoSedes->fetch_assoc()) {
				$codigoSedeRow = $dataCodigoSedes['cod_sede'];
				$consultaCodigoInstitucion = "SELECT cod_inst FROM sedes$periodoActual WHERE cod_sede = $codigoSedeRow;";
				$respuestaCodigoInstitucion = $Link->query($consultaCodigoInstitucion) or die ('Error al consultar el código de la institución ' . mysqli_error($Link));
				if ($respuestaCodigoInstitucion->num_rows > 0) {
					$dataCodigoInstitucion = $respuestaCodigoInstitucion->fetch_assoc();
					$codigoInstitucionRow = $dataCodigoInstitucion['cod_inst'];
					if ($codigoInstitucionRow == $codigoInstitucion || $codigoInstitucion == '') {
						$codigoSedes .= "'$codigoSedeRow'".",";
						$codigoInstitucion = $codigoInstitucionRow; 
					}
				}
			}
		}
		$codigoSedes = substr($codigoSedes, 0 , -1);
		$condicionCoordinador = " WHERE s.cod_sede IN ($codigoSedes) ";
  	}


  	// Declaración de variables.
  	$datosTabla = [];
  	$periodoContrato = $_SESSION['periodoActual'];

  	$consulta = "SELECT f.ID AS id_fqrs,
					u.Ciudad AS municipio,
					s.nom_sede AS nombre_sede,
				    CASE tc.tipo
				    	WHEN 'F' THEN 'Felicitaciones'
				    	WHEN 'Q' THEN 'Queja'
				    	WHEN 'R' THEN 'Reclamo'
				    	WHEN 'S' THEN 'Sugerencia'
				    END AS tipo_caso,
				    tp.Descripción AS tipo_persona,
				    f.nombre_completo AS nombre_persona,
				    td.nombre AS tipo_documento,
				    f.num_doc AS numero_documento,
				    IF (f.estado = 0, '<span class=\"label label-warning\">Abierto</span>', '<span class=\"label label-primary\">Cerrado</span>') AS estado,
				    f.fecha_creacion
				FROM fqrs f
					INNER JOIN ubicacion u ON u.CodigoDANE = f.cod_mun
				    INNER JOIN tipo_casosfqrs tc ON tc.ID = f.tipo_caso
				    INNER JOIN tipo_personafqrs tp ON tp.ID = f.tipo_persona
				    INNER JOIN tipodocumento td ON td.id = f.tipo_doc
				    INNER JOIN sedes".$_SESSION['periodoActual']." s ON s.cod_sede = f.cod_sede $condicionRector $condicionCoordinador;";
		// exit(var_dump($consulta));		    			    
	$resultado = $Link->query($consulta) or die ('Error al consultar los fqrs existentes' . mysqli_error($Link));
  	if($resultado->num_rows > 0){
  		while($registros = $resultado->fetch_assoc()) {
  			if ($_SESSION['perfil'] == "0" || $permisos['fqrs'] == "2") {
  				$registros['input'] = '<div class="btn-group">
	                                	<div class="dropdown">
	                                  		<button class="btn btn-primary btn-sm" id="dLabel" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	                                    		Acciones <span class="caret"></span>
	                                  		</button>
	                                  		<ul class="dropdown-menu pull-right" aria-labelledby="dLabel">
	                                    		<li><a href="#" class="ver_fqrs" data-id_fqrs="'. $registros["id_fqrs"] .'"><i class="fa fa-pencil-square-o fa-lg"></i> editar</a></li>
	                                  		</ul>
	                                	</div>
	                              	   </div>';
  			}
      		$datosTabla[] = $registros;
  		}
  	}
  	// exit(var_dump($datosTabla));
  	$respuestaAJAX = [
		'sEcho' => 1,
		'iTotalRecords' => count($datosTabla),
		'iTotalDisplayRecords' => count($datosTabla),
		'aaData' => $datosTabla
	];

  	echo json_encode($respuestaAJAX);