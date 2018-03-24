<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div id="stripeContainer">
    
    <div class="alert red" id="stripe_error" style="display:none; width:auto; float:none;"></div>

    <div class="validation"></div>
    <form id="stripePaymentForm">      
        
        <div>
            <label><?php echo lang('card_number');?></label>
            <input type="tel" size="20" class="stripe-number" data-stripe="number"/>
        </div>

        <div class="col-nest">
            <div class="col" data-cols="1/2" data-medium-cols="1/2" data-small-cols="1/2">
                <label><?php echo lang('card_month').' / '.lang('card_year');?></label>
                <input type="tel" class="stripe-exp" placeholder="MM / YY">
            </div>
            <div class="col" data-cols="1/2" data-medium-cols="1/2" data-small-cols="1/2">
                <label><?php echo lang('card_code');?></label>
                <input type="tel" size="4" class="stripe-cvc" data-stripe="cvc"/>
            </div>
        </div>

        <img src="<?php echo addon_img('stripe', 'icons.svg');?>" alt="<?php echo lang('cards_we_accept');?>" style="max-width:225px; display:block; margin:auto; float:right" />
        
        <button class="blue" id="stripeSubmit" type="button"><?php echo lang('submit_order');?></button>
    </form>
</div>

<style type="text/css">
    #stripeContainer {
        position:relative;
    }
</style>

<script src="<?php echo base_url('addons/stripe/assets/js/jquery.payment.js') ?>"></script>

<script>
    $.getScript('https://js.stripe.com/v2/', function(){
        // This identifies your website in the createToken call below
        <?php if($settings['mode'] == 'test'):?>
            Stripe.setPublishableKey('<?php echo $settings['test_publishable_key'];?>');
        <?php else: ?>
            Stripe.setPublishableKey('<?php echo $settings['live_publishable_key'];?>');
        <?php endif;?>

    });

    function stripeResponseHandler(status, response) {
        var form = $('#stripePaymentForm');

        if (response.error) {
            
            // Show the errors on the form
            $('#stripe_error').html('<i class="close"></i>'+response.error.message);
            $('#stripe_error').toggle();

            //turn the click back on
            $('#stripeSubmit').on('click', submitStripePayment).attr('disabled', false);
            $('#stripeContainer').spin(false);

        } else {
            // token contains id, last4, and card type
            var token = response.id;
        
            // and re-submit
            $.post('<?php echo base_url('/stripe/process-payment');?>', {stripeToken: token}, function(data){
                if(data.errors != undefined)
                {
                    var error = '<div class="alert red">';
                    $.each(data.errors, function(index, value)
                    {
                        error += '<p>'+value+'</p>';
                    });
                    
                    error += '</div>';

                    $.gumboTray(error);
                    $('#stripeSubmit').on('click', submitStripePayment).attr('disabled', false);
                    $('#stripeContainer').spin(false);
                }
                else
                {
                    if(data.orderId != undefined)
                    {
                        window.location = '<?php echo site_url('order-complete');?>/'+data.orderId;
                    }
                }
            }, 'json');
        }
    }

    $('.stripe-number').payment('formatCardNumber');
    $('.stripe-exp').payment('formatCardExpiry');
    $('.stripe-cvc').payment('formatCardCVC');

    $('#stripeSubmit').on('click', submitStripePayment);

    $.fn.toggleInputError = function(error) {
        this.parent('div').append('<div class="form-error text-red">'+error+'</div>');
    };

    function submitStripePayment()
    {
        $('#stripeContainer').spin();

        $('#stripeSubmit').off('click', submitStripePayment).attr('disabled', true);

        var form = $('#stripePaymentForm');

        $('#stripeContainer').find('.form-error').remove();

        $('#btn_stripe').attr('disabled', true).addClass('disabled');

        // Call our token request function
        var cardType = $.payment.cardType($('.stripe-number').val());
        var fail = false;
        if( !$.payment.validateCardNumber( $('.stripe-number').val() ) )
        {
            $('.stripe-number').toggleInputError('<?php echo lang('invalid_card_number');?>');
            fail = true;
        }

        if( !$.payment.validateCardExpiry( $('.stripe-exp').payment('cardExpiryVal') ) )
        {
            $('.stripe-exp').toggleInputError('<?php echo lang('invalid_expiration');?>');
            fail = true;
        }

        if( !$.payment.validateCardCVC( $('.stripe-cvc').val(), cardType ) )
        {
            $('.stripe-cvc').toggleInputError('<?php echo lang('invalid_cvv');?>');
            fail = true;
        }

        if(fail)
        {
            $('#stripeSubmit').on('click', submitStripePayment).attr('disabled', false);
            $('#stripeContainer').spin(false);
            return;
        }

        expiration = $('.stripe-exp').payment('cardExpiryVal');
        Stripe.card.createToken({
            number: $('.stripe-number').val(),
            cvc: $('.stripe-cvc').val(),
            exp_month: (expiration.month || 0),
            exp_year: (expiration.year || 0)
        }, stripeResponseHandler);
        
        return false;
    };
</script>

