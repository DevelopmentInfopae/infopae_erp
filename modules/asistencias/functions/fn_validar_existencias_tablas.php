<?php
$mesTablaAsistencia = $mes;
$annoTablaAsistencia = $anno;
if(strlen($annoTablaAsistencia) > 2){
	$annoTablaAsistencia = substr($annoTablaAsistencia,2);
}

$consulta = " CREATE TABLE IF NOT EXISTS asistencia_det$mesTablaAsistencia$annoTablaAsistencia ( tipo_doc varchar(10) DEFAULT NULL, num_doc varchar(24) DEFAULT NULL, dia varchar(5) DEFAULT NULL, semana varchar(5) DEFAULT NULL, mes varchar(2) DEFAULT NULL, complemento varchar(10) DEFAULT NULL, fecha datetime DEFAULT NULL, id_usuario int(11) NOT NULL, asistencia smallint(1) DEFAULT '1', repite smallint(1) DEFAULT '0', consumio smallint(1) DEFAULT '0', repitio smallint(1) DEFAULT '0', UNIQUE KEY tipo_doc_num_doc_fecha_mes_semana_dia (tipo_doc,num_doc,mes,semana,dia,complemento) USING BTREE ) ENGINE=InnoDB DEFAULT CHARSET=utf8; ";

//echo "<br><br>$consulta<br><br>";

$result = $Link->query($consulta) or die ('Creación de tablas de asistencia detallada mes año'. mysqli_error($Link));

$consulta = " CREATE TABLE IF NOT EXISTS asistencia_enc$mesTablaAsistencia$annoTablaAsistencia ( dia varchar(5) DEFAULT NULL, semana varchar(5) DEFAULT NULL, mes varchar(2) DEFAULT NULL, cod_sede bigint(20) DEFAULT NULL, complemento varchar(10) DEFAULT NULL, estado smallint(1) DEFAULT '1', UNIQUE KEY cierre (dia,semana,mes,cod_sede,complemento) ) ENGINE=InnoDB DEFAULT CHARSET=utf8; ";

// echo $consulta;

$result = $Link->query($consulta) or die ('Creación de tablas de asistencia encabezado mes año'. mysqli_error($Link));