<?php 
    include '../../header.php';

    if ($permisos['configuracion'] == "0" || $permisos['configuracion'] == "1") {
      ?><script type="text/javascript">
          window.open('<?= $baseUrl ?>', '_self');
      </script>
    <?php exit(); }

    $titulo = 'Editar Permisos'; 

    $idPerfil = $_POST['id'];
    $informacion = [];

    $consultaPermisos = " SELECT 
    						p.nombre AS nombre,
    						p.entregas_biometricas AS entregas_biometricas,
    						p.instituciones AS instituciones,
    						p.archivos_globales AS archivos,
    						p.titulares_derecho AS titulares,
    						p.menus AS menus,
    						p.diagnostico_infraestructura AS diagnostico,
    						p.dispositivos_biometricos AS dispositivos,
    						p.despachos AS despachos,
    						p.orden_compra AS ordenes,
    						p.entrega_complementos AS entregas,
    						p.novedades AS novedades,
    						p.nomina AS nomina,
    						p.fqrs AS fqrs,
    						p.informes AS informes,
    						p.asistencia AS asistencia,
    						p.control_acceso AS control,
    						p.procesos AS procesos,
    						p.configuracion AS configuracion,
    						p.escritura AS escritura
    					FROM perfiles p
    					WHERE id = $idPerfil;
    					";
    $respuestaPermisos = $Link->query($consultaPermisos) or die ('Error al consultar los permisos. ' . mysqli_error($Link));	
    if ($respuestaPermisos->num_rows > 0) {
    	$dataPermisos = $respuestaPermisos->fetch_assoc();
    	$informacion = $dataPermisos;	
    }
    // var_dump($informacion);				

?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  	<div class="col-lg-12">
    	<h2><?= $titulo; ?></h2>
    	<ol class="breadcrumb">
      		<li>
        		<a href="<?= $baseUrl; ?>">Inicio</a>
      		</li>
      		<li>
        		<a href="<?= $baseUrl.'/modules/perfil_usuarios/index.php'; ?>">Perfil Usuarios</a>
      		</li>
      		<li class="active">
        		<strong><?php echo $titulo; ?></strong>
      		</li>
    	</ol>
  	</div>
</div>   

