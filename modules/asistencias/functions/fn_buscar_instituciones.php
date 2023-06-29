<option value="">Seleccione una</option>
<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';
$periodoActual = $_SESSION['periodoActual']; 

$institucionRector = "";
$municipio = '';
if(isset($_POST['municipio']) && $_POST['municipio'] != ''){
	$municipio = mysqli_real_escape_string($Link, $_POST['municipio']);
}
$institucion = '';
if(isset($_POST['institucion']) && $_POST['institucion'] != ''){
	$institucion = mysqli_real_escape_string($Link, $_POST['institucion']);
}
$validacion = '';
if(isset($_POST['validacion']) && $_POST['validacion'] != ''){
	$validacion = mysqli_real_escape_string($Link, $_POST['validacion']);
}
$opciones = "<option value=\"\">Seleccione uno</option>";
// exit(var_dump($_POST));
$institucionesAuxiliar = "";
if ($_SESSION['perfil'] == "8") {
	$idAuxiliar = $_SESSION["idUsuario"];
	$consultaInstitucionAuxiliar = " SELECT DISTINCT(cod_inst) AS codigoInstitucion FROM sedes$periodoActual WHERE id_auxiliar_asistencia =$idAuxiliar ";
	$respuestaInstitucionAuxiliar = $Link->query($consultaInstitucionAuxiliar) or die ('Error al consultar la institucion del auxiliar ' .mysqli_error($Link));
	if ($respuestaInstitucionAuxiliar->num_rows > 0) {
		$institucionesAuxiliar = "( ";
		while ($dataInstitucionAuxiliar = $respuestaInstitucionAuxiliar->fetch_assoc()) {
			$institucionesAuxiliar .= "'" .$dataInstitucionAuxiliar['codigoInstitucion']. "',";
		}
		$institucionesAuxiliar = trim($institucionesAuxiliar, ",");
		$institucionesAuxiliar .= ")";
	}
}

// Si es ususario de tipo rector buscar la institución del rector.
if($_SESSION["perfil"] == 6){
	$documentoRector = mysqli_real_escape_string($Link, $_SESSION['num_doc']);
	$consulta = " SELECT codigo_inst, nom_inst FROM instituciones WHERE cc_rector = \"$documentoRector\" ";
	//echo "<br><br>$consulta<br><br>";
	$resultado = $Link->query($consulta) or die ('No se pudo cargar la institucion del rector. '. mysqli_error($Link));
	if($resultado->num_rows >= 1){
		$row = $resultado->fetch_assoc();
		if($row['codigo_inst'] != ""){
			$institucionRector = $row['codigo_inst'];	
		}
	}
} 
else if($_SESSION['perfil'] == 7) {
	$documentoCoordinador = $_SESSION['num_doc'];
	$consulta = " SELECT codigo_inst, nom_inst FROM instituciones WHERE 1 = 1 ";
	$consultaInstitucion = "SELECT i.codigo_inst FROM instituciones i LEFT JOIN sedes$periodoActual s ON s.cod_inst = i.codigo_inst WHERE s.id_coordinador = $documentoCoordinador LIMIT 1 ";
	$respuestaInstitucion = $Link->query($consultaInstitucion) or die ('Error al consultar el codigo de institucion ' . mysqli_error($Link));
	if ($respuestaInstitucion->num_rows > 0) {
		$dataInstitucion = $respuestaInstitucion->fetch_assoc();
		$institucionRector = $dataInstitucion['codigo_inst'];
	}
}
else{
	$consulta = " SELECT codigo_inst, nom_inst FROM instituciones WHERE 1=1 ";
}

$consulta.= " AND cod_mun = \"$municipio\" AND codigo_inst IN (SELECT cod_inst FROM sedes$periodoActual WHERE 1=1 ";
if($validacion == 'Tablet'){
	$consulta.= " and (tipo_validacion = \"$validacion\" or tipo_validacion = \"Lector de Huella\" ) ";
}else{
	if($validacion != ''){
		$consulta.= " and tipo_validacion = \"$validacion\" ";
	}
}
$consulta.= " AND cod_mun_sede = \"$municipio\") ";

if($institucionRector != ""){
	$consulta.= " AND codigo_inst = \"$institucionRector\" ";
}

if ($institucionesAuxiliar != "") {
	$consulta .= "AND codigo_inst IN $institucionesAuxiliar ";
}

$consulta = $consulta." ORDER BY nom_inst ASC "; 
$resultado = $Link->query($consulta) or die ('No se pudieron cargar los muunicipios. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	while($row = $resultado->fetch_assoc()){ ?>
		<option value="<?= $row['codigo_inst'] ?>" <?= ($institucion == $row['codigo_inst']) ? 'selected' : '' ?> ><?= $row['nom_inst'] ?></option>			
	<?php }
}