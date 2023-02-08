<option value="">Todos</option>
<?php
  //var_dump($_POST);
  include '../../../config.php';
  $municipio = $_POST['municipio'];
  $tipo = $_POST['tipo'];
  $institucion = $_POST['institucion'];

  echo $institucion;
  $periodoActual = $_SESSION['periodoActual'];
  require_once '../../../db/conexion.php';

  $Link = new mysqli($Hostname, $Username, $Password, $Database);
  if ($Link->connect_errno) {
    echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }
  $Link->set_charset("utf8");

  $consulta = " SELECT DISTINCT s.cod_inst, s.nom_inst
                  FROM sedes$periodoActual s
                  LEFT JOIN sedes_cobertura sc on s.cod_sede = sc.cod_sede
                  WHERE s.cod_mun_sede = '$municipio'
                  AND sc.$tipo > 0 ";

  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
  if($resultado->num_rows >= 1){
    while($row = $resultado->fetch_assoc()) { ?>
      <option value="<?php echo $row['cod_inst']; ?>" <?php if($institucion == $row['cod_inst']){echo " selected ";} ?> ><?php echo $row['nom_inst']; ?></option>
    <?php }// Termina el while
  }//Termina el if que valida que si existan resultados
