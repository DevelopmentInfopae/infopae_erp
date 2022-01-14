<?php
/**
 * Buscar Municipios.
 * Rutina que busca los municipios
 * Rutina desarrollada originalmente para el modulo de asistencias
 * @author Ricardo Farfán <ricardo@xlogam.com>
 */
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$municipio = '';

if(isset($_POST['municipio']) && $_POST['municipio'] != ''){ $municipio = mysqli_real_escape_string($Link, $_POST['municipio']); }
else if(isset($_SESSION['p_Municipio']) && $_SESSION['p_Municipio'] != ''){ $municipio = mysqli_real_escape_string($Link, $_SESSION['p_Municipio']); }

$opciones = "<option value=\"\">Seleccione uno</option>";
$DepartamentoOperador = $_SESSION['p_CodDepartamento'];
$consulta = " select distinct codigodane, ciudad from ubicacion where etc <> \"1\" ";
if($DepartamentoOperador != ''){
  $consulta = $consulta." AND codigodane LIKE '$DepartamentoOperador%' ";
}
if ($_SESSION['p_Municipio'] != "" || $_SESSION['p_Municipio'] != 0) {
  $consulta .= " AND codigoDANE =  '" .$_SESSION['p_Municipio']. "'";
}
$consulta = $consulta." order by ciudad asc ";
// echo $consulta;
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
