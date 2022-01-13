<option value="">Todos</option>
<?php
  include '../../../config.php';
  require_once '../../../db/conexion.php';
  require_once '../../../autentication.php';
  // exit(var_dump($_POST));
  $periodoActual = $_SESSION['periodoActual'];
  $tipo = (isset($_POST['tipo'])) ? $_POST['tipo'] : '';
  $semana = (isset($_POST['semana'])) ? $_POST['semana'] : '';
  $municipio = (isset($_POST['municipio'])) ? $_POST['municipio'] : '';
  $institucion = (isset($_POST['institucion'])) ? $_POST['institucion'] : '';

  $condicionCoordinador = '';
  $condicionRector = '';

  if ($_SESSION['perfil'] == "6" && $_SESSION['perfil'] != "") {
     $codigoInstitucion = "";
     $documentoRector = $_SESSION['num_doc'];
     $consultaInstitucion = "SELECT codigo_inst FROM instituciones WHERE cc_rector = $documentoRector LIMIT 1;";
     $respuestaInstitucion = $Link->query($consultaInstitucion) or die ('Error al consultar el código de la institución ' . mysqli_error($Link));
     if ($respuestaInstitucion->num_rows > 0) {
      $dataInstitucion = $respuestaInstitucion->fetch_assoc();
      $codigoInstitucion = $dataInstitucion['codigo_inst'];
     }
     $condicionRector = " AND s.cod_inst = $codigoInstitucion ";
  }

  if ($_SESSION['perfil'] == "7" && $_SESSION['num_doc'] != '') {
    $codigoSedes = "";
    $documentoCoordinador = $_SESSION['num_doc'];
    $consultaCodigoSedes = "SELECT cod_sede FROM sedes$periodoActual WHERE id_coordinador = $documentoCoordinador;";
    $respuestaCodigoSedes = $Link->query($consultaCodigoSedes) or die('Error al consultar el código de la sede ' . mysqli_error($Link));
    if ($respuestaCodigoSedes->num_rows > 0) {
      $codigoInstitucion = '';
      while ($dataCodigoSedes = $respuestaCodigoSedes->fetch_assoc()) {
        $codigoSedeRow = $dataCodigoSedes['cod_sede'];
        $consultaCodigoInstitucion = "SELECT cod_inst FROM sedes$periodoActual WHERE cod_sede = $codigoSedeRow;";
        $respuestaCodigoInstitucion = $Link->query($consultaCodigoInstitucion) or die ('Error al consultar el código de la institución ' . mysqli_error($Link));
        if ($respuestaCodigoInstitucion->num_rows > 0) {
          $dataCodigoInstitucion = $respuestaCodigoInstitucion->fetch_assoc();
          $codigoInstitucionRow = $dataCodigoInstitucion['cod_inst'];
          if ($codigoInstitucionRow == $codigoInstitucion || $codigoInstitucion == '') {
            $codigoSedes .= "'$codigoSedeRow'".",";
            $codigoInstitucion = $codigoInstitucionRow; 
          }
        }
      }
    }
    $codigoSedes = substr($codigoSedes, 0 , -1);
    $condicionCoordinador = " AND s.cod_sede IN ($codigoSedes) ";
    }

  $consulta = "SELECT DISTINCT s.cod_sede, s.nom_sede FROM sedes$periodoActual s LEFT JOIN sedes_cobertura sc on s.cod_sede = sc.cod_sede WHERE 1=1 $condicionCoordinador $condicionRector ";

  if($semana != ''){ $consulta = $consulta." AND sc.semana = '$semana'"; }
  if($municipio != ''){ $consulta = $consulta." AND s.cod_mun_sede = '$municipio'"; }
  if($tipo != ''){ $consulta = $consulta."  AND sc.$tipo > 0 AND (sc.Etario1_$tipo > 0 || sc.Etario2_$tipo > 0 || sc.Etario3_$tipo > 0 )"; }
  if($institucion != ''){ $consulta = $consulta."  AND s.cod_inst = '$institucion' "; }
  $consulta .= " ORDER BY s.nom_sede ASC";

  // exit(var_dump($_SESSION));
  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
  if($resultado->num_rows >= 1){
    while($row = $resultado->fetch_assoc()) {
?>
      <option value="<?php echo $row['cod_sede']; ?>"><?php echo $row['nom_sede']; ?></option>
<?php
    }
  }
