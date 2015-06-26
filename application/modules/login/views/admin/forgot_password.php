<div class="row">

    <div class="col-md-4 col-md-offset-4">
        <div style="text-align:center; margin-bottom:15px;">
            <img src="<?php echo base_url('assets/img/logo.svg');?>"/>
        </div>

    <?php echo form_open('admin/forgot-password') ?>

        <div class="form-group">
            <label for="username"><?php echo lang('username');?></label>
            <?php echo form_input(array('name'=>'username', 'class'=>'form-control')); ?>
        </div>

        <input class="btn btn-primary" type="submit" value="<?php echo lang('reset_password');?>"/>
        
    <?php echo  form_close(); ?>

        <div class="text-center">
            <a href="<?php echo site_url('admin/login');?>"><?php echo lang('return_to_login');?></a>
        </div>
    </div>
</div>
