<option value="">Seleccione...</option>
<?php 
require_once '../../../config.php';
require_once '../../../db/conexion.php';

$tabla = "focalizacion".$_POST['semana'];
$sedes = "sedes".$_SESSION['periodoActual'];
$condicionInstitucion = '';

if ($_SESSION['perfil'] == "6" && $_SESSION['num_doc'] != "") {
	$codigoInstitucion = '';
	$documentoRector = $_SESSION['num_doc'];
	$consultaInstitucion = "SELECT codigo_inst FROM instituciones WHERE cc_rector = $documentoRector;";
	$respuestaInstitucion = $Link->query($consultaInstitucion) or die ('Error al consultar el código de la institución ' . mysqli_error($Link));
	if ($respuestaInstitucion->num_rows > 0) {
		$dataInstitucion = $respuestaInstitucion->fetch_assoc();
		$codigoInstitucion = $dataInstitucion['codigo_inst'];
	}
	$condicionInstitucion = " WHERE F.cod_inst = $codigoInstitucion ";
}

if ($_SESSION['perfil'] == "7" && $_SESSION['perfil'] != "") {
	$codigoInstitucion = "";
	$documentoCoordinador = $_SESSION['num_doc'];
	$consultaCodigoInstitucion = "SELECT cod_inst FROM $sedes WHERE id_coordinador = $documentoCoordinador LIMIT 1 ";
	$respuestaCodigoInstitucion = $Link->query($consultaCodigoInstitucion) or die ('Error al consultar el código de la institución ' . mysqli_error($Link));
	if ($respuestaCodigoInstitucion->num_rows > 0) {
		$dataCodigoInstitucion = $respuestaCodigoInstitucion->fetch_assoc();
		$codigoInstitucion = $dataCodigoInstitucion['cod_inst'];
	}
	$condicionInstitucion = " WHERE F.cod_inst = $codigoInstitucion ";
}

$consulta = "SELECT ubicacion.CodigoDANE, ubicacion.Ciudad FROM $tabla as F 
				INNER JOIN $sedes as sede ON sede.cod_sede = F.cod_sede
			    INNER JOIN ubicacion ON ubicacion.CodigoDANE = sede.cod_mun_sede
			    $condicionInstitucion
			GROUP BY ubicacion.CodigoDANE;";
// exit(var_dump($consulta));			
$resultado = $Link->query($consulta);
if ($resultado->num_rows > 0) {
	while ($mun = $resultado->fetch_assoc()) { ?>
		<option value="<?php echo $mun['CodigoDANE']; ?>"><?php echo $mun['Ciudad']; ?></option>
	<?php }
}