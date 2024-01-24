<option value="">Seleccione...</option>
<?php
require_once '../../../db/conexion.php';
include '../../../config.php';
$periodoActual = $_SESSION['periodoActual'];

$consultaInst = " SELECT DISTINCT(i.codigo_inst), i.nom_inst 
                    FROM instituciones i
                    INNER JOIN sedes$periodoActual s ON s.cod_inst = i.codigo_inst 
                    WHERE 1=1 ";

if (isset($_POST['municipio']) && $_POST['municipio'] != '') {
    $consultaInst .= " AND i.cod_mun = " .$_POST['municipio']. " ";
}

if (isset($_POST['sector']) && $_POST['sector'] != '') {
    $consultaInst .= " AND s.sector = " .$_POST['sector']. " ";
}

$respuestaInst = $Link->query($consultaInst) or die ('Error al consultar las Instituciones fn ln6' );
if ($respuestaInst->num_rows > 0) {
    while ($dataInst = $respuestaInst->fetch_object()) {
?>      <option value="<?= $dataInst->codigo_inst ?>"  > 
                <?= $dataInst->nom_inst ?> 
        </option>   
<?php        
    }
}

?>
