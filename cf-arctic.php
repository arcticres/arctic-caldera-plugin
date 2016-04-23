<?php
/**
 * Plugin Name: Caldera Forms - Arctic Reservations Integration
 * Plugin URI:
 * Description: Create person and inquiry records in Arctic Reservations on form submission.
 * Version:     1.0.0
 * Author:      Arctic Reservations LLC
 * Author URI:  http://www.arcticreservations.com/
 * License:     MIT
 * License URI: https://opensource.org/licenses/MIT
 */

/**
 * Add the processors
 *
 * @since 1.0.0
 */
add_filter('caldera_forms_get_form_processors', 'cf_arctic_register_processor');
function cf_arctic_register_processor($pr) {
	$pr['arctic-person'] = array(
		'name' => __('Arctic: Create Person', 'cf-arctic'),
		'description' => __('Create a person record in Arctic on submission.', 'cf-arctic'),
		'author' => 'Arctic Reservations LLC',
		'author_url' => 'http://www.arcticreservations.com/',
		'icon' => plugin_dir_url(__FILE__) . 'icon.png',
		'processor' => 'cf_arctic_create_person',
		'template' => plugin_dir_path(__FILE__) . 'config-person.php',
	);

	$pr['arctic-inquiry'] = array(
		'name' => __('Arctic: Create Inquiry', 'cf-arctic'),
		'description' => __('Create an inquiry record in Arctic on submission. Must be called after creating a person record.', 'cf-arctic'),
		'author' => 'Arctic Reservations LLC',
		'author_url' => 'http://www.arcticreservations.com/',
		'icon' => plugin_dir_url(__FILE__) . 'icon.png',
		'processor' => 'cf_arctic_create_inquiry',
		'template' => plugin_dir_path(__FILE__) . 'config-inquiry.php',
	);

	return $pr;
}

/**
 * Todo: potentially add a settings interface for configuring API (security implications?)
 */

/**
 * Private functions used for processing form submissions.
 */

function _cf_arctic_is_configured() {
	return file_exists(plugin_dir_path(__FILE__)  . 'arctic-auth.php');
}

function _cf_arctic_configure() {
	// already loaded?
	if (class_exists('\Arctic\Api', false)) return;

	// load the arctic API
	require plugin_dir_path(__FILE__) . 'arctic-api' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'Api.php';

	// load the configure file
	@include plugin_dir_path(__FILE__) . 'arctic-auth.php';
}

/**
 * @param string $form_name
 * @return \Arctic\Model\FormField[]
 */
function _cf_arctic_custom_fields($form_name) {
	// not configured? return array
	if (!_cf_arctic_is_configured()) {
		return array();
	}

	// configure
	_cf_arctic_configure();

	// get form fields
	try {
		return \Arctic\Model\FormField::query('formname = \'' . addslashes($form_name) . '\' AND builtin = FALSE ORDER BY order ASC');
	}
	catch (\Arctic\Exception $e) {
		return array();
	}
}

function _cf_arctic_is_true($value, $default=false) {
	// numeric
	if (is_numeric($value)) {
		return $value > 0;
	}

	// string
	if (is_string($value)) {
		if (preg_match('/true|yes/i', $value)) {
			return true;
		}

		if (preg_match('/false|no/i', $value)) {
			return false;
		}

		return $default;
	}

	// hard convert
	return (bool)$value;
}

function _cf_arctic_get_countryid($value) {
	return null;
}

function _cf_arctic_inserted($key, $id=null) {
	static $inserted = array();

	//read
	if (null === $id) {
		if (isset($inserted[$key])) {
			return $inserted[$key];
		}
		return false;
	}

	// write
	$inserted[$key] = $id;
	return $id;
}

function _cf_arctic_fail($model, $config, $form, $error) {
	// accept exceptions
	$exception = null;
	if ($error instanceof \Exception) {
		$exception = $error;
		$error = $exception->getMessage();
	}

	// potentially show user error?
	return array(
		'type' => 'error',
		'note' => $error
	);
}

