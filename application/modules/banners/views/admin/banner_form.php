<?php pageHeader(lang('banner_form')); ?>

<?php echo form_open_multipart('admin/banners/banner_form/'.$banner_collection_id.'/'.$banner_id); ?>
    <div class="form-group">
        <label for="name"><?php echo lang('name');?> </label>
        <?php echo form_input(['name'=>'name', 'value' => assign_value('name', $name), 'class'=>'form-control']); ?>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="enable_date"><?php echo lang('enable_date');?> </label>
                <?php echo form_input(['name'=>'enable_date', 'data-value'=>assign_value('enable_date', $enable_date), 'class'=>'datepicker form-control']); ?>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="disable_date"><?php echo lang('disable_date');?> </label>
                <?php echo form_input(['name'=>'disable_date', 'data-value'=>assign_value('disable_date', $disable_date), 'class'=>'datepicker form-control']); ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="link"><?php echo lang('link');?> </label>
                <?php echo form_input(['name'=>'link', 'value' => assign_value('link', $link), 'class'=>'form-control']); ?>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>&nbsp;</label>
                <div class="checkbox">
                    <label>
                        <?php echo form_checkbox(['name'=>'new_window', 'value'=>1, 'checked'=>set_checkbox('new_window', 1, $new_window)]); ?> <?php echo lang('new_window');?>
                    </label>
                </div>
            </div>
        </div>
    </div>



    <div class="form-group">
        <label for="image"><?php echo lang('image');?> </label>
        <?php echo form_upload(['name'=>'image', 'id'=>'image', 'class'=>'form-control']); ?>
    </div>

    <div class="form-group">
        <?php if($banner_id && $image != ''):?>
            <div style="text-align:center; padding:5px; border:1px solid #ccc;"><img src="<?php echo base_url('uploads/'.$image);?>" alt="current"/><br/><?php echo lang('current_file');?></div>
        <?php endif;?>
    </div>

    <input class="btn btn-primary" type="submit" value="<?php echo lang('save');?>"/>

</form>
<script>
    $('form').submit(function() {
        $('.btn .btn-primary').attr('disabled', true).addClass('disabled');
    });
</script>
