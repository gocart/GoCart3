<?php pageHeader(lang('admin_form')) ?>

<?php echo form_open('admin/users/form/'.$id); ?>

    <div class="form-group">
        <label><?php echo lang('firstname');?></label>
        <?php echo form_input(['name'=>'firstname', 'class'=>'form-control', 'value'=>assign_value('firstname', $firstname)]); ?>
    </div>
    
    <div class="form-group">
        <label><?php echo lang('lastname');?></label>
        <?php echo form_input(['name'=>'lastname', 'class'=>'form-control', 'value'=>assign_value('lastname', $lastname)]); ?>
    </div>

    <div class="form-group">
        <label><?php echo lang('username');?></label>
        <?php echo form_input(['name'=>'username', 'class' => 'form-control', 'value'=>assign_value('username', $username)]); ?>
    </div>

    <div class="form-group">
        <label><?php echo lang('email');?></label>
        <?php echo form_input(['name'=>'email', 'class'=> 'form-control', 'value'=>assign_value('email', $email)]); ?>
    </div>

    <div class="form-group">
        <label><?php echo lang('access');?></label>
        <?php
        $options = ['Admin' => lang('admin'), 'Orders' => lang('orders')];
        echo form_dropdown('access', $options, assign_value('phone', $access), 'class="form-control"');
        ?>
    </div>

    <div class="form-group">
        <label><?php echo lang('password');?></label>
        <?php echo form_password(['name'=>'password', 'class'=>'form-control']); ?>
    </div>

    <div class="form-group">
        <label><?php echo lang('confirm_password');?></label>
        <?php echo form_password(['name'=>'confirm', 'class'=>'form-control']);?>
    </div>

    <input class="btn btn-primary" type="submit" value="<?php echo lang('save');?>"/>

</form>
<script type="text/javascript">
$('form').submit(function() {
    $('.btn .btn-primary').attr('disabled', true).addClass('disabled');
});
</script>