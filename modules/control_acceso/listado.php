<?php
  include '../../header.php';
  set_time_limit (0);
  ini_set('memory_limit','6000M');
  $periodoActual = $_SESSION['periodoActual'];
  require_once '../../db/conexion.php';
?>

<style>
	.Salida{
		background-color: #F58634;
		width: 60%;
		display: block;
		color: #ffffff;
		border-radius: 3px;
	}
	.Entrada{
		background-color: #00A859;
		width: 60%;
		display: block;
		color: #ffffff;
		border-radius: 3px;
	}
</style>



<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
	<div class="col-md-6 col-lg-8">
		<h2>Listado de control de acceso</h2>
		<ol class="breadcrumb">
		  <li>
			<a href="<?php echo $baseUrl; ?>">Home</a>
		  </li>
		  <li class="active">
			<strong>Listado de control de accesso</strong>
		  </li>
		</ol>
	</div>
	<div class="col-md-6 col-lg-4">
		<div class="title-action">
			<?php if($_SESSION['perfil'] == 0 || $_SESSION['perfil'] == 1){ ?>
				<a href="<?php echo $baseUrl; ?>/modules/control_acceso/index.php" target="_blank" class="btn btn-primary"> <!-- <i class="fa fa-plus"></i>  --> Control de acceso</a>
			<?php } ?>
		</div>
	</div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
	<div class="row">
		<div class="col-lg-12">
			<div class="ibox float-e-margins">
				<div class="ibox-content contentBackground">
					<div class="row">
					<div class="col-sm-12 col-md-12">
					
					
					<div class="table-responsive">
					<table class="table table-striped table-bordered table-hover selectableRows" id="box-table-movimientos" >
					<thead>
					<tr>
					<th>Fecha / Hora</th>
					<th>Evento</th>
					<th>Nombre</th>
					<th>Documento</th>
				
					<th>Cargo</th>
					
					</tr>
					</thead>
					<tbody>



					<?php
					$consulta = " SELECT  DATE_FORMAT(cp.fecha, \"%d/%m/%Y %H:%i:%s\") AS fecha, IF(cp.tipo = 1, \"Entrada\", \"Salida\") as evento, e.Nombre, e.Nitcc, e.cargo FROM control_personal cp LEFT JOIN empleados e ON cp.num_doc = e.Nitcc ORDER BY cp.ID desc ";
					//echo "<br><br>$consulta2<br><br>";
					
					
					$resultado = $Link->query($consulta);
					if($resultado->num_rows > 0){
						while($row = $resultado->fetch_assoc()){
							?>
							<tr> 
								<td><?= $row['fecha'] ?></td> 
								<td style="text-align:center;"><span class="<?= $row['evento'] ?>"><?= $row['evento'] ?></span></td>
								<td><?= $row['Nombre'] ?></td> 
								<td><?= $row['Nitcc'] ?></td> 
								<td><?= $row['cargo'] ?></td> 
						

							<?php

						}
						//var_dump($row);
					}
					?>





	
					</tbody>
					<tfoot>
					<tr>
					<th>Fecha / Hora</th>
					<th>Evento</th>
					<th>Nombre</th>
					<th>Documento</th>
			
					<th>Cargo</th>
		
					</tr>
					</tfoot>
					</table>
					</div>		
					</div>
					</div>
					










		</div><!-- /.ibox-content -->
	  </div><!-- /.ibox float-e-margins -->
	</div><!-- /.col-lg-12 -->
  </div><!-- /.row -->
</div><!-- /.wrapper wrapper-content animated fadeInRight -->

