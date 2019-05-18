<?php
require_once '../../../config.php';
require_once '../../../db/conexion.php';

$data = [];
$codigo_sede = $Link->real_escape_string($_POST['sede']);
$semana = $Link->real_escape_string($_POST['semana']);

$consulta_suplentes = "SELECT sup.*, CONCAT(sup.nom1, ' ', sup.nom2, ' ', sup.ape1, ' ', sup.ape2) AS nombre, g.nombre as grado, jor.nombre as jornada
FROM suplentes$semana sup
LEFT JOIN grados g ON g.id = sup.cod_grado
LEFT JOIN jornada jor ON jor.id = sup.cod_jorn_est
WHERE cod_sede = '$codigo_sede'";

$respuesta_suplentes = $Link->query($consulta_suplentes) or die('Error al consultar los suplentes: '. $Link->error);

if($respuesta_suplentes->num_rows > 0)
{
  while($suplente = $respuesta_suplentes->fetch_assoc())
  {
  	$suplente['acciones'] = '<div class="btn-group">'.
                                      '<div class="dropdown pull-right">'.
                                        '<button class="btn btn-primary btn-sm" type="button" id="acciones_suplentes" data-toggle="dropdown"  aria-haspopup="true">'.
                                          'Acciones <span class="caret"></span>'.
                                        '</button>'.
                                        '<ul class="dropdown-menu pull-right" aria-labelledby="acciones_suplentes">'.
                                          '<li>'.
                                            '<a href="#" class="editar_suplente" id="'.$suplente['id'].'" data-semana="'.$semana.'"><i class="fa fa-pencil fa-lg"></i> Editar</a>'.
                                          '</li>'.
                                          // '<li class="divider"></li>'.
                                          // '<li>'.
                                          //   '<a href="#">'.
                                          //     'Estado: &nbsp;'.
                                          //     '<input type="checkbox" class="estadoSede" data-toggle="toggle" data-on="Activo" data-off="Inactivo" data-size="mini" data-width="70" data-height="24">'.
                                          //   '</a>'.
                                          // '</li>'.
                                        '</ul>'.
                                      '</div>'.
                                    '</div>';
    $data[] = $suplente;
  }
}

$output = [
  'sEcho' => 1,
  'iTotalRecords' => count($data),
  'iTotalDisplayRecords' => count($data),
  'aaData' => $data
];

echo json_encode($output);