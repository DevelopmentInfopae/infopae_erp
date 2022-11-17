<?php
	/*
	0	SuperAdministrador
	1	Administrador
	2	Operario
	3	Auxiliar
	4	Manipuladora
	5	Auditor
	6	Rector
	7	Coordinador
	8	Aux Asistencia
	*/
?>

<!-- manejo perfil superadministrador  -->
<?php if ($_SESSION['perfil'] == "0" && $permisos['id'] == "0"): ?>
	<li class="active">
		<a href="<?php echo $baseUrl; ?>"><i class="fa fa-home"></i> <span class="nav-label">Inicio</span></a>
	</li>
	<li>
		<a href="#"><i class="fas fa-fingerprint"></i> <span class="nav-label">Entregas Biometricas</span><span class="fa arrow"></span></a>
		<ul class="nav nav-second-level collapse">
			<li> <a href="<?php echo $baseUrl; ?>/modules/entregas_biometricas/dashboard.php">Dashboard</a> </li>
			<li> <a href="<?php echo $baseUrl; ?>/modules/entregas_biometricas/index.php">Registrar entregas vía QR - BarCode</a> 
			</li>
		</ul>
	</li>
	<li> 
		<a href="<?php echo $baseUrl; ?>/modules/instituciones/instituciones.php"><i class="fa fa-graduation-cap"></i> <span class="nav-label">Instituciones</span></a> 
	</li>
	<li> 
		<a href="<?php echo $baseUrl; ?>/modules/instituciones/sedes.php"><i class="fa fa-bank"></i> <span class="nav-label">Sedes educativas</span></a> 
	</li>
	<li> 
		<a href="<?php echo $baseUrl; ?>/modules/archivos"><i class="fa fa-folder-open"></i> <span class="nav-label">Archivos Globales</span></a> 
	</li>
	<li>
		<a href="#"><i class="fa fa-child"></i> <span class="nav-label">Titulares de Derecho</span><span class="fa arrow"></span></a>
		<ul class="nav nav-second-level collapse">
			<li> <a href="<?php echo $baseUrl; ?>/modules/titulares_derecho/index.php">Derecho</a> </li>
			<li> <a href="<?php echo $baseUrl; ?>/modules/suplentes/index.php">Suplentes</a> </li>
		</ul>
	</li>
	<li>
		<a href="#"><i class="fas fa-utensils"></i> <span class="nav-label">Menús</span><span class="fa arrow"></span></a>
		<ul class="nav nav-second-level collapse">
			<li> <a href="<?php echo $baseUrl; ?>/modules/menus2/ver_alimentos.php">Alimentos</a> </li>
			<li> <a href="<?php echo $baseUrl; ?>/modules/menus2/ver_preparaciones.php">Preparaciones</a> </li>
			<li> <a href="<?php echo $baseUrl; ?>/modules/menus2">Menús</a> </li>
			<li> <a href="<?php echo $baseUrl; ?>/modules/menu_valref">Aportes calóricos y nutricionales</a> </li>
		</ul>
	</li>
	<li>
		<a href="#"><i class="fa fa-bank"></i> <span class="nav-label">Diagnóstico Infraestructura</span><span class="fa arrow"></span></a>
		<ul class="nav nav-second-level collapse">
			<li> <a href="<?php echo $baseUrl; ?>/modules/infraestructuras/index.php">Diagnóstico Infraestructura</a> </li>
		</ul>
	</li>
	<li>
		<a href="#"><i class="fa fa-calculator"></i> <span class="nav-label">Dispositivos Biométricos</span><span class="fa arrow"></span></a>
		<ul class="nav nav-second-level collapse">
			<li> <a href="<?php echo $baseUrl; ?>/modules/dispositivos_biometricos/index.php">Dispositivos Biométricos</a> </li>
			<li><a href="<?php echo $baseUrl; ?>/modules/dispositivos_biometricos/formato_datos_personales.php">Formato Datos Personales</a></li>
		</ul>
	</li>
	<li>
		<a href="#"><i class="fa fa-truck"></i> <span class="nav-label">Despachos</span> <span class="fa arrow"></span></a>
		<ul class="nav nav-second-level collapse">
			<li><a href="<?php echo $baseUrl; ?>/modules/despachos/despachos.php">Alimentos</a></li>
			<li><a href="<?php echo $baseUrl; ?>/modules/despachos/editar.php">Edición alimentos</a></li>
			<li> <a href="<?php echo $baseUrl; ?>/modules/insumos2/despachos.php">Insumos</a> </li>
		</ul>
	</li>
	<li> 
		<a href="<?php echo $baseUrl; ?>/modules/ordenes_de_compra/ordenes_de_compra.php"><i class="fas fa-truck-loading"></i> <span class="nav-label">Ordenes de compra</span></a> 
	</li>
	<li>
		<a href="#"><i class="fas fa-book-open"></i> <span class="nav-label">Entregas de Complementos Alimentarios</span><span class="fa arrow"></span></a>
		<ul class="nav nav-second-level collapse">
			<li><a href="<?php echo $baseUrl; ?>/modules/consultas/consulta_resumida_entregas.php">Consulta resumida</a></li>
			<!-- <li><a href="<?php echo $baseUrl; ?>/modules/consultas/consulta_detallada_entregas.php">Consulta detallada</a></li> -->
			<li>
				<a href="#" id="damian">Impresión de planillas<span class="fa arrow"></span></a>
				<ul class="nav nav-third-level">
					<li> <a href="<?php echo $baseUrl; ?>/modules/impresion_planillas/control_asistencia.php">Control de asistencia</a> </li>
					<li> <a href="<?php echo $baseUrl; ?>/modules/impresion_planillas/certificados.php">Certificados por institución</a> </li>
					<li> <a href="<?php echo $baseUrl; ?>/modules/impresion_planillas/certificados_covid19.php">Certificado Rector COVID19</a> </li>
					<li> <a href="<?php echo $baseUrl; ?>/modules/impresion_planillas/certificados_bono.php">Certificado Bono</a> </li>
				</ul>
			</li>
			<li>
				<a href="#" id="damian">Procesar<span class="fa arrow"></span></a>
				<ul class="nav nav-third-level">
					<li> <a href="#" class="sinDesarrollar">Procesar entregas</a> </li>
					<!-- <li> <a href="#" class="sinDesarrollar">Entregas Detalladas</a> </li> -->
					<li> <a href="#" class="sinDesarrollar">Aplicar novedades a entregas</a> </li>
				</ul>
			</li>
			<li>
				<a href="#" id="damian">Importar<span class="fa arrow"></span></a>
				<ul class="nav nav-third-level">
					<li> <a href="#" class="sinDesarrollar">Entregas Resumidas desde CSV</a> </li>
					<li> <a href="#" class="sinDesarrollar">Desde USB/biométrico</a> </li>
					<li> <a href="#" class="sinDesarrollar">Desde servidor biométrico</a> </li>
				</ul>
			</li>
			<li><a href="<?php echo $baseUrl; ?>/modules/registros_biometricos/registros_biometricos.php">Ver registros biométricos</a></li>
			<li><a href="#" class="sinDesarrollar">Validar entregas biométricas</a></li>
		</ul>
	</li>
	<li>
		<a href="#"><i class="fas fa-bookmark"></i> <span class="nav-label">Novedades</span><span class="fa arrow"></span></a>
		<ul class="nav nav-second-level collapse">
			<li>
				<a href="<?php echo $baseUrl; ?>/modules/novedades_priorizacion/index.php">Priorización</a>
			</li>
			<li>
				<a href="#">Focalización <span class="fa arrow"></span></a>
				<ul class="nav nav-third-level">
					<li><a href="<?php echo $baseUrl; ?>/modules/novedades_ejecucion/index.php">Titulares</a></li>
					<li><a href="<?php echo $baseUrl; ?>/modules/novedades_ejecucion/suplentes.php">Suplentes</a></li>
					<li><a href="<?php echo $baseUrl; ?>/modules/novedades_ejecucion/repitentes.php">Repitentes</a></li>
				</ul>
			</li>
			<li>
				<a href="<?php echo $baseUrl; ?>/modules/intercambios/index.php">Menú</a>
			</li>
		</ul>
	</li>
	<li>
		<a href="<?= $baseUrl; ?>/modules/nomina"><i class="fas fa-hand-holding-usd"></i> <span class="nav-label"> Nómina </span></a>
	</li>
	<li>
		<a href="<?= $baseUrl; ?>/modules/fqrs/index.php"><i class="fa fa-question"></i> <span class="nav-label">FQRS</span></a>
	</li>
	<li>
		<a href="#"><i class="fas fa-layer-group"></i></i> <span class="nav-label">Informes</span><span class="fa arrow"></span></a>
		<ul class="nav nav-second-level collapse">
			<?php if( $_SESSION['perfil'] != 9 ){ ?>
				<li> <a href="<?php echo $baseUrl; ?>/modules/trazabilidad/index.php">Trazabilidad</a> </li>
				<li> <a href="<?php echo $baseUrl; ?>/modules/trazabilidad_insumos/index.php">Trazabilidad Insumos</a> </li>
			<?php } ?>
			<li> <a href="<?php echo $baseUrl; ?>/modules/estadisticas/index.php">Estadisticas</a></li>
			<li> <a href="<?php echo $baseUrl; ?>/modules/estadisticas_avanzadas/index.php">Estadisticas Avanzadas</a></li>
			<?php if( $_SESSION['perfil'] != 9 ){ ?>
				<li> <a href="<?= $baseUrl; ?>/modules/bitacora_usuarios/index.php">Bitácora de usuarios</a></li>
				<li> <a href="#" class="sinDesarrollar">Informe CHIP</a></li>
				<li> <a href="<?php echo $baseUrl; ?>/modules/informes/informe_alimentos.php">Informe de alimentos</a></li>
				<li> <a href="<?php echo $baseUrl; ?>/modules/insumos/insumos_proveedor.php">Informe de Insumos ordenados por proveedores</a> </li>
				<li> <a href="<?= $baseUrl; ?>/modules/informes/ordenes_compra.php">Ordenes de compra</a></li>
				<li> <a href="<?= $baseUrl; ?>/modules/inejecuciones/index.php">Informe Inejecuciones</a></li>
			<?php } ?>
		</ul>
	</li>
	<li>
		<a href="#"><i class="fa fa-child"></i> <span class="nav-label">Asistencias</span><span class="fa arrow"></span></a>
		<ul class="nav nav-second-level collapse">

			<?php if( $_SESSION['perfil'] == 0 || $_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 8 || $_SESSION['perfil'] == 3 ){ ?>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias">Toma de asistencia</a> </li>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/repitentes.php"> Selección de repitentes </a> </li>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/consumo.php"> Registro de consumos </a> </li>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/suplentes.php"> Selección de suplentes </a> </li>
			<?php } ?>

			<?php if( $_SESSION['perfil'] == 0 || $_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 5  || $_SESSION['perfil'] == 6 || $_SESSION['perfil'] == 7 ){ ?>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/informe_asistencia.php"> Informe de asistencia </a> </li>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/registro_biometrico.php"> Registro Biometrico </a> </li>
			<?php } ?>

			<?php if( $_SESSION['perfil'] == 0 || $_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 3 ){ ?>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/control_tablets.php"> Control de toma de asistencias </a> </li>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/control_biometrico.php"> Control del registro biometrico </a> </li>
			<?php } ?>

		</ul>
	</li>
	<li> 
		<a href="<?php echo $baseUrl; ?>/modules/control_acceso/listado.php"><i class="far fa-clock"></i> <span class="nav-label">Control de Acceso</span></a> 
	</li>
	<li>
		<a href="#"><i class="fas fa-cog"></i><span class="nav-label">Procesos</span><span class="fa arrow"></span></a>
		<ul class="nav nav-second-level collapse">
			<li><a href="#" class="sinDesarrollar">Copia de seguridad</a></li>
			<li><a href="#" class="sinDesarrollar">Bloqueo de período</a></li>
			<li><a href="#" class="sinDesarrollar">Calculo de edades de titulares</a></li>
		</ul>
	</li>
	<li>
		<a href="#"><i class="fas fa-tools"></i> <span class="nav-label">Configuración</span><span class="fa arrow"></span></a>
		<ul class="nav nav-second-level collapse">
			<li> <a href="<?php echo $baseUrl; ?>/modules/parametros">Parámetros generales</a> </li>
			<li> <a href="<?php echo $baseUrl; ?>/modules/dias_contrato">Días de contratos</a> </li>
			<li><a href="<?php echo $baseUrl; ?>/modules/complementos_alimentarios">Complementos alimentarios</a></li>
			<li><a href="<?php echo $baseUrl; ?>/modules/insumos">Insumos</a></li>
			<li><a href="<?= $baseUrl; ?>/modules/grupos_etarios">Grupos etarios</a></li>
			<li><a href="<?= $baseUrl; ?>/modules/cronograma">Cronograma</a></li>
			<li><a href="<?= $baseUrl; ?>/modules/noticias">Noticias</a></li>
			<li> <a href="<?php echo $baseUrl; ?>/modules/usuarios">Usuarios</a> </li>
			<li><a href="<?= $baseUrl; ?>/modules/proveedores">Proveedores</a></li>
			<li>
				<a href="#">Rutas <span class="fa arrow"></span></a>
				<ul class="nav nav-third-level">
					<li><a href="<?php echo $baseUrl; ?>/modules/rutas/rutas.php">Listar Rutas</a></li>
					<li><a href="<?php echo $baseUrl; ?>/modules/rutas/ruta_nuevo.php">Nueva Ruta</a></li>
				</ul>
			</li>
			<li> <a href="<?php echo $baseUrl; ?>/modules/bodegas">Bodegas</a> </li>
			<li><a href="<?= $baseUrl; ?>/modules/empleados">Empleados</a></li>
			<li><a href="<?= $baseUrl; ?>/modules/tipo_vehiculos">Tipo vehiculo</a></li>
			<li><a href="<?= $baseUrl; ?>/modules/tipo_despachos">Tipo alimentos</a></li>
			<li><a href="<?= $baseUrl; ?>/modules/tipo_documentos">Tipo documento</a></li>
			<li><a href="<?= $baseUrl; ?>/modules/discapacidad">Discapacidades</a></li>
			<li><a href="<?= $baseUrl; ?>/modules/estrato">Estrato</a></li>
			<li><a href="<?= $baseUrl; ?>/modules/etnia">Etnias</a></li>
			<li><a href="<?= $baseUrl; ?>/modules/grados">Grados</a></li>
			<li><a href="<?= $baseUrl; ?>/modules/jornadas">Jornada</a></li>
			<li><a href="<?= $baseUrl; ?>/modules/poblacion_victima">Población victima</a></li>
			<li><a href="<?= $baseUrl; ?>/modules/bancos/index.php">Bancos</a></li>
			<li><a href="<?= $baseUrl; ?>/modules/manipuladoras_valores_nomina/index.php">Manipuladora valores nómina</a></li>
			<li><a href="<?= $baseUrl; ?>/modules/nomina_entidad/index.php">Nómina Entidad</a></li>
			<li><a href="<?= $baseUrl; ?>/modules/nomina_riesgos/index.php">Nómina Riesgos</a></li>
			<li><a href="<?= $baseUrl; ?>/modules/parametros_manipuladoras/index.php">Parámetros Manipuladoras</a></li>
			<li><a href="<?= $baseUrl; ?>/modules/parametros_nomina/index.php">Parámetros Nómina</a></li>
			<li><a href="<?= $baseUrl; ?>/modules/parametros_infraestructura/index.php">Parámetros Infraestructura</a></li>
			<li><a href="<?= $baseUrl; ?>/modules/prioridad_caracterizacion/index.php">Prioridad Caracterización</a></li>
			<li><a href="<?= $baseUrl; ?>/modules/tipo_casosfqrs/index.php">Tipo Caso Fqrs</a></li>
			<li><a href="<?= $baseUrl; ?>/modules/tipo_personafqrs/index.php">Tipo Persona Fqrs</a></li>
			<li><a href="<?= $baseUrl; ?>/modules/variacion_menu/index.php">Variación Menú</a></li>
			<li><a href="<?= $baseUrl; ?>/modules/perfil_usuarios/index.php">Perfil Usuarios</a></li>
		</ul>
	</li>
