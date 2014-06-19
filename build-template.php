<?php
include_once "citadel-converter-lib.php";
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="">
		<meta name="author" content="">

		<title>Citadel - Converter <?php echo $version; ?></title>
		
		<!-- Custom styles -->
		<link href="css/style.css" rel="stylesheet">
	</head>

	<body>
		<div class="container">
			<p>This PHP lib is meant to be used as a service that can convert CSV files to Citadel-JSON on the fly</p>
			<p>Following API should be exposed :</p>
			<ul>
				<li>build-template : can be used to generate a new conversion template</li>
				<li>convert : can be used to convert files</li>
			</ul>
			<p>Use following parameters after the URL, or use the form below :</p>
			<ul>
				<li>file=URL_FILE_RESSOURCE</li>
				<li>templates=URL_TEMPLATE_RESSOURCE</li>
			</ul>
			
			<form method="POST">
				
			</form>
		</div>
	</body>
</html>
