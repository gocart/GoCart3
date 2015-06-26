<?php pageHeader(lang('link_form'));?>

<?php echo form_open('admin/pages/link_form/'.$id); ?>

    <div class="form-group">
        <label for="menu_title"><?php echo lang('title');?> </label>
        <?php echo form_input(['name'=>'title', 'value'=>assign_value('title', $title), 'class'=>'form-control']);?>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="form-group">
                <label for="url"><?php echo lang('url');?></label>
                <?php echo form_input(['name'=>'url', 'value'=>assign_value('url', $url), 'class'=>'form-control']);?>
                <span class="help-block"><?php echo lang('url_example');?></span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>&nbsp;</label>
                <div class="checkbox">
                    <label>
                        <?php echo form_checkbox(['name'=>'new_window', 'value'=>'1', 'checked'=>(bool)$new_window]);?>
                        <?php echo lang('open_in_new_window');?>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="form-group">
                <label for="sequence"><?php echo lang('parent_id');?></label>
                <?php
                $options = [];
                $options[0] = lang('top_level');
                function page_loop($pages, $dash = '', $id=0)
                {
                    $options    = [];
                    foreach($pages as $page)
                    {
                        //this is to stop the whole tree of a particular link from showing up while editing it
                        if($id != $page->id)
                        {
                            $options[$page->id] = $dash.' '.$page->title;
                            $options = $options + page_loop($page->children, $dash.'-', $id);
                        }
                    }
                    return $options;
                }
                $options = $options + page_loop($pages, '', $id);
                echo form_dropdown('parent_id', $options,  assign_value('parent_id', $parent_id), 'class="form-control"');
                ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="sequence"><?php echo lang('sequence');?></label>
                <?php echo form_input(['name'=>'sequence', 'value'=>assign_value('sequence', $sequence), 'class'=>'form-control']);?>
            </div>
        </div>
    </div>
    
    <input class="btn btn-primary" type="submit" value="<?php echo lang('save');?>"/>
    
</form>