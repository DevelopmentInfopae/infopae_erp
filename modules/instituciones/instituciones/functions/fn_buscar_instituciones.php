<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

//Se va a capturar el año actual para sacar la subcadena del periodo que permitira saber que tablas consultar de acuerdo al año.
$periodoactual = $_SESSION['periodoActual'];
$modulo = (isset($_POST["modulo"]) && $_POST["modulo"] != "") ? $_POST["modulo"] : "";

if(isset($_POST["municipio"]) && $_POST["municipio"] != "" ){
  $municipio = mysqli_real_escape_string($Link, $_POST["municipio"]);

	$vsql = "SELECT codigo_inst AS cod_inst, nom_inst FROM instituciones WHERE cod_mun = '$municipio' ORDER BY nom_inst ASC;";
	$result = $Link->query($vsql);
	$Link->close();
?>

	<option value=""><?php echo ($modulo != "") ? "Seleccione uno" : "Todas" ; ?></option>
	<?php
		while($row = $result->fetch_assoc()) {  ?>
		<option value="<?php echo $row["cod_inst"]; ?>"><?php echo $row["nom_inst"]; ?></option>
<?php
		}
}
else{
?>
		<option value="">Todas</option>
<?php
}