<?php pageHeader(lang('country_form')) ?>

<?php echo form_open('admin/locations/country_form/'.$id); ?>

    <div class="form-group">
        <label for="name"><?php echo lang('name');?></label>
        <?php echo form_input(['name'=>'name', 'value'=>assign_value('name', $name), 'class'=>'form-control']);?>
    </div>  
    
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="iso_code_2"><?php echo lang('iso_code_2');?> / <?php echo lang('iso_code_3');?></label>
                <?php echo form_input(['name'=>'iso_code_2', 'maxlength'=>'2', 'value'=>assign_value('iso_code_2', $iso_code_2), 'class'=>'form-control']);?>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="iso_code_3"><?php echo lang('iso_code_3');?></label>
                <?php echo form_input(['name'=>'iso_code_3', 'maxlength'=>'3', 'value'=>assign_value('iso_code_3', $iso_code_3), 'class'=>'form-control']); ?>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="address_format"><?php echo lang('address_format');?></label>
        <?php echo form_textarea(['name'=>'address_format', 'value'=>assign_value('address_format', $address_format), 'rows'=>6, 'class'=>'form-control']); ?>
    </div>

    <div class="checkbox">
        <label>
            <?php echo form_checkbox(['name'=>'zip_required', 'value'=>1, 'checked'=>set_checkbox('zip_required', 1, (bool)$zip_required)]); ?>
            <?php echo lang('require_zip');?>
        </label>
    </div>

    <div class="form-group">
        <label for="tax"><?php echo lang('tax');?></label>
        <div class="input-group">
            <span class="input-group-addon">%</span>
           <?php echo form_input(['class'=> 'form-control','name'=>'tax', 'maxlength'=>'10', 'value'=>assign_value('tax', $tax)]); ?>
        </div>
     </div>

    <div class="checkbox">
        <label>
            <?php echo form_checkbox(['name'=>'status', 'value'=>1, 'checked'=>set_checkbox('status', 1, (bool)$status)]); ?>
            <?php echo lang('enabled');?>
        </label>
    </div>

    <button type="submit" class="btn btn-primary"><?php echo lang('save');?></button>

</form>

<script type="text/javascript">
$('form').submit(function() {
    $('.btn .btn-primary').attr('disabled', true).addClass('disabled');
});
</script>