<?php endif ?>

<!-- manejo perfil administrador  -->
<?php if ($_SESSION['perfil'] == "1" && $permisos['id'] == "1"): ?>
	<li class="active">
		<a href="<?php echo $baseUrl; ?>"><i class="fa fa-home"></i> <span class="nav-label">Inicio</span></a>
	</li>
	<?php if ($permisos['entregas_biometricas'] == "1" || $permisos['entregas_biometricas'] == "2"): ?>
		<li>
			<a href="#"><i class="fas fa-fingerprint"></i> <span class="nav-label">Entregas Biometricas</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li> <a href="<?php echo $baseUrl; ?>/modules/entregas_biometricas/dashboard.php">Dashboard</a> </li>
				<?php if ($permisos['entregas_biometricas'] == "2"): ?>
					<li> <a href="<?php echo $baseUrl; ?>/modules/entregas_biometricas/index.php"> Registrar entregas vía QR - BarCode</a> 
					</li>
				<?php endif ?>
			</ul>
		</li>
	<?php endif ?>
	<?php if ($permisos['instituciones'] == "1" || $permisos['instituciones'] == "2"): ?>
		<li> 
			<a href="<?php echo $baseUrl; ?>/modules/instituciones/instituciones.php"><i class="fa fa-graduation-cap"></i> <span class="nav-label">Instituciones</span></a> 
		</li>
		<li> 
			<a href="<?php echo $baseUrl; ?>/modules/instituciones/sedes.php"><i class="fa fa-bank"></i> <span class="nav-label">Sedes educativas</span></a> 
		</li>
	<?php endif ?>
	<?php if ($permisos['archivos_globales'] == "1" || $permisos['archivos_globales'] == "2"): ?>
		<li> 
			<a href="<?php echo $baseUrl; ?>/modules/archivos"><i class="fa fa-folder-open"></i> <span class="nav-label">Archivos Globales</span></a> 
		</li>
	<?php endif ?>
	<?php if ($permisos['titulares_derecho'] == "1" || $permisos['titulares_derecho'] == "2"): ?>
		<li>
			<a href="#"><i class="fa fa-child"></i> <span class="nav-label">Titulares de Derecho</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li> <a href="<?php echo $baseUrl; ?>/modules/titulares_derecho/index.php">Derecho</a> </li>
				<li> <a href="<?php echo $baseUrl; ?>/modules/suplentes/index.php">Suplentes</a> </li>
			</ul>
		</li>
	<?php endif ?>
	<?php if ($permisos['menus'] == "1" || $permisos['menus'] == "2"): ?>
		<li>
			<a href="#"><i class="fas fa-utensils"></i> <span class="nav-label">Menús</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li> <a href="<?php echo $baseUrl; ?>/modules/menus2/ver_alimentos.php">Alimentos</a> </li>
				<li> <a href="<?php echo $baseUrl; ?>/modules/menus2/ver_preparaciones.php">Preparaciones</a> </li>
				<li> <a href="<?php echo $baseUrl; ?>/modules/menus2">Menús</a> </li>
				<li> <a href="<?php echo $baseUrl; ?>/modules/menu_valref">Aportes calóricos y nutricionales</a> </li>
			</ul>
		</li>
	<?php endif ?>
	<?php if ($permisos['diagnostico_infraestructura'] == "1" || $permisos['diagnostico_infraestructura'] == "2"): ?>
		<li>
			<a href="#"><i class="fa fa-bank"></i> <span class="nav-label">Diagnóstico Infraestructura</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li> <a href="<?php echo $baseUrl; ?>/modules/infraestructuras/index.php">Diagnóstico Infraestructura</a> </li>
			</ul>
		</li>
	<?php endif ?>
	<?php if ($permisos['dispositivos_biometricos'] == "1" || $permisos['dispositivos_biometricos'] == "2"): ?>
		<li>
			<a href="#"><i class="fa fa-calculator"></i> <span class="nav-label">Dispositivos Biométricos</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li> <a href="<?php echo $baseUrl; ?>/modules/dispositivos_biometricos/index.php">Dispositivos Biométricos</a> </li>
				<li><a href="<?php echo $baseUrl; ?>/modules/dispositivos_biometricos/formato_datos_personales.php">Formato Datos Personales</a></li>
			</ul>
		</li>
	<?php endif ?>
	<?php if ($permisos['despachos'] == "1" || $permisos['despachos'] == "2"): ?>
		<li>
			<a href="#"><i class="fa fa-truck"></i> <span class="nav-label">Despachos</span> <span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li><a href="<?php echo $baseUrl; ?>/modules/despachos/despachos.php">Alimentos</a></li>
				<?php if ($permisos['despachos'] == "2"): ?>
					<li><a href="<?php echo $baseUrl; ?>/modules/despachos/editar.php">Edición alimentos</a></li>
				<?php endif ?>
				<li> <a href="<?php echo $baseUrl; ?>/modules/insumos2/despachos.php">Insumos</span></a> </li>
			</ul>
		</li>
	<?php endif ?>
	<?php if ($permisos['orden_compra'] == "1" || $permisos['orden_compra'] == "2"): ?>
		<li> 
			<a href="<?php echo $baseUrl; ?>/modules/ordenes_de_compra/ordenes_de_compra.php"><i class="fas fa-truck-loading"></i> <span class="nav-label">Ordenes de compra</span></a> 
		</li>
	<?php endif ?>
	<?php if ($permisos['entrega_complementos'] == "1" || $permisos['entrega_complementos'] == "2"): ?>
		<li>
			<a href="#"><i class="fas fa-book-open"></i><span class="nav-label">Entregas de Complementos Alimentarios</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li><a href="<?php echo $baseUrl; ?>/modules/consultas/consulta_resumida_entregas.php">Consulta resumida</a></li>
				<li>
					<a href="#" id="damian">Impresión de planillas<span class="fa arrow"></span></a>
					<ul class="nav nav-third-level">
						<li> <a href="<?php echo $baseUrl; ?>/modules/impresion_planillas/control_asistencia.php">Control de asistencia</a> </li>
						<li> <a href="<?php echo $baseUrl; ?>/modules/impresion_planillas/certificados.php">Certificados por institución</a> </li>
						<li> <a href="<?php echo $baseUrl; ?>/modules/impresion_planillas/certificados_covid19.php">Certificado Rector COVID19</a> </li>
						<li> <a href="<?php echo $baseUrl; ?>/modules/impresion_planillas/certificados_bono.php">Certificado Bono</a> </li>
					</ul>
				</li>
				<li>
					<a href="#" id="damian">Procesar<span class="fa arrow"></span></a>
					<ul class="nav nav-third-level">
						<li> <a href="#" class="sinDesarrollar">Procesar entregas</a> </li>
						<!-- <li> <a href="#" class="sinDesarrollar">Entregas Detalladas</a> </li> -->
						<li> <a href="#" class="sinDesarrollar">Aplicar novedades a entregas</a> </li>
					</ul>
				</li>
				<li>
					<a href="#" id="damian">Importar<span class="fa arrow"></span></a>
					<ul class="nav nav-third-level">
						<li> <a href="#" class="sinDesarrollar">Entregas Resumidas desde CSV</a> </li>
						<li> <a href="#" class="sinDesarrollar">Desde USB/biométrico</a> </li>
						<li> <a href="#" class="sinDesarrollar">Desde servidor biométrico</a> </li>
					</ul>
				</li>
				<li><a href="<?php echo $baseUrl; ?>/modules/registros_biometricos/registros_biometricos.php">Ver registros biométricos</a></li>
				<li><a href="#" class="sinDesarrollar">Validar entregas biométricas</a></li>
			</ul>
		</li>
	<?php endif ?>
	<?php if ($permisos['novedades'] == "1" || $permisos['novedades'] == "2"): ?>
		<li>
			<a href="#"><i class="fas fa-bookmark"></i> <span class="nav-label">Novedades</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li>
					<a href="<?php echo $baseUrl; ?>/modules/novedades_priorizacion/index.php">Priorización</a>
				</li>
				<li>
					<a href="#">Focalización<span class="fa arrow"></span></a>
					<ul class="nav nav-third-level">
						<li><a href="<?php echo $baseUrl; ?>/modules/novedades_ejecucion/index.php">Titulares</a></li>
						<?php if ($permisos['novedades'] == "2"): ?>
							<li><a href="<?php echo $baseUrl; ?>/modules/novedades_ejecucion/suplentes.php">Suplentes</a></li>
							<li><a href="<?php echo $baseUrl; ?>/modules/novedades_ejecucion/repitentes.php">Repitentes</a></li>
						<?php endif ?>
					</ul>
				</li>
				<li>
					<a href="<?php echo $baseUrl; ?>/modules/intercambios/index.php">Menú</a>
				</li>
			</ul>
		</li>
	<?php endif ?>
	<?php if ($permisos['nomina'] == "1" || $permisos['nomina'] == "2"): ?>
		<li>
			<a href="<?= $baseUrl; ?>/modules/nomina"><i class="fas fa-hand-holding-usd"></i> <span class="nav-label"> Nómina </span></a>
		</li>
	<?php endif ?>
	<?php if ($permisos['fqrs'] == "1" || $permisos['fqrs'] == "2"): ?>
		<li>
			<a href="<?= $baseUrl; ?>/modules/fqrs/index.php"><i class="fa fa-question"></i> <span class="nav-label">FQRS</span></a>
		</li>
	<?php endif ?>
	<?php if ($permisos['informes'] == "1" || $permisos['informes'] == "2"): ?>
		<li>
			<a href="#"><i class="fas fa-layer-group"></i></i> <span class="nav-label">Informes</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
			<?php if( $_SESSION['perfil'] != 9 ){ ?>
				<li> <a href="<?php echo $baseUrl; ?>/modules/trazabilidad/index.php">Trazabilidad</a> </li>
				<li> <a href="<?php echo $baseUrl; ?>/modules/trazabilidad_insumos/index.php">Trazabilidad Insumos</a> </li>
			<?php } ?>
				<li> <a href="<?php echo $baseUrl; ?>/modules/estadisticas/index.php">Estadisticas</a></li>
				<li> <a href="<?php echo $baseUrl; ?>/modules/estadisticas_avanzadas/index.php">Estadisticas Avanzadas</a></li>
			<?php if( $_SESSION['perfil'] != 9 ){ ?>
				<li> <a href="<?= $baseUrl; ?>/modules/bitacora_usuarios/index.php">Bitácora de usuarios</a></li>
				<li> <a href="#" class="sinDesarrollar">Informe CHIP</a></li>
				<li> <a href="<?php echo $baseUrl; ?>/modules/informes/informe_alimentos.php">Informe de alimentos</a></li>
				<li> <a href="<?php echo $baseUrl; ?>/modules/insumos/insumos_proveedor.php">Informe de Insumos ordenados por proveedores</a> </li>
				<li> <a href="<?= $baseUrl; ?>/modules/informes/ordenes_compra.php">Ordenes de compra</a></li>
				<li> <a href="<?= $baseUrl; ?>/modules/inejecuciones/index.php">Informe Inejecuciones</a></li>
			<?php } ?>
			</ul>
		</li>
	<?php endif ?>
	<?php if ($permisos['asistencia'] == "1" || $permisos['asistencia'] == "2"): ?>
		<li>
			<a href="#"><i class="fa fa-child"></i> <span class="nav-label">Asistencias</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
			<?php if( $_SESSION['perfil'] == 0 || $_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 8 || $_SESSION['perfil'] == 3 ){ ?>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias">Toma de asistencia</a> </li>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/repitentes.php"> Selección de repitentes </a> </li>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/consumo.php"> Registro de consumos </a> </li>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/suplentes.php"> Selección de suplentes </a> </li>
			<?php } ?>

			<?php if( $_SESSION['perfil'] == 0 || $_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 5  || $_SESSION['perfil'] == 6 || $_SESSION['perfil'] == 7 ){ ?>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/informe_asistencia.php"> Informe de asistencia </a> </li>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/registro_biometrico.php"> Registro Biometrico </a> </li>
			<?php } ?>

			<?php if( $_SESSION['perfil'] == 0 || $_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 3 ){ ?>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/control_tablets.php"> Control de toma de asistencias </a> </li>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/control_biometrico.php"> Control del registro biometrico </a> </li>
			<?php } ?>
			</ul>
		</li>
	<?php endif ?>
	<?php if ($permisos['control_acceso'] == "1" || $permisos['control_acceso'] == "2"): ?>
		<li> 
			<a href="<?php echo $baseUrl; ?>/modules/control_acceso/listado.php"><i class="far fa-clock"></i><span class="nav-label">Control de Acceso</span></a> 
		</li>
	<?php endif ?>
	<?php if ($permisos['procesos'] == "1" || $permisos['procesos'] == "2"): ?>
		<li>
			<a href="#"><i class="fas fa-cog"></i><span class="nav-label">Procesos</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li><a href="#" class="sinDesarrollar">Copia de seguridad</a></li>
				<li><a href="#" class="sinDesarrollar">Bloqueo de período</a></li>
				<li><a href="#" class="sinDesarrollar">Calculo de edades de titulares</a></li>
			</ul>
		</li>
	<?php endif ?>
	<?php if ($permisos['configuracion'] == "2"): ?>
		<li>
			<a href="#"><i class="fas fa-tools"></i> <span class="nav-label">Configuración</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li> <a href="<?php echo $baseUrl; ?>/modules/parametros">Parámetros generales</a> </li>
				<li> <a href="<?php echo $baseUrl; ?>/modules/dias_contrato">Días de contratos</a> </li>
				<li><a href="<?php echo $baseUrl; ?>/modules/complementos_alimentarios">Complementos alimentarios</a></li>
				<li><a href="<?php echo $baseUrl; ?>/modules/insumos">Insumos</a></li>
				<li><a href="<?= $baseUrl; ?>/modules/grupos_etarios">Grupos etarios</a></li>
				<li><a href="<?= $baseUrl; ?>/modules/cronograma">Cronograma</a></li>
				<li><a href="<?= $baseUrl; ?>/modules/noticias">Noticias</a></li>
				<li> <a href="<?php echo $baseUrl; ?>/modules/usuarios">Usuarios</a> </li>
				<li><a href="<?= $baseUrl; ?>/modules/proveedores">Proveedores</a></li>
				<li>
					<a href="#">Rutas<span class="fa arrow"></span></a>
					<ul class="nav nav-third-level">
						<li><a href="<?php echo $baseUrl; ?>/modules/rutas/rutas.php">Listar Rutas</a></li>
						<li><a href="<?php echo $baseUrl; ?>/modules/rutas/ruta_nuevo.php">Nueva Ruta</a></li>
					</ul>
				</li>
				<li> <a href="<?php echo $baseUrl; ?>/modules/bodegas">Bodegas</a> </li>
				<li><a href="<?= $baseUrl; ?>/modules/empleados">Empleados</a></li>
				<li><a href="<?= $baseUrl; ?>/modules/tipo_vehiculos">Tipo vehiculo</a></li>
				<li><a href="<?= $baseUrl; ?>/modules/tipo_despachos">Tipo alimentos</a></li>
				<li><a href="<?= $baseUrl; ?>/modules/tipo_documentos">Tipo documento</a></li>
				<li><a href="<?= $baseUrl; ?>/modules/discapacidad">Discapacidades</a></li>
				<li><a href="<?= $baseUrl; ?>/modules/estrato">Estrato</a></li>
				<li><a href="<?= $baseUrl; ?>/modules/etnia">Etnias</a></li>
				<li><a href="<?= $baseUrl; ?>/modules/grados">Grados</a></li>
				<li><a href="<?= $baseUrl; ?>/modules/jornadas">Jornada</a></li>
				<li><a href="<?= $baseUrl; ?>/modules/poblacion_victima">Población victima</a></li>
				<li><a href="<?= $baseUrl; ?>/modules/bancos/index.php">Bancos</a></li>
				<li><a href="<?= $baseUrl; ?>/modules/manipuladoras_valores_nomina/index.php">Manipuladora valores nómina</a></li>
				<li><a href="<?= $baseUrl; ?>/modules/nomina_entidad/index.php">Nómina Entidad</a></li>
				<li><a href="<?= $baseUrl; ?>/modules/nomina_riesgos/index.php">Nómina Riesgos</a></li>
				<li><a href="<?= $baseUrl; ?>/modules/parametros_manipuladoras/index.php">Parámetros Manipuladoras</a></li>
				<li><a href="<?= $baseUrl; ?>/modules/parametros_nomina/index.php">Parámetros Nómina</a></li>
				<li><a href="<?= $baseUrl; ?>/modules/parametros_infraestructura/index.php">Parámetros Infraestructura</a></li>
				<li><a href="<?= $baseUrl; ?>/modules/prioridad_caracterizacion/index.php">Prioridad Caracterización</a></li>
				<li><a href="<?= $baseUrl; ?>/modules/tipo_casosfqrs/index.php">Tipo Caso Fqrs</a></li>
				<li><a href="<?= $baseUrl; ?>/modules/tipo_personafqrs/index.php">Tipo Persona Fqrs</a></li>
				<li><a href="<?= $baseUrl; ?>/modules/variacion_menu/index.php">Variación Menú</a></li>
				<li><a href="<?= $baseUrl; ?>/modules/perfil_usuarios/index.php">Perfil Usuarios</a></li>
			</ul>
		</li>
	<?php endif ?>
