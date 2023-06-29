<?php
set_time_limit (0);
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$tipoInventario = $_SESSION['p_inventory'];

// Se va ha buscar que existan cada una de las tablas, de lo contrario se crearan. Productos Mov
$consulta = " show tables like 'inventarios_bodegas_enc' ";
$result = $Link->query($consulta) or die ('Error al consultar existencia de tablas inventarios: '. mysqli_error($Link));
$existe = $result->num_rows;
if($existe <= 0){
	$consulta = " CREATE TABLE `inventarios_bodegas_enc` (
                                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                                    `municipio` INT(11) NOT NULL DEFAULT '0',
                                    `bodega` BIGINT(20) NOT NULL DEFAULT '0',
                                    `complemento` VARCHAR(20) NOT NULL DEFAULT '' COLLATE 'utf8_general_ci',
                                    `inventario_inicial` TINYINT(4) NOT NULL DEFAULT '0',
                                    `synchronization_date` DATE  NOT NULL,
                                PRIMARY KEY (`id`) USING BTREE
                                )
                                ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ";
	$result = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));

    $consulta2 = " CREATE TABLE `inventarios_bodegas_det` (
                                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                                    `id_bodega` INT(11) NULL DEFAULT NULL,
                                    `codigo` VARCHAR(20) NOT NULL DEFAULT '0' COLLATE 'utf8_general_ci',
                                    `cantidad` DECIMAL(28,8) NOT NULL DEFAULT '0.00000000',
                                    `fecha_entrada` DATETIME NULL DEFAULT NULL,
                                    `fecha_salida` DATETIME NULL DEFAULT NULL,
                                    PRIMARY KEY (`id`) USING BTREE,
                                    INDEX `id_bodega` (`id_bodega`) USING BTREE,
                                    CONSTRAINT `inventarios_bodegas_det_ibfk_1` FOREIGN KEY (`id_bodega`) REFERENCES `inventarios_bodegas_enc` (`id`) ON UPDATE RESTRICT ON DELETE RESTRICT
                                )
                                ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci  ";
    $result = $Link->query($consulta2) or die ('Unable to execute query. '. mysqli_error($Link));
}

if ($tipoInventario == 1) {
    // primero insertamos las bodegas en el encabezado
    $insercionesEncabezado = " INSERT INTO inventarios_bodegas_enc ( 
                                                municipio, 
                                                bodega, 
                                                inventario_inicial)  
                                        (SELECT CIUDAD, 
                                                ID, 
                                                '0' 
                                                FROM bodegas 
                                                WHERE ID NOT IN (SELECT bodega FROM inventarios_bodegas_enc )) ";
    $respuestaEncabezado = $Link->query($insercionesEncabezado) or die ('Error Ln 12');
    $respuesta = [  'estado' => '1',
                    'mensaje' => 'Bodegas Inicializadas Exitosamente.'
                    ];
    echo json_encode($respuesta);
    exit();
}else if($tipoInventario == 2){
    $consultaComplemento = " SELECT CODIGO FROM tipo_complemento WHERE ValorRacion > 0 ";
    $respuestaComplemento = $Link->query($consultaComplemento) or die ('Error al consultar los complemento LN 15');
    if ($respuestaComplemento->num_rows > 0) {
        while ($dataComplemento = $respuestaComplemento->fetch_assoc()) {
            $compt = $dataComplemento['CODIGO'];
            $insercionesEncabezado = " INSERT INTO inventarios_bodegas_enc ( 
                                                        municipio, 
                                                        bodega, 
                                                        complemento, 
                                                        inventario_inicial)  
                                                    (SELECT CIUDAD, 
                                                            ID,
                                                            '$compt', 
                                                            '0' 
                                                            FROM bodegas WHERE ID NOT IN (SELECT bodega FROM inventarios_bodegas_enc WHERE complemento = '$compt' ))  ";
            $respuestaEncabezado = $Link->query($insercionesEncabezado) or die ('Error Ln 35');
        }   
    }
    $respuesta = [  'estado' => '1',
                    'mensaje' => 'Bodegas Inicializadas Exitosamente. '
                ];
    echo json_encode($respuesta);
    exit();
}



