<!DOCTYPE html>
<html>
	<head>
		<title>Belgrade Lakes Water Clarity Project</title>
		<meta name="viewport" content="initial-scale=1.0">
		<link rel="icon" type="image/png" href="seal.png">
		<link href="http://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css" rel="stylesheet">
    		<meta charset="utf-8">
		<style>
			html, body {
        			height: 100%;
        			margin: 0;
        			padding: 0;
      			}
      			#container {
  				width: 100%;
  				height: 100%;
  				position: relative;
			}
			#box {
				width: 30%;
				height: 10%;
				color: white;
				font-family: "Helvetica Neue", Helvetica, sans-serif;
				text-align: center;
				top: 50px;
				left: 50px;
				position: absolute;
				z-index: 10;
			}
			#box h1 {
				font-size: 1.4em;
			}
			#legend {
				width: 300px;
				margin: auto;
			}
			#legend h2 {
				font-size: 1.2em;
			}
			#map {
				height: 100%;
			}
			h1, p {
				text-align: center;
			}
			#window {
				position: relative;
				background: url(cross_section.png) no-repeat;
				padding-left: 200px;
				padding-right: 200px;
				padding-top: 10px;
				padding-bottom: 100px;
				margin-bottom: 100px;
        			background-size: 440px 220px;
      				background-position: center;
			}
 			#secchiSlider {
				padding-top: 20px;
				height: 75px;
        			width: 1px;
        			margin: auto;
				margin-top: 60px;
				background-position: center;
      			}
     		 	#secchiSlider .ui-slider-handle {
      				background: url(secchi.png) no-repeat scroll 100% 100%;
    				height: 75px;
    				width: 85px;
    				border-style: none;
				margin-left: -37px;
	 		}
			.gm-style-iw {
				height: 400px;
				width: 300px;
				padding: 0px;
				line-height: 200%;
      			}
		</style>
		<?php include('dataHandler.php') ?>
	</head>
	<body>
		<div id="container">
  			<div id="box">
  				<h1>Water Clarity and Property Values Simulation</h1>
  				<p>Click on a lake to see its current state. Move the Secchi disk in the slider to change the clarity.</p>
				<a href="about2.html" target="_blank" style="color: white;">Learn More</a>
				<div id="legend">
					<h2>Lake Border Legend</h2>
					<div style="background-color: #B22222">Property Value loss: $7500 - $15000</div>
					<div style="background-color: #FF4500">Property Value loss: $1 - $7500</div>
					<div style="background-color: #40A4DF">No change in property value</div>
					<div style="background-color: #000080">Property Value gain: $1 - $15000</div>
				</div>
  			</div>
			<div id="map"></div>
		</div>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
		<script>
		// the base layer google map
		var map;
		
		// starting data
		var greatPondSecchi = <?php echo($greatPond) ?>;
		var eastPondSecchi = <?php echo($eastPond) ?>;
		var northPondSecchi = <?php echo($northPond) ?>;
		var longPondUpperSecchi = <?php echo($upperLongPond) ?>;
		var longPondLowerSecchi = <?php echo($lowerLongPond) ?>;
		var snowPondSecchi = <?php echo($snowPond) ?>;
		var salmonLakeSecchi = <?php echo($salmonLake) ?>;

		var epDate = "<?php echo($epDate) ?>";
		var epDO = <?php echo($epDO) ?>;
		var epT = (<?php echo($epT) ?>);

		var npDate = "<?php echo($npDate) ?>";
		var npDO = <?php echo($npDO) ?>;
		var npT = (<?php echo($npT) ?>);

		var gpDate = "<?php echo($gpDate) ?>";
		var gpDO = <?php echo($gpDO) ?>;
		var gpT = (<?php echo($gpT) ?>);

		var lpuDate = "<?php echo($lpuDate) ?>";
		var lpuDO = <?php echo($lpuDO) ?>;
		var lpuT = (<?php echo($lpuT) ?>);

		var lplDate = "<?php echo($lplDate) ?>";
		var lplDO = <?php echo($lplDO) ?>;
		var lplT = (<?php echo($lplT) ?>);

		var spDate = "<?php echo($spDate) ?>";
		var spDO = <?php echo($spDO) ?>;
		var spT = (<?php echo($spT) ?>);

		var slDate = "<?php echo($slDate) ?>";
		var slDO = <?php echo($slDO) ?>;
		var slT = (<?php echo($slT) ?>);
		
		// the watershed layer
		var eastPondPolygon;
		var northPondPolygon;
		var greatPondPolygon;
		var longPondUpperPolygon;
		var longPondLowerPolygon;
		var snowPondPolygon;
		var salmonLakePolygon;
		// the color of a healthy lake
		var blue = '#40a4df';
		// the color of a bloomed lake
		var green = '#6dad5e';
      		
      		// initialize the map with town boundaries and markers
      		function initMap() {
      			// create the map, centered at Great Pond
       			map = new google.maps.Map(document.getElementById('map'), {
          			center: {lat: 44.54252, lng: -69.85318},
          			zoom: 12,
          			mapTypeId: google.maps.MapTypeId.SATELLITE,
          			mapTypeControl: false
        		});
      			
      			// get the lat/lng coordinates for East Pond
  			var eastPondCoords = [];
  			for (var i = 0; i < eastPondArray.length; i += 2) {
  				var coord = new google.maps.LatLng(eastPondArray[i+1], eastPondArray[i]);
  				eastPondCoords.push(coord);
  			}

  			// construct East Pond
 			eastPondPolygon = new google.maps.Polygon({
    				paths: eastPondCoords,
    				strokeColor: blue,
    				strokeOpacity: 0.8,
    				strokeWeight: 5,
    				fillColor: blue,
    				fillOpacity: 0.25
  			});
  			eastPondPolygon.setMap(map);

  			// add a listener to East Pond to open info window
  			eastPondPolygon.addListener('click', function() {
  				eastPondWindow.open(map, eastPondMarker);
				northPondWindow.close();
				greatPondWindow.close();
				longPondUpperWindow.close();
				longPondLowerWindow.close();
				snowPondWindow.close();
				salmonLakeWindow.close();
  			});

  			// create East Pond marker and info window
      			var eastPondWindow = new google.maps.InfoWindow({
      				content: "<h1>East Pond</h1><p id=\"epout\">Secchi: " + eastPondSecchi + "<br>Deep Oxygen (%): " + epDO + "<br>Surface Temperature (°F): " + epT + "<br>Observation date: " + epDate + "</p><div id=\"window\"><div id=\"secchiSlider\"><div id=\"current\">Current</div><div id=\"min\">2015 Low</div><div id=\"max\">2015 High</div></div></div>"
  	  		});
  	  		var eastPondMarker = new google.maps.Marker({
      				position: {lat: 44.61156, lng: -69.78454},
      				map: map,
      				title: 'East Pond'
  			});

			// add listener to East Pond marker
  			eastPondMarker.addListener('click', function() {
  				eastPondWindow.open(map, eastPondMarker);
				northPondWindow.close();
				greatPondWindow.close();
				longPondUpperWindow.close();
				longPondLowerWindow.close();
				snowPondWindow.close();
				salmonLakeWindow.close();
  			});

			// add Secchi visualization slider
			google.maps.event.addListener(eastPondWindow, 'domready', function() {
				// load and display historical data
				var epStart = <?php echo($eastPond) ?>;
				var epMin = <?php echo($eastPondMin) ?>;
				var epMax = <?php echo($eastPondMax) ?>;
				
				$( "#secchiSlider" ).slider({
            				orientation: "vertical",
            				max: 9,
            				min: 0,
            				value: 10-eastPondSecchi,
					// this updates the live display of the value
					slide: function(event, ui) {
						var dep = 10-ui.value;
						$("#epout").html("Secchi: " + dep + "<br>Deep Oxygen (%): " + epDO + "<br>Surface Temperature (°F): " + epT + "<br>Observation date: " + epDate);
      						eastPondUpdate(dep, epMin, epMax);
    					}
				});
	
				$(function() {
             				$("#current").css({
                				position: "absolute",
						textIndent: "80px",
						lineHeight: "3px",
						background: "#000000",
						width: "66px",
						height: "2px",
						top: (epStart*10-10) + "px",
						left: "-33px"
					});
				});

				$(function() {
             				$("#min").css({
                				position: "absolute",
						textIndent: "-76px",
						lineHeight: "3px",
						background: "#FFFF00",
						width: "66px",
						height: "2px",
						top: (epMin*10-10) + "px",
						left: "-33px"
					});
				});

				$(function() {
             				$("#max").css({
                				position: "absolute",
						textIndent: "-76px",
						lineHeight: "3px",
						background: "#FFFF00",
						width: "66px",
						height: "2px",
						top: (epMax*10-10) + "px",
						left: "-33px"
					});
				});
				$(function() { $("#current").attr("title", epStart) });
				$(function() { $("#min").attr("title", epMin) });
				$(function() { $("#max").attr("title", epMax) });
			});
  		
  			// get the LatLng coordinates for North Pond
			var northPondCoords = [];
  			for (var i = 0; i < northPondArray.length; i += 2) {
  				var coord = new google.maps.LatLng(northPondArray[i+1], northPondArray[i]);
  				northPondCoords.push(coord);
  			}

  			// construct North Pond
 			northPondPolygon = new google.maps.Polygon({
    				paths: northPondCoords,
    				strokeColor: blue,
    				strokeOpacity: 0.8,
    				strokeWeight: 5,
    				fillColor: blue,
    				fillOpacity: 0.25
  			});
  			northPondPolygon.setMap(map);

  			// add a listener to North Pond to open info window
  			northPondPolygon.addListener('click', function() {
  				northPondWindow.open(map, northPondMarker);
				eastPondWindow.close();
				snowPondWindow.close();
				greatPondWindow.close();
				longPondUpperWindow.close();
				longPondLowerWindow.close();
				salmonLakeWindow.close();
  			});

  			// create the North Pond marker and info window
  			var northPondWindow = new google.maps.InfoWindow({
  				content: "<h1>North Pond</h1><p id=\"npout\">Secchi: " + northPondSecchi + "<br>Deep Oxygen (%): " + npDO + "<br>Surface Temperature (°F): " + npT + "<br>Observation date: " + npDate + "</p><div id=\"window\"><div id=\"secchiSlider\"><div id=\"current\">Current</div><div id=\"min\">2015 Low</div><div id=\"max\">2015 High</div></div></div>"
  			});
  			var northPondMarker = new google.maps.Marker({
  				position: {lat: 44.62558, lng: -69.83830},
  				map: map,
  				title: 'North Pond'
  			});

			// add listener to North Pond marker
			northPondMarker.addListener('click', function() {
  				northPondWindow.open(map, northPondMarker);
				eastPondWindow.close();
				snowPondWindow.close();
				greatPondWindow.close();
				longPondUpperWindow.close();
				longPondLowerWindow.close();
				salmonLakeWindow.close();
  			});

			// construct visualization slider
			google.maps.event.addListener(northPondWindow, 'domready', function() {
				// load and display historical data
				var npStart = <?php echo($northPond) ?>;
				var npMin = <?php echo($northPondMin) ?>;
				var npMax = <?php echo($northPondMax) ?>;

      				$( "#secchiSlider" ).slider({
            				orientation: "vertical",
            				max: 9,
            				min: 0,
            				value: 10-northPondSecchi,
					// this updates the live display of the value
					slide: function(event, ui) {
      						var dep = 10-ui.value;
						$("#npout").html("Secchi: " + dep + "<br>Deep Oxygen (%): " + npDO + "<br>Surface Temperature (°F): " + npT + "<br>Observation date: " + npDate);
      						northPondUpdate(dep, npMin, npMax);
    					}
            			})
				
				$(function() {
             				$("#current").css({
                				position: "absolute",
						textIndent: "80px",
						lineHeight: "3px",
						background: "#000000",
						width: "66px",
						height: "2px",
						top: (npStart*10-10) + "px",
						left: "-33px"
					});
				});

				$(function() {
             				$("#min").css({
                				position: "absolute",
						textIndent: "-76px",
						lineHeight: "3px",
						background: "#FFFF00",
						width: "66px",
						height: "2px",
						top: (npMin*10-10) + "px",
						left: "-33px"
					});
				});

				$(function() {
             				$("#max").css({
                				position: "absolute",
						textIndent: "-76px",
						lineHeight: "3px",
						background: "#FFFF00",
						width: "66px",
						height: "2px",
						top: (npMax*10-10) + "px",
						left: "-33px"
					});
				});
				$(function() { $("#current").attr("title", npStart) });
				$(function() { $("#min").attr("title", npMin) });
				$(function() { $("#max").attr("title", npMax) });
			});

  			// get the LatLng coordinates for Great Pond
  			var greatPondCoords = [];
  			for (var i = 0; i < greatPondArray.length; i += 2) {
  				var coord = new google.maps.LatLng(greatPondArray[i+1], greatPondArray[i]);
  				greatPondCoords.push(coord);
  			}

  			// construct Great Pond
 			greatPondPolygon = new google.maps.Polygon({
    				paths: greatPondCoords,
    				strokeColor: blue,
    				strokeOpacity: 0.8,
    				strokeWeight: 5,
    				fillColor: blue,
    				fillOpacity: 0.25
  			});
  			greatPondPolygon.setMap(map);

  			// add a listener to Great Pond to open info window
  			greatPondPolygon.addListener('click', function() {
  				greatPondWindow.open(map, greatPondMarker);
				eastPondWindow.close();
				northPondWindow.close();
				snowPondWindow.close();
				longPondUpperWindow.close();
				longPondLowerWindow.close();
				salmonLakeWindow.close();
  			});

  			// create Great Pond marker and info window
  			var greatPondWindow = new google.maps.InfoWindow({
  				content: "<h1>Great Pond</h1><p id=\"gpout\">Secchi: " + greatPondSecchi + "<br>Deep Oxygen (%): " + gpDO + "<br>Surface Temperature (°F): " + gpT + "<br>Observation date: " + gpDate + "</p><div id=\"window\"><div id=\"secchiSlider\"><div id=\"current\">Current</div><div id=\"min\">2015 Low</div><div id=\"max\">2015 High</div></div></div>"
  			});
  			var greatPondMarker = new google.maps.Marker({
  				position: {lat: 44.55834, lng: -69.87688},
  				map: map,
  				title: 'Great Pond (Goldie)'
  			});

			// add a listener to Great Pond marker
  			greatPondMarker.addListener('click', function() {
  				greatPondWindow.open(map, greatPondMarker);
				eastPondWindow.close();
				northPondWindow.close();
				snowPondWindow.close();
				longPondUpperWindow.close();
				longPondLowerWindow.close();
				salmonLakeWindow.close();
  			});

			// construct the visualization slider
			google.maps.event.addListener(greatPondWindow, 'domready', function() {
				// load historical data
				var gpStart = <?php echo($greatPond) ?>;
				var gpMin = <?php echo($greatPondMin) ?>;
				var gpMax = <?php echo($greatPondMax) ?>;	

				$( "#secchiSlider" ).slider({
            				orientation: "vertical",
            				max: 9,
            				min: 0,
            				value: 10-greatPondSecchi,
					// this updates the live display of the value
					slide: function(event, ui) {
      						var dep = 10-ui.value;
						$("#gpout").html("Secchi: " + dep + "<br>Deep Oxygen (%): " + gpDO + "<br>Surface Temperature (°F): " + gpT + "<br>Observation date: " + gpDate);
      						greatPondUpdate(dep, gpMin, gpMax);
    					}
            			})
				
				$(function() {
             				$("#current").css({
                				position: "absolute",
						textIndent: "80px",
						lineHeight: "3px",
						background: "#000000",
						width: "66px",
						height: "2px",
						top: (gpStart*10-10) + "px",
						left: "-33px"
					});
				});

				$(function() {
             				$("#min").css({
                				position: "absolute",
						textIndent: "-76px",
						lineHeight: "3px",
						background: "#FFFF00",
						width: "66px",
						height: "2px",
						top: (gpMin*10-10) + "px",
						left: "-33px"
					});
				});

				$(function() {
             				$("#max").css({
                				position: "absolute",
						textIndent: "-76px",
						lineHeight: "3px",
						background: "#FFFF00",
						width: "66px",
						height: "2px",
						top: (gpMax*10-10) + "px",
						left: "-33px"
					});
				});
				$(function() { $("#current").attr("title", gpStart) });
				$(function() { $("#min").attr("title", gpMin) });
				$(function() { $("#max").attr("title", gpMax) });
			});
  				
  			// get the LatLng coordinates for Upper Long Pond
  			var longPondUpperCoords = [];
  			for (var i = 0; i < longPondNorthArray.length; i += 2) {
  				var coord = new google.maps.LatLng(longPondNorthArray[i+1], longPondNorthArray[i]);
  				longPondUpperCoords.push(coord);
  			}

  			// construct Upper Long Pond
 			longPondUpperPolygon = new google.maps.Polygon({
    				paths: longPondUpperCoords,
    				strokeColor: blue,
    				strokeOpacity: 0.8,
    				strokeWeight: 5,
    				fillColor: blue,
    				fillOpacity: 0.25
  			});
  			longPondUpperPolygon.setMap(map);

  			// add a listener to Upper Long Pond to open info window
  			longPondUpperPolygon.addListener('click', function() {
  				longPondUpperWindow.open(map, longPondUpperMarker);
				eastPondWindow.close();
				northPondWindow.close();
				greatPondWindow.close();
				longPondLowerWindow.close();
				snowPondWindow.close();
				salmonLakeWindow.close();
  			});

  			// create Upper Long Pond marker and info window
  			var longPondUpperWindow = new google.maps.InfoWindow({
  				content: "<h1>Upper Long Pond</h1><p id=\"lpuout\">Secchi: " + longPondUpperSecchi + "<br>Deep Oxygen (%): " + lpuDO + "<br>Surface Temperature (°F): " + lpuT + "<br>Observation date: " + lpuDate + "</p><div id=\"window\"><div id=\"secchiSlider\"><div id=\"current\">Current</div><div id=\"min\">2015 Low</div><div id=\"max\">2015 High</div></div></div>"
  			});
  			var longPondUpperMarker = new google.maps.Marker({
  				position: {lat: 44.52766, lng: -69.89804},
  				map: map,
  				title: 'Upper Long Pond'
  			});

			// add a listener to Upper Long Pond marker
  			longPondUpperMarker.addListener('click', function() {
  				longPondUpperWindow.open(map, longPondUpperMarker);
				eastPondWindow.close();
				northPondWindow.close();
				greatPondWindow.close();
				longPondLowerWindow.close();
				snowPondWindow.close();
				salmonLakeWindow.close();
  			});

			// construct the visualization slider
			google.maps.event.addListener(longPondUpperWindow, 'domready', function() {
				// load and display historical data
				var lpuStart = <?php echo($upperLongPond) ?>;
				var lpuMin = <?php echo($upperLongPondMin) ?>;
				var lpuMax = <?php echo($upperLongPondMax) ?>;

      				$( "#secchiSlider" ).slider({
            				orientation: "vertical",
            				max: 9,
            				min: 0,
            				value: 10-longPondUpperSecchi,
					// this updates the live display of the value
					slide: function(event, ui) {
      						var dep = 10-ui.value;
						$("#lpuout").html("Secchi: " + dep + "<br>Deep Oxygen (%): " + lpuDO + "<br>Surface Temperature (°F): " + lpuT + "<br>Observation date: " + lpuDate);
      						longPondUpperUpdate(dep, lpuMin, lpuMax);
    					}
            			})
				
				$(function() {
             				$("#current").css({
                				position: "absolute",
						textIndent: "80px",
						lineHeight: "3px",
						background: "#000000",
						width: "66px",
						height: "2px",
						top: (lpuStart*10-10) + "px",
						left: "-33px"
					});
				});

				$(function() {
             				$("#min").css({
                				position: "absolute",
						textIndent: "-76px",
						lineHeight: "3px",
						background: "#FFFF00",
						width: "66px",
						height: "2px",
						top: (lpuMin*10-10) + "px",
						left: "-33px"
					});
				});

				$(function() {
             				$("#max").css({
                				position: "absolute",
						textIndent: "-76px",
						lineHeight: "3px",
						background: "#FFFF00",
						width: "66px",
						height: "2px",
						top: (lpuMax*10-10) + "px",
						left: "-33px"
					});
				});
				$(function() { $("#current").attr("title", lpuStart) });
				$(function() { $("#min").attr("title", lpuMin) });
				$(function() { $("#max").attr("title", lpuMax) });
			});

			// get the LatLng coordinates for Lower Long Pond
  			var longPondLowerCoords = [];
  			for (var i = 0; i < longPondSouthArray.length; i += 2) {
  				var coord = new google.maps.LatLng(longPondSouthArray[i+1], longPondSouthArray[i]);
  				longPondLowerCoords.push(coord);
  			}

  			// construct Lower Long Pond
 			longPondLowerPolygon = new google.maps.Polygon({
    				paths: longPondLowerCoords,
    				strokeColor: blue,
    				strokeOpacity: 0.8,
    				strokeWeight: 5,
    				fillColor: blue,
    				fillOpacity: 0.25
  			});
  			longPondLowerPolygon.setMap(map);

  			// add a listener to Lower Long Pond to open info window
  			longPondLowerPolygon.addListener('click', function() {
  				longPondLowerWindow.open(map, longPondLowerMarker);
				eastPondWindow.close();
				northPondWindow.close();
				greatPondWindow.close();
				longPondUpperWindow.close();
				snowPondWindow.close();
				salmonLakeWindow.close();
  			});

  			// create Lower Long Pond marker and info window
  			var longPondLowerWindow = new google.maps.InfoWindow({
  				content: "<h1>Lower Long Pond</h1><p id=\"lplout\">Secchi: " + longPondLowerSecchi + "<br>Deep Oxygen (%): " + lplDO + "<br>Surface Temperature (°F): " + lplT + "<br>Observation date: " + lplDate + "</p><div id=\"window\"><div id=\"secchiSlider\"><div id=\"current\">Current</div><div id=\"min\">2015 Low</div><div id=\"max\">2015 High</div></div></div>"
  			});
  			var longPondLowerMarker = new google.maps.Marker({
  				position: {lat: 44.49852, lng: -69.91321},
  				map: map,
  				title: 'Lower Long Pond'
  			});

			// add a listener to Lower Long Pond marker
  			longPondLowerMarker.addListener('click', function() {
  				longPondLowerWindow.open(map, longPondLowerMarker);
				eastPondWindow.close();
				northPondWindow.close();
				greatPondWindow.close();
				longPondUpperWindow.close();
				snowPondWindow.close();
				salmonLakeWindow.close();
  			});

			// construct the visualization slider
			google.maps.event.addListener(longPondLowerWindow, 'domready', function() {
      				// load and display historical data
				var lplStart = <?php echo($lowerLongPond) ?>;
				var lplMin = <?php echo($lowerLongPondMin) ?>;
				var lplMax = <?php echo($lowerLongPondMax) ?>;

				$( "#secchiSlider" ).slider({
            				orientation: "vertical",
            				max: 9,
            				min: 0,
            				value: 10-longPondLowerSecchi,
					// this updates the live display of the value
					slide: function(event, ui) {
      						var dep = 10-ui.value;
						$("#lplout").html("Secchi: " + dep + "<br>Deep Oxygen (%): " + lplDO + "<br>Surface Temperature (°F): " + lplT + "<br>Observation date: " + lplDate);
      						longPondLowerUpdate(dep, lplMin, lplMax);
    					}
            			})
				
				$(function() {
             				$("#current").css({
                				position: "absolute",
						textIndent: "80px",
						lineHeight: "3px",
						background: "#000000",
						width: "66px",
						height: "2px",
						top: (lplStart*10-10) + "px",
						left: "-33px"
					});
				});

				$(function() {
             				$("#min").css({
                				position: "absolute",
						textIndent: "-76px",
						lineHeight: "3px",
						background: "#FFFF00",
						width: "66px",
						height: "2px",
						top: (lplMin*10-10) + "px",
						left: "-33px"
					});
				});

				$(function() {
             				$("#max").css({
                				position: "absolute",
						textIndent: "-76px",
						lineHeight: "3px",
						background: "#FFFF00",
						width: "66px",
						height: "2px",
						top: (lplMax*10-10) + "px",
						left: "-33px"
					});
				});
				$(function() { $("#current").attr("title", lplStart) });
				$(function() { $("#min").attr("title", lplMin) });
				$(function() { $("#max").attr("title", lplMax) });
			});
  				
  			// get the LatLng coordinates for Messalonskee Lake
  			var snowPondCoords = [];
  			for (var i = 0; i < snowPondArray.length; i += 2) {
  				var coord = new google.maps.LatLng(snowPondArray[i+1], snowPondArray[i]);
  				snowPondCoords.push(coord);
  			}
  				
  			// construct Messalonskee Lake
 			snowPondPolygon = new google.maps.Polygon({
    				paths: snowPondCoords,
    				strokeColor: blue,
    				strokeOpacity: 0.8,
    				strokeWeight: 5,
    				fillColor: blue,
    				fillOpacity: 0.25
  			});
  			snowPondPolygon.setMap(map);

  			// add a listener to Messalonskee Lake to open info window
  			snowPondPolygon.addListener('click', function() {
  				snowPondWindow.open(map, snowPondMarker);
				eastPondWindow.close();
				northPondWindow.close();
				greatPondWindow.close();
				longPondUpperWindow.close();
				longPondLowerWindow.close();
				salmonLakeWindow.close();
  			});

  			// create the Messalonskee Lake marker and info window
  			var snowPondWindow = new google.maps.InfoWindow({
  				content: "<h1>Messalonskee Lake</h1><p id=\"spout\">Secchi: " + snowPondSecchi + "<br>Deep Oxygen (%): " + spDO + "<br>Surface Temperature (°F): " + spT + "<br>Observation date: " + spDate + "</p><div id=\"window\"><div id=\"secchiSlider\"><div id=\"current\">Current</div><div id=\"min\">2015 Low</div><div id=\"max\">2015 High</div></div></div>"
  			});
  			var snowPondMarker = new google.maps.Marker({
  				position: {lat: 44.49774, lng: -69.77421},
  				map: map,
  				title: 'Snow Pond'
  			});

			// add a listener to Messalonskee Lake marker
  			snowPondMarker.addListener('click', function() {
  				snowPondWindow.open(map, snowPondMarker);
				eastPondWindow.close();
				northPondWindow.close();
				greatPondWindow.close();
				longPondUpperWindow.close();
				longPondLowerWindow.close();
				salmonLakeWindow.close();
  			});

			// construct the visualization slider
			google.maps.event.addListener(snowPondWindow, 'domready', function() {
      				// load and display historical data
				var spStart = <?php echo($snowPond) ?>;
				var spMin = <?php echo($snowPondMin) ?>;
				var spMax = <?php echo($snowPondMax) ?>;

				$( "#secchiSlider" ).slider({
            				orientation: "vertical",
            				max: 9,
            				min: 0,
            				value: 10-snowPondSecchi,
					// this updates the live display of the value
					slide: function(event, ui) {
      						var dep = 10-ui.value;
						$("#spout").html("Secchi: " + dep + "<br>Deep Oxygen (%): " + spDO + "<br>Surface Temperature (°F): " + spT + "<br>Observation date: " + spDate);
      						snowPondUpdate(dep, spMin, spMax);
    					}
            			})
				
				$(function() {
             				$("#current").css({
                				position: "absolute",
						textIndent: "80px",
						lineHeight: "3px",
						background: "#000000",
						width: "66px",
						height: "2px",
						top: (spStart*10-10) + "px",
						left: "-33px"
					});
				});

				$(function() {
             				$("#min").css({
                				position: "absolute",
						textIndent: "-76px",
						lineHeight: "3px",
						background: "#FFFF00",
						width: "66px",
						height: "2px",
						top: (spMin*10-10) + "px",
						left: "-33px"
					});
				});

				$(function() {
             				$("#max").css({
                				position: "absolute",
						textIndent: "-76px",
						lineHeight: "3px",
						background: "#FFFF00",
						width: "66px",
						height: "2px",
						top: (spMax*10-10) + "px",
						left: "-33px"
					});
				});
				$(function() { $("#current").attr("title", spStart) });
				$(function() { $("#min").attr("title", spMin) });
				$(function() { $("#max").attr("title", spMax) });
			});
  				
  			// get the LatLng coordinates for Salmon Lake
  			var salmonLakeCoords = [];
  			for (var i = 0; i < salmonLakeArray.length; i += 2) {
  				var coord = new google.maps.LatLng(salmonLakeArray[i+1], salmonLakeArray[i]);
  				salmonLakeCoords.push(coord);
  			}
  				
  			// construct Salmon Lake
 			salmonLakePolygon = new google.maps.Polygon({
    				paths: salmonLakeCoords,
    				strokeColor: blue,
    				strokeOpacity: 0.8,
    				strokeWeight: 5,
    				fillColor: blue,
    				fillOpacity: 0.25
  			});
  			salmonLakePolygon.setMap(map);

  			// add a listener to Salmon Lake to open info window
  			salmonLakePolygon.addListener('click', function() {
  				salmonLakeWindow.open(map, salmonLakeMarker);
				eastPondWindow.close();
				northPondWindow.close();
				greatPondWindow.close();
				longPondUpperWindow.close();
				longPondLowerWindow.close();
				snowPondWindow.close();
  			});

			// create the Salmon Lake marker and info window
  			var salmonLakeWindow = new google.maps.InfoWindow({
  				content: "<h1>Salmon Lake</h1><p id=\"slout\">Secchi: " + salmonLakeSecchi + "<br>Deep Oxygen (%): " + slDO + "<br>Surface Temperature (°F): " + slT + "<br>Observation date: " + slDate + "</p><div id=\"window\"><div id=\"secchiSlider\"><div id=\"current\">Current</div><div id=\"min\">2015 Low</div><div id=\"max\">2015 High</div></div></div>"
  			});
  			var salmonLakeMarker = new google.maps.Marker({
  				position: {lat: 44.52153, lng: -69.78396},
  				map: map,
  				title: 'Salmon Lake'
  			});

			// add a listener to Salmon Lake marker
  			salmonLakeMarker.addListener('click', function() {
  				salmonLakeWindow.open(map, salmonLakeMarker);
				eastPondWindow.close();
				northPondWindow.close();
				greatPondWindow.close();
				longPondUpperWindow.close();
				longPondLowerWindow.close();
				snowPondWindow.close();
  			});

			// construct the visualization slider
			google.maps.event.addListener(salmonLakeWindow, 'domready', function() {
      				// load and display historical data
				var slStart = <?php echo($salmonLake) ?>;
				var slMin = <?php echo($salmonLakeMin) ?>;
				var slMax = <?php echo($salmonLakeMax) ?>;

				$( "#secchiSlider" ).slider({
            				orientation: "vertical",
            				max: 9,
            				min: 0,
            				value: 10-salmonLakeSecchi,
					// this updates the live display of the value
					slide: function(event, ui) {
      						var dep = 10-ui.value;
						$("#slout").html("Secchi: " + dep + "<br>Deep Oxygen (%): " + slDO + "<br>Surface Temperature (°F): " + slT + "<br>Observation date: " + slDate);
      						salmonLakeUpdate(dep, slMin, slMax);
    					}
            			})
				
				$(function() {
             				$("#current").css({
                				position: "absolute",
						textIndent: "80px",
						lineHeight: "3px",
						background: "#000000",
						width: "66px",
						height: "2px",
						top: (slStart*10-10) + "px",
						left: "-33px"
					});
				});

				$(function() {
             				$("#min").css({
                				position: "absolute",
						textIndent: "-76px",
						lineHeight: "3px",
						background: "#FFFF00",
						width: "66px",
						height: "2px",
						top: (slMin*10-10) + "px",
						left: "-33px"
					});
				});

				$(function() {
             				$("#max").css({
                				position: "absolute",
						textIndent: "-76px",
						lineHeight: "3px",
						background: "#FFFF00",
						width: "66px",
						height: "2px",
						top: (slMax*10-10) + "px",
						left: "-33px"
					});
				});
				$(function() { $("#current").attr("title", slStart) });
				$(function() { $("#min").attr("title", slMin) });
				$(function() { $("#max").attr("title", slMax) });
			});
		}

		// danger
		var red = '#B22222';
		// caution
		var orange = '#FF4500';
		// good
		var navy = '#000080';
			
		// change the slider background and color for East Pond
	 	function eastPondUpdate(val, cmin, cmax) {
			eastPondSecchi = val;
			if (val <= 2) {
				eastPondPolygon.setOptions({strokeColor: red, fillColor: green});
				$("#window").css({
                			position: "relative",
					background: "url(cross_section2.png) no-repeat, url(cross_section.png) no-repeat",
					paddingLeft: "200px",
					paddingRight: "200px",
					paddingTop: "10px",
					paddingBottom: "100px",
					marginBottom: "100px",
        				backgroundSize: "440px 220px",
      					backgroundPosition: "center"
				});
			}
			else if (val < cmin) {
				eastPondPolygon.setOptions({strokeColor: orange, fillColor: blue});
             			$("#window").css({
					position: "relative",
					background: "url(cross_section.png) no-repeat",
					paddingLeft: "200px",
					paddingRight: "200px",
					paddingTop: "10px",
					paddingBottom: "100px",
					marginBottom: "100px",
        				backgroundSize: "440px 220px",
      					backgroundPosition: "center"
				});
			}
			else if (val > cmax) {
				eastPondPolygon.setOptions({strokeColor: navy, fillColor: blue});
			}
			else {
				eastPondPolygon.setOptions({strokeColor: blue, fillColor: blue});
				$("#window").css({
					position: "relative",
					background: "url(cross_section.png) no-repeat",
					paddingLeft: "200px",
					paddingRight: "200px",
					paddingTop: "10px",
					paddingBottom: "100px",
					marginBottom: "100px",
        				backgroundSize: "440px 220px",
      					backgroundPosition: "center"
				});
			}
		}

		// change the slider background and color for North Pond
	 	function northPondUpdate(val, cmin, cmax) {
			northPondSecchi = val;
			if (val <= 2) {
				northPondPolygon.setOptions({strokeColor: red, fillColor: green});
             			$("#window").css({
                			position: "relative",
					background: "url(cross_section2.png) no-repeat, url(cross_section.png) no-repeat",
					paddingLeft: "200px",
					paddingRight: "200px",
					paddingTop: "10px",
					paddingBottom: "100px",
					marginBottom: "100px",
        				backgroundSize: "440px 220px",
      					backgroundPosition: "center"
				});
			}
			else if (val < cmin) {
				northPondPolygon.setOptions({strokeColor: orange, fillColor: blue});
             			$("#window").css({
					position: "relative",
					background: "url(cross_section.png) no-repeat",
					paddingLeft: "200px",
					paddingRight: "200px",
					paddingTop: "10px",
					paddingBottom: "100px",
					marginBottom: "100px",
        				backgroundSize: "440px 220px",
      					backgroundPosition: "center"
				});
			}
			else if (val > cmax) {
				northPondPolygon.setOptions({strokeColor: navy, fillColor: blue});
			}
			else {
				northPondPolygon.setOptions({strokeColor: blue, fillColor: blue});
			}
		}

		// change the slider background and color for Great Pond
	 	function greatPondUpdate(val, cmin, cmax) {
			greatPondSecchi = val;
			if (val <= 2) {
				greatPondPolygon.setOptions({strokeColor: red, fillColor: green});
				$("#window").css({
                			position: "relative",
					background: "url(cross_section2.png) no-repeat, url(cross_section.png) no-repeat",
					paddingLeft: "200px",
					paddingRight: "200px",
					paddingTop: "10px",
					paddingBottom: "100px",
					marginBottom: "100px",
        				backgroundSize: "440px 220px",
      					backgroundPosition: "center"
				});
			}
			else if (val < cmin){
				greatPondPolygon.setOptions({strokeColor: orange, fillColor: blue});
             			$("#window").css({
					position: "relative",
					background: "url(cross_section.png) no-repeat",
					paddingLeft: "200px",
					paddingRight: "200px",
					paddingTop: "10px",
					paddingBottom: "100px",
					marginBottom: "100px",
        				backgroundSize: "440px 220px",
      					backgroundPosition: "center"
				});
			}
			else if (val > cmax) {
				greatPondPolygon.setOptions({strokeColor: navy, fillColor: blue});
			}
			else {
				greatPondPolygon.setOptions({strokeColor: blue, fillColor: blue});
			}
		}

		// change the slider background and color for Upper Long Pond
	 	function longPondUpperUpdate(val, cmin, cmax) {
			longPondUpperSecchi = val;
			if (val <= 2) {
				longPondUpperPolygon.setOptions({strokeColor: red, fillColor: green});
				$("#window").css({
                			position: "relative",
					background: "url(cross_section2.png) no-repeat, url(cross_section.png) no-repeat",
					paddingLeft: "200px",
					paddingRight: "200px",
					paddingTop: "10px",
					paddingBottom: "100px",
					marginBottom: "100px",
        				backgroundSize: "440px 220px",
      					backgroundPosition: "center"
				});
			}
			else if (val < cmin) {
				longPondUpperPolygon.setOptions({strokeColor: orange, fillColor: blue});
				$("#window").css({
					position: "relative",
					background: "url(cross_section.png) no-repeat",
					paddingLeft: "200px",
					paddingRight: "200px",
					paddingTop: "10px",
					paddingBottom: "100px",
					marginBottom: "100px",
        				backgroundSize: "440px 220px",
      					backgroundPosition: "center"
				});
			}
			else if (val > cmax) {
				longPondUpperPolygon.setOptions({strokeColor: navy, fillColor: blue});
			}
			else {
				longPondUpperPolygon.setOptions({strokeColor: blue, fillColor: blue});
			}
		}

		// change the slider background and color for Lower Long Pond
	 	function longPondLowerUpdate(val, cmin, cmax) {
			longPondLowerSecchi = val;
			if (val <= 2) {
				longPondLowerPolygon.setOptions({strokeColor: red, fillColor: green});
				$("#window").css({
                			position: "relative",
					background: "url(cross_section2.png) no-repeat, url(cross_section.png) no-repeat",
					paddingLeft: "200px",
					paddingRight: "200px",
					paddingTop: "10px",
					paddingBottom: "100px",
					marginBottom: "100px",
        				backgroundSize: "440px 220px",
      					backgroundPosition: "center"
				});
			}
			else if (val < cmin) {
				longPondLowerPolygon.setOptions({strokeColor: orange, fillColor: blue});
				$("#window").css({
					position: "relative",
					background: "url(cross_section.png) no-repeat",
					paddingLeft: "200px",
					paddingRight: "200px",
					paddingTop: "10px",
					paddingBottom: "100px",
					marginBottom: "100px",
        				backgroundSize: "440px 220px",
      					backgroundPosition: "center"
				});
			}
			else if (val > cmax) {
				longPondLowerPolygon.setOptions({strokeColor: navy, fillColor: blue});
			}
			else {
				longPondLowerPolygon.setOptions({strokeColor: blue, fillColor: blue});
			}
		}

		// change the slider background and color for Snow Pond
	 	function snowPondUpdate(val, cmin, cmax) {
			snowPondSecchi = val;
			if (val <= 2) {
				snowPondPolygon.setOptions({strokeColor: red, fillColor: green});
				$("#window").css({
                			position: "relative",
					background: "url(cross_section2.png) no-repeat, url(cross_section.png) no-repeat",
					paddingLeft: "200px",
					paddingRight: "200px",
					paddingTop: "10px",
					paddingBottom: "100px",
					marginBottom: "100px",
        				backgroundSize: "440px 220px",
      					backgroundPosition: "center"
				});
			}
			else if (val < cmin) {
				snowPondPolygon.setOptions({strokeColor: orange, fillColor: blue});
				$("#window").css({
					position: "relative",
					background: "url(cross_section.png) no-repeat",
					paddingLeft: "200px",
					paddingRight: "200px",
					paddingTop: "10px",
					paddingBottom: "100px",
					marginBottom: "100px",
        				backgroundSize: "440px 220px",
      					backgroundPosition: "center"
				});
			}
			else if (val > cmax) {
				snowPondPolygon.setOptions({strokeColor: navy, fillColor: blue});
			}
			else {
				snowPondPolygon.setOptions({strokeColor: blue, fillColor: blue});
			}
		}

		// change the slider background and color for Salmon Lake
	 	function salmonLakeUpdate(val, cmin, cmax) {
			salmonLakeSecchi = val;
			if (val <= 2) {
				salmonLakePolygon.setOptions({strokeColor: red, fillColor: green});
				$("#window").css({
                			position: "relative",
					background: "url(cross_section2.png) no-repeat, url(cross_section.png) no-repeat",
					paddingLeft: "200px",
					paddingRight: "200px",
					paddingTop: "10px",
					paddingBottom: "100px",
					marginBottom: "100px",
        				backgroundSize: "440px 220px",
      					backgroundPosition: "center"
				});
			}
			else if (val < cmin) {
				salmonLakePolygon.setOptions({strokeColor: orange, fillColor: blue});
				$("#window").css({
					position: "relative",
					background: "url(cross_section.png) no-repeat",
					paddingLeft: "200px",
					paddingRight: "200px",
					paddingTop: "10px",
					paddingBottom: "100px",
					marginBottom: "100px",
        				backgroundSize: "440px 220px",
      					backgroundPosition: "center"
				});
			}
			else if (val > cmax) {
				salmonLakePolygon.setOptions({strokeColor: navy, fillColor: blue});
			}
			else {
				salmonLakePolygon.setOptions({strokeColor: blue, fillColor: blue});
			}
		}
    		</script>
		<script src="lakeData.js"></script>
		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCqns-GJ-8KyPsbZst4I0rBIsyhiF5Vy3A&callback=initMap" async defer></script>
	</body>
</html>
