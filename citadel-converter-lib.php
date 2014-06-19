<?php
session_start();
$version = "0.1";

// Fix UTF-8 encoding
require_once('vendors/forceutf8/src/ForceUTF8/Encoding.php');
use \ForceUTF8\Encoding;

/* Converter lib should :
 * Take a CSV file as input
 * Use a predefined conversion settings
 * output a ready-to-use JSON file
 * cache it until original CSV has changed and/or using validity metadata
 */

$en_strings = array(
	'error:nofileselected' => "No file selected",
	'error:nofileuploaded' => "No file uploaded",
	'error:upload' => "Error uploading dataset. Please try again.",
	'error:upload:system' => "System error uploading dataset.",
	'error:toobig' => "Dataset cannot be uploaded because it's too big.",
	'error:nodataset' => "No Dataset settings selected",
	'error:missingconversionsettings' => "No Conversion settings selected",
	'error:nofilefound' => "We've got a problem : no file found !",
);


$fr_strings = array(
	'error:nofileselected' => "Aucun fichier sélectionné",
	'error:nofileuploaded' => "Aucun fichier chargé",
	'error:upload' => "Erreur lors de l'envoi du fichier. Veuillez réessayer.",
	'error:upload:system' => "Erreur système lors de l'envoi du jeu de données.",
	'error:toobig' => "Le jeu de données n'a pas pu être chargé car il est trop gros.",
	'error:nodataset' => "Aucun jeu de données sélectionné",
	'error:missingconversionsettings' => "Aucun réglage de conversion sélectionné",
	'error:nofilefound' => "Oups, ça ne marche pas : aucun fichier trouvé !",
);

$lang = strip_tags($_REQUEST['lang']);
switch($lang) {
	/*
	case 'it':
		$language = $it_strings;
		break;
	*/
	case 'fr':
		$language = $fr_strings;
		break;
	default:
		$language = $en_strings;
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
function getJSON($dataset, $template) {
	global $template;
	global $array_mapping;
	$skipFirstRow = $template['skip-first-row'];
	$array_mapping = setArrayMapping($dataset[0]);
	$now = new DateTime();
	
	// Build the JSON
	$json = new stdClass();
	$json->dataset = new stdClass();
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
	// @TODO : replace by a foreach loop
	for ($i = $skipFirstRow ? 1 : 0; $i < count($dataset); $i++) {
		$poiArray = $dataset[$i];
		$poiObj = new StdClass();
		$poiObj->id = getValue($poiArray, 'dataset-poi-id');
		// Set incremental id if no defined id in the dataset (required by apps)
		if (empty($poiObj->id)) { $poiObj->id = $i + 1; }
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
	}
	return json_encode($json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
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
		$array_key = $array_mapping[$map_key];
		
		//echo "$key => $map_key => $array_key = {$poiArray[$array_key]}<br />";
		//echo print_r($poiArray, true) . '<hr />';
		// Return the extracted value
		return $poiArray[$array_key];
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




function getEncoding($str) {
	$enc_order = array('ASCII', 'JIS', 'UTF-8', 'ISO-8859-1', 'WINDOWS-1521');
	//$enc_order = array('UTF-8', 'ASCII', 'ISO-8859-1', 'ISO-8859-2', 'ISO-8859-3', 'ISO-8859-4', 'ISO-8859-5', 'ISO-8859-6', 'ISO-8859-7', 'ISO-8859-8', 'ISO-8859-9', 'ISO-8859-10', 'ISO-8859-13', 'ISO-8859-14', 'ISO-8859-15', 'ISO-8859-16', 'Windows-1251', 'Windows-1252', 'Windows-1254');
	$enc_order = implode(', ', $enc_order);
	return mb_detect_encoding($str, $enc_order);
}

function toUTF8($str) {
	$encoding = getEncoding($str);
	if ($encoding == 'UTF-8') {
		return Encoding::toUTF8($str);
	} else {
		return Encoding::fixUTF8($str);
	}
	//echo '<br />' . $encoding;
	//return mb_convert_encoding($str, $encoding, 'UTF-8');
	if ($encoding != 'UTF-8') {
		return utf8_encode($str);
		// $str = iconv($encoding /**/== 'ISO-8859-1' ? 'CP850' : $encoding/**/, "UTF-8", $str);
		return iconv($encoding, "UTF-8", $str);
		/* italian if comment, decomment good french
		if ($encoding == 'ISO-8859-1') {
			return iconv('CP850', "UTF-8", $str);
		} else {
			return iconv($encoding, "UTF-8", $str);
		}
		*/
	}
	//echo "=>NOCONV | $str<br />";
	return $str;
}

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

