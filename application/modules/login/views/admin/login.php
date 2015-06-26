<div class="row">

    <div class="col-md-4 col-md-offset-4">
        <div style="text-align:center; margin-bottom:15px;">
            <img src="<?php echo base_url('assets/img/logo.svg');?>"/>
        </div>

        <?php echo form_open('admin/login') ?>

            <div class="form-group">
                <label for="username"><?php echo lang('username');?></label>
                <?php echo form_input(array('name'=>'username', 'class'=>'form-control')); ?>
            </div>

            <div class="form-group">
                <label for="password"><?php echo lang('password');?></label>
                <?php echo form_password(array('name'=>'password', 'class'=>'form-control')); ?>
            </div>

            <div class="form-group">
                <label>
                    <?php echo form_checkbox(array('name'=>'remember', 'value'=>'true'))?>
                    <?php echo lang('stay_logged_in');?>
                </label>
            </div>

            <input class="btn btn-primary" type="submit" value="<?php echo lang('login');?>"/>

            <input type="hidden" value="<?php echo $redirect; ?>" name="redirect"/>
            <input type="hidden" value="submitted" name="submitted"/>
            
        <?php echo  form_close(); ?>

        <div class="text-center">
            <a href="<?php echo site_url('admin/forgot-password');?>"><?php echo lang('forgot_password');?></a>
        </div>

    </div>
</div>
