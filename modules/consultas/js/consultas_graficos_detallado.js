var barData;
var barOptions = {
    responsive: true
};
var ctx2;
var myBarChart;


var doughnutData;
var doughnutOptions = {
    responsive: true
};
var ctx4;
var myPieChart;



$(document).ready(function(){
  graficar_resumida();
});




function graficar_resumida (){
  console.log("Graficando!");

  var municipio = $('#municipio').val();
  var institucion = $('#institucion').val();
  var sede = $('#sede').val();
  var estudiante = $('#estudiante').val();

  var diainicial = $('#diainicial').val();
  var mesinicial = $('#mesinicial').val();
  var annoinicial = $('#annoinicial').val();

  var diafinal = $('#diafinal').val();
  var mesfinal = $('#mesfinal').val();
  var annofinal = $('#annofinal').val();

  var segmento = $('#segmento').val();

  var datos = {
    "municipio":municipio,
    "institucion":institucion,
    "sede":sede,
    "estudiante":estudiante,
    "diainicial":diainicial,
    "mesinicial":mesinicial,
    "annoinicial":annoinicial,
    "diafinal":diafinal,
    "mesfinal":mesfinal,
    "annofinal":annofinal,
    "segmento":segmento
  };

  $.ajax({
    type:'post',
    url:'functions/fn_grafica_detallada.php',
    data:datos,
		beforeSend: function(){},
		success: function(data){
			try {
				var obj = JSON.parse(data);
        var total = 0;
        var total2 = 0;
        console.log(obj);

        barData = {
          labels: obj.titulos,
          datasets: [
            {
              label: 'Niños',
              backgroundColor: 'rgba(26,179,148,0.5)',
              borderColor: "rgba(26,179,148,0.7)",
              pointBackgroundColor: "rgba(26,179,148,1)",
              pointBorderColor: "#fff",
              data: obj.valores
            }
          ]
        };

        $('#barChart').remove();
        $('#contenedorPie').append('<canvas id="barChart" height="140"></canvas>');
        ctx2 = document.getElementById("barChart").getContext("2d");
        myBarChart = new Chart(ctx2, {type: 'bar', data: barData, options:barOptions});

















        // Grafica de pastel
        doughnutData = {
            labels: obj.titulos,
            datasets: [{
                data: obj.valores,
                backgroundColor: ["#a3e1d4","#dedede","#b5b8cf","#5c7f78", "#b9fff0", "#2e403c", "#a6e5d8"]
            }]
        } ;







        $('#doughnutChart').remove();

        $('#contenedorBar').append('<canvas id="doughnutChart" height="140"></canvas>');
        ctx4 = document.getElementById("doughnutChart").getContext("2d");
        myPieChart = new Chart(ctx4, {type: 'pie', data: doughnutData, options:doughnutOptions});


			}
			catch(err) {
				$('.debug').html(err.message);
				$('.debug').append('<br/><br/>');
				$('.debug').append(data);
			}
		}
  })
	.done(function(){ })
	.fail(function(){ })
	.always(function(){ console.log('Terminó el Ajax'); });
} /* Termina la función graficar_resumida */















$(function () {

var x= 0;

if(x != 0){



    var barData = {
        labels: ["January", "February", "March", "April", "May", "June", "July"],
        datasets: [
            {
              label: "Data 2",
              backgroundColor: 'rgba(26,179,148,0.5)',
              borderColor: "rgba(26,179,148,0.7)",
              pointBackgroundColor: "rgba(26,179,148,1)",
              pointBorderColor: "#fff",
              data: [28, 48, 40, 19, 86, 27, 90]
            }
            /*,
            {
                label: "Data 2",
                backgroundColor: 'rgba(26,179,148,0.5)',
                borderColor: "rgba(26,179,148,0.7)",
                pointBackgroundColor: "rgba(26,179,148,1)",
                pointBorderColor: "#fff",
                data: [28, 48, 40, 19, 86, 27, 90]
            }*/
        ]
    };

    var barOptions = {
        responsive: true
    };


    var ctx2 = document.getElementById("barChart").getContext("2d");
    new Chart(ctx2, {type: 'bar', data: barData, options:barOptions});

    var polarData = {
        datasets: [{
            data: [
                300,140,200
            ],
            backgroundColor: [
                "#a3e1d4", "#dedede", "#b5b8cf"
            ],
            label: [
                "My Radar chart"
            ]
        }],
        labels: [
            "App","Software","Laptop"
        ]
    };

    var polarOptions = {
        segmentStrokeWidth: 2,
        responsive: true

    };



    var doughnutData = {
        labels: ["App","Software","Laptop" ],
        datasets: [{
            data: [300,50,100],
            backgroundColor: ["#a3e1d4","#dedede","#b5b8cf"]
        }]
    } ;


    var doughnutOptions = {
        responsive: true
    };


    var ctx4 = document.getElementById("doughnutChart").getContext("2d");
    new Chart(ctx4, {type: 'pie', data: doughnutData, options:doughnutOptions});


    var radarData = {
        labels: ["Eating", "Drinking", "Sleeping", "Designing", "Coding", "Cycling", "Running"],
        datasets: [
            {
                label: "My First dataset",
                backgroundColor: "rgba(220,220,220,0.2)",
                borderColor: "rgba(220,220,220,1)",
                data: [65, 59, 90, 81, 56, 55, 40]
            },
            {
                label: "My Second dataset",
                backgroundColor: "rgba(26,179,148,0.2)",
                borderColor: "rgba(26,179,148,1)",
                data: [28, 48, 40, 19, 96, 27, 100]
            }
        ]
    };

    var radarOptions = {
        responsive: true
    };

}

});
