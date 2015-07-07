
<div class="col" data-cols="1/2" data-push="1/4">
    <div class="page-header">
        <h1><?php echo lang('form_register');?></h1>
    </div>
    
    <?php echo form_open('register', 'id="registration_form"'); ?>
        <input type="hidden" name="submitted" value="submitted" />
        <input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />

        <label for="company"><?php echo lang('account_company');?></label>
        <?php echo form_input(['name'=>'company', 'value'=> assign_value('company')]);?>

        <div class="col-nest">
            <div class="col" data-cols="1/2">
                <label for="account_firstname"><?php echo lang('account_firstname');?></label>
                <?php echo form_input(['name'=>'firstname', 'value'=> assign_value('firstname')]);?>
            </div>

            <div class="col" data-cols="1/2">
                <label for="account_lastname"><?php echo lang('account_lastname');?></label>
                <?php echo form_input(['name'=>'lastname', 'value'=> assign_value('lastname')]);?>
            </div>
        </div>

        <div class="col-nest">
            <div class="col" data-cols="1/2">
                <label for="account_email"><?php echo lang('account_email');?></label>
                <?php echo form_input(['name'=>'email', 'value'=>assign_value('email')]);?>
            </div>
        
            <div class="col" data-cols="1/2">
                <label for="account_phone"><?php echo lang('account_phone');?></label>
                <?php echo form_input(['name'=>'phone', 'value'=> assign_value('phone')]);?>
            </div>
        </div>

        <label class="checklist">
            <input type="checkbox" name="email_subscribe" value="1" <?php echo set_radio('email_subscribe', '1', TRUE); ?>/> <?php echo lang('account_newsletter_subscribe');?>
        </label>

        <div class="col-nest">
            <div class="col" data-cols="1/2">
                <label for="account_password"><?php echo lang('account_password');?></label>
                <input type="password" name="password" autocomplete="off" />
            </div>

            <div class="col" data-cols="1/2">
                <label for="account_confirm"><?php echo lang('account_confirm');?></label>
                <input type="password" name="confirm" autocomplete="off" />
            </div>
        </div>
        
        <input type="submit" value="<?php echo lang('form_register');?>" class="blue" />
    </form>

    <div style="text-align:center;">
        <a href="<?php echo site_url('login'); ?>"><?php echo lang('go_to_login');?></a>
    </div>
</div>

<script>
<?php if(validation_errors()):
    $errors = \CI::form_validation()->get_error_array(); ?>

    var formErrors = <?php echo json_encode($errors);?>
    
    for (var key in formErrors) {
        if (formErrors.hasOwnProperty(key)) {
            $('#registration_form [name="'+key+'"]').parent().append('<div class="form-error text-red">'+formErrors[key]+'</div>')
        }
    }
<?php endif;?>
</script>