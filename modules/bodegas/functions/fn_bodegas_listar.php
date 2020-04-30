<?php
  require_once '../../../db/conexion.php';
  require_once '../../../config.php';

  $data = [];
  $consulta = "SELECT
								bod.ID AS codigoBodega,
								bod.NOMBRE AS nombreBodega,
								ubi.Ciudad AS ciudadBodega,
								bod.RESPONSABLE AS responsableBodega
							FROM bodegas bod
							INNER JOIN ubicacion ubi ON ubi.CodigoDANE = bod.CIUDAD";
  $resultado = $Link->query($consulta);
  if ($resultado->num_rows > 0)
  {
  	while($registros = $resultado->fetch_assoc())
  	{
  		$registros['input'] = '<div class="btn-group">
		                          <div class="dropdown">
		                            <button class="btn btn-primary btn-sm" id="dLabel" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		                              Acciones <span class="caret"></span>
		                            </button>
		                            <ul class="dropdown-menu pull-right" aria-labelledby="dLabel">
		                              <li><a href="#" class="editarBodega" data-codigobodega="'. $registros["codigoBodega"] .'"><i class="fa fa-pencil fa-lg"></i> Editar</a></li>
		                              <li><a href="#" class="confirmarEliminarBodega" data-codigobodega="'. $registros["codigoBodega"] .'"><i class="fa fa-trash fa-lg"></i> Eliminar</a></li>
		                            </ul>
		                          </div>
		                        </div>';
  		$data[] = $registros;
  	}
  }

  $salida = [
  	'sEcho' => 1,
  	'iTotalRecords' => count($data),
  	'iTotalDisplayRecords' => count($data),
  	'aaData' => $data
  ];

  echo json_encode($salida);