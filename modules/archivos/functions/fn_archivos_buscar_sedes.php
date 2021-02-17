<option value="">Todas</option>
<?php
include '../../../config.php';
$institucion = '';

if(isset($_POST['institucion'])){ $institucion = $_POST['institucion']; }

$periodoActual = $_SESSION['periodoActual'];
require_once '../../../db/conexion.php';

$consulta = "SELECT DISTINCT s.cod_sede, s.nom_sede FROM sedes$periodoActual s LEFT JOIN sedes_cobertura sc on s.cod_sede = sc.cod_sede WHERE 1=1";
if($institucion != ''){ $consulta = $consulta."  AND s.cod_inst = '$institucion'"; }
$consulta .= " ORDER BY s.nom_sede ASC";

echo "<br><br>$consulta<br><br>";

$resultado = $Link->query($consulta) or die ('No se pudo ejecutar la consulta para listar las sedes.'. mysqli_error($Link));
if($resultado->num_rows >= 1){
    while($row = $resultado->fetch_assoc()) { ?>
        <option value="<?php echo $row['cod_sede']; ?>"><?php echo $row['nom_sede']; ?></option>
    <?php }// Termina el while
}//Termina el if que valida que si existan resultados

