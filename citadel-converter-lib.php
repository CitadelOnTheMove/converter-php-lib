<?php
session_start();
$version = "0.1";

// Fix UTF-8 encoding
require_once('vendors/ForceUTF8/Encoding.php');
use \ForceUTF8\Encoding;

/* Converter lib should :
 * Take a CSV file as input
 * Use a predefined conversion settings
 * output a ready-to-use JSON file
 * cache it until original CSV has changed and/or using validity metadata
 */


// Set converter language
$lang = @strip_tags($_REQUEST['lang']);
if (empty($lang)) $lang = 'en';
global $CONFIG;
if (!include_once("languages/$lang.php")) { include_once("languages/en.php"); }
$CONFIG['language'] = $language;

/*
		$filename = $_SESSION['dataset-id'];
		if ($id == null || empty($id)) {
			$filename = 'dataset';
		}
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Content-type: application/json; charset=UTF-8');
		header('Content-disposition: attachment;filename="'.$filename.'.json"');
		echo $_SESSION['dataset-json'];
*/


/*
function getDatasetPreview($lines = 0) {
	$preview = "";
	if ($lines > 0) {
		$content = file($_SESSION['dataset']);
		
		for ($i = 0; $i < $lines && $i < count($content); $i++) {
			$preview = $preview . toUTF8($content[$i]);
		}
	}
	else {
		$content = file_get_contents($_SESSION['dataset']);
		$preview = toUTF8($content);
	}
	
	return $preview;
}
*/



/* Return Citadel-JSON from any CSV file, using a pre-defined template mapping
 * $dataset : the dataset array (from a CSV file, one row per POI)
 * $skipFirstRow : whether first row is a label or not
 */
