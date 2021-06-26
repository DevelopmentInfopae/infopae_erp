<?php 
  include '../../../config.php';
  if ($_SESSION['perfil'] != "6" && $_SESSION['perfil'] != "7"): ?>
  <option value="">Todos</option>

<?php endif ?>

<?php

  $condicionRector = '';
  $condicionCoordinador = '';
  $municipio = '';
  $tipo = '';

  if(isset($_POST['municipio'])){
    $municipio = $_POST['municipio'];
  }

  if(isset($_POST['tipo'])){
    $tipo = $_POST['tipo'];
  }



  $periodoActual = $_SESSION['periodoActual'];
  require_once '../../../db/conexion.php';

  $Link = new mysqli($Hostname, $Username, $Password, $Database);
  if ($Link->connect_errno) {
    echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }
  $Link->set_charset("utf8");

  if ($_SESSION['perfil'] == '6' && $_SESSION['num_doc'] != "" ) {
    $consultaInstitucion = "SELECT codigo_inst FROM instituciones WHERE cc_rector = " .$_SESSION['num_doc']. " LIMIT 1;";
    $respuestaInstitucion = $Link->query($consultaInstitucion) or die ('Error al consultar la institución' . mysqli_error($Link));
    if ($respuestaInstitucion->num_rows > 0) {
      $dataInstitucion = $respuestaInstitucion->fetch_assoc();
      $codigoInstitucion = $dataInstitucion['codigo_inst']; 
    }
    $condicionRector = " AND s.cod_inst = $codigoInstitucion ";   
  }

  if ($_SESSION['perfil'] == "7" && $_SESSION['num_doc'] != "") {
    $documentoCoordinador = $_SESSION['num_doc'];
    $consultaCodigoInstitucion = "SELECT i.codigo_inst FROM instituciones i INNER JOIN sedes$periodoActual s ON s.cod_inst = i.codigo_inst WHERE s.id_coordinador = $documentoCoordinador ";
    $respuestaCodigoInstitucion = $Link->query($consultaCodigoInstitucion) or die ('Error al consultar el código de la institución. ' .mysqli_error($Link));
    if ($respuestaCodigoInstitucion->num_rows > 0) {
      $dataCodigoInstitucion = $respuestaCodigoInstitucion->fetch_assoc();
      $codigoInstitucion = $dataCodigoInstitucion['codigo_inst'];
    }
    $condicionCoordinador = " AND s.cod_inst = $codigoInstitucion ";
  }

  $consulta = " select distinct s.cod_inst, s.nom_inst
  from sedes$periodoActual s
  left join sedes_cobertura sc on s.cod_sede = sc.cod_sede
  where
  s.cod_mun_sede = '$municipio' $condicionRector $condicionCoordinador ";

  if($tipo != ''){
    $consulta = $consulta." and sc.$tipo > 0 ";
  }

  $consulta = $consulta." ORDER BY s.nom_inst ASC;";


  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
  if($resultado->num_rows >= 1){
    while($row = $resultado->fetch_assoc()) { ?>
      <option value="<?php echo $row['cod_inst']; ?>"><?php echo $row['nom_inst']; ?></option>
    <?php }// Termina el while
  }//Termina el if que valida que si existan resultados
