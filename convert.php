<?php
include_once "citadel-converter-lib.php";

// Source file : any local or remote file
$source = $_REQUEST['source'];
if (empty($source)) $source = 'samples/dataset.csv';

// Export filename - we could use some templating such as DATE to add a date...
$filename = strip_tags($_REQUEST['filename']);
if (empty($filename)) { $filename = 'export_' . date('YmdHis'); }

// Mapping template : any local or remote template config
global $template;
// @TODO : allow fetching serialized array structure from remote source
$remote_template = $_REQUEST['template'];
if (!empty($remote_template)) {
	$remote_template = file_gets_contents($remote_template);
	$remote_template = unserialize($remote_template);
	$template = $remote_template;
} else {
	// Let's define the metadata and mapping values
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
}

// Load CSV file
// str_getcsv ( string $input [, string $delimiter = "," [, string $enclosure = '"' [, string $escape = "\\" ]]] )
// $csv = array_map('str_getcsv', file('data.csv')); // Parse full file in one line
// auto_detect_line_endings();
//echo "<html><head><meta charset='UTF-8' /></head><body>";
$csv = getDataset($source, $template['delimiter'], $template['enclosure'], $template['escape']);
if (!$csv) { echo $language['error:nofilefound']; exit; }

$citadel_json = getJSON($csv, $template);


//echo $citadel_json; exit;
//echo toUTF8($citadel_json); exit;

/*
$preview = toUTF8($content);
*/

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json; charset=UTF-8');
header('Content-disposition: attachment;filename="'.$filename.'.json"');
echo $citadel_json;

