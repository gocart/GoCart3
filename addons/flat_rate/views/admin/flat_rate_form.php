<div class="page-header">
    <h2><?php echo lang('flat_rate');?></h2>
</div>

<div class="row">
<?php echo form_open_multipart('admin/flat-rate/form'); ?>
<div class="col-md-6">
<div class="form-group">
    <label for="enabled"><?php echo lang('enabled');?> </label>
    <?php echo form_dropdown('enabled', array('0' => lang('disabled'), '1' => lang('enabled')), assign_value('enabled',$enabled), 'class="form-control"'); ?>
</div>
<div class="form-group">
    <label for="enabled"><?php echo lang('rate');?> </label>
    <?php echo form_input(['name'=>'rate', 'value'=>assign_value('rate', $rate), 'class' => 'form-control' ]); ?>
</div>
    <div class="form-actions">
        <button type="submit" class="btn btn-primary"><?php echo lang('save');?></button>
    </div>
</div>   
</form>
</div> 
<script type="text/javascript">
$('form').submit(function() {
    $('.btn .btn-primary').attr('disabled', true).addClass('disabled');
});
</script>