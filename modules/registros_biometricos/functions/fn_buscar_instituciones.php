<?php
include '../../../config.php';
require_once '../../../autentication.php';
require_once '../../../db/conexion.php';

//Se va a capturar el año actual para sacar la subcadena del periodo
//que permitira saber que tablas consultar de acuerdo al año.

//var_dump($_SESSION);
$periodoactual = $_SESSION['periodoActual'];


if(isset($_POST["municipio"]) && $_POST["municipio"] != "" ){

    $municipio = $_POST["municipio"];

$vsql = "select distinct cod_inst, nom_inst from sedes".$periodoactual." where cod_mun_sede = '$municipio' order by nom_inst asc";



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
