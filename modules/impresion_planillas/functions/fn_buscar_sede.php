<option value="">Todas</option>
<?php
  include '../../../config.php';
  require_once '../../../autentication.php';
  require_once '../../../db/conexion.php';

  $periodoActual = $_SESSION['periodoActual'];
  $municipio = '';
  $institucion = '';

  if(isset($_POST['municipio'])){
    $municipio = $_POST['municipio'];
  }

  if(isset($_POST['institucion'])){
    $institucion = $_POST['institucion'];
  }

  if(isset($_POST['mes'])){
    $mes = $_POST['mes'];
  }


  $Link = new mysqli($Hostname, $Username, $Password, $Database);
  if ($Link->connect_errno) {
    echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }
  $Link->set_charset("utf8");
  $entregas = " entregas_res_$mes$periodoActual ";

  $consulta = " SELECT DISTINCT s.cod_sede, s.nom_sede
                    FROM sedes$periodoActual s
                    LEFT JOIN sedes_cobertura sc on s.cod_sede = sc.cod_sede
                    INNER JOIN $entregas enc ON s.cod_sede = enc.cod_sede
                    where 1=1 ";


  if($municipio != ''){
    $consulta = $consulta." and s.cod_mun_sede = '$municipio' ";
  }


  if($institucion != ''){
    $consulta = $consulta."  and s.cod_inst = '$institucion' ";
  }

// exit(var_dump($consulta));
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
    $consulta .= " AND s.cod_sede IN ($codigoSedes) ";
  }


  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
  if($resultado->num_rows >= 1){
    while($row = $resultado->fetch_assoc()) { ?>
      <option value="<?php echo $row['cod_sede']; ?>"><?php echo $row['nom_sede']; ?></option>
    <?php }// Termina el while
  }//Termina el if que valida que si existan resultados
