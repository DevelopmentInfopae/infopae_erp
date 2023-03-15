<option value="">Seleccione...</option>
<?php
include '../../../config.php';
require_once '../../../db/conexion.php';
$periodoActual = $_SESSION['periodoActual'];

if(isset($_POST['municipio'])){ $municipio = $_POST['municipio']; }

$consultaBodegas = " SELECT cod_sede, nom_sede FROM sedes$periodoActual WHERE cod_mun_sede = '" .$municipio. "'";
$respuestaBodegas = $Link->query($consultaBodegas) or die ('Error al consultar las bodegas' );
if ($respuestaBodegas->num_rows > 0) {
    while ($dataBodegas = $respuestaBodegas->fetch_assoc()) {
?>      <option value="<?= $dataBodegas['cod_sede'] ?>"><?= $dataBodegas['nom_sede'] ?></option>     
<?php        
    }
}

?>

