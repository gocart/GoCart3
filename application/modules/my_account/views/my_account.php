<div class="page-header">
    <h2><?php echo str_replace('{name}', $customer['firstname'].' '.$customer['lastname'], lang('my_account_page_title'));?></h2>
</div>

<div class="col-nest">
    <div class="col" data-cols="1/3">
        <?php echo form_open('my-account'); ?>

            <h3><?php echo lang('account_information');?></h3>

            <label for="company"><?php echo lang('account_company');?></label>
            <?php echo form_input(['name'=>'company', 'value'=> assign_value('company', $customer['company'])]);?>
                
            <div class="col-nest">
            
                <div class="col" data-cols="1/2">
                    <label for="account_firstname"><?php echo lang('account_firstname');?></label>
                    <?php echo form_input(['name'=>'firstname', 'value'=> assign_value('firstname', $customer['firstname'])]);?>
                </div>

                <div class="col" data-cols="1/2">
                    <label for="account_lastname"><?php echo lang('account_lastname');?></label>
                    <?php echo form_input(['name'=>'lastname', 'value'=> assign_value('lastname', $customer['lastname'])]);?>
                </div>
            
                <div class="col" data-cols="1/2">
                    <label for="account_email"><?php echo lang('account_email');?></label>
                    <?php echo form_input(['name'=>'email', 'value'=> assign_value('email', $customer['email'])]);?>
                </div>
            
                <div class="col" data-cols="1/2">
                    <label for="account_phone"><?php echo lang('account_phone');?></label>
                    <?php echo form_input(['name'=>'phone', 'value'=> assign_value('phone', $customer['phone'])]);?>
                </div>
            </div>

            <label class="checklist">
                <input type="checkbox" name="email_subscribe" value="1" <?php if((bool)$customer['email_subscribe']) { ?> checked="checked" <?php } ?>/> <?php echo lang('account_newsletter_subscribe');?>
            </label>
        
            <div style="margin:30px 0px 10px; text-align:center;">
                <strong><?php echo lang('account_password_instructions');?></strong>
            </div>
        
            <div class="col-nest">
                <div class="col" data-cols="1/2">
                    <label for="account_password"><?php echo lang('account_password');?></label>
                    <?php echo form_password(['name'=>'password']);?>
                </div>

                <div class="col" data-cols="1/2">
                    <label for="account_confirm"><?php echo lang('account_confirm');?></label>
                    <?php echo form_password(['name'=>'confirm']);?>
                </div>
            </div>
        
            <input type="submit" value="<?php echo lang('form_submit');?>" class="blue" />
        </form>
    </div>

    <div id="addresses" class="col" data-cols="2/3"></div>
</div>
<div class="col-nest">
    <div class="col" data-cols="1">
        <div class="page-header" style="margin-top:30px;">
            <h2><?php echo lang('order_history');?></h2>
        </div>
        <?php if($orders):
            echo $orders_pagination;
        ?>
        <table class="table bordered zebra">
            <thead>
                <tr>
                    <th><?php echo lang('order_date');?></th>
                    <th><?php echo lang('order_number');?></th>
                    <th><?php echo lang('order_status');?></th>
                </tr>
            </thead>

            <tbody>
            <?php
            foreach($orders as $order): ?>
                <tr>
                    <td>
                        <?php $d = format_date($order->ordered_on); 
                
                        $d = explode(' ', $d);
                        echo $d[0].' '.$d[1].', '.$d[3];
                
                        ?>
                    </td>
                    <td><a href="<?php echo site_url('order-complete/'.$order->order_number); ?>"><?php echo $order->order_number; ?></a></td>
                    <td><?php echo $order->status;?></td>
                </tr>
        
            <?php endforeach;?>
            </tbody>
        </table>
        <?php else: ?>
            <div class="alert yellow"><i class="close"></i><?php echo lang('no_order_history');?></div>
        <?php endif;?>
    </div>
</div>

<script>
$(document).ready(function(){
    loadAddresses();
});

function closeAddressForm()
{
    $.gumboTray.close();
    loadAddresses();
}

function loadAddresses()
{
    $('#addresses').spin();
    $('#addresses').load('<?php echo base_url('addresses');?>');
}
</script>