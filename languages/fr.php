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
			<br />
			<h2>Etape 1: Préparez et publiez votre fichier CSV</h2>
			<p>Cette étape nécessaire ne fait pas l'objet de cet outil. Le fichier CSV doit contenir au moins les coordonnées géographiques (latitude et longitude) ainsi qu'un titre. Publiez le fichier de sorte que vous puissiez y accéder directement par une adresse web unique (URL). L'utilisation d'un répertoire open data est conseillé.</p>
			<h2>Etape 2 : Préparez le modèle de conversion</h2>
			<p><a href=\"template-generator.php\" target=\"_blank\">Ouvrir le générateur de modèle de conversion dans une nouvelle fenêtre</a></p>
			<p>Les champs (colonnes) du fichier CSV doivent être associés aux champs du fichier Citadel-JSON, de sorte qu'ils puissent être directement compréhensibles par l'application mobile. Le convertisseur peut être configuré via un tableau PHP, mais cette page permet de simplifier cette étape pour les non-développeurs.</p>
			<p>Cette étape est essentielle, car elle permet de définir comment vos données seront affichées dans l'application mobile résultante. N'hésitez pas à tester les fichiers générés, et à affiner le modèle de conversion.</p>
			<p>Une fois cela fait, vous récupérez un fichier texte relativement peu lisble (en fait un tableau PHP sérialisé), qui doit être publiè de sorte qu'il soit accessible via une adresse web (URL), comme le fichier CSV source. De cette manière, vous pourrez l'utiliser avec le convertisseur, et le réutiliser pour tout fichier qui utilise la même structure CSV.</p>
			<h2>Etape 3 : Générez l'URL de conversion</h2>
			<p>Le formulaire suivant vous permet de fabriquer l'URL de téléchargement du fichier converti, à partir du fichier source et du modèle de conversion. Vous pouvez l'utiliser pour récupérer le fichier converti, ou pour fournir directement cette URL à votre application pour qu'elle dispose de données à jour.</p>",
	'converter:download:file' => "Télécharger le fichier Citadel-JSON généré",
	'converter:download:link' => "OU utilisez ce lien direct dans votre application : ",
	'converter:form' => "Configurez pour récupérer le fichier converti",
	'converter:form:source' => "Fichier source (fichier local ou URL) : ",
	'converter:form:template' => "Fichier du modèle de conversion : ",
	'converter:form:filename' => "Nom du fichier exporté (optionel, ne pas indiquer l'extension) : ",
	'converter:form:givelink' => "Donnez-moi le lien vers le fichier converti !",
	
	
);

