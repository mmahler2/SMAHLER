<html>
	<head>
		<title>SMAHLER</title>
		<link rel="stylesheet" type="text/css" href="/smahler/style.css" />
		<link href='http://fonts.googleapis.com/css?family=Raleway:400,300,700' rel='stylesheet' type='text/css'>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<script>
			function submit() {
				$("#result").removeClass('visible');
				console.log('submitting...');
				var url = $('#url').val();
				if( !(url.indexOf('http') >= 0) && url != ''){
					url = "http://" + url;
				}
				var custom = $("#custom_hash").val();
				$.ajax({
					type: 'POST',
					url: 'new.php',
					data: {url:url, custom:custom},
					success: function(data) {
					    $("#result").html(data);
					    console.log('success!');
					    $("#result").addClass('visible');
					}
				});		
			}
		</script>
	</head>
	<body>
		<div id="container">
			<h1 style="margin-top:60px;">SMAHLER</h1>
			<input type="text" id="url" placeholder="enter annoying long URL . . ." onkeydown="if (event.keyCode == 13) submit();"/><span id="hdivid">/</span><input type="text" id="custom_hash" placeholder="custom" onkeydown="if (event.keyCode == 13) submit();"/>
			<div id="submit" onclick="submit()"><h1>GO</h1></div>
			<div id="result"></div>
			<h1 class="show-customs" style="margin-bottom:0;border-bottom:1px solid #ccc;margin-top:40px;">Custom <span style="font-weight:800;">URLs</span><span id="show-customs">+</span></h1>
			<div id="custom-lib">
				
				<?php
					$cn = new mysqli("localhost", "m2", "Maximus1", "m2");
					if ($cn->connect_error)
						die("Connection failed: " . $cn->connect_error);
					$q = "select * from smahler where custom = 1 order by hits DESC" ;
					$res = $cn->query($q);
					while($row = $res->fetch_assoc()) {
						echo("<div>");
						echo("<a href='/smahler/" . $row['hash'] . "'>smahler/" . $row['hash'] . "</a><span class='hits'>hits: ".$row['hits']."</span><br/>");
						echo("<p>" .$row['redirect']. "</p>");
						echo("</div>");
					}
				?>
			</div>
		</div>
		<script>
			$("#show-customs").click(function(){
				$(this).toggleClass('active');
				$("#custom-lib").toggleClass('active');
			});
		</script>
	</body>
</html>