<?php include '../../footer.php'; ?>

	<!-- Mainly scripts -->
	<script src="<?php echo $baseUrl; ?>/theme/js/jquery-3.1.1.min.js"></script>
	<script src="<?php echo $baseUrl; ?>/theme/js/bootstrap.min.js"></script>
	<script src="<?php echo $baseUrl; ?>/theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
	<script src="<?php echo $baseUrl; ?>/theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

	<script src="<?php echo $baseUrl; ?>/theme/js/plugins/dataTables/datatables.min.js"></script>

	<!-- Custom and plugin javascript -->
	<script src="<?php echo $baseUrl; ?>/theme/js/inspinia.js"></script>
	<script src="<?php echo $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>
	<script src="<?php echo $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>
	<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>


	<script src="<?php echo $baseUrl; ?>/modules/control_acceso/js/listado.js?v=20200423"></script>
	<script>
		$(document).ready(function(){

			var botonAcciones = '<div class="dropdown pull-right" id=""><button class="btn btn-primary btn-sm btn-outline" type="button" id="accionesTabla" data-toggle="dropdown" aria-haspopup="true">Acciones<span class="caret"></span></button><ul class="dropdown-menu pull-right" aria-labelledby="accionesTabla">';
			botonAcciones += '<li><a href="#" onclick="despachos_por_sede()">Individual</a></li>';
			botonAcciones += '<li><a href="#" onclick="despachos_por_sede_vertical()">Individual Vertical</a></li>';
			botonAcciones += '<li><a href="#" onclick="despachos_kardex()">Kardex</a></li>';
			botonAcciones += '<li><a href="#" onclick="despachos_kardex_multiple()">Kardex Múltiple</a></li>';
			botonAcciones += '<li><a href="#" onclick="despachos_consolidado()">Consolidado</a></li>';
			botonAcciones += '<li><a href="#" onclick="despachos_consolidado_x_sede()">Consolidado x Sedes</a></li>';
			botonAcciones += '<li><a href="#" onclick="despachos_consolidado_vertical()">Consolidado Vertical</a></li>';

			// Menu para COVID
			botonAcciones += '<li><a href="#" onclick="covid19_despachos_consolidado()">Entrega Raciones COVID-19</a></li>';

			botonAcciones += '<li><a href="#" onclick="despachos_agrupados()">Agrupado</a></li>';

			<?php if($_SESSION['perfil'] == 0 || $_SESSION['perfil'] == 1){ ?>
				botonAcciones += '<li><a href="#" onclick="editar_despacho()">Editar Despacho</a></li>';
				botonAcciones += '<li><a href="#" onclick="despachos_por_sede_fecha_lote()">Ingresar Lotes y Fechas de vencimiento</a></li>';
				botonAcciones += '<li><a href="#" onclick="eliminar_despacho()">Eliminar Despacho</a></li>';
			botonAcciones += '<?php } ?>';
			botonAcciones += '</ul></div>';

			$('.containerBtn').html(botonAcciones);

		});
	</script>


	<!-- Page-Level Scripts -->


<?php mysqli_close($Link); ?>

<form action="despacho_por_sede.php" method="post" name="formDespachoPorSede" id="formDespachoPorSede" target="_blank">
  <input type="hidden" name="despachoAnnoI" id="despachoAnnoI" value="">
  <input type="hidden" name="despachoMesI" id="despachoMesI" value="">
  <input type="hidden" name="despacho" id="despacho" value="">
</form>

<form action="despachos.php" id="parametrosBusqueda" method="get">
  <input type="hidden" id="pb_annoi" name="pb_annoi" value="">
  <input type="hidden" id="pb_mesi" name="pb_mesi" value="">
  <input type="hidden" id="pb_diai" name="pb_diai" value="">
  <input type="hidden" id="pb_annof" name="pb_annof" value="">
  <input type="hidden" id="pb_mesf" name="pb_mesf" value="">
  <input type="hidden" id="pb_diaf" name="pb_diaf" value="">
  <input type="hidden" id="pb_semana" name="pb_semana" value="">
  <input type="hidden" id="pb_tipo" name="pb_tipo" value="">
  <input type="hidden" id="pb_municipio" name="pb_municipio" value="">
  <input type="hidden" id="pb_institucion" name="pb_institucion" value="">
  <input type="hidden" id="pb_sede" name="pb_sede" value="">
  <input type="hidden" id="pb_tipoDespacho" name="pb_tipoDespacho" value="">
  <input type="hidden" id="pb_ruta" name="pb_ruta" value="">
  <input type="hidden" id="pb_btnBuscar" name="pb_btnBuscar" value="">
</form>

</body>
</html>
