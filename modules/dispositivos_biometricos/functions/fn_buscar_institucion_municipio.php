<option value="">Seleccione</option>
<?php
include '../../../config.php';
require_once '../../../db/conexion.php';

// $dato_municipio = $Link->query("SELECT CodMunicipio FROM parametros") or die(mysqli_error($Link));
// if ($dato_municipio->num_rows > 0) { $municipio_defecto = $dato_municipio->fetch_array(); }

$municipio = (isset($_POST['municipio']) && $_POST['municipio'] != '') ? $Link->real_escape_string($_POST["municipio"]) : "";
// $tipo = (isset($_POST['tipo']) && $_POST['tipo'] != '') ? mysqli_real_escape_string($Link, $_POST["tipo"]) : "";
// $periodoActual = $_SESSION['periodoActual'];

$consulta_instituciones = "SELECT codigo_inst AS codigo, nom_inst AS nombre FROM instituciones WHERE cod_mun = '$municipio' ORDER BY nom_inst";
$respuesta_instituciones = $Link->query($consulta_instituciones) or die ('Error al consultar instituciones: '. $Link->error);
if($respuesta_instituciones->num_rows > 0){
	while($institucion = $respuesta_instituciones->fetch_assoc()){
?>
<option value="<?= $institucion["codigo"]; ?>"><?= $institucion["nombre"]; ?></option>
<?php
	}
}
