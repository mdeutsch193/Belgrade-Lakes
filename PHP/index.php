<!--By Martin Deutsch '19-->
<!DOCTYPE html>
<html>
	<head>
		<title>Belgrade Lakes Water Clarity Project</title>
		<meta name="viewport" content="initial-scale=1.0">
    		<meta charset="utf-8">
		<link rel="icon" type="image/png" href="seal.png">
		<style>
			#page {
				height: 100%;
				width: 100%;
			}
		</style>
	</head>
	<body>
		<noscript>Please enable Javascript in your browser</noscript>
		<img id="spinner" src="page-loader.gif" style="position: absolute; width: 20%; height: 30%; left: 40%; top: 20%;">
		<div id="counter" style="position: absolute; font-size: 2em; left: 49%; top: 55%;">0%</div>
		<div id="page">
		</div>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script>
			$(document).ready(function() {
				var i = 0;
				var intervalID = setInterval(function(){ $("#counter").text(i + "%"); if(i<100) {i++;} }, 265);
				$("#page").load("home.php", function() {
					clearInterval(intervalID);
					$("#spinner").hide();
					$("#counter").hide();
				})
			});
		</script>
	</body>
</html>


