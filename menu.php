<?php if( $_SESSION['perfil'] != 8 && $_SESSION['perfil'] != 3 ){ ?>
	<li class="active">
		<a href="<?php echo $baseUrl; ?>"><i class="fa fa-home"></i> <span class="nav-label">Inicio</span></a>
	</li>
<?php } else if($_SESSION['perfil'] == 8) { ?>
	<li class="active">
		<a href="<?= $baseUrl; ?>/modules/asistencias"><i class="fa fa-home"></i> <span class="nav-label">Inicio</span></a>
	</li>
<?php } else if($_SESSION['perfil'] == 3) { ?>
	<li class="active">
		<a href="<?= $baseUrl; ?>/modules/asistencias/control_tablets.php"><i class="fa fa-home"></i> <span class="nav-label">Inicio</span></a>
	</li>
<?php } ?>












<?php if($_SESSION['perfil'] == 6){ ?>
	<li> <a href="<?php echo $baseUrl; ?>/modules/instituciones/institucion.php"><i class="fa fa-graduation-cap"></i> <span class="nav-label">Mi institución</span></a> </li>

	<li>
		<a href="#"><i class="fa fa-th-large"></i> <span class="nav-label">Entregas de Complementos Alimentarios</span><span class="fa arrow"></span></a>
		<ul class="nav nav-second-level collapse">
			<li><a href="<?php echo $baseUrl; ?>/modules/consultas/consulta_resumida_entregas.php">Consulta resumida</a></li>
			<li>
				<a href="#" id="damian">Impresión de planillas<span class="fa arrow"></span></a>
				<ul class="nav nav-third-level">
					<li> <a href="<?php echo $baseUrl; ?>/modules/impresion_planillas/control_asistencia.php">Control de asistencia</a> </li>
					<li> <a href="<?php echo $baseUrl; ?>/modules/impresion_planillas/certificados.php">Certificados por institución</a> </li>
				</ul>
			</li>
		</ul>
	</li>
<?php } ?>







