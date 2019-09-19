<?php
require_once '../../../config.php';
require_once '../../../db/conexion.php';


$dato_municipio = $Link->query("SELECT CodMunicipio FROM parametros") or die(mysqli_error($Link));
if ($dato_municipio->num_rows > 0) { $municipio_defecto = $dato_municipio->fetch_array(); }


$log = '';
$opciones = "<option value=\"\">Seleccione uno</option>";
$periodoActual = $_SESSION['periodoActual'];


$consulta = " SELECT DISTINCT s.cod_mun_sede, u.Ciudad, u.CodigoDANE FROM sedes_cobertura sc LEFT JOIN sedes$periodoActual s ON sc.cod_sede = s.cod_sede LEFT JOIN ubicacion u ON s.cod_mun_sede = u.CodigoDANE and u.ETC = 0 ORDER BY u.Ciudad ASC ";
$resultado = $Link->query($consulta) or die ('Unable to execute query - Buscando Municipios '. mysqli_error($Link));
if($resultado->num_rows >= 1){
    $aux = 0;
    while($row = $resultado->fetch_assoc()) {
        $codigo = $row['CodigoDANE'];
        $ciudad = $row['Ciudad'];
        $selected = ($municipio_defecto["CodMunicipio"] == $row['CodigoDANE']) ? "selected" : "";
        $opciones .= " <option value=\"$codigo\" $selected>$ciudad</option> ";
    }
}
echo json_encode(array("log"=>$log, "respuesta"=>$opciones));
