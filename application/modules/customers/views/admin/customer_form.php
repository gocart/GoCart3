<div class="page-header">
    <h1><?php echo lang('customer_form');?></h1>
</div>

<?php echo form_open('admin/customers/form/'.$id); ?>

    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo lang('company');?></label>
                <?php echo form_input(['name'=>'company', 'value'=>assign_value('company', $company), 'class'=>'form-control']); ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo lang('firstname');?></label>
                <?php echo form_input(['name'=>'firstname', 'value'=>assign_value('firstname', $firstname), 'class'=>'form-control']); ?>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo lang('lastname');?></label>
                <?php echo form_input(['name'=>'lastname', 'value'=>assign_value('lastname', $lastname), 'class'=>'form-control']); ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo lang('email');?></label>
                <?php echo form_input(['name'=>'email', 'value'=>assign_value('email', $email), 'class'=>'form-control']); ?>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo lang('phone');?></label>
                <?php echo form_input(['name'=>'phone', 'value'=>assign_value('phone', $phone), 'class'=>'form-control']); ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo lang('password');?></label>
                <?php echo form_password(['name'=>'password', 'class'=>'form-control']); ?>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo lang('confirm');?></label>
                <?php echo form_password(['name'=>'confirm', 'class'=>'form-control']); ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="checkbox">
                <label>
                <?php echo form_checkbox(['name'=>'email_subscribe', 'value'=>1, 'checked'=>(bool)$email_subscribe]).' '.lang('email_subscribed'); ?>
                </label>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="checkbox">
                <label>
                    <?php echo form_checkbox(['name'=>'active', 'value'=>1, 'checked'=>$active]).' '.lang('active'); ?>
                </label>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo lang('group');?></label>
                <?php echo form_dropdown('group_id', $group_list, assign_value('group_id',$group_id), 'class="form-control"'); ?>
            </div>
        </div>
    </div>

    <input class="btn btn-primary" type="submit" value="<?php echo lang('save');?>"/>
</form>
