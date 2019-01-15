<?php
include '../../../config.php';
    require_once '../../../db/conexion.php';

    //Se va a capturar el año actual para sacar la subcadena del periodo
    //que permitira saber que tablas consultar de acuerdo al año.

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
			if($_SESSION['perfil'] == 6){
				$rectorDocumento = $_SESSION['num_doc'];
				$vsql = "SELECT instituciones.codigo_inst as cod_inst, instituciones.nom_inst as nom_inst  from instituciones left join ubicacion on instituciones.cod_mun = ubicacion.CodigoDANE where cc_rector = $rectorDocumento limit 1";
			}
















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
