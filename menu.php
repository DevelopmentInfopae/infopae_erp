<li class="active">
	<a href="<?php echo $baseUrl; ?>"><i class="fa fa-home"></i> <span class="nav-label">Inicio</span></a>
</li>

<?php if($_SESSION['perfil'] == 6){ ?>
	<li> <a href="<?php echo $baseUrl; ?>/modules/instituciones/institucion.php"><i class="fa fa-graduation-cap"></i> <span class="nav-label">Mi institución</span></a> </li>

	<li>
		<a href="#"><i class="fa fa-th-large"></i> <span class="nav-label">Entregas de Complementos Alimentarios</span><span class="fa arrow"></span></a>
		<ul class="nav nav-second-level collapse">
			<li><a href="<?php echo $baseUrl; ?>/modules/consultas/consulta_resumida_entregas.php">Consulta resumida</a></li>
		</ul>
	</li>
<?php } ?>







<?php if( $_SESSION['perfil'] == 0 || $_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 5 ){ ?>

	<li> <a href="<?php echo $baseUrl; ?>/modules/instituciones/instituciones.php"><i class="fa fa-graduation-cap"></i> <span class="nav-label">Instituciones</span></a> </li>
	<li> <a href="<?php echo $baseUrl; ?>/modules/instituciones/sedes.php"><i class="fa fa-bank"></i> <span class="nav-label">Sedes educativas</span></a> </li>
	<li>
		<a href="#"><i class="fa fa-th-large"></i> <span class="nav-label">Titulares de Derecho</span><span class="fa arrow"></span></a>
		<ul class="nav nav-second-level collapse">
			<li> <a href="<?php echo $baseUrl; ?>/modules/titulares_derecho/index.php"><i class="fa fa-child"></i> <span class="nav-label">Derecho</span></a> </li>
			<li> <a href="<?php echo $baseUrl; ?>/modules/suplentes/index.php"><i class="fa fa-tags"></i> <span class="nav-label">Suplentes</span></a> </li>
		</ul>
	</li>

	<li>
		<a href="#"><i class="fa fa-th-large"></i> <span class="nav-label">Menús</span><span class="fa arrow"></span></a>
		<ul class="nav nav-second-level collapse">
			<li> <a href="<?php echo $baseUrl; ?>/modules/menus2/ver_alimentos.php"><span class="nav-label">Alimentos</span></a> </li>
			<li> <a href="<?php echo $baseUrl; ?>/modules/menus2/ver_preparaciones.php"><span class="nav-label">Preparaciones</span></a> </li>
			<li> <a href="<?php echo $baseUrl; ?>/modules/menus2"><span class="nav-label">Menús</span></a> </li>
			<li> <a href="<?php echo $baseUrl; ?>/modules/menu_valref"><span class="nav-label">Aportes calúricos y nutricionales</span></a> </li>
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
		</ul>
	</li>


	<li>
		<a href="<?php echo $baseUrl; ?>/modules/despachos/despachos.php"><i class="fa fa-truck"></i> <span class="nav-label">Despachos</span> <span class="fa arrow"></span></a>
		<ul class="nav nav-second-level">
			<li><a href="<?php echo $baseUrl; ?>/modules/despachos/despachos.php">Alimentos</a></li>
			<li><a href="<?php echo $baseUrl; ?>/modules/ordenes_compra/ordenes.php">Órdenes de compra</a></li>
			<li> <a href="<?php echo $baseUrl; ?>/modules/insumos/despachos.php"><span class="nav-label">Insumos</span></a> </li>
		</ul>
	</li>


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
		<a href="#"><i class="fa fa-th-large"></i> <span class="nav-label">Novedades</span><span class="fa arrow"></span></a>
		<ul class="nav nav-second-level collapse">
			<li><a href="<?php echo $baseUrl; ?>/modules/novedades_priorizacion/index.php">Priorización</a></li>
			<li><a href="<?php echo $baseUrl; ?>/modules/novedades_ejecucion/index.php">Focalización</a></li>
		</ul>
	</li>


	<li>
		<a href="#"><i class="fa fa-th-large"></i> <span class="nav-label">Informes</span><span class="fa arrow"></span></a>
		<ul class="nav nav-second-level collapse">
			<li> <a href="<?php echo $baseUrl; ?>/modules/trazabilidad/index.php"><span class="nav-label">Trazabilidad</span></a> </li>
			<li> <a href="<?php echo $baseUrl; ?>/modules/trazabilidad_insumos/index.php"><span class="nav-label">Trazabilidad Insumos</span></a> </li>
			<li> <a href="<?php echo $baseUrl; ?>/modules/estadisticas/index.php">Estadisticas</a></li>
			<li><a href="#" class="sinDesarrollar">Bitácora de usuarios</a></li>
			<li><a href="#" class="sinDesarrollar">Informe CHIP</a></li>
			<li><a href="#" class="sinDesarrollar">Informe de alimentos ordenados por proveedor</a></li>
			<li> <a href="<?php echo $baseUrl; ?>/modules/insumos/insumos_proveedor.php"><span class="nav-label">Informe de Insumos ordenados por proveedores</span></a> </li>
			<li><a href="#" class="sinDesarrollar">Informe de compras efectuadas al proveedor</a></li>
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
			<li><a href="<?= $baseUrl; ?>/modules/tipo_despachos">Tipo despacho</a></li>
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


<li>
	<a href="#"><i class="fa fa-child"></i> <span class="nav-label">Asistencias</span><span class="fa arrow"></span></a>
	<ul class="nav nav-second-level collapse">
		<li> <a href="<?php echo $baseUrl; ?>/modules/asistencias"><span class="nav-label">Toma de asistencia</span></a> </li>
		<li> <a href="<?php echo $baseUrl; ?>/modules/asistencias/repitentes.php"><span class="nav-label">Selección de repitentes</span></a> </li>
		<li> <a href="<?php echo $baseUrl; ?>/modules/asistencias"><span class="nav-label">Registro de consumos</span></a> </li>
	</ul>
</li>
