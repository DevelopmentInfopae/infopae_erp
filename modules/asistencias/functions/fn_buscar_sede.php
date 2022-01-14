<option value="">Seleccione una</option>
<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$periodoActual = mysqli_real_escape_string($Link, $_SESSION['periodoActual']);
$institucion = '';
if(isset($_POST['institucion']) && $_POST['institucion'] != ''){
	$institucion = mysqli_real_escape_string($Link, $_POST['institucion']);
}

$validacion = '';
if(isset($_POST['validacion']) && $_POST['validacion'] != ''){
	$validacion = mysqli_real_escape_string($Link, $_POST['validacion']);
}

$opciones = "<option value=\"\">Seleccione uno</option>";
$sedesAuxiliar = "";
if ($_SESSION['perfil'] == "8") {
	$idAuxiliar = $_SESSION['idUsuario'];
	$consultaSedesAuxiliar = " SELECT cod_sede FROM sedes$periodoActual WHERE cod_inst = $institucion AND id_auxiliar_asistencia = $idAuxiliar ";
	$respuestaSedesAuxiliar = $Link->query($consultaSedesAuxiliar) or die ('Error al consultar las sedes del auxiliar ' . mysqli_error($Link));
	if ($respuestaSedesAuxiliar->num_rows > 0) {
		$sedesAuxiliar= " ( ";
		while ($dataSedesAuxiliar = $respuestaSedesAuxiliar->fetch_assoc()) {
			$sedesAuxiliar .= "'" .$dataSedesAuxiliar['cod_sede']. "',";
		}
		$sedesAuxiliar = trim($sedesAuxiliar, ",");
		$sedesAuxiliar .= ")";
	}
}

$consulta = " select cod_sede, nom_sede from sedes$periodoActual where 1=1 ";

if($validacion == 'Tablet'){
	$consulta.= " and (tipo_validacion = \"$validacion\" or tipo_validacion = \"Lector de Huella\" ) ";
}else{
	if($validacion != ''){
		$consulta.= " and tipo_validacion = \"$validacion\" ";
	}
}
$consulta.= " and cod_inst = \"$institucion\" ";

if ($sedesAuxiliar != "") {
	$consulta .= " AND cod_sede IN $sedesAuxiliar ";
}

$consulta = $consulta." order by nom_sede asc ";
echo $consulta;
$resultado = $Link->query($consulta) or die ('No se pudieron cargar los muunicipios. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	while($row = $resultado->fetch_assoc()){ ?>	
		<option value="<?= $row['cod_sede'] ?>"> <?= $row['nom_sede'] ?> </option>
	<?php }
}