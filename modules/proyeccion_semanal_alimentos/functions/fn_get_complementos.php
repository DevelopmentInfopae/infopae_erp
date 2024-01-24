<?php
    require_once '../../../db/conexion.php';

    $respuestaComplementos = $Link->query(" SELECT CODIGO FROM tipo_complemento WHERE ValorRacion > 0 ORDER BY CODIGO ");
    if ($respuestaComplementos->num_rows > 0) {
        $aux = 0;
        while ($dataComplementos = $respuestaComplementos->fetch_object()) {
            $consultaCobertura = " SELECT " .$dataComplementos->CODIGO. " AS comp 
                                    FROM sedes_cobertura
                                    WHERE mes = '" .$_POST['mes']. "' AND semana ='" .$_POST['semana']. "' AND " .$dataComplementos->CODIGO. ">0 "; 
            $respuestaCobertura = $Link->query($consultaCobertura) or die ('Error al consultar la cobertura Ln 11');
            if ($respuestaCobertura->num_rows > 0) {
                $complemento = $dataComplementos->CODIGO;
?>
                <div class="complemento">
                    <label>
                        <input  type="checkbox" 
                                class="complemento i-checks" 
                                id="complemento<?= $aux; ?>" 
                                name="complemento<?= $aux; ?>" 
                                value="<?= $complemento; ?>" style="margin-bottom: 5px;" checked>
                                <?= $complemento ?>
                    </label>
                </div>
<?php
                $aux++;
            }
        }                                                        
    }




  