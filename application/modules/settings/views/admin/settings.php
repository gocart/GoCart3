<?php pageHeader(lang('settings'));?>

<?php echo form_open_multipart('admin/settings');?>
    <fieldset>
        <legend><?php echo lang('shop_details');?></legend>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label><?php echo lang('company_name');?></label>
                    <?php echo form_input(array('class'=>'form-control', 'name'=>'company_name', 'value'=>assign_value('company_name', $company_name)));?>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label><?php echo lang('theme');?></label>
                    <?php echo form_dropdown('theme', $themes, assign_value('theme', $theme), 'class="form-control"');?>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label><?php echo lang('select_homepage');?></label>
                    <?php echo form_dropdown('homepage', $pages, assign_value('homepage', $homepage), 'class="form-control"');?>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label><?php echo lang('products_per_page');?></label>
                    <?php echo form_input(array('class'=>'form-control', 'name'=>'products_per_page', 'value'=>assign_value('products_per_page', $products_per_page), 'class' => 'form-control'));?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label><?php echo lang('default_meta_keywords');?></label>
                    <?php echo form_input(array('class'=>'form-control', 'name'=>'default_meta_keywords', 'value'=>assign_value('default_meta_keywords', $default_meta_keywords), 'class' => 'form-control'));?>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label><?php echo lang('default_meta_description');?></label>
                    <?php echo form_input(array('class'=>'form-control', 'name'=>'default_meta_description', 'value'=>assign_value('default_meta_description', $default_meta_description), 'class' => 'form-control'));?>
                </div>
            </div>
        </div>
    </fieldset>

    <fieldset>
        <legend><?php echo lang('email_settings');?></legend>
        <div class="row form-group">
            <div class="col-md-4">
                <div class="form-group">
                    <label><?php echo lang('email_to');?></label>
                    <?php echo form_input(array('class'=>'form-control', 'name'=>'email_to', 'value'=>assign_value('email_to', $email_to), 'class' => 'form-control'));?>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label><?php echo lang('email_from');?></label>
                    <?php echo form_input(array('class'=>'form-control', 'name'=>'email_from', 'value'=>assign_value('email_from', $email_from), 'class'=>'form-control'));?>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label><?php echo lang('email_method');?></label>
                    <?php echo form_dropdown('email_method', ['mail'=>'Mail', 'smtp'=>'SMTP', 'sendmail'=>'Sendmail'], assign_value('email_method', $email_method), 'class="form-control" id="emailMethod"');?>
                </div>
            </div>
        </div>

        <div class="row emailMethods form-group" id="email_smtp">
            <div class="col-md-3">
                <div class="form-group">
                <label><?php echo lang('smtp_server');?></label>
                <?php echo form_input(array('class'=>'form-control', 'name'=>'smtp_server', 'value'=>assign_value('smtp_server', $smtp_server)));?>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label><?php echo lang('smtp_port');?></label>
                    <?php echo form_input(array('class'=>'form-control', 'name'=>'smtp_port', 'value'=>assign_value('smtp_port', $smtp_port)));?>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label><?php echo lang('smtp_username');?></label>
                    <?php echo form_input(array('class'=>'form-control', 'name'=>'smtp_username', 'value'=>assign_value('smtp_username', $smtp_username)));?>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label><?php echo lang('smtp_password');?></label>
                    <?php echo form_input(array('class'=>'form-control', 'name'=>'smtp_password', 'value'=>assign_value('smtp_password', $smtp_password)));?>
                </div>
            </div>
        </div>

        <div class="row emailMethods" id="email_sendmail">
            <div class="col-md-4">
                <div class="form-group">
                    <label><?php echo lang('sendmail_path');?></label>
                    <?php echo form_input(array('class'=>'form-control', 'name'=>'sendmail_path', 'value'=>assign_value('sendmail_path', $sendmail_path)));?>
                </div>
            </div>
        </div>

    </fieldset>

    <fieldset>
        <legend><?php echo lang('ship_from_address');?></legend>
        
        <div class="form-group">
            <label><?php echo lang('country');?></label>
            <?php echo form_dropdown('country_id', $countries_menu, assign_value('country_id', $country_id), 'id="country_id" class="form-control"');?>
        </div>

        <div class="form-group">
            <label><?php echo lang('address1');?></label>
            <?php echo form_input(array('name'=>'address1', 'class'=>'form-control','value'=>assign_value('address1',$address1)));?>
        </div>

        <div class="form-group">
            <?php echo form_input(array('name'=>'address2', 'class'=>'form-control','value'=> assign_value('address2',$address2)));?>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label><?php echo lang('city');?></label>
                    <?php echo form_input(array('name'=>'city','class'=>'form-control', 'value'=>assign_value('city',$city)));?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label><?php echo lang('state');?></label>
                    <?php echo form_dropdown('zone_id', $zones_menu, assign_value('zone_id', $zone_id), 'id="zone_id" class="form-control"');?>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label><?php echo lang('zip');?></label>
                    <?php echo form_input(array('maxlength'=>'10', 'class'=>'form-control', 'name'=>'zip', 'value'=> assign_value('zip',$zip)));?>
                </div>
            </div>
        </div>
    </fieldset>

    <fieldset>
        <legend><?php echo lang('locale_currency');?></legend>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label><?php echo lang('locale');?></label>
                    <?php echo form_dropdown('locale', $locales, assign_value('locale', $locale), 'class="form-control"');?>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label><?php echo lang('timezone');?></label>
                    <?php echo form_dropdown('timezone', array_combine(timezone_identifiers_list(), timezone_identifiers_list()), assign_value('timezone', $timezone), 'class="form-control"');?>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label><?php echo lang('currency');?></label>
                    <?php echo form_dropdown('currency_iso', $iso_4217, assign_value('currency_iso', $currency_iso), 'class="form-control"');?>
                </div>
            </div>
        </div>

    </fieldset>

    <fieldset>
        <legend><?php echo lang('security');?></legend>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label><?php echo lang('stage_username'); ?></label>
                    <?php echo form_input(['class'=>'form-control', 'name'=>'stage_username', 'value'=>assign_value('stage_username',$stage_username)]);?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label><?php echo lang('stage_password'); ?></label>
                    <?php echo form_input(['class'=>'form-control', 'name'=>'stage_password', 'value'=>assign_value('stage_password',$stage_password)]);?>
                </div>
            </div>
        </div>

        <div class="checkbox">
            <label>
                <?php echo form_checkbox('ssl_support', '1', assign_value('ssl_support',$ssl_support));?> <?php echo lang('ssl_support');?>
            </label>
        </div>

        <div class="checkbox">
            <label>
                <?php echo form_checkbox('require_login', '1', assign_value('require_login',$require_login));?> <?php echo lang('require_login');?>
            </label>
        </div>

        <div class="checkbox">
            <label>
                <?php echo form_checkbox('new_customer_status', '1', assign_value('new_customer_status',$new_customer_status));?> <?php echo lang('new_customer_status');?>
            </label>
        </div>

    </fieldset>

    <fieldset>
        <legend><?php echo lang('package_details');?></legend>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label><?php echo lang('weight_unit');?></label>
                    <?php echo form_input(array('name'=>'weight_unit', 'class'=>'form-control','value'=>assign_value('weight_unit',$weight_unit)));?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label><?php echo lang('dimension_unit');?></label>
                    <?php echo form_input(array('name'=>'dimension_unit', 'class'=>'form-control','value'=>assign_value('dimension_unit',$dimension_unit)));?>
                </div>
            </div>
        </div>
    </fieldset>

    <fieldset>
        <legend><?php echo lang('order_inventory');?></legend>

        <table class="table">
            <thead>
                <tr>
                    <th style="width:20%;"><?php echo lang('order_status');?></th>
                    <th style="width:20%;"><?php echo lang('order_statuses');?></th>
                    <th style="text-align:right;">
                        <div class="col-md-4 input-group pull-right">
                        <input type="text" value="" class="form-control" id="new_order_status_field" style="margin:0px;" placeholder="<?php echo lang('status_name');?>"/>
                        <div class="input-group-btn">
                        <button type="button" class="btn btn-success" onclick="add_status()"><i class="icon-plus"></i></button>
                        </div>
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody id="orderStatuses">
            </tbody>
        </table>
        <?php echo form_textarea(array('name'=>'order_statuses', 'value'=>assign_value('order_statuses',$order_statuses), 'id'=>'order_statuses_json'));?>

        <div class="checkbox">
            <label>
                <?php echo form_checkbox('inventory_enabled', '1', assign_value('inventory_enabled',$inventory_enabled));?> <?php echo lang('inventory_enabled');?>
            </label>
        </div>

        <div class="checkbox">
            <label>
                <?php echo form_checkbox('allow_os_purchase', '1', assign_value('allow_os_purchase',$allow_os_purchase));?> <?php echo lang('allow_os_purchase');?>
            </label>
        </div>

    </fieldset>

    <fieldset>
        <legend><?php echo lang('tax_settings');?></legend>

        <div class="form-group">
            <label><?php echo lang('tax_address');?></label>
            <?php echo form_dropdown('tax_address', ['ship'=>lang('shipping_address'), 'bill'=>lang('billing_address')], assign_value('tax_address',$tax_address), 'class="form-control"');?>
        </div>

        <div class="checkbox">
            <label>
                <?php echo form_checkbox('tax_shipping', '1', assign_value('tax_shipping',$tax_shipping));?> <?php echo lang('tax_shipping');?>
            </label>
        </div>
    </fieldset>


    <input type="submit" class="btn btn-primary" value="<?php echo lang('save');?>" />

