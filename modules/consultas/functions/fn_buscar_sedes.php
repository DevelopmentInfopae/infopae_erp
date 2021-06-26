<?php
  require_once '../../../config.php';
  require_once '../../../db/conexion.php';

  if (isset($_SESSION['annoactual']) && $_SESSION['annoactual']!= '') {
    $annoactual = $_SESSION['annoactual'];
  } 
  else{
    $annoactual = date('Y');
  }

  $_SESSION['annoactual'] = $annoactual;
  $periodoActual = substr($annoactual, 2, 2); 
  $condicionCoordinador = '';
  // exit(var_dump($_SESSION));
  if(isset($_POST["institucion"]) && $_POST["institucion"] != "" ){
    if ($_SESSION['perfil'] == "7") {
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
    $condicionCoordinador = " AND cod_sede IN ($codigoSedes) ";
    }

    $institucion = $_POST["institucion"]; 
    $vsql = "select distinct cod_sede, nom_sede from sedes".$periodoActual." where cod_inst = '$institucion' $condicionCoordinador order by nom_sede asc";    
    $Link = new mysqli($Hostname, $Username, $Password, $Database);
    $Link->set_charset("utf8");
    $result = $Link->query($vsql);
    $Link->close();
  
  ?>
  
  <option value="">TODOS</option>
  
  <?php
    while($row = $result->fetch_assoc()) {  ?>
			<option value="<?php echo $row["cod_sede"]; ?>"><?php echo $row["nom_sede"]; ?></option>
  <?php }
    }
    else{
    	?>
<option value="">TODOS</option>
    	<?php
    }
?>