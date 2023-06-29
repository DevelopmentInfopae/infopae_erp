<?php
include '../../../config.php';
require_once '../../../autentication.php';
require_once '../../../db/conexion.php';

$consecutivo = $_POST['consecutivo'];
$mes = $_POST['mes'];
$num_oco = $_POST['Num_OCO'];
$periodoActual = $_SESSION['periodoActual'];
$tabla = "orden_compra_enc$mes$periodoActual";

$consulta=" SELECT  DISTINCT s.cod_sede, u.Ciudad, s.nom_inst, s.nom_sede, s.cod_variacion_menu
                    FROM sedes$periodoActual s
                    LEFT JOIN ubicacion u on s.cod_mun_sede = u.CodigoDANE and u.ETC = 0
                    LEFT JOIN sedes_cobertura sc on s.cod_sede = sc.cod_sede
                    INNER JOIN $tabla enc ON enc.cod_Sede = s.cod_sede
                    WHERE 1=1 AND Num_OCO = '" .$num_oco. "'";

$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
   while($row = $resultado->fetch_assoc()) {
      $consecutivo++;
?>
      <tr>
         <td class="text-center"><input type="checkbox" class="i-checks" value="<?php echo $row['cod_sede']; ?>" data-variacion="<?php echo $row['cod_variacion_menu']; ?>" /></td>
         <td><input type="hidden" name="sede<?php echo $consecutivo; ?>" id="sede<?php echo $consecutivo; ?>" value="<?php echo $row['cod_sede']; ?>"><?php echo $row['Ciudad']; ?></td>
         <td><?php echo $row['nom_inst']; ?></td>
         <td><?php echo $row['nom_sede']; ?></td>
      </tr>
<?php 
   }// Termina el while
}//Termina el if que valida que si existan resultados
