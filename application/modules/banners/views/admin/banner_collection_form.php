<?php pageHeader(lang('banner_collection_form')); ?>

<?php echo form_open_multipart('admin/banners/banner_collection_form/'.$banner_collection_id); ?>
    <div class="form-group">
        <label for="title"><?php echo lang('name');?> </label>
        <?php echo form_input(['name'=>'name', 'value' => assign_value('name', $name), 'class' =>'form-control']); ?>
    </div>

    <input class="btn btn-primary" type="submit" value="<?php echo lang('save');?>"/>
</form>