<option value="">Seleccione uno</option>
<?php
include '../../../config.php';
require_once '../../../autentication.php';
$periodoActual = $_SESSION['periodoActual'];
require_once '../../../db/conexion.php';

  $Link = new mysqli($Hostname, $Username, $Password, $Database);
  if ($Link->connect_errno) {
    echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }
  $Link->set_charset("utf8");

  $codigoDepartamento = $_SESSION['p_CodDepartamento'];

  $consulta = " select distinct u.Ciudad, u.CodigoDANE from ubicacion u
  where u.CodigoDANE like '$codigoDepartamento%' and u.ETC = 0
  order by u.Ciudad asc ";

  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
  if($resultado->num_rows >= 1){
    $aux = 0;
    while($row = $resultado->fetch_assoc()) {?>
      <option value="<?php echo $row['CodigoDANE']; ?>"><?php echo strtoupper($row['Ciudad']); ?></option>
    <?php }// Termina el while
  }//Termina el if que valida que si existan resultados
