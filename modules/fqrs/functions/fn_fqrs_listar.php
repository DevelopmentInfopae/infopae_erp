<?php
	require_once '../../../db/conexion.php';
  	require_once '../../../config.php';

  	// Declaración de variables.
  	$datosTabla = [];

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
				    IF (f.estado = 0, 'Abierto', 'Cerrado') AS estado,
				    f.fecha_creacion
				FROM fqrs f
					INNER JOIN ubicacion u ON u.CodigoDANE = f.cod_mun
				    INNER JOIN tipo_casosfqrs tc ON tc.ID = f.tipo_caso
				    INNER JOIN tipo_personafqrs tp ON tp.ID = f.tipo_persona
				    INNER JOIN tipodocumento td ON td.id = f.tipo_doc
				    INNER JOIN sedes20 s ON s.cod_sede = f.cod_sede";
	$resultado = $Link->query($consulta);
  	if($resultado->num_rows > 0){
  		while($registros = $resultado->fetch_assoc()) {

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
      		$datosTabla[] = $registros;
  		}
  	}

  	$respuestaAJAX = [
		'sEcho' => 1,
		'iTotalRecords' => count($datosTabla),
		'iTotalDisplayRecords' => count($datosTabla),
		'aaData' => $datosTabla
	];

  	echo json_encode($respuestaAJAX);