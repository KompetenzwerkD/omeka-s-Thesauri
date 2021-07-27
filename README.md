# Omeka S module Thesauri

This module for Omeka S helps with the creation and curation of thesauri. 

## Requirements

* Omeka S > 3.0.0
* CustomVocabs
* SKOS vocabulary

## Installation

* Download this git repository into the `<omeka_path>/modules` directory.
* Rename the folder to `Thesauri`
* Press the Install button in the `modules` view in the Omeka S admin panel. 

The installation creates two resource templates: `Concept` and `Concept Scheme`

## Usage

The module provides a view for managing thesauri. You can find it under "Thesauri" on the left sidebar.

To create a new thesaurus use the "Add new thesaurus" button on the top right. The name you give it will be used to create the item set (Concept scheme) as well as custom vocab. You can both rename the item set and custom vocab later, if needed.

To view all concepts in a thesaurus, click the little down arrow icon right of the name. The concepts will be listed in alphabetical order, and with the plus icon belove the concepts you can create a new one. 

You can delete a thesaurus by pressing the delete icon left of its name and then confirming the action. When you delete a thesaurus the item set, the custom vocab AND all concepts within the concept scheme will be deleted.

## License

GPLv3

## Authors

kompetenzwerkd@saw-leipzig.de

## Copyright

2021, Sächsische Akademie der Wissenschaften zu Leipzig