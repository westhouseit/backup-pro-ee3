<input type="hidden" value="0" name="gcs_reduced_redundancy" />
<fieldset class="col-group <?php echo (is_array($form_errors['gcs_access_key']) ? 'invalid' : 'security-enhance'); ?>">
	<div class="setting-txt col w-8">
		<h3><label for="gcs_access_key"><?php echo $view_helper->m62Lang('gcs_access_key'); ?></label></h3>
		<em><?php echo $view_helper->m62Lang('gcs_access_key_instructions'); ?></em>
	</div>
	<div class="setting-field col w-8">
		<?php echo form_input('gcs_access_key', $form_data['gcs_access_key'], 'id="gcs_access_key"'); ?>
		<?php echo $view_helper->m62FormErrors($form_errors['gcs_access_key']); ?>
	</div>
</fieldset>

<fieldset class="col-group <?php echo (is_array($form_errors['gcs_secret_key']) ? 'invalid' : 'security-enhance'); ?>">
	<div class="setting-txt col w-8">
		<h3><label for="gcs_secret_key"><?php echo $view_helper->m62Lang('gcs_secret_key'); ?></label></h3>
		<em><?php echo $view_helper->m62Lang('gcs_secret_key_instructions'); ?></em>
	</div>
	<div class="setting-field col w-8">
		<?php echo form_password('gcs_secret_key', $form_data['gcs_secret_key'], 'id="gcs_secret_key"'); ?>
		<?php echo $view_helper->m62FormErrors($form_errors['gcs_secret_key']); ?>
	</div>
</fieldset>

<fieldset class="col-group required <?php echo (is_array($form_errors['gcs_bucket']) ? 'invalid' : ''); ?>">
	<div class="setting-txt col w-8">
		<h3><label for="gcs_bucket"><?php echo $view_helper->m62Lang('gcs_bucket'); ?></label></h3>
		<em><?php echo $view_helper->m62Lang('gcs_bucket_instructions'); ?></em>
	</div>
	<div class="setting-field col w-8">
		<?php echo form_input('gcs_bucket', $form_data['gcs_bucket'], 'id="gcs_bucket"'); ?>
		<?php echo $view_helper->m62FormErrors($form_errors['gcs_bucket']); ?>
	</div>
</fieldset>

<fieldset class="col-group <?php echo (is_array($form_errors['gcs_optional_prefix']) ? 'invalid' : ''); ?>">
	<div class="setting-txt col w-8">
		<h3><label for="gcs_optional_prefix"><?php echo $view_helper->m62Lang('gcs_optional_prefix'); ?></label></h3>
		<em><?php echo $view_helper->m62Lang('gcs_optional_prefix_instructions'); ?></em>
	</div>
	<div class="setting-field col w-8">
		<?php echo form_input('gcs_optional_prefix', $form_data['gcs_optional_prefix'], 'id="gcs_optional_prefix"'); ?>
		<?php echo $view_helper->m62FormErrors($form_errors['gcs_optional_prefix']); ?>
	</div>
</fieldset>

<fieldset class="col-group <?php echo (is_array($form_errors['gcs_reduced_redundancy']) ? 'invalid' : ''); ?>">
	<div class="setting-txt col w-8">
		<h3><label for="gcs_reduced_redundancy"><?php echo $view_helper->m62Lang('gcs_reduced_redundancy'); ?></label></h3>
		<em><?php echo $view_helper->m62Lang('gcs_reduced_redundancy_instructions'); ?></em>
	</div>
	<div class="setting-field col w-8">
		<label class="choice mr <?php echo ($form_data['gcs_reduced_redundancy'] == '1' ? 'chosen' : ''); ?>">
		  <?php echo form_checkbox('gcs_reduced_redundancy', '1', $form_data['gcs_reduced_redundancy'], 'id="gcs_reduced_redundancy"'); ?>
		</label>
		<?php echo $view_helper->m62FormErrors($form_errors['gcs_reduced_redundancy']); ?>
	</div>
</fieldset>