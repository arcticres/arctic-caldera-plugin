Arctic-Caldera-Plugin
=====================

This is a WordPress plugin that extends [Caldera Forms](https://wordpress.org/plugins/caldera-forms/) with
two new form processors, which will push form data into [Arctic Reservations](http://www.arcticreservations.com/).
The form processors are built off the existing [Arctic API](https://github.com/arcticres/arctic-api) and supports
two processors currently:

* Creating person records - This includes basic person details, contact information and any customer custom fields.
* Creating inquiry records - This includes inquiry notes and any custom fields, as well as the ability to build a longer
  textual representation of other form data.

Currently, the plugin only accepts API information through the addition of a specific file 
with API credentials. This may later be supplemented with a way to manage API credentials 
through the WordPress interface. Consult the `arctic-auth-sample.php` file for 
information on adding authentication details to the plugin.

Installation
------------

This installation assumes you have a WordPress installation and have already 
installed the [Caldera Forms](https://wordpress.org/plugins/caldera-forms/) plugin. 
To install this plugin, enabling the forms to integrate with Arctic Reservations, follow 
the steps below:

1. Download the files from this repository.
2. Upload all files to a new "arctic-caldera-forms" folder in the "wp-content/plugins" 
   directory.
2. Create and upload a "arctic-auth.php" file with API credentials. Use 
   "arctic-auth-sample.php" as an example, or consult with Arctic Reservations support 
   for assistance. (At this time, API credentials can not be managed through the 
   WordPress interface.)
3. Activate the plugin from the "Plugins" page.


Usage
-----

Once installed, you can setup forms to push data into Arctic Reservations:

1. Create or edit a Caldera form, and go to the "Processors" tab.
2. Click "Add Processor".
3. Select one of the two Arctic processors (one creates a person record, one creates an 
   inquiry record). Most likely, you will want to use both to create both a person record 
   and a corresponding inquiry record. In this case, add the "Person" one first (otherwise 
   Arctic will not know who to associate the inquiry with).
4. Once added, you will see a list of the built-in and custom fields from your Arctic 
   installation. You will use the "magic tags" to embed specific entries from the form 
   into each corresponding Arctic field.

![Usage](https://github.com/arcticres/arctic-caldera-plugin/blob/master/screenshot-1.png)


Version
-------

Version 0.1.0

An initial beta release.

Limitations
-----------

There are a few known limitations and potential future features:

* Currently, country fields are not supported yet. All addresses are made in the default country.
* Full state names are not yet supported.
* Some built-in fields are missing (gender, birth date, etc).
* Some custom fields may not be correctly rendered.

If you find other limitations, bugs or issues, please open an issue.

### Authors

**L. Nathan Perkins**

- <https://github.com/nathanntg>
- <https://www.nathanntg.com/>
