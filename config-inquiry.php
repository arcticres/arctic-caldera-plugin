<p>Below you can map your form fields into the Arctic fields. Use the Caldera Form magic tags to reference form data.
   Leave any unneeded fields blank.</p>

<p><strong style="color:red;">IMPORTANT:</strong> The "Create Person" processor must be run before the "Create Inquiry"
   processor, so that there is a person to associate the inquiry with.</p>

<h4>Built-In Fields</h4>

<div class="caldera-config-group">
	<label><?php echo __('Mode', 'cf-arctic'); ?> </label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config magic-tag-enabled" name="{{_name}}[f_mode]" value="{{f_mode}}">
		<p>Leave blank to use form name.</p>
	</div>
</div>

<div class="caldera-config-group">
	<label><?php echo __('Notes', 'cf-arctic'); ?> </label>
	<div class="caldera-config-field">
		<textarea class="block-input field-config magic-tag-enabled" name="{{_name}}[f_notes]">{{f_notes}}</textarea>
	</div>
</div>

<div class="caldera-config-group">
	<label><?php echo __('Save Other Fields', 'cf-slack'); ?> </label>
	<div class="caldera-config-field">
		<label><input type="checkbox" class="field-config" name="{{_name}}[save_other]" id="{{_id}}_save_other" value="1" {{#if save_other}}checked="checked"{{/if}}> Save all other fields in notes automatically</label>
	</div>
</div>

<h4>Custom Fields</h4>

<?php foreach (_cf_arctic_custom_fields('custominquiry') as $custom_field) { ?>
	<div class="caldera-config-group">
		<label><?php echo isset($custom_field->data['setLabel']) ? $custom_field->data['setLabel'] : $custom_field->name; ?> </label>
		<div class="caldera-config-field">
			<input type="text" class="block-input field-config magic-tag-enabled <?php if (isset($custom_field->data['setRequired']) && $custom_field->data['setRequired']) echo 'required'; ?>" name="{{_name}}[c_<?= $custom_field->name ?>]" value="{{c_<?= $custom_field->name ?>}}">
		</div>
	</div>
<?php } ?>
