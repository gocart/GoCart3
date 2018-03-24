<div class="page-header">
    <h2><?php echo lang('paypal_pro');?></h2>
</div>

<?php echo form_open_multipart('admin/paypal_pro/form'); ?>
<div class="row">
<div class="col-md-6">
    <div class="form-group">
        <label for="enabled"><?php echo lang('enabled');?> </label>
        <?php echo form_dropdown('enabled', array('0' => lang('disabled'), '1' => lang('enabled')), assign_value('enabled',$enabled), 'class="form-control"'); ?>
    </div>

    <div class="form-group">
        <label for="mode"><?php echo lang('paypal_mode');?></label>
        <?php echo form_dropdown('testMode', array(1 => lang('test'), 0 => lang('live')), assign_value('testMode',$testMode), 'class="form-control"'); ?>
    </div>

    <div class="form-group">
        <label for="paypal-key"><?php echo lang('paypal_username');?></label>
        <?php echo form_input(['name'=>'paypal_username', 'value'=>assign_value('paypal_username', $paypal_username), 'class'=>'form-control']);?>
    </div>

    <div class="form-group">
        <label for="paypal_password"><?php echo lang('paypal_password');?></label>
        <?php echo form_input(['name'=>'paypal_password', 'value'=>assign_value('paypal_password', $paypal_password), 'class'=>'form-control']);?>
    </div>

    <div class="form-group">
        <label for="paypal_signature"><?php echo lang('paypal_signature');?></label>
        <?php echo form_input(['name'=>'paypal_signature', 'value'=>assign_value('paypal_signature', $paypal_signature), 'class'=>'form-control']);?>
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