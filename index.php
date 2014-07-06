<?php
include_once "citadel-converter-lib.php";
$base_url = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']) . '/';

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="">
		<meta name="author" content="">
		
		<link rel="stylesheet" href="main.css" />
		
		<title>Citadel - Converter <?php echo $version; ?></title>
		
		<!-- Custom styles -->
		<link href="css/style.css" rel="stylesheet">
	</head>

	<body>
		<div class="container">
			<h1><?php echo echo_lang('converter:title'); ?></h1>
			<?php echo echo_lang('converter:description'); ?>
			
			<?php
			$source = get_input('source');
			$filename = get_input('filename');
			$template = get_input('template');
			if (!empty($source)) {
				$download_url = $base_url . 'convert.php?source=' . urlencode($source) . '&filename=' . urlencode($filename) . '&template=' . urlencode($template);
				?>
				<blockquote>
					<p><a href="<?php echo $base_url . $download_url; ?>"><?php echo $CONFIG['language']['converter:download:file']; ?></a></p>
					<p><?php echo echo_lang('converter:download:link'); ?><br /><q><?php echo $download_url; ?></q></p>
				</blockquote><br />
				<?php
			}
			
			// Set default if no value specified
			if (empty($source)) { $source = 'samples/dataset.csv'; }
			if (empty($filename)) { $filename = ''; }
			if (empty($template)) { $template = 'samples/template.txt'; }
			?>
			
			<form method="POST">
				<fieldset>
					<legend><?php echo echo_lang('converter:form'); ?></legend>
				<p><label><?php echo echo_lang('converter:form:source'); ?><input type="text" name="source" value="<?php echo $source; ?>" /></label></p>
				<p><label><?php echo echo_lang('converter:form:template'); ?><input type="text" name="template" value="<?php echo $template; ?>" /></label></p>
				<p><label><?php echo echo_lang('converter:form:filename'); ?><input type="text" name="filename" value="<?php echo $filename; ?>" /></label></p>
				<p><input type="submit" value="<?php echo echo_lang('converter:form:givelink'); ?>" /></p>
				</fieldset>
			</form>
		</div>
	</body>
</html>

