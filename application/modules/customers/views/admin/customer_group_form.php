<?php pageHeader(lang('customer_group_form'));?>

<?php echo form_open('admin/customers/group_form/'.$id); ?>
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo lang('group_name');?></label>
                <?php echo form_input('name', assign_value('name', $name), 'class="form-control"') ?>
            </div>
        </div>
    </div>

    <input class="btn btn-primary" type="submit" value="<?php echo lang('save');?>"/>
</form>
