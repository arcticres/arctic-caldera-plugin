<?php if (!Cf_Arctic::is_configured()) { ?>
	<p><strong style="color:red;">Not configured.</strong> Before you can use the Arctic / Caldera integration, you must
		include a <code>arctic-auth.php</code> file in the plugin directory with API credentials.</p>
<?php } ?>

<p>Below you can map your form fields into the Arctic fields. Use the Caldera Form magic tags to reference form data.
   Leave any unneeded fields blank.</p>

<p><strong style="color:orange;">IMPORTANT:</strong> The "Create Person" processor must be run before the "Create Inquiry"
   processor, so that there is a person to associate the inquiry with.</p>

<h4>Built-In Fields</h4>

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

<?php foreach (Cf_Arctic::get_custom_fields('custominquiry') as $custom_field) { ?>
	<div class="caldera-config-group">
		<label><?php echo isset($custom_field->data['setLabel']) ? $custom_field->data['setLabel'] : $custom_field->name; ?> </label>
		<div class="caldera-config-field">
			<input type="text" class="block-input field-config magic-tag-enabled <?php if (isset($custom_field->data['setRequired']) && $custom_field->data['setRequired']) echo 'required'; ?>" name="{{_name}}[c_<?= $custom_field->name ?>]" value="{{c_<?= $custom_field->name ?>}}">
		</div>
	</div>
<?php } ?>
