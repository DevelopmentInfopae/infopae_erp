
<option value="">Seleccione</option>

<?php
include '../../../config.php';
require_once '../../../db/conexion.php';

$dato_municipio = $Link->query("SELECT CodMunicipio FROM parametros") or die(mysqli_error($Link));
if ($dato_municipio->num_rows > 0) { $municipio_defecto = $dato_municipio->fetch_array(); }
$municipio = (isset($_POST['municipio']) && $_POST['municipio'] != '') ? mysqli_real_escape_string($Link, $_POST["municipio"]) : "";
$tipo = (isset($_POST['tipo']) && $_POST['tipo'] != '') ? mysqli_real_escape_string($Link, $_POST["tipo"]) : "";
$mes = (isset($_POST['mes']) && $_POST['mes'] != '') ? mysqli_real_escape_string($Link, $_POST["mes"]) : "";
$periodoActual = $_SESSION['periodoActual'];

if (intval($mes) < 10) {
	$mes = '0'.intval($mes);
}

$entregas = " entregas_res_$mes$periodoActual ";
$consulta = " SELECT 	DISTINCT s.cod_inst, 
						s.nom_inst 
					FROM sedes$periodoActual s 
					LEFT JOIN sedes_cobertura sc on s.cod_sede = sc.cod_sede 
					LEFT JOIN instituciones i ON s.cod_inst = i.codigo_inst
					INNER JOIN $entregas ent ON ent.cod_sede = sc.cod_sede
					where s.cod_mun_sede = '$municipio' ";

if ($mes != '') {
	$consulta .= " AND sc.mes = '$mes' ";
}					

if($_SESSION['perfil'] == "6"){  
	$rectorDocumento = $_SESSION['num_doc'];
	$consulta .= " and cc_rector = $rectorDocumento ";
}

if ($_SESSION['perfil'] == "7") {
	$documentoCoordinador = $_SESSION['num_doc'];
	$consulta .= " AND id_coordinador = $documentoCoordinador ";
}


if($tipo != ''){
	$consulta .=" AND sc.$tipo > 0 ";
}
$consulta .= " ORDER BY s.nom_inst";
// exit(var_dump($consulta));
$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	while($row = $resultado->fetch_assoc()) { ?>
		<option value="<?php echo $row['cod_inst']; ?>" <?php if ($municipio_defecto["CodMunicipio"] == $row["cod_inst"]) { echo "selected"; } ?>><?php echo $row['nom_inst']; ?></option>
	<?php }
}
