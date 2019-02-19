<option value="">Seleccione una</option>

<?php
	require_once '../../../config.php';
	require_once '../../../autentication.php';
	require_once '../../../db/conexion.php';

	$periodoActual = mysqli_real_escape_string($Link, $_SESSION['periodoActual']);

	// $dato_municipio = $Link->query("SELECT CodMunicipio FROM parametros") or die(mysqli_error($Link));
	// if ($dato_municipio->num_rows > 0) { $municipio_defecto = $dato_municipio->fetch_array(); }

	$municipio = (isset($_POST['municipio']) && $_POST['municipio'] != '') ? mysqli_real_escape_string($Link, $_POST["municipio"]) : "";


	// $consulta = " select distinct s.cod_inst, s.nom_inst from sedes$periodoActual s where s.cod_mun_sede = '$municipio' order by s.nom_inst asc ";
	$consulta = "SELECT DISTINCT s.cod_inst, s.nom_inst FROM sedes_cobertura sc LEFT JOIN sedes$periodoActual s ON sc.cod_sede = s.cod_sede WHERE s.cod_mun_sede = '$municipio' ORDER BY s.nom_inst ASC ";
	$resultado = $Link->query($consulta) or die ('Unable to execute query - Buscando instituciones '. mysqli_error($Link));
	if($resultado->num_rows >= 1){
    while($row = $resultado->fetch_assoc()){
?>
	<option value="<?= $row['cod_inst']; ?>"><?= $row['nom_inst']; ?></option>
<?php
	        /*$codigo = $row['cod_inst'];
	        $nombre = $row['nom_inst'];
	        $respuesta .= "<option value=\"$codigo\">$nombre</option>";*/
	    }
	}


// echo json_encode(array("log"=>$log, "respuesta"=>$respuesta));
