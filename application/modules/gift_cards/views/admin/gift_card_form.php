<?php pageHeader(lang('gift_cards'));?>

<?php echo form_open('admin/gift-cards/form/'); ?>
<fieldset>
    <div class="row"> 
        <div class="col-md-6 form-group">
            <label for="to_name"><?php echo lang('recipient_name');?> </label>
            <?php echo form_input(['name'=>'to_name', 'value'=>assign_value('code'), 'class'=>'form-control']);?>
        </div>
        <div class="col-md-6 form-group">
            <label for="to_email"><?php echo lang('recipient_email');?></label>
            <?php echo form_input(['name'=>'to_email', 'value'=>assign_value('to_email'), 'class'=>'form-control']);?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 form-group">
            <label for="sender_name"><?php echo lang('sender_name');?></label>
            <?php echo form_input(['name'=>'from', 'value'=>assign_value('from'), 'class'=>'form-control']);?>
        </div>
        <div class="col-md-6 form-group">
            <label for="beginning_amount"><?php echo lang('amount');?></label>
            <?php echo form_input(['name'=>'beginning_amount', 'value'=>assign_value('beginning_amount'), 'class'=>'form-control']);?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 form-group">
            <label for="personal_message"><?php echo lang('personal_message');?></label>
            <?php echo form_textarea(['name'=>'personal_message', 'value'=>assign_value('personal_message'), 'class'=>'form-control']);?>
        </div>
        <div class="col-md-6 checkbox">
            <label>
                <?php echo form_checkbox(['name'=>'sendNotification', 'value'=>'true']);?>
                <?php echo lang('send_notification');?>
            </label>
        </div>
    </div>

    <div class="form-actions">
        <input class="btn btn-primary" type="submit" value="<?php echo lang('save');?>"/>
    </div>
    </fieldset>
</form>