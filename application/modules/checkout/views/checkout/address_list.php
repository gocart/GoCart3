<div class="page-header">
    <button id="addAddress" type="button" class="input-xs pull-right"><?php echo lang('add_address');?></button>
    <h3><?php echo lang('your_addresses');?></h3>
</div>
<?php if(count($addresses) > 0):?>
<div id="addressError" class="alert red hide"></div>
<div class="col-nest">
    <div class="col" data-cols="1">
        <table class="table horizontal-border">
            <tbody>

                <?php foreach($addresses as $a):?>
                    <tr>
                        <td>
                            <?php echo format_address($a, true); ?>
                        </td>
                        <td>
                            <label><?php
                                $checked = (GC::getCart()->billing_address_id == $a['id'])?true:false;
                                echo form_radio(['name'=>'billing_address', 'value'=>$a['id'], 'checked'=>$checked]);
                            ?><?php echo lang('billing');?></label>
                        </td>
                        <td>
                            <label><?php
                                $checked = (GC::getCart()->shipping_address_id == $a['id'])?true:false;
                                echo form_radio(['name'=>'shipping_address', 'value'=>$a['id'], 'checked'=>$checked]);
                            ?>
                            <?php echo lang('shipping');?></label>
                        </td>
                        <td>
                            <i class="icon-pencil" onclick="editAddress(<?php echo $a['id'];?>)"></i>
                            <i class="icon-x text-red" onclick="deleteAddress(<?php echo $a['id'];?>)"></i>
                        </td>
                    </tr>
                <?php endforeach;?>
            </tbody>
        </table>
    </div>
</div>

<?php else:?>

<script>
$('.checkoutAddress').spin();
$('.checkoutAddress').load('<?php echo site_url('addresses/form');?>');
getCartSummary();
</script>

<?php endif;?>

<script>
function editAddress(id)
{
    $('.checkoutAddress').spin();
    $('.checkoutAddress').load('<?php echo site_url('addresses/form');?>/'+id);
}

function deleteAddress(id)
{
    if( confirm('<?php echo lang('delete_address_confirmation');?>') )
    {
        $.post('<?php echo site_url('addresses/delete');?>/'+id, function(){
            closeAddressForm();
        });
    }
}

$('#addAddress').click(function(){
    $('.checkoutAddress').spin();
    $('.checkoutAddress').load('<?php echo site_url('addresses/form');?>');
})

$('[name="billing_address"]').change(function(){
    $('#billingAddress').spin();
    $.post('<?php echo site_url('checkout/address');?>', {'type':'billing', 'id':$(this).val()}, function(data){
        if(data.error != undefined)
        {
            alert(data.error);
            closeAddressForm();
        }
        else
        {
            getCartSummary();
        }
        $('#billingAddress').spin(false);
    });
});

$('[name="shipping_address"]').change(function(){
    $('#shipingAddress').spin();
    $.post('<?php echo site_url('checkout/address');?>', {'type':'shipping', 'id':$(this).val()}, function(data){
        if(data.error != undefined)
        {
            alert(data.error);
            closeAddressForm();
        }
        else
        {
            getCartSummary();
        }
        $('#shipingAddress').spin(false);
    });
});

var billingAddresses = $('[name="billing_address"]');
var shippingAddresses = $('[name="shipping_address"]');

if(billingAddresses.length == 1)
{
    billingAddresses.attr('checked', true).change();
}

if(shippingAddresses.length == 1)
{
    shippingAddresses.attr('checked', true).change();
}
</script>
