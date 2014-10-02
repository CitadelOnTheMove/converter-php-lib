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


function add_lang_switch($lang = 'en', $lang_list = array('en', 'fr')) {
	$return = '';
	foreach ($lang_list as $l) {
		if ($l == $lang) { $return .= '<strong><a href="?lang=' . $l . '">' . strtoupper($l) . '</a></strong> '; } 
		else { $return .= '<a href="?lang=' . $l . '">' . strtoupper($l) . '</a> '; }
	}
	return $return;
}

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
function renderJSON($dataset, $template) {
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
	$defaultCategories = explode(',', $defaultCategories);
	
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
		$location->point->pos->srsName = ($template['mapping']['dataset-coordinate-system'] == "WGS84") ? "http://www.opengis.net/def/crs/EPSG/0/4326" : "";
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



/* Return geoJSON + Citadel JSON from any CSV file, using a pre-defined template mapping
 * Includes the Citadel JSON fields in it as well
 * $dataset : the dataset array (from a CSV file, one row per POI)
 * $skipFirstRow : whether first row is a label or not
 */
// @TODO : consider 2 cases
// 	1) export CSV file to geoJSON + Citadel JSON
// 	2) if we already have geoJSON, don't modify it at all, and only add the new Citadel JSON
// 	=> We need to merge export functions and add input and export parameters
function renderGeoJSON($dataset, $template) {
	global $template;
	global $array_mapping;
	$skipFirstRow = $template['skip-first-row'];
	$array_mapping = setArrayMapping($dataset[0]);
	$now = new DateTime();
	
	// Build the JSON
	$json = new stdClass();
	
	// Build the Citadel JSON
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
	$defaultCategories = explode(',', $defaultCategories);
	
	$json->dataset->poi = array();
	
	// Data content
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
	
	// Build the geoJSON
	$json->type = "FeatureCollection";
	$json->generator = "Citadel on the Move PHP converter";
	$json->copyright = $template['metadata']['dataset-license-term'] . ' ' . $template['metadata']['dataset-license-url'];
	$json->timestamp = $now->format('c');
	$json->features = new stdClass();
	
	$json->features = array();
	
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
		$poiObj->type = "Feature";
		$poiObj->id = getValue($poiArray, 'dataset-poi-id');
		// Set incremental id if no defined id in the dataset (required by apps)
		//if (empty($poiObj->id)) { $poiObj->id = $i + 1; }
		if (empty($poiObj->id)) { $poiObj->id = $i; }
		// Build Feature properties
		$poiObj->properties = new StdClass();
		$poiObj->properties->title = getValue($poiArray, 'dataset-poi-title');
		$poiObj->properties->description = getValue($poiArray, 'dataset-poi-description');
		if ($poiObj->properties->description == null) {
			$poiObj->properties->description = "";
		}
		$poiObj->properties->category = explode(',', getValue($poiArray, 'dataset-poi-category'));
		array_walk($poiObj->properties->category, create_function('&$val', '$val = trim($val);'));
		if (count($poiObj->properties->category) == 0 || (count($poiObj->properties->category) == 1 && $poiObj->properties->category[0] == '')) {
			$poiObj->properties->category = $defaultCategories;
		}
		$poiObj->properties->address = getValue($poiArray, 'dataset-poi-address');
		$poiObj->properties->postal = getValue($poiArray, 'dataset-poi-postal');
		$poiObj->properties->city = getValue($poiArray, 'dataset-poi-city');
		// Build Point properties
		$location = new StdClass();
		$location->type = "Point";
		$location->coordinates = array();
		if ($template['mapping']['dataset-poi-lat'] == $template['mapping']['dataset-poi-long']) {
			$latlong = getValue($poiArray, 'dataset-poi-lat');
			$latlong = trim($latlong);
			$latlong = explode(';', $latlong);
			$location->coordinates = array((float) $latlong[1], (float) $latlong[0]);
		} else {
			$latlong = array((float) trim(getValue($poiArray, 'dataset-poi-long')), (float) trim(getValue($poiArray, 'dataset-poi-lat')));
			$location->coordinates = $latlong;
		}
		$poiObj->geometry = $location;

		$json->features[] = $poiObj;
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
	if (empty($template) || empty($array_mapping)) {
		error_log("ERROR : empty template or array_mapping");
		return false;
	}
	// Return value only if mapping defined
	if ($template['mapping']["$key"] !== "") {
		// Find CSV name from semantic JSON name mapping
		$map_key = $template['mapping']["$key"];
		// Find array index from CSV name mapping
		if (isset($array_mapping["$map_key"])) {
			$array_key = $array_mapping["$map_key"];
		} else {
			error_log("DEBUG : missing key : key=$key => map_key=$map_key does not exist in array_mapping");
		}
		//echo "$key => $map_key => $array_key = {$poiArray[$array_key]}<br />";
		//echo print_r($poiArray, true) . '<hr />';
		//if ($key == "dataset-poi-title") error_log("DEBUG : $key => " . $map_key . " / " . $array_key . " / " . $poiArray["$array_key"]);
		// Return the extracted value
		if ($array_key !== null) {
			return $poiArray["$array_key"];
		} else {
			error_log("DEBUG : empty array_key for key=$key, map_key=$map_key");
			return "";
		}
	}
	return "";
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
function getCSVDataset($dataset, $delimiter = ';', $enclosure = '"', $escape = '\\') {
	$result = array();
	$content = file($dataset);
	if ($content) {
		foreach ($content as $line) {
			$result[] = str_getcsv(toUTF8($line), $delimiter, $enclosure, $escape);
		}
		return $result;
	} else return false;
}


// See http://stackoverflow.com/questions/10290849/how-to-remove-multiple-utf-8-bom-sequences-before-doctype
// BOM and other binary characters break json_decode...
function remove_utf8_bom($text) {
	$bom = pack('H*','EFBBBF');
	$text = preg_replace("/^$bom/", '', $text);
	return $text;
}

// Get the geoJSON dataset
function getGeoJSON($dataset) {
	$result = array();
	// File retrieval can fail on timeout or redirects, so make it more failsafe
	//$geojson = file_get_contents($dataset);
	$context = stream_context_create(array(
			'http' => array(
				'max_redirects' => 5,
				'timeout' => 60,
			)
		));
	// @TODO : we should store files at least for a few minutes or hours in a tmp/folder, 
	// using timestamp and URL hash for quick retrieval based on time and URL source unicity
	$geojson = file_get_contents($dataset, false, $context);
	$geojson = utf8_encode($geojson);
	$geojson = str_replace(array("\n","\r"),"",$geojson); 
	$geojson = preg_replace('/([{,]+)(\s*)([^"]+?)\s*:/','$1"$3":',$geojson); 
	$geojson = preg_replace('/(,)\s*}$/','}',$geojson);
	$geojson = remove_utf8_bom($geojson);
	$geojson = preg_replace_callback('/([\x{0000}-\x{0008}]|[\x{000b}-\x{000c}]|[\x{000E}-\x{001F}])/u', function($sub_match){return '\u00' . dechex(ord($sub_match[1]));},$geojson);
	$geojson = json_decode($geojson);
	//echo print_r($geojson, true); // debug
	return $geojson;
}

// Transform the geoJSON dataset into an array : one array entry per "row" == POI
// The first row should be the dataset column labels (for easier mapping)
function getGeoJSONDataset($geojson) {
	$result = array();
	if ($geojson) {
		switch($geojson->type) {
			case "FeatureCollection":
				// Build coordinates keys first
				$keys = array('longitude', 'latitude');
				// Build properties keys and use them as we would with "first CSV line"
				foreach($geojson->features[0]->properties as $key => $val) { $keys[] = $key; }
				//echo print_r($keys, true) . '<hr />';
				$result[] = $keys;
				foreach($geojson->features as $element) {
					$poi = array($element->geometry->coordinates[0], $element->geometry->coordinates[1]);
					// Add POI data
					foreach($keys as $key) {
						if (in_array($key, array('longitude', 'latitude'))) continue;
						$poi[] = $element->properties->$key;
					}
					$result[] = $poi;
					//echo print_r($poi, true) . '<hr />';
				}
				break;
			case "Feature":
				// Only one feature in this file ??
			case "Point":
			default:
				// Valid geoJSON but pointless
				return false;
		}
		return $result;
	} else return false;
}


// Transform the osmJSON dataset into an array : one array entry per "row" == POI
// The first row should be the dataset column labels (for easier mapping)
// Note that OSM JSON data can be exported directly by Overpass API
function getOsmJSONDataset($osmjson) {
	$result = array();
	$main_keys = array('id', 'longitude', 'latitude');
	$title_keys = $main_keys;
	if ($osmjson) {
		
		// Build label keys first
		foreach($osmjson->elements as $element) {
			foreach($element->tags as $key => $tag) {
				if (!in_array($key, $title_keys)) $title_keys[] = $key;
			}
		}
		$result[] = $title_keys;
		
		// Build properties keys and use them as we would with "first CSV line"
		//echo print_r($keys, true) . '<hr />';
		foreach($osmjson->elements as $element) {
			$poi = array($element->id, $element->lon, $element->lat);
			// Add POI data
			foreach($title_keys as $key) {
				if (in_array($key, $main_keys)) continue;
				$poi[] = $element->tags->$key;
			}
			$result[] = $poi;
			//echo print_r($poi, true) . '<hr />';
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
	// Note : function accepts array or comma-separated list
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
	$return = $CONFIG['language'][$key];
	if (empty($return)) $return = $key;
	return $return;
}



