<option value="">Todos</option>
<?php
include '../../../config.php';

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

  $consulta = " select distinct s.cod_inst, s.nom_inst
  from sedes$periodoActual s
  left join sedes_cobertura sc on s.cod_sede = sc.cod_sede
  where
  s.cod_mun_sede = '$municipio' ";

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
