<div class="page-header">
    <h2><?php echo lang('stripe');?></h2>
</div>

<?php echo form_open_multipart('admin/stripe/form'); ?>
<div class="row">
<div class="col-md-6">
    <div class="form-group">
        <label for="enabled"><?php echo lang('enabled');?> </label>
        <?php echo form_dropdown('enabled', array('0' => lang('disabled'), '1' => lang('enabled')), assign_value('enabled',$enabled), 'class="form-control"'); ?>
    </div>

    <div class="form-group">
        <label for="mode"><?php echo lang('mode');?></label>
        <?php echo form_dropdown('mode', array('test' => lang('test'), 'live' => lang('live')), assign_value('mode',$mode), 'class="form-control"'); ?>
    </div>

    <div class="form-group">
        <label for="test-secret-key"><?php echo lang('test_secret_key');?></label>
        <?php echo form_input(['name'=>'test_secret_key', 'value'=>assign_value('test_secret_key', $test_secret_key), 'class'=>'form-control']);?>
    </div>

    <div class="form-group">
        <label for="test-publishable-key"><?php echo lang('test_publishable_key');?></label>
        <?php echo form_input(['name'=>'test_publishable_key', 'value'=>assign_value('test_publishable_key', $test_publishable_key), 'class'=>'form-control']);?>
    </div>

    <div class="form-group">
        <label for="live-secret-key"><?php echo lang('live_secret_key');?></label>
        <?php echo form_input(['name'=>'live_secret_key', 'value'=>assign_value('live_secret_key', $live_secret_key), 'class'=>'form-control']);?>
    </div>

    <div class="form-group">
        <label for="live-publishable-key"><?php echo lang('live_publishable_key');?></label>
        <?php echo form_input(['name'=>'live_publishable_key', 'value'=>assign_value('live_publishable_key', $live_publishable_key), 'class'=>'form-control']);?>
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