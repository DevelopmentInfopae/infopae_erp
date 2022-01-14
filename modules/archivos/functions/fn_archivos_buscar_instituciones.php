<option value="">Todas</option>
<?php
include '../../../config.php';
$municipio = '';

if(isset($_POST['municipio'])){ $municipio = $_POST['municipio']; }

$periodoActual = $_SESSION['periodoActual'];
require_once '../../../db/conexion.php';

$consulta = " select distinct s.cod_inst, s.nom_inst from sedes$periodoActual s left join sedes_cobertura sc on s.cod_sede = sc.cod_sede where s.cod_mun_sede = '$municipio' ";

$consulta = $consulta." ORDER BY s.nom_inst ASC;";

$resultado = $Link->query($consulta) or die ('No se pudo ejecutar la consulta para listar las instituciones.'. mysqli_error($Link));
if($resultado->num_rows >= 1){
while($row = $resultado->fetch_assoc()) { ?>
    <option value="<?php echo $row['cod_inst']; ?>"><?php echo $row['nom_inst']; ?></option>
<?php }// Termina el while
}//Termina el if que valida que si existan resultados

