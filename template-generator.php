<?php
include_once "citadel-converter-lib.php";
$action = get_input('action', '');
$load_template = get_input('load_template', false);



// Paramètres configurables
// Liste des paramètres
//$params_options = array('skip-first-row', 'delimiter', 'enclosure', 'escape');

if (!empty($load_template)) {
	//echo "Loading existing template...<br />";
	// Load_existing template;
	$template = unserialize(base64_decode(trim($load_template)));
	
	//echo '<pre>' . print_r($template, true) . '</pre>';
	$skip_first_row = $template['skip-first-row'];
	$delimiter = $template['delimiter'];
	$enclosure = $template['enclosure'];
	$escape = $template['escape'];
	$dataset_id = $template['metadata']['dataset-id'];
	$dataset_lang = $template['metadata']['dataset-lang'];
	$dataset_author_id = $template['metadata']['dataset-author-id'];
	$dataset_author_name = $template['metadata']['dataset-author-name'];
	$dataset_license_url = $template['metadata']['dataset-license-url'];
	$dataset_license_term = $template['metadata']['dataset-license-term'];
	$dataset_source_url = $template['metadata']['dataset-source-url'];
	$dataset_source_term = $template['metadata']['dataset-source-term'];
	$dataset_update_frequency = $template['metadata']['dataset-update-frequency'];
	// Mapping
	$dataset_poi_category_default = $template['mapping']['dataset-poi-category-default'];
	$dataset_poi_id = $template['mapping']['dataset-poi-id'];
	$dataset_poi_title = $template['mapping']['dataset-poi-title'];
	$dataset_poi_description = $template['mapping']['dataset-poi-description'];
	$dataset_poi_category = $template['mapping']['dataset-poi-category'];
	$dataset_poi_lat = $template['mapping']['dataset-poi-lat'];
	$dataset_poi_long = $template['mapping']['dataset-poi-long'];
	$dataset_coordinate_system = $template['mapping']['dataset-coordinate-system'];
	$dataset_poi_address = $template['mapping']['dataset-poi-address'];
	$dataset_poi_postal = $template['mapping']['dataset-poi-postal'];
	$dataset_poi_city = $template['mapping']['dataset-poi-city'];
	//echo "License : $dataset_license_url";
	
} else {
	$skip_first_row = get_input('skip-first-row', 'yes');
	if ($skip_first_row == 'yes') $skip_first_row = true; else $skip_first_row = false;
	$delimiter = get_input('delimiter', ';');
	$enclosure = get_input('enclosure', '"');
	$escape = get_input('escape', '\\');
	$dataset_id = get_input('dataset_id', '');
	$dataset_lang = get_input('dataset_lang', 'fr_FR');
	$dataset_author_id = get_input('dataset_author_id', '');
	$dataset_author_name = get_input('dataset_author_name', '');
	$dataset_license_url = get_input('dataset_license_url', '');
	$dataset_license_term = get_input('dataset_license_term', 'CC-BY');
	$dataset_source_url = get_input('dataset_source_url', '');
	$dataset_source_term = get_input('dataset_source_term', 'source');
	$dataset_update_frequency = get_input('dataset_update_frequency', 'semester');
	// Mapping
	$dataset_poi_category_default = get_input('dataset_poi_category_default', '');
	$dataset_poi_id = get_input('dataset_poi_id', '');
	$dataset_poi_title = get_input('dataset_poi_title', 'Titre');
	$dataset_poi_description = get_input('dataset_poi_description', 'Description');
	$dataset_poi_category = get_input('dataset_poi_category', 'Catégorie1');
	$dataset_poi_lat = get_input('dataset_poi_lat', 'Latitude');
	$dataset_poi_long = get_input('dataset_poi_long', 'Longitude');
	$dataset_coordinate_system = get_input('dataset_coordinate_system', 'WGS84');
	$dataset_poi_address = get_input('dataset_poi_address', 'Adresse');
	$dataset_poi_postal = get_input('dataset_poi_postal', 'Codepostal');
	$dataset_poi_city = get_input('dataset_poi_city', 'Ville');
}



