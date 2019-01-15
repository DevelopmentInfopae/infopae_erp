<?php
require_once '../../../config.php';
require_once '../../../db/conexion.php';
$log = '';
$opciones = "<option value=\"\">Seleccione uno</option>";
$periodoActual = $_SESSION['periodoActual'];

// $consulta = " select distinct u.Ciudad, u.CodigoDANE from sedes$periodoActual s left join ubicacion u on s.cod_mun_sede = u.CodigoDANE order by u.Ciudad asc ";
$consulta = " SELECT DISTINCT s.cod_mun_sede, u.Ciudad, u.CodigoDANE FROM sedes_cobertura sc LEFT JOIN sedes$periodoActual s ON sc.cod_sede = s.cod_sede LEFT JOIN ubicacion u ON s.cod_mun_sede = u.CodigoDANE and u.ETC = 0 ORDER BY u.Ciudad ASC ";
// var_dump($consulta);
$resultado = $Link->query($consulta) or die ('Unable to execute query - Buscando Municipios '. mysqli_error($Link));
if($resultado->num_rows >= 1){
    $aux = 0;
    while($row = $resultado->fetch_assoc()) {
        $codigo = $row['CodigoDANE'];
        $ciudad = $row['Ciudad'];
        $opciones .= " <option value=\"$codigo\">$ciudad</option> ";
    }
}
echo json_encode(array("log"=>$log, "respuesta"=>$opciones));
