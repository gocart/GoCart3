<?php echo pageHeader(lang('digital_products_form'));?>
<?php echo form_open_multipart('admin/digital_products/form/'.$id); ?>

    <div class="row">
        <div class="col-md-6">

            <div class="form-group">
                <?php if($id==0) : ?>
                    <label for="file"><?php echo lang('file_label');?> </label>
                    <?php echo form_upload(['name'=>'userfile', 'class'=>'form-control']);?>
                <?php else : ?>
                    <label for="file"><?php echo lang('filename');?>: </label>
                    <?php echo $filename ?>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="title"><?php echo lang('title');?> </label>
                <?php echo form_input(['name'=>'title', 'value'=>assign_value('title', $title), 'class'=>'form-control']);?>
            </div>

            <div class="form-group">
                <label for="title"><?php echo lang('max_downloads');?> </label>
                <?php echo form_input(['name'=>'max_downloads', 'value'=>assign_value('max_downloads', $max_downloads), 'class'=>'form-control']); ?>
                <span class="help-inline"><?php echo lang('max_downloads_note');?></span>
            </div>
            
        </div>
        <div class="col-md-5">
            <div class="well">
                <?php echo sprintf(lang('file_size_warning'), ini_get('post_max_size'), ini_get('upload_max_filesize')); ?>
            </div>
        </div>
    </div>

    <input class="btn btn-primary" type="submit" value="<?php echo lang('save');?>"/>

</form>