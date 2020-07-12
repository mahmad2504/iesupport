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
		<th><div id="graphdiv1"></div></th>
		<th><div id="graphdiv2"></th>
	  </tr>
	  <tr>
		<td><div id="graphdiv3"></div></td>
		<td>Smith</td>

	  </tr>
	</table>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/dygraph/2.1.0/dygraph.min.js"></script>
  
	<script>
	    var graphdata1 = @json($graphdata1);
		var graphdata2 = @json($graphdata2);
		var start = "{{$start}}";
		var end = "{{$end}}";
		
		document.getElementById("start").defaultValue = start;
		document.getElementById("end").defaultValue = end;

		for(var i=0;i<graphdata1.length;i++)
		{
			graphdata1[i][0]=new Date(graphdata1[i][0]);
		}
		for(var i=0;i<graphdata2.length;i++)
		{
			graphdata2[i][0]=new Date(graphdata2[i][0]);
		}
		$(document).ready(function()
		{
			$( "#search" ).click(function() {
				var url="{{ route('index') }}"+"?start="+$('#start').val()+"&end="+$('#end').val();				;
				console.log(url);
				window.location.href = url;
			});
			new Dygraph(
              document.getElementById("graphdiv1"),
			  graphdata1,
			
              {
				//labels: [ "Date", "Created this week" ,"Total Created","Resolved this week","Total resolved","Defects resolved this week","Total defects resolved","Defects created this week","Total defects created"],
                labels: [ "Date", "w-created","created","w-fixed","fixed","w-dfixed","dfixed","w-dcreated","dcreated"],
				strokeWidth: 2,
				includeZero : true,
				title:"VSTARMOD",
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
				  'created': {
					   fillGraph:true,
					   color: '#ff0000',
				  },
				  'w-fixed': {
					   color: '#00ff00'
				  },
				  'fixed': {
					   color: '#00ff00',
					   pointSize: 0,
					   drawPoints: true,
					   fillGraph:true,
				  },
				  'w-dfixed': {
					   color: '#00ff00'
				  },
				  'dfixed': {
					   strokeWidth: 0.5,
					   color: '#006400',
					   pointSize: 1,
					   drawPoints: true,
					   fillGraph:true,
					  
				  },
				  'w-dcreated': {
					   color: '#00ff00'
					   
				  },
				  'dcreated': {
					   strokeWidth: 0.5,
					   pointSize: 1,
					   drawPoints: true,
					   color: '#FF8C00',
					   fillGraph:true,
				  }
                }
              }
			);
			new Dygraph(
              document.getElementById("graphdiv2"),
			  graphdata2,
			
              {
				//labels: [ "Date", "Created this week" ,"Total Created","Resolved this week","Total resolved","Defects resolved this week","Total defects resolved","Defects created this week","Total defects created"],
                labels: [ "Date", "w-created","created","w-fixed","fixed","w-dfixed","dfixed","w-dcreated","dcreated"],
				strokeWidth: 2,
				includeZero : true,
				title:"VOLSUP",
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
				  'created': {
					   fillGraph:true,
					   color: '#ff0000'
				  },
				  'w-fixed': {
					   color: '#00ff00'
				  },
				  'fixed': {
					   color: '#00ff00',
					   fillGraph:true,
				  },
				  'w-dfixed': {
					   color: '#00ff00'
				  },
				  'dfixed': {
					   strokeWidth: 0.5,
					   color: '#006400',
					   pointSize: 1,
					   drawPoints: true,
					   fillGraph:true,
					  
				  },
				  'w-dcreated': {
					   color: '#00ff00'
					   
				  },
				  'dcreated': {
					   strokeWidth: 0.5,
					   pointSize: 1,
					   drawPoints: true,
					   color: '#ff0000',
					   fillGraph:true,
				  }
                }
              }
			);
		});
	</script>
    </body>
</html>
