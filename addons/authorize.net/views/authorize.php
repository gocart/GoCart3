<div class="page-header"><?php echo lang('authorize') ?></div>

<div id="authorize-details-form">
    <div class="validationauthorize"></div>  
    <form id="authorizeCardForm" method="post">
        <label><?php echo lang('card_number');?></label>
        <div class="form-group-authorize">
            <input type="tel" id="ccNoauthorize" class="ccNoauthorize"/>
        </div>  
        <div class="col-nest">
            <div class="col" data-cols="1/2" data-medium-cols="1/2" data-small-cols="1/2">
                <label><?php echo lang('card_month').' / '.lang('card_year');?></label>
                <div class="form-group-authorize">
                    <input type="tel" id="expauthorize" class="expauthorize" placeholder="MM / YY">
                </div>
            </div>
            <div class="col" data-cols="1/2" data-medium-cols="1/2" data-small-cols="1/2">
                <label><?php echo lang('card_code');?></label>
                <div class="form-group-authorize">
                    <input type="tel" id="cvvauthorize" class="cvvauthorize"/>
                </div>
            </div>
        </div>
        <!--<img src="https://authorize.io/images/site-seal/wizz/dark/300x100-VMAD.png"/>!-->
        <button id="btn_authorize" onclick="submitAuthorizeCheckout()" class="blue" type="button"><?php echo lang('submit_order');?></button>
    </form>
</div>

<script src="<?php echo base_url('addons/authorize.net/assets/js/jquery.payment.js') ?>"></script>
<style type="text/css">
    #authorize-details-form {
        position:relative;
    }
</style>

<script>

    $('.ccNoauthorize').payment('formatCardNumber');
    $('.expauthorize').payment('formatCardExpiry');
    $('.cvvauthorize').payment('formatCardCVC');

    $('#btn_authorize').on('click', submitAuthorizeCheckout);

    $.fn.toggleInputError = function(error) {
        this.parent('.form-group-authorize').append('<div class="form-error text-red">'+error+'</div>');
    };

    function submitAuthorizeCheckout(event)
    {
        //unbind immediately
        $('#btn_authorize').off('click', submitAuthorizeCheckout).attr('disabled', true);

        $('#authorizeCardForm').find('.form-error').remove();

        // Call our token request function
        var cardType = $.payment.cardType($('#ccNoauthorize').val());
        var fail = false;
        if( !$.payment.validateCardNumber( $('.ccNoauthorize').val() ) )
        {
            $('.ccNoauthorize').toggleInputError('<?php echo lang('invalid_card_number');?>');
            fail = true;
        }

        if( !$.payment.validateCardExpiry( $('.expauthorize').payment('cardExpiryVal') ) )
        {
            $('.expauthorize').toggleInputError('<?php echo lang('invalid_expiration');?>');
            fail = true;
        }

        if( !$.payment.validateCardCVC( $('.cvvauthorize').val(), cardType ) )
        {
            $('.cvvauthorize').toggleInputError('<?php echo lang('invalid_cvv');?>');
            fail = true;
        }

        if(fail)
        {
            $('#btn_authorize').on('click', submitAuthorizeCheckout).attr('disabled', false);
            return;
        }

        var expiration = $('.expauthorize').payment('cardExpiryVal');

        //show the spinner
        $('#authorize-details-form').spin();

        $.post('<?php echo base_url('authorize/process-payment') ?>', {
            cc_number: $('.ccNoauthorize').val(), 
            exp_month: expiration.month, 
            exp_year: expiration.year, 
            cvv:$('.cvvauthorize').val()
        }, function(data){
            if(data.errors)
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
            
            $('#authorize-details-form').spin(false);
            $('#btn_authorize').on('click', submitAuthorizeCheckout).attr('disabled', false);

        }, 'json');
    
        return false;
    }
</script>

