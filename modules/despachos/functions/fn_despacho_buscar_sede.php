<option value="">Todos</option>
<?php
  include '../../../config.php';
  require_once '../../../db/conexion.php';
  require_once '../../../autentication.php';

  $periodoActual = $_SESSION['periodoActual'];
  $tipo = (isset($_POST['tipo'])) ? $_POST['tipo'] : '';
  $semana = (isset($_POST['semana'])) ? $_POST['semana'] : '';
  $municipio = (isset($_POST['municipio'])) ? $_POST['municipio'] : '';
  $institucion = (isset($_POST['institucion'])) ? $_POST['institucion'] : '';

  $consulta = "SELECT DISTINCT s.cod_sede, s.nom_sede FROM sedes$periodoActual s LEFT JOIN sedes_cobertura sc on s.cod_sede = sc.cod_sede WHERE 1=1";

  if($semana != ''){ $consulta = $consulta." AND sc.semana = '$semana'"; }
  if($municipio != ''){ $consulta = $consulta." AND s.cod_mun_sede = '$municipio'"; }
  if($tipo != ''){ $consulta = $consulta."  AND sc.$tipo > 0 AND (sc.Etario1_$tipo > 0 || sc.Etario2_$tipo > 0 || sc.Etario3_$tipo > 0 )"; }
  if($institucion != ''){ $consulta = $consulta."  AND s.cod_inst = '$institucion'"; }

  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
  if($resultado->num_rows >= 1){
    while($row = $resultado->fetch_assoc()) {
?>
      <option value="<?php echo $row['cod_sede']; ?>"><?php echo $row['nom_sede']; ?></option>
<?php
    }
  }
