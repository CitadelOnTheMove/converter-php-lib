<?php
include_once "citadel-converter-lib.php";
$base_url = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']) . '/';

echo $base_url;
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
			<p>This PHP lib is meant to be used as a service that can convert CSV files to Citadel-JSON files on the fly</p>
			<p>Following API should be exposed :</p>
			<ul>
				<li>convert : can be used to convert CSV files to Citadel-JSON</li>
				<li>build-template : can be used to generate a new conversion template</li>
			</ul>
			
			<?php
			$source = strip_tags($_REQUEST['source']);
			$filename = urlencode(strip_tags($_REQUEST['filename']));
			$template = urlencode(strip_tags($_REQUEST['template']));
			if (!empty($source)) {
				$download_url = 'convert.php?source=' . urlencode($source) . '&filename=' . urlencode($filename) . '&template=' . urlencode($template);
				?>
				<p><a href="<?php echo $base_url . $download_url; ?>">Download generated Citadel-JSON file</a></p>
				<p>OR use this direct URL to provide updated file to your app : <?php echo $download_url; ?></p>
				<?php
			}
			
			// Set default if no value specified
			if (empty($source)) { $source = 'samples/dataset.csv'; }
			if (empty($filename)) { $filename = ''; }
			if (empty($template)) { $template = ''; }
			?>
			
			<form method="POST">
				<p><label>Source file (local file or URL) : <input type="text" name="source" value="<?php echo $source; ?>" /></label></p>
				<p><label>Exported file name (optional, no file extension) : <input type="text" name="filename" value="<?php echo $filename; ?>" /></label></p>
				<p><label>Template file (not functionnal yet) : <input type="text" name="template" value="<?php echo $template; ?>" /></label></p>
				<p><input type="submit" value="Give me the link to the converted file !" /></p>
			</form>
		</div>
	</body>
</html>