// Generate a template
if (in_array($action, array('generate', 'export'))) {
	$template = array(
		// Set to true if the first line of data contains the lables (titles of columns)
		// Set to false if the data starts immediately
		// See also mapping, as this setting has an impact on how the mapping is made
		'skip-first-row' => $skip_first_row,
		// Common delimiters are : ",", ";", "\t", "|" and " " (comma, period, tabulation, pipe and space)
		'delimiter' => $delimiter,
		// Enclosure is the character used to enclose values
		// Common enclosure are : "'", "\"" (single or double quote)
		'enclosure' => $enclosure,
		// Escape is the character used to escape special characters 
		// (such as the ones used for enclosure or as delimiter)
		// Common escape character should not be changed : "\\"
		'escape' => $escape, 
		// Metadata are the values that will describe the entire dataset
		'metadata' => array(
			'dataset-id' => $dataset_id, // A unique id for the dataset
			'dataset-lang' => $dataset_lang, // ISO code of the dataset language
			'dataset-author-id' => $dataset_author_id, // A unique ID for the author (optional)
			'dataset-author-name' => $dataset_author_name, // The author name (optional)
			'dataset-license-url' => $dataset_license_url, // Dataset license URL (optional)
			'dataset-license-term' => $dataset_license_term, // Dataset license term (optional)
			'dataset-source-url' => $dataset_source_url, // Dataset source URL (where is located the input file - optional)
			'dataset-source-term' => $dataset_source_term, // Dataset source term (should not be changed)
			'dataset-update-frequency' => $dataset_update_frequency, // Dataset update frequency (change only if you know why !)
		),
		// The following describes each row of the dataset = POI = Point Of Interest = marker on the map
		// Mapping is the semantic process that will tie a specific column to a Citadel-JSON field
		// Mapping keys should not be changed (except if the Citadel-JSON format evolves)
		// Mapping values can take 2 forms, depending on 'skip-first-row' setting :
		// If set to true : values should be the "title colums", ie the labels of the CSV table
		// If set to false : values should be the index numbers of the columns, ie 1 less than the column number.
		//     Example : if you have 10 columns (from 1 to 10), the corresponding index will range from 0 to 9
		//     If you data start with a title, you should indicate '0' as the value for the 'dataset-poi-title' key
		'mapping' => array(
			'dataset-poi-category-default' => $dataset_poi_category_default, // Default category for the POI = ataset license URL (optional)
			'dataset-poi-id' => $dataset_poi_id, // A Unique ID for the POI (optional)
			'dataset-poi-title' => $dataset_poi_title,
			'dataset-poi-description' => $dataset_poi_description,
			'dataset-poi-category' => $dataset_poi_category,
			'dataset-poi-lat' => $dataset_poi_lat,
			'dataset-poi-long' => $dataset_poi_long,
			'dataset-coordinate-system' => $dataset_coordinate_system,
			'dataset-poi-address' => $dataset_poi_address,
			'dataset-poi-postal' => $dataset_poi_postal,
			'dataset-poi-city' => $dataset_poi_city,
		),
	);

	$serialized_template = base64_encode(serialize($template));
	// Output the template in a form which can be fetched from a remote server
	if ($action == 'export') {
		echo $serialized_template;
		exit;
	}
}

