<?php

$language = array(
	'error:nofileselected' => "No file selected",
	'error:nofileuploaded' => "No file uploaded",
	'error:upload' => "Error uploading dataset. Please try again.",
	'error:upload:system' => "System error uploading dataset.",
	'error:toobig' => "Dataset cannot be uploaded because it's too big.",
	'error:nodataset' => "No Dataset settings selected",
	'error:missingconversionsettings' => "No Conversion settings selected",
	'error:nofilefound' => "We've got a problem : no file found !",
	
	// Index file
	'converter:title' => "CSV to Citadel JSON converter",
	'converter:description' => "<p>This PHP converter library is meant to be used as a service that can convert CSV files to Citadel-JSON files on the fly.</p>
			<p>It includes the following services:</p>
			<ul>
				<li>convert.php : a converting API, used to convert CSV files to Citadel-JSON. The form below facilitates its use.</li>
				<li>build-template.php : a conversion template generator, that can be used to generate a new conversion template which can then be provided to the converting API.</li>
			</ul>
			<h2>Step 1: Prepare and publish your CSV file</h2>
			<p>Preparing the CSV file is not part of that tool. The CSV file should contain at least the geographical coordinates (latitude and longitude) and a title. Publish the file anywhere it can be accessed by a web address (URL). Using a public datastore is recommended.</p>
			<br />
			<h2>Step 2: Use the template generator</h2>
			<p><a href=\"template-generator.php?lang=en\" target=\"_blank\">Open the template generator in a new window</a></p>
			<p>The CSV fields needs to be mapped to Citadel JSON fields, which are designed to be easily displayed into a mobile application. The converter can be configured with a PHP array, but this tool make the process easier for non-developpers to prepare the fields mapping.</p>
			<p>Besides preparing the data themselves, this is the most important part as it will define how your data will be displayed into the final mobile application.</p>
			<p>Once done, you will get a rather un-readable text file (serialized PHP array), but don't worry and publish that file  anywhere it can be accessed by a web address (URL). This way you will be able to use it in the converter, and reuse it for any other CSV file that is formatted the same way.</p>
			<br />
			<h2>Step 3: Generate the converter URL</h2>
			<p>The following form will generate an URL that can be used to get the converted file, or directly into your application to provide it with a live data source.</p>",
	'converter:download:file' => "Download generated Citadel-JSON file",
	'converter:download:link' => "OR use this direct URL into your app: ",
	'converter:form' => "Configure to get the converted file",
	'converter:form:source' => "Source file (local file or URL): ",
	'converter:form:template' => "Template file: ",
	'converter:form:serialized_template' => "OR Template file content: ",
	'converter:form:filename' => "Exported file name (optional, no file extension): ",
	'converter:form:import' => "Import format: ",
	'converter:form:format' => "Export format: ",
	'converter:form:givelink' => "Give me the link to the converted file !",
	
	// Template generator
	'converter:tplgen:title' => "Conversion template generator",
	'converter:tplgen:description' => "<p>This form is meant to facilitate the generation of mapping templates that can be used directly by the converter.</p>
			<p>A mapping template is basically a serialized PHP array, so it a text string that can be hosted on any web server, or sent in a form.</p>
			<p>This tool makes it generation and edition easier</p>",
	'converter:tplgen:output' => "Generated conversion template",
	'converter:tplgen:form' => "Edit / Generate a conversion template",
	'converter:tplgen:legend:technical' => "Input technical settings",
	'converter:tplgen:firstline' => "Fields title on first line:",
	'converter:tplgen:firstline:yes' => "YES",
	'converter:tplgen:firstline:no' => "NO (direct data)",
	'converter:tplgen:delimiter' => "Delimiter:",
	'converter:tplgen:enclosure' => "Enclosure:",
	'converter:tplgen:escape' => "Escape:",
	'converter:tplgen:legend:metadata' => "Dataset description",
	'converter:tplgen:dataset_id' => "Dataset ID:",
	'converter:tplgen:dataset_lang' => "Dataset language:",
	'converter:tplgen:authorid' => "Author ID:",
	'converter:tplgen:authorname' => "Author name:",
	'converter:tplgen:updatefreq' => "Update frequency:",
	'converter:tplgen:licenceurl' => "Licence URL:",
	'converter:tplgen:licenceterm' => "Licence term:",
	'converter:tplgen:sourceurl' => "Source URL:",
	'converter:tplgen:sourceterm' => "Source term:",
	'converter:tplgen:legend:semantic' => "Semantic fields mapping",
	'converter:tplgen:legend:display' => "Display fields",
	'converter:tplgen:poi_default_cat' => "POI default category:",
	'converter:tplgen:poi_id' => "POI ID field:",
	'converter:tplgen:poi_title' => "POI title:",
	'converter:tplgen:poi_descr' => "POI description field:",
	'converter:tplgen:poi_cat' => "POI category field:",
	'converter:tplgen:legend:geo' => "Geographical fields",
	'converter:tplgen:lat' => "POI latitude field:",
	'converter:tplgen:long' => "POI longitude field:",
	'converter:tplgen:geosystem' => "POI geographical coordinates system field:",
	'converter:tplgen:address' => "POI address field:",
	'converter:tplgen:postalcode' => "POI postal code field:",
	'converter:tplgen:city' => "POI city field:",
	'converter:tplgen:submit' => "Generate the conversion template",
	'converter:tplgen:action' => "What do you want to get ?",
	'converter:tplgen:action:generate' => "Output template and keep tweaking / editing it",
	'converter:tplgen:action:export' => "Export final template (form will disappear)",
	
	'converter:tplgen:legend:import' => "OR Import and edit existing template",
	'converter:tplgen:import' => "Template file content",
	
	
);


