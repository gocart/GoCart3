<div class="page-header">
    <h2><?php echo lang('twocheckout');?></h2>
</div>
<?php echo form_open_multipart('admin/twocheckoutapi/form'); ?>
<div class="row">
<div class="col-md-6">
    <div class="form-group">
        <label for="enabled"><?php echo lang('enabled');?> </label>
        <?php echo form_dropdown('enabled', array('0' => lang('disabled'), '1' => lang('enabled')), assign_value('enabled',$enabled), 'class="form-control"'); ?>
    </div>

    <div class="form-group">
        <label for="demo"><?php echo lang('mode');?></label>
        <?php echo form_dropdown('demo', array('YES' => lang('test'), 'live' => lang('live')), assign_value('mode',$demo), 'class="form-control"'); ?>
    </div>

    <div class="form-group">
        <label for="currency"><?php echo lang('currency_label');?></label>
        <?php echo form_input(['name'=>'currency', 'value'=>assign_value('currency', $currency), 'class'=>'form-control']);?>
    </div>

    <div class="form-group">
        <label for="sid"><?php echo lang('sid');?></label>
        <?php echo form_input(['name'=>'sid', 'value'=>assign_value('sid', $sid), 'class'=>'form-control']);?>
    </div>

    <div class="form-group">
        <label for="public"><?php echo lang('public');?></label>
        <?php echo form_input(['name'=>'public', 'value'=>assign_value('public', $public), 'class'=>'form-control']);?>
    </div>

    <div class="form-group">
        <label for="private"><?php echo lang('private');?></label>
        <?php echo form_input(['name'=>'private', 'value'=>assign_value('private', $private), 'class'=>'form-control']);?>
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
