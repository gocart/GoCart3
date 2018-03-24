<div class="page-header"><?php echo lang('moneris_solutions') ?></div>

<div id="monerisContainer">
    <form id="monerisPaymentForm" method="post">
        <div>
            <label><?php echo lang('card_number');?></label>
            <input type="tel" class="monerisCardNumber"/>
        </div>  
        <div class="col-nest">
            <div class="col" data-cols="1/2" data-medium-cols="1/2" data-small-cols="1/2">                                
                <div>
                    <label><?php echo lang('card_month').' / '.lang('card_year');?></label>
                    <input type="tel" class="monerisExpiration" placeholder="MM / YY">
                </div>
            </div>
            <div class="col" data-cols="1/2" data-medium-cols="1/2" data-small-cols="1/2">                                
                <div>
                    <label><?php echo lang('card_code');?></label>
                    <input type="tel" class="monerisCVV" />
                </div>
            </div>
        </div>
        <button id="monerisSubmitButton" class="blue" type="button"><?php echo lang('submit_order');?></button>
    </form>
</div>
<script src="<?php echo base_url('addons/moneris/assets/js/jquery.payment.js') ?>"></script>
<style type="text/css">
    #monerisContainer {
            position:relative;
    }
</style>

<script>
    
    $('.monerisCardNumber').payment('formatCardNumber');
    $('.monerisExpiration').payment('formatCardExpiry');
    $('.monerisCVV').payment('formatCardCVC');

    $.fn.toggleInputError = function(error) {
         this.parent().append('<div class="form-error text-red">'+error+'</div>');
    }; 
    $('#monerisSubmitButton').on('click', submitMonerisCheckout);

    function submitMonerisCheckout()
    {
        $('#monerisSubmitButton').off('click', submitMonerisCheckout).attr('disabled', true);

        $('#monerisCardForm').find('.form-error').remove(); 

        // Call our token request function
        var cardType = $.payment.cardType($('.monerisCardNumber').val());
        var fail = false;

        if( !$.payment.validateCardNumber( $('.monerisCardNumber').val() ) )
        {
            $('.monerisCardNumber').toggleInputError('<?php echo lang('invalid_card_number');?>');
            fail = true;
        }

        if( !$.payment.validateCardExpiry( $('.monerisExpiration').payment('cardExpiryVal') ) )
        {
            $('.monerisExpiration').toggleInputError('<?php echo lang('invalid_expiration');?>');
            fail = true;
        }

        if( !$.payment.validateCardCVC( $('.monerisCVV').val(), cardType ) )
        {
            $('.monerisCVV').toggleInputError('<?php echo lang('invalid_cvv');?>');
            fail = true;
        }

        if(fail)
        {
            $('#monerisSubmitButton').on('click', submitMonerisCheckout).attr('disabled', false);
            return;
        }

        var expiration = $('.monerisExpiration').payment('cardExpiryVal');

        $('#monerisContainer').spin();

        $.post('<?php echo base_url('/moneris/process-payment');?>', {
            cc_data: {
                card_num:$('.monerisCardNumber').val(),
                exp_date_yy: expiration.year,
                exp_date_mm: expiration.month ,
                cvv: $('.monerisCVV').val()
            }
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
            $('#monerisContainer').spin(false);
            $('#monerisSubmitButton').on('click', submitMonerisCheckout).attr('disabled', false);
        }, 'json');
        return false;
    }  
</script>