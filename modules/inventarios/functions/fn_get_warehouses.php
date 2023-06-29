<option value="">Seleccione...</option>
<?php
include '../../../config.php';
require_once '../../../db/conexion.php';
$periodoActual = $_SESSION['periodoActual'];

$auxMunicipio = '';
$auxResponsable = '';
if(isset($_POST['municipio'])){ 
    $municipio = $_POST['municipio'];
    $auxMunicipio .= " AND CIUDAD = '" .$municipio. "' " ; 
}

if(isset($_POST['sinc'])){ 
    $auxResponsable .= " AND RESPONSABLE = '' "; 
}

$consultaBodegas = " SELECT ID, NOMBRE FROM bodegas WHERE 1=1 $auxMunicipio $auxResponsable " ;
$respuestaBodegas = $Link->query($consultaBodegas) or die ('Error al consultar las bodegas' );
if ($respuestaBodegas->num_rows > 0) {
    while ($dataBodegas = $respuestaBodegas->fetch_assoc()) {
?>      <option value="<?= $dataBodegas['ID'] ?>"><?= $dataBodegas['NOMBRE'] ?></option>   
<?php        
    }
}

?>

