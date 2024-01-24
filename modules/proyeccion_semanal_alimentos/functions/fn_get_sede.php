<option value="">Seleccione...</option>
<?php
require_once '../../../db/conexion.php';
include '../../../config.php';
$periodoActual = $_SESSION['periodoActual'];
// exit(var_dump($_POST));
$consultaSedes = " SELECT cod_sede, nom_sede FROM sedes$periodoActual WHERE cod_inst = " .$_POST['institucion']. " ";

if (isset($_POST['sector']) && $_POST['sector'] != '') {
    $consultaSedes .= " AND sector = " .$_POST['sector']. " ";
}

$respuestaSedes = $Link->query($consultaSedes) or die ('Error al consultar las sedes fn ln 8' );
if ($respuestaSedes->num_rows > 0) {
    while ($dataSedes = $respuestaSedes->fetch_object()) {
?>      <option value="<?= $dataSedes->cod_sede ?>"  > 
                <?= $dataSedes->nom_sede ?> 
        </option>   
<?php        
    }
}

?>
