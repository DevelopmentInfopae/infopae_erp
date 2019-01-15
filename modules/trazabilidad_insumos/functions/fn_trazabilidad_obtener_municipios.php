<option value="">Seleccione...</option>
<?php 
  require_once '../../../config.php';
  require_once '../../../db/conexion.php';


  $mes = $_POST['mes_tabla'];

  $consultarMunicipios = "SELECT 
						    ubicacion.CodigoDANE, ubicacion.Ciudad
						FROM
							insumosmov".$mes.$_SESSION['periodoActual']." AS denc
						        INNER JOIN
						    sedes".$_SESSION['periodoActual']." AS sede ON sede.cod_sede = denc.BodegaDestino
						        INNER JOIN
						    ubicacion ON ubicacion.CodigoDANE = sede.cod_mun_sede
						GROUP BY ubicacion.Ciudad;";
$resultadoMunicipios = $Link->query($consultarMunicipios);
if ($resultadoMunicipios->num_rows > 0) {
	while ($municipios = $resultadoMunicipios->fetch_assoc()) { ?>
		<option value="<?php echo $municipios['CodigoDANE'] ?>"><?php echo $municipios['Ciudad'] ?></option>
	<?php }
}