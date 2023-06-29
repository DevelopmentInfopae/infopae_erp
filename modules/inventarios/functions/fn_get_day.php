<option value="">Seleccione...</option>
<?php
include '../../../config.php';
require_once '../../../db/conexion.php';
$periodoActual = $_SESSION['periodoActual'];

if(isset($_POST['semana'])){ 
    $semana = $_POST['semana'];
}

$consultaSemanas = " SELECT DIA FROM planilla_semanas WHERE SEMANA = '$semana' ";
$respuestaSemanas = $Link->query($consultaSemanas) or die ('Error al consultar las bodegas' );
if ($respuestaSemanas->num_rows > 0) {
    while ($dataSemanas = $respuestaSemanas->fetch_assoc()) {
?>      <option value="<?= $dataSemanas['DIA'] ?>"><?= $dataSemanas['DIA'] ?></option>   
<?php        
    }
}

?>

