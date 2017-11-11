<?php if (!Cf_Arctic::is_configured()) { ?> 
	<p><strong style="color:red;">Not configured.</strong> Before you can use the Arctic / Caldera integration, you must
		include a <code>arctic-auth.php</code> file in the plugin directory with API credentials.</p>
<?php } ?>

<p>Below you can map your form fields into the Arctic fields. Use the Caldera Form magic tags to reference form data.
	Leave any unneeded fields blank, except for first and last name which are required.</p>

<h4>Built-In Fields</h4>

<div class="caldera-config-group">
	<label><?php echo __('First Name', 'cf-arctic'); ?> </label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config required magic-tag-enabled" name="{{_name}}[f_namefirst]" value="{{f_namefirst}}">
	</div>
</div>

<div class="caldera-config-group">
	<label><?php echo __('Last Name', 'cf-arctic'); ?> </label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config required magic-tag-enabled" name="{{_name}}[f_namelast]" value="{{f_namelast}}">
	</div>
</div>

<div class="caldera-config-group">
	<label><?php echo __('Company', 'cf-arctic'); ?> </label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config magic-tag-enabled" name="{{_name}}[f_namecompany]" value="{{f_namecompany}}">
	</div>
</div>

<div class="caldera-config-group">
	<label><?php echo __('Referral Source', 'cf-arctic'); ?> </label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config magic-tag-enabled" name="{{_name}}[f_customersource]" value="{{f_customersource}}">
	</div>
</div>

<div class="caldera-config-group">
	<label><?php echo __('Note', 'cf-arctic'); ?> </label>
	<div class="caldera-config-field">
		<textarea class="block-input field-config magic-tag-enabled" name="{{_name}}[r_note]">{{r_note}}</textarea>
	</div>
</div>

<h4>Contact: Email</h4>

<div class="caldera-config-group">
	<label><?php echo __('Email Address', 'cf-arctic'); ?> </label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config magic-tag-enabled" name="{{_name}}[r_emailaddress]" value="{{r_emailaddress}}">
	</div>
</div>

<div class="caldera-config-group">
	<label><?php echo __('Subscribe to Email List', 'cf-arctic'); ?> </label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config magic-tag-enabled" name="{{_name}}[r_emailsubscribe]" value="{{r_emailsubscribe}}">
		<p>Should include "yes" or "true" or "1" if customer wants to subscribe. Should include "no" or "false" or "0" if customer does not want to subscribe.</p>
	</div>
</div>

<!-- potentially add label -->

<h4>Contact: Phone Number</h4>

<div class="caldera-config-group">
	<label><?php echo __('Phone Number', 'cf-arctic'); ?> </label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config magic-tag-enabled" name="{{_name}}[r_phonenumber]" value="{{r_phonenumber}}">
	</div>
</div>

<!-- potentially add label and country -->

<h4>Contact: Address</h4>

<div class="caldera-config-group">
	<label><?php echo __('Address 1', 'cf-arctic'); ?> </label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config magic-tag-enabled" name="{{_name}}[r_address1]" value="{{r_address1}}">
	</div>
</div>

<div class="caldera-config-group">
	<label><?php echo __('Address 2', 'cf-arctic'); ?> </label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config magic-tag-enabled" name="{{_name}}[r_address2]" value="{{r_address2}}">
	</div>
</div>

<div class="caldera-config-group">
	<label><?php echo __('City', 'cf-arctic'); ?> </label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config magic-tag-enabled" name="{{_name}}[r_addresscity]" value="{{r_addresscity}}">
	</div>
</div>

<div class="caldera-config-group">
	<label><?php echo __('State', 'cf-arctic'); ?> </label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config magic-tag-enabled" name="{{_name}}[r_addressstate]" value="{{r_addressstate}}">
	</div>
</div>

<div class="caldera-config-group">
	<label><?php echo __('Postal Code', 'cf-arctic'); ?> </label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config magic-tag-enabled" name="{{_name}}[r_addresspostalcode]" value="{{r_addresspostalcode}}">
	</div>
</div>

<?php /* <div class="caldera-config-group">
	<label><?php echo __('Country', 'cf-arctic'); ?> </label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config magic-tag-enabled" name="{{_name}}[r_addresscountry]" value="{{r_addresscountry}}">
	</div>
</div> */ ?>

<!-- potentially add label -->

<h4>Custom Fields</h4>

<?php foreach (Cf_Arctic::get_custom_fields('typecustomer') as $custom_field) { ?>
	<div class="caldera-config-group">
		<label><?php echo isset($custom_field->data['setLabel']) ? $custom_field->data['setLabel'] : $custom_field->name; ?> </label>
		<div class="caldera-config-field">
			<input type="text" class="block-input field-config magic-tag-enabled <?php if (isset($custom_field->data['setRequired']) && $custom_field->data['setRequired']) echo 'required'; ?>" name="{{_name}}[c_<?= $custom_field->name ?>]" value="{{c_<?= $custom_field->name ?>}}">
		</div>
	</div>
<?php } ?>

<h4>Settings</h4>

<div class="caldera-config-group">
    <label><?php echo __('Attempt Update', 'cf-slack'); ?> </label>
    <div class="caldera-config-field">
        <label><input type="checkbox" class="field-config" name="{{_name}}[attempt_update]" id="{{_id}}_attempt_update" value="1" {{#if attempt_update}}checked="checked"{{/if}}> Update existing customer if potential duplicate</label>
        <p class="description"><?php __('Recommended. By default, always Arctic creates a new customer record. If checked, Arctic will look for a close match (based on name, email address and phone number), and update the existing entry instead.', 'cf-arctic'); ?></p>
    </div>
</div>
