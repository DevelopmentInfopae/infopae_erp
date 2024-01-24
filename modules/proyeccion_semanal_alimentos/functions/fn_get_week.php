<option value="">Seleccione...</option>
<?php
include '../../../config.php';
require_once '../../../db/conexion.php';
$periodoActual = $_SESSION['periodoActual'];

$consultaSemanas = " SELECT DISTINCT SEMANA FROM planilla_semanas WHERE 1=1 ";
if(isset($_POST['mes'])){
    $mes = $_POST['mes'];
    $consultaSemanas .= " AND MES = '" .$_POST['mes']. "'" ;
}

$respuestaSemanas = $Link->query($consultaSemanas) or die ('Error al consultar las bodegas' );
if ($respuestaSemanas->num_rows > 0) {
    while ($dataSemanas = $respuestaSemanas->fetch_assoc()) {
?>      <option value="<?= $dataSemanas['SEMANA'] ?>"  > 
                <?= $dataSemanas['SEMANA'] ?> 
        </option>   
<?php        
    }
}

?>