<?php if( $_SESSION['perfil'] == 0 || $_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 5 || $_SESSION['perfil'] == 9 ){ ?>

	<li>
		<a href="#"><i class="fas fa-fingerprint"></i> <span class="nav-label">Entregas Biometricas</span><span class="fa arrow"></span></a>
		<ul class="nav nav-second-level collapse">
			<li> <a href="<?php echo $baseUrl; ?>/modules/entregas_biometricas/dashboard.php"><span class="nav-label">Dashboard</span></a> </li>
			<li> <a href="<?php echo $baseUrl; ?>/modules/entregas_biometricas/index.php"><span class="nav-label">Registrar entregas vía QR - BarCode</span></a> </li>
		</ul>
	</li>

	<li> <a href="<?php echo $baseUrl; ?>/modules/instituciones/instituciones.php"><i class="fa fa-graduation-cap"></i> <span class="nav-label">Instituciones</span></a> </li>


	<li> <a href="<?php echo $baseUrl; ?>/modules/instituciones/sedes.php"><i class="fa fa-bank"></i> <span class="nav-label">Sedes educativas</span></a> </li>
	<li>
		<a href="#"><i class="fa fa-th-large"></i> <span class="nav-label">Titulares de Derecho</span><span class="fa arrow"></span></a>
		<ul class="nav nav-second-level collapse">
			<li> <a href="<?php echo $baseUrl; ?>/modules/titulares_derecho/index.php"><i class="fa fa-child"></i> <span class="nav-label">Derecho</span></a> </li>
			<li> <a href="<?php echo $baseUrl; ?>/modules/suplentes/index.php"><i class="fa fa-tags"></i> <span class="nav-label">Suplentes</span></a> </li>
		</ul>
	</li>

	<?php if( $_SESSION['perfil'] != 9 ){ ?>
		<li>
			<a href="#"><i class="fa fa-th-large"></i> <span class="nav-label">Menús</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li> <a href="<?php echo $baseUrl; ?>/modules/menus2/ver_alimentos.php"><span class="nav-label">Alimentos</span></a> </li>
				<li> <a href="<?php echo $baseUrl; ?>/modules/menus2/ver_preparaciones.php"><span class="nav-label">Preparaciones</span></a> </li>
				<li> <a href="<?php echo $baseUrl; ?>/modules/menus2"><span class="nav-label">Menús</span></a> </li>
				<li> <a href="<?php echo $baseUrl; ?>/modules/menu_valref"><span class="nav-label">Aportes calóricos y nutricionales</span></a> </li>
			</ul>
		</li>
		<li>
			<a href="#"><i class="fa fa-bank"></i> <span class="nav-label">Diagnóstico Infraestructura</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li> <a href="<?php echo $baseUrl; ?>/modules/infraestructuras/index.php"><span class="nav-label">Diagnóstico Infraestructura</span></a> </li>
			</ul>
		</li>
		<li>
			<a href="#"><i class="fa fa-calculator"></i> <span class="nav-label">Dispositivos Biométricos</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li> <a href="<?php echo $baseUrl; ?>/modules/dispositivos_biometricos/index.php"><span class="nav-label">Dispositivos Biométricos</span></a> </li>
				<li><a href="<?php echo $baseUrl; ?>/modules/dispositivos_biometricos/formato_datos_personales.php"><span class="nav-label">Formato Datos Personales</span></a></li>
			</ul>
		</li>
		<li>
			<a href="<?php echo $baseUrl; ?>/modules/despachos/despachos.php"><i class="fa fa-truck"></i> <span class="nav-label">Despachos</span> <span class="fa arrow"></span></a>
			<ul class="nav nav-second-level">
				<li><a href="<?php echo $baseUrl; ?>/modules/despachos/despachos.php">Alimentos</a></li>
				<li><a href="<?php echo $baseUrl; ?>/modules/despachos/editar.php">Edición alimentos</a></li>
				<!-- <li><a href="<?php //echo $baseUrl; ?>/modules/ordenes_compra/ordenes.php">Órdenes de compra</a></li> -->
				<li> <a href="<?php echo $baseUrl; ?>/modules/insumos/despachos.php"><span class="nav-label">Insumos</span></a> </li>
			</ul>
		</li>





		<li> <a href="<?php echo $baseUrl; ?>/modules/ordenes_de_compra/ordenes_de_compra.php"><i class="fa fa-truck"></i> <span class="nav-label">Ordenes de compra</span></a> </li>











	<?php } ?>
	<li>
		<a href="#"><i class="fa fa-th-large"></i> <span class="nav-label">Entregas de Complementos Alimentarios</span><span class="fa arrow"></span></a>
		<ul class="nav nav-second-level collapse">
			<li><a href="<?php echo $baseUrl; ?>/modules/consultas/consulta_resumida_entregas.php">Consulta resumida</a></li>
			<!-- <li><a href="<?php echo $baseUrl; ?>/modules/consultas/consulta_detallada_entregas.php">Consulta detallada</a></li> -->
			<li>
				<a href="#" id="damian">Impresión de planillas<span class="fa arrow"></span></a>
				<ul class="nav nav-third-level">
					<li> <a href="<?php echo $baseUrl; ?>/modules/impresion_planillas/control_asistencia.php">Control de asistencia</a> </li>
					<li> <a href="<?php echo $baseUrl; ?>/modules/impresion_planillas/certificados.php">Certificados por institución</a> </li>
					<li> <a href="<?php echo $baseUrl; ?>/modules/impresion_planillas/certificados_covid19.php">Certificado Rector COVID19</a> </li>
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

	<?php if( $_SESSION['perfil'] != 9 ){ ?>
		<li>
			<a href="#"><i class="fa fa-th-large"></i> <span class="nav-label">Novedades</span><span class="fa arrow"></span></a>
			<ul class="nav nav-second-level collapse">
				<li><a href="<?php echo $baseUrl; ?>/modules/novedades_priorizacion/index.php">Priorización</a></li>
				<!-- <li><a href="<?php echo $baseUrl; ?>/modules/novedades_ejecucion/index.php">Focalización</a></li> -->
				<li>
					<a href="#"><span class="nav-label">Focalización</span> <span class="fa arrow"></span></a>
					<ul class="nav nav-third-level">
						<li><a href="<?php echo $baseUrl; ?>/modules/novedades_ejecucion/index.php">Titulares</a></li>
						<li><a href="<?php echo $baseUrl; ?>/modules/novedades_ejecucion/suplentes.php">Suplentes</a></li>
						<li><a href="<?php echo $baseUrl; ?>/modules/novedades_ejecucion/repitentes.php">Repitentes</a></li>
					</ul>
				</li>


				<li><a href="<?php echo $baseUrl; ?>/modules/intercambios/index.php">Menú</a></li>

				<!-- <li>
					<a href="#"><span class="nav-label">Menús</span> <span class="fa arrow"></span></a>
					<ul class="nav nav-third-level">
						<li> <a href="<?//= $baseUrl; ?>/modules/intercambios/intercambio_alimento.php">Intercambio de alimento</a> </li>
						<li> <a href="<?//= $baseUrl; ?>/modules/intercambios/intercambio_preparacion.php">Intercambio de preparación</a> </li>
						<li> <a href="<?//= $baseUrl; ?>/modules/intercambios/intercambio_dia_menu.php">Intercambio de día de menú</a> </li>
					</ul>
				</li> -->

			</ul>
		</li>
	<?php } ?>



	<li>
		<a href="#"><i class="fa fa-th-large"></i> <span class="nav-label">Informes</span><span class="fa arrow"></span></a>
		<ul class="nav nav-second-level collapse">
			<?php if( $_SESSION['perfil'] != 9 ){ ?>
				<li> <a href="<?php echo $baseUrl; ?>/modules/trazabilidad/index.php"><span class="nav-label">Trazabilidad</span></a> </li>
				<li> <a href="<?php echo $baseUrl; ?>/modules/trazabilidad_insumos/index.php"><span class="nav-label">Trazabilidad Insumos</span></a> </li>
			<?php } ?>
			<li> <a href="<?php echo $baseUrl; ?>/modules/estadisticas/index.php">Estadisticas</a></li>
			<?php if( $_SESSION['perfil'] != 9 ){ ?>
				<li> <a href="#" class="sinDesarrollar">Bitácora de usuarios</a></li>
				<li> <a href="#" class="sinDesarrollar">Informe CHIP</a></li>
				<li> <a href="<?php echo $baseUrl; ?>/modules/informes/informe_alimentos.php">Informe de alimentos</a></li>
				<li> <a href="<?php echo $baseUrl; ?>/modules/insumos/insumos_proveedor.php"><span class="nav-label">Informe de Insumos ordenados por proveedores</span></a> </li>
				<li><a href="#" class="sinDesarrollar">Informe de compras efectuadas al proveedor</a></li>
			<?php } ?>
		</ul>
	</li>

<?php } ?>










