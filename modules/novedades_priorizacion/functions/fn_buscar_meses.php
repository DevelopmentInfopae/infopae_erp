<?php
require_once '../../../config.php';
require_once '../../../autentication.php';
require_once '../../../db/conexion.php';

$sede = (isset($_POST['sede']) && $_POST['sede'] != '') ? mysqli_real_escape_string($Link, $_POST["sede"]) : "";
$periodoActual = mysqli_real_escape_string($Link, $_SESSION['periodoActual']);

$log = '';
$respuesta = "<option value=\"\">Seleccione uno</option>";

//$consulta = " SELECT TABLE_NAME AS tabla FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '$Database' and TABLE_NAME LIKE 'priorizacion%' ";
$consulta = " select distinct sc.mes from sedes_cobertura sc where sc.cod_sede = '$sede' order by sc.mes asc ";
//var_dump($consulta);
$resultado = $Link->query($consulta) or die ('Unable to execute query - Buscando Meses '. mysqli_error($Link));
if($resultado->num_rows >= 1){
    $aux = 0;
    while($row = $resultado->fetch_assoc()) {
        $mes = $row['mes'];
        $nombre = nombreMes($mes);
        if($nombre != ''){
            $respuesta .= " <option value=\"$mes\">$nombre</option> ";
        }
    }
}
echo json_encode(array("log"=>$log, "respuesta"=>$respuesta));

function nombreMes($mes){
    switch ($mes) {
        case "01":
            return "Enero";
            break;
        case "02":
            return "Febrero";
            break;
        case "03":
            return "Marzo";
            break;
        case "04":
            return "Abril";
            break;
        case "05":
            return "Mayo";
            break;
        case "06":
            return "Junio";
            break;
        case "07":
            return "Julio";
            break;
        case "08":
            return "Agosto";
            break;
        case "09":
            return "Septiembre";
            break;
        case "10":
            return "Octubre";
            break;
        case "11":
            return "Noviembre";
            break;
        case "12":
            return "Diciembre";
            break;
    }

}
