<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';
$periodoActual = $_SESSION['periodoActual'];

// Declaración de variables.
$datosTabla = [];

$consulta = "SELECT 
                  pagos_nomina.Fecha,
                  CONCAT(pagos_nomina.documento, '-', pagos_nomina.numero) AS numero,
                  pagos_nomina.mes,
                  CONCAT('De ', pagos_nomina.semquin_inicial, ' a ', pagos_nomina.semquin_final) as periodo,
                  empleados.nombre,
                  empleados.Nitcc,
                  empleados.tipo,
                  ubicacion.ciudad,
                  sedes.nom_sede,
                  pagos_nomina.tipo_complem,
                  SUM(pagos_nomina.auxilio_transporte + pagos_nomina.auxilio_extra + pagos_nomina.otros_devengados) as total_devengados,
                  SUM(pagos_nomina.desc_eps + pagos_nomina.desc_afp + pagos_nomina.otros_deducidos + pagos_nomina.retefuente + pagos_nomina.reteica) as tota_deducidos,
                  SUM(pagos_nomina.total_pagado) as total_pagado
              FROM pagos_nomina 
                INNER JOIN empleados ON empleados.Nitcc = pagos_nomina.doc_empleado
                INNER JOIN ubicacion ON ubicacion.CodigoDANE = pagos_nomina.cod_mun_sede
                INNER JOIN sedes$periodoActual as sedes ON sedes.cod_sede = pagos_nomina.cod_sede
              GROUP BY pagos_nomina.numero";
// exit($consulta);
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
                                  
                                </ul>
                              </div>
                            </div>';
    $datosTabla[] = $registros;
  }
}

  
                                    // <li><a href="#" class="confirmarEliminarEmpleado" data-idempleado="'. $registros["idEmpleado"] .'"><i class="fa fa-trash fa-lg"></i> Eliminar</a></li>

  $respuestaAJAX = [
    'sEcho' => 1,
    'iTotalRecords' => count($datosTabla),
    'iTotalDisplayRecords' => count($datosTabla),
    'aaData' => $datosTabla
  ];

  echo json_encode($respuestaAJAX);