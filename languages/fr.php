<?php

$language = array(
	'error:nofileselected' => "Aucun fichier sélectionné",
	'error:nofileuploaded' => "Aucun fichier chargé",
	'error:upload' => "Erreur lors de l'envoi du fichier. Veuillez réessayer.",
	'error:upload:system' => "Erreur système lors de l'envoi du jeu de données.",
	'error:toobig' => "Le jeu de données n'a pas pu être chargé car il est trop gros.",
	'error:nodataset' => "Aucun jeu de données sélectionné",
	'error:missingconversionsettings' => "Aucun réglage de conversion sélectionné",
	'error:nofilefound' => "Oups, ça ne marche pas : aucun fichier trouvé !",
	
	// Index file
	'converter:title' => "Convertisseur CSV vers Citadel JSON",
	'converter:description' => "<p>Cette bibliothèque de conversion en PHP est faite pour être utilisée comme un service web qui vous permet de convertir des fichiers CSV en Citadel-JSON à la volée.</p>
			<p>Elle inclut les services suivants :</p>
			<ul>
				<li>convert.php : API de conversion, utilisée pour convert des fichiers CSV en Citadel-JSON. Le formulaire ci-dessous simplifie utilisation.</li>
				<li>build-template.php : un outil de génération de modèles de conversion, qui peut être utilisé pour créer de nouveaux modèles de conversion directement utilisables par l'API de conversion.</li>
			</ul>
			<h2>Etape 1: Préparez et publiez votre fichier CSV</h2>
			<p>Cette étape nécessaire ne fait pas l'objet de cet outil. Le fichier CSV doit contenir au moins les coordonnées géographiques (latitude et longitude) ainsi qu'un titre. Publiez le fichier de sorte que vous puissiez y accéder directement par une adresse web unique (URL). L'utilisation d'un répertoire open data est conseillé.</p>
			<br />
			<h2>Etape 2 : Préparez le modèle de conversion</h2>
			<p><a href=\"template-generator.php?lang=fr\" target=\"_blank\">Ouvrir le générateur de modèle de conversion dans une nouvelle fenêtre</a></p>
			<p>Les champs (colonnes) du fichier CSV doivent être associés aux champs du fichier Citadel-JSON, de sorte qu'ils puissent être directement compréhensibles par l'application mobile. Le convertisseur peut être configuré via un tableau PHP, mais cette page permet de simplifier cette étape pour les non-développeurs.</p>
			<p>Cette étape est essentielle, car elle permet de définir comment vos données seront affichées dans l'application mobile résultante. N'hésitez pas à tester les fichiers générés, et à affiner le modèle de conversion.</p>
			<p>Une fois cela fait, vous récupérez un fichier texte relativement peu lisble (en fait un tableau PHP sérialisé), qui doit être publiè de sorte qu'il soit accessible via une adresse web (URL), comme le fichier CSV source. De cette manière, vous pourrez l'utiliser avec le convertisseur, et le réutiliser pour tout fichier qui utilise la même structure CSV.</p>
			<br />
			<h2>Etape 3 : Générez l'URL de conversion</h2>
			<p>Le formulaire suivant vous permet de fabriquer l'URL de téléchargement du fichier converti, à partir du fichier source et du modèle de conversion. Vous pouvez l'utiliser pour récupérer le fichier converti, ou pour fournir directement cette URL à votre application pour qu'elle dispose de données à jour.</p>",
	'converter:download:file' => "Télécharger le fichier Citadel-JSON généré",
	'converter:download:link' => "OU utilisez ce lien direct dans votre application : ",
	'converter:download:shortlink' => "OR utilisez ce lien plus court : ",
	'converter:form' => "Configurez pour récupérer le fichier converti",
	'converter:form:source' => "Fichier source (fichier local ou URL) : ",
	'converter:form:template' => "Fichier du modèle de conversion : ",
	'converter:form:serialized_template' => "OU Contenu du modèle de conversion : ",
	'converter:form:filename' => "Nom du fichier exporté (optionel, ne pas indiquer l'extension) : ",
	'converter:form:import' => "Format import : ",
	'converter:form:format' => "Format export : ",
	'converter:form:givelink' => "Donnez-moi le lien vers le fichier converti !",
	
	// Template generator
	'converter:tplgen:title' => "Générateur de modèle de conversion",
	'converter:tplgen:description' => "<p>Ce formulaire permet de créer des modèles de conversion utilisables directement par le convertisseur.</p>
			<p>Un modèle de conversion consiste en un tableau PHP sérialisé, c'est-à-dire un simple fichier texte qui peut être stocké en ligne, ou envoyé au convertisseur via un formulaire.</p>
			<p>Cet outil simplifie la création et la modification de ces fichiers.</p>",
	'converter:tplgen:output' => "Modèle de conversion généré",
	'converter:tplgen:form' => "Créer / modifier le modèle de conversion",
	'converter:tplgen:legend:technical' => "Informations techniques",
	'converter:tplgen:firstline' => "La première ligne contient-elle les titres des colonnes ? :",
	'converter:tplgen:firstline:yes' => "OUI",
	'converter:tplgen:firstline:no' => "NON (données seules)",
	'converter:tplgen:delimiter' => "Délimiteur :",
	'converter:tplgen:enclosure' => "Encadrement des données :",
	'converter:tplgen:escape' => "Caractère d'échappement :",
	'converter:tplgen:legend:metadata' => "Description du jeu de données",
	'converter:tplgen:dataset_id' => "ID du jeu de données :",
	'converter:tplgen:dataset_lang' => "Language des données :",
	'converter:tplgen:authorid' => "ID de l'auteur :",
	'converter:tplgen:authorname' => "Nom de l'auteur :",
	'converter:tplgen:updatefreq' => "Fréquence de mise à jour :",
	'converter:tplgen:licenceurl' => "URL de la licence :",
	'converter:tplgen:licenceterm' => "Nom de la licence :",
	'converter:tplgen:sourceurl' => "URL de la source des données :",
	'converter:tplgen:sourceterm' => "Nom de la source des données :",
	'converter:tplgen:legend:semantic' => "Correspondance des champs",
	'converter:tplgen:legend:display' => "Champs d'affichage",
	'converter:tplgen:poi_default_cat' => "Catégorie par défaut des points d'intérêt :",
	'converter:tplgen:poi_id' => "Champ de l'ID des points d'intérêt :",
	'converter:tplgen:poi_title' => "Champ du titre des points d'intérêt :",
	'converter:tplgen:poi_descr' => "Champ de la description des points d'intérêt :",
	'converter:tplgen:poi_cat' => "Champ de la catégorie des points d'intérêt :",
	'converter:tplgen:legend:geo' => "Champs géographiques",
	'converter:tplgen:lat' => "Champ de la latitude des points d'intérêt :",
	'converter:tplgen:long' => "Champ de la longitude des points d'intérêt :",
	'converter:tplgen:geosystem' => "Champ du système de coordonnnées géographiques des points d'intérêt :",
	'converter:tplgen:address' => "Champ de l'adresse des points d'intérêt :",
	'converter:tplgen:postalcode' => "Champ du code postal des points d'intérêt :",
	'converter:tplgen:city' => "Champ de la ville des points d'intérêt :",
	'converter:tplgen:submit' => "Générer le modèle de conversion",
	'converter:tplgen:action' => "Que souhaitez-vous obtenir ?",
	'converter:tplgen:action:generate' => "Générer le template et continuer à éditer",
	'converter:tplgen:action:export' => "Exporter le modèle (le formulaire va disparaître)",
	
	'converter:tplgen:legend:import' => "OU Importer et modifier un modèle existant",
	'converter:tplgen:import' => "Contenu du modèle",
	
);

