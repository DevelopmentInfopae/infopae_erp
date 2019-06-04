<?php
// $mesTablaAsistencia = $mes;
// $annoTablaAsistencia = $anno;

$consulta = "CREATE TABLE IF NOT EXISTS asistencia_det$mesTablaAsistencia$annoTablaAsistencia (tipo_doc varchar(10) DEFAULT NULL, num_doc varchar(24) DEFAULT NULL, fecha datetime DEFAULT NULL, mes varchar(2) DEFAULT NULL, semana varchar(5) DEFAULT NULL, dia varchar(5) DEFAULT NULL, asistencia smallint(1) DEFAULT '1', id_usuario int(11) NOT NULL, repite smallint(1) DEFAULT '0', consumio smallint(1) DEFAULT '0', repitio smallint(1) DEFAULT '0', KEY id_usuario (id_usuario) ) ENGINE=InnoDB DEFAULT CHARSET=utf8";

// echo $consulta;

$result = $Link->query($consulta) or die ('Creación de tablas de asistencia detallada mes año'. mysqli_error($Link));

$consulta = "CREATE TABLE IF NOT EXISTS asistencia_enc$mesTablaAsistencia$annoTablaAsistencia (mes varchar(2) DEFAULT NULL, semana varchar(5) DEFAULT NULL, dia varchar(5) DEFAULT NULL, estado smallint(1) DEFAULT '1', cod_sede bigint(20) DEFAULT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8"; 

// echo $consulta;

$result = $Link->query($consulta) or die ('Creación de tablas de asistencia encabezado mes año'. mysqli_error($Link));