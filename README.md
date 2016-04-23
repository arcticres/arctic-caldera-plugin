Arctic-Caldera-Plugin
=====================

This is a WordPress plugin that extends [Caldera Forms](https://wordpress.org/plugins/caldera-forms/) with
two new form processors, which will push form data into [Arctic Reservations](http://www.arcticreservations.com/).
The form processors are built off the existing [Arctic API](https://github.com/arcticres/arctic-api) and supports
two processors currently:

* Creating person records - This includes basic person details, contact information and any customer custom fields.
* Creating inquiry records - This includes inquiry notes and any custom fields, as well as the ability to build a longer
  textual representation of other form data.

Currently, the plugin only accepts API information through the addition of a specific file with API credentials.
This may later be supplemented with a way to manage API credentials through the WordPress interface. Consult the
`arctic-auth-sample.php` file for information on adding authentication details to the plugin.

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
