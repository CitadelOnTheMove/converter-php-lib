<?php
include_once "citadel-converter-lib.php";
$action = @strip_tags($_REQUEST['action']);


// Generate a template
if ($action == "generate") {
	$template = array(
		// Set to true if the first line of data contains the lables (titles of columns)
		// Set to false if the data starts immediately
		// See also mapping, as this setting has an impact on how the mapping is made
		'skip-first-row' => true,
		// Common delimiters are : ",", ";", "\t", "|" and " " (comma, period, tabulation, pipe and space)
		'delimiter' => ';',
		// Enclosure is the character used to enclose values
		// Common enclosure are : "'", "\"" (single or double quote)
		'enclosure' => '"', 
		// Escape is the character used to escape special characters 
		// (such as the ones used for enclosure or as delimiter)
		// Common escape character should not be changed : "\\"
		'escape' => '\\', 
		// Metadata are the values that will describe the entire dataset
		'metadata' => array(
			'dataset-id' => '', // A unique id for the dataset
			'dataset-lang' => 'fr_FR', // ISO code of the dataset language
			'dataset-author-id' => '', // A unique ID for the author (optional)
			'dataset-author-name' => '', // The author name (optional)
			'dataset-license-url' => '', // Dataset license URL (optional)
			'dataset-license-term' => 'CC-BY', // Dataset license term (optional)
			'dataset-source-url' => '', // Dataset source URL (where is located the input file - optional)
			'dataset-source-term' => 'source', // Dataset source term (should not be changed)
			'dataset-update-frequency' => 'semester', // Dataset update frequency (change only if you know why !)
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
			'dataset-poi-category-default' => '', // Default category for the POI = ataset license URL (optional)
			'dataset-poi-id' => '', // A Unique ID for the POI (optional)
			'dataset-poi-title' => 'Titre',
			'dataset-poi-description' => 'Description',
			'dataset-poi-category' => 'Catégorie1',
			'dataset-poi-lat' => 'Latitude',
			'dataset-poi-long' => 'Longitude',
			'dataset-coordinate-system' => 'WGS84',
			'dataset-poi-address' => 'Adresse',
			'dataset-poi-postal' => 'Codepostal',
			'dataset-poi-city' => 'Ville',
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

		<title>Citadel - Converter <?php echo $version; ?></title>
		
		<!-- Custom styles -->
		<link href="css/style.css" rel="stylesheet">
	</head>

	<body>
		<div class="container">
			<p>This form is meant to facilitate the generation of mapping templates thatcan be used directly by the converter.</p>
			<p>A mapping template is basically a serialized PHP array, so it a text string that can be hosted on any web server, or sent in a form.</p>
			<p>This tool makes it generation easier</p>
			
			<form method="POST">
				<fieldset>
					<legend>Required fields</legend>
					<p><label>Author... : <input type="text" name="source" value="<?php echo $author; ?>" /></label></p>
				</fieldset>
				
				<fieldset>
					<legend>Extra fields</legend>
					<p><label>Licence... : <input type="text" name="source" value="<?php echo $licence; ?>" /></label></p>
					<p><label>File name... : <input type="text" name="source" value="<?php echo $filename; ?>" /></label></p>
				</fieldset>
				
			</form>
		</div>
	</body>
</html>



