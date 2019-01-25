<option value="">Todas</option>
<?php
include '../../../config.php';
require_once '../../../db/conexion.php';

$dato_municipio = $Link->query("SELECT CodMunicipio FROM parametros") or die(mysqli_error($Link));
if ($dato_municipio->num_rows > 0) { $municipio_defecto = $dato_municipio->fetch_array(); }

$municipio = (isset($_POST['municipio']) && $_POST['municipio'] != '') ? mysqli_real_escape_string($Link, $_POST["municipio"]) : "";
$tipo = (isset($_POST['tipo']) && $_POST['tipo'] != '') ? mysqli_real_escape_string($Link, $_POST["tipo"]) : "";
$periodoActual = $_SESSION['periodoActual'];
$consulta = " select distinct s.cod_inst, s.nom_inst from sedes$periodoActual s left join sedes_cobertura sc on s.cod_sede = sc.cod_sede where s.cod_mun_sede = '$municipio' ";
if($tipo != ''){
	$consulta = $consulta." and sc.$tipo > 0 ";
}
$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	while($row = $resultado->fetch_assoc()) { ?>
		<option value="<?php echo $row['cod_inst']; ?>" <?php if ($municipio_defecto["CodMunicipio"] == $row["cod_inst"]) { echo "selected"; } ?>><?php echo $row['nom_inst']; ?></option>
	<?php }
}
