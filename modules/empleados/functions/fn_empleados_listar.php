<?php
  require_once '../../../db/conexion.php';
  require_once '../../../config.php';

  // Declaración de variables.
  $datosTabla = [];

  $consulta = "SELECT
                emp.ID AS idEmpleado,
                emp.Nitcc AS cedulaEmpleado,
                emp.Nombre AS nombreEmpleado,
                emp.Email AS emailEmpleado,
                ubi.Ciudad AS ciudadEmpleado
              FROM
                empleados emp
              LEFT JOIN ubicacion ubi ON ubi.CodigoDANE = emp.Ciudad;";

  $resultado = $Link->query($consulta);
  if($resultado->num_rows > 0)
  {
    while($registros = $resultado->fetch_assoc())
    {
      $registros['input'] = '<div class="btn-group">
                                <div class="dropdown">
                                  <button class="btn btn-primary btn-sm" id="dLabel" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Acciones <span class="caret"></span>
                                  </button>
                                  <ul class="dropdown-menu pull-right" aria-labelledby="dLabel">
                                    <li><a href="#" class="editarEmpleado" data-idempleado="'. $registros["idEmpleado"] .'"><i class="fa fa-pencil fa-lg"></i> Editar</a></li>
                                    <li><a href="#" class="confirmarEliminarEmpleado" data-idempleado="'. $registros["idEmpleado"] .'"><i class="fa fa-trash fa-lg"></i> Eliminar</a></li>
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