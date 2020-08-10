<?php
include '../../../config.php';
//var_dump($_POST);

$municipio = '';
$ruta = '';
$tipo = '';
$institucion = '';
$sede = '';
$consecutivo = 0;
$itemsActuales = array();

$semana = $_POST['semana'];
$municipio = $_POST['municipio'];
$ruta = $_POST['ruta'];
$tipo = $_POST['tipo'];
$institucion = $_POST['institucion'];
$sede = $_POST['sede'];
$consecutivo = $_POST['consecutivo'];

if(isset($_POST['itemsActuales'])){
  $itemsActuales = $_POST['itemsActuales'];
}




$periodoActual = $_SESSION['periodoActual'];

/*
select distinct s.cod_sede, u.Ciudad, s.nom_inst, s.nom_sede
from sedes16 s
left join ubicacion u on s.cod_mun_sede = u.CodigoDANE
left join sedes_cobertura sc on s.cod_sede = sc.cod_sede
where 1=1
and s.cod_mun_sede = '68020'
and sc.CAJMPS > 0

*/




$consulta=" select distinct s.cod_sede, u.Ciudad, s.nom_inst, s.nom_sede, s.cod_variacion_menu
from sedes$periodoActual s
left join ubicacion u on s.cod_mun_sede = u.CodigoDANE and u.ETC = 0
left join sedes_cobertura sc on s.cod_sede = sc.cod_sede
where 1=1 ";


if($semana != ''){
  $consulta = $consulta." and sc.semana = '$semana' ";
}
if($municipio != ''){
  $consulta = $consulta." and s.cod_mun_sede = '$municipio' ";
}
if($tipo != ''){
  $consulta = $consulta." and sc.$tipo > 0 ";
  $consulta = $consulta." and (sc.Etario1_$tipo > 0 || sc.Etario2_$tipo > 0 || sc.Etario3_$tipo > 0 ) ";
}
if($institucion != ''){
  $consulta = $consulta." and s.cod_inst = '$institucion' ";
}
if($sede != ''){
  $consulta = $consulta." and s.cod_sede = '$sede' ";
}

if($ruta != ''){
  $consulta = $consulta." and s.cod_sede in (select cod_sede from rutasedes WHERE IDRUTA = $ruta) ";
}





if(count($itemsActuales) > 0){
  for ($i=0; $i < count($itemsActuales) ; $i++) {
    $aux = $itemsActuales[$i];
    $consulta = $consulta." and s.cod_sede != '$aux' ";
  }
}








//echo "<br>".$consulta."<br>";

require_once '../../../autentication.php';
require_once '../../../db/conexion.php';

$Link = new mysqli($Hostname, $Username, $Password, $Database);
if ($Link->connect_errno) {
  echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
$Link->set_charset("utf8");

$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
  while($row = $resultado->fetch_assoc()) {
    $consecutivo++;
    ?>
    <tr>
      <td class="text-center"><input type="checkbox" class="i-checks" value="<?php echo $row['cod_sede']; ?>" data-variacion="<?php echo $row['cod_variacion_menu']; ?>" /></td>
      <td><input type="hidden" name="sede<?php echo $consecutivo; ?>" id="sede<?php echo $consecutivo; ?>" value="<?php echo $row['cod_sede']; ?>"><?php echo $row['Ciudad']; ?></td>
      <td><?php echo $row['nom_inst']; ?></td>
      <td><?php echo $row['nom_sede']; ?></td>
    </tr>
  <?php }// Termina el while
}//Termina el if que valida que si existan resultados
