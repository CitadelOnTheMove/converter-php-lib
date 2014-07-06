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
			<span style="float:right; font-family:monospace; font-size:1.2em;">
				<?php echo add_lang_switch($lang); ?>
			</span>
			<h1><?php echo echo_lang('converter:title'); ?></h1>
			<?php echo echo_lang('converter:description'); ?>
			
			<?php
			$source = get_input('source');
			$filename = get_input('filename');
			$remote_template = get_input('remote_template');
			$serialized_template = get_input('serialized_template');
			if (!empty($source)) {
				$download_url = $base_url . 'convert.php?source=' . urlencode($source) . '&filename=' . urlencode($filename) . '&remote_template=' . urlencode($remote_template) . '&serialized_template=' . urlencode($serialized_template);
				?>
				<blockquote>
					<p><a href="<?php echo $download_url; ?>"><?php echo $CONFIG['language']['converter:download:file']; ?></a></p>
					<p><?php echo echo_lang('converter:download:link'); ?><br /><pre><?php echo $download_url; ?></pre></p>
				</blockquote><br />
				<?php
			}
			
			// Set default if no value specified
			if (empty($source)) { $source = 'samples/dataset.csv'; }
			if (empty($filename)) { $filename = ''; }
			if (empty($remote_template) && empty($serialized_template)) { $remote_template = 'samples/template.txt'; }
			?>
			
			<form method="POST">
				<fieldset>
					<legend><?php echo echo_lang('converter:form'); ?></legend>
					<div style="width:45%; float:left;">
						<p><label><?php echo echo_lang('converter:form:source'); ?><input type="text" name="source" value="<?php echo $source; ?>" /></label></p>
						<p><label><?php echo echo_lang('converter:form:filename'); ?><input type="text" name="filename" value="<?php echo $filename; ?>" /></label></p>
					</div>
					<div style="width:45%; float:right;">
						<p><label><?php echo echo_lang('converter:form:template'); ?><input type="text" name="remote_template" value="<?php echo $remote_template; ?>" /></label></p>
						<p><label><?php echo echo_lang('converter:form:serialized_template'); ?><textarea type="text" name="serialized_template" style="width:90%; height:5ex;"><?php echo $serialized_template; ?></textarea></label></p>
					</div>
					<p style="clear:both;"><input type="submit" value="<?php echo echo_lang('converter:form:givelink'); ?>" /></p>
				</fieldset>
			</form>
		</div>
	</body>
</html>

