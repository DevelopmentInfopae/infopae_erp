<?php
    include '../../../config.php';
    require_once '../../../db/conexion.php';

    if (isset($_SESSION['annoactual']) && $_SESSION['annoactual']!= '') {
      $annoactual = $_SESSION['annoactual'];
    }
    else{
      $annoactual = date('Y');
    }

    $_SESSION['annoactual'] = $annoactual;
    $periodoactual = substr($annoactual, 2, 2);

    if(isset($_POST["municipio"]) && $_POST["municipio"] != "" ){
		$municipio = $_POST["municipio"];
		$vsql = "select distinct cod_inst, nom_inst from sedes".$periodoactual." where cod_mun_sede = '$municipio' order by nom_inst asc";

		if($_SESSION['perfil'] == "6"){
			$rectorDocumento = $_SESSION['num_doc'];
			$vsql = "SELECT instituciones.codigo_inst as cod_inst, instituciones.nom_inst as nom_inst  from instituciones left join ubicacion on instituciones.cod_mun = ubicacion.CodigoDANE where cc_rector = $rectorDocumento limit 1";
		}

        if ($_SESSION['perfil'] == "7") {
            $documentoCoordinador = $_SESSION['num_doc'];
            $vsql = "SELECT i.codigo_inst AS cod_inst, i.nom_inst AS nom_inst FROM instituciones i LEFT JOIN ubicacion u on i.cod_mun = u.CodigoDANE LEFT JOIN sedes$periodoactual s ON s.cod_inst = i.codigo_inst WHERE s.id_coordinador = $documentoCoordinador LIMIT 1";
        }
    // exit(var_dump($vsql));    

    $Link = new mysqli($Hostname, $Username, $Password, $Database);
    $result = $Link->query($vsql);
    $Link->close();

?>
<option value="">TODOS</option>
<?php

    while($row = $result->fetch_assoc()) {  ?>
			<option value="<?php echo $row["cod_inst"]; ?>"><?php echo utf8_encode($row["nom_inst"]); ?></option>
    <?php }

    }
    else{
    	?>
        <option value="">TODOS</option>
    	<?php
    }


?>
