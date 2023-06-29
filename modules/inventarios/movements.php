<?php
set_time_limit (0);
require_once '../../db/conexion.php';
require_once '../../config.php';
require_once 'functions/model/inventory_model.php';

$alimento = $_GET['codigo'];
$bodega = $_GET['bodega'];
$sinc = $_GET['sinc'];
$periodoActual = $_SESSION['periodoActual'];

/**************** FACTORES DE CONVERSION ****************/
$factores = search_factors($Link, $periodoActual);
/**************** FACTORES DE CONVERSION ****************/

/**************** DESCRIPCION Y UNIDAD  ************/
$dataNombreAlimento = search_descripcion($Link, $periodoActual, $alimento);
$nameProduct = $dataNombreAlimento['Descripcion'];
$unit = $dataNombreAlimento['NombreUnidad2'];
/**************** DESCRIPCION Y UNIDAD  ************/

$consultaSedes = " SELECT cod_sede FROM sedes$periodoActual WHERE cod_sede = $bodega ";
$respuestaSedes = $Link->query($consultaSedes) or die('Error al consultar ln 9');
if ($respuestaSedes->num_rows > 0) {  // aca validamos si es una bodega principal o una bodega sede, en esta condicion entra una bodega sede
    
    /******* REMISIONES ******/
    $remisiones = search_remision_warehouse_secundary($Link, $bodega, $alimento);
    /******* REMISIONES ******/

    /********** MINUTAS *************/
    $minutas = search_minuta_dayli($Link, $bodega, $alimento, $sinc, $periodoActual, $factores); 
    /********** MINUTAS *************/

    $array_resultante = array_merge($remisiones, $minutas);
    ksort($array_resultante);
}else{ // aca entramos cuando es una bodega principal
    
    /*********************** COMPRAS ************************/
    $compras = search_ord_warehouse_principaly($Link, $bodega, $alimento);
    /*********************** COMPRAS ************************/

    /************************ REMISIONES ********************/
    $remisiones = search_remision_warehouse_principaly($Link, $bodega, $alimento);
    /************************ REMISIONES ********************/

    $array_resultante = array_merge($compras, $remisiones); 
    ksort($array_resultante);
}
?>

<!-- Modal -->
<div class="modal fade" id="movements_modal" tabindex="-1" role="dialog" aria-labelledby="movements_modal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="movements_modal"><b>Movimiento por producto</b></h2>
                <h3 style="color : #31C95F;"><?= $alimento.' - '.$nameProduct.' - '. $unit ?></h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-3">
                        <b>Fecha</b>
                    </div>
                    <div class="col-sm-3">
                        <b>Documento</b>
                    </div>
                    <div class="col-sm-3 text-center">
                        <b>Entrada</b>
                    </div>
                    <div class="col-sm-3 text-center">
                        <b>Salida</b>
                    </div>
                </div>
                <?php
                    $total = 0;
                    foreach ($array_resultante as $key => $value) {
                ?>  
                    <div class="row">
                        <div class="col-sm-3">
                            <?= date('Y-m-d', strtotime($key))   ?>
                        </div>
                        <div class="col-sm-3">
                            <?= $value['Numero'] ?>
                        </div>
                        <div class="col-sm-3 text-center">
                            <?php if($value['tipo'] == 'e'): $total += $value['total'] ?>
                                <?= number_format($value['total'], 2, ',', '.')  ?>
                            <?php endif; ?>
                        </div>
                        <div class="col-sm-3 text-center">
                            <?php if($value['tipo'] == 's'): $total -= $value['total'] ?>
                                <?= number_format($value['total'], 2, ',', '.') ?>
                            <?php endif; ?>
                        </div>
                    </div>    
                <?php
                    }
                ?>
                <div class="row">
                    <div class="col-sm-3">
                        <b>Total</b>
                    </div>
                    <div class="col-sm-3">
                        
                    </div>
                    <div class="col-sm-6 text-center" style="border-top: 0.1rem solid;">
                        <b><?= number_format($total, 2, ',', '.')  ?></b>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><span class="fas fa-times"></span> Cerrar</button>
                <button type="button" class="btn btn-primary"><span class="fa fa-file-pdf-o"></span> Imprimir</button>
            </div>
        </div>
    </div>
</div>

<script>
	$(document).ready(function() {
		$('#movements_modal').modal('show');
	});
</script>