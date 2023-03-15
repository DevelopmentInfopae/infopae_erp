<?php 
$idPerfil = $_SESSION['perfil'];
$permisos = [];
$consultaPermisos = " SELECT id, 
							entregas_biometricas, 
							instituciones, 
							archivos_globales, 
							titulares_derecho, 
							menus, 
							diagnostico_infraestructura, 
							dispositivos_biometricos, 
							despachos, 
							orden_compra, 
							inventario,
							entrega_complementos, 
							novedades, 
							nomina, 
							fqrs, 
							informes, 
							asistencia, 
							control_acceso, 
							procesos, 
							configuracion 
						FROM perfiles	
						WHERE id = $idPerfil; ";

$respuestaPermisos = $Link->query($consultaPermisos) or die ('Error al consultar los permisos. ' . mysqli_error($Link));						
if ($respuestaPermisos->num_rows > 0 ) {
	$dataPermisos = $respuestaPermisos->fetch_assoc();
	$permisos = $dataPermisos;
}