<?php endif ?>

<!-- manejo perfil operador  -->
<?php if ($_SESSION['perfil'] == "2" && $permisos['id'] == "2"): ?>
	<li class="active">
		<a href="<?php echo $baseUrl; ?>"><i class="fa fa-home"></i> <span class="nav-label">Inicio</span></a>
	</li>
	<?php if ($permisos['entregas_biometricas'] == "1" || $permisos['entregas_biometricas'] == "2"): ?>
		<li>
			<a href="#"><i class="fas fa-fingerprint"></i> <span class="nav-label">Entregas Biometricas</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li> <a href="<?php echo $baseUrl; ?>/modules/entregas_biometricas/dashboard.php">Dashboard</a> </li>
				<?php if ($permisos['entregas_biometricas'] == "2"): ?>
					<li> <a href="<?php echo $baseUrl; ?>/modules/entregas_biometricas/index.php">Registrar entregas vía QR - BarCode</a> 
					</li>
				<?php endif ?>
			</ul>
		</li>
	<?php endif ?>
	<?php if ($permisos['instituciones'] == "1" || $permisos['instituciones'] == "2"): ?>
		<li> 
			<a href="<?php echo $baseUrl; ?>/modules/instituciones/instituciones.php"><i class="fa fa-graduation-cap"></i> <span class="nav-label">Instituciones</span></a> 
		</li>
		<li> 
			<a href="<?php echo $baseUrl; ?>/modules/instituciones/sedes.php"><i class="fa fa-bank"></i> <span class="nav-label">Sedes educativas</span></a> 
		</li>
	<?php endif ?>
	<?php if ($permisos['archivos_globales'] == "1" || $permisos['archivos_globales'] == "2"): ?>
		<li> 
			<a href="<?php echo $baseUrl; ?>/modules/archivos"><i class="fa fa-folder-open"></i> <span class="nav-label">Archivos Globales</span></a> 
		</li>
	<?php endif ?>
	<?php if ($permisos['titulares_derecho'] == "1" || $permisos['titulares_derecho'] == "2"): ?>
		<li>
			<a href="#"><i class="fa fa-child"></i> <span class="nav-label">Titulares de Derecho</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li> <a href="<?php echo $baseUrl; ?>/modules/titulares_derecho/index.php">Derecho</a> </li>
				<li> <a href="<?php echo $baseUrl; ?>/modules/suplentes/index.php">Suplentes</a> </li>
			</ul>
		</li>
	<?php endif ?>
	<?php if ($permisos['menus'] == "1" || $permisos['menus'] == "2"): ?>
		<li>
			<a href="#"><i class="fas fa-utensils"></i> <span class="nav-label">Menús</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li> <a href="<?php echo $baseUrl; ?>/modules/menus2/ver_alimentos.php">Alimentos</a> </li>
				<li> <a href="<?php echo $baseUrl; ?>/modules/menus2/ver_preparaciones.php">Preparaciones</a> </li>
				<li> <a href="<?php echo $baseUrl; ?>/modules/menus2">Menús</a> </li>
				<li> <a href="<?php echo $baseUrl; ?>/modules/menu_valref">Aportes calóricos y nutricionales</a> </li>
			</ul>
		</li>
	<?php endif ?>
	<?php if ($permisos['diagnostico_infraestructura'] == "1" || $permisos['diagnostico_infraestructura'] == "2"): ?>
		<li>
			<a href="#"><i class="fa fa-bank"></i> <span class="nav-label">Diagnóstico Infraestructura</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li> <a href="<?php echo $baseUrl; ?>/modules/infraestructuras/index.php">Diagnóstico Infraestructura</a> </li>
			</ul>
		</li>
	<?php endif ?>
	<?php if ($permisos['dispositivos_biometricos'] == "1" || $permisos['dispositivos_biometricos'] == "2"): ?>
		<li>
			<a href="#"><i class="fa fa-calculator"></i> <span class="nav-label">Dispositivos Biométricos</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li> <a href="<?php echo $baseUrl; ?>/modules/dispositivos_biometricos/index.php">Dispositivos Biométricos</a> </li>
				<li><a href="<?php echo $baseUrl; ?>/modules/dispositivos_biometricos/formato_datos_personales.php">Formato Datos Personales</a></li>
			</ul>
		</li>
	<?php endif ?>
	<?php if ($permisos['despachos'] == "1" || $permisos['despachos'] == "2"): ?>
		<li>
			<a href="<?php echo $baseUrl; ?>/modules/despachos/despachos.php"><i class="fa fa-truck"></i> <span class="nav-label">Despachos</span> <span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li><a href="#">Alimentos</a></li>
				<?php if ($permisos['despachos'] == "2"): ?>
					<li><a href="<?php echo $baseUrl; ?>/modules/despachos/editar.php">Edición alimentos</a></li>
				<?php endif ?>
				<li> <a href="<?php echo $baseUrl; ?>/modules/insumos2/despachos.php">Insumos</a> </li>
			</ul>
		</li>
	<?php endif ?>
	<?php if ($permisos['orden_compra'] == "1" || $permisos['orden_compra'] == "2"): ?>
		<li> 
			<a href="<?php echo $baseUrl; ?>/modules/ordenes_de_compra/ordenes_de_compra.php"><i class="fas fa-truck-loading"></i> <span class="nav-label">Ordenes de compra</span></a> 
		</li>
	<?php endif ?>
	<?php if ($permisos['entrega_complementos'] == "1" || $permisos['entrega_complementos'] == "2"): ?>
		<li>
			<a href="#"><i class="fas fa-book-open"></i> <span class="nav-label">Entregas de Complementos Alimentarios</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li><a href="<?php echo $baseUrl; ?>/modules/consultas/consulta_resumida_entregas.php">Consulta resumida</a></li>
				<li>
					<a href="#" id="damian">Impresión de planillas<span class="fa arrow"></span></a>
					<ul class="nav nav-third-level">
						<li> <a href="<?php echo $baseUrl; ?>/modules/impresion_planillas/control_asistencia.php">Control de asistencia</a> </li>
						<li> <a href="<?php echo $baseUrl; ?>/modules/impresion_planillas/certificados.php">Certificados por institución</a> </li>
						<li> <a href="<?php echo $baseUrl; ?>/modules/impresion_planillas/certificados_covid19.php">Certificado Rector COVID19</a> </li>
						<li> <a href="<?php echo $baseUrl; ?>/modules/impresion_planillas/certificados_bono.php">Certificado Bono</a> </li>
					</ul>
				</li>
				<li>
					<a href="#" id="damian">Procesar<span class="fa arrow"></span></a>
					<ul class="nav nav-third-level">
						<li> <a href="#" class="sinDesarrollar">Procesar entregas</a> </li>
						<!-- <li> <a href="#" class="sinDesarrollar">Entregas Detalladas</a> </li> -->
						<li> <a href="#" class="sinDesarrollar">Aplicar novedades a entregas</a> </li>
					</ul>
				</li>
				<li>
					<a href="#" id="damian">Importar<span class="fa arrow"></span></a>
					<ul class="nav nav-third-level">
						<li> <a href="#" class="sinDesarrollar">Entregas Resumidas desde CSV</a> </li>
						<li> <a href="#" class="sinDesarrollar">Desde USB/biométrico</a> </li>
						<li> <a href="#" class="sinDesarrollar">Desde servidor biométrico</a> </li>
					</ul>
				</li>
				<li><a href="<?php echo $baseUrl; ?>/modules/registros_biometricos/registros_biometricos.php">Ver registros biométricos</a></li>
				<li><a href="#" class="sinDesarrollar">Validar entregas biométricas</a></li>
			</ul>
		</li>
	<?php endif ?>
	<?php if ($permisos['novedades'] == "1" || $permisos['novedades'] == "2"): ?>
		<li>
			<a href="#"><i class="fas fa-bookmark"></i> <span class="nav-label">Novedades</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li>
					<a href="<?php echo $baseUrl; ?>/modules/novedades_priorizacion/index.php">Priorización</a>
				</li>
				<li>
					<a href="#">Focalización <span class="fa arrow"></span></a>
					<ul class="nav nav-third-level">
						<li><a href="<?php echo $baseUrl; ?>/modules/novedades_ejecucion/index.php">Titulares</a></li>
						<?php if ($permisos['novedades'] == "2"): ?>
							<li><a href="<?php echo $baseUrl; ?>/modules/novedades_ejecucion/suplentes.php">Suplentes</a></li>
							<li><a href="<?php echo $baseUrl; ?>/modules/novedades_ejecucion/repitentes.php">Repitentes</a></li>
						<?php endif ?>
					</ul>
				</li>
				<li>
					<a href="<?php echo $baseUrl; ?>/modules/intercambios/index.php">Menú</a>
				</li>
			</ul>
		</li>
	<?php endif ?>
	<?php if ($permisos['nomina'] == "1" || $permisos['nomina'] == "2"): ?>
		<li>
			<a href="<?= $baseUrl; ?>/modules/nomina"><i class="fas fa-hand-holding-usd"></i> <span class="nav-label"> Nómina </span></a>
		</li>
	<?php endif ?>
	<?php if ($permisos['fqrs'] == "1" || $permisos['fqrs'] == "2"): ?>
		<li>
			<a href="<?= $baseUrl; ?>/modules/fqrs/index.php"><i class="fa fa-question"></i> <span class="nav-label">FQRS</span></a>
		</li>
	<?php endif ?>
	<?php if ($permisos['informes'] == "1" || $permisos['informes'] == "2"): ?>
		<li>
			<a href="#"><i class="fas fa-layer-group"></i></i> <span class="nav-label">Informes</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
			<?php if( $_SESSION['perfil'] != 9 ){ ?>
				<li> <a href="<?php echo $baseUrl; ?>/modules/trazabilidad/index.php">Trazabilidad</a> </li>
				<li> <a href="<?php echo $baseUrl; ?>/modules/trazabilidad_insumos/index.php">Trazabilidad Insumos</a> </li>
			<?php } ?>
				<li> <a href="<?php echo $baseUrl; ?>/modules/estadisticas/index.php">Estadisticas</a></li>
				<li> <a href="<?php echo $baseUrl; ?>/modules/estadisticas_avanzadas/index.php">Estadisticas Avanzadas</a></li>
			<?php if( $_SESSION['perfil'] != 9 ){ ?>
				<li> <a href="<?= $baseUrl; ?>/modules/bitacora_usuarios/index.php">Bitácora de usuarios</a></li>
				<li> <a href="#" class="sinDesarrollar">Informe CHIP</a></li>
				<li> <a href="<?php echo $baseUrl; ?>/modules/informes/informe_alimentos.php">Informe de alimentos</a></li>
				<li> <a href="<?php echo $baseUrl; ?>/modules/insumos/insumos_proveedor.php">Informe de Insumos ordenados por proveedores</a> </li>
				<li> <a href="<?= $baseUrl; ?>/modules/informes/ordenes_compra.php">Ordenes de compra</a></li>
				<li> <a href="<?= $baseUrl; ?>/modules/inejecuciones/index.php">Informe Inejecuciones</a></li>
			<?php } ?>
			</ul>
		</li>
	<?php endif ?>
	<?php if ($permisos['asistencia'] == "1" || $permisos['asistencia'] == "2"): ?>
		<li>
			<a href="#"><i class="fa fa-child"></i> <span class="nav-label">Asistencias</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
			<?php if( $_SESSION['perfil'] == 0 || $_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 8 || $_SESSION['perfil'] == 3 ){ ?>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias">Toma de asistencia</a> </li>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/repitentes.php"> Selección de repitentes </a> </li>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/consumo.php"> Registro de consumos </a> </li>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/suplentes.php"> Selección de suplentes </a> </li>
			<?php } ?>

			<?php if( $_SESSION['perfil'] == 0 || $_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 5  || $_SESSION['perfil'] == 6 || $_SESSION['perfil'] == 7 ){ ?>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/informe_asistencia.php"> Informe de asistencia </a> </li>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/registro_biometrico.php"> Registro Biometrico </a> </li>
			<?php } ?>

			<?php if( $_SESSION['perfil'] == 0 || $_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 3 ){ ?>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/control_tablets.php"> Control de toma de asistencias </a> </li>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/control_biometrico.php"> Control del registro biometrico </a> </li>
			<?php } ?>
			</ul>
		</li>
	<?php endif ?>
	<?php if ($permisos['control_acceso'] == "1" || $permisos['control_acceso'] == "2"): ?>
		<li> 
			<a href="<?php echo $baseUrl; ?>/modules/control_acceso/listado.php"><i class="far fa-clock"></i> <span class="nav-label">Control de Acceso</span></a> 
		</li>
	<?php endif ?>
	<?php if ($permisos['procesos'] == "1" || $permisos['procesos'] == "2"): ?>
		<li>
			<a href="#"><i class="fas fa-cog"></i><span class="nav-label">Procesos</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li><a href="#" class="sinDesarrollar">Copia de seguridad</a></li>
				<li><a href="#" class="sinDesarrollar">Bloqueo de período</a></li>
				<li><a href="#" class="sinDesarrollar">Calculo de edades de titulares</a></li>
			</ul>
		</li>
	<?php endif ?>
