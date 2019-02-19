<option value="">Todos</option>
<?php
include '../../../config.php';
  //var_dump($_POST);

  $municipio = $_POST['municipio'];
  $tipo = $_POST['tipo'];
  $institucion = $_POST['institucion'];
  $sede = $_POST['sede'];
  $semana = $_POST['semana'];

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
  where
  sc.semana = '$semana'
  and s.cod_mun_sede = '$municipio'
  and sc.$tipo > 0
  and (sc.Etario1_$tipo > 0 || sc.Etario2_$tipo > 0 || sc.Etario3_$tipo > 0 )
  and s.cod_inst = '$institucion' ";




  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
  if($resultado->num_rows >= 1){
    while($row = $resultado->fetch_assoc()) { ?>
      <option value="<?php echo $row['cod_sede']; ?>"   <?php if($sede == $row['cod_sede'] ){echo " selected ";} ?>     ><?php echo $row['nom_sede']; ?></option>
    <?php }// Termina el while
  }//Termina el if que valida que si existan resultados
