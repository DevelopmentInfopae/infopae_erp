<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$municipio = '';

if(isset($_POST['municipio']) && $_POST['municipio'] != ''){
  $municipio = mysqli_real_escape_string($Link, $_POST['municipio']);
}else if(isset($_SESSION['p_Municipio']) && $_SESSION['p_Municipio'] != ''){
  $municipio = mysqli_real_escape_string($Link, $_SESSION['p_Municipio']);
}

$opciones = "<option value=\"\">Seleccione uno</option>";

$periodoActual = $_SESSION['periodoActual'];
$codMunicipio = $_SESSION['p_Municipio'];
$condicionCoordinador = '';

$consulta = " select distinct codigodane, ciudad from ubicacion ";
if($_SESSION['perfil'] == 6){
  $rectorDocumento = $_SESSION['num_doc'];
  $consulta .= " left join instituciones on instituciones.cod_mun = ubicacion.CodigoDANE where cc_rector = $rectorDocumento";
  $consulta .= " and etc <> \"1\" ";

 }else{
   $consulta .= " where etc <> \"1\" ";
 }

if ($_SESSION['perfil'] == "7") {
  $documentoCoordinador = $_SESSION['num_doc'];
  $consultaCodigoMunicipio = "SELECT i.cod_mun FROM instituciones i INNER JOIN sedes$periodoActual s ON s.cod_inst = i.codigo_inst WHERE s.id_coordinador = $documentoCoordinador LIMIT 1 ";
  $respuestaCodigoMunicipio = $Link->query($consultaCodigoMunicipio) or die ('Error al consultar el codigo del municipio ' . mysqli_error($Link));
  if ($respuestaCodigoMunicipio->num_rows > 0) {
    $dataCodigoMunicipio = $respuestaCodigoMunicipio->fetch_assoc();
    $codigoMunicipio = $dataCodigoMunicipio['cod_mun'];
  }
  $condicionCoordinador = " AND CodigoDANE = $codigoMunicipio ";
}

if($codMunicipio == '0'){
  $consulta = $consulta." AND codigodane LIKE '$DepartamentoOperador%' $condicionCoordinador ";
}else if($codMunicipio != '0'){
  $consulta .= " AND codigoDANE = $codMunicipio ";
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