<?php endif ?>

<!-- manejo perfil auxiliar  -->
<?php if ($_SESSION['perfil'] == "3" && $permisos['id'] == "3"): ?>
	<li class="active">
		<a href="<?= $baseUrl; ?>/modules/asistencias/control_tablets.php"><i class="fa fa-home"></i> <span class="nav-label">Inicio</span></a>
	</li>
	<?php if ($permisos['entregas_biometricas'] == "1" || $permisos['entregas_biometricas'] == "2"): ?>
		<li>
			<a href="#"><i class="fas fa-fingerprint"></i> <span class="nav-label">Entregas Biometricas</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li> <a href="<?php echo $baseUrl; ?>/modules/entregas_biometricas/dashboard.php">Dashboard</a> </li>
				<?php if ($permisos['entregas_biometricas'] == "2"): ?>
					<li> <a href="<?php echo $baseUrl; ?>/modules/entregas_biometricas/index.php">Registrar entregas vía QR - BarCode</a> 
					</li>
				<?php endif ?>
			</ul>
		</li>
	<?php endif ?>
	<?php if ($permisos['instituciones'] == "1" || $permisos['instituciones'] == "2"): ?>
		<li> 
			<a href="<?php echo $baseUrl; ?>/modules/instituciones/instituciones.php"><i class="fa fa-graduation-cap"></i> <span class="nav-label">Instituciones</span></a> 
		</li>
		<li> 
			<a href="<?php echo $baseUrl; ?>/modules/instituciones/sedes.php"><i class="fa fa-bank"></i> <span class="nav-label">Sedes educativas</span></a> 
		</li>
	<?php endif ?>
	<?php if ($permisos['archivos_globales'] == "1" || $permisos['archivos_globales'] == "2"): ?>
		<li> 
			<a href="<?php echo $baseUrl; ?>/modules/archivos"><i class="fa fa-folder-open"></i> <span class="nav-label">Archivos Globales</span></a> 
		</li>
	<?php endif ?>
	<?php if ($permisos['titulares_derecho'] == "1" || $permisos['titulares_derecho'] == "2"): ?>
		<li>
			<a href="#"><i class="fa fa-child"></i> <span class="nav-label">Titulares de Derecho</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li> <a href="<?php echo $baseUrl; ?>/modules/titulares_derecho/index.php">Derecho</a> </li>
				<li> <a href="<?php echo $baseUrl; ?>/modules/suplentes/index.php">Suplentes</a> </li>
			</ul>
		</li>
	<?php endif ?>
	<?php if ($permisos['menus'] == "1" || $permisos['menus'] == "2"): ?>
		<li>
			<a href="#"><i class="fas fa-utensils"></i> <span class="nav-label">Menús</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li> <a href="<?php echo $baseUrl; ?>/modules/menus2/ver_alimentos.php">Alimentos</a> </li>
				<li> <a href="<?php echo $baseUrl; ?>/modules/menus2/ver_preparaciones.php">Preparaciones</a> </li>
				<li> <a href="<?php echo $baseUrl; ?>/modules/menus2">Menús</a> </li>
				<li> <a href="<?php echo $baseUrl; ?>/modules/menu_valref">Aportes calóricos y nutricionales</a> </li>
			</ul>
		</li>
	<?php endif ?>
	<?php if ($permisos['diagnostico_infraestructura'] == "1" || $permisos['diagnostico_infraestructura'] == "2"): ?>
		<li>
			<a href="#"><i class="fa fa-bank"></i> <span class="nav-label">Diagnóstico Infraestructura</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li> <a href="<?php echo $baseUrl; ?>/modules/infraestructuras/index.php">Diagnóstico Infraestructura</a> </li>
			</ul>
		</li>
	<?php endif ?>
	<?php if ($permisos['dispositivos_biometricos'] == "1" || $permisos['dispositivos_biometricos'] == "2"): ?>
		<li>
			<a href="#"><i class="fa fa-calculator"></i> <span class="nav-label">Dispositivos Biométricos</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li> <a href="<?php echo $baseUrl; ?>/modules/dispositivos_biometricos/index.php">Dispositivos Biométricos</a> </li>
				<li><a href="<?php echo $baseUrl; ?>/modules/dispositivos_biometricos/formato_datos_personales.php">Formato Datos Personales</a></li>
			</ul>
		</li>
	<?php endif ?>
	<?php if ($permisos['despachos'] == "1" || $permisos['despachos'] == "2"): ?>
		<li>
			<a href="#"><i class="fa fa-truck"></i> <span class="nav-label">Despachos</span> <span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li><a href="<?php echo $baseUrl; ?>/modules/despachos/despachos.php">Alimentos</a></li>
				<?php if ($permisos['despachos'] == "2"): ?>
					<li><a href="<?php echo $baseUrl; ?>/modules/despachos/editar.php">Edición alimentos</a></li>
				<?php endif ?>
				<li> <a href="<?php echo $baseUrl; ?>/modules/insumos2/despachos.php">Insumos</a> </li>
			</ul>
		</li>
	<?php endif ?>
	<?php if ($permisos['orden_compra'] == "1" || $permisos['orden_compra'] == "2"): ?>
		<li> 
			<a href="<?php echo $baseUrl; ?>/modules/ordenes_de_compra/ordenes_de_compra.php"><i class="fas fa-truck-loading"></i> <span class="nav-label">Ordenes de compra</span></a> 
		</li>
	<?php endif ?>
	<?php if ($permisos['entrega_complementos'] == "1" || $permisos['entrega_complementos'] == "2"): ?>
		<li>
			<a href="#"><i class="fas fa-book-open"></i> <span class="nav-label">Entregas de Complementos Alimentarios</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li><a href="<?php echo $baseUrl; ?>/modules/consultas/consulta_resumida_entregas.php">Consulta resumida</a></li>
				<li>
					<a href="#" id="damian">Impresión de planillas<span class="fa arrow"></span></a>
					<ul class="nav nav-third-level">
						<li> <a href="<?php echo $baseUrl; ?>/modules/impresion_planillas/control_asistencia.php">Control de asistencia</a> </li>
						<li> <a href="<?php echo $baseUrl; ?>/modules/impresion_planillas/certificados.php">Certificados por institución</a> </li>
						<li> <a href="<?php echo $baseUrl; ?>/modules/impresion_planillas/certificados_covid19.php">Certificado Rector COVID19</a> </li>
						<li> <a href="<?php echo $baseUrl; ?>/modules/impresion_planillas/certificados_bono.php">Certificado Bono</a> </li>
					</ul>
				</li>
				<li>
					<a href="#" id="damian">Procesar<span class="fa arrow"></span></a>
					<ul class="nav nav-third-level">
						<li> <a href="#" class="sinDesarrollar">Procesar entregas</a> </li>
						<!-- <li> <a href="#" class="sinDesarrollar">Entregas Detalladas</a> </li> -->
						<li> <a href="#" class="sinDesarrollar">Aplicar novedades a entregas</a> </li>
					</ul>
				</li>
				<li>
					<a href="#" id="damian">Importar<span class="fa arrow"></span></a>
					<ul class="nav nav-third-level">
						<li> <a href="#" class="sinDesarrollar">Entregas Resumidas desde CSV</a> </li>
						<li> <a href="#" class="sinDesarrollar">Desde USB/biométrico</a> </li>
						<li> <a href="#" class="sinDesarrollar">Desde servidor biométrico</a> </li>
					</ul>
				</li>
				<li><a href="<?php echo $baseUrl; ?>/modules/registros_biometricos/registros_biometricos.php">Ver registros biométricos</a></li>
				<li><a href="#" class="sinDesarrollar">Validar entregas biométricas</a></li>
			</ul>
		</li>
	<?php endif ?>
	<?php if ($permisos['novedades'] == "1" || $permisos['novedades'] == "2"): ?>
		<li>
			<a href="#"><i class="fas fa-bookmark"></i> <span class="nav-label">Novedades</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li>
					<a href="<?php echo $baseUrl; ?>/modules/novedades_priorizacion/index.php">Priorización</a>
				</li>
				<li>
					<a href="#">Focalización <span class="fa arrow"></span></a>
					<ul class="nav nav-third-level">
						<li><a href="<?php echo $baseUrl; ?>/modules/novedades_ejecucion/index.php">Titulares</a></li>
						<?php if ($permisos['novedades'] == "2"): ?>
							<li><a href="<?php echo $baseUrl; ?>/modules/novedades_ejecucion/suplentes.php">Suplentes</a></li>
							<li><a href="<?php echo $baseUrl; ?>/modules/novedades_ejecucion/repitentes.php">Repitentes</a></li>
						<?php endif ?>
					</ul>
				</li>
				<li>
					<a href="<?php echo $baseUrl; ?>/modules/intercambios/index.php">Menú</a>
				</li>
			</ul>
		</li>
	<?php endif ?>
	<?php if ($permisos['nomina'] == "1" || $permisos['nomina'] == "2"): ?>
		<li>
			<a href="<?= $baseUrl; ?>/modules/nomina"><i class="fas fa-hand-holding-usd"></i> <span class="nav-label"> Nómina </span></a>
		</li>
	<?php endif ?>
	<?php if ($permisos['fqrs'] == "1" || $permisos['fqrs'] == "2"): ?>
		<li>
			<a href="<?= $baseUrl; ?>/modules/fqrs/index.php"><i class="fa fa-question"></i> <span class="nav-label">FQRS</span></a>
		</li>
	<?php endif ?>
	<?php if ($permisos['informes'] == "1" || $permisos['informes'] == "2"): ?>
		<li>
			<a href="#"><i class="fas fa-layer-group"></i></i> <span class="nav-label">Informes</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
			<?php if( $_SESSION['perfil'] != 9 ){ ?>
				<li> <a href="<?php echo $baseUrl; ?>/modules/trazabilidad/index.php">Trazabilidad</a> </li>
				<li> <a href="<?php echo $baseUrl; ?>/modules/trazabilidad_insumos/index.php">Trazabilidad Insumos</a> </li>
			<?php } ?>
				<li> <a href="<?php echo $baseUrl; ?>/modules/estadisticas/index.php">Estadisticas</a></li>
				<li> <a href="<?php echo $baseUrl; ?>/modules/estadisticas_avanzadas/index.php">Estadisticas Avanzadas</a></li>
			<?php if( $_SESSION['perfil'] != 9 ){ ?>
				<li> <a href="<?= $baseUrl; ?>/modules/bitacora_usuarios/index.php">Bitácora de usuarios</a></li>
				<li> <a href="#" class="sinDesarrollar">Informe CHIP</a></li>
				<li> <a href="<?php echo $baseUrl; ?>/modules/informes/informe_alimentos.php">Informe de alimentos</a></li>
				<li> <a href="<?php echo $baseUrl; ?>/modules/insumos/insumos_proveedor.php">Informe de Insumos ordenados por proveedores</a> </li>
				<li> <a href="<?= $baseUrl; ?>/modules/informes/ordenes_compra.php">Ordenes de compra</a></li>
				<li> <a href="<?= $baseUrl; ?>/modules/inejecuciones/index.php">Informe Inejecuciones</a></li>
			<?php } ?>
			</ul>
		</li>
	<?php endif ?>
	<?php if ($permisos['asistencia'] == "1" || $permisos['asistencia'] == "2"): ?>
		<li>
			<a href="#"><i class="fa fa-child"></i> <span class="nav-label">Asistencias</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
			<?php if( $_SESSION['perfil'] == 0 || $_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 8 || $_SESSION['perfil'] == 3 ){ ?>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias">Toma de asistencia</a> </li>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/repitentes.php"> Selección de repitentes </a> </li>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/consumo.php"> Registro de consumos </a> </li>
			<?php } ?>

			<?php if( $_SESSION['perfil'] == 0 || $_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 5  || $_SESSION['perfil'] == 6 || $_SESSION['perfil'] == 7 ){ ?>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/informe_asistencia.php"> Informe de asistencia </a> </li>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/registro_biometrico.php"> Registro Biometrico </a> </li>
			<?php } ?>

			<?php if( $_SESSION['perfil'] == 0 || $_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 3 ){ ?>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/control_tablets.php"> Control de toma de asistencias </a> </li>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/control_biometrico.php"> Control del registro biometrico </a> </li>
			<?php } ?>
			</ul>
		</li>
	<?php endif ?>
	<?php if ($permisos['control_acceso'] == "1" || $permisos['control_acceso'] == "2"): ?>
		<li> 
			<a href="<?php echo $baseUrl; ?>/modules/control_acceso/listado.php"><i class="far fa-clock"></i> <span class="nav-label">Control de Acceso</span></a> 
		</li>
	<?php endif ?>
	<?php if ($permisos['procesos'] == "1" || $permisos['procesos'] == "2"): ?>
		<li>
			<a href="#"><i class="fas fa-cog"></i><span class="nav-label">Procesos</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li><a href="#" class="sinDesarrollar">Copia de seguridad</a></li>
				<li><a href="#" class="sinDesarrollar">Bloqueo de período</a></li>
				<li><a href="#" class="sinDesarrollar">Calculo de edades de titulares</a></li>
			</ul>
		</li>
	<?php endif ?>
