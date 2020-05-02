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
                pro.Email AS emailProveedor,
                ubi.Ciudad AS municipio,
                pro.compraslocales AS comprasLocales
              FROM
                proveedores pro
              INNER JOIN
                ubicacion ubi ON ubi.CodigoDANE = pro.cod_municipio";

  $resultado = $Link->query($consulta);
  if($resultado->num_rows > 0) {
    while($registros = $resultado->fetch_assoc()) {
      if ($registros["comprasLocales"] == 1) {
        $registros["comprasLocales"] = '<i class="fa fa-check text-success"></i>';
      } else {
        $registros["comprasLocales"] = '<i class="fa fa-ban text-danger"></i>';
      }

      $registros['input'] = '<div class="btn-group">
                                <div class="dropdown">
                                  <button class="btn btn-primary btn-sm" id="dLabel" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Acciones <span class="caret"></span>
                                  </button>
                                  <ul class="dropdown-menu pull-right" aria-labelledby="dLabel">
                                    <li><a href="#" class="editarProveedores" data-idProveedor="'. $registros["idProveedor"] .'"><i class="fa fa-pencil fa-lg"></i> Editar</a></li>
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