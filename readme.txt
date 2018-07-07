=== Arctic Integration for Caldera Forms ===
Contributors: nathanntg
Tags: arctic, caldera forms, form notification
Requires at least: 3.9
Tested up to: 4.9
Stable tag: 0.1.0
License: MIT
License URI: https://opensource.org/licenses/MIT

Send data from Caldera Forms to Arctic Reservations, creating either person or inquiry records.

== Description ==

This is a plugin processor for Caldera Forms that allows sending form information to an installation
of [Arctic Reservations](http://www.arcticreservations.com) using the Arctic API. The plugin currently
supports two processors:

1. Creating a person record
2. Creating an inquiry record

The processors support most built-in fields, as well as all custom fields.

In the case of error or failure, the plugin will still allow Caldera Forms to save the entries and
will then attempt to email the site administrator to warn them about the failure.

This plugin is on GitHub:

https://github.com/arcticres/arctic-caldera-plugin

== Installation ==

1. Install the plugin through the WordPress Plugins Installer or upload the files to a new 
   `arctic-caldera-forms` folder in the wp-content/plugins directory.
2. Upload a `arctic-auth.php` file into the plugin directory with your API credentials 
   (for security reasons, the API credentials can not be managed through WordPress 
   directly at this time).
3. Activate the plugin from the "Plugins" page.

== Usage ==

See the first screenshot below for an illustration of these steps:

1. Create or edit a Caldera form, and go to the "Processors" tab.
2. Click "Add Processor".
3. Select one of the two Arctic processors (one creates a person record, one creates an 
   inquiry record). Most likely, you will want to use both, to create both a person record 
   and a corresponding inquiry record. In this case, add the "Person" one first (otherwise 
   Arctic will not know who to associate the inquiry with).
4. Once added, you will see a list of the built-in and custom fields from your Arctic 
   installation. You will use the "magic tags" to embed specific entries from the form 
   into each corresponding Arctic field.

== Screenshots ==

1. **Instructions** - Shows how to add an Arctic processor to a form.
2. **Create Person Processor** - Takes form data and creates a person record.
3. **Create Inquiry Processor** - Takes form data and creates an inquiry record.

== Changelog ==
= 0.1.0 =
Beta version for testing purposes
