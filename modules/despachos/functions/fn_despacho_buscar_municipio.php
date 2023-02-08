<option value="">Seleccione uno</option>
<?php
  include '../../../config.php';
  $tipo = $_POST['tipo'];
  $periodoActual = $_SESSION['periodoActual'];

  require_once '../../../db/conexion.php';
  $Link = new mysqli($Hostname, $Username, $Password, $Database);
  if ($Link->connect_errno) {
    echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }
  $Link->set_charset("utf8");

  $consulta = " SELECT DISTINCT u.Ciudad, u.CodigoDANE
                  FROM sedes_cobertura sc
                  LEFT JOIN sedes$periodoActual s on s.cod_sede = sc.cod_sede
                  LEFT JOIN ubicacion u on s.cod_mun_sede = u.CodigoDANE and u.ETC = 0
                  WHERE sc.$tipo > 0 ";

  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
  if($resultado->num_rows >= 1){
    $aux = 0;
    while($row = $resultado->fetch_assoc()) {?>
      <option value="<?php echo $row['CodigoDANE']; ?>"><?php echo $row['Ciudad']; ?></option>
    <?php }// Termina el while
  }//Termina el if que valida que si existan resultados
