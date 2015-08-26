<?php pageHeader(lang('category_form')); ?>

<?php echo form_open_multipart('admin/categories/form/'.$id); ?>

<div class="row">
    <div class="col-md-8">

        <div class="form-group">
            <label for="name"><?php echo lang('name');?></label>
            <?php echo form_input(['name'=>'name', 'value'=>assign_value('name', $name), 'class'=>'form-control']); ?>
        </div>

        <div class="form-group">
            <label for="description"><?php echo lang('description');?></label>
            <?php echo form_textarea(['name'=>'description', 'class'=>'redactor', 'value'=>assign_value('description', $description)]); ?>
        </div>


        <div class="form-group">
            <label for="excerpt"><?php echo lang('excerpt');?> </label>
            <?php echo form_textarea(['name'=>'excerpt', 'value'=>assign_value('excerpt', $excerpt), 'class'=>'form-control', 'rows'=>3]); ?>
        </div>

        <div class="form-group">
            <label for="image"><?php echo lang('image');?> </label>
            <div class="input-append">
                <?php echo form_upload(array('name'=>'image', 'class'=>'form-control'));?>
            </div>
                
            <?php if($id && $image != ''):?>
            
            <div style="text-align:center; padding:5px; border:1px solid #ddd;"><img src="<?php echo base_url('uploads/images/small/'.$image);?>" alt="current"/><br/><?php echo lang('current_file');?></div>
            
            <?php endif;?>
        </div>
    </div>
    <div class="col-md-4">
        <?php foreach($groups as $group):?>
            <fieldset>
                <legend><?php echo $group->name;?></legend>
                <div class="form-group">
                    <?php echo form_dropdown('enabled_'.$group->id, [1 => lang('enabled'), 0 => lang('disabled')], assign_value('enabled_'.$group->id,${'enabled_'.$group->id}), 'class="form-control"'); ?>
                </div>
            </fieldset>
        <?php endforeach;?>

        <div class="form-group">
            <label for="parent_id"><?php echo lang('parent');?> </label>
            <?php echo form_dropdown('parent_id', $categories, $parent_id, 'class="form-control"'); ?>
        </div>
        <div class="form-group">
            <label for="slug"><?php echo lang('slug');?> </label>
            <?php echo form_input(['name'=>'slug', 'value'=>assign_value('slug', $slug), 'class'=>'form-control']); ?>
        </div>
        
        <div class="form-group">
            <label for="sequence"><?php echo lang('sequence');?> </label>
            <?php echo form_input(['name'=>'sequence', 'value'=>assign_value('sequence', $sequence), 'class'=>'form-control']); ?>
        </div>

        <div class="form-group">
            <label for="seo_title"><?php echo lang('seo_title');?> </label>
            <?php echo form_input(['name'=>'seo_title', 'value'=>assign_value('seo_title', $seo_title), 'class'=>'form-control']); ?>
        </div>
        
        <div class="form-group">
            <label><?php echo lang('meta');?></label> 
            <?php echo form_textarea(['rows'=>3, 'name'=>'meta', 'value'=>assign_value('meta', html_entity_decode($meta)), 'class'=>'form-control']); ?>
            <span class="help-block"><?php echo lang('meta_data_description');?></span>
        </div>
        

    </div>
</div>


<button type="submit" class="btn btn-primary"><?php echo lang('save');?></button>

</form>

<script type="text/javascript">
$('form').submit(function() {
    $('.btn .btn-primary').attr('disabled', true).addClass('disabled');
});
</script>