/**
 * Create a person record in Arctic
 *
 * @since 1.0.0
 *
 * @param array $config Processor config
 * @param array $form Form config
 * @return array|null
 */
function cf_arctic_create_person($config, $form) {
	if (!_cf_arctic_is_configured()) {
		return _cf_arctic_fail(null, $config, $form, 'Arctic plugin is not configured.');
	}

	_cf_arctic_configure();

	// create person
	$person = new \Arctic\Model\Person\Person();

	// iterate over fields
	foreach ($config as $key => $value) {
		if (!is_string($key) || strlen($key) < 3) continue;
		switch (substr($key, 0, 2)) {
			case 'f_': // built-in field
			case 'c_': // custom field
				// strip prefix
				$obj_key = substr($key, 2);

				// protected
				if ('_' === $obj_key[0] || in_array($obj_key, array('id', 'emailaddresses', 'addresses', 'phonenumbers', 'notes'))) {
					break;
				}

				// process value
				$value = Caldera_Forms::do_magic_tags($value);
				if (empty($value)) break;

				// store value
				$person->$obj_key = $value;
		}
	}

	// handle relationship fields

	// 1. email address
	if (isset($config['r_emailaddress']) && $value = Caldera_Forms::do_magic_tags($config['r_emailaddress'])) {
		$email = new \Arctic\Model\Person\EmailAddress();
		$email->emailaddress = $value;
		if (isset($config['r_emaillabel']) && $value = Caldera_Forms::do_magic_tags($config['r_emaillabel'])) {
			$email->type = $value;
		}
		else {
			$email->type = 'Primary';
		}
		if (isset($config['r_emailsubscribe'])) {
			$email->subscribetoemaillist = _cf_arctic_is_true($config['r_emailsubscribe']);
		}
		$person->emailaddresses[] = $email;
	}

	// 2. phone number
	if (isset($config['r_phonenumber']) && $value = Caldera_Forms::do_magic_tags($config['r_phonenumber'])) {
		$phone = new \Arctic\Model\Person\PhoneNumber();
		$phone->phonenumber = $value;
		if (isset($config['r_phonecountry']) && $value = Caldera_Forms::do_magic_tags($config['r_phonecountry'])) {
			if ($id = _cf_arctic_get_countryid($value)) {
				$phone->countryid = $id;
			}
		}
		if (isset($config['r_phonelabel']) && $value = Caldera_Forms::do_magic_tags($config['r_phonelabel'])) {
			$phone->type = $value;
		}
		else {
			$phone->type = 'Primary';
		}
		$person->phonenumbers[] = $phone;
	}

	// 3. address
	if (isset($config['r_address1']) || isset($config['r_address2']) || isset($config['r_addresscity']) || isset($config['r_addressstate']) || isset($config['r_addresspostalcode']) || isset($config['r_addresscountry'])) {
		$address = new \Arctic\Model\Person\Address();
		$save = false;
		if (isset($config['r_address1']) && $value = Caldera_Forms::do_magic_tags($config['r_address1'])) {
			$address->address1 = $value;
			$save = true;
		}
		if (isset($config['r_address2']) && $value = Caldera_Forms::do_magic_tags($config['r_address2'])) {
			$address->address2 = $value;
			$save = true;
		}
		if (isset($config['r_addresscity']) && $value = Caldera_Forms::do_magic_tags($config['r_addresscity'])) {
			$address->city = $value;
			$save = true;
		}
		if (isset($config['r_addressstate']) && $value = Caldera_Forms::do_magic_tags($config['r_addressstate'])) {
			$address->state = $value;
			$save = true;
		}
		if (isset($config['r_addresspostalcode']) && $value = Caldera_Forms::do_magic_tags($config['r_addresspostalcode'])) {
			$address->postalcode = $value;
			$save = true;
		}
		if (isset($config['r_addresscountry']) && $value = Caldera_Forms::do_magic_tags($config['r_addresscountry'])) {
			if ($id = _cf_arctic_get_countryid($value)) {
				$address->countryid = $id;
			}
		}
		if (isset($config['r_addresslabel']) && $value = Caldera_Forms::do_magic_tags($config['r_addresslabel'])) {
			$address->type = $value;
		}
		else {
			$address->type = 'Primary';
		}
		if ($save) {
			$person->addresses[] = $address;
		}
	}

	// 4. note
	if (isset($config['r_note']) && $value = Caldera_Forms::do_magic_tags($config['r_note'])) {
		$note = new \Arctic\Model\Person\Note();
		$note->note = $value;
		$person->notes[] = $note;
	}

	// RUN
	try {
		// save
		$person->insert();
		_cf_arctic_inserted('person', $person->id);
	}
	catch (\Arctic\Exception $e) {
		return _cf_arctic_fail($person, $config, $form, $e);
	}
}

