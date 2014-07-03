<?php
include_once "citadel-converter-lib.php";
$action = get_input('action', '');



// Paramètres configurables
// Liste des paramètres
//$params_options = array('skip-first-row', 'delimiter', 'enclosure', 'escape');


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



// Generate a template
if ($action == "generate") {
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

	// Output the template in a form which can be fetched from a remote server
	echo serialize($template);
	exit;
}

// If no action was asked,display the generation form
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
			<p>This form is meant to facilitate the generation of mapping templates that can be used directly by the converter.</p>
			<p>A mapping template is basically a serialized PHP array, so it a text string that can be hosted on any web server, or sent in a form.</p>
			<p>This tool makes it generation easier</p>
			
<?php
$skip_first_row = get_input('skip_first_row', 'yes');
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
?>

			<form method="POST">
				<fieldset>
					<legend>Input technical settings</legend>
					<p><label>Fields title on first line: <input type="text" name="skip_first_row" value="<?php echo $skip_first_row; ?>" /></label></p>
					<p><label>Delimiter: <input type="text" name="delimiter" value="<?php echo $delimiter; ?>" /></label></p>
					<p><label>Enclosure: <input type="text" name="enclosure" value="<?php echo $enclosure; ?>" /></label></p>
					<p><label>Escape: <input type="text" name="escape" value="<?php echo $escape; ?>" /></label></p>
				</fieldset>
				<br />
				<br />
				<fieldset>
					<legend>Dataset description</legend>
					<div style="width:45%; float:left;">
						<p><label>Dataset ID: <input type="text" name="dataset_id" value="<?php echo $dataset_id; ?>" /></label></p>
						<p><label>Dataset language: <input type="text" name="dataset_lang" value="<?php echo $dataset_lang; ?>" /></label></p>
						<p><label>Author ID: <input type="text" name="dataset_author_id" value="<?php echo $dataset_author_id; ?>" /></label></p>
						<p><label>Author name: <input type="text" name="dataset_author_name" value="<?php echo $dataset_author_name; ?>" /></label></p>
						<p><label>Update frequency: <input type="text" name="dataset_update_frequency" value="<?php echo $dataset_update_frequency; ?>" /></label></p>
					</div>
					<div style="width:45%; float:right;">
						<p><label>Licence URL: <input type="text" name="dataset_license_url" value="<?php echo $dataset_license_url; ?>" /></label></p>
						<p><label>Licence term: <input type="text" name="dataset_license_term" value="<?php echo $dataset_license_term; ?>" /></label></p>
						<p><label>Source URL: <input type="text" name="dataset_source_url" value="<?php echo $dataset_source_url; ?>" /></label></p>
						<p><label>Source term: <input type="text" name="dataset_source_term" value="<?php echo $dataset_source_term; ?>" /></label></p>
					</div>
				</fieldset>
				<br />
				<br />
				<fieldset>
					<legend>Semantic fields mapping</legend>
					<div style="width:45%; float:left;">
						<p><strong>Display fields</strong></p>
						<p><label>POI default category: <input type="text" name="dataset_poi_category_default" value="<?php echo $dataset_poi_category_default; ?>" /></label></p>
						<p><label>POI ID field: <input type="text" name="dataset_poi_id" value="<?php echo $dataset_poi_id; ?>" /></label></p>
						<p><label>POI title: <input type="text" name="dataset_poi_title" value="<?php echo $dataset_poi_title; ?>" /></label></p>
						<p><label>POI description field: <input type="text" name="dataset_poi_description" value="<?php echo $dataset_poi_description; ?>" /></label></p>
						<p><label>POI category field: <input type="text" name="dataset_poi_category" value="<?php echo $dataset_poi_category; ?>" /></label></p>
					</div>
					<div style="width:45%; float:right;">
						<p><strong>Geographical fields</strong></p>
						<p><label>POI latitude field: <input type="text" name="dataset_poi_lat" value="<?php echo $dataset_poi_lat; ?>" /></label></p>
						<p><label>POI longitude field: <input type="text" name="dataset_poi_long" value="<?php echo $dataset_poi_long; ?>" /></label></p>
						<p><label>POI geographical coordinates system field: <input type="text" name="dataset_coordinate_system" value="<?php echo $dataset_coordinate_system; ?>" /></label></p>
						<p><label>POI address field: <input type="text" name="dataset_poi_address" value="<?php echo $dataset_poi_address; ?>" /></label></p>
						<p><label>POI postal code field: <input type="text" name="dataset_poi_postal" value="<?php echo $dataset_poi_postal; ?>" /></label></p>
						<p><label>POI city field: <input type="text" name="dataset_poi_city" value="<?php echo $dataset_poi_city; ?>" /></label></p>
					</div>
				</fieldset>
				
			</form>
		</div>
	</body>
</html>

