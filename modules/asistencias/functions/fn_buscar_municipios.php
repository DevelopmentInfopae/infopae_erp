<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$municipio = '';

if(isset($_POST['municipio']) && $_POST['municipio'] != ''){
    $municipio = $_POST['municipio'];
}else if(isset($_SESSION['p_Municipio']) && $_SESSION['p_Municipio'] != ''){
  $municipio = $_SESSION['p_Municipio'];
}

$opciones = "<option value=\"\">Seleccione uno</option>";




$DepartamentoOperador = $_SESSION['p_CodDepartamento'];
$consulta = " select distinct codigodane, ciudad from ubicacion where etc <> \"1\" ";
if($DepartamentoOperador != ''){
  $consulta = $consulta." AND codigodane LIKE '$DepartamentoOperador%' ";
}
$consulta = $consulta." order by ciudad asc ";


$resultado = $Link->query($consulta) or die ('No se pudieron cargar los muunicipios. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
    $respuesta = 1;
    while($row = $resultado->fetch_assoc()){
        $id = $row["codigodane"];
        $valor = $row["ciudad"];
        $opciones .= "<option value=\"$id\"";
        if($municipio == $id){
            $opciones .= " selected ";
        }
        $opciones .= ">";
        $opciones .= "$valor</option>";
    }
    $resultadoAJAX = array(
      "estado" => 1,
      "mensaje" => "Se ha cargado con exito.",
      "opciones" => $opciones
    );
}else{
  $resultadoAJAX = array(
    "estado" => 0,
    "mensaje" => "Se ha presentado un error."
  );
}
echo json_encode($resultadoAJAX);
