<option value="">Seleccione...</option>
<?php 
require_once '../../../config.php';
require_once '../../../db/conexion.php';
// var_dump($_GET);
$condicionRector = '';
$municipio = $_GET['municipio'];
$periodoActual = $_SESSION['periodoActual'];
$institucion = $_GET['institucion'];

if ($_SESSION['perfil'] == 6 && $_SESSION['num_doc'] != '') {
	$consultaInstitucion = "SELECT codigo_inst FROM instituciones WHERE cc_rector = " . $_SESSION['num_doc'] ."";
	$respuestaInstitucion = $Link->query($consultaInstitucion) or die ('Error al consultar la institución ' . mysqli_error($Link));
	if ($respuestaInstitucion->num_rows > 0) {
	 	$dataInstitucion = $respuestaInstitucion->fetch_assoc();
	 	$codigoInstitucion = $dataInstitucion['codigo_inst'];
	}
	$condicionRector = " AND codigo_inst = $codigoInstitucion "; 
}

if ($_SESSION['perfil'] == 7 && $_SESSION['num_doc'] != '') {
	$documentoCoordinador = $_SESSION['num_doc'];
	$consultaInstitucion = "SELECT i.codigo_inst FROM instituciones i LEFT JOIN sedes$periodoActual s ON s.cod_inst = i.codigo_inst WHERE s.id_coordinador = $documentoCoordinador ";
	$respuestaInstitucion = $Link->query($consultaInstitucion) or die ('Error al consultar el codigo de la institución ' . mysqli_error($Link));
	if ($respuestaInstitucion->num_rows > 0) {
		$dataInstitucion = $respuestaInstitucion->fetch_assoc();
		$codigoInstitucion = $dataInstitucion['codigo_inst'];
	}
	$condicionRector = " AND codigo_inst = $codigoInstitucion ";
}

$consulta = "SELECT * FROM instituciones WHERE cod_mun = '".$municipio."' $condicionRector ORDER BY nom_inst ASC;";
// exit(var_dump($consulta));
$resultado = $Link->query($consulta);
if ($resultado->num_rows > 0) {
	while ($mun = $resultado->fetch_assoc()) { ?>
		<option value="<?php echo $mun['codigo_inst']; ?>" <?php if($institucion !== '' && $institucion == $mun['codigo_inst'] ){ echo "selected" ;} ?> ><?php echo $mun['nom_inst']; ?></option>
	<?php }
}