// Display the generation form
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
		<link href="main.css" rel="stylesheet">
	</head>

	<body>
		<div class="container">
			<span style="float:right; font-family:monospace; font-size:1.2em;">
				<?php echo add_lang_switch($lang); ?>
			</span>
			<h1><?php echo echo_lang('converter:tplgen:title'); ?></h1>
			<?php echo echo_lang('converter:tplgen:description'); ?>
			<br />
			
			<?php if (isset($serialized_template)) { ?>
				<h2><?php echo echo_lang('converter:tplgen:output'); ?></h2>
				<blockquote>
					<textarea readonly="readonly" style="width:90%; height:120px;"><?php echo $serialized_template; ?></textarea>
				</blockquote>
				<br />
			<?php } ?>
			
			<h2><?php echo echo_lang('converter:tplgen:form'); ?></h2>
			<form method="POST">
				<fieldset>
					<legend><?php echo echo_lang('converter:tplgen:legend:technical'); ?></legend>
					<p><label><?php echo echo_lang('converter:tplgen:firstline'); ?> <select name="skip_first_row">
						<?php if ($skip_first_row) echo '<option value="yes" selected="selected">' . echo_lang('converter:tplgen:firstline:yes') . '</option><option value="no">' . echo_lang('converter:tplgen:firstline:no') . '</option>';
						else echo '<option value="yes">' . echo_lang('converter:tplgen:firstline:yes') . '</option><option value="no" selected="selected">' . echo_lang('converter:tplgen:firstline:no') . '</option>';
						?>
					</select></label></p>
					<p><label><?php echo echo_lang('converter:tplgen:delimiter'); ?> <input type="text" name="delimiter" value="<?php echo htmlentities($delimiter, ENT_QUOTES); ?>" /></label></p>
					<p><label><?php echo echo_lang('converter:tplgen:enclosure'); ?> <input type="text" name="enclosure" value="<?php echo htmlentities($enclosure, ENT_QUOTES); ?>" /></label></p>
					<p><label><?php echo echo_lang('converter:tplgen:escape'); ?><input type="text" name="escape" value="<?php echo htmlentities($escape, ENT_QUOTES); ?>" /></label></p>
				</fieldset>
				<br />
				<br />
				<fieldset>
					<legend><?php echo echo_lang('converter:tplgen:legend:metadata'); ?></legend>
					<div style="width:45%; float:left;">
						<p><label><?php echo echo_lang('converter:tplgen:dataset_id'); ?> <input type="text" name="dataset_id" value="<?php echo $dataset_id; ?>" /></label></p>
						<p><label><?php echo echo_lang('converter:tplgen:dataset_lang'); ?> <input type="text" name="dataset_lang" value="<?php echo $dataset_lang; ?>" /></label></p>
						<p><label><?php echo echo_lang('converter:tplgen:authorid'); ?> <input type="text" name="dataset_author_id" value="<?php echo $dataset_author_id; ?>" /></label></p>
						<p><label><?php echo echo_lang('converter:tplgen:authorname'); ?> <input type="text" name="dataset_author_name" value="<?php echo $dataset_author_name; ?>" /></label></p>
						<p><label><?php echo echo_lang('converter:tplgen:updatefreq'); ?> <input type="text" name="dataset_update_frequency" value="<?php echo $dataset_update_frequency; ?>" /></label></p>
					</div>
					<div style="width:45%; float:right;">
						<p><label><?php echo echo_lang('converter:tplgen:licenceurl'); ?> <input type="text" name="dataset_license_url" value="<?php echo $dataset_license_url; ?>" /></label></p>
						<p><label><?php echo echo_lang('converter:tplgen:licenceterm'); ?> <input type="text" name="dataset_license_term" value="<?php echo $dataset_license_term; ?>" /></label></p>
						<p><label><?php echo echo_lang('converter:tplgen:sourceurl'); ?> <input type="text" name="dataset_source_url" value="<?php echo $dataset_source_url; ?>" /></label></p>
						<p><label><?php echo echo_lang('converter:tplgen:sourceterm'); ?> <input type="text" name="dataset_source_term" value="<?php echo $dataset_source_term; ?>" /></label></p>
					</div>
				</fieldset>
				<br />
				<br />
				<fieldset>
					<legend><?php echo echo_lang('converter:tplgen:legend:semantic'); ?></legend>
					<div style="width:45%; float:left;">
						<p><strong><?php echo echo_lang('converter:tplgen:legend:display'); ?></strong></p>
						<p><label><?php echo echo_lang('converter:tplgen:poi_default_cat'); ?> <input type="text" name="dataset_poi_category_default" value="<?php echo $dataset_poi_category_default; ?>" /></label></p>
						<p><label><?php echo echo_lang('converter:tplgen:poi_id'); ?> <input type="text" name="dataset_poi_id" value="<?php echo $dataset_poi_id; ?>" /></label></p>
						<p><label><?php echo echo_lang('converter:tplgen:poi_title'); ?> <input type="text" name="dataset_poi_title" value="<?php echo $dataset_poi_title; ?>" /></label></p>
						<p><label><?php echo echo_lang('converter:tplgen:poi_descr'); ?> <input type="text" name="dataset_poi_description" value="<?php echo $dataset_poi_description; ?>" /></label></p>
						<p><label><?php echo echo_lang('converter:tplgen:poi_cat'); ?> <input type="text" name="dataset_poi_category" value="<?php echo $dataset_poi_category; ?>" /></label></p>
					</div>
					<div style="width:45%; float:right;">
						<p><strong><?php echo echo_lang('converter:tplgen:legend:geo'); ?></strong></p>
						<p><label><?php echo echo_lang('converter:tplgen:lat'); ?> <input type="text" name="dataset_poi_lat" value="<?php echo $dataset_poi_lat; ?>" /></label></p>
						<p><label><?php echo echo_lang('converter:tplgen:long'); ?> <input type="text" name="dataset_poi_long" value="<?php echo $dataset_poi_long; ?>" /></label></p>
						<p><label><?php echo echo_lang('converter:tplgen:geosystem'); ?> <input type="text" name="dataset_coordinate_system" value="<?php echo $dataset_coordinate_system; ?>" /></label></p>
						<p><label><?php echo echo_lang('converter:tplgen:address'); ?> <input type="text" name="dataset_poi_address" value="<?php echo $dataset_poi_address; ?>" /></label></p>
						<p><label><?php echo echo_lang('converter:tplgen:postalcode'); ?> <input type="text" name="dataset_poi_postal" value="<?php echo $dataset_poi_postal; ?>" /></label></p>
						<p><label><?php echo echo_lang('converter:tplgen:city'); ?> <input type="text" name="dataset_poi_city" value="<?php echo $dataset_poi_city; ?>" /></label></p>
					</div>
				</fieldset>
				<p><label><?php echo echo_lang('converter:tplgen:action'); ?> 
					<select name="action">
						<option value="generate"><?php echo echo_lang('converter:tplgen:action:generate'); ?></option>
						<option value="export"><?php echo echo_lang('converter:tplgen:action:export'); ?></option>
					</select>
				</label></p>
				<p><input type="submit" value="<?php echo echo_lang('converter:tplgen:submit'); ?>" /></p>
			</form>
			
			<h2><?php echo echo_lang('converter:tplgen:legend:import'); ?></h2>
			<form method="POST">
				<p><label><?php echo echo_lang('converter:tplgen:import'); ?> <textarea name="load_template" style="width:100%; height:20ex;"></textarea></label></p>
				<p><input type="submit" value="<?php echo echo_lang('converter:tplgen:submit'); ?>" /></p>
			</form>
			
		</div>
	</body>
</html>

