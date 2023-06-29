<option value="">Seleccione...</option>
<?php
include '../../../config.php';
require_once '../../../db/conexion.php';
$periodoActual = $_SESSION['periodoActual'];

if(isset($_POST['mes'])){ 
    $mes = $_POST['mes'];
}

$consultaSemanas = " SELECT DISTINCT SEMANA FROM planilla_semanas WHERE mes = '$mes' ";
$respuestaSemanas = $Link->query($consultaSemanas) or die ('Error al consultar las bodegas' );
if ($respuestaSemanas->num_rows > 0) {
    while ($dataSemanas = $respuestaSemanas->fetch_assoc()) {
?>      <option value="<?= $dataSemanas['SEMANA'] ?>"><?= $dataSemanas['SEMANA'] ?></option>   
<?php        
    }
}

?>

