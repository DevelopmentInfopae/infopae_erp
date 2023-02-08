<?php
require_once '../db/conexion.php';
$modulo = $_POST['modulo'];
$submodulo = $_POST['submodulo'];
$baseUrl = $_POST['baseurl'];
$nivel = $_POST['nivel'];
$nivel++;

$condicionSubmodulo = '';
if ($submodulo != '') {
    $condicionSubmodulo .= " AND m1.sub_modulo = '$submodulo' ";
}

$consultaSubmodulos = " SELECT  m1.icon,
                                m1.permisos,
                                m1.nombre, 
                                m1.label, 
                                m1.modulo, 
                                m1.sub_modulo, 
                                m1.nivel, 
                                m1.ruta
                            FROM menu_sidebar m1
                            WHERE m1.nivel = '$nivel' AND modulo = '$modulo' $condicionSubmodulo ";
$respuestaSubmodulos = $Link->query($consultaSubmodulos) or die (mysqli_error($Link));
if ($respuestaSubmodulos->num_rows > 0) {
    while ($dataSubmodulos = $respuestaSubmodulos->fetch_assoc()) {
        $submodulos[] = $dataSubmodulos;
    }
}
// exit(var_dump($consultaSubmodulos));
echo "<div class='col-lg-12'>";
foreach ($submodulos as $key => $value) {
    $icon = $value['icon'];
    $permiso = $value['permisos'];
    $nombre = $value['nombre'];
    $ruta = $value['ruta'];
    $modulo = $value['modulo'];
    $sub_modulo = $value['sub_modulo']; 
    if ($ruta != '#') {
        echo "<div class='col-md-3 col-sm-6'>";
        $label = $value['label'];
        if ($ruta != '') {
            $href = $baseUrl.$ruta;
            echo "  <button onclick=location.href='$href' value='$href' type='button' class='btn btn-success btn-outline btn-block button_index' id='button_index'>";
        }
        else{
            $href = '#';
            echo "  <button onclick=obtenerSubmodulos('$modulo','$nivel','$baseUrl','$sub_modulo') value='$href' type='button' class='btn btn-success btn-outline btn-block button_index' id='button_index'>";
        }

        echo "     <i class = '$icon'></i> 
                <span>$label</span>    
            </button>
        </div>";
    }        
}
echo "</div>";  

?>