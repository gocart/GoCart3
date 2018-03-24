<div class="page-header"><?php echo lang('beanstream') ?></div>

<div id="beanstreamContainer">
    <form id="beanstreamCardForm" method="post">
        <div>
            <label><?php echo lang('card_name');?></label>
            <input type="tel" size="50" class="beanstreamNameOnCard"/>
        </div>

        <div>
            <label><?php echo lang('card_number');?></label>
            <input type="tel" size="20" class="beanstreamCardNumber"/>
        </div>

        <div class="col-nest">
            <div class="col" data-cols="1/2" data-medium-cols="1/2" data-small-cols="1/2">
                <label><?php echo lang('card_month').' / '.lang('card_year');?></label>
                <input type="tel" class="beanstreamCardExpiry" placeholder="MM/YY">
            </div>
            <div class="col" data-cols="1/2" data-medium-cols="1/2" data-small-cols="1/2">
                <label><?php echo lang('card_code');?></label>
                <input type="tel" size="4" class="beanstreamCardCVC"/>
            </div>
        </div>
        <button id="beanstreamSubmitButton" class="blue" type="button"><?php echo lang('submit_order');?></button>
    </form>
</div>

<script src="<?php echo base_url('addons/beanstream/assets/js/jquery.payment.js') ?>"></script>

<style type="text/css">
    #beanstreamContainer {
        position:relative;
    }
</style>
<script>

    $('.beanstreamCardNumber').payment('formatCardNumber');
    $('.beanstreamCardExpiry').payment('formatCardExpiry');
    $('.beanstreamCardCVC').payment('formatCardCVC');

    $('#beanstreamSubmitButton').on('click', submitBeanstreamCheckout);

    $.fn.toggleBeanstreamInputError = function(error) {
        this.parent().append('<div class="form-error text-red">'+error+'</div>');
    };

    function submitBeanstreamCheckout(event)
    {
        $('#beanstreamContainer').spin();
        $('#beanstreamSubmitButton').off('click', submitBeanstreamCheckout).attr('disabled', true);
        
        $('#beanstreamCardForm').find('.form-error').remove();

        // Call our token request function
        var cardType = $.payment.cardType($('#beanstreamCardNumber').val());
        var fail = false;
        var cardHolderName = $('.beanstreamNameOnCard').val();
        
        if( cardHolderName.match('/^[a-zA-Z]+$/') || cardHolderName.length == 0)
        {
            $('.beanstreamNameOnCard').toggleBeanstreamInputError('<?php echo lang('invalid_card_name');?>');
            fail = true;
        }

        if( !$.payment.validateCardNumber( $('.beanstreamCardNumber').val() ) )
        {
            $('.beanstreamCardNumber').toggleBeanstreamInputError('<?php echo lang('invalid_card_number');?>');
            fail = true;
        }

        if( !$.payment.validateCardExpiry( $('.beanstreamCardExpiry').payment('cardExpiryVal') ) )
        {
            $('.beanstreamCardExpiry').toggleBeanstreamInputError('<?php echo lang('invalid_expiration');?>');
            fail = true;
        }

        if( !$.payment.validateCardCVC( $('.beanstreamCardCVC').val(), cardType ) )
        {
            $('.beanstreamCardCVC').toggleBeanstreamInputError('<?php echo lang('invalid_cvv');?>');
            fail = true;
        }

        if(fail)
        {
            $('#beanstreamSubmitButton').on('click', submitBeanstreamCheckout).attr('disabled', false);
            $('#beanstreamContainer').spin(false);
            return;
        }

        var expiration = $('.beanstreamCardExpiry').payment('cardExpiryVal');

        $.post('<?php echo base_url('beanstream/process-payment') ?>', {
            cc_name: $('.beanstreamNameOnCard').val(),
            cc_number: $('.beanstreamCardNumber').val(),
            exp_month: expiration.month,
            exp_year: expiration.year,
            cvv:$('.beanstreamCardCVC').val()
        }, function(data){
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

            $('#beanstreamContainer').spin(false);
            $('#beanstreamSubmitButton').on('click', submitBeanstreamCheckout).attr('disabled', false);

        }, 'json');
        return false;
    }
</script>

