<div class="col-nest">
    <div class="col" data-cols="2/3">

        <div class="checkoutAddress">
        <?php if(!empty($addresses))
        {
            $this->show('checkout/address_list', ['addresses'=>$addresses]);
        }
        else
        {
            ?>
            <script>
                $('.checkoutAddress').load('<?php echo site_url('addresses/form');?>');
            </script>
            <?php //(new GoCart\Controller\Addresses)->form();
        }
        ?>
        </div>

        <div id="shippingMethod"></div>
        <div id="paymentMethod"></div>

    </div>
    <div class="col" data-cols="1/3">
        <div id="orderSummary"></div>
    </div>
</div>

<script>
    var grandTotalTest = <?php echo (GC::getGrandTotal() > 0)?1:0;?>;

    function closeAddressForm(){
        $('.checkoutAddress').load('<?php echo site_url('checkout/address-list');?>');
    }

    function processErrors(errors)
    {
        //scroll to the top
        $('body').scrollTop(0);

        $.each(errors, function(key,val) {

            if(key == 'inventory')
            {
                setInventoryErrors(val);
                $('#summaryErrors').text('<?php echo lang('some_items_are_out_of_stock');?>').show();
            }
            else if(key == 'shipping')
            {
                showShippingError(val);
            }
            else if(key == 'shippingAddress')
            {
                $('#addressError').text('<?php echo lang('error_shipping_address')?>').show();
            }
            else if(key == 'billingAddress')
            {
                $('#addressError').text('<?php echo lang('error_billing_address')?>').show();
            }
        });
    }

    $(document).ready(function(){
        //getBillingAddressForm();
        //getShippingAddressForm();
        //getShippingMethods();
        getCartSummary();
        getPaymentMethods();
    });

    function getCartSummary(callback)
    {
        //update shipping too
        getShippingMethods();

        $('#orderSummary').spin();
        $.post('<?php echo site_url('cart/summary');?>', function(data) {
            $('#orderSummary').html(data);
            if(callback != undefined)
            {
                callback();
            }
        });
    }

    function getShippingMethods()
    {
        $('#shippingMethod').load('<?php echo site_url('checkout/shipping-methods');?>');
    }

    function getPaymentMethods()
    {
        $('#paymentMethod').load('<?php echo site_url('checkout/payment-methods');?>');
    }
</script>
