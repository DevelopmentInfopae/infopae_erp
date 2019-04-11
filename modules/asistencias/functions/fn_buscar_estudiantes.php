<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

// Declaración de variables.
$data = [];

$periodoActual = $_SESSION['periodoActual'];

$institucion = (isset($_POST["institucion"]) && $_POST["institucion"] != "") ? mysqli_real_escape_string($Link, $_POST["institucion"]) : "";

$municipio   = (isset($_POST['municipio']) && $_POST['municipio'] != '') ? ($_POST["municipio"] == "0") ? "" : mysqli_real_escape_string($Link, $_POST["municipio"]) : "";


$consulta = "select f.num_doc, concat(f.nom1, \" \", f.nom2, \" \", f.ape1, \" \", f.ape2) as nombre, f.cod_grado as grado, f.nom_grupo as grupo from focalizacion01 f where f.cod_inst = 268307000035 and f.cod_sede = 26830700003501 and f.cod_grado = 9 and f.nom_grupo = 901 and f.tipo_complemento = \"CAJMRI\" ";

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