</form>

<script type="text/template" id="orderStatusTemplate">
    <tr>
        <td>
            <input type="radio" value="{{status}}" name="order_status">
        </td>
        <td>
            {{status}}
        </td>
        <td style="text-align:right;">
            <button type="button" class="removeOrderStatus btn btn-danger" value="{{status}}"><i class="icon-close"></i></button>
        </td>
    </tr>
</script>


<script>

    var orderStatus = <?php echo json_encode($order_status);?>;
    var orderStatuses = <?php echo $order_statuses;?>;
    var orderStatusTemplate = $('#orderStatusTemplate').html();

    function renderOrderStatus()
    {
        $('#orderStatuses').html('');
        $.each(orderStatuses, function(id, val){
            var data = {status:val}
            var output = Mustache.render(orderStatusTemplate, data);
            $('#orderStatuses').append(output);
            $('input[value="'+orderStatus+'"]').prop('checked', true);
        });
        //update the order_statuses_json field
        $('#order_statuses_json').val( JSON.stringify(orderStatuses) );
    }

    function add_status()
    {
        var status = $('#new_order_status_field').val();
        orderStatuses[status] = status;
        renderOrderStatus();

        $('#new_order_status_field').val('');
    }

    function deleteStatus(status)
    {
        delete orderStatuses[status];
        renderOrderStatus();
    }

    $(document).ready(function(){
        $('#country_id').change(function(){
            $.post('<?php echo site_url('admin/locations/get_zone_menu');?>',{id:$('#country_id').val()}, function(data) {
              $('#zone_id').html(data);
            });
        });

        renderOrderStatus();

        $('#emailMethod').bind('click change keyup keypress', function(){
            $('.emailMethods').hide();
            $('#email_'+$(this).val()).show();
        });


        $('#new_order_status_field').on('keyup', function(event){
            if (event.which == 13) {
                add_status();
            }
        }).keypress(function(event){
            if (event.which  == 13) {
                event.preventDefault();
                return false;
            }
        });

        $('#orderStatuses').on('click', '.removeOrderStatus', function(){
            if(confirm('<?php echo lang('confirm_delete_order_status');?>'))
            {
                deleteStatus($(this).val());
            }
        });

        $('#orderStatuses').on('change', 'input[name="order_status"]', function(){
            orderStatus = $(this).val();
        });
    });

</script>

<style type="text/css">
#order_statuses_json, .emailMethods {
   display:none;
}
#email_<?php echo $email_method;?> {
    display:block;
}
</style>
