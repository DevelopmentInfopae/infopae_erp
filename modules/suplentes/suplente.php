<?php
include '../../header.php';
set_time_limit (0);
ini_set('memory_limit','6000M');

if ($permisos['titulares_derecho'] == "0") {
    ?><script type="text/javascript">
      window.open('<?= $baseUrl ?>', '_self');
    </script>
<?php exit(); }

$periodoActual = $_SESSION['periodoActual'];
require_once '../../db/conexion.php';
$Link = new mysqli($Hostname, $Username, $Password, $Database);
if ($Link->connect_errno) {
    echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
$Link->set_charset("utf8");
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  	<div class="col-lg-12">
      <h2>Titulares de derecho</h2>
		<ol class="breadcrumb">
			<li>
			 	<a href="<?php echo $baseUrl; ?>">Home</a>
			</li>
			<li class="active">
			  	<strong>Titulares de derecho</strong>
			</li>
		</ol>
  	</div>
</div>


<div class="wrapper wrapper-content  animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox">
                <div class="ibox-content">
					<h3>Seleccione la semana de focalización</h3>
					<?php
					$consulta = " select distinct semana from planilla_semanas ";
					$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
					if($resultado->num_rows >= 1){
						while($row = $resultado->fetch_assoc()){
							$aux = $row['semana'];
							$consulta2 = " show tables like 'focalizacion$aux' ";
							$resultado2 = $Link->query($consulta2) or die ('Unable to execute query. '. mysqli_error($Link));
							if($resultado2->num_rows >= 1){
							 $semanas[] = $aux;
							}
						}
					}
					//var_dump($semanas);
					?>
					<div class="row">
						<div class="col-sm-12">
							<form class="" action="" method="get">
								<div class="row">
									<div class="col-sm-3 form-group">
										<label for="semana">Semana</label>
										<select class="form-control" name="semana" id="semana">
											<option value="">Seleccione una</option>
											<?php foreach ($semanas as $semana){ ?>
												<option value="<?php echo $semana; ?>" <?php if(isset($_GET['semana']) && $_GET['semana'] == $semana){echo " selected "; }  ?>><?php echo $semana; ?></option>
											<?php } ?>
										</select>
									</div>
									<div class="col-sm-3">
									</div>
								</div>
								<div class="row">
									<div class="col-sm-3 form-group">
										<button class="btn btn-primary" type="submit">Buscar</button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>






<?php
if( isset($_GET['semana']) && $_GET['semana'] !='' ){
	$semana = $_GET['semana'];
?>

<div class="wrapper wrapper-content  animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
              <div class="ibox">
                  <div class="ibox-content">
                      <div class="row">
                    	<div class="col-sm-12">

                        <div class="table-responsive">

                    <table class="table table-striped table-hover dataTables-sedes" >
						<thead>
							<tr>
								<th>Num doc</th>
								<th>Tipo doc</th>
								<th>Nombre</th>
								<th>Género</th>
								<th>Grado</th>
								<th>Grupo</th>
								<th>Jornada</th>
								<th>Edad</th>
								<th>Tipo complemento</th>
							</tr>
						</thead>
						<tbody>
							<?php

							$consulta = " SELECT f.num_doc, t.Abreviatura AS tipo_doc, CONCAT(f.nom1, ' ', f.nom2, ' ', f.ape1, ' ', f.ape2) AS nombre, f.genero, g.nombre as grado, f.nom_grupo, jor.nombre as jornada, f.edad, f.Tipo_complemento FROM focalizacion$semana f LEFT JOIN tipodocumento t ON t.id = f.tipo_doc LEFT JOIN grados g ON g.id = f.cod_grado LEFT JOIN jornada jor ON jor.id = f.cod_jorn_est order by f.nom1 asc ";

							$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
							if($resultado->num_rows >= 1){
								while($row = $resultado->fetch_assoc()){ ?>
								<tr numDoc="<?php echo $row['num_doc']; ?>" tipoDoc="<?php echo $row['tipo_doc']; ?>" style="cursor:pointer">
									<td><?php echo $row['num_doc']; ?></td>
									<td><?php echo $row['tipo_doc']; ?></td>
									<td><?php echo $row['nombre']; ?></td>
									<td style="text-align_center;"><?php echo $row['genero']; ?></td>
									<td><?php echo $row['grado']; ?></td>
									<td style="text-align:center;"><?php echo $row['nom_grupo']; ?></td>
									<td><?php echo $row['jornada']; ?></td>
									<td style="text-align:center;"><?php echo $row['edad']; ?></td>
									<td style="text-align:center;"><?php echo $row['Tipo_complemento']; ?></td>
								</tr>
								<?php }
							}
							?>
						</tbody>
						<tfoot>
							<tr>
								<th>Num doc</th>
								<th>Tipo doc</th>
								<th>Nombre</th>
								<th>Genero</th>
								<th>Grado</th>
								<th>Grupo</th>
								<th>Jornada</th>
								<th>Edad</th>
								<th>Tipo COMP</th>
							</tr>
						</tfoot>
                    </table>
                        </div>
                        <!-- Termina table responsive -->
                        </div>
                      </div>
                  </div>
              </div>
          </div>
     </div>
 </div>

<?php } ?>


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


<script src="<?php echo $baseUrl; ?>/modules/titulares_derecho/js/titulares.js"></script>



<!-- Page-Level Scripts -->


<?php mysqli_close($Link); ?>

<form action="despacho_por_sede.php" method="post" name="formDespachoPorSede" id="formDespachoPorSede">
  <input type="hidden" name="despachoAnnoI" id="despachoAnnoI" value="">
  <input type="hidden" name="despachoMesI" id="despachoMesI" value="">
  <input type="hidden" name="despacho" id="despacho" value="">
</form>

<form action="despachos.php" id="parametrosBusqueda" method="get">
  <input type="hidden" id="pb_annoi" name="pb_annoi" value="">
  <input type="hidden" id="pb_mes" name="pb_mes" value="">
  <input type="hidden" id="pb_diai" name="pb_diai" value="">
  <input type="hidden" id="pb_annof" name="pb_annof" value="">
  <input type="hidden" id="pb_mesf" name="pb_mesf" value="">
  <input type="hidden" id="pb_diaf" name="pb_diaf" value="">
  <input type="hidden" id="pb_tipo" name="pb_tipo" value="">
  <input type="hidden" id="pb_municipio" name="pb_municipio" value="">
  <input type="hidden" id="pb_institucion" name="pb_institucion" value="">
  <input type="hidden" id="pb_sede" name="pb_sede" value="">
  <input type="hidden" id="pb_tipoDespacho" name="pb_tipoDespacho" value="">
  <input type="hidden" id="pb_ruta" name="pb_ruta" value="">
  <input type="hidden" id="pb_btnBuscar" name="pb_btnBuscar" value="">
</form>


    <!-- Page-Level Scripts -->
    <script>
        $(document).ready(function(){
            $('.dataTables-sedes').DataTable({
                pageLength: 25,
                responsive: true,
                dom: '<"html5buttons"B>lTfgitp',
                  oLanguage: {
      sLengthMenu: 'Mostrando _MENU_ registros',
      sZeroRecords: 'No se encontraron registros',
      sInfo: 'Mostrando _START_ a _END_ de _TOTAL_ registros ',
      sInfoEmpty: 'Mostrando 0 a 0 de 0 registros',
      sInfoFiltered: '(Filtrado desde _MAX_ registros)',
      sSearch:         'Buscar: ',
      oPaginate:{
        sFirst:    'Primero',
        sLast:     'Último',
        sNext:     'Siguiente',
        sPrevious: 'Anterior'
      }
    },
                buttons: [ {extend: 'excel', title: 'ExampleFile'} ]

            });

        });

    </script>

<form action="titular.php" method="get" name="verTitular" id="verTitular">
  <input type="hidden" name="numDoc" id="numDoc">
  <input type="hidden" name="tipoDoc" id="tipoDoc">
  <input type="hidden" name="semana" id="semana" value="<?php if(isset($_GET['semana']) && $_GET['semana'] != ''){echo $_GET['semana']; }?>">
</form>




</body>
</html>
