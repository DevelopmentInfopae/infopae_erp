<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

// Declaración de variables.
$data = [];
$semanaActual = "";
$sede = "";
$grado = "";
$grupo = "";

$periodoActual = mysqli_real_escape_string($Link, $_SESSION['periodoActual']);

// var_dump($_POST);

$semanaActual = (isset($_POST["semanaActual"]) && $_POST["semanaActual"] != "") ? mysqli_real_escape_string($Link, $_POST["semanaActual"]) : "";

$sede = (isset($_POST["sede"]) && $_POST["sede"] != "") ? mysqli_real_escape_string($Link, $_POST["sede"]) : "";

$grado = (isset($_POST["grado"]) && $_POST["grado"] != "") ? mysqli_real_escape_string($Link, $_POST["grado"]) : "";

$grupo = (isset($_POST["grupo"]) && $_POST["grupo"] != "") ? mysqli_real_escape_string($Link, $_POST["grupo"]) : "";




$consulta = " select f.num_doc, concat(f.ape1, \" \", f.ape2, \" \", f.nom1, \" \", f.nom2) as nombre, f.cod_grado as grado, f.nom_grupo as grupo from focalizacion$semanaActual f where 1=1 ";

if($sede != "" ){
	$consulta .= " and f.cod_sede = $sede ";
}
if($grado != "" ){
	$consulta .= " and f.cod_grado = $grado ";
}
if($grupo != "" ){
	$consulta .= " and f.nom_grupo = $grupo ";
}
$consulta .= " order by f.cod_grado, f.nom_grupo, f.ape1 ";

// echo $consulta;

// $consulta = "select f.num_doc, concat(f.nom1, \" \", f.nom2, \" \", f.ape1, \" \", f.ape2) as nombre, f.cod_grado as grado, f.nom_grupo as grupo from focalizacion$semanActual f where f.cod_inst = 268307000035 and f.cod_sede = 26830700003501 and f.cod_grado = 9 and f.nom_grupo = 901 and f.tipo_complemento = \"CAJMRI\" ";

$resultado = $Link->query($consulta);
if($resultado->num_rows > 0){
  while($row = $resultado->fetch_assoc()) {
    $data[] = $row;
  }
}

$output = [
  'sEcho' => 1,
  'iTotalRecords' => count($data),
  'iTotalDisplayRecords' => count($data),
  'aaData' => $data
];

echo json_encode($output);



/*

												$consulta = "SELECT DISTINCT codigoDANE, ciudad FROM ubicacion WHERE ETC <> '1' ";

												$DepartamentoOperador = $_SESSION['p_CodDepartamento'];
												if($DepartamentoOperador != ''){
													$consulta = $consulta." AND CodigoDANE LIKE '$DepartamentoOperador%' ";
												}
												$consulta = $consulta." ORDER BY ciudad ASC ";
												$resultado = $Link->query($consulta);
												if($resultado->num_rows > 0){
													while($row = $resultado->fetch_assoc()) { ?>
														<option value="<?php echo $row["codigoDANE"]; ?>" <?php if(isset($_POST["municipio"]) && $_POST["municipio"] == $row["codigoDANE"] || $municipio_defecto["CodMunicipio"] == $row["codigoDANE"]) { echo " selected "; } ?>>
															<?php echo $row["ciudad"]; ?>
														</option>
									
													}
												}
										*/