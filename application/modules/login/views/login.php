<div class="col" data-cols="1/2" data-push="1/4">
    <div class="page-header">
        <h1><?php echo lang('login');?></h1>
    </div>
    <?php if(validation_errors()):?>
        <div class="alert red">
            <?php echo validation_errors(); ?>
        </div>
    <?php endif;?>
    <?php echo form_open('login/'.$redirect); ?>

        <label for="email"><?php echo lang('email');?></label>
        <input type="text" name="email"/>

        <label for="password"><?php echo lang('password');?></label>
        <input type="password" name="password"/>

        <label class="checklist">
            <input name="remember" value="true" type="checkbox" />
             <?php echo lang('keep_me_logged_in');?>
        </label>

        <input type="submit" value="<?php echo lang('form_login');?>" name="submit" class="blue"/>
    </form>

    <div style="text-align:center;">
        <a href="<?php echo site_url('forgot-password'); ?>"><?php echo lang('forgot_password')?></a> | <a href="<?php echo site_url('register'); ?>"><?php echo lang('register');?></a>
    </div>
</div>