<div class="wrapper wrapper-content animated fadeInRight">
	<div class="row"> 
		<div class="col-lg-12">
			<div class="ibox float-e-margins">
				<div class="ibox-content contentBackground">
					<div class="row">
						<div class="col-lg-12 col-sm-12">
							<h2>Tipo Perfil : <?= $informacion['nombre']; ?></h2>
						</div>
					</div>
					<br><hr>
					<!-- modulo 1, 2 y 3-->
					<div class="row">
						<div class="col-lg-4 col-sm-12">
							<div class="row">
								<div class="col-lg-4 col-sm-6">
									<label><h4>Entregas Biometricas </h4></label>
								</div>
								<div class="col-lg-5 col-sm-6">
									<?php if ($idPerfil != "6" && $idPerfil != "7"): ?>
										<?php if ($informacion['entregas_biometricas'] == "2"): ?>
											<button type="button" class="btn btn-primary btn-outline btn-group btn-group-justified" id="entregas_biometricas" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['entregas_biometricas'] ?>, 'entregas_biometricas' )" >Lectura y escritura</button>
										<?php endif ?>
									<?php endif ?>
									<?php if ($informacion['entregas_biometricas'] == "1"): ?>
										<button type="button" class="btn btn-warning btn-outline btn-group btn-group-justified" id="entregas_biometricas" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['entregas_biometricas'] ?>, 'entregas_biometricas' )" >Lectura</button>
									<?php endif ?>
									<?php if ($informacion['entregas_biometricas'] == "0"): ?>
										<button type="button" class="btn btn-danger btn-outline  btn-group btn-group-justified" id="entregas_biometricas" value="<?= $informacion['entregas_biometricas'] ?>" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['entregas_biometricas'] ?>, 'entregas_biometricas' )">Inactivo</button>
									<?php endif ?>
								</div>
							</div>	
						</div>
						<div class="col-lg-4 col-sm-12">
							<div class="row">
								<div class="col-lg-4 col-sm-6">
									<label><h4>Instituciones </h4></label>
								</div>
								<div class="col-lg-5 col-sm-6">
									<?php if ($idPerfil != "6" && $idPerfil != "7"): ?>
										<?php if ($informacion['instituciones'] == "2"): ?>
											<button type="button" class="btn btn-primary btn-outline btn-group btn-group-justified" id="instituciones" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['instituciones'] ?>, 'instituciones' )" >Lectura y escritura</button>
										<?php endif ?>
									<?php endif ?>
									<?php if ($informacion['instituciones'] == "1"): ?>
										<button type="button" class="btn btn-warning btn-outline btn-group btn-group-justified" id="instituciones" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['instituciones'] ?>, 'instituciones' )" >Lectura</button>
									<?php endif ?>
									<?php if ($informacion['instituciones'] == "0"): ?>
										<button type="button" class="btn btn-danger btn-outline btn-group btn-group-justified" id="instituciones" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['instituciones'] ?>, 'instituciones' )">Inactivo</button>
									<?php endif ?>
								</div>
							</div>
						</div>
						<div class="col-lg-4 col-sm-12">
							<div class="row">
								<div class="col-lg-4 col-sm-6">
									<label><h4>Archivos Globales</h4></label>
								</div>
								<div class="col-lg-5 col-sm-6">
									<?php if ($idPerfil != "6" && $idPerfil != "7"): ?>
										<?php if ($informacion['archivos'] == "2"): ?>
											<button type="button" class="btn btn-primary btn-outline btn-group btn-group-justified" id="archivos" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['archivos'] ?>, 'archivos' )" >Lectura y escritura</button>
										<?php endif ?>
									<?php endif ?>
									<?php if ($informacion['archivos'] == "1"): ?>
										<button type="button" class="btn btn-warning btn-outline btn-group btn-group-justified" id="archivos" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['archivos'] ?>, 'archivos' )" >Lectura</button>
									<?php endif ?>
									<?php if ($informacion['archivos'] == "0"): ?>
										<button type="button" class="btn btn-danger btn-outline btn-group btn-group-justified" id="archivos" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['archivos'] ?>, 'archivos' )">Inactivo</button>
									<?php endif ?>
								</div>	
							</div>
						</div>
					</div>
					<hr>
					<!-- modulo 4, 5 y 6 -->
					<div class="row">
						<div class="col-lg-4 col-sm-12">
							<div class="row">
								<div class="col-lg-4 col-sm-6">
									<label><h4>Titulares Derecho</h4></label>
								</div>
								<div class="col-lg-5 col-sm-6">
									<?php if ($idPerfil != "6" && $idPerfil != "7"): ?>
										<?php if ($informacion['titulares'] == "2"): ?>
											<button type="button" class="btn btn-primary btn-outline btn-group btn-group-justified" id="titulares" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['titulares'] ?>, 'titulares' )">Lectura y escritura</button>
										<?php endif ?>	
									<?php endif ?>	
									<?php if ($informacion['titulares'] == "1"): ?>
										<button type="button" class="btn btn-warning btn-outline btn-group btn-group-justified" id="titulares" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['titulares'] ?>, 'titulares' )">Lectura</button>
									<?php endif ?>
									<?php if ($informacion['titulares'] == "0"): ?>
										<button type="button" class="btn btn-danger btn-outline btn-group btn-group-justified" id="titulares" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['titulares'] ?>, 'titulares' )">Inactivo</button>
									<?php endif ?>
								</div>
							</div>		
						</div>
						<div class="col-lg-4 col-sm-12">
							<div class="row">
								<div class="col-lg-4 col-sm-6">
									<label><h4>Menús</h4></label>
								</div>
								<div class="col-lg-5 col-sm-6">
									<?php if ($idPerfil != "6" && $idPerfil != "7"): ?>
										<?php if ($informacion['menus'] == "2"): ?>
											<button type="button" class="btn btn-primary btn-outline btn-group btn-group-justified" id="menus" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['menus'] ?>, 'menus' )">Lectura y escritura</button>
										<?php endif ?>
									<?php endif ?>
									<?php if ($informacion['menus'] == "1"): ?>
										<button type="button" class="btn btn-warning btn-outline btn-group btn-group-justified" id="menus" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['menus'] ?>, 'menus' )">Lectura</button>
									<?php endif ?>
									<?php if ($informacion['menus'] == "0"): ?>
										<button type="button" class="btn btn-danger btn-outline btn-group btn-group-justified" id="menus" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['menus'] ?>, 'menus' )">Inactivo</button>
									<?php endif ?>
								</div>	
							</div>
						</div>
						<div class="col-lg-4 col-sm-12">
							<div class="row">
								<div class="col-lg-4 col-sm-6">
									<label><h4>Diagnóstico Infraestructura</h4></label>
								</div>
								<div class="col-lg-5 col-sm-6">	
									<?php if ($idPerfil != "6" && $idPerfil != "7"): ?>
										<?php if ($informacion['diagnostico'] == "2"): ?>
											<button type="button" class="btn btn-primary btn-outline btn-group btn-group-justified" id="diagnostico" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['diagnostico']; ?>, 'diagnostico' )">Lectura y escritura</button>
										<?php endif ?>
									<?php endif ?>
									<?php if ($informacion['diagnostico'] == "1"): ?>
										<button type="button" class="btn btn-warning btn-outline btn-group btn-group-justified" id="diagnostico" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['diagnostico']; ?>, 'diagnostico' )">Lectura</button>
									<?php endif ?>
									<?php if ($informacion['diagnostico'] == "0"): ?>
										<button type="button" class="btn btn-danger btn-outline btn-group btn-group-justified" id="diagnostico" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['diagnostico']; ?>, 'diagnostico' )">Inactivo</button>
									<?php endif ?>
								</div>
							</div>		
						</div>
					</div>
					<hr>
					<!-- modulo 7, 8 y 9 -->
					<div class="row">
						<div class="col-lg-4 col-sm-12">
							<div class="row">
								<div class="col-lg-4 col-sm-6">
									<label><h4>Dispositivos Biométricos</h4></label>
								</div>
								<div class="col-lg-5 col-sm-6">
									<?php if ($idPerfil != "6" && $idPerfil != "7"): ?>
										<?php if ($informacion['dispositivos'] == "2"): ?>
											<button type="button" class="btn btn-primary btn-outline btn-group btn-group-justified" id="dispositivos" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['dispositivos']; ?>, 'dispositivos' )">Lectura y escritura</button>
										<?php endif ?>
									<?php endif ?>
									<?php if ($informacion['dispositivos'] == "1"): ?>
										<button type="button" class="btn btn-warning btn-outline btn-group btn-group-justified" id="dispositivos" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['dispositivos']; ?>, 'dispositivos' )">Lectura</button>
									<?php endif ?>
									<?php if ($informacion['dispositivos'] == "0"): ?>
										<button type="button" class="btn btn-danger btn-outline btn-group btn-group-justified" id="dispositivos" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['dispositivos']; ?>, 'dispositivos' )">Inactivo</button>
									<?php endif ?>
								</div>	
							</div>
						</div>
						<div class="col-lg-4 col-sm-12">
							<div class="row">
								<div class="col-lg-4 col-sm-6">
									<label><h4>Despachos</h4></label>
								</div>
								<div class="col-lg-5 col-sm-6">
									<?php if ($idPerfil != "6" && $idPerfil != "7"): ?>
										<?php if ($informacion['despachos'] == "2"): ?>
											<button type="button" class="btn btn-primary btn-outline btn-group btn-group-justified" id="despachos" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['despachos']; ?>, 'despachos' )">Lectura y escritura</button>
										<?php endif ?>	
									<?php endif ?>
									<?php if ($informacion['despachos'] == "1"): ?>
										<button type="button" class="btn btn-warning btn-outline btn-group btn-group-justified" id="despachos" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['despachos']; ?>, 'despachos' )">Lectura</button>
									<?php endif ?>
									<?php if ($informacion['despachos'] == "0"): ?>
										<button type="button" class="btn btn-danger btn-outline btn-group btn-group-justified" id="despachos" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['despachos']; ?>, 'despachos' )">Inactivo</button>
									<?php endif ?>
								</div>
							</div>		
						</div>
						<div class="col-lg-4 col-sm-12">
							<div class="row">
								<div class="col-lg-4 col-sm-6">
									<label><h4>Ordenes de Compra</h4></label>
								</div>
								<div class="col-lg-5 col-sm-6">
									<?php if ($idPerfil != "6" && $idPerfil != "7"): ?>
										<?php if ($informacion['ordenes'] == "2"): ?>
											<button type="button" class="btn btn-primary btn-outline btn-group btn-group-justified" id="ordenes" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['ordenes']; ?>, 'ordenes' )" >Lectura y escritura</button>
										<?php endif ?>
									<?php endif ?>
									<?php if ($informacion['ordenes'] == "1"): ?>
										<button type="button" class="btn btn-warning btn-outline btn-group btn-group-justified" id="ordenes" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['ordenes']; ?>, 'ordenes' )" >Lectura</button>
									<?php endif ?>
									<?php if ($informacion['ordenes'] == "0"): ?>
										<button type="button" class="btn btn-danger btn-outline btn-group btn-group-justified" id="ordenes" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['ordenes']; ?>, 'ordenes' )">Inactivo</button>
									<?php endif ?>
								</div>	
							</div>
						</div>
					</div>
					<hr>
					<!-- modulo 10, 11 y 12 -->
					<div class="row">
						<div class="col-lg-4 col-sm-12">
							<div class="row">
								<div class="col-lg-4 col-sm-6">
									<label><h4>Entrega de Complementos Alimentarios</h4></label>
								</div>
								<div class="col-lg-5 col-sm-6">	
									<?php if ($idPerfil != "6" && $idPerfil != "7"): ?>
										<?php if ($informacion['entregas'] == "2"): ?>
											<button type="button" class="btn btn-primary btn-outline btn-group btn-group-justified" id="entregas" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['entregas']; ?>, 'entregas' )">Lectura y escritura</button>
										<?php endif ?>
									<?php endif ?>
									<?php if ($informacion['entregas'] == "1"): ?>
										<button type="button" class="btn btn-warning btn-outline btn-group btn-group-justified" id="entregas" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['entregas']; ?>, 'entregas' )">Lectura</button>
									<?php endif ?>
									<?php if ($informacion['entregas'] == "0"): ?>
										<button type="button" class="btn btn-danger btn-outline btn-group btn-group-justified" id="entregas" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['entregas']; ?>, 'entregas' )" >Inactivo</button>
									<?php endif ?>
								</div>
							</div>		
						</div>
						<div class="col-lg-4 col-sm-12">
							<div class="row">
								<div class="col-lg-4 col-sm-6">
									<label><h4>Novedades</h4></label>
								</div>
								<div class="col-lg-5 col-sm-6">
									<?php if ($idPerfil != "6" && $idPerfil != "7"): ?>
										<?php if ($informacion['novedades'] == "2"): ?>
											<button type="button" class="btn btn-primary btn-outline btn-group btn-group-justified" id="novedades" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['novedades']; ?>, 'novedades' )">Lectura y escritura</button>
										<?php endif ?>
									<?php endif ?>
									<?php if ($informacion['novedades'] == "1"): ?>
										<button type="button" class="btn btn-warning btn-outline btn-group btn-group-justified" id="novedades" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['novedades']; ?>, 'novedades' )">Lectura</button>
									<?php endif ?>
									<?php if ($informacion['novedades'] == "0"): ?>
										<button type="button" class="btn btn-danger btn-outline btn-group btn-group-justified" id="novedades" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['novedades']; ?>, 'novedades' )" >Inactivo</button>
									<?php endif ?>
								</div>	
							</div>
						</div>
						<div class="col-lg-4 col-sm-12">
							<div class="row">
								<div class="col-lg-4 col-sm-6">
									<label><h4>Nómina</h4></label>
								</div>
								<div class="col-lg-5 col-sm-6">	
									<?php if ($idPerfil != "6" && $idPerfil != "7"): ?>
										<?php if ($informacion['nomina'] == "2"): ?>
											<button type="button" class="btn btn-primary btn-outline btn-group btn-group-justified" id="nomina" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['nomina']; ?>, 'nomina' )" >Lectura y escritura</button>
										<?php endif ?>
									<?php endif ?>
									<?php if ($informacion['nomina'] == "1"): ?>
										<button type="button" class="btn btn-warning btn-outline btn-group btn-group-justified" id="nomina" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['nomina']; ?>, 'nomina' )" >Lectura</button>
									<?php endif ?>
									<?php if ($informacion['nomina'] == "0"): ?>
										<button type="button" class="btn btn-danger btn-outline btn-group btn-group-justified" id="nomina" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['nomina']; ?>, 'nomina' )" >Inactivo</button>
									<?php endif ?>
								</div>
							</div>		
						</div>
					</div>
					<hr>	 
					<!-- modulo 13, 14 y 15 -->
					<div class="row">
						<div class="col-lg-4 col-sm-12">
							<div class="row">
								<div class="col-lg-4 col-sm-6">
									<label><h4>Fqrs</h4></label>
								</div>
								<div class="col-lg-5 col-sm-6">
									<?php if ($idPerfil != "6" && $idPerfil != "7"): ?>
										<?php if ($informacion['fqrs'] == "2"): ?>
											<button type="button" class="btn btn-primary btn-outline btn-group btn-group-justified" id="fqrs" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['fqrs']; ?>, 'fqrs' )" >Lectura y escritura</button>
										<?php endif ?>
									<?php endif ?>
									<?php if ($informacion['fqrs'] == "1"): ?>
										<button type="button" class="btn btn-warning btn-outline btn-group btn-group-justified" id="fqrs" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['fqrs']; ?>, 'fqrs' )" >Lectura</button>
									<?php endif ?>
									<?php if ($informacion['fqrs'] == "0"): ?>
										<button type="button" class="btn btn-danger btn-outline btn-group btn-group-justified" id="fqrs" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['fqrs']; ?>, 'fqrs' )" >Inactivo</button>
									<?php endif ?>
								</div>	
							</div>
						</div>
						<div class="col-lg-4 col-sm-12">
							<div class="row">
								<div class="col-lg-4 col-sm-6">
									<label><h4>Informes</h4></label>
								</div>
								<div class="col-lg-5 col-sm-6">	
									<?php if ($idPerfil != "6" && $idPerfil != "7"): ?>
										<?php if ($informacion['informes'] == "2"): ?>
											<button type="button" class="btn btn-primary btn-outline btn-group btn-group-justified" id="informes" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['informes']; ?>, 'informes' )" >Lectura y escritura</button>
										<?php endif ?>
									<?php endif ?>
									<?php if ($informacion['informes'] == "1"): ?>
										<button type="button" class="btn btn-warning btn-outline btn-group btn-group-justified" id="informes" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['informes']; ?>, 'informes' )" >Lectura</button>
									<?php endif ?>
									<?php if ($informacion['informes'] == "0"): ?>
										<button type="button" class="btn btn-danger btn-outline btn-group btn-group-justified" id="informes" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['informes']; ?>, 'informes' )" >Inactivo</button>
									<?php endif ?>
								</div>
							</div>		
						</div>
						<div class="col-lg-4 col-sm-12">
							<div class="row">
								<div class="col-lg-4 col-sm-6">
									<label><h4>Asistencia</h4></label>
								</div>
								<div class="col-lg-5 col-sm-6">
									<?php if ($idPerfil != "6" && $idPerfil != "7"): ?>
										<?php if ($informacion['asistencia'] == "2"): ?>
											<button type="button" class="btn btn-primary btn-outline btn-group btn-group-justified" id="asistencia" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['asistencia']; ?>, 'asistencia' )" >Lectura y escritura</button>
										<?php endif ?>
									<?php endif ?>
									<?php if ($informacion['asistencia'] == "1"): ?>
										<button type="button" class="btn btn-warning btn-outline btn-group btn-group-justified" id="asistencia" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['asistencia']; ?>, 'asistencia' )" >Lectura</button>
									<?php endif ?>
									<?php if ($informacion['asistencia'] == "0"): ?>
										<button type="button" class="btn btn-danger btn-outline btn-group btn-group-justified" id="asistencia" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['asistencia']; ?>, 'asistencia' )" >Inactivo</button>
									<?php endif ?>
								</div>	
							</div>
						</div>
					</div>
					<hr>
					<!-- modulo 16, 17 y 18 -->
					<div class="row">
						<div class="col-lg-4 col-sm-12">
							<div class="row">
								<div class="col-lg-4 col-sm-6">
									<label><h4>Control de Acceso</h4></label>
								</div>
								<div class="col-lg-5 col-sm-6">	
									<?php if ($idPerfil != "6" && $idPerfil != "7"): ?>
										<?php if ($informacion['control'] == "2"): ?>
											<button type="button" class="btn btn-primary btn-outline btn-group btn-group-justified" id="control" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['control']; ?>, 'control' )" >Lectura y escritura</button>
										<?php endif ?>
									<?php endif ?>
									<?php if ($informacion['control'] == "1"): ?>
										<button type="button" class="btn btn-warning btn-outline btn-group btn-group-justified" id="control" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['control']; ?>, 'control' )" >Lectura</button>
									<?php endif ?>
									<?php if ($informacion['control'] == "0"): ?>
										<button type="button" class="btn btn-danger btn-outline btn-group btn-group-justified" id="control" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['control']; ?>, 'control' )" >Inactivo</button>
									<?php endif ?>
								</div>
							</div>		
						</div>
						<div class="col-lg-4 col-sm-12">
							<div class="row">
								<div class="col-lg-4 col-sm-6">
									<label><h4>Procesos</h4></label>
								</div>
								<div class="col-lg-5 col-sm-6">
									<?php if ($idPerfil != "6" && $idPerfil != "7"): ?>
										<?php if ($informacion['procesos'] == "2"): ?>
											<button type="button" class="btn btn-primary btn-outline btn-group btn-group-justified" id="procesos" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['procesos']; ?>, 'procesos' )" >Lectura y escritura</button>
										<?php endif ?>
									<?php endif ?>
									<?php if ($informacion['procesos'] == "1"): ?>
										<button type="button" class="btn btn-warning btn-outline btn-group btn-group-justified" id="procesos" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['procesos']; ?>, 'procesos' )" >Lectura</button>
									<?php endif ?>
									<?php if ($informacion['procesos'] == "0"): ?>
										<button type="button" class="btn btn-danger btn-outline btn-group btn-group-justified" id="procesos" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['procesos']; ?>, 'procesos' )" >Inactivo</button>
									<?php endif ?>
								</div>	
							</div>
						</div>
						<div class="col-lg-4 col-sm-12">
							<div class="row">
								<div class="col-lg-4 col-sm-6">
									<label><h4>Configuración</h4></label>
								</div>
								<div class="col-lg-5 col-sm-6">	
									<?php if ($idPerfil != "6" && $idPerfil != "7"): ?>
										<?php if ($informacion['configuracion'] == "2"): ?>
											<button type="button" class="btn btn-primary btn-outline btn-group btn-group-justified" id="configuracion" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['configuracion']; ?>, 'configuracion' )" <?php if($idPerfil != "1" && $idPerfil != "0"){ echo "disabled = true"; } ?> >Lectura y escritura</button>
										<?php endif ?>
									<?php endif ?>
									<?php if ($informacion['configuracion'] == "1"): ?>
										<button type="button" class="btn btn-warning btn-outline btn-group btn-group-justified" id="configuracion" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['configuracion']; ?>, 'configuracion' )" <?php if($idPerfil != "1" && $idPerfil != "0"){ echo "disabled = true"; } ?> >Lectura</button>
									<?php endif ?>
									<?php if ($informacion['configuracion'] == "0"): ?>
										<button type="button" class="btn btn-danger btn-outline btn-group btn-group-justified" id="configuracion" onclick="confirmarCambio(<?= $idPerfil; ?> , <?= $informacion['configuracion']; ?>, 'configuracion' )" <?php if($idPerfil != "1" && $idPerfil != "0"){ echo "disabled = true"; } ?> >Inactivo</button>
									<?php endif ?>
								</div>
							</div>		
						</div>
					</div><hr>
				</div> <!-- contentBackground -->
			</div> <!-- float-e-margins -->
		</div> <!-- col-lg-12 -->
	</div> <!-- row -->	
</div> <!-- fadeInRight -->

 <!-- Ventana modal confirmar -->
<div class="modal inmodal fade" id="ventanaConfirmar" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-sm">
      	<div class="modal-content">
        	<div class="modal-header text-info" style="padding: 15px;">
          		<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
          		<h3><i class="fa fa-question-circle fa-lg" aria-hidden="true"></i> Información InfoPAE </h3>
        	</div>
        	<div class="modal-body">
            	<p class="text-center"></p>
        	</div>
        	<div class="modal-footer">
          		<input type="hidden" id="id">
          		<input type="hidden" id="estadoACambiar">
          		<input type="hidden" id="moduloACambiar">
          		<button type="button" class="btn btn-danger btn-outline btn-sm" data-dismiss="modal">Cancelar</button>
          		<button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" onclick="cambiarEstado();">Aceptar</button>
        	</div>
      	</div>
    </div>
</div>

<?php include '../../footer.php'; ?>

<script src="<?php echo $baseUrl; ?>/theme/js/jquery-3.1.1.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/bootstrap.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

<!-- Custom and plugin javascript -->
<script src="<?php echo $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script src="<?php echo $baseUrl; ?>/modules/perfil_usuarios/js/editarPermisos.js"></script>