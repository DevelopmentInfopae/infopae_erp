<option value="">Seleccione...</option>
<?php 
  require_once '../../../config.php';
  require_once '../../../db/conexion.php';


  $sede = $_POST['sedes'];

  $consultarRutas = "SELECT rs.IDRUTA as idruta, rs.cod_Sede as codsede, r.nombre as nombre
                     FROM 
                        rutasedes rs
                     INNER JOIN 
                        rutas r on rs.IDRUTA=r.Id
                     where 
                        rs.cod_Sede='$sede' ";
$resultadoRutas = $Link->query($consultarRutas);
if ($resultadoRutas->num_rows > 0) {
  while ($ruta = $resultadoRutas->fetch_assoc()) { ?>
    <option value="<?php echo $ruta['idruta'] ?>"><?php echo $ruta['nombre'] ?></option>
  <?php }
}

