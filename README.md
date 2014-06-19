Citadel PHP Converter lib
=========================

Citadel PHP converter lib aims to facilitate the use of the Citadel-JSON format by providing the basic PHP structure that allows :
* Using it as an on-the-fly CSV to Citadel-JSON converter for your mobile applications
* Embedding it into other opensource products, such as CMS or Data stores, to provides natively Citadel-JSON files

This is rather a developper or tech-friendly tool.


Description
===========

The current library facilitates the conversion of static flat CSV files to Citadel-JSON files that can be used directly into the Citadel mobile application templates.


Usage
=====

* Drop the library folder on a webserver
* Tweak the convert.php file to adjust the conversion settings to the structure of your CSV source file
* Visit the web page and fill the form to get the converted file, or an URL that can be used in your application


Roadmap
=======

This code is only a basis for more advanced projects, so we have a full roadmap and we will appareciate a lot your feedback and suggestions ! Please use the Issues feature to provide your inputs.

The general idea of the roadmap is to 
* implement the other data fields from the Citadel-JSON format
* add an editor feature to allow using various fields into the output description field for POI
* enable live encoding from various datasets
** also enable getting templates from anywhere by an URL (almost done)
** handle different mapping templates
** add a mapping template editor / facilty
* add some converted data caching (so we update the file only when requested, or depending on some info, whether in the dataset or.. we'll see)
* enable to plug the library to other data sources than CSV files, and particularly database backends


