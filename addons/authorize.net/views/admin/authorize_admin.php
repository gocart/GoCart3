<div class="page-header">
    <h2><?php echo lang('authorize');?></h2>
</div>

<?php echo form_open_multipart('admin/authorize/form'); ?>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="enabled"><?php echo lang('enabled');?> </label>
                <?php echo form_dropdown('enabled', array('0' => lang('disabled'), '1' => lang('enabled')), assign_value('enabled',$enabled), 'class="form-control"'); ?>
            </div>
            
            <div class="form-group">
                <label for="developerMode"><?php echo lang('developerMode');?></label>
                <?php echo form_dropdown('developerMode', array(1 => lang('yes'), 0 => lang('no')), assign_value('developerMode',$developerMode), 'class="form-control"'); ?>
            </div>

            <div class="form-group">
                <label for="demo"><?php echo lang('testMode');?></label>
                <?php echo form_dropdown('testMode', array(1 => lang('yes'), 0 => lang('no')), assign_value('testMode',$testMode), 'class="form-control"'); ?>
            </div>

            <div class="form-group">
                <label for="apiLoginId"><?php echo lang('apiLoginId');?></label>
                <?php echo form_input(['name'=>'apiLoginId', 'value'=>assign_value('apiLoginId', $apiLoginId), 'class'=>'form-control']);?>
            </div>

            <div class="form-group">
                <label for="transactionKey"><?php echo lang('transactionKey');?></label>
                <?php echo form_input(['name'=>'transactionKey', 'value'=>assign_value('transactionKey', $transactionKey), 'class'=>'form-control']);?>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><?php echo lang('save');?></button>
            </div>
        </div>
    </div>  
</form>

<script>
$('form').submit(function() {
    $('.btn .btn-primary').attr('disabled', true).addClass('disabled');
});
</script>
