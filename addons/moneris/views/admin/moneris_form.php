<div class="page-header">
    <h2><?php echo lang('paypal');?></h2>
</div>

<?php echo form_open_multipart('admin/moneris/form'); ?>
<div class="row">
<div class="col-md-6">
	<div class="form-group">
		<label><?php echo lang('enabled');?></label>
		<select name="enabled" class="form-control">
			<option value="1" <?php echo((bool)$enabled)?' selected="selected"':'';?>><?php echo lang('enabled');?></option>
			<option value="0" <?php echo((bool)$enabled)?'':' selected="selected"';?>><?php echo lang('disabled');?></option>
		</select>
	</div>

	<div class="form-group">
		<label><?php echo lang('mode');?></label>
		<select name="mode" class="form-control">
			<option value="test" <?php echo($mode=='test')?'selected="selected"':'';?>><?php echo lang('test_mode');?></option>
			<option value="production" <?php echo($mode!='production')?'':'selected="selected"';?>><?php echo lang('production');?></option>
		</select>
	</div>


	<div class="form-group">
		<label><?php echo lang('site_id') ?></label>
			<?php echo form_input(['name'=>'site_id', 'value'=>assign_value('site_id', $site_id), 'class'=>'form-control']);?>
	</div>


	<div class="form-group">
		<label><?php echo lang('api_key') ?></label>
			<?php echo form_input(['name'=>'api_key', 'value'=>assign_value('api_key', $api_key), 'class'=>'form-control']);?>
	</div>

	<div class="form-group">
		<label><?php echo lang('descriptor') ?></label>
			<?php echo form_input(['name'=>'descriptor', 'value'=>assign_value('descriptor', $descriptor), 'class'=>'form-control']);?>
	</div>


<div class="form-actions">
    <button type="submit" class="btn btn-primary"><?php echo lang('save');?></button>
</div>
</div>
</div>
</form>

<script type="text/javascript">
$('form').submit(function() {
    $('.btn .btn-primary').attr('disabled', true).addClass('disabled');
});
</script>