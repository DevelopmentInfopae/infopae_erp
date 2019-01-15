<?php
include '../../../config.php';
require_once '../../../autentication.php';
require_once '../../../db/conexion.php';

$idRuta = '';
$consecutivo = 0;
$itemsActuales = array();

$idRuta = $_POST['idRuta'];

if(isset($_POST['itemsActuales'])){
  $itemsActuales = $_POST['itemsActuales'];
}

$periodoActual = $_SESSION['periodoActual'];

$consulta=" SELECT DISTINCT s.cod_sede, u.Ciudad, s.nom_inst, s.nom_sede FROM sedes$periodoActual s
LEFT JOIN ubicacion u ON s.cod_mun_sede = u.CodigoDANE and u.ETC = 0
WHERE 1=1 AND cod_sede in (select cod_sede from rutasedes WHERE IDRUTA = $idRuta) ";

if(count($itemsActuales) > 0){
  for ($i=0; $i < count($itemsActuales) ; $i++) {
    $aux = $itemsActuales[$i];
    $consulta = $consulta." and s.cod_sede != '$aux' ";
  }
}

$Link = new mysqli($Hostname, $Username, $Password, $Database);
if ($Link->connect_errno) { echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error; }
$Link->set_charset("utf8");
$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
  while($row = $resultado->fetch_assoc()) {
    $consecutivo++;
    ?>
    <tr>
      <td><input type="checkbox" value="<?php echo $row['cod_sede']; ?>"/></td>
      <td><input type="hidden" name="sede<?php echo $consecutivo; ?>" id="sede<?php echo $consecutivo; ?>" value="<?php echo $row['cod_sede']; ?>"><?php echo $row['Ciudad']; ?></td>
      <td><?php echo $row['nom_inst']; ?></td>
      <td><?php echo $row['nom_sede']; ?></td>
    </tr>
  <?php }// Termina el while
}//Termina el if que valida que si existan resultados