/**
 * Create an inquiry record in Arctic
 *
 * @since 1.0.0
 *
 * @param array $config Processor config
 * @param array $form Form config
 * @return array|null
 */
function cf_arctic_create_inquiry($config, $form) {
	if (!_cf_arctic_is_configured()) {
		return _cf_arctic_fail(null, $config, $form, 'Arctic plugin is not configured.');
	}

	_cf_arctic_configure();

	// create person
	$inquiry = new \Arctic\Model\Inquiry\Inquiry();

	// get person
	if ($person_id = _cf_arctic_inserted('person')) {
		$inquiry->personid;
	}
	else {
		return _cf_arctic_fail($inquiry, $config, $form, 'No previously saved person information.');
	}

	// iterate over fields
	foreach ($config as $key => $value) {
		if (!is_string($key) || strlen($key) < 3) continue;
		switch (substr($key, 0, 2)) {
			case 'f_': // built-in field
			case 'c_': // custom field
				// strip prefix
				$obj_key = substr($key, 2);

				// protected
				if ('_' === $obj_key[0] || in_array($obj_key, array('id', 'businessgroup', 'trip', 'person'))) {
					break;
				}

				// process value
				$value = Caldera_Forms::do_magic_tags($value);
				if (empty($value)) break;

				// store value
				$inquiry->$obj_key = $value;
		}
	}

	// special
	
	// 1. mode
	if (empty($inquiry->mode)) {
		$inquiry->mode = 'CalderaForm' . (isset($form['name']) ? ': ' . $form['name'] : '');
	}
	
	// 2. save other fields
	if (isset($config['save_other']) && $config['save_other']) {
		// get used slugs
		if (preg_match_all('/%([^%:]+)(|:[^%]*)%/', implode('', $config), $matches, PREG_PATTERN_ORDER)) {
			$slugs = $matches[1];
		}
		else {
			$slugs = array();
		}

		// iterate over all fields
		$other = array();
		foreach ($form['fields'] as $field) {
			// skip
			if ($field['type'] === 'html' || $field['type'] === 'button') {
				continue;
			}

			// included elsewhere?
			if (isset($field['slug']) && in_array($field['slug'], $slugs)) {
				continue;
			}

			// get value
			$entry_value = Caldera_Forms::get_field_data($field['ID'], $form);
			$other[] = sprintf('%s: %s', $field['label'], $entry_value);
		}

		// had other?
		if ($other) {
			if ($inquiry->notes) {
				$inquiry->notes = $inquiry->notes . "\n\n" . implode("\n", $other);
			}
			else {
				$inquiry->notes = implode("\n", $other);
			}
		}
	}

	// RUN
	try {
		// save
		$inquiry->insert();
		_cf_arctic_inserted('inquiry', $inquiry->id);
	}
	catch (\Arctic\Exception $e) {
		return _cf_arctic_fail($inquiry, $config, $form, $e);
	}
}

// TODO: potentially add arctic country field, trip type field, rental item field
