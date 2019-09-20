<option value="">Todos</option>
<?php
include '../../../config.php';
  //var_dump($_POST);

  $municipio = '';
  $tipo = '';
  $institucion = '';
  $semana = '';

  if(isset($_POST['municipio'])){
    $municipio = $_POST['municipio'];
  }

  if(isset($_POST['tipo'])){
    $tipo = $_POST['tipo'];
  }

  if(isset($_POST['institucion'])){
    $institucion = $_POST['institucion'];
  }

  if(isset($_POST['semana'])){
    $semana = $_POST['semana'];
  }

  require_once '../../../autentication.php';
  $periodoActual = $_SESSION['periodoActual'];
  require_once '../../../db/conexion.php';

  $Link = new mysqli($Hostname, $Username, $Password, $Database);
  if ($Link->connect_errno) {
    echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }
  $Link->set_charset("utf8");

  $consulta = " select distinct s.cod_sede, s.nom_sede
  from sedes$periodoActual s
  left join sedes_cobertura sc on s.cod_sede = sc.cod_sede
  where 1=1 ";

  if($semana != ''){
    $consulta = $consulta." and sc.semana = '$semana' ";
  }

  if($municipio != ''){
    $consulta = $consulta." and s.cod_mun_sede = '$municipio' ";
  }

  if($tipo != ''){
    $consulta = $consulta."  and sc.$tipo > 0 and (sc.Etario1_$tipo > 0 || sc.Etario2_$tipo > 0 || sc.Etario3_$tipo > 0 ) ";
  }

  if($institucion != ''){
    $consulta = $consulta."  and s.cod_inst = '$institucion' ";
  }









//echo $consulta;


  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
  if($resultado->num_rows >= 1){
    while($row = $resultado->fetch_assoc()) { ?>
      <option value="<?php echo $row['cod_sede']; ?>"><?php echo $row['nom_sede']; ?></option>
    <?php }// Termina el while
  }//Termina el if que valida que si existan resultados
