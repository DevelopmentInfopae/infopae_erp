<option value="">Seleccione...</option>
<?php 
  require_once '../../../config.php';
  require_once '../../../db/conexion.php';
  $periodoActual = $_SESSION['periodoActual'];

  $institucion = $_POST['inst'];
  $consultarSedes = "SELECT 
                      cod_sede, nom_sede 
                    FROM
                      sedes$periodoActual
                    WHERE cod_inst='$institucion'";
$resultadoSedes = $Link->query($consultarSedes);
if ($resultadoSedes->num_rows > 0) {
  while ($sedes = $resultadoSedes->fetch_assoc()) { ?>
    <option value="<?php echo $sedes['cod_sede'] ?>"><?php echo $sedes['nom_sede'] ?></option>
  <?php }
}