function getJSON($dataset, $template) {
	global $template;
	global $array_mapping;
	$skipFirstRow = $template['skip-first-row'];
	$array_mapping = setArrayMapping($dataset[0]);
	$now = new DateTime();
	
	// Build the JSON
	$json = new stdClass();
	$json->dataset = new stdClass();
	
	// Metadata fields
	$json->dataset->id = $template['metadata']['dataset-id'];
	$json->dataset->updated = $now->format('c');
	$json->dataset->created = $now->format('c');
	$json->dataset->lang = $template['metadata']['dataset-lang'];
	$json->dataset->author = new stdClass();
	$json->dataset->author->id = $template['metadata']['dataset-author-id'];
	$json->dataset->author->value = $template['metadata']['dataset-author-name'];
	$json->dataset->license = new stdClass();
	$json->dataset->license->href = $template['metadata']['dataset-license-url'];
	$json->dataset->license->term = $template['metadata']['dataset-license-term'];
	$json->dataset->link = new stdClass();
	$json->dataset->link->href = $template['metadata']['dataset-source-url'];
	$json->dataset->link->term = $template['metadata']['dataset-source-term'];
	$json->dataset->updatefrequency = $template['metadata']['dataset-update-frequency'];
	
	$defaultCategories = $template['mapping']['dataset-poi-category-default'];
	
	$json->dataset->poi = array();
	
	// Data content
	// @TODO : replace by a foreach loop
	//for ($i = $skipFirstRow ? 1 : 0; $i < count($dataset); $i++) {
	//	$poiArray = $dataset[$i];
	//}
	$i = 0;
	foreach ($dataset as $poiArray) {
		$i++;
		// Skip first row if set as headers row
		if (($i == 1) && $skipFirstRow) continue;
		$poiObj = new StdClass();
		$poiObj->id = getValue($poiArray, 'dataset-poi-id');
		// Set incremental id if no defined id in the dataset (required by apps)
		//if (empty($poiObj->id)) { $poiObj->id = $i + 1; }
		if (empty($poiObj->id)) { $poiObj->id = $i; }
		$poiObj->title = getValue($poiArray, 'dataset-poi-title');
		$poiObj->description = getValue($poiArray, 'dataset-poi-description');
		if ($poiObj->description == null) {
			$poiObj->description = "";
		}
		$poiObj->category = explode(',', getValue($poiArray, 'dataset-poi-category'));
		array_walk($poiObj->category, create_function('&$val', '$val = trim($val);'));
		if (count($poiObj->category) == 0 || (count($poiObj->category) == 1 && $poiObj->category[0] == '')) {
			$poiObj->category = $defaultCategories;
		}
		$location = new StdClass();
		$location->point = new StdClass();
		$location->point->term = "centroid";
		$location->point->pos = new StdClass();
		$location->point->pos->srsName = ($template['mapping']['dataset-coordinate-system'] == "WGS84") ? "http://www.opengis.net/def/crs/EPSG/0/4326" : null;
		if ($template['mapping']['dataset-poi-lat'] == $template['mapping']['dataset-poi-long']) {
			$latlong = getValue($poiArray, 'dataset-poi-lat');
			$latlong = trim(str_replace(';', ' ', $latlong));
			$location->point->pos->posList = $latlong;
		} else {
			$location->point->pos->posList = trim(getValue($poiArray, 'dataset-poi-lat')) . ' ' . trim(getValue($poiArray, 'dataset-poi-long'));
		}
		$location->address = new StdClass();
		$location->address->value = getValue($poiArray, 'dataset-poi-address');
		$location->address->postal = getValue($poiArray, 'dataset-poi-postal');
		$location->address->city = getValue($poiArray, 'dataset-poi-city');
		$poiObj->location = $location;
		$poiObj->attribute = array();

		$json->dataset->poi[] = $poiObj;
	}
	
	// Handle older versions of JSON encoding functions
	if (version_compare(phpversion(), '5.4', '<')) {
		// PHP < 5.4 doesn't excape unicode (you end up with \u00 characters)
		// So we need to convert it before returning the content
		$json = json_encode($json);
		return preg_replace_callback(
			'/\\\\u([0-9a-f]{4})/i',
			function ($matches) {
				$sym = mb_convert_encoding(
					pack('H*', $matches[1]), 
					'UTF-8', 
					'UTF-16'
				);
				return $sym;
			},
			$json
		);
	} else {
		return json_encode($json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
	}
}


/* Gets the value of the specified (semantic) key in a data line
 * $poiArray : an array representing a row of CSV data
 * $key : the name of the wanted key in the Citadel-JSON schema
 */
function getValue($poiArray, $key) {
	// Get the semantic => CSV name mapping
	global $template;
	// Get the CSV name => array key mapping
	global $array_mapping;
	
	// Return value only if mapping defined
	if ($template['mapping'][$key] !== "") {
		// Find CSV name from semantic JSON name mapping
		$map_key = $template['mapping'][$key];
		// Find array index from CSV name mapping
		if (array_key_exists($map_key, $array_mapping)) $array_key = $array_mapping[$map_key];
		else error_log("DEBUG : missing key : key=$key => map_key=$map_key does not exist in array_mapping");
		//echo "$key => $map_key => $array_key = {$poiArray[$array_key]}<br />";
		//echo print_r($poiArray, true) . '<hr />';
		// Return the extracted value
		if (!empty($array_key)) return $poiArray[$array_key];
		else {
			error_log("DEBUG : empty array_key for key=$key, map_key=$map_key");
			return null;
		}
	}
	return null;
}


/* Translates the labels from the CSV file into a key mapping
 * The resulting array is used to process the CSV lines arrays
 * This is required because the dataset array from CSV does not have named keys
 * 
 * $labels : the actual labels OR the first line of data, in array form 
 *           (we need to count columns even if there is no label)
 * 
 * Note : is there is no label (data starts at line 1), this function will use 
 *        array index numbers instead of named keys.
 * Important : array index numbers start at 0 and not 1 (1 less than column number) :
 *             so column 1 becomes index 0, column 2 becomes index 1, etc.
 */
function setArrayMapping($labels) {
	global $template;
	if ($template['skip-first-row']) {
		$labels = array_map("toUTF8", $labels);
		$array_mapping = array_flip($labels);
	} else {
		for ($i = 0; $i < count($labels); $i++) {
			$array_mapping["$i"] = "$i";
		}
	}
	//echo "ARRAY MAPPING => " . print_r($array_mapping, true) . '<hr />';
	return $array_mapping;
}


// Get the dataset as an array : one array entry per "row" or POI
// The first row should be the dataset column labels (for easier mapping)
function getDataset($dataset, $delimiter = ';', $enclosure = '"', $escape = '\\') {
	$result = array();
	$content = file($dataset);
	if ($content) {
		foreach ($content as $line) {
			$result[] = str_getcsv(toUTF8($line), $delimiter, $enclosure, $escape);
		}
		return $result;
	} else return false;
}


// Converts anything to UTF-8
function toUTF8($str) {
	// Make the conversion
	$encoding = getEncoding($str);
	if ($encoding == 'UTF-8') {
		return Encoding::toUTF8($str);
	} else {
		return Encoding::fixUTF8($str);
	}
}

// Gets the current encoding
function getEncoding($str) {
	$enc_order = array('ASCII', 'JIS', 'UTF-8', 'ISO-8859-1', 'WINDOWS-1521');
	//$enc_order = array('UTF-8', 'ASCII', 'ISO-8859-1', 'ISO-8859-2', 'ISO-8859-3', 'ISO-8859-4', 'ISO-8859-5', 'ISO-8859-6', 'ISO-8859-7', 'ISO-8859-8', 'ISO-8859-9', 'ISO-8859-10', 'ISO-8859-13', 'ISO-8859-14', 'ISO-8859-15', 'ISO-8859-16', 'Windows-1251', 'Windows-1252', 'Windows-1254');
	$enc_order = implode(', ', $enc_order);
	return mb_detect_encoding($str, $enc_order);
}

// Get and clean the requests
function get_input($variable, $default = '', $filter= true) {
	if (!isset($_REQUEST[$variable])) return $default;
	if (is_array($_REQUEST[$variable])) {
		$result = $_REQUEST[$variable];
	} else {
		$result = trim($_REQUEST[$variable]);
	}
	if ($filter) {
		if (is_array($result)) $result = array_map('strip_tags', $result);
		else $result = strip_tags($result);
	}
	return $result;
}

// Return translation from a key
// @TODO Add sprintf params and logic
function echo_lang($key, $params = array()) {
	global $CONFIG;
	return $CONFIG['language'][$key];
}