<?php endif ?>

<!-- manejo perfil auditor -->
<?php if ($_SESSION['perfil'] == "5" && $permisos['id'] == "5"): ?>
	<li class="active">
		<a href="<?php echo $baseUrl; ?>"><i class="fa fa-home"></i> <span class="nav-label">Inicio</span></a>
	</li>
	<?php if ($permisos['entregas_biometricas'] == "1" || $permisos['entregas_biometricas'] == "2"): ?>
		<li>
			<a href="#"><i class="fas fa-fingerprint"></i> <span class="nav-label">Entregas Biometricas</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li> <a href="<?php echo $baseUrl; ?>/modules/entregas_biometricas/dashboard.php">Dashboard</a> </li>
				<?php if ($permisos['entregas_biometricas'] == "2"): ?>
					<li> <a href="<?php echo $baseUrl; ?>/modules/entregas_biometricas/index.php">Registrar entregas vía QR - BarCode</a> 
					</li>
				<?php endif ?>
			</ul>
		</li>
	<?php endif ?>
	<?php if ($permisos['instituciones'] == "1" || $permisos['instituciones'] == "2"): ?>
		<li> 
			<a href="<?php echo $baseUrl; ?>/modules/instituciones/instituciones.php"><i class="fa fa-graduation-cap"></i> <span class="nav-label">Instituciones</span></a> 
		</li>
		<li> 
			<a href="<?php echo $baseUrl; ?>/modules/instituciones/sedes.php"><i class="fa fa-bank"></i> <span class="nav-label">Sedes educativas</span></a> 
		</li>
	<?php endif ?>
	<?php if ($permisos['archivos_globales'] == "1" || $permisos['archivos_globales'] == "2"): ?>
		<li> 
			<a href="<?php echo $baseUrl; ?>/modules/archivos"><i class="fa fa-folder-open"></i> <span class="nav-label">Archivos Globales</span></a> 
		</li>
	<?php endif ?>
	<?php if ($permisos['titulares_derecho'] == "1" || $permisos['titulares_derecho'] == "2"): ?>
		<li>
			<a href="#"><i class="fa fa-child"></i> <span class="nav-label">Titulares de Derecho</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li> <a href="<?php echo $baseUrl; ?>/modules/titulares_derecho/index.php">Derecho</a> </li>
				<li> <a href="<?php echo $baseUrl; ?>/modules/suplentes/index.php">Suplentes</a> </li>
			</ul>
		</li>
	<?php endif ?>
	<?php if ($permisos['menus'] == "1" || $permisos['menus'] == "2"): ?>
		<li>
			<a href="#"><i class="fas fa-utensils"></i> <span class="nav-label">Menús</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li> <a href="<?php echo $baseUrl; ?>/modules/menus2/ver_alimentos.php">Alimentos</a> </li>
				<li> <a href="<?php echo $baseUrl; ?>/modules/menus2/ver_preparaciones.php">Preparaciones</a> </li>
				<li> <a href="<?php echo $baseUrl; ?>/modules/menus2">Menús</a> </li>
				<li> <a href="<?php echo $baseUrl; ?>/modules/menu_valref">Aportes calóricos y nutricionales</a> </li>
			</ul>
		</li>
	<?php endif ?>
	<?php if ($permisos['diagnostico_infraestructura'] == "1" || $permisos['diagnostico_infraestructura'] == "2"): ?>
		<li>
			<a href="#"><i class="fa fa-bank"></i> <span class="nav-label">Diagnóstico Infraestructura</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li> <a href="<?php echo $baseUrl; ?>/modules/infraestructuras/index.php">Diagnóstico Infraestructura</a> </li>
			</ul>
		</li>
	<?php endif ?>
	<?php if ($permisos['dispositivos_biometricos'] == "1" || $permisos['dispositivos_biometricos'] == "2"): ?>
		<li>
			<a href="#"><i class="fa fa-calculator"></i> <span class="nav-label">Dispositivos Biométricos</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li> <a href="<?php echo $baseUrl; ?>/modules/dispositivos_biometricos/index.php">Dispositivos Biométricos</a> </li>
				<li><a href="<?php echo $baseUrl; ?>/modules/dispositivos_biometricos/formato_datos_personales.php">Formato Datos Personales</a></li>
			</ul>
		</li>
	<?php endif ?>
	<?php if ($permisos['despachos'] == "1" || $permisos['despachos'] == "2"): ?>
		<li>
			<a href="#"><i class="fa fa-truck"></i> <span class="nav-label">Despachos</span> <span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li><a href="<?php echo $baseUrl; ?>/modules/despachos/despachos.php">Alimentos</a></li>
				<?php if ($permisos['despachos'] == "2"): ?>
					<li><a href="<?php echo $baseUrl; ?>/modules/despachos/editar.php">Edición alimentos</a></li>
				<?php endif ?>
				<li> <a href="<?php echo $baseUrl; ?>/modules/insumos2/despachos.php">Insumos</a> </li>
			</ul>
		</li>
	<?php endif ?>
	<?php if ($permisos['orden_compra'] == "1" || $permisos['orden_compra'] == "2"): ?>
		<li> 
			<a href="<?php echo $baseUrl; ?>/modules/ordenes_de_compra/ordenes_de_compra.php"><i class="fas fa-truck-loading"></i> <span class="nav-label">Ordenes de compra</span></a> 
		</li>
	<?php endif ?>
	<?php if ($permisos['entrega_complementos'] == "1" || $permisos['entrega_complementos'] == "2"): ?>
		<li>
			<a href="#"><i class="fas fa-book-open"></i> <span class="nav-label">Entregas de Complementos Alimentarios</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li><a href="<?php echo $baseUrl; ?>/modules/consultas/consulta_resumida_entregas.php">Consulta resumida</a></li>
				<li>
					<a href="#" id="damian">Impresión de planillas<span class="fa arrow"></span></a>
					<ul class="nav nav-third-level">
						<li> <a href="<?php echo $baseUrl; ?>/modules/impresion_planillas/control_asistencia.php">Control de asistencia</a> </li>
						<li> <a href="<?php echo $baseUrl; ?>/modules/impresion_planillas/certificados.php">Certificados por institución</a> </li>
						<li> <a href="<?php echo $baseUrl; ?>/modules/impresion_planillas/certificados_covid19.php">Certificado Rector COVID19</a> </li>
						<li> <a href="<?php echo $baseUrl; ?>/modules/impresion_planillas/certificados_bono.php">Certificado Bono</a> </li>
					</ul>
				</li>
				<li>
					<a href="#" id="damian">Procesar<span class="fa arrow"></span></a>
					<ul class="nav nav-third-level">
						<li> <a href="#" class="sinDesarrollar">Procesar entregas</a> </li>
						<!-- <li> <a href="#" class="sinDesarrollar">Entregas Detalladas</a> </li> -->
						<li> <a href="#" class="sinDesarrollar">Aplicar novedades a entregas</a> </li>
					</ul>
				</li>
				<li>
					<a href="#" id="damian">Importar<span class="fa arrow"></span></a>
					<ul class="nav nav-third-level">
						<li> <a href="#" class="sinDesarrollar">Entregas Resumidas desde CSV</a> </li>
						<li> <a href="#" class="sinDesarrollar">Desde USB/biométrico</a> </li>
						<li> <a href="#" class="sinDesarrollar">Desde servidor biométrico</a> </li>
					</ul>
				</li>
				<li><a href="<?php echo $baseUrl; ?>/modules/registros_biometricos/registros_biometricos.php">Ver registros biométricos</a></li>
				<li><a href="#" class="sinDesarrollar">Validar entregas biométricas</a></li>
			</ul>
		</li>
	<?php endif ?>
	<?php if ($permisos['novedades'] == "1" || $permisos['novedades'] == "2"): ?>
		<li>
			<a href="#"><i class="fas fa-bookmark"></i> <span class="nav-label">Novedades</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li>
					<a href="<?php echo $baseUrl; ?>/modules/novedades_priorizacion/index.php">Priorización</a>
				</li>
				<li>
					<a href="#">Focalización<span class="fa arrow"></span></a>
					<ul class="nav nav-third-level">
						<li><a href="<?php echo $baseUrl; ?>/modules/novedades_ejecucion/index.php">Titulares</a></li>
						<?php if ($permisos['novedades'] == "2"): ?>
							<li><a href="<?php echo $baseUrl; ?>/modules/novedades_ejecucion/suplentes.php">Suplentes</a></li>
							<li><a href="<?php echo $baseUrl; ?>/modules/novedades_ejecucion/repitentes.php">Repitentes</a></li>
						<?php endif ?>
					</ul>
				</li>
				<li>
					<a href="<?php echo $baseUrl; ?>/modules/intercambios/index.php">Menú</a>
				</li>
			</ul>
		</li>
	<?php endif ?>
	<?php if ($permisos['nomina'] == "1" || $permisos['nomina'] == "2"): ?>
		<li>
			<a href="<?= $baseUrl; ?>/modules/nomina"><i class="fas fa-hand-holding-usd"></i> <span class="nav-label"> Nómina </span></a>
		</li>
	<?php endif ?>
	<?php if ($permisos['fqrs'] == "1" || $permisos['fqrs'] == "2"): ?>
		<li>
			<a href="<?= $baseUrl; ?>/modules/fqrs/index.php"><i class="fa fa-question"></i> <span class="nav-label">FQRS</span></a>
		</li>
	<?php endif ?>
	<?php if ($permisos['informes'] == "1" || $permisos['informes'] == "2"): ?>
		<li>
			<a href="#"><i class="fas fa-layer-group"></i></i> <span class="nav-label">Informes</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
			<?php if( $_SESSION['perfil'] != 9 ){ ?>
				<li> <a href="<?php echo $baseUrl; ?>/modules/trazabilidad/index.php">Trazabilidad</a> </li>
				<li> <a href="<?php echo $baseUrl; ?>/modules/trazabilidad_insumos/index.php">Trazabilidad Insumos</a> </li>
			<?php } ?>
				<li> <a href="<?php echo $baseUrl; ?>/modules/estadisticas/index.php">Estadisticas</a></li>
				<li> <a href="<?php echo $baseUrl; ?>/modules/estadisticas_avanzadas/index.php">Estadisticas Avanzadas</a></li>
			<?php if( $_SESSION['perfil'] != 9 ){ ?>
				<li> <a href="<?= $baseUrl; ?>/modules/bitacora_usuarios/index.php">Bitácora de usuarios</a></li>
				<li> <a href="#" class="sinDesarrollar">Informe CHIP</a></li>
				<li> <a href="<?php echo $baseUrl; ?>/modules/informes/informe_alimentos.php">Informe de alimentos</a></li>
				<li> <a href="<?php echo $baseUrl; ?>/modules/insumos/insumos_proveedor.php">Informe de Insumos ordenados por proveedores</a> </li>
				<li> <a href="<?= $baseUrl; ?>/modules/informes/ordenes_compra.php">Ordenes de compra</a></li>
				<li> <a href="<?= $baseUrl; ?>/modules/inejecuciones/index.php">Informe Inejecuciones</a></li>
			<?php } ?>
			</ul>
		</li>
	<?php endif ?>
	<?php if ($permisos['asistencia'] == "1" || $permisos['asistencia'] == "2"): ?>
		<li>
			<a href="#"><i class="fa fa-child"></i> <span class="nav-label">Asistencias</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
			<?php if( $_SESSION['perfil'] == 0 || $_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 8 || $_SESSION['perfil'] == 3 ){ ?>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias">Toma de asistencia</a> </li>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/repitentes.php"> Selección de repitentes </a> </li>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/consumo.php"> Registro de consumos </a> </li>
			<?php } ?>

			<?php if( $_SESSION['perfil'] == 0 || $_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 5  || $_SESSION['perfil'] == 6 || $_SESSION['perfil'] == 7 ){ ?>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/informe_asistencia.php"> Informe de asistencia </a> </li>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/registro_biometrico.php"> Registro Biometrico </a> </li>
			<?php } ?>

			<?php if( $_SESSION['perfil'] == 0 || $_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 3 ){ ?>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/control_tablets.php"> Control de toma de asistencias </a> </li>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/control_biometrico.php"> Control del registro biometrico </a> </li>
			<?php } ?>
			</ul>
		</li>
	<?php endif ?>
	<?php if ($permisos['control_acceso'] == "1" || $permisos['control_acceso'] == "2"): ?>
		<li> 
			<a href="<?php echo $baseUrl; ?>/modules/control_acceso/listado.php"><i class="far fa-clock"></i> <span class="nav-label">Control de Acceso</span></a> 
		</li>
	<?php endif ?>
	<?php if ($permisos['procesos'] == "1" || $permisos['procesos'] == "2"): ?>
		<li>
			<a href="#"><i class="fas fa-cog"></i><span class="nav-label">Procesos</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li><a href="#" class="sinDesarrollar">Copia de seguridad</a></li>
				<li><a href="#" class="sinDesarrollar">Bloqueo de período</a></li>
				<li><a href="#" class="sinDesarrollar">Calculo de edades de titulares</a></li>
			</ul>
		</li>
	<?php endif ?>
