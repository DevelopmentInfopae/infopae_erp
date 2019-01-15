// npm install -save express
// npm install -save socket.io
var http = require('http');
url = require('url');
var port = 8080;
http.createServer(function (req, res){
	res.setHeader('Access-Control-Allow-Origin', '*');
	res.setHeader('Access-Control-Request-Method', '*');
	res.setHeader('Access-Control-Allow-Methods', 'OPTIONS, GET');
	res.setHeader('Access-Control-Allow-Headers', '*');


	if(url.parse(req.url,true)){
		
		var query = url.parse(req.url,true).query;
	    var parametros = JSON.stringify(query);
	    var objectValue = JSON.parse(parametros);
	    var nombre = objectValue['nombre'];
	    var dispositivo = objectValue['dispositivo'];
	    var nombreExcel = nombre.toLowerCase();
		res.writeHead(200,{'Content-type': 'text/html'});
		
		
		//Proceso para generar el archivo de Excel
		var backup2xls = require('anviz-backup2xls');
		backup2xls(dispositivo+'/'+nombre+'.KQ', dispositivo+'/'+nombreExcel+'.xlsx');
		console.log('Se ha creado el archivo'+nombreExcel+'.xlsx');


		//Proceso para generar el archivo CSV
			var xlsx = require('node-xlsx');
			var fs = require('fs');
			var obj = xlsx.parse(dispositivo+'/'+nombreExcel+'.xlsx'); // parses a file
			var rows = [];
			var writeStr = "";

			//looping through all sheets
			for(var i = 0; i < obj.length; i++)
			{
			    var sheet = obj[i];
			    //loop through all rows in the sheet
			    for(var j = 0; j < sheet['data'].length; j++)
			    {
			        //add the row to the rows array
			        rows.push(sheet['data'][j]);
			    }
			}

			//creates the csv string to write it to a file
			for(var i = 0; i < rows.length; i++)
			{
			    writeStr += rows[i].join(",") + "\n";
			}

			//writes to a file, but you will presumably send the csv as a      
			//response instead
			fs.writeFile(dispositivo+'/'+nombreExcel+'.csv', writeStr, function(err) {
			    if(err) {
			        return console.log(err);
			    }
			    console.log('Se ha creado el archivo'+nombreExcel+'.csv');
			});
		//Termina el proceso para generar el archivo CSV


		res.write('1');

	}
	res.end();
}).listen(port);
console.log('El servidor esta corriendo en el puerto '+port);