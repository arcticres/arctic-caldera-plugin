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

function cf_arctic_is_configured() {
	return file_exists(plugin_dir_path(__FILE__)  . 'arctic-auth.php');
}

/**
 * Add the processors
 *
 * @since 1.0.0
 */
add_filter('caldera_forms_get_form_processors', 'cf_arctic_register_processor');
function cf_arctic_register_processor($pr) {
	$pr['arctic-person'] = array(
		'name' => __('Arctic: Create Person', 'cf-arctic'),
		'description' => __('Create a person record in Arctic on submission', 'cf-arctic'),
		'author' => 'Arctic Reservations LLC',
		'author_url' => 'http://www.arcticreservations.com/',
		'icon' => plugin_dir_url(__FILE__) . 'icon.png',
		'processor' => 'cf_arctic_create_person',
		'template' => plugin_dir_path(__FILE__) . 'config-person.php',
	);

	$pr['arctic-inquiry'] = array(
		'name' => __('Arctic: Create Inquiry', 'cf-arctic'),
		'description' => __('Create a person record in Arctic on submission', 'cf-arctic'),
		'author' => 'Arctic Reservations LLC',
		'author_url' => 'http://www.arcticreservations.com/',
		'icon' => plugin_dir_url(__FILE__) . 'icon.png',
		'processor' => 'cf_arctic_create_inquiry',
		'template' => plugin_dir_path(__FILE__) . 'config-inquiry.php',
	);

	return $pr;
}

///**
// * Add the settings
// *
// * @since 1.0.0
// */
//add_action('admin_init', 'cf_arctic_settings_init');
//function cf_arctic_settings_init() {
//	register_setting('cf-arctic', 'my-setting');
//	add_settings_section('section-one', 'Section One', 'section_one_callback', 'my-plugin');
//	add_settings_field('field-one', 'Field One', 'field_one_callback', 'my-plugin', 'section-one');
//}
//
//add_action('admin_menu', 'cf_arctic_settings_menu');
//function cf_arctic_settings_menu() {
//	add_options_page('Caldera Forms - Arctic Reservations Integration', 'Caldera - Arctic', 'manage_options', 'cf-arctic', 'cf_arctic_settings_page');
//}
//
//function cf_arctic_settings_page() {
//	echo 'a';
//}

/**
 * Create a person record in Arctic
 *
 * @since 1.0.0
 *
 * @param array $config Processor config
 * @param array $form Form config
 */
function cf_arctic_create_person($config, $form){

	// create fields
	$fields = array();
	foreach( $form['fields'] as $field ){
		if( $field['type'] === 'html' || $field['type'] === 'button' ){
			continue;
		}
		$entry_value = Caldera_Forms::get_field_data( $field['ID'], $form );
		$fields[] = array(
			'title'		=>	$field['label'],
			'value'		=>	$entry_value,
			'short'		=>	( strlen( $entry_value ) < 100 ? true : false )
		);
	}
	// Create Payload
	$payload = array(
		"username"		=>	 Caldera_Forms::do_magic_tags( $config['username'] )
	);
	// icon
	if( !empty( $config['file'] ) ){
		$payload['icon_url'] =	$config['file'];
	}

	// override channel if set
	$channel = trim( $config['channel'] );
	if( !empty( $channel ) ){
		$payload['channel'] = Caldera_Forms::do_magic_tags( trim( $config['channel'] ) );
	}
	// attach if setup
	if( !empty( $config['attach'] ) ){
		$payload['attachments'] = array(
			array(
				"fallback" 	=>	Caldera_Forms::do_magic_tags( $config['text'] ),
				"pretext"	=>	Caldera_Forms::do_magic_tags( $config['text'] ),
				"color"		=>	$config['color'],
				"fields"	=>	$fields
			)
		);
	}else{
		$payload['text'] = Caldera_Forms::do_magic_tags( $config['text'] );
	}

	/**
	 * Filter request before sending message to Slack API
	 *
	 * Runs before encoding to JSON
	 *
	 * @since 1.1.0
	 *
	 * @param array $payload Arguments for API request
	 * @param array $config Processor config
	 * @param array $form Form config
	 */
	add_filter( 'cf_arctic_create_person_pre_request', $payload, $config, $form );

	$args = array(
		'body' => array(
			'payload'	=>	json_encode($payload)
		)
	);

	$response = wp_remote_post( $config['url'], $args );
	/**
	 * Get response from Slack API message request.
	 *
	 * Runs after request is sent, but before form processor ends
	 *
	 * @since 1.1.0
	 *
	 * @param WP_Error|array $response The response or WP_Error on failure.
	 * @param array $payload Arguments for API request
	 * @param array $config Processor config
	 * @param array $form Form config
	 */
	do_action( 'cf_arctic_create_person_sent', $response, $payload, $config, $form );

}
