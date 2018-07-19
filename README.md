## Biography WordPress Plugin

Simple plugin for WordPress that builds a page in Admin under _Settings_. Provides the ability to select a photo, add a WYSIWYG editor, and enable/disable the plugin. There is also an option to add/remove a class name to the blurb (if needed - think turning display on mobile on/off).

This plugin makes use of **wp_options** database table, and creates a new option, adding the custom input values via an array.

### Installation Instructions:
* Upload the zipped file when adding new WordPress plugin and activate
* Navigate to Settings->Biography in Admin Menu
* In PHP, use the function _get_Tys_Bio()_ to output the blurb



