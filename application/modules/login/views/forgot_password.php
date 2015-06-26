<div class="col" data-cols="1/2" data-push="1/4">
    <div class="page-header">
        <h1><?php echo lang('forgot_password');?></h1>
    </div>

    <?php if(validation_errors()):?>
        <div class="alert red">
            <?php echo validation_errors();?>
        </div>
    <?php endif;?>

    <?php echo form_open('forgot-password'); ?>

        <label for="email"><?php echo lang('email');?></label>
        <input type="text" name="email"/>
   
        <input type="hidden" value="submitted" name="submitted"/>

        <input type="submit" value="<?php echo lang('reset_password');?>" class="blue"/>
    </form>

    <div style="text-align:center;">
        <a href="<?php echo site_url('login'); ?>"><?php echo lang('return_to_login');?></a>
    </div>
</div>