<?php endif ?>

<!-- manejo perfil rector -->
<?php if ($_SESSION['perfil'] == "6" && $permisos['id'] == "6"): ?>
	<li class="active">
		<a href="<?php echo $baseUrl; ?>"><i class="fa fa-home"></i> <span class="nav-label">Inicio</span></a>
	</li>
	<?php if ($permisos['instituciones'] == "1" || $permisos['instituciones'] == "2" ): ?>
		<li> 
			<a href="<?php echo $baseUrl; ?>/modules/instituciones/institucion.php"><i class="fa fa-graduation-cap"></i> <span class="nav-label">Mi Institución</span></a> 
		</li>
	<?php endif ?>
	<?php if ($permisos['titulares_derecho'] == "1" || $permisos['titulares_derecho'] == "2"): ?>
		<li>
			<a href="#"><i class="fa fa-child"></i> <span class="nav-label">Titulares de Derecho</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li> <a href="<?php echo $baseUrl; ?>/modules/titulares_derecho/index.php">Derecho</a> </li>
				<li> <a href="<?php echo $baseUrl; ?>/modules/suplentes/index.php">Suplentes</a> </li>
			</ul>
		</li>
	<?php endif ?>
	<?php if ($permisos['entrega_complementos'] == "1" || $permisos['entrega_complementos'] == "2"): ?>
		<li>
			<a href="#"><i class="fas fa-book-open"></i> <span class="nav-label">Entregas de Complementos Alimentarios</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li>
					<a href="<?php echo $baseUrl; ?>/modules/consultas/consulta_resumida_entregas.php">Consulta resumida</a></li>
				<li>
					<a href="#" id="damian">Impresión de planillas<span class="fa arrow"></span></a>
					<ul class="nav nav-third-level">
						<li> <a href="<?php echo $baseUrl; ?>/modules/impresion_planillas/control_asistencia.php">Control de asistencia</a> </li>
						<li> <a href="<?php echo $baseUrl; ?>/modules/impresion_planillas/certificados.php">Certificados por institución</a> </li>
						<li> <a href="<?php echo $baseUrl; ?>/modules/impresion_planillas/certificados_covid19.php">Certificado Rector COVID19</a> </li>
					</ul>
				</li>
			</ul>
		</li>
	<?php endif ?>
	<?php if ($permisos['menus'] == "1" || $permisos['menus'] == "2"): ?>
		<li>
			<a href="#"><i class="fas fa-utensils"></i> <span class="nav-label">Menús</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li> <a href="<?php echo $baseUrl; ?>/modules/menus2">Menús</a> </li>
			</ul>
		</li>
	<?php endif ?>
	<?php if ($permisos['despachos'] == "1" || $permisos['despachos'] == "2"): ?>
		<li>
			<a href="#"><i class="fa fa-truck"></i> <span class="nav-label">Despachos</span> <span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li><a href="<?php echo $baseUrl; ?>/modules/despachos/despachos.php">Alimentos</a></li>
			</ul>
		</li>
	<?php endif ?>|
	<?php if ($permisos['fqrs'] == "1" || $permisos['fqrs'] == "2"): ?>
		<li>
			<li><a href="<?= $baseUrl; ?>/modules/fqrs/index.php"><i class="fa fa-question"></i> <span class="nav-label">FQRS</span></a></li>
		</li>
	<?php endif ?>
	<?php if ($permisos['asistencia'] == "1" || $permisos['asistencia'] == "2"): ?>
		<li>
			<a href="#"><i class="fa fa-child"></i> <span class="nav-label">Asistencias</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
			<?php if( $_SESSION['perfil'] == 0 || $_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 8 || $_SESSION['perfil'] == 3 ){ ?>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias">Toma de asistencia</a> </li>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/repitentes.php"> Selección de repitentes </a> </li>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/consumo.php"> Registro de consumos </a> </li>
			<?php } ?>

			<?php if( $_SESSION['perfil'] == 0 || $_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 5  || $_SESSION['perfil'] == 6 || $_SESSION['perfil'] == 7 ){ ?>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/informe_asistencia.php"> Informe de asistencia </a> </li>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/registro_biometrico.php"> Registro Biometrico </a> </li>
			<?php } ?>

			<?php if( $_SESSION['perfil'] == 0 || $_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 3 ){ ?>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/control_tablets.php"> Control de toma de asistencias </a> </li>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/control_biometrico.php"> Control del registro biometrico </a> </li>
			<?php } ?>
			</ul>
		</li>
	<?php endif ?>
