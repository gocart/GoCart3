<div class="page-header">
    <h2><?php echo lang('beanstream');?></h2>
</div>
<?php echo form_open_multipart('admin/beanstream/form'); ?>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="enabled"><?php echo lang('enabled');?> </label>
                <?php echo form_dropdown('enabled', array(0 => lang('disabled'), 1 => lang('enabled')), assign_value('enabled',$enabled), 'class="form-control"'); ?>
            </div>

            <div class="form-group">
                <label for="api_passcode"><?php echo lang('api_passcode');?></label>
                <?php echo form_input(['name'=>'api_passcode', 'value'=>assign_value('api_passcode', $api_passcode), 'class'=>'form-control']);?>
            </div>

            <div class="form-group">
                <label for="accountid"><?php echo lang('merchant_id');?></label>
                <?php echo form_input(['name'=>'merchant_id', 'value'=>assign_value('merchant_id', $merchant_id), 'class'=>'form-control']);?>
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
