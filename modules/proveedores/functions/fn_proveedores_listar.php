<?php
  require_once '../../../db/conexion.php';
  require_once '../../../config.php';

  // Declaración de variables.
  $datosTabla = [];

  $consulta = "SELECT
                pro.ID AS idProveedor,
                pro.Nitcc AS nitProveedor,
                pro.Nombrecomercial AS nombreComercialProveedor,
                pro.RazonSocial AS razonsocialProveedor,
                pro.Email AS emailProveedor
              FROM
                proveedores pro;";

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
                                    <li><a href="#" class="editarProveedores" data-idProveedor="'. $registros["idProveedor"] .'"><i class="fa fa-pencil fa-lg"></i> Editar</a></li>
                                    <li><a href="#" class="confirmarEliminarProveedores" data-idProveedor="'. $registros["idProveedor"] .'" data-razonsocialproveedor="'. $registros['razonsocialProveedor'] .'"><i class="fa fa-trash fa-lg"></i> Eliminar</a></li>
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