<?php endif ?>

<!-- manejo perfil coordinador -->
<?php if ($_SESSION['perfil'] == "7" && $permisos['id'] == "7"): ?>
	<li class="active">
		<a href="<?php echo $baseUrl; ?>"><i class="fa fa-home"></i> <span class="nav-label">Inicio</span></a>
	</li>
	<?php if ($permisos['instituciones'] == "1" || $permisos['instituciones'] == "2"): ?>
		<li>
		 	<a href="<?php echo $baseUrl; ?>/modules/instituciones/institucion.php"><i class="fa fa-graduation-cap"></i> <span class="nav-label">Mis Sedes </span></a> 
		</li>
	<?php endif ?>
	<?php if ($permisos['titulares_derecho'] == "1" || $permisos['titulares_derecho'] == "2"): ?>
		<li>
			<a href="#"><i class="fa fa-child"></i> <span class="nav-label">Titulares de Derecho</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li> <a href="<?php echo $baseUrl; ?>/modules/titulares_derecho/index.php">Derecho</a> </li>
				<li> <a href="<?php echo $baseUrl; ?>/modules/suplentes/index.php">Suplentes</a> </li>
			</ul>
		</li>
	<?php endif ?>
	<?php if ($permisos['entrega_complementos'] == "1" || $permisos['entrega_complementos'] == "2"): ?>
		<li>
			<a href="#"><i class="fas fa-book-open"></i> <span class="nav-label">Entregas de Complementos Alimentarios</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li><a href="<?php echo $baseUrl; ?>/modules/consultas/consulta_resumida_entregas.php">Consulta resumida</a></li>
				<li>
					<a href="#" id="damian">Impresión de planillas<span class="fa arrow"></span></a>
					<ul class="nav nav-third-level">
						<li> <a href="<?php echo $baseUrl; ?>/modules/impresion_planillas/control_asistencia.php">Control de asistencia</a> </li>

					</ul>
				</li>
			</ul>
		</li>
	<?php endif ?>
	<?php if ($permisos['menus'] == "1" || $permisos['menus'] == "2"): ?>
		<li>
			<a href="#"><i class="fas fa-utensils"></i> <span class="nav-label">Menús</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li> <a href="<?php echo $baseUrl; ?>/modules/menus2">Menús</a> </li>
			</ul>
		</li>
	<?php endif ?>
	<?php if ($permisos['despachos'] == "1" || $permisos['despachos'] == "2"): ?>
		<li>
			<a href="#"><i class="fa fa-truck"></i> <span class="nav-label">Despachos</span> <span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li><a href="<?php echo $baseUrl; ?>/modules/despachos/despachos.php">Alimentos</a></li>
			</ul>
		</li>
	<?php endif ?>|
	<?php if ($permisos['fqrs'] == "1" || $permisos['fqrs'] == "2"): ?>
		<li>
			<li><a href="<?= $baseUrl; ?>/modules/fqrs/index.php"><i class="fa fa-question"></i> <span class="nav-label">FQRS</span></a></li>
		</li>
	<?php endif ?>
	<?php if ($permisos['asistencia'] == "1" || $permisos['asistencia'] == "2"): ?>
		<li>
			<a href="#"><i class="fa fa-child"></i> <span class="nav-label">Asistencias</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
			<?php if( $_SESSION['perfil'] == 0 || $_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 8 || $_SESSION['perfil'] == 3 ){ ?>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias">Toma de asistencia</a> </li>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/repitentes.php"> Selección de repitentes </a> </li>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/consumo.php"> Registro de consumos </a> </li>
			<?php } ?>

			<?php if( $_SESSION['perfil'] == 0 || $_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 5  || $_SESSION['perfil'] == 6 || $_SESSION['perfil'] == 7 ){ ?>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/informe_asistencia.php"> Informe de asistencia </a> </li>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/registro_biometrico.php"> Registro Biometrico </a> </li>
			<?php } ?>

			<?php if( $_SESSION['perfil'] == 0 || $_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 3 ){ ?>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/control_tablets.php"> Control de toma de asistencias </a> </li>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/control_biometrico.php"> Control del registro biometrico </a> </li>
			<?php } ?>
			</ul>
		</li>
	<?php endif ?>
