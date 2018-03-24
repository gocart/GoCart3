<div class="page-header"><?php echo lang('paypal_pro') ?></div>

<div id="PayPalProContainer">
    <form id="PayPalProCardForm" method="post">
        <div class="form-group-paypalpro">
            <label><?php echo lang('card_number');?></label>
            <input type="tel" size="20" class="PayPalProCardNumber"/>
        </div>  
        <div class="col-nest">
            <div class="col" data-cols="1/2" data-medium-cols="1/2" data-small-cols="1/2">
                <label><?php echo lang('card_month').' / '.lang('card_year');?></label>
                <div class="form-group-paypalpro">
                    <input type="tel" class="PayPalProCardExpiry" placeholder="MM / YY">
                </div>
            </div>
            <div class="col" data-cols="1/2" data-medium-cols="1/2" data-small-cols="1/2">
                <label><?php echo lang('card_code');?></label>
                <div class="form-group-paypalpro">
                    <input type="tel" size="4" class="PayPalProCardCVC"/>
                </div>
            </div>
        </div>
        <button id="PayPalProSubmitButton" class="blue" type="button"><?php echo lang('submit_order');?></button>
    </form>
</div>

<script src="<?php echo base_url('addons/eway/assets/js/jquery.payment.js') ?>"></script>

<style type="text/css">
#PayPalProContainer {
        position:relative;
    }
</style>

<script type="text/javascript">

$('.PayPalProCardNumber').payment('formatCardNumber');
    $('.PayPalProCardExpiry').payment('formatCardExpiry');
    $('.PayPalProCardCVC').payment('formatCardCVC');

    $('#PayPalProSubmitButton').on('click', submitPayPalProCheckout);

    $.fn.togglePayPalProInputError = function(error) {
        this.parent().append('<div class="form-error text-red">'+error+'</div>');
    };

    function submitPayPalProCheckout(event)
    {
        $('#PayPalProContainer').spin();
        $('#PayPalProSubmitButton').off('click', submitPayPalProCheckout).attr('disabled', true);
        
        $('#PayPalProCardForm').find('.form-error').remove();

        // Call our token request function
        var cardType = $.payment.cardType($('#PayPalProCardNumber').val());
        var fail = false;

        if( !$.payment.validateCardNumber( $('.PayPalProCardNumber').val() ) )
        {
            $('.PayPalProCardNumber').togglePayPalProInputError('<?php echo lang('invalid_card_number');?>');
            fail = true;
        }

        if( !$.payment.validateCardExpiry( $('.PayPalProCardExpiry').payment('cardExpiryVal') ) )
        {
            $('.PayPalProCardExpiry').togglePayPalProInputError('<?php echo lang('invalid_expiration');?>');
            fail = true;
        }

        if( !$.payment.validateCardCVC( $('.PayPalProCardCVC').val(), cardType ) )
        {
            $('.PayPalProCardCVC').togglePayPalProInputError('<?php echo lang('invalid_cvv');?>');
            fail = true;
        }

        if(fail)
        {
            $('#PayPalProSubmitButton').on('click', submitPayPalProCheckout).attr('disabled', false);
            $('#PayPalProContainer').spin(false);
            return;
        }

        var expiration = $('.PayPalProCardExpiry').payment('cardExpiryVal');

        $.post('<?php echo base_url('paypal_pro/process-payment') ?>', {
            cc_number: $('.PayPalProCardNumber').val(),
            exp_month: expiration.month,
            exp_year: expiration.year,
            cvv:$('.PayPalProCardCVC').val()
        }, function(data) {
            if(data.errors != undefined)
            {
                var error = '<div class="alert red">';
                $.each(data.errors, function(index, value)
                {
                    error += '<p>'+value+'</p>';
                });
                error += '</div>';

                $.gumboTray(error);
            }
            else
            {
                if(data.orderId != undefined)
                {
                    window.location = '<?php echo site_url('order-complete');?>/'+data.orderId;
                }
            }

            $('#PayPalProContainer').spin(false);
            $('#PayPalProSubmitButton').on('click', submitPayPalProCheckout).attr('disabled', false);

        }, 'json');

        return false;
    }
</script>