<?php if( $_SESSION['perfil'] == 0 || $_SESSION['perfil'] == 1 ){ ?>
	<li>
		<a href="#"><i class="fa fa-th-large"></i> <span class="nav-label">Procesos</span><span class="fa arrow"></span></a>
		<ul class="nav nav-second-level collapse">
			<li><a href="#" class="sinDesarrollar">Copia de seguridad</a></li>
			<li><a href="#" class="sinDesarrollar">Bloqueo de período</a></li>
			<li><a href="#" class="sinDesarrollar">Calculo de edades de titulares</a></li>
		</ul>
	</li>

	<li>
		<a href="#"><i class="fa fa-th-large"></i> <span class="nav-label">Configuración</span><span class="fa arrow"></span></a>
		<ul class="nav nav-second-level collapse">
			<li> <a href="<?php echo $baseUrl; ?>/modules/parametros"><span class="nav-label">Parámetros generales</span></a> </li>
			<li> <a href="<?php echo $baseUrl; ?>/modules/dias_contrato"><span class="nav-label">Días de contratos</span></a> </li>
			<li><a href="<?php echo $baseUrl; ?>/modules/complementos_alimentarios">Complementos alimentarios</a></li>
			<li><a href="<?php echo $baseUrl; ?>/modules/insumos">Insumos</a></li>
			<li><a href="<?= $baseUrl; ?>/modules/grupos_etarios">Grupos etarios</a></li>
			<li><a href="#" class="sinDesarrollar">Perfiles de usuario</a></li>
			<li> <a href="<?php echo $baseUrl; ?>/modules/usuarios"><span class="nav-label">Usuarios</span></a> </li>
			<!-- <li><a href="#" class="sinDesarrollar">Asignar usuarios a bodegas</a></li> -->
			<li><a href="<?= $baseUrl; ?>/modules/proveedores">Proveedores</a></li>
			<li>
				<a href="#"><span class="nav-label">Rutas</span> <span class="fa arrow"></span></a>
				<ul class="nav nav-third-level">
					<li><a href="<?php echo $baseUrl; ?>/modules/rutas/rutas.php">Listar Rutas</a></li>
					<li><a href="<?php echo $baseUrl; ?>/modules/rutas/ruta_nuevo.php">Nueva Ruta</a></li>
				</ul>
			</li>
			<li> <a href="<?php echo $baseUrl; ?>/modules/bodegas"><span class="nav-label">Bodegas</span></a> </li>
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
		</ul>
	</li>
<?php } ?>




<?php if( $_SESSION['perfil'] == 0 || $_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 8 || $_SESSION['perfil'] == 3 || $_SESSION['perfil'] == 5 || $_SESSION['perfil'] == 6 ){ ?>
	<li>
		<a href="#"><i class="fa fa-child"></i> <span class="nav-label">Asistencias</span><span class="fa arrow"></span></a>
		<ul class="nav nav-second-level collapse">

			<?php if( $_SESSION['perfil'] == 0 || $_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 8 || $_SESSION['perfil'] == 3 ){ ?>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias">Toma de asistencia</a> </li>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/repitentes.php"> Selección de repitentes </a> </li>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/consumo.php"> Registro de consumos </a> </li>
			<?php } ?>

			<?php if( $_SESSION['perfil'] == 0 || $_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 5  || $_SESSION['perfil'] == 6 ){ ?>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/informe_asistencia.php"> Informe de asistencia </a> </li>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/registro_biometrico.php"> Registro Biometrico </a> </li>
			<?php } ?>

			<?php if( $_SESSION['perfil'] == 0 || $_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 3 ){ ?>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/control_tablets.php"> Control de toma de asistencias </a> </li>
				<li> <a href="<?= $baseUrl; ?>/modules/asistencias/control_biometrico.php"> Control del registro biometrico </a> </li>
			<?php } ?>

		</ul>
	</li>
<?php } ?>