<?php endif ?>

<!-- manejo perfil auxiliar asistencia -->
<?php if ($_SESSION['perfil'] == "8" && $permisos['id'] == "8"): ?>
	<li class="active">
		<a href="<?= $baseUrl; ?>/modules/asistencias"><i class="fa fa-home"></i> <span class="nav-label">Inicio</span></a>
	</li>
	<?php if ($permisos['asistencia'] == "1" || $permisos['asistencia'] == "2"): ?>
		<li>
			<a href="#"><i class="fa fa-child"></i> <span class="nav-label">Asistencias</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
			<?php if( $_SESSION['perfil'] == 0 || $_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 8 || $_SESSION['perfil'] == 3 ){ ?>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias">Toma de asistencia</a> </li>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/repitentes.php"> Selección de repitentes </a> </li>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/consumo.php"> Registro de consumos </a> </li>
			<?php } ?>

			<?php if( $_SESSION['perfil'] == 0 || $_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 5  || $_SESSION['perfil'] == 6 || $_SESSION['perfil'] == 7 ){ ?>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/informe_asistencia.php"> Informe de asistencia </a> </li>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/registro_biometrico.php"> Registro Biometrico </a> </li>
			<?php } ?>

			<?php if( $_SESSION['perfil'] == 0 || $_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 3 ){ ?>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/control_tablets.php"> Control de toma de asistencias </a> </li>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/control_biometrico.php"> Control del registro biometrico </a> </li>
			<?php } ?>
			</ul>
		</li>
	<?php endif ?>
<?php endif ?>