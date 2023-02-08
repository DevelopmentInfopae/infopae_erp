<option value="">Seleccione...</option>
<?php 
  require_once '../../../config.php';
  require_once '../../../db/conexion.php';


  $ruta = $_POST['ruta'];

  $consultarInstituciones = "SELECT 
                                codigo_inst, nom_inst 
                              FROM
                                instituciones 
                              WHERE cod_mun='$municipio'";
$resultadoInstituciones = $Link->query($consultarInstituciones);
if ($resultadoInstituciones->num_rows > 0) {
  
  while ($instituciones = $resultadoInstituciones->fetch_assoc()) { ?>
    <option value="<?php echo $instituciones['codigo_inst'] ?>"><?php echo $instituciones['nom_inst'] ?></option>
 
  <?php }
}