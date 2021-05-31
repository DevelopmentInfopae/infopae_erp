<div class="row">
  <div class="col-sm-12 form-group">
    <h2>Gráfica de resultados</h2>
	</div>
</div>

<form class="" action="index.html" method="post">
  <div class="row">
    <div class="col-sm-12 form-group">
      <label for="detallado">Segmentar por:</label>
      <div class="row">
        <div class="col-sm-6 form-group">
          <select id="segmento" name="segmento" class="form-control">
            <option value="1">Edad</option>
            <option value="2" selected="selected">Estrato</option>
            <option value="3">Sisben</option>
            <option value="4">Discapacidad</option>
            <option value="5">Etnia</option>
            <!-- <option value="6">Resguardo</option> -->
            <option value="7">Población Victima</option>
            <option value="8">Grado</option>
            <option value="9">Jornada Escolar</option>
            <option value="10">Zona Residencia Estudiante</option>
            <select>
        </div>
        <div class="col-sm-1 form-group">
          <button type="button" onclick="graficar_resumida();" class="btn btn-primary">Graficar</button>
        </div>
      </div>
    </div>
  </div>
</form>

<div class="row">
  <div class="col-lg-6">
    <div class="ibox float-e-margins">
      <div class="ibox-content">
        <div id="contenedorPie">
          <canvas id="doughnutChart" height="140"></canvas>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="ibox float-e-margins">
      <div class="ibox-content">
        <div id="contenedorBar">
          <canvas id="barChart" height="140"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>
