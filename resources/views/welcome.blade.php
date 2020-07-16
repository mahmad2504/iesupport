<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
		<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/dygraph/2.1.0/dygraph.min.css" />
        <!-- Styles -->
        <style>
		
        </style>
    </head>
    <body>
	<label style="margin-left:100px;" for="start">Start:</label>
    <input type="date" id="start" name="Start">
	
	<label style="margin-left:10px;" for="start">End:</label>
    <input type="date" id="end" name="End">
	
	<button id="search" name="search">Search</button>
	<hr>
	<table style="width:100%">
	  <tr>
		<th>
		<label style="float:left;margin-left:70px">VOLSUP</label>
		<select  style="float:left;margin-left:10px" name="select1" id="select1">
		  <option value="Volcano IVS,Volcano BL">IVS/BL</option>
		  <option value="Volcano IVS">IVS</option>
		  <option value="Volcano BL">BL</option>
		</select>
		<br>
		<div id="graphdiv1"></div>
		</th>
		<th>
		<label style="margin-left:70px;float:left;font-weight:bold;">VSTARMOD</label>
		<select  style="float:left;margin-left:10px" name="select2" id="select2">
		  <option value="CVBL,CVLTP">CVBL/CVLTP</option>
		  <option value="CVBL">CVBL</option>
		  <option value="CVLTP">CVLTP</option>
		</select>
	    <br>
		<div id="graphdiv2">
		</th>
	  </tr>
	  <tr>
		<td></td>
	

	  </tr>
	</table>
	<div style="width:700px;height:300px;">
	<canvas id="myChart" ></canvas>
	</div>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
	<script src="js/dygraph.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.0.0-alpha/Chart.min.js"></script>
	<script>
		var data1 = @json($graphdata1);
		var data2 = @json($graphdata2);
		
	    var graphdata1 = [];
		var graphdata2 = [];
		var start = "{{$start}}";
		var end = "{{$end}}";
		document.getElementById("start").defaultValue = start;
		document.getElementById("end").defaultValue = end;
		
		function Process(data)
		{
			for(var i=0;i<data.length;i++)
			{
				data[i][0]=new Date(data[i][0]);
			}
			return data;
		}
		graphdata1 = Process(data1);
		graphdata2 = Process(data2);
		function DrawGraph1()
		{
			var dygraph1 = new Dygraph(
			document.getElementById("graphdiv1"),
			graphdata1,
              {
				//labels: [ "Date", "Created this week" ,"Total Created","Resolved this week","Total resolved","Defects resolved this week","Total defects resolved","Defects created this week","Total defects created"],
                labels: [ "Date", "w-created","Created","w-fixed","Fixed","w-dfixed","Defect fixed","w-dcreated","Defect created"],
				strokeWidth: 2,
				includeZero : true,
				title:"",
				labelsSeparateLines: false,
                //legend: 'always',
				//colors: ['E69997', '#54A653', '#284785','#284785','#284785','#284785','#284785','#284785','#284785'],
				visibility: [false, true, false,true,false,true,false,true],
				showRangeSelector: false,	
				width: 700,
                height:400,
                series: {
				  'w-created': {
					   color: '#ff0000'
				  },
				  'Created': {
					   fillGraph:true,
					   color: '#ff0000',
				  },
				  'w-fixed': {
					   color: '#00ff00'
				  },
				  'Fixed': {
					   color: '#00ff00',
					   pointSize: 0,
					   drawPoints: true,
					   fillGraph:true,
				  },
				  'w-dfixed': {
					   color: '#00ff00'
				  },
				  'Defect fixed': {
					   strokeWidth: 0.5,
					   color: '#006400',
					   pointSize: 1,
					   drawPoints: true,
					   fillGraph:true,
					  
				  },
				  'w-dcreated': {
					   color: '#00ff00'
					   
				  },
				  'Defect created': {
					   strokeWidth: 0.5,
					   pointSize: 1,
					   drawPoints: true,
					   color: '#FF8C00',
					   fillGraph:true,
				  }
                }
              }
			);
		}
		function DrawGraph2()
		{
			Dygraph2 = new Dygraph(
			  document.getElementById("graphdiv2"),
			  graphdata2,
			
              {
				//labels: [ "Date", "Created this week" ,"Total Created","Resolved this week","Total resolved","Defects resolved this week","Total defects resolved","Defects created this week","Total defects created"],
                labels: [ "Date", "w-created","Created","w-fixed","Fixed","w-dfixed","Defect fixed","w-dcreated","Defect created"],
				strokeWidth: 2,
				includeZero : true,
				title:"",
				labelsSeparateLines: false,
                //legend: 'always',
				//colors: ['E69997', '#54A653', '#284785','#284785','#284785','#284785','#284785','#284785','#284785'],
				visibility: [false, true, false,true,false,true,false,false],
				showRangeSelector: false,	
				width: 700,
                height:400,
                series: {
				  'w-created': {
					   color: '#ff0000'
				  },
				  'Created': {
					   fillGraph:true,
					   color: '#ff0000'
				  },
				  'w-fixed': {
					   color: '#00ff00'
				  },
				  'Fixed': {
					   color: '#00ff00',
					   fillGraph:true,
				  },
				  'w-dfixed': {
					   color: '#00ff00'
				  },
				  'Defect fixed': {
					   strokeWidth: 0.5,
					   color: '#006400',
					   pointSize: 1,
					   drawPoints: true,
					   fillGraph:true,
					  
				  },
				  'w-dcreated': {
					   color: '#00ff00'
					   
				  },
				  'Defect created': {
					   strokeWidth: 0.5,
					   pointSize: 1,
					   drawPoints: true,
					   color: '#ff0000',
					   fillGraph:true,
				  }
                }
              }
			);
			
		}
		$(document).ready(function()
		{
			$( "#search" ).click(function() {
				var url="/data/VOLSUP"+"?start="+$('#start').val()+"&end="+$('#end').val()+"&issuetypes="+$('#select1').val();
				$.ajax({
					url: url,
				}).done(function(data) {
					graphdata1 = Process(data);
					DrawGraph1();
				});
				
				var url="/data/VSTARMOD"+"?start="+$('#start').val()+"&end="+$('#end').val()+"&components="+$('#select2').val();
				$.ajax({
					url: url,
				}).done(function(data) {
					graphdata2 = Process(data);
					DrawGraph2();
				});
				
			});
			$( "#select1" ).change(function() {
				var url="/data/VOLSUP"+"?start="+$('#start').val()+"&end="+$('#end').val()+"&issuetypes="+$('#select1').val();
				$.ajax({
					url: url,
				}).done(function(data) {
					graphdata1 = Process(data);
					DrawGraph1();
				});
			});
			$( "#select2" ).change(function() {
				var url="/data/VSTARMOD"+"?start="+$('#start').val()+"&end="+$('#end').val()+"&components="+$('#select2').val();
				$.ajax({
					url: url,
				}).done(function(data) {
					graphdata2 = Process(data);
					DrawGraph2();
				});
			});
			
			DrawGraph1();
			DrawGraph2();
           
		});
	
	</script>